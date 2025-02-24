<?php 
/*------------------------------------------------------------------------
# global.php - Ossolution Property
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
<?php 
	$option_order = array();
	$option_order[] = HTMLHelper::_('select.option','ASC',TextOs::_('Ascending'));
	$option_order[] = HTMLHelper::_('select.option','DESC',TextOs::_('Descending'));?>
<fieldset class="form-horizontal options-form">
	<legend><?php echo TextOs::_('DEFAULT_SORTING_SETTING')?></legend>
	<table cellpadding="0" cellspacing="0" width="100%" class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Default Agents Sort' );?>::<?php echo TextOs::_('AGENT_SORT_EXPLAIN'); ?>">
					<label for="configuration[general_default_agents_sort]">
					    <?php echo TextOs::_( 'Default Agents Sort' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php 
					if (!isset($configs['general_default_agents_sort']) || $configs['general_default_agents_sort'] != 'name') $configs['general_default_agents_sort'] = 'ordering';
					$option_agent_sort = array();
					$option_agent_sort[] = HTMLHelper::_('select.option','name',TextOs::_('Name'));
					$option_agent_sort[] = HTMLHelper::_('select.option','ordering',TextOs::_('Order'));
					echo HTMLHelper::_('select.genericlist',$option_agent_sort,'configuration[general_default_agents_sort]','class="inputbox"','value','text',$configs['general_default_agents_sort']);				
				?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Default Agents Order' );?>::<?php echo TextOs::_('AGENT_ORDER_EXPLAIN'); ?>">
					<label for="configuration[general_default_agents_order]">
					    <?php echo TextOs::_( 'Default Agents Order' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
					if (!isset($configs['general_default_agents_order'])) $configs['general_default_agents_order'] = 'ASC'; 
					echo HTMLHelper::_('select.genericlist',$option_order,'configuration[general_default_agents_order]','class="inputbox"','value','text',$configs['general_default_agents_order']);
				?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Default Categories Sort' );?>::<?php echo TextOs::_('CATEGORIES_SORT_EXPLAIN'); ?>">
					<label for="configuration[general_default_categories_sort]">
					    <?php echo TextOs::_( 'Default Categories Sort' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php 
					if (!isset($configs['general_default_categories_sort'])) $configs['general_default_categories_sort'] = 'ordering';
					$option_agent_sort = array();
					$option_agent_sort[] = HTMLHelper::_('select.option','category_name',TextOs::_('Name Category'));
					$option_agent_sort[] = HTMLHelper::_('select.option','ordering',TextOs::_('Order'));
					echo HTMLHelper::_('select.genericlist',$option_agent_sort,'configuration[general_default_categories_sort]','class="inputbox"','value','text',$configs['general_default_categories_sort']);				
				?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Default Categories Order' );?>::<?php echo TextOs::_('CATEGORIES_ORDER_EXPLAIN'); ?>">
					<label for="configuration[general_default_categories_order]">
					    <?php echo TextOs::_( 'Default Categories Order' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
					if (!isset($configs['general_default_categories_order'])) $configs['general_default_categories_order'] = 'ASC'; 
					echo HTMLHelper::_('select.genericlist',$option_order,'configuration[general_default_categories_order]','class="inputbox"','value','text',$configs['general_default_categories_order']);
				?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Default properties Sort' );?>::<?php echo TextOs::_('PROPERTIES_SORT_EXPLAIN'); ?>">
					<label for="configuration[general_default_properties_sort]">
					    <?php echo TextOs::_( 'Default properties Sort' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php 
					if (!isset($configs['general_default_properties_sort'])) $configs['general_default_properties_sort'] = 'pro_name';
					$option_agent_sort = array();
					$option_agent_sort[] = HTMLHelper::_('select.option','pro_name',TextOs::_('Name Property'));
					$option_agent_sort[] = HTMLHelper::_('select.option','price',TextOs::_('Price'));
					$option_agent_sort[] = HTMLHelper::_('select.option','city',TextOs::_('City'));
					echo HTMLHelper::_('select.genericlist',$option_agent_sort,'configuration[general_default_properties_sort]','class="inputbox form-select"','value','text',$configs['general_default_properties_sort']);				
				?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Default properties Order' );?>::<?php echo TextOs::_('PROPERTIES_ORDER_EXPLAIN'); ?>">
					<label for="configuration[general_default_properties_order]">
					    <?php echo TextOs::_( 'Default properties Order' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
					if (!isset($configs['general_default_properties_order'])) $configs['general_default_properties_order'] = 'ASC'; 
					echo HTMLHelper::_('select.genericlist',$option_order,'configuration[general_default_properties_order]','class="inputbox"','value','text',$configs['general_default_properties_order']);
				?>
			</td>
		</tr>
	</table>
</fieldset>