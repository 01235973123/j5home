<?php 
/*------------------------------------------------------------------------
# top_menu.php - Ossolution Property
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
	<legend><?php echo TextOs::_('Top menu')?></legend>
	<table  width="100%" class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show Top menu' );?>::<?php echo TextOs::_('Do you want to use the top menu links? Setting this to \'no\' will completely hide the top menu link.'); ?>">
                      <label for="checkbox_general_show_top_menu">
                          <?php echo TextOs::_( 'Show Top menu' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
			<?php 
				if (version_compare(JVERSION, '3.0', 'lt')) {
					$checkbox_general_show_top_menu = '';
					if (isset($configs['general_show_top_menu']) && $configs['general_show_top_menu'] == 1){
						$checkbox_general_show_top_menu = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_show_top_menu" value="" <?php echo $checkbox_general_show_top_menu;?> onclick="if(this.checked) adminForm['configuration[general_show_top_menu]'].value = 1;else adminForm['configuration[general_show_top_menu]'].value = 0;">
				<input type="hidden" name="configuration[general_show_top_menu]" value="<?php echo isset($configs['general_show_top_menu'])?$configs['general_show_top_menu']:'0' ?>">
			<?php
				}else{
					if(intval($configs['general_show_top_menu']) == 0){
						$checked2 = 'checked="checked"';
						$checked1 = "";
					}else{
						$checked1 = 'checked="checked"';
						$checked2 = "";
					}
					$name = "general_show_top_menu";
					?>
					<fieldset id="jform_params_<?php echo $name;?>" class="radio btn-group">
						<input type="radio" id="jform_params_<?php echo $name;?>0" name="configuration[<?php echo $name; ?>]" value="1" <?php echO $checked1;?>/>
						<label for="jform_params_<?php echo $name;?>0"><?php echo Text::_('OS_YES');?></label>
						<input type="radio" id="jform_params_<?php echo $name;?>1" name="configuration[<?php echo $name; ?>]" value="0" <?php echO $checked2;?>/>
						<label for="jform_params_<?php echo $name;?>1"><?php echo Text::_('OS_NO');?></label>
					</fieldset>
					<?php
				}
			?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'New listing' );?>::<?php echo TextOs::_('Do you want to show the new listings link on the front-end in the top menu?'); ?>">
                     <label for="checkbox_general_agent_listings">
                         <?php echo TextOs::_( 'New listing' ).':'; ?>
                     </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_general_new_listing = '';
					if (isset($configs['general_new_listing']) && $configs['general_new_listing'] == 1){
						$checkbox_general_new_listing = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_new_listing"  <?php echo $checkbox_general_new_listing;?> onclick="if(this.checked) adminForm['configuration[general_new_listing]'].value = 1;else adminForm['configuration[general_new_listing]'].value = 0;">
				<input type="hidden" name="configuration[general_new_listing]" value="<?php echo isset($configs['general_new_listing'])?$configs['general_new_listing']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Categories listing' );?>::<?php echo TextOs::_('Do you want to show the categories listings link on the front-end in the top menu?'); ?>">
                     <label for="checkbox_general_approval">
                         <?php echo TextOs::_( 'Categories listing' ).':'; ?>
                     </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_general_categories_listing = '';
					if (isset($configs['general_categories_listing']) && $configs['general_categories_listing'] == 1){
						$checkbox_general_categories_listing = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_categories_listing" value="" <?php echo $checkbox_general_categories_listing;?> onclick="if(this.checked) adminForm['configuration[general_categories_listing]'].value = 1;else adminForm['configuration[general_categories_listing]'].value = 0;">
				<input type="hidden" name="configuration[general_categories_listing]" value="<?php echo isset($configs['general_categories_listing'])?$configs['general_categories_listing']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Agents listing' );?>::<?php echo TextOs::_('Do you want to show the agents listing link on the front-end in the the top menu?'); ?>">
                      <label for="checkbox_free_listing_images">
                          <?php echo TextOs::_( 'Agents listing' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_general_agents_listing = '';
					if (isset($configs['general_agents_listing']) && $configs['general_agents_listing'] == 1){
						$checkbox_general_agents_listing = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_agents_listing" value="" <?php echo $checkbox_general_agents_listing;?> onclick="if(this.checked) adminForm['configuration[general_agents_listing]'].value = 1;else adminForm['configuration[general_agents_listing]'].value = 0;">
				<input type="hidden" name="configuration[general_agents_listing]" value="<?php echo isset($configs['general_agents_listing'])?$configs['general_agents_listing']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Member listing' );?>::<?php echo TextOs::_("Do you want to show the user's properties on the front-end in the top menu?"); ?>">
                      <label for="checkbox_free_listing_images">
                          <?php echo TextOs::_( 'Member listing' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_general_member_listing = '';
					if (isset($configs['general_member_listing']) && $configs['general_member_listing'] == 1){
						$checkbox_general_member_listing = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_member_listing" value="" <?php echo $checkbox_general_member_listing;?> onclick="if(this.checked) adminForm['configuration[general_member_listing]'].value = 1;else adminForm['configuration[general_member_listing]'].value = 0;">
				<input type="hidden" name="configuration[general_member_listing]" value="<?php echo isset($configs['general_member_listing'])?$configs['general_member_listing']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Top featured listing' );?>::<?php echo TextOs::_('Do you want to show the top featured listing menu in the the top menu.'); ?>">
                      <label for="checkbox_free_listing_images">
                          <?php echo TextOs::_( 'Top featured listing' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_general_top_featured_listing = '';
					if (isset($configs['general_top_featured_listing']) && $configs['general_top_featured_listing'] == 1){
						$checkbox_general_top_featured_listing = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_top_featured_listing" value="" <?php echo $checkbox_general_top_featured_listing;?> onclick="if(this.checked) adminForm['configuration[general_top_featured_listing]'].value = 1;else adminForm['configuration[general_top_featured_listing]'].value = 0;">
				<input type="hidden" name="configuration[general_top_featured_listing]" value="<?php echo isset($configs['general_top_featured_listing'])?$configs['general_top_featured_listing']:'0' ?>">
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'My Favorites' );?>::<?php echo TextOs::_('Show the link to the My Favorites page of registered users'); ?>">
                      <label for="checkbox_free_listing_images">
                          <?php echo TextOs::_( 'My Favorites' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_general_my_favorites = '';
					if (isset($configs['general_my_favorites']) && $configs['general_my_favorites'] == 1){
						$checkbox_general_my_favorites = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_my_favorites" value="" <?php echo $checkbox_general_my_favorites;?> onclick="if(this.checked) adminForm['configuration[general_my_favorites]'].value = 1;else adminForm['configuration[general_my_favorites]'].value = 0;">
				<input type="hidden" name="configuration[general_my_favorites]" value="<?php echo isset($configs['general_my_favorites'])?$configs['general_my_favorites']:'0' ?>">
			</td>
		</tr>
		
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Search' );?>::<?php echo TextOs::_('Show the search link in the top menu'); ?>">
                      <label for="checkbox_free_listing_images">
                          <?php echo TextOs::_( 'Search' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
				<?php 
					$checkbox_general_search = '';
					if (isset($configs['general_search']) && $configs['general_search'] == 1){
						$checkbox_general_search = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_search" value="" <?php echo $checkbox_general_search;?> onclick="if(this.checked) adminForm['configuration[general_search]'].value = 1;else adminForm['configuration[general_search]'].value = 0;">
				<input type="hidden" name="configuration[general_search]" value="<?php echo isset($configs['general_search'])?$configs['general_search']:'0' ?>">
			</td>
		</tr>
	</table>
</fieldset>

