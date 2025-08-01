<?php
/**
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * @var \Joomla\Registry\Registry $params
 */

// Require library + register autoloader
require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

EventbookingHelper::loadLanguage();

EventbookingHelper::loadComponentCssForModules();

$config           = EventbookingHelper::getConfig();
$datePickerFormat = $config->get('date_field_format', '%Y-%m-%d');
$dateFormat       = str_replace('%', '', $datePickerFormat);
$input            = Factory::getApplication()->getInput();

$showCategory       = $params->get('show_category', 1);
$showLocation       = $params->get('show_location', 0);
$enableRadiusSearch = $params->get('enable_radius_search', 0);
$showFromDate       = $params->get('show_from_date', 0);
$showToDate         = $params->get('show_to_date', 0);

$categoryId     = $input->getInt('category_id', 0);
$locationId     = $input->getInt('location_id', 0);
$fromDate       = $input->getString('filter_from_date');
$toDate         = $input->getString('filter_to_date');
$text           = $input->getString('search', '');
$filterAddress  = $input->getString('filter_address');
$filterDistance = $input->getInt('filter_distance');

if ($fromDate)
{
	$date = DateTime::createFromFormat($dateFormat, $fromDate);

	if ($date !== false)
	{
		$fromDate = $date->format('Y-m-d');
	}
}

$bootstrapHelper = EventbookingHelperBootstrap::getInstance();

if ($bootstrapHelper->getFrameworkClass('uk-input'))
{ // UIKIT
	$inputClass  = 'uk-input';
	$selectClass = 'uk-select';
}
elseif ($bootstrapHelper->getFrameworkClass('form-select'))
{ // Bootstrap 5
	$inputClass  = 'form-control';
	$selectClass = 'form-select';
}
else
{
	$inputClass  = 'form-control';
	$selectClass = 'form-control';
}

if ($toDate)
{
	$date = DateTime::createFromFormat($dateFormat, $toDate);

	if ($date !== false)
	{
		$toDate = $date->format('Y-m-d');
	}
}

$text = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');

/* @var \Joomla\Database\DatabaseDriver $db */
$db          = Factory::getContainer()->get('db');
$query       = $db->getQuery(true);
$fieldSuffix = EventbookingHelper::getFieldSuffix();
$user        = Factory::getApplication()->getIdentity();

$bootstrapHelper = EventbookingHelperBootstrap::getInstance();

//Build Category Drodown
if ($showCategory)
{
	$query->select('id, parent AS parent_id')
		->select($db->quoteName('name' . $fieldSuffix, 'title'))
		->from('#__eb_categories')
		->where('published = 1')
		->whereIn('access', $user->getAuthorisedViewLevels())
		->order($config->get('category_dropdown_ordering', 'name'));
	$db->setQuery($query);
	$rows = $db->loadObjectList();

	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row = $rows[$i];

		if (!EventbookingHelper::getTotalEvent($row->id))
		{
			unset($rows[$i]);
		}
	}

	$children = [];

	// first pass - collect children
	foreach ($rows as $v)
	{
		$pt            = $v->parent_id;
		$list          = $children[$pt] ?? [];
		$list[]        = $v;
		$children[$pt] = $list;
	}

	$list      = HTMLHelper::_('menu.treerecurse', 0, '', [], $children, 9999, 0, 0);
	$options   = [];
	$options[] = HTMLHelper::_('select.option', 0, Text::_('EB_SELECT_CATEGORY'));

	foreach ($list as $listItem)
	{
		$options[] = HTMLHelper::_('select.option', $listItem->id, '&nbsp;&nbsp;&nbsp;' . $listItem->treename);
	}

	$lists['category_id'] = HTMLHelper::_('select.genericlist', $options, 'category_id', [
		'option.text.toHtml' => false,
		'list.attr'          => 'class="' . $selectClass . '"',
		'option.text'        => 'text',
		'option.key'         => 'value',
		'list.select'        => $categoryId,
	]);
}

//Build location dropdown
if ($showLocation)
{
	$config = EventbookingHelper::getConfig();

	$query->clear()
		->select('a.id')
		->select($db->quoteName('a.name' . $fieldSuffix, 'name'))
		->from('#__eb_locations AS a')
		->where('a.published = 1')
		->order('a.name');

	$subQuery = $db->getQuery(true);
	$subQuery->select('DISTINCT location_id')
		->from('#__eb_events AS b')
		->where('b.published = 1')
		->where('b.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');

	if ($config->hide_past_events)
	{
		$currentDate = $db->quote(HTMLHelper::_('date', 'Now', 'Y-m-d'));

		if ($config->show_children_events_under_parent_event)
		{
			$subQuery->where(
				'(DATE(b.event_date) >= ' . $currentDate . ' OR DATE(b.cut_off_date) >= ' . $currentDate . ' OR DATE(b.max_end_date) >= ' . $currentDate . ')'
			);
		}
		else
		{
			$subQuery->where('(DATE(b.event_date) >= ' . $currentDate . ' OR DATE(b.cut_off_date) >= ' . $currentDate . ')');
		}
	}

	$query->where('a.id IN (' . $subQuery . ')');

	$db->setQuery($query);
	$options              = [];
	$options[]            = HTMLHelper::_('select.option', 0, Text::_('EB_SELECT_LOCATION'), 'id', 'name');
	$options              = array_merge($options, $db->loadObjectList());
	$lists['location_id'] = HTMLHelper::_('select.genericlist', $options, 'location_id', ' class="' . $selectClass . '"', 'id', 'name', $locationId);
}

if ($enableRadiusSearch)
{
	$radiusOptions = $params->get('radius_options', '5,10,20,30,50,100,200');
	$options       = [];
	$radiusOptions = explode(',', $radiusOptions);
	$radiusOptions = array_map('trim', $radiusOptions);

	if ($config->get('radius_search_distance', 'KM') == 'KM')
	{
		$languageKey = 'EB_WITHIN_X_KM';
	}
	else
	{
		$languageKey = 'EB_WITHIN_X_MILE';
	}

	foreach ($radiusOptions as $option)
	{
		$options[] = HTMLHelper::_('select.option', (int) $option, Text::sprintf($languageKey, $option));
	}

	$lists['filter_distance'] = HTMLHelper::_(
		'select.genericlist',
		$options,
		'filter_distance',
		' class="' . $selectClass . '" ',
		'value',
		'text',
		$filterDistance
	);
}

$presetCategoryId = (int) $params->get('category_id');
$layout           = $params->get('layout_type', 'default');
$itemId           = (int) $params->get('item_id') ?: EventbookingHelper::getItemid();

require ModuleHelper::getLayoutPath('mod_eb_search', $params->get('module_layout', 'default'));
