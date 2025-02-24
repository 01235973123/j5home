<?php
/*------------------------------------------------------------------------
# activelistingform.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
?>
<script src="<?php echo Uri::root()?>media/com_osproperty/assets/js/paymentmethods.js" type="text/javascript"></script>
<h1 class="componentheading">

	<?php
    switch ($type){
        case "1":
            echo Text::_('OS_ACTIVE_LISTING');
            break;
        case "2":
            echo Text::_('OS_UPGRADE_PROPERTIES_TO_FEATURE');
            break;
        case "3":
            echo Text::_('OS_EXTEND_LISTING_SUBSCRIPTION');
            break;
    }
    echo " [".OSPHelper::getLanguageFieldValue($row,'pro_name')."]";
    ?>
</h1>
<?php
$stripePaymentMethod = null;
?>
<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=property_paymentprocess&Itemid='.$itemid); ?>" name="ftForm1" id="ftForm1">
    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="addpropertypanel6">
        <div class="col width-100 nomargin nopadding <?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
            <?php
            if($configClass['general_use_expiration_management'] == 1 && $row->expiration->id > 0){
                $expiration = $row->expiration;
                ?>
                <table class="addproperty-membership-table" width="100%">
                    <tr>
                        <th width="50%" class="paddingleft15">
                            <?php echo Text::_('OS_EXPIRATION_INFORMATION');?>
                        </th>
                        <th width="50%" class="paddingleft15">
                            <?php echo Text::_('OS_PROPERTY_TYPE');?>
                        </th>
                    </tr>
                    <?php
                    if($row->isFeatured == 1){
                        ?>
                        <tr>
                            <td>
                                <?php echo Text::_('OS_FEATURED_UNTIL');?>
                            </td>
                            <td>
                                <?php
                                $expiration_featured = strtotime($expiration->expired_feature_time);
                                if($expiration_featured < time()){
                                    $style = "extend_alert";
                                }else{
                                    $style = "";
                                }
                                echo "<span class='$style'>".date($configClass['general_date_format'],$expiration_featured)."</a>";
                                ?>
                            </td>
                        </tr>
                    <?php }
                    if($expiration->expired_time != "0000-00-00 00:00:00"){
                        ?>
                        <tr>
                            <td>
                                <?php echo Text::_('OS_UNPUBLISHED_ON');?>
                            </td>
                            <td>
                                <?php
                                $expiration_time = strtotime($expiration->expired_time);
                                if($expiration_time < time()){
                                    $style = "extend_alert";
                                }else{
                                    $style = "";
                                }
                                echo "<span class='$style'>".date($configClass['general_date_format'],$expiration_time)."</a>";
                                ?>
                            </td>
                        </tr>
                    <?php }
                    if($expiration->remove_from_database != "0000-00-00 00:00:00"){
                        ?>
                        <tr>
                            <td>
                                <?php echo Text::_('OS_REMOVED_ON');?>
                            </td>
                            <td>
                                <?php
                                $expiration_remove = strtotime($expiration->remove_from_database);
                                echo "<span>".date($configClass['general_date_format'],$expiration_remove)."</a>";
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
            <BR />
            <table class="addproperty-membership-table" width="100%">
                <tr>
                    <th width="15%">
                        #
                    </th>
                    <th width="85%">
                        <?php echo Text::_('OS_PROPERTY_TYPE');?>
                    </th>
                </tr>
                <tr>
                    <td>
                        <input type="radio" name="isFeatured" id="isFeatured1" value="0" onClick="javascript:showPaymentForm(0);" checked /> &nbsp;<label for="isFeatured1"><?php echo Text::_('OS_STANDARD');?></label>
                    </td>
                    <td>
                        <span><?php echo Text::_('OS_STANDARD_EXPLANATION'); ?></span>
                        <div class="clearfix"></div>
                        <table class="addproperty-membership-subtable">
                            <tr>
                                <?php
                                if($configClass['general_use_expiration_management'] == 1){ ?>
                                    <th width="50%">
                                        <?php echo Text::_('OS_LIFE_TIME');?>
                                    </th>
                                <?php } ?>
                                <th width="50%">
                                    <?php echo Text::_('OS_PRICE');?>
                                </th>
                            </tr>
                            <tr>
                                <?php
                                if($configClass['general_use_expiration_management'] == 1){ ?>
                                    <td>
                                        <?php
                                        echo $configClass['general_time_in_days'];
                                        echo " ".Text::_('OS_DAYS');
                                        ?>
                                    </td>
                                <?php } ?>
                                <td width="50%">
                                    <?php
                                    if($configClass['normal_cost'] == "0"){
                                        echo Text::_('OS_FREE');
                                    }else{
                                        echo OSPHelper::generatePrice(HelperOspropertyCommon::loadCurrency($configClass['general_currency_default']),$configClass['normal_cost']);
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="radio" name="isFeatured" id="isFeatured2" onClick="javascript:showPaymentForm(1);" value="1" /> &nbsp;<label for="isFeatured2"><?php echo Text::_('OS_FEATURED');?></label>
                    </td>
                    <td>
                        <span><?php echo Text::_('OS_FEATURED_EXPLANATION'); ?></span>
                        <div class="clearfix"></div>
                        <table class="addproperty-membership-subtable">
                            <tr>
                                <?php
                                if($configClass['general_use_expiration_management'] == 1){ ?>
                                    <th width="50%">
                                        <?php echo Text::_('OS_LIFE_TIME');?>
                                    </th>
                                <?php } ?>
                                <th width="50%">
                                    <?php echo Text::_('OS_PRICE');?>
                                </th>
                            </tr>
                            <tr>
                                <?php
                                if($configClass['general_use_expiration_management'] == 1){ ?>
                                    <td>
                                        <?php
                                        echo $configClass['general_time_in_days_featured'];
                                        echo " ".Text::_('OS_DAYS');
                                        ?>
                                    </td>
                                <?php } ?>
                                <td width="50%">
                                    <?php
                                    if($configClass['general_featured_upgrade_amount'] == "0"){
                                        echo Text::_('OS_FREE');
                                    }else{
                                        echo OSPHelper::generatePrice(HelperOspropertyCommon::loadCurrency($configClass['general_currency_default']),$configClass['general_featured_upgrade_amount']);
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php
            if(floatVal($configClass['normal_cost']) > 0){
                $display = "block";
            }else{
                $display = "none";
            }
            ?>
            <div id="payment_list" style="display:<?php echo $display;?>;">
                <?php
                $methods = $lists['methods'];
                if(count($methods) > 0){
                    ?>
                    <input type="hidden" name="nmethods" id="nmethods" value="<?php echo count($methods)?>" />
                    <table class="addproperty-membership-payments-table" width="100%">
                        <tr>
                            <th width="5%">
                                #
                            </th>
                            <th width="20%">
                                <?php echo Text::_('OS_PAYMENT_NAME');?>
                            </th>
                            <th width="55%">
                                <?php echo Text::_('OS_PAYMENT_DESC');?>
                            </th>
                            <th width="20%">
                            </th>
                        </tr>
                        <?php
                        $method = null ;
                        for ($i = 0 , $n = count($methods); $i < $n; $i++) 
						{
                            $paymentMethod = $methods[$i];
                            if ($paymentMethod->getName() == $lists['paymentMethod']) 
							{
                                $checked	= ' checked="checked" ';
                                $method		= $paymentMethod ;
                            }
                            else
							{
                                $checked = '';
							}

							if (strpos($paymentMethod->getName(), 'os_stripe') !== false)
							{
								$stripePaymentMethod = $paymentMethod;
							}
                            ?>
                            <tr>
                                <td class="center">
                                    <input onclick="javascript:changePaymentMethod();" type="radio" name="payment_method" id="pmt<?php echo $i?>" value="<?php echo $paymentMethod->getName(); ?>" <?php echo $checked; ?> />
                                </td>
                                <td>
                                    <label for="pmt<?php echo $i?>"><?php echo Text::_($paymentMethod->getTitle()) ; ?></label>
                                </td>
                                <td>
                                    <?php echo $paymentMethod->getDescription() ; ?>
                                </td>
                                <td class=" center">
                                    <?php
                                    if(file_exists(JPATH_ROOT.'/images/osproperty/plugins/'.$paymentMethod->getName().'.png')){
                                        ?>
                                        <img src="<?php echo Uri::root().'images/osproperty/plugins/'.$paymentMethod->getName().'.png'?>"  width="110" />
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>

                            <?php
                        }
                        ?>
                    </table>
                    <?php
                } 
				else 
				{
                    $method = $methods[0] ;
					if (strpos($method->getName(), 'os_stripe') !== false)
					{
						$stripePaymentMethod = $method;
					}
                }

                if ($method->getCreditCard()) {
                    $style = '' ;
                } else {
                    $style = 'style = "display:none"';
                }
                ?>
                <table class="addproperty-membership-credit-table">
                    <tr id="tr_card_head">
                        <th colspan=2>
                            <?php echo Text::_('OS_CREDIT_CARD_INFORMATION'); ?>
                        </th>
                    </tr>
                    <?php
                    if ($method->getName() == 'os_squareup')
                    {
                        $style = '';
                    }
                    else
                    {
                        $style = 'style = "display:none"';
                    }
                    ?>
                    <tr id="sq_field_zipcode" <?php echo $style; ?>>
                        <td class="infor_left_col"><?php echo  Text::_('OS_SQUAREUP_ZIPCODE'); ?><span class="required">*</span></td>
                        <td class="infor_right_col">
                            <div id="field_zip_input">
                                <input type="text" id="sq_billing_zipcode" name="sq_billing_zipcode" class="input-medium" value="" />
                            </div>
                        </td>
                    </tr>
                    <tr id="tr_card_number" <?php echo $style; ?>>
                        <td class="infor_left_col"><?php echo  Text::_('OS_AUTH_CARD_NUMBER'); ?><span class="required">*</span></td>
                        <td class="infor_right_col">
                            <div id="sq-card-number">
                                <input type="text" name="x_card_num" id="x_card_num" class="input-medium" onkeyup="checkNumber(this,'<?php echo Text::_('OS_ONLY_NUMBER'); ?>')" value="<?php echo $x_card_num; ?>" size="20" />
                            </div>
                        </td>
                    </tr>
                    <tr id="tr_exp_date" <?php echo $style; ?>>
                        <td class="infor_left_col">
                            <?php echo Text::_('OS_AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
                        </td>
                        <td class="infor_right_col">
                            <div id="sq-expiration-date">
                                <?php echo $lists['exp_month'] .'  /  '.$lists['exp_year'] ; ?>
                            </div>
                        </td>
                    </tr>
                    <tr id="tr_cvv_code" <?php echo $style; ?>>
                        <td class="infor_left_col">
                            <?php echo Text::_('OS_AUTH_CVV_CODE'); ?><span class="required">*</span>
                        </td>
                        <td class="infor_right_col">
                            <div id="sq-cvv">
                                <input type="text" name="x_card_code" id="x_card_code" class="input-medium" onKeyUp="checkNumber(this,'<?php echo Text::_('OS_ONLY_NUMBER'); ?>')" value="<?php echo $x_card_code; ?>" size="20" />
                            </div>
                        </td>
                    </tr>
                    <?php
                    if ($method->getCardType()) {
                        $style = '' ;
                    } else {
                        $style = ' style = "display:none;" ' ;
                    }
                    ?>
                    <tr id="tr_card_type" <?php echo $style; ?>>
                        <td class="infor_left_col">
                            <?php echo Text::_('OS_CARD_TYPE'); ?><span class="required">*</span>
                        </td>
                        <td class="infor_right_col">
                            <?php echo $lists['card_type'] ; ?>
                        </td>
                    </tr>
                    <?php
                    if ($method->getCardHolderName()) {
                        $style = '' ;
                    } else {
                        $style = ' style = "display:none;" ' ;
                    }
                    ?>
                    <tr id="tr_card_holder_name" <?php echo $style; ?>>
                        <td class="infor_left_col">
                            <?php echo Text::_('OS_CARD_HOLDER_NAME'); ?><span class="required">*</span>
                        </td>
                        <td class="infor_right_col">
                            <input type="text" name="card_holder_name" id="card_holder_name" class="input-medium"  value="<?php echo $cardHolderName; ?>" size="40" />
                        </td>
                    </tr>
					<?php
					if ($stripePaymentMethod !== null && method_exists($stripePaymentMethod, 'getParams'))
					{
						/* @var os_stripe $stripePaymentMethod */
						$params = $stripePaymentMethod->getParams();
						$useStripeCardElement = true;

						if ($useStripeCardElement)
						{
							if ($method->getName() === 'os_stripe')
							{
								$style = '';
							}
							else
							{
								$style = ' style = "display:none;" ';
							}
							?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> payment_information" <?php echo $style; ?> id="stripe-card-form">
								<div class="<?php echo $bootstrapHelper->getClassMapping('span3')?>" for="stripe-card-element">
									<label class="boldtext">
										<?php echo Text::_('OS_CREDIT_OR_DEBIT_CARD'); ?><span class="required">*</span>
									</label>
								</div>
								<div class="<?php echo $bootstrapHelper->getClassMapping('span8');?>" id="stripe-card-element">

								</div>
							</div>
							<?php
						}
					}
					?>
                </table>
            </div>
        </div>
    </div>
    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="addpropertypanel6">
        <div class="col width-100 nomargin nopadding <?php echo $bootstrapHelper->getClassMapping('span12'); ?> center padding10">
            <a href="javascript:confirmUpgradeForm()" id="btn-submit" class="btn btn-primary" title="<?php echo Text::_('OS_PROCESS_PAYMENT');?>"><?php echo Text::_('OS_PROCESS_PAYMENT');?></a>
            <a href="javascript:history.go(-1)" class="btn btn-warning" title="<?php echo Text::_('OS_CANCEL');?>"><?php echo Text::_('OS_CANCEL');?></a>
        </div>
    </div>
	<input type="hidden" name="option" value="com_osproperty" />
	<input type="hidden" name="task" value="property_paymentprocess" />
	<input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />
	<input type="hidden" name="remove_value" id="remove_value" value="" />
	<input type="hidden" name="live_site" value="<?php echo Uri::root()?>" />
    <input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
    <input type="hidden" name="type" id="type" value="<?php echo $type;?>" />
    <input type="hidden" id="card-nonce" name="nonce" />
