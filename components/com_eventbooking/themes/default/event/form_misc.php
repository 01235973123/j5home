<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$bootstrapHelper   = EventbookingHelperBootstrap::getInstance();
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');

$dateFields = [
	'cancel_before_date',
];

foreach ($dateFields as $dateField)
{
	if ((int) $this->item->{$dateField})
	{
		continue;
	}

	$this->item->{$dateField} = '';
}

if ($this->config->get('fes_show_event_password', 0))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo EventbookingHelperHtml::getFieldLabel('event_password', Text::_('EB_EVENT_PASSWORD'), Text::_('EB_EVENT_PASSWORD_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="event_password" id="event_password" class="input-small form-control" value="<?php echo $this->item->event_password; ?>"/>
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_access', 1))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo EventbookingHelperHtml::getFieldLabel('access', Text::_('EB_ACCESS'), Text::_('EB_ACCESS_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['access']; ?>
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_registration_access', 1))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo EventbookingHelperHtml::getFieldLabel('registration_access', Text::_('EB_REGISTRATION_ACCESS'), Text::_('EB_REGISTRATION_ACCESS_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['registration_access']; ?>
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_paypal_email', 1))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_PAYPAL_EMAIL'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="paypal_email" class="form-control" size="50" value="<?php echo $this->item->paypal_email ; ?>" />
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_notification_emails', 1))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_NOTIFICATION_EMAILS'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="notification_emails" class="form-control" size="70" value="<?php echo $this->item->notification_emails ; ?>" />
		</div>
	</div>
<?php
}

if ($this->config->activate_deposit_feature)
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_DEPOSIT_AMOUNT'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="number" name="deposit_amount" id="deposit_amount" class="input-medium form-control d-inline-block" size="5" value="<?php echo $this->item->deposit_amount; ?>"/>&nbsp;&nbsp;<?php echo $this->lists['deposit_type']; ?>
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_free_event_registration_status'))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_FREE_EVENT_REGISTRATION_STATUS'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['free_event_registration_status']; ?>
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_members_discount_apply_for'))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_MEMBERS_DISCOUNT_APPLY_FOR'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['members_discount_apply_for']; ?>
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_activate_waiting_list'))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_ENABLE_WAITING_LIST'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['activate_waiting_list']; ?>
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_collect_member_information'))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_COLLECT_MEMBER_INFORMATION'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['collect_member_information']; ?>
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_prevent_duplicate_registration'))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_PREVENT_DUPLICATE'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['prevent_duplicate_registration']; ?>
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_send_emails'))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_SEND_NOTIFICATION_EMAILS'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['send_emails']; ?>
		</div>
	</div>
<?php
}
?>
<div class="<?php echo $controlGroupClass; ?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo Text::_('EB_ENABLE_CANCEL'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<?php
			if (isset($this->lists['enable_cancel_registration']))
			{
				echo $this->lists['enable_cancel_registration'];
			}
			else
			{
				echo EventbookingHelperHtml::getBooleanInput('enable_cancel_registration', $this->item->enable_cancel_registration);
			}
		?>
	</div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo Text::_('EB_CANCEL_BEFORE_DATE'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<?php echo HTMLHelper::_('calendar', $this->item->cancel_before_date, 'cancel_before_date', 'cancel_before_date', $this->datePickerFormat . ' %H:%M:%S', ['class' => 'input-medium']); ?>
	</div>
</div>
<?php
if ($this->config->get('fes_show_publish_up', 0))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_PUBLISH_UP'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo HTMLHelper::_('calendar', $this->item->publish_up, 'publish_up', 'publish_up', $this->datePickerFormat . ' %H:%M:%S', ['class' => 'input-medium']); ?>
		</div>
	</div>
<?php
}

if ($this->config->get('fes_show_publish_down', 0))
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_PUBLISH_DOWN'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo HTMLHelper::_('calendar', $this->item->publish_down, 'publish_down', 'publish_down', $this->datePickerFormat . ' %H:%M:%S', ['class' => 'input-medium']); ?>
		</div>
	</div>
<?php
}

if ($this->config->term_condition_by_event)
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('EB_TERMS_CONDITIONS'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['article_id'] ; ?>
		</div>
	</div>
<?php
}
?>

<div class="<?php echo $controlGroupClass; ?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo  Text::_('EB_META_KEYWORDS'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<textarea rows="5" cols="30" class="input-xlarge form-control" name="meta_keywords"><?php echo $this->item->meta_keywords; ?></textarea>
	</div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo  Text::_('EB_META_DESCRIPTION'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<textarea rows="5" cols="30" class="input-xlarge form-control" name="meta_description"><?php echo $this->item->meta_description; ?></textarea>
	</div>
</div>