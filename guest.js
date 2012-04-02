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
        $("#step-1").slideDown(500, function() {
          $("#submit").val(texts.proceed).slideDown();
        })
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
            loading(false);
            $("#step-2").slideDown();
          });
        }
        break;
      case 2:
        if (values.email && values.name) {
          step = 3;
          if (!nameKnown) {
            $("#step-3 .message").text(texts.step_3.message_new
              .replace(/%name/, firstName(values.name)));
          }
          $("#step-3").slideDown();
        }
        else {
          alert(texts.input_error);
        }
        break;
      case 3:
        if (values.email && values.name) {
          step = 4;
          switch (values.coming) {
            case "0":
              $("#step-4 .message").text(texts.step_4.message_coming_no);
              $("#step-4 .input").hide();
              $form.submit(); // No need to fill out step 4.
              break;
            case "0.5":
              $("#step-4 .message").text(texts.step_4.message_coming_maybe);
              break;
            case "1":
              $("#step-4 .message").text(texts.step_4.message_coming_yes);
              break;
          }
          $("submit", $form).val(texts.submit);
          $("#step-4").slideDown();
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
            $("#submit", $form).slideUp();
            $("#step-5").slideDown();
          }
          loading(false);
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
