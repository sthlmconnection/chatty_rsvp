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

  // Check state and increment step.
  var changeState = function() {
    switch (step) {
      case 0:
        $("#step-1 .message").delay(1000).slideDown(200, function() {
          $("#step-1 .input").delay(1000).slideDown(200, function() {
            $("#submit").val(texts.proceed).fadeIn(100);
          });
        });
        step = 1;
        break;

      case 1:
        if (values.email) {
          loading(true);
          $.getJSON("guest.php?email=" + values.email, function(data) {
            step = 2;
            if (data && data.email) {
              $form.find("#email").attr("disabled", "disabled");
              if (data.name) {
                nameKnown = true;
                $("#name").val(data.name).hide();
                $("#step-2 .message")
                  .text(texts.step_2.message_existing
                    .replace(/%name/, firstName(data.name)));
                $form.submit(); // No need to fill out step 2.
              }
            }
            $("#step-2 .message").slideDown(200, function() {
              if (!nameKnown) {
                $("#step-2 .input").delay(500).slideDown(200);
              }
              loading(false);
            });
          });
        }
        break;
      case 2:
        if (values.email && values.name) {
          loading(true);
          step = 3;
          if (!nameKnown) {
            $("#step-3 .message").text(texts.step_3.message_new
              .replace(/%name/, firstName(values.name)));
          }
          $("#step-3 .message").delay(nameKnown ? 1000 : 500).slideDown(200, function() {
            $("#step-3 .input").delay(500).slideDown(200, function() {
              loading(false);
            });
          });
        }
        else {
          alert(texts.input_error);
        }
        break;
      case 3:
        if (values.email && values.name) {
          loading(true);
          step = 4;
          switch (values.coming) {
            case "0":
              $("#step-4 .message").text(texts.step_4.message_coming_no);
              $("#step-5 .message:eq(0)").text(texts.step_5.message_coming_no);
              $("#step-4 .input").attr("data-hide", "true");
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
          $("#step-4 .message").delay(500).slideDown(200, function() {
            $("#step-4 .input:not([data-hide=true])").delay(500).slideDown(200);
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
            step = 5;
            $("#submit", $form).fadeOut(100, function() {
              loading(false);
              $("#step-5 .message:eq(0)").slideDown(200, function() {
                $("#step-5 .message:eq(1)").delay(500).slideDown(200);
              });
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
