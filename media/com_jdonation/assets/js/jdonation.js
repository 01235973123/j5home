/****
 * Payme method class
 * @param id
 * @param name
 * @param title
 * @param creditCard
 * @param cardType
 * @param cardCvv
 * @param cardHolderName
 * @return
 */
function PaymentMethod(name, creditCard, cardType, cardCvv, cardHolderName, enableRecurring) {	
	this.name = name ;	
	this.creditCard = creditCard ;
	this.cardType = cardType ;
	this.cardCvv = cardCvv ;
	this.cardHolderName = cardHolderName ;
	this.enableRecurring = enableRecurring ;
}
/***
 * Get name of the payment method
 * @return string
 */
PaymentMethod.prototype.getName = function() {
	return this.name ;
}
/***
 * This is creditcard payment method or not	
 * @return int
 */
PaymentMethod.prototype.getCreditCard = function() {
	return this.creditCard ;
}
/****
 * Show creditcard type or not
 * @return string
 */
PaymentMethod.prototype.getCardType = function() {
	return this.cardType ;
}
/***
 * Check to see whether card cvv code is required
 * @return string
 */
PaymentMethod.prototype.getCardCvv = function() {
	return this.cardCvv ;
}
/***
 * Check to see whether this payment method require entering card holder name
 * @return
 */
PaymentMethod.prototype.getCardHolderName = function() {
	return this.cardHolderName ;
}
/***
* Get name of the payment method
* @return string
*/
PaymentMethod.prototype.getEnableRecurring = function() {
	  return this.enableRecurring ;
}
/***
 * Payment method class, hold all the payment methods
 */
function PaymentMethods() {
	this.length = 0 ;
	this.methods = new Array();
}
/***
 * Add a payment method to array
 * @param paymentMethod
 * @return
 */
 PaymentMethods.prototype.Add = function(paymentMethod) {	
	this.methods[this.length] = paymentMethod ;
	this.length = this.length + 1 ;
}
/***
 * Find a payment method based on it's name
 * @param name
 * @return {@link PaymentMethod}
 */
 PaymentMethods.prototype.Find = function(name) {
	for (var i = 0 ; i < this.length ; i++) {
		if (this.methods[i].name == name) {
			return this.methods[i] ;			
		}
	}
	return null ;
}

/*
var stripeResponseHandler = function(status, response) {
    JD.jQuery(function($) {
        var $form = $('#os_form');
        if (response.error) {
            // Show the errors on the form
            //$form.find('.payment-errors').text(response.error.message);
            alert(response.error.message);
            $form.find('#btn-submit').prop('disabled', false);
        } else {
            // token contains id, last4, and card type
            var token = response.id;
            // Empty card data since we now have token
            $('input[name^=x_card_num]').val('');
            $('input[name^=x_card_code]').val('');
            $('input[name^=card_holder_name]').val('');
            // Insert the token into the form so it gets submitted to the server
            $form.append($('<input type="hidden" name="stripeToken" />').val(token));
            // and re-submit
            $form.get(0).submit();
        }
    });
};
*/

var stripeResponseHandler = function (status, response) {
    JD.jQuery(function ($) {
        var $form = $('#os_form');
        if (response.error) {
            // Show the errors on the form
            //$form.find('.payment-errors').text(response.error.message);

            if (response.error.type == 'card_error' && (typeof jdStripeErrors !== 'undefined') && jdStripeErrors[response.error.code]) {
                response.error.message = jdStripeErrors[response.error.code];
            }
            alert(response.error.message);
            $form.find('#btn-submit').prop('disabled', false);
        } else {
            // token contains id, last4, and card type
            var token = response.id;
            // Empty card data since we now have token
            $('#x_card_num').val('');
            $('#x_card_code').val('');
            $('#card_holder_name').val('');
            // Insert the token into the form so it gets submitted to the server
            $form.append($('<input type="hidden" name="stripeToken" />').val(token));
            // and re-submit
            $form.get(0).submit();
        }
    });
};

function stripeTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById('os_form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);

    // Submit the form
    form.submit();
}

