<?php 
/*------------------------------------------------------------------------
# property_listing.php - Ossolution Property
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
	<legend><?php echo TextOs::_('Frontend property listing by Agents')?></legend>
	<table cellpadding="0" cellspacing="0" width="100%" class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Agent registered' );?>::<?php echo TextOs::_('Would you like to allow the registered members can register to become agent members.'); ?>">
                      <label for="checkbox_general_agent_registered">
                          <?php echo TextOs::_( 'Agent registered' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_general_agent_registered = '';
					if (isset($configs['allow_agent_registration']) && $configs['allow_agent_registration'] == 1){
						$checkbox_allow_agent_registration = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_allow_agent_registration" value="" <?php echo $checkbox_allow_agent_registration;?> onclick="if(this.checked) adminForm['configuration[allow_agent_registration]'].value = 1;else adminForm['configuration[allow_agent_registration]'].value = 0;">
				<input type="hidden" name="configuration[allow_agent_registration]" value="<?php echo isset($configs['allow_agent_registration'])?$configs['allow_agent_registration']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Auto approval agent register request' );?>::<?php echo TextOs::_('Would you like to allow auto approval the agent register request.'); ?>">
                      <label for="checkbox_auto_approval_agent_registration">
                          <?php echo TextOs::_( 'Auto approval agent register request' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_auto_approval_agent_registration = '';
					if (isset($configs['auto_approval_agent_registration']) && $configs['auto_approval_agent_registration'] == 1){
						$checkbox_auto_approval_agent_registration = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_auto_approval_agent_registration" value="" <?php echo $checkbox_auto_approval_agent_registration;?> onclick="if(this.checked) adminForm['configuration[auto_approval_agent_registration]'].value = 1;else adminForm['configuration[auto_approval_agent_registration]'].value = 0;">
				<input type="hidden" name="configuration[auto_approval_agent_registration]" value="<?php echo isset($configs['auto_approval_agent_registration'])?$configs['auto_approval_agent_registration']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Agent listings' );?>::<?php echo TextOs::_('Would you like to allow agent members to list properties for sale via the front-end listings panel?'); ?>">
                     <label for="checkbox_general_agent_listings">
                         <?php echo TextOs::_( 'Agent listings' ).':'; ?>
                     </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_general_agent_listings = '';
					if (isset($configs['general_agent_listings']) && $configs['general_agent_listings'] == 1){
						$checkbox_general_agent_listings = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_agent_listings" value="" <?php echo $checkbox_general_agent_listings;?> onclick="if(this.checked) adminForm['configuration[general_agent_listings]'].value = 1;else adminForm['configuration[general_agent_listings]'].value = 0;">
				<input type="hidden" name="configuration[general_agent_listings]" value="<?php echo isset($configs['general_agent_listings'])?$configs['general_agent_listings']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Auto approval' );?>::<?php echo TextOs::_('Do new and updated listings require admin approval before publishing?'); ?>">
                     <label for="checkbox_general_approval">
                         <?php echo TextOs::_( 'Auto approval' ).':'; ?>
                     </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_general_approval = '';
					if (isset($configs['general_approval']) && $configs['general_approval'] == 1){
						$checkbox_general_approval = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_approval" value="" <?php echo $checkbox_general_approval;?> onclick="if(this.checked) adminForm['configuration[general_approval]'].value = 1;else adminForm['configuration[general_approval]'].value = 0;">
				<input type="hidden" name="configuration[general_approval]" value="<?php echo isset($configs['general_approval'])?$configs['general_approval']:'0' ?>">
			</td>
		</tr>
		<!--
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Free listing images' );?>::<?php echo TextOs::_('If you have free and paid listings for your members, do you want free listings to be able to show an image? If you select \'NO\' just a placeholder will be displayed, which may encourage more paid listings. If you select \'YES\' - free listings will only be able to have 4 images. This will not affect listings if you\'re ONLY running in free listings mode - members can have 24 images when run in this mode.'); ?>">
                      <label for="checkbox_free_listing_images">
                          <?php echo TextOs::_( 'Free listing images' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_free_listing_images = '';
					if (isset($configs['general_approval']) && $configs['general_approval'] == 1){
						$checkbox_free_listing_images = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_free_listing_images" value="" <?php echo $checkbox_general_approval;?> onclick="if(this.checked) adminForm['configuration[general_free_listing_images]'].value = 1;else adminForm['configuration[general_free_listing_images]'].value = 0;">
				<input type="hidden" name="configuration[general_free_listing_images]" value="<?php echo isset($configs['general_free_listing_images'])?$configs['general_free_listing_images']:'0' ?>">
			</td>
		</tr>
		-->
	</table>
</fieldset>