</form>
<script type="text/javascript">
jQuery(document).ready(function(){
	if (typeof stripe !== 'undefined')
	{

		var style = {
			//hidePostalCode: true,
			base: {
				// Add your base input styles here. For example:
				fontSize: '16px',
				color: "#32325d",
			}
		};
		// Create an instance of the card Element.
		//card = elements.create('card', {hidePostalCode: true, style: style});
		card = elements.create('card', {style: style});
		// Add an instance of the card Element into the `card-element` <div>.
		card.mount('#stripe-card-element');
	}
});
<?php
	os_payments::writeJavascriptObjects();
?>
function showPaymentForm(value)
{
    if(value == 0)
    {
        <?php
        if(floatVal($configClass['normal_cost']) > 0)
        {
        ?>
            document.getElementById('payment_list').style.display = "block";
        <?php } else { ?>
            document.getElementById('payment_list').style.display = "none";
        <?php
        } ?>
    }
    else if(value == 1)
    {
        <?php
        if(floatVal($configClass['general_featured_upgrade_amount']) > 0)
        {
        ?>
            document.getElementById('payment_list').style.display = "block";
        <?php } else { ?>
        document.getElementById('payment_list').style.display = "none";
        <?php
        } ?>
    }
}
function confirmUpgradeForm()
{
	var isFeatured1 = document.getElementById('isFeatured1');
	var isFeatured1 = isFeatured1.value;
	var isFeatured2 = document.getElementById('isFeatured2');
	var isFeatured2 = isFeatured2.value;
	var cansubmit;
	<?php
	if($configClass['active_payment'] == 1)
	{
		?>
		if(isFeatured2 == 1)
		{
			<?php
			if(floatVal($configClass['general_featured_upgrade_amount'] > 0))
			{
			?>
				var methodpass = 1;
				var paymentMethod 	= "";
				var x_card_num = "";
				var x_card_code = "";
				var card_holder_name = "";
				var exp_month = "";
				var exp_year = "";
				cansubmit = checkPaymentMethod();
			<?php
			}
			else
			{
			?>
				cansubmit  = 1;
			<?php
			}
			?>
		}
		else if(isFeatured1 == 1)
		{
			<?php
			if(floatVal($configClass['normal_cost'] > 0))
			{
				?>
				var methodpass = 1;
				var paymentMethod 	= "";
				var x_card_num = "";
				var x_card_code = "";
				var card_holder_name = "";
				var exp_month = "";
				var exp_year = "";
				cansubmit = checkPaymentMethod();
				<?php
			}
			else
			{
				?>
				cansubmit  = 1;
				<?php
			}
			?>
		}
		<?php
	}
	else
	{
		?>
		cansubmit  = 1;
		<?php
	}
	?>
	if(cansubmit == 1)
	{
		document.ftForm1.submit();
	}
}