JD.jQuery(function($){
	/**
	 * JD validate form
	 */
	JDVALIDATEFORM = (function(formId){
        $(formId).validationEngine('attach', {
            onValidationComplete: function(form, status){
                if (status == true) {
                    form.on('submit', function(e) {
                        e.preventDefault();
                    });
                    return true;
                }
                return false;
            }
        });
	})
	
	$('#donation_typerecurring').click(function() { 
		if ($('#donation_typerecurring').is(':checked')) 
		{
			$('#r_frequency').addClass('validate[required]');
		}
		else
		{
			if(recurring_require == 0)
			{
				$('#r_frequency').removeClass('validate[required]');
			}
		}
	});

	/**
	 * clear text box
	 */
	clearTextbox = (function(){
		var smallInput = $('#smallinput').val();
		$('[name^=amount]').attr('class', smallInput);
		$('[name^=rd_amount]').addClass('validate[required]');
	    if ($('[name^=amount]').val())
	    {
	    	$('[name^=amount]').val('');
	    }

		//summary
		//var amounts_format = $("#amounts_format").val();
		if(amounts_format == "1")
		{
			var donated_amount = $("input[name='rd_amount']:checked"). val();
		}
		else
		{
			var donated_amount = $("select[name='rd_amount'] option:selected").val();
		}
		
		donated_amount = parseFloat(donated_amount);

		$('#amount').val(donated_amount);

		var selected_payment;
		if ( selected_payment == "")
		{
			selected_payment		= $("input[name='payment_method']:checked"). val();
		}
		
		var allow_payment_fee = 0;
		if($("input[name='pay_payment_gateway_fee']:checked"). val() == '1')
		{
			 allow_payment_fee = 1;
		}
		var currency_code		= $("#currency_code").val();
		var data = {
			'task'	 : 'donation.summary',
			'amount' : donated_amount,
			'payment': selected_payment,
			'currency_code': currency_code,
			'payment_fee_pay' : allow_payment_fee
		};
		$.ajax({
			type: 'POST',
			url: root_path + 'index.php?option=com_jdonation',
			data: data,
			dataType: 'html',
			success: function(htmltext) {
				$('#donatedAmount').html(htmltext);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				//alert(errorThrown);
			}
		});
		//$('#donatedAmount').append(donated_amount);
	});
	/**
	 * chnage donation type
	 * donation_type_heading
	 */
	changeDonationType = (function(){
		var donationType = $('input[name^=donation_type]:checked').val();
		if (donationType == 'recurring')
		{
		    $('#tr_frequency').slideDown();
		    $('#tr_number_donations').slideDown();
		}
		else
		{
		    $('#tr_frequency').slideUp();
		    $('#tr_number_donations').slideUp();
		}
	});

	

	updateSummary = (function(allow_payment_fee){
		if(typeof selected_payment !== 'undefined')
		{

			if ( selected_payment == '')
			{
				var payment_method	= $("input[name='payment_method']:checked"). val();
			}
			else
			{
				payment_method = selected_payment;
			}
		}
		else
		{
			var payment_method	= $("input[name='payment_method']:checked"). val();
		}

		//summary
		var donated_amount = 0;
		if(amounts_format == "1")
		{
			if($("input[name='rd_amount']").is(":checked"))
			{
				donated_amount = $("input[name='rd_amount']:checked"). val();
				donated_amount = parseFloat(donated_amount);
			}
		}
		else
		{
			   donated_amount = $("select[name='rd_amount'] option:selected").val();
			   donated_amount = parseFloat(donated_amount);
		}
		
		
		var custom_amount = $('[name^=amount]').val();
		if(custom_amount != '')
		{
			custom_amount = parseFloat(custom_amount);
			if(donated_amount == 0)
			{
				donated_amount = custom_amount;
			}
		}
		var allow_payment_fee = 0;
		if($("input[name='pay_payment_gateway_fee']:checked"). val() == '1')
		{
			 allow_payment_fee = 1;
		}
		var currency_code		= $("#currency_code").val();
		var data = {
			'task'	 : 'donation.summary',
			'amount' : donated_amount,
			'payment': payment_method,
			'currency_code': currency_code,
			'payment_fee_pay' : allow_payment_fee
		};
		$.ajax({
			type: 'POST',
			url: root_path + 'index.php?option=com_jdonation',
			data: data,
			dataType: 'html',
			success: function(htmltext) {
				$('#donatedAmount').html(htmltext);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				//alert(errorThrown);
			}
		});
	});
	/***
	 * Process event when someone change a payment method
	 */
	changePaymentMethod = (function(){
		var paymentMethod = $('input[name^=payment_method]:checked').val();
		method = methods.Find(paymentMethod);
		document.querySelectorAll('div.payment-form').forEach(function(div) {
			div.style.display = 'none';
		});
		document.getElementById(paymentMethod + '_message').style.display = 'block';
		
		if (method.getCreditCard()) {
			$('#creditcarddivmain').slideDown();
		    $('#tr_card_number').slideDown();
		    $('#tr_exp_date').slideDown();
		    $('#tr_cvv_code').slideDown();
		    if (method.getCardType())
		    {
		        $('#tr_card_type').slideDown();
		    }
		    else
		    {
		        $('#tr_card_type').slideUp();
		    }
		    if (method.getCardHolderName())
		    {
		        $('#tr_card_holder_name').slideDown();
		    }
		    else
		    {
		        $('#tr_card_holder_name').slideUp();
		    }
		}
		else
		{
			$('#creditcarddivmain').slideUp();
		    $('#tr_card_number').slideUp();
		    $('#tr_exp_date').slideUp();
		    $('#tr_cvv_code').slideUp();
		    $('#tr_card_type').slideUp();
		    $('#tr_card_holder_name').slideUp();
		}
		
		if (paymentMethod == 'os_echeck')
		{
		    $('#tr_bank_rounting_number').slideDown();
		    $('#tr_bank_account_number').slideDown();
		    $('#tr_bank_account_type').slideDown();
		    $('#tr_bank_name').slideDown();
		    $('#tr_bank_account_holder').slideDown();
		}
		else
		{
		    if ($('#tr_bank_rounting_number').length)
		    {
		        $('#tr_bank_rounting_number').slideUp();
		        $('#tr_bank_account_number').slideUp();
		        $('#tr_bank_account_type').slideUp();
		        $('#tr_bank_name').slideUp();
		        $('#tr_bank_account_holder').slideUp();
		    }
		}
		if (paymentMethod == 'os_sisow')
		{
		    $('#tr_bank_lists').slideDown();
		}
		else
		{
		    $('#tr_bank_lists').slideUp();
		}

		if (paymentMethod == 'os_squareup') {
            $('#sq_field_zipcode').show();
        }
        else {
            $('#sq_field_zipcode').hide();
        }

		if (paymentMethod == 'os_squarecard') {
            $('#square-card-form').show();
        }
        else {
            $('#square-card-form').hide();
        }

		if (typeof stripe !== 'undefined')
        {
            if (paymentMethod.indexOf('os_stripe') == 0)
            {
                $('#stripe-card-form').show();
            }
            else
            {
                $('#stripe-card-form').hide();
            }
        }


		displayRecurringOptions();

		//summary
		var donated_amount = 0;
		if($("input[name='rd_amount']").is(":checked"))
		{
			donated_amount = $("input[name='rd_amount']:checked"). val();
			
			donated_amount = parseFloat(donated_amount);
		}
		//console.log("Selected option " + donated_amount);
		var custom_amount = $('[name^=amount]').val();
		//console.log("Selected option " + custom_amount);
		if(custom_amount != '')
		{
			custom_amount = parseFloat(custom_amount);
			if(donated_amount <= 0)
			{
				donated_amount = custom_amount;
			}
		}
		var allow_payment_fee = 0;
		if($("input[name='pay_payment_gateway_fee']:checked"). val() == '1')
		{
			 allow_payment_fee = 1;
		}
		var currency_code		= $("#currency_code").val();
		var data = {
			'task'	 : 'donation.summary',
			'amount' : donated_amount,
			'payment': paymentMethod,
			'currency_code' : currency_code,
			'payment_fee_pay' : allow_payment_fee
		};
		$.ajax({
			type: 'POST',
			url: root_path + 'index.php?option=com_jdonation',
			data: data,
			dataType: 'html',
			success: function(htmltext) {
				$('#donatedAmount').html(htmltext);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				//alert(errorThrown);
			}
		});

	});
	/**
	 * process change campaign
	 */
	processChangeCampaign = (function()
	{
        var campaignId = $('#campaign_id').val();
        if($("[name*='field_campaign']").val())
        {
        	if($("[name*='current_campaign']").val() > 0)
			{
				$('.campaign_' + currentCampaign).slideUp();
			}	
			$('.campaign_' + campaignId).slideDown();
        }
        if($("[name*='amount_by_campaign']").val())
		{
			if (campaignId)
			{
				$('#amount_container').html($('#campaign_' + campaignId).html());
			}
			else
			{
				$('#amount_container').html($('#amount_container_backup').html());
			}
		}

		if(campaignId)
		{
			//alert('campaign_recurring_frequency_' + campaignId);
			$('#recurringFrequency').html($('#campaign_recurring_frequency_' + campaignId).html());
		}
		else
		{
			$('#recurringFrequency').html($('#recurringFrequencyBackup').html());
		}

        displayRecurringOptions();
        currentCampaign = campaignId;
		$("[name*='current_campaign']").val(currentCampaign);

		if(campaignId != "")
		{
			if($("#curr_" + campaignId).val() != "")
			{
				$("#currency_selection").slideUp();
				$("#campaign_currency").slideDown();
				$("#campaign_currency").html($("#curr_" + campaignId).val());
			}
			else
			{
				$("#currency_selection").slideDown();
				$("#campaign_currency").slideUp();
			}

			if($("#activate_dedicate_" + campaignId).val() == "1")
			{
				$("#dedicate_heading").slideDown();
				$("#honoreediv").slideDown();
			}
			else
			{
				$("#dedicate_heading").slideUp();
				$("#honoreediv").slideUp();
			}
		}
		else
		{
			$("#currency_selection").slideDown();
			$("#campaign_currency").slideUp();
			if($("#activate_dedicate").val() == "1")
			{
				$("#dedicate_heading").slideDown();
				$("#honoreediv").slideDown();
			}
			else
			{
				$("#dedicate_heading").slideUp();
				$("#honoreediv").slideUp();
			}
		}
    });

	deSelectRadio1 = (function(){
		$('[name^=amount]').attr('class', amountInputCssClasses);
	});

	/**
	 * De select radio
	 */    
	deSelectRadio = (function(){
		  var selected_payment;
		  $('[name^=amount]').attr('class', amountInputCssClasses);
		  $('[name^=rd_amount]').removeClass('validate[required]');
		  //remove it if customers need to be able to enter comma separator
		  $('[name^=amount]').val($('[name^=amount]').val().replace(',',''));
          if (parseFloat($('[name^=amount]').val()))
          {
        	  if ($('[name^=rd_amount]').length)
              {
        		  $('[name^=rd_amount]').prop("checked", false);
              }
			  //summary
			  //$('#donatedAmount').append($('[name^=amount]').val());
			    if(selected_payment == '')
			    {
					var payment_method	= $("input[name='payment_method']:checked"). val();
				}
				else
			    {
					payment_method = selected_payment;
			    }
				var allow_payment_fee = 0;
				if($("input[name='pay_payment_gateway_fee']:checked"). val() == '1')
				{
					 allow_payment_fee = 1;
				}
				var data = {
					'task'	 : 'donation.summary',
					'amount' : $('[name^=amount]').val(),
					'payment': payment_method,
					'payment_fee_pay' : allow_payment_fee
				};
				$.ajax({
					type: 'POST',
					url: root_path + 'index.php?option=com_jdonation',
					data: data,
					dataType: 'html',
					success: function(htmltext) {
						$('#donatedAmount').html(htmltext);
					},
					error: function(jqXHR, textStatus, errorThrown) {
						//alert(errorThrown);
					}
				});
          }
          else
          {
        	  $('[name^=amount]').val('');
          }		
    });
    /**
     * display recurring options
     */
	displayRecurringOptions = (function(){
		if($('[name^=enable_recurring]').val())
    	{
			var campaignId = $('#campaign_id').val();
			var paymentMethod = '';
			var showRecurringOptions = 1;
            var donationType = 0;
			if($("[name*='count_method']").val() > 1)
			{
				paymentMethod = $('input[name^=payment_method]:checked').val();
			}
			else
			{
				paymentMethod = document.os_form.payment_method.value;
			}
			if (campaignId > 0)
			{
                donationType = recurrings[campaignId];
                if (donationType == 1)
                {
                    showRecurringOptions = false;
                }
			}
			method = methods.Find(paymentMethod);
			if (showRecurringOptions && method.getEnableRecurring())
			{
                if (donationType == 0)
                {
                    $('#donation_type').slideDown();
					$('#donation_type_heading').slideDown();
					$('#checkout_heading').html('<strong>4</strong>');
					$('#donor_information_heading').html('<strong>3</strong>');
                }
                if (donationType == 2)
                {
					$('#donation_type_heading').slideDown();
					$('#checkout_heading').html('<strong>4</strong>');
					$('#donor_information_heading').html('<strong>3</strong>');
                    $('input[name="donation_type"][value="recurring"]').prop('checked', true);
                }
				var donationType = $('input[name^=donation_type]:checked').val();
				if (donationType == 'recurring')
				{
					$('#tr_frequency').slideDown();
					$('#tr_number_donations').slideDown();
				}
			}
			else
			{
				$('#donation_type').slideUp();
				$('#tr_frequency').slideUp();
				$('#donation_type_heading').slideUp();
				$('#checkout_heading').html('<strong>3</strong>');
				$('#donor_information_heading').html('<strong>2</strong>');
				$('#tr_number_donations').slideUp();
				$('input[name="donation_type"][value="onetime"]').prop('checked', true);
			}
    	}
    });
	/**
	 * build state field
	 */
	buildStateField = (function(stateFieldId, countryFieldId, defaultState){
		if($('#' + stateFieldId).length)
		{
			//set state
			if ($('#' + countryFieldId).length)
			{
				var countryName = $('#' + countryFieldId).val();
			}
			else 
			{
				var countryName = '';
			}
			$.ajax({
				type: 'POST',
				url: siteUrl + 'index.php?option=com_jdonation&task=get_states&country_name='+ countryName+'&field_name='+stateFieldId + '&state_name=' + defaultState,
				success: function(data) {
					$('#field_' + stateFieldId + ' .controls').html(data);
					$('#field_' + stateFieldId + ' .col-md-9').html(data);
				},
				error: function(jqXHR, textStatus, errorThrown) {						
					//alert(textStatus);
				}
			});	
			//Bind onchange event to the country 
			if ($('#' + countryFieldId).length)
			{
				$('#' + countryFieldId).change(function(){
					$('#field_' + stateFieldId + ' .controls select').after('<span class="wait">&nbsp;<img src="components/com_jdonation/assets/images/loading.gif" alt="" /></span>');
					$.ajax({
						type: 'POST',
						url: siteUrl + 'index.php?option=com_jdonation&task=get_states&country_name='+ $(this).val()+'&field_name=' + stateFieldId + '&state_name=' + defaultState,
						success: function(data) {
							$('#field_' + stateFieldId + '_select').html(data);
							$('#field_' + stateFieldId + ' .col-md-9').html(data);
							$('.wait').remove();
						},
						error: function(jqXHR, textStatus, errorThrown) {						
							//alert(textStatus);
						}
					});
					
				});
			}						
		}//end check exits state
	});

	buildStateFieldSimple = (function(stateFieldId, countryFieldId, defaultState){
		if($('#' + stateFieldId).length)
		{
			//set state

			if ($('#' + countryFieldId).length)
			{
				var countryName = $('#' + countryFieldId).val();
			}
			else 
			{
				var countryName = '';
			}
			//alert(siteUrl + 'index.php?option=com_jdonation&task=get_states&country_name='+ countryName+'&field_name='+stateFieldId + '&state_name=' + defaultState);
			$.ajax({
				type: 'POST',
				url: siteUrl + 'index.php?option=com_jdonation&task=get_states&country_name='+ countryName+'&field_name='+stateFieldId + '&state_name=' + defaultState,
				success: function(data) {
					$('#field_' + stateFieldId).html(data);
				},
				error: function(jqXHR, textStatus, errorThrown) {						
					//alert(textStatus);
				}
			});	
			//Bind onchange event to the country 
			if ($('#' + countryFieldId).length)
			{
				$('#' + countryFieldId).change(function(){
					$('#field_' + stateFieldId + ' .controls select').after('<span class="wait">&nbsp;<img src="components/com_jdonation/assets/images/loading.gif" alt="" /></span>');
					$.ajax({
						type: 'POST',
						url: siteUrl + 'index.php?option=com_jdonation&task=get_states&country_name='+ $(this).val()+'&field_name=' + stateFieldId + '&state_name=' + defaultState,
						success: function(data) {
							$('#field_' + stateFieldId).html(data);
							$('.wait').remove();
						},
						error: function(jqXHR, textStatus, errorThrown) {						
							//alert(textStatus);
						}
					});
					
				});
			}						
		}//end check exits state
	});

	JDMaskInputs = function (form) {
        form.querySelectorAll('input[data-input-mask]').forEach(function (input) {
            var mask = input.dataset.inputMask;

            // Assume this is a regular expression
            if (mask.slice(0, 1) === '/' && mask.slice(-1) === '/') {
                mask = mask.slice(1); // Remove first character
                mask = mask.slice(0, -1); // Remove last character
                mask = new RegExp(mask);
            }

            var regExpMask = IMask(
                input,
                {
                    mask: mask
                });
        });
    };
	
})

function showDedicate(){
	var show_dedicate = jQuery('#show_dedicate').val();
	if(show_dedicate == 0){
		jQuery('#show_dedicate').val('1');
		jQuery('#honoreediv').removeClass('nodisplay');
		jQuery('#honoreediv').slideDown();
	}else{
		jQuery('#show_dedicate').val('0');
		jQuery('#honoreediv').slideUp();
	}
}