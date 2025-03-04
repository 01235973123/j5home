(function (document, $) {
    $(document).ready(function () {
        $("#adminForm").validationEngine('attach', {
            onValidationComplete: function(form, status){
                if (status === true) {
                    form.on('submit', function(e) {
                        e.preventDefault();
                    });

                    form.find('#btn-submit').prop('disabled', true);

                    return paymentMethodCallbackHandle();
                }
                return false;
            }
        });

        buildStateFields('state', 'country', Joomla.getOptions('selectedState'));

        if (typeof stripe !== 'undefined')
        {
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

        if (Joomla.getOptions('squareAppId'))
        {
            createSquareCardElement();
        }

        // Dispatch custom event
        document.dispatchEvent(new CustomEvent("onEBAfterRegistrationFormLoaded", {
            detail: { registrationType: 'registrationPayment'}
        }));
    });

    calculateRegistrationFee= function()
    {
        updatePaymentMethod();

        if (document.adminForm.show_payment_fee.value == 1)
        {
            var paymentMethod,
                registrantId = $('#registrant_id').val(),
                $btnSubmit = $('#btn-submit'),
                $loadingAnimation = $('#ajax-loading-animation');

            $btnSubmit.attr('disabled', 'disabled');
            $loadingAnimation.show();

            if ($('input:radio[name^=payment_method]').length)
            {
                paymentMethod = $('input:radio[name^=payment_method]:checked').val();
            }
            else
            {
                paymentMethod = $('input[name^=payment_method]').val();
            }

            $.ajax({
                type: 'GET',
                url: siteUrl + 'index.php?option=com_eventbooking&task=register.calculate_registration_fee&payment_method=' + paymentMethod + '&registrant_id=' + registrantId,
                dataType: 'json',
                success: function (msg, textStatus, xhr)
                {
                    $btnSubmit.removeAttr('disabled');
                    $loadingAnimation.hide();

                    if ($('#amount').length)
                    {
                        $('#total_amount').val(msg.amount);
                    }

                    $('#payment_processing_fee').val(msg.payment_processing_fee);
                    $('#gross_amount').val(msg.gross_amount);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert(textStatus);
                }
            });
        }
    };


    calculateRemainderFee = function()
    {
        updatePaymentMethod();

        if (document.adminForm.show_payment_fee.value == 1)
        {
            var paymentMethod,
                registrantId = $('#registrant_id').val(),
                $btnSubmit = $('#btn-submit'),
                $loadingAnimation = $('#ajax-loading-animation');

            $btnSubmit.attr('disabled', 'disabled');
            $loadingAnimation.show();

            if ($('input:radio[name^=payment_method]').length)
            {
                paymentMethod = $('input:radio[name^=payment_method]:checked').val();
            }
            else
            {
                paymentMethod = $('input[name^=payment_method]').val();
            }

            $.ajax({
                type: 'GET',
                url: siteUrl + 'index.php?option=com_eventbooking&task=register.calculate_remainder_fee&payment_method=' + paymentMethod + '&registrant_id=' + registrantId,
                dataType: 'json',
                success: function (msg, textStatus, xhr)
                {
                    $btnSubmit.removeAttr('disabled');
                    $loadingAnimation.hide();

                    if ($('#amount').length)
                    {
                        $('#total_amount').val(msg.amount);
                    }

                    $('#payment_processing_fee').val(msg.payment_processing_fee);
                    $('#gross_amount').val(msg.gross_amount);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert(textStatus);
                }
            });
        }
    };
})(document, Eb.jQuery);