function checkPaymentMethod()
{
	var methodpass = 1;
	var paymentMethod 	= "";
	var x_card_num = "";
	var x_card_code = "";
	var card_holder_name = "";
	var exp_month = "";
	var exp_year = "";
	var check = 1;
	<?php
	$methods = $lists['methods'];
	if (count($methods) > 0) {
		if (count($methods) > 1) {
		?>
			var paymentValid = false;
			var nmethods = document.getElementById('nmethods');
			var methodtemp;
			for (var i = 0 ; i < nmethods.value; i++) {
				methodtemp = document.getElementById('pmt' + i);
				if(methodtemp.checked == true){
					paymentValid = true;
					paymentMethod = methodtemp.value;
					break;
				}
			}
			
			if (!paymentValid) {
				alert("<?php echo Text::_('OS_REQUIRE_PAYMENT_OPTION'); ?>");
				methodpass = 0;
			}		
		<?php	
		} else {
		?>
			paymentMethod = "<?php echo $methods[0]->getName(); ?>";
		<?php	
		}				
		?>
        if (paymentMethod != 'os_squareup') {
            method = methods.Find(paymentMethod);
            if ((method.getCreditCard()) && (check == 1))
            {
                var x_card_nume = document.getElementById('x_card_num');
                if (x_card_nume.value == "") {
                    alert("<?php echo Text::_('OS_ENTER_CARD_NUMBER'); ?>");
                    x_card_nume.focus();
                    methodpass = 0;
                    return 0;
                } else {
                    x_card_num = x_card_nume.value;
                }

                var x_card_codee = document.getElementById('x_card_code');
                if (x_card_codee.value == "") {
                    alert("<?php echo Text::_('OS_ENTER_CARD_CODE'); ?>");
                    x_card_codee.focus();
                    methodpass = 0;
                    return 0;
                } else {
                    x_card_code = x_card_codee.value;
                }

				var exp_yeare = document.getElementById('exp_year');
				exp_year = exp_yeare.value;
				var exp_monthe = document.getElementById('exp_month');
				exp_month = exp_monthe.value;
            }
        }

        if(paymentMethod == "os_stripe")
		{
			if (typeof stripe !== 'undefined' && paymentMethod.indexOf('os_stripe') == 0 && jQuery('#stripe-card-form').is(":visible"))
			{
				stripe.createToken(card).then(function(result) {
					if (result.error) {
						// Inform the customer that there was an error.
						//var errorElement = document.getElementById('card-errors');
						//errorElement.textContent = result.error.message;
						alert(result.error.message);
						form.find('#btn-submit').prop('disabled', false);
					}
					else
					{
						// Send the token to your server.
						stripeTokenHandler(result.token);
					}
				});

				return false;
			}
			else if (typeof stripePublicKey !== 'undefined' && paymentMethod.indexOf('os_stripe') == 0 && $('#tr_card_number').is(':visible'))
			{
				Stripe.card.createToken({
					number: jQuery('#x_card_num').val(),
					cvc: jQuery('#x_card_code').val(),
					exp_month: jQuery('#exp_month').val(),
					exp_year: jQuery('#exp_year').val(),
					name: jQuery('#card_holder_name').val()
				}, stripeResponseHandler);
			}
		}

        if (paymentMethod == 'os_squareup' && jQuery('#tr_card_number').is(':visible'))
        {
            sqPaymentForm.requestCardNonce();

            return false;
        }
		return 1;
		<?php
	}
	?>
}

function stripeTokenHandler(token) {
	// Insert the token ID into the form so it gets submitted to the server
	var $form = jQuery('#ftForm1');
	// Insert the token into the form so it gets submitted to the server
	if (token.error)
	{
		// Show the errors on the form
		alert(response.error.message);
	}
	else
	{
		$form.append(jQuery('<input type="hidden" name="stripeToken" />').val(token.id));
		$form.submit();
	}
}

function responseHandler(result) 
{
	var $form = jQuery('#ftForm1');
	var returnMsg = "";
	if (result['code'] == 0) 
	{
		// Request was successful. Insert hidden field into the form before submitting.
		// Continue to submit the form to the action, where we will read the decode and extract POST data.
		$form.append(jQuery('<input type="hidden" name="TransactionToken" />').val(result.text));
		$form.submit();
	}
	else 
	{
		for (var i in result) 
		{
			returnMsg += result[i];
		}
		alert(returnMsg);
		return false;
	}
}

function stripeResponseHandler(status, response) 
{
	var $form = jQuery('#ftForm1');
	if (response.error) 
	{
		// Show the errors on the form
		alert(response.error.message);
	} 
	else 
	{
		// token contains id, last4, and card type
		var token = response.id;
		// Insert the token into the form so it gets submitted to the server
		$form.append(jQuery('<input type="text" name="stripeToken" />').val(token));
		//$form.submit();
	}
};
</script>