<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');

Factory::getApplication()->getDocument()->addScript(Uri::root(true) . '/media/com_eventbooking/js/admin-coupon-default.min.js')
	->getWebAssetManager()
	->useScript('showon');

EventbookingHelper::normalizeNullDateTimeData($this->item, ['valid_from', 'valid_to']);

$languageKeys = [
	'EB_ENTER_COUPON',
	'EN_ENTER_DISCOUNT_AMOUNT',
];

EventbookingHelperHtml::addJSStrings($languageKeys);
?>
<form action="<?php echo $this->getFormAction(); ?>" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
	<?php
	if (!empty($this->registrants))
	{
		echo HTMLHelper::_( 'uitab.startTabSet', 'coupon', ['active' => 'coupon-page', 'recall' => true]);
		echo HTMLHelper::_( 'uitab.addTab', 'coupon', 'coupon-page', Text::_('EB_BASIC_INFORMATION'));
	}
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_CODE'); ?>
		</div>
		<div class="controls">
			<input class="form-control" type="text" name="code" id="code" size="15" maxlength="250"
				   value="<?php echo $this->item->code; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_DISCOUNT'); ?>
		</div>
		<div class="controls">
			<input class="input-medium form-control d-inline-block" type="number" name="discount" id="discount" size="10" maxlength="250"
				   value="<?php echo $this->item->discount; ?>"/>&nbsp;&nbsp;<?php echo $this->lists['coupon_type']; ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_CATEGORIES'); ?>
		</div>
		<div class="controls">
			<?php echo EventbookingHelperHtml::getChoicesJsSelect($this->lists['category_id']); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_COUPON_ASSIGNMENT'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['assignment'] ; ?>
		</div>
	</div>
	<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowon(['assignment' => ['1', '-1']]); ?>'>
		<div class="control-label">
			<?php echo Text::_('EB_EVENTS'); ?>
		</div>
		<div class="controls">
			<?php echo EventbookingHelperHtml::getChoicesJsSelect($this->lists['event_id']); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_TIMES'); ?>
		</div>
		<div class="controls">
			<input class="input-medium form-control" type="number" name="times" id="times" size="5" maxlength="250"
				   value="<?php echo $this->item->times; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_TIME_USED'); ?>
		</div>
		<div class="controls">
			<input class="input-medium form-control" type="number" name="used" id="used" size="5" maxlength="250"
			       value="<?php echo $this->item->used; ?>"/>
		</div>
	</div>
	<?php
		if ($this->item->coupon_type == 2)
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_USED_AMOUNT'); ?>
				</div>
				<div class="controls">
					<input class="input-medium form-control" type="number" name="used_amount" id="used_amount" size="5" maxlength="250"
				   value="<?php echo $this->item->used_amount; ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_REMAINING_AMOUNT'); ?>
				</div>
				<div class="controls">
					<?php echo round($this->item->discount - $this->item->used_amount, 2); ?>
				</div>
			</div>
		<?php
		}
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_MAX_USAGE_PER_USER'); ?>
		</div>
		<div class="controls">
			<input class="input-medium form-control" type="number" name="max_usage_per_user" id="max_usage_per_user" size="5" maxlength="250"
				   value="<?php echo $this->item->max_usage_per_user; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_VALID_FROM_DATE'); ?>
		</div>
		<div class="controls">
			<?php echo HTMLHelper::_('calendar', $this->item->valid_from, 'valid_from', 'valid_from', $this->datePickerFormat . ' %H:%M', ['showTime' => true]); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_VALID_TO_DATE'); ?>
		</div>
		<div class="controls">
			<?php echo HTMLHelper::_('calendar', $this->item->valid_to, 'valid_to', 'valid_to', $this->datePickerFormat . ' %H:%M', ['showTime' => true]); ?>
		</div>
	</div>
	<?php
		if (!$this->config->multiple_booking)
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_APPLY_TO'); ?>
				</div>
				<div class="controls">
					<?php echo $this->lists['apply_to']; ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_ENABLE_FOR'); ?>
				</div>
				<div class="controls">
					<?php echo $this->lists['enable_for']; ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_COUPON_MIN_NUMBER_REGISTRANTS'); ?>
				</div>
				<div class="controls">
					<input class="input-medium form-control" type="number" name="min_number_registrants" id="min_number_registrants" size="5" maxlength="250"
						   value="<?php echo $this->item->min_number_registrants; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_COUPON_MAX_NUMBER_REGISTRANTS'); ?>
				</div>
				<div class="controls">
					<input class="input-medium form-control" type="number" name="max_number_registrants" id="max_number_registrants" size="5" maxlength="250"
						   value="<?php echo $this->item->max_number_registrants; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_COUPON_MIN_PAYMENT_AMOUNT'); ?>
				</div>
				<div class="controls">
					<input class="input-medium form-control" type="number" name="min_payment_amount" id="min_payment_amount" size="5" maxlength="250"
					       value="<?php echo $this->item->min_payment_amount; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_COUPON_MAX_PAYMENT_AMOUNT'); ?>
				</div>
				<div class="controls">
					<input class="input-medium form-control" type="number" name="max_payment_amount" id="max_payment_amount" size="5" maxlength="250"
					       value="<?php echo $this->item->max_payment_amount; ?>"/>
				</div>
			</div>
		<?php
		}
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('EB_USER'); ?>
		</div>
		<div class="controls">
			<?php // Note, the third parameter of the method is hardcoded to prevent onchange event, do not remove it.?>
			<?php echo EventbookingHelperHtml::getUserInput($this->item->user_id, 'user_id', 100); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_ACCESS'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['access']; ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_NOTE'); ?>
		</div>
		<div class="controls">
			<input class="input-xxlarge form-control" type="text" name="note" id="note" maxlength="250"
				   value="<?php echo $this->item->note; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('EB_PUBLISHED'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['published']; ?>
		</div>
	</div>
	<?php
	if (!empty($this->registrants))
	{
		echo HTMLHelper::_( 'uitab.endTab');
		echo HTMLHelper::_( 'uitab.addTab', 'coupon', 'registrants-page', Text::_('EB_COUPON_USAGE'));
		echo $this->loadTemplate('registrants');
		echo HTMLHelper::_( 'uitab.endTab');
		echo HTMLHelper::_( 'uitab.endTabSet');
	}
	?>
	<div class="clearfix"></div>
	<?php echo HTMLHelper::_('form.token'); ?>
	<input type="hidden" name="id" value="<?php echo (int) $this->item->id; ?>"/>
	<input type="hidden" name="task" value=""/>
	<?php
	if (!$this->item->used)
	{
	?>
		<input type="hidden" name="used" value="0"/>
	<?php
	}
	?>
</form>