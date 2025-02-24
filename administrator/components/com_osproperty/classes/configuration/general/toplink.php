<?php 
/*------------------------------------------------------------------------
# toplink.php - Ossolution Property
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
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Top category link' );?>::<?php echo TextOs::_('Top category link explain'); ?>">
				 	<label for="configuration[bussiness_name]">
						<?php echo TextOs::_( 'Top category link' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php 
				if (version_compare(JVERSION, '3.0', 'lt')) {
					$optionArr = array();
					$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
					$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[show_category_link]','class="input-mini"','value','text',$configs['show_category_link']);
				}else{
					$name = "show_category_link";
					if(intval($configs[$name]) == 0){
						$checked2 = 'checked="checked"';
						$checked1 = "";
					}else{
						$checked1 = 'checked="checked"';
						$checked2 = "";
					}
					
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
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Company link' );?>::<?php echo TextOs::_('Company link explain'); ?>">
				 	<label for="configuration[show_companies_link]">
						<?php echo TextOs::_( 'Company link' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
				if (version_compare(JVERSION, '3.0', 'lt')) {
					
					
					$optionArr = array();
					$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
					$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[show_companies_link]','class="input-mini"','value','text',$configs['show_companies_link']);
				}else{
					$name = "show_companies_link";
					if(intval($configs[$name]) == 0){
						$checked2 = 'checked="checked"';
						$checked1 = "";
					}else{
						$checked1 = 'checked="checked"';
						$checked2 = "";
					}
					
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
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Top add property link' );?>::<?php echo TextOs::_('Top add property link explain'); ?>">
				 	<label for="configuration[show_add_properties_link]">
						<?php echo TextOs::_( 'Top add property link' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
				if (version_compare(JVERSION, '3.0', 'lt')) {
					
					$optionArr = array();
					$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
					$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[show_add_properties_link]','class="input-mini"','value','text',$configs['show_add_properties_link']);
				}else{
					$name = "show_add_properties_link";
					if(intval($configs[$name]) == 0){
						$checked2 = 'checked="checked"';
						$checked1 = "";
					}else{
						$checked1 = 'checked="checked"';
						$checked2 = "";
					}
					
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
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Top agent listing link' );?>::<?php echo TextOs::_('Top agent listing link explain'); ?>">
				 	<label for="configuration[show_agents]">
						<?php echo TextOs::_( 'Top agent listing link' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
				if (version_compare(JVERSION, '3.0', 'lt')) {
					
					
					$optionArr = array();
					$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
					$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[show_agents]','class="input-mini"','value','text',$configs['show_agents']);
				}else{
					$name = "show_agents";
					if(intval($configs[$name]) == 0){
						$checked2 = 'checked="checked"';
						$checked1 = "";
					}else{
						$checked1 = 'checked="checked"';
						$checked2 = "";
					}
					
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
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Top search link' );?>::<?php echo TextOs::_('Top search link explain'); ?>">
				 	<label for="configuration[show_search]">
						<?php echo TextOs::_( 'Top search link' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
				if (version_compare(JVERSION, '3.0', 'lt')) {
					
					$optionArr = array();
					$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
					$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[show_search]','class="input-mini"','value','text',$configs['show_search']);
				}else{
					$name = "show_search";
					if(intval($configs[$name]) == 0){
						$checked2 = 'checked="checked"';
						$checked1 = "";
					}else{
						$checked1 = 'checked="checked"';
						$checked2 = "";
					}
					
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
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Top favorites link' );?>::<?php echo TextOs::_('Top favorites link explain'); ?>">
				 	<label for="configuration[show_favorites]">
						<?php echo TextOs::_( 'Top favorites link' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
				if (version_compare(JVERSION, '3.0', 'lt')) {
					
					$optionArr = array();
					$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
					$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[show_favorites]','class="input-mini"','value','text',$configs['show_favorites']);
				}else{
					$name = "show_favorites";
					if(intval($configs[$name]) == 0){
						$checked2 = 'checked="checked"';
						$checked1 = "";
					}else{
						$checked1 = 'checked="checked"';
						$checked2 = "";
					}
					
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
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Top compare link' );?>::<?php echo TextOs::_('Top compare link explain'); ?>">
				 	<label for="configuration[show_compare]">
						<?php echo TextOs::_( 'Top compare link' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
				if (version_compare(JVERSION, '3.0', 'lt')) {
					
					
					$optionArr = array();
					$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
					$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[show_compare]','class="input-mini"','value','text',$configs['show_compare']);
				}else{
					$name = "show_compare";
					if(intval($configs[$name]) == 0){
						$checked2 = 'checked="checked"';
						$checked1 = "";
					}else{
						$checked1 = 'checked="checked"';
						$checked2 = "";
					}
					
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
			<td class="key" nowrap="nowrap" valign="top">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show top menu in' );?>::<?php echo TextOs::_('Show top menu in explain'); ?>">
				 	<label for="configuration[show_top_menus_in]">
						<?php echo TextOs::_( 'Show top menu in' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
					$pageArr[] = HTMLHelper::_('select.option','frontpage',Text::_('OS_FRONTPAGE'));
					$pageArr[] = HTMLHelper::_('select.option','property',Text::_('OS_PROPERTY'));
					$pageArr[] = HTMLHelper::_('select.option','agent',Text::_('OS_AGENT'));
					$pageArr[] = HTMLHelper::_('select.option','company',Text::_('OS_COMPANY'));
					$pageArr[] = HTMLHelper::_('select.option','category',Text::_('OS_CATEGORY'));
					$pageArr[] = HTMLHelper::_('select.option','search',Text::_('OS_SEARCH'));
					$pageArr[] = HTMLHelper::_('select.option','compare',Text::_('Compare properties'));
					$pageArr[] = HTMLHelper::_('select.option','direction',Text::_('Get Direction'));
					if (isset($configs['show_top_menus_in'])){
						$pagelist = $configs['show_top_menus_in'];
						$pagelistArr = explode("|",$pagelist);
					}
					echo HTMLHelper::_('select.genericlist',$pageArr,'show_top_menus_in[]','multiple','value','text',$pagelistArr) ;
				?>
			</td>
		</tr>
		
	</table>
</fieldset>

<fieldset>
	<legend><?php echo Text::_('OS_REPORT')?></legend>
	<table  width="100%" class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_REPORT' );?>::<?php echo Text::_('OS_REPORT_EXPLAIN'); ?>">
                      <label for="checkbox_general_show_top_menu">
                          <?php echo Text::_( 'OS_REPORT' ).':'; ?>
                      </label>
				</span>
			</td>
			<td>
			<?php 
				if (version_compare(JVERSION, '3.0', 'lt')) {
					$checkbox_general_show_top_menu = '';
					if (isset($configs['enable_report']) && $configs['enable_report'] == 1){
						$checkbox_general_show_top_menu = 'checked="checked"';
					}
				?>
				<input type="checkbox"  name="checkbox_general_show_top_menu" value="" <?php echo $checkbox_general_show_top_menu;?> onclick="if(this.checked) adminForm['configuration[enable_report]'].value = 1;else adminForm['configuration[enable_report]'].value = 0;">
				<input type="hidden" name="configuration[enable_report]" value="<?php echo isset($configs['enable_report'])?$configs['enable_report']:'0' ?>">
			<?php
				}else{
					if(intval($configs['enable_report']) == 0){
						$checked2 = 'checked="checked"';
						$checked1 = "";
					}else{
						$checked1 = 'checked="checked"';
						$checked2 = "";
					}
					$name = "enable_report";
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
	</table>
</fieldset>	