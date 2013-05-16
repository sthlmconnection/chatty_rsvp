/**
 * chatty_rsvp.js
 * Provide interactive steps for the RSVS form.
 */
(function($) {

  var values = {},
    step = 0,
    $form,
    nameKnown = false;
  
  // Collect all values of the form into a hash.
  var getFormValues = function($form) {
    $.each($form.serializeArray(), function(i, field) {
      values[field.name] = field.value;
    });
  }

  // Validate an email address.
  var validateEmail = function(email) {
    return email && /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/.test(email);
  }

  // Form submit handler.
  var submitForm = function() {
    getFormValues($form);
    changeState();
    return false;
  }

  // Set/unset the loading state.
  var loading = function(status) {
    if (status) {
      $form.addClass("loading")
        .find("#submit").attr("disabled", "disabled");
    }
    else {
      $form.removeClass("loading")
        .find("#submit").removeAttr("disabled");
    }
  }

  // Show the next step.
  var showNext = function(callback) {
    var intervalTimer;
    $("#step-" + step).find("input").blur().attr("disabled", "disabled");

    step++;
    var $items = $("#step-" + step).find(".message, .input").not(".hidden");
    var i = 0;

    // Display each item within a step at a time interval.
    if ($items.length > 0) {
      intervalTimer = setInterval(function() {
        var $item = $items.eq(i);
        if ($item.length > 0) {
          i++;
          $item.slideDown(100, function() {
            $("html, body").animate({scrollTop: $item.offset().top}, 500);
          });
        }
        else { // All items processed.
          clearInterval(intervalTimer);
          $items.find("input").eq(0).focus();
          if (callback) {
            callback();
          }
        }
      }, 1000);
    }
  }

  // Check state and increment step.
  var changeState = function() {
    switch (step) {
      // Empty initial state.
      case 0:
        showNext(function() {
          $("#submit").val(texts.proceed).slideDown(100);
        });
        break;

      // User enters email.
      case 1:
        if (validateEmail(values.email)) {
          // Look for an existing email record.
          loading(true);
          $.getJSON("guest.php?email=" + values.email, function(data) {
            if (data && data.email) {
              // Populate form values.
              if (data.firstname) {
                nameKnown = true;
                $("#step-2 .input").addClass("hidden");
                $("#firstname").val(data.firstname);
                $("#lastname").val(data.lastname);
                $("#nameinput").val(data.firstname + ' ' + data.lastname);
                $("#step-2 .message")
                  .text(texts.step_2.message_existing
                    .replace(/%name/, data.firstname));
              }
              $.each(["coming", "friend"], function() {
                $form.find("input[name=" + this + "][value=" + data[this] + "]").click();
              });
              $("#message").val(data.message);
            }
            showNext(function() {
              loading(false);
              if (nameKnown) {
                $form.submit(); // No need to fill out step 2.
              }
            });
          });
        }
        else {
          alert(texts.input_error);
        }
        break;

      // User enters name.
      case 2:
        if (validateEmail(values.email) && (values.firstname || values.nameinput)) {
          if (values.nameinput) {
            (function() {
              var parts = values.nameinput.trim().split(' ');
              values.firstname = parts.shift();
              values.lastname = parts.join(' ');
            })();
            $form.find('#firstname').val(values.firstname);
            $form.find('#lastname').val(values.lastname);
          }
          loading(true);
          if (!nameKnown) {
            $("#step-3 .message").text(texts.step_3.message_new
              .replace(/%name/, values.firstname));
          }
          showNext(function() {
            loading(false);
          });
        }
        else {
          alert(texts.input_error);
        }
        break;

      // User enters RSVP.
      case 3:
        if (validateEmail(values.email) && values.firstname) {
          loading(true);
          switch (values.coming) {
            case "0":
              $("#step-4 .message").text(texts.step_4.message_coming_no);
              $("#step-6 .message:eq(0)").text(texts.step_5.message_coming_no);
              $("#step-4 .input").addClass("hidden");
              break;
            case "0.5":
              $("#step-4 .message").text(texts.step_4.message_coming_maybe);
              $("#step-6 .message:eq(0)").text(texts.step_5.message_coming_maybe);
              break;
            case "1":
              $("#step-4 .message").text(texts.step_4.message_coming_yes);
              $("#step-6 .message:eq(0)").text(texts.step_5.message_coming_yes);
              break;
          }
          showNext(function() {
            loading(false);
            if (values.coming == 0) {
              $form.submit(); // No need to fill out step 4.
            }
          });
        }
        else {
          alert(texts.input_error);
        }
        break;

      // User enters whether (s)he is bringing a friend.
      case 4:
        loading(true);
        showNext(function() {
          loading(false);
          // Last step, change button text to "Submit".
          $("#submit", $form).val(texts.submit);
        });
        break;

      // User enters a custom message.
      case 5:
        if (validateEmail(values.email) && values.firstname) {
          loading(true);
          if (!values.message) {
            $("#step-5 .input input").attr("placeholder", texts.no);
          }
          jQuery.ajax({
            type: "post",
            url: "guest.php",
            data: values,
            success: function(data) {
              if (/failed/.test(data) || /error/.test(data)) {
                loading(false);
                alert(texts.submit_error);
              }
              else {
                $("#submit", $form).slideUp(200);
                loading(false);
                showNext();
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              loading(false);
              alert(texts.submit_error);
            }    
          });
        }
        else {
          alert(texts.input_error);
        }
        break;
    }
  }

  // Setup form behavior.
  $(document).ready(function() {
    $form = $("#rsvp");
    $form.submit(submitForm);
    changeState();
  });

})(jQuery)
