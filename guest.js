/**
 * guest.js
 * Provide interactive steps for the RSVS form.
 *
 * @todo: Scroll to bottom when adding messages.
 * @todo: Focus on current input field.
 * @todo: Disable submitted fields.
 */
(function($) {

  var values = {},
    step = 0,
    $form,
    nameKnown = false;
  
  var getFormValues = function($form) {
    $.each($form.serializeArray(), function(i, field) {
      values[field.name] = field.value;
    });
  }

  var firstName = function(name) {
    return name.split(" ")[0];
  }

  var submitForm = function() {
    getFormValues($form);
    changeState();
    return false;
  }

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

  // Show next step.
  var showNext = function(callback) {
    var intervalTimer;
    step++;
    var $items = $("#step-" + step).find(".message, .input").not(".hidden");
    var i = 0;

    // Process each item at a time interval.
    if ($items.length > 0) {
      intervalTimer = setInterval(function() {
        var $item = $items.eq(i);
        if ($item.length > 0) {
          i++;
          $item.slideDown();
        }
        else { // All items processed.
          clearInterval(intervalTimer);
          callback();
        }
      }, 500);
    }
  }

  // Check state and increment step.
  var changeState = function() {
    switch (step) {
      case 0:
        showNext(function() {
          $("#submit").val(texts.proceed).fadeIn(100);
        });
        break;

      case 1:
        if (values.email) {
          loading(true);
          $.getJSON("guest.php?email=" + values.email, function(data) {
            if (data && data.email) {
              $form.find("#email").attr("disabled", "disabled");
              if (data.name) {
                nameKnown = true;
                $("#step-2 .input").addClass("hidden")
                  .find("input").val(data.name);
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
        break;
      case 2:
        if (values.email && values.name) {
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
      case 3:
        if (values.email && values.name) {
          loading(true);
          switch (values.coming) {
            case "0":
              $("#step-4 .message").text(texts.step_4.message_coming_no);
              $("#step-5 .message:eq(0)").text(texts.step_5.message_coming_no);
              $("#step-4 .input").addClass("hidden");
              $form.submit(); // No need to fill out step 4.
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
          $("submit", $form).val(texts.submit);
          showNext(function() {
            loading(false);
          });
        }
        else {
          alert(texts.input_error);
        }
        break;
      case 4:
        loading(true);
        $.post("guest.php", values, function(data) {
          if (/failed/.test(data) || /error/.test(data)) {
            alert(texts.submit_error);
          }
          else {
            $("#submit", $form).fadeOut(100, function() {
              loading(false);
              showNext();
            });
          }
        });
        break;
    }
  }

  $(document).ready(function() {
    $form = $("#rsvp");
    $form.submit(submitForm);
    changeState();
  });

})(jQuery)
