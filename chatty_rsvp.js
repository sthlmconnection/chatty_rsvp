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

  // Get the first name.
  var firstName = function(name) {
    return name.split(" ")[0];
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
    $("#step-" + step).find("input").attr("disabled", "disabled");

    step++;
    var $items = $("#step-" + step).find(".message, .input").not(".hidden");
    var i = 0;

    // Process each item at a time interval.
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
              if (data.name) {
                nameKnown = true;
                $("#reference").val(data.reference);
                $("#step-2 .input").addClass("hidden")
                  .find("#name").val(data.name);
                $("#step-2 .message")
                  .text(texts.step_2.message_existing
                    .replace(/%name/, firstName(data.name)));
              }
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
        if (validateEmail(values.email) && values.name) {
          loading(true);
          if (!nameKnown) {
            $("#step-3 .message").text(texts.step_3.message_new
              .replace(/%name/, firstName(values.name)));
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
        if (validateEmail(values.email) && values.name) {
          loading(true);
          switch (values.coming) {
            case "0":
              $("#step-4 .message").text(texts.step_4.message_coming_no);
              $("#step-5 .message:eq(0)").text(texts.step_5.message_coming_no);
              $("#step-4 .input").addClass("hidden");
              break;
            case "0.5":
              $("#step-4 .message").text(texts.step_4.message_coming_maybe);
              $("#step-5 .message:eq(0)").text(texts.step_5.message_coming_maybe);
              break;
            case "1":
              $("#step-4 .message").text(texts.step_4.message_coming_yes);
              $("#step-5 .message:eq(0)").text(texts.step_5.message_coming_yes);
              break;
          }
          showNext(function() {
            loading(false);
            $("#submit", $form).val(texts.submit);
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
        if (validateEmail(values.email) && values.name) {
          loading(true);
          $.post("guest.php", values, function(data) {
            if (/failed/.test(data) || /error/.test(data)) {
              loading(false);
              alert(texts.submit_error);
            }
            else {
              $("#submit", $form).slideUp(200);
              loading(false);
              showNext();
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
