<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die ('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';

AriKernel::import('Utils.DateUtility');
AriKernel::import('Xml.XmlHelper');

class JElementCalendar extends JElement
{
	var	$_name = 'Calendar';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$ctrlId = $control_name . $name;
		$ctrlName = $control_name . '[' . $name .']';
		$ctrlPrefix = $control_name . '_' . $name;
		$useTime = (bool)AriXmlHelper::getAttribute($node, 'use_time', true);
		
		$date = null;
		if ($value)
		{
			$db = JFactory::getDBO();
			if ($value != $db->getNullDate())
			{
				$date = new JDate($value, 'UTC');
				$date->setTimezone(AriDateUtility::getTimeZone2());
			}
		}
		
		$month = $date ? $date->format('m', true) : 0;
		$day = $date ? $date->format('d', true) : 0;
		$year = $date ? $date->format('Y', true) : 0;

		$hour = $date ? $date->format('H', true) : 0;
		$minute = $date ? $date->format('i', true) : 0;

		$selectedDate = $date ? (intval($month, 10) . '/' . intval($day, 10) . '/' . $year) : '';
		$containerId = uniqid('cal', false);

		$this->registerAssets();
		$this->addScript($containerId, $ctrlPrefix, $ctrlId, $ctrlName, $selectedDate);

		return sprintf(
		'<div id="%7$s" style="position: relative;" class="calendar-element'  . (J4 ? ' j4' : '') . '">' .
			'<div class="calendar-element-container">' .
			'<div>' .
				'<input type="text" id="tbx%1$s" class="text_area ari-date-ctrl ari-tbx-cal form-control" size="10" readonly="readonly" value="%6$s" />' .
				'<input type="hidden" id="%2$s" name="%3$s" class="ari-date-ctrl" value="%5$s" />' .
			'</div>' .
			($useTime
				? '<div>' .
					JHTML::_('select.integerlist', 0, 23, 1, 'ddlStartHour' . $ctrlPrefix, ' class="ari-date-ctrl ari-cal-hour form-select" id="' . 'ddlStartHour' . $ctrlPrefix . '"', $date ? intval($hour, 10) : 0) .
					' : ' .
					JHTML::_('select.integerlist', 0, 59, 1, 'ddlStartMinute' . $ctrlPrefix, ' class="ari-date-ctrl ari-cal-minute form-select" id="' . 'ddlStartMinute' . $ctrlPrefix . '"', $date ? intval($minute, 10) : 0) .
				  '</div>'
				: ''
			) .
			'<div>&nbsp;&nbsp;[<a href="#" class="ari-date-reset" onclick="return false;">Clear</a>]</div>' .
			'</div>' .
			'<div id="%1$sHolder" class="ari-calendar-overlay">' .
				'<div id="%1$sContainer" style="visibility: hidden;" class="ari-calendar">' .
					'<div class="hd">%4$s :</div>' .
				'</div>' .
			'</div>' .
		'</div>',
			$ctrlPrefix,
			$ctrlId,
			$ctrlName,
			JText::_(AriXmlHelper::getAttribute($node, 'label')),
			$date ? AriDateUtility::toUnix2($date) : '',
			$selectedDate,
			$containerId
		);
	}

	function addScript($containerId, $ctrlPrefix, $ctrlId, $ctrlName, $selectedDate)
	{
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration(sprintf(
			'ARICalendarElement("%1$s", "%2$s", "%3$s", "%4$s");',
			$containerId,
			$ctrlPrefix,
			$ctrlId,
			$selectedDate
		));
	}
	
	function registerAssets()
	{
		$doc =& JFactory::getDocument();
		
		$uri = JURI::root(true) . '/administrator/components/com_ariquiz/elements/assets/calendar/';
		$doc->addScript($uri . 'calendar.js');
	}
}