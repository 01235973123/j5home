(function (document, $) {
	$(document).ready(function () {
		buildStateFields('state', 'country', Joomla.getOptions('selectedState'));

		var paymentNeeded = Joomla.getOptions('paymentNeeded');
		var hasStripePaymentMethod = Joomla.getOptions('hasStripePaymentMethod');

		if (hasStripePaymentMethod && typeof stripe !== 'undefined') {
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

		if (Joomla.getOptions('squareAppId')) {
			createSquareCardElement();
		}

		$("#os_form").validationEngine('attach', {
			onValidationComplete: function (form, status) {
				if (status === true) {
					form.on('submit', function (e) {
						e.preventDefault();
					});

					form.find('#btn-submit').prop('disabled', true);

					if (paymentNeeded) {
						var paymentMethod;

						if ($('input:radio[name="payment_method"]').length) {
							paymentMethod = $('input:radio[name="payment_method"]:checked').val();
						} else {
							paymentMethod = $('input[name="payment_method"]').val();
						}

						if (!document.dispatchEvent(new CustomEvent('OSMPaymentCallbackHandle', {
							detail: {
								paymentMethod: paymentMethod
							}
						}))) {
							return false;
						}

						if (typeof stripePublicKey !== 'undefined' && paymentMethod.indexOf('os_stripe') === 0 && $('#tr_card_number').is(':visible')) {
							Stripe.card.createToken({
								number: $('input[name="x_card_num"]').val(),
								cvc: $('input[name="x_card_code"]').val(),
								exp_month: $('select[name="exp_month"]').val(),
								exp_year: $('select[name="exp_year"]').val(),
								name: $('input[name="card_holder_name"]').val()
							}, stripeResponseHandler);

							return false;
						}

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

						if (paymentMethod.indexOf('os_squarecard') === 0) {
							squareCardCallBackHandle();

							return false;
						}
					}

					return true;
				}

				return false;
			}
		});

		document.dispatchEvent(new CustomEvent('OSMSubscriptionFormLoaded'));
	});
})(document, jQuery);