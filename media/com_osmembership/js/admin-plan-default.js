(function (document, Joomla) {
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;

        if (pressbutton === 'cancel') {
            Joomla.submitform(pressbutton, form);
        } else {
            //Validate the entered data before submitting
            if (form.title.value === '') {
                alert(Joomla.JText._('OSM_ENTER_PLAN_TITLE'));
                form.title.focus();
                return;
            }

            var recurringSubscription = form.recurring_subscription.value;

            if (form.subscription_length.value <= 0 && recurringSubscription === '1') {
                alert(Joomla.JText._('OSM_ENTER_SUBSCRIPTION_LENGTH'));
                form.subscription_length.focus();
                return;
            }
            
            if (recurringSubscription === '1' && form.price.value === '') {
                alert(Joomla.JText._('OSM_PRICE_REQUIRED'));
                form.price.focus();
                return;
            }

            document.querySelectorAll('.article-checkbox').forEach(function (item) {
                item.checked = false;
            });

            document.querySelectorAll('.k2-item-checkbox').forEach(function (item) {
                item.checked = false;
            });

            document.querySelectorAll('.sppb-page-checkbox').forEach(function (item) {
                item.checked = false;
            });

            Joomla.submitform(pressbutton, form);
        }
    };
})(document, Joomla);