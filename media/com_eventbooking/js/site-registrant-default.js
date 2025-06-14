(function (document, $) {
  setRecalculateFee = function () {
    $("#re_calculate_fee").prop("checked", true);
  };

  populateRegistrantData = function () {
    var userId;

    if (document.getElementById("user_id_id")) {
      userId = document.getElementById("user_id_id").value;
    } else {
      userId = $("#user_id").val();
    }

    var eventId = $("#event_id").val();

    var rootUri = Joomla.getOptions("rootUri");
    $.ajax({
      type: "GET",
      url:
        rootUri +
        "/index.php?option=com_eventbooking&task=get_profile_data&user_id=" +
        userId +
        "&event_id=" +
        eventId,
      dataType: "json",
      success: function (json) {
        var selecteds = [];
        for (var field in json) {
          value = json[field];
          if ($("input[name='" + field + "[]']").length) {
            //This is a checkbox or multiple select
            if ($.isArray(value)) {
              selecteds = value;
            } else {
              selecteds.push(value);
            }

            $("input[name='" + field + "[]']").val(selecteds);
          } else if ($("input[type='radio'][name='" + field + "']").length) {
            $("input[name=" + field + "][value=" + value + "]").attr(
              "checked",
              "checked"
            );
          } else if (
            $("#" + field).closest(".field-calendar").length > 0 &&
            $("#" + field).next("button.btn-primary[data-inputfield]").length >
              0
          ) {
            $("#" + field).val(value);
            $("#" + field).attr("data-alt-value", value);
            $("#" + field).attr("data-local-value", value);
          } else {
            $("#" + field).val(value);
          }
        }
      },
    });
  };

  Joomla.submitbutton = function (pressbutton) {
    if (pressbutton === "registrant.cancel_edit") {
      $("#adminForm").validationEngine("detach");
      Joomla.submitform(pressbutton);
    } else if (pressbutton === "registrant.cancel") {
      if (confirm(Joomla.JText._("EB_CANCEL_REGISTRATION_CONFIRM"))) {
        Joomla.submitform(pressbutton);
      }
    } else if (pressbutton === "registrant.refund") {
      if (confirm(Joomla.JText._("EB_REFUND_REGISTRATION_CONFIRM"))) {
        Joomla.submitform(pressbutton);
      }
    } else {
      Joomla.submitform(pressbutton);
    }
  };

  $(document).ready(function () {
    $("#adminForm").validationEngine();
    buildStateFields("state", "country", Joomla.getOptions("selectedState"));
    EBMaskInputs(document.getElementById("adminForm"));
  });
})(document, Eb.jQuery);
