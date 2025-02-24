<?php 
/*------------------------------------------------------------------------
# expiration.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

?>
<fieldset>
	<legend><?php echo TextOs::_('Expiration notification')?></legend>
	<table cellpadding="0" cellspacing="0" width="100%" class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Send expiration notification email' );?>::<?php echo TextOs::_('Do you want to send the expiration notification email'); ?>">
                      <label for="checkbox_general_use_expiration_management">
                          <?php echo TextOs::_( 'Send expiration notification email' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_send_approximates = '';
					if (isset($configs['send_approximates']) && $configs['send_approximates'] == 1){
						$checkbox_send_approximates = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_send_approximates" value="" <?php echo $checkbox_send_approximates;?> onclick="if(this.checked) adminForm['configuration[send_approximates]'].value = 1;else adminForm['configuration[send_approximates]'].value = 0;">
				<input type="hidden" name="configuration[send_approximates]" value="<?php echo isset($configs['send_approximates'])?$configs['send_approximates']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Number days notification email is sent' );?>::<?php echo TextOs::_('Number days before expired day, the notification will be sent'); ?>">
                     <label for="configurationgeneral_time_in_days">
                         <?php echo TextOs::_( 'Number days notification email is sent' ).':'; ?>
                     </label>
				</span>
			</td>
			<td>
				<input type="text" name="configuration[approximates_days]" id="configurationapproximates_days" value="<?php echo isset($configs['approximates_days'])?$configs['approximates_days']:'' ?>" class="text-area-order input-mini" class="text-area-order input-mini" size="5" maxlength="5" >
			</td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend><?php echo TextOs::_('Email configuration')?></legend>
	<table cellpadding="0" cellspacing="0" width="100%" class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Number emails will be sent in one hour' );?>::<?php echo TextOs::_('Depend on your config, please enter number emails will be sent in one day'); ?>">
                     <label for="configurationgeneral_time_in_days">
                         <?php echo TextOs::_( 'Number emails will be sent in one hour' ).':'; ?>
                     </label>
				</span>
			</td>
			<td>
				<input type="text" name="configuration[number_email_by_hour]" id="configurationnumber_email_by_hour" value="<?php echo isset($configs['number_email_by_hour'])?$configs['number_email_by_hour']:'' ?>" class="text-area-order input-mini" size="5" maxlength="5">
			</td>
		</tr>
	</table>
</fieldset>