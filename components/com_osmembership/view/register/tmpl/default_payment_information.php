<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;

use Joomla\CMS\Language\Text;

/**@var OSMembershipHelperBootstrap $bootstrapHelper **/
$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass = $bootstrapHelper->getClassMapping('input-prepend');
$inputAppendClass  = $bootstrapHelper->getClassMapping('input-append');
$addOnClass        = $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$inputSmallClass   = $bootstrapHelper->getClassMapping('input-small');
$inputMediumClass  = $bootstrapHelper->getClassMapping('input-medium');

if ($this->config->twitter_bootstrap_version == 'uikit3')
{
	$amountInputClass = $inputMediumClass . ' uk-input';
}
else
{
	$amountInputClass = $inputSmallClass . ' form-control';
}

$couponCode = $this->input->getString('coupon_code', '');

if ($this->config->enable_coupon)
{
?>
	<div class="<?php echo $controlGroupClass ?> osm-coupon-container">
		<div class="<?php echo $controlLabelClass; ?>">
			<label><?php echo Text::_('OSM_COUPON'); ?><?php if ($this->plan->require_coupon) echo '<span class="star"> *</span>'; ?></label>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="form-control <?php echo $inputMediumClass . ($this->plan->require_coupon ? ' validate[required]' : ''); ?>" name="coupon_code" id="coupon_code" value="<?php echo $this->escape($couponCode);?>" onchange="calculateSubscriptionFee();" />
			<span class="invalid" id="coupon_validate_msg" style="display: none;"><?php echo Text::_('OSM_INVALID_COUPON'); ?></span>
		</div>
	</div>
<?php
}
if ($this->plan->recurring_subscription)
{
	if ($this->getLayout() == 'default')
	{
		echo $this->loadTemplate('payment_information_recurring');
	}
	else
	{
		echo $this->loadCommonLayout('register/tmpl/default_payment_information_recurring.php');
	}
}
else
{
	if ($this->fees['setup_fee'] > 0)
	{
	?>
		<div class="<?php echo $controlGroupClass ?>" id="osm-setup-fee-container">
			<div class="<?php echo $controlLabelClass; ?>">
				<label><?php echo Text::_('OSM_SETUP_FEE'); ?></label>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				$input = '<input id="setup_fee" type="text" readonly="readonly" class="form-control ' . $amountInputClass . '" value="' . OSMembershipHelper::formatAmount($this->fees['setup_fee'], $this->config) . '" />';

				if ($this->config->currency_position == 0)
				{
					echo $bootstrapHelper->getPrependAddon($input, $this->currencySymbol);
				}
				else
				{
					echo $bootstrapHelper->getAppendAddon($input, $this->currencySymbol);
				}
				?>
			</div>
		</div>
	<?php
	}
?>
	<div class="<?php echo $controlGroupClass ?>" id="osm-amount-container">
		<div class="<?php echo $controlLabelClass; ?>">
			<label><?php echo Text::_('OSM_PRICE'); ?></label>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			$input = '<input id="amount" type="text" readonly="readonly" class="form-control ' . $amountInputClass . '" value="' . OSMembershipHelper::formatAmount($this->fees['amount'], $this->config) . '" />';

			if ($this->config->currency_position == 0)
			{
				echo $bootstrapHelper->getPrependAddon($input, $this->currencySymbol);
			}
			else
			{
				echo $bootstrapHelper->getAppendAddon($input, $this->currencySymbol);
			}
			?>
		</div>
	</div>
	<?php
	if ($this->config->enable_coupon)
	{
	?>
		<div class="<?php echo $controlGroupClass ?>" id="osm-discount-container">
			<div class="<?php echo $controlLabelClass; ?>">
				<label><?php echo Text::_('OSM_DISCOUNT'); ?></label>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				$input = '<input id="discount_amount" type="text" readonly="readonly" class="form-control ' . $amountInputClass . '" value="' . OSMembershipHelper::formatAmount($this->fees['discount_amount'], $this->config) . '" />';

				if ($this->config->currency_position == 0)
				{
					echo $bootstrapHelper->getPrependAddon($input, $this->currencySymbol);
				}
				else
				{
					echo $bootstrapHelper->getAppendAddon($input, $this->currencySymbol);
				}
				?>
			</div>
		</div>
	<?php
	}

	if ($this->taxRate > 0)
	{
		if ($this->fees['tax_amount'] > 0)
		{
			$style = '' ;
		}
		else
		{
			$style = ' style = "display:none;" ' ;
		}
	?>
		<div class="<?php echo $controlGroupClass ?>" id="osm-tax-amount-container"<?php echo $style; ?>>
			<div class="<?php echo $controlLabelClass; ?>">
				<label><?php echo Text::_('OSM_TAX_AMOUNT'); ?></label>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				$input = '<input id="tax_amount" type="text" readonly="readonly" class="form-control ' . $amountInputClass . '" value="' . OSMembershipHelper::formatAmount($this->fees['tax_amount'], $this->config) . '" />';

				if ($this->config->currency_position == 0)
				{
					echo $bootstrapHelper->getPrependAddon($input, $this->currencySymbol);
				}
				else
				{
					echo $bootstrapHelper->getAppendAddon($input, $this->currencySymbol);
				}
				?>
			</div>
		</div>
	<?php
	}

	if ($this->showPaymentFee)
	{
	?>
		<div class="<?php echo $controlGroupClass ?>" id="osm-payment-processing-fee-container">
			<div class="<?php echo $controlLabelClass; ?>">
				<label><?php echo Text::_('OSM_PAYMENT_FEE'); ?></label>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				$input = '<input id="payment_processing_fee" type="text" readonly="readonly" class="form-control ' . $amountInputClass . '" value="' . OSMembershipHelper::formatAmount($this->fees['payment_processing_fee'], $this->config) . '" />';

				if ($this->config->currency_position == 0)
				{
					echo $bootstrapHelper->getPrependAddon($input, $this->currencySymbol);
				}
				else
				{
					echo $bootstrapHelper->getAppendAddon($input, $this->currencySymbol);
				}
				?>
			</div>
		</div>
	<?php
	}

	if ($this->config->enable_coupon || $this->fees['setup_fee'] > 0 || $this->taxRate > 0 || $this->showPaymentFee)
	{
	?>
		<div class="<?php echo $controlGroupClass ?>" id="osm-gross-amount-container">
			<div class="<?php echo $controlLabelClass; ?>">
				<label><?php echo Text::_('OSM_GROSS_AMOUNT'); ?></label>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				$input = '<input id="gross_amount" type="text" readonly="readonly" class="form-control ' . $amountInputClass . '" value="' . OSMembershipHelper::formatAmount($this->fees['gross_amount'], $this->config) . '" />';

				if ($this->config->currency_position == 0)
				{
					echo $bootstrapHelper->getPrependAddon($input, $this->currencySymbol);
				}
				else
				{
					echo $bootstrapHelper->getAppendAddon($input, $this->currencySymbol);
				}
				?>
			</div>
		</div>
	<?php
	}
}
