<?php 
/*------------------------------------------------------------------------
# requirefields.php - Ossolution Property
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
<!--
<fieldset>
	<legend><?php echo TextOs::_('Require fields')?></legend>
	<table cellpadding="0" cellspacing="0" width="100%" class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Postcode' );?>::<?php echo TextOs::_('POSTCODE_EXPLAIN'); ?>">
                      <label for="checkbox_require_postcode">
                          <?php echo TextOs::_( 'Postcode' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_require_postcode = '';
					if (isset($configs['require_postcode']) && $configs['require_postcode'] == 1){
						$checkbox_require_postcode = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_require_postcode" value="" <?php echo $checkbox_require_postcode;?> onclick="if(this.checked) adminForm['configuration[require_postcode]'].value = 1;else adminForm['configuration[require_postcode]'].value = 0;">
				<input type="hidden" name="configuration[require_postcode]" value="<?php echo isset($configs['require_postcode'])?$configs['require_postcode']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'City' );?>::<?php echo TextOs::_('CITY_EXPLAIN'); ?>">
                      <label for="checkbox_require_city">
                          <?php echo TextOs::_( 'City' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_require_city = '';
					if (isset($configs['require_city']) && $configs['require_city'] == 1){
						$checkbox_require_city = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_require_city" value="" <?php echo $checkbox_require_city;?> onclick="if(this.checked) adminForm['configuration[require_city]'].value = 1;else adminForm['configuration[require_city]'].value = 0;">
				<input type="hidden" name="configuration[require_city]" value="<?php echo isset($configs['require_city'])?$configs['require_city']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'City' );?>::<?php echo TextOs::_('STATE_EXPLAIN'); ?>">
                      <label for="checkbox_require_state">
                          <?php echo TextOs::_( 'State' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_require_state = '';
					if (isset($configs['require_state']) && $configs['require_state'] == 1){
						$checkbox_require_state = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_require_state" value="" <?php echo $checkbox_require_state;?> onclick="if(this.checked) adminForm['configuration[require_state]'].value = 1;else adminForm['configuration[require_state]'].value = 0;">
				<input type="hidden" name="configuration[require_state]" value="<?php echo isset($configs['require_state'])?$configs['require_state']:'0' ?>">
			</td>
		</tr>
	</table>
</fieldset>
-->