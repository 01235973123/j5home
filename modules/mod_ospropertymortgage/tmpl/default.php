<?php
use Joomla\CMS\Language\Text;
/*------------------------------------------------------------------------
# default.php - mod_oscategorymortgage
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2010 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$rowFluidClass          = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class            = $bootstrapHelper->getClassMapping('span12');
$controlGroupClass      = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass      = $bootstrapHelper->getClassMapping('control-label');
$controlsClass          = $bootstrapHelper->getClassMapping('controls');
?>

<script type="text/javascript">

<!--

function docalculate()
{
f = document.borrow;
income = Math.floor(f.income.value) *12*4;
yourdebt = income;
f.loan.value = currency(yourdebt);
}

function currency(num)
{
var dollars = Math.floor(num);
for (var i = 0; i < num.length; i++)
{
  if (num.charAt(i) == ".")
break;
}
var cents = "" + Math.round(num * 100);
cents = cents.substring(cents.length-2, cents.length);
return (dollars + "." + cents);
}
//-->

</script>


<div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?> mortgage">
	<div class="<?php echo $rowFluidClass?>">
        <div class="<?php echo $span12Class?>">
			<div><?php echo Text::_( 'OS_MORTGAGE_INTRO' ); ?></div>
			<form method="post" name="borrow" action="">
				<div class="<?php echo $controlsClass; ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('input-prepend');?>">
						<span class="add-on"><?php echo Text::_( 'OS_MORTGAGE_CURRENCY' ); ?></span>
						<input class="input-small form-control" type="text" id="income" name="income" size="15" maxlength="15" placeholder="<?php echo Text::_( 'OS_MORTGAGE_AMOUNT' ); ?>"/>
					</div>
				</div>
				<div class="<?php echo $controlsClass; ?> marginbottom10">
					<input class="btn btn-primary" type="button" onclick="docalculate()" name="<?php echo Text::_( 'OS_MORTGAGE_CALC' ); ?>" value="<?php echo Text::_( 'OS_MORTGAGE_CALC' ); ?>" />
					<input class="btn btn-warning" type="reset" name="<?php echo Text::_( 'OS_MORTGAGE_CLEAR' ); ?>" value="<?php echo Text::_( 'OS_MORTGAGE_CLEAR' ); ?>" />
				</div>
				<div class="<?php echo $controlsClass; ?>">
                    <div class="<?php echo $controlsClass?>">
						<div class="<?php echo $bootstrapHelper->getClassMapping('input-prepend');?>">
                            <span class="add-on"><?php echo Text::_( 'OS_MORTGAGE_CURRENCY' ); ?></span>
							<input class="input-small form-control" type="text" size="15" maxlength="15" id="loan" name="loan" placeholder="<?php echo Text::_( 'OS_MORTGAGE_REPAY' ); ?>"/>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
