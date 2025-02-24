<?php 
/*------------------------------------------------------------------------
# layout_of_site.php - Ossolution Property
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

$db = Factory::getDbo();
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass	   = $bootstrapHelper->getClassMapping('controls');
$inputLargeClass   = $bootstrapHelper->getClassMapping('input-large');
?>
<fieldset class="form-horizontal options-form">
	<legend><?php echo Text::_('General Setting')?></legend>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('load_bootstrap', Text::_( 'OS_LOAD_BOOTSTRAP' ), Text::_('OS_LOAD_BOOTSTRAP_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			OspropertyConfiguration::showCheckboxfield('load_bootstrap',$configs['load_bootstrap']);
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('twitter_bootstrap_version', Text::_( 'OS_BOOTSTRAP_VERSION' ), Text::_('OS_BOOTSTRAP_VERSION_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			//OspropertyConfiguration::showCheckboxfield('twitter_bootstrap_version',(int)$configs['twitter_bootstrap_version'],'Bootstrap version 3','Bootstrap version 2');
			$options   = array();
			$options[] = HTMLHelper::_('select.option', 2, Text::_('OS_VERSION_2'));
			$options[] = HTMLHelper::_('select.option', 3, Text::_('OS_VERSION_3'));
			$options[] = HTMLHelper::_('select.option', 4, Text::_('OS_VERSION_4'));

			echo HTMLHelper::_('select.genericlist', $options, 'configuration[twitter_bootstrap_version]', 'class="form-select input-large ilarge"', 'value', 'text', $configs['twitter_bootstrap_version'] ? (int)$configs['twitter_bootstrap_version'] : 2);
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('load_chosen', Text::_( 'OS_LOAD_CHOSEN' ), Text::_('OS_LOAD_CHOSEN_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			OspropertyConfiguration::showCheckboxfield('load_chosen',$configs['load_chosen']);
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('load_lazy', Text::_( 'OS_USE_LAZY' ), Text::_('OS_USE_LAZY_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			OspropertyConfiguration::showCheckboxfield('load_lazy',$configs['load_lazy']);
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('general_date_format', TextOs::_( 'Date format' ), Text::_('OS_SELECT_THE_DATE_FORMAT_FOR_PAGE_DISPLAY')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			$option_format_date = array();
			$option_format_date[] =  HTMLHelper::_('select.option','d-m-Y H:i:s','d-m-Y H:i:s');
			$option_format_date[] =  HTMLHelper::_('select.option','m-d-Y H:i:s','m-d-Y H:i:s');
			$option_format_date[] =  HTMLHelper::_('select.option','Y-m-d H:i:s','Y-m-d H:i:s');
			$option_format_date[] =  HTMLHelper::_('select.option','d-m-Y h:i A','d-m-Y h:i A');
			echo HTMLHelper::_('select.genericlist',$option_format_date,'configuration[general_date_format]','class="form-select input-large ilarge"','value','text',isset($configs['general_date_format'])? $configs['general_date_format']:'');
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('general_date_format', Text::_( 'OS_DEFAULT_COUNTRIES' ), TextOs::_('SELECT_COUNTRY_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			$db->setQuery("Select id as value, country_name as text from #__osrs_countries order by country_name");
			$countries = $db->loadObjectList();
			$checkbox_show_country_id = array();
			if (isset($configs['show_country_id'])){
				$checkbox_show_country_id = explode(',',$configs['show_country_id']);
			}
			if($configs['show_country_id'] == ""){
				$checkbox_show_country_id[] = 194;
			}
			echo OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$countries,'configuration[show_country_id][]','multiple class="inputbox"','value','text',$checkbox_show_country_id));
			?>
		</div>
	</div>

	<?php
	$query = $db->getQuery(true);
	$query->clear();
	$rows = array();
	$query->select('a.id AS value, a.title AS text, a.level');
	$query->from('#__menu AS a');
	$query->join('LEFT', $db->quoteName('#__menu').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');

	$query->where('a.menutype != '.$db->quote(''));
	$query->where('a.component_id IN (SELECT extension_id FROM #__extensions WHERE element="com_osproperty")');
	$query->where('a.client_id = 0');
	$query->where('a.published = 1');

	$query->group('a.id, a.title, a.level, a.lft, a.rgt, a.menutype, a.parent_id, a.published');
	$query->order('a.lft ASC');

	// Get the options.
	$db->setQuery($query);
	$rows = $db->loadObjectList();

	// Pad the option text with spaces using depth level as a multiplier.
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$rows[$i]->text = str_repeat('- ', $rows[$i]->level).$rows[$i]->text;
	}
	$options = array();
	$options[] = HTMLHelper::_('select.option', 0, Text::_('-- None --'), 'value', 'text');
	$rows = array_merge($options, $rows);

	$lists['default_menu_item'] = HTMLHelper::_('select.genericlist', $rows, 'configuration[default_itemid]',
		array(
			'option.text.toHtml'	=> false,
			'option.text'			=> 'text',
			'option.value'			=> 'value',
			'list.attr'				=> ' class="form-select input-large ilarge" ',
			'list.select'			=> $configs['default_itemid']));
	?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('load_lazy', Text::_( 'OS_DEFAULT_ITEMID' ), Text::_('OS_DEFAULT_ITEMID_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $lists['default_menu_item']; ?>
		</div>
	</div>
</fieldset>