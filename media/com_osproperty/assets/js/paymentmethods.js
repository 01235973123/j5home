/****
 * Payment method class
 * @param id
 * @param name
 * @param title
 * @param creditCard
 * @param cardType
 * @param cardCvv
 * @param cardHolderName
 * @return
 */
function PaymentMethod(name, creditCard, cardType, cardCvv, cardHolderName) {	
	this.name = name ;	
	this.creditCard = creditCard ;
	this.cardType = cardType ;
	this.cardCvv = cardCvv ;
	this.cardHolderName = cardHolderName ;
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


var stripeResponseHandler = function(status, response) {
    jQuery(function($) {
        var $form = jQuery('#ftForm1');
        if (response.error) {
            // Show the errors on the form
            //$form.find('.payment-errors').text(response.error.message);
            alert(response.error.message);
            $form.find('#btn-submit').prop('disabled', false);
        } else {
            // token contains id, last4, and card type
            var token = response.id;
            // Empty card data since we now have token
            jQuery('input[name^=x_card_num]').val('');
            jQuery('input[name^=x_card_code]').val('');
            jQuery('input[name^=card_holder_name]').val('');
            // Insert the token into the form so it gets submitted to the server
            $form.append(jQuery('<input type="hidden" name="stripeToken" />').val(token));
            // and re-submit
            $form.get(0).submit();
        }
    });
};


/***
 * Process event when someone change a payment method
 */ 
function changePaymentMethod() {			
	var form = document.ftForm1;		
	var paymentMethod;
	for (var i = 0; i < form.payment_method.length; i++) {
		if (form.payment_method[i].checked == true) {
			paymentMethod = form.payment_method[i].value ;
			break;
		}
	}	
	var trCardNumber = document.getElementById('tr_card_number');
	var trExpDate = document.getElementById('tr_exp_date');
	var trCvvCode = document.getElementById('tr_cvv_code');
	var trCardType = document.getElementById('tr_card_type') ;
	var trCardHolderName = document.getElementById('tr_card_holder_name');
	var trCardHead = document.getElementById('tr_card_head');
	//var trBankList = document.getElementById('tr_bank_list');
	method = methods.Find(paymentMethod);
	if (method.getCreditCard()) {
		trCardHead.style.display = "";
		trCardNumber.style.display = "";				
		trExpDate.style.display = "";				
		trCvvCode.style.display = "";
		if (method.getCardType()) {
			trCardType.style.display = '';
		} else {
			trCardType.style.display = 'none';
		}
		if (method.getCardHolderName()) {
			trCardHolderName.style.display = '';
		} else {
			trCardHolderName.style.display = 'none';
		}
	} else {
		trCardHead.style.display = "none";
		trCardNumber.style.display = "none";				
		trExpDate.style.display = "none";				
		trCvvCode.style.display = "none";
		trCardType.style.display = 'none';
		trCardHolderName.style.display = "none";		
	}
	
	
	if (typeof stripe !== 'undefined')
    {
        if (paymentMethod.indexOf('os_stripe') == 0)
        {
            jQuery('#stripe-card-form').show();
        }
        else
        {
            jQuery('#stripe-card-form').hide();
        }
    }
}				 
 
 function updateStateList() {
	var form = document.appform ;
	//First of all, we need to empty the state dropdown
	var list = form.state ;

	// empty the list
	for (i = 1 ; i < list.options.length ; i++) {
		list.options[i] = null;
	}
	list.length = 1 ;
	var i = 0;
	//Get the country index
	var country = form.country.value ;			
	if (country != '') {
		//Find index of the country
		for (var i = 0 ; i < countryNames.length ; i++) {
			if (countryNames[i] == country) {						
				break ;
			}
		}
		//We will find the states
		var countryId = countryIds[i] ;				
		var stateNames = stateList[countryId]; ;
		if (stateNames) {
			var arrStates = stateNames.split(',');
			i = 1 ;
			var state = '';
			var stateName = '' ;
			for (var j = 0 ; j < arrStates.length ; j++) {
				state = arrStates[j] ;
				stateName = state.split(':');
				opt = new Option();
				opt.value = stateName[0];
				opt.text = stateName[1];
				list.options[i++] = opt;
			}
			list.lenght = i ;
		}								
	}					
} 