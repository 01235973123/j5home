(function (document, $) {
    $(document).ready(function(){

        var paymentMethod = Joomla.getOptions('paymentMethod', '');

        if (paymentMethod.indexOf('os_stripe') === 0 && typeof stripe !== 'undefined') {
            var style = {
                base: {
                    // Add your base input styles here. For example:
                    fontSize: '16px',
                    color: "#32325d",
                }
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {style: style});

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#stripe-card-element');
        }

        $("#os_form").validationEngine('attach', {
            onValidationComplete: function(form, status){
                if (status === true) {
                    form.on('submit', function(e) {
                        e.preventDefault();
                    });

                    form.find('#btn-submit').prop('disabled', true);

                    // Stripe card element
                    if (typeof stripe !== 'undefined' && paymentMethod.indexOf('os_stripe') === 0 && $('#stripe-card-form').is(":visible")) {
                        stripe.createToken(card).then(function (result) {
                            if (result.error) {
                                // Inform the customer that there was an error.
                                //var errorElement = document.getElementById('card-errors');
                                //errorElement.textContent = result.error.message;
                                alert(result.error.message);
                                form.find('#btn-submit').removeAttr('disabled');
                            } else {
                                // Send the token to your server.
                                stripeTokenHandler(result.token);
                            }
                        });

                        return false;
                    }

                    return true;
                }
                return false;
            }
        });
    });
})(document, jQuery);