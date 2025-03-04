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

AriKernel::import('Xml.XmlHelper');
AriKernel::import('Joomla.Html.HtmlHelper');

class JElementPeriod extends JElement
{
	var	$_name = 'Period';

	function fetchElement($name, $value, &$node, $control_name)
	{
		AriJoomlaHtmlHelper::loadJQuery();

		$ctrlId = $control_name . $name;
		$ctrlName = $control_name . '[' . $name .']';
		$ctrlPrefix = $control_name . '_' . $name;
		
		$periodTypes = array(
			array(
				'text' => 'COM_ARIQUIZ_PERIOD_DAY',
				'value' => 'day'
			),
			array(
				'text' => 'COM_ARIQUIZ_PERIOD_WEEK',
				'value' => 'week'
			),
			array(
				'text' => 'COM_ARIQUIZ_PERIOD_MONTH',
				'value' => 'month'
			),
			array(
				'text' => 'COM_ARIQUIZ_PERIOD_YEAR',
				'value' => 'year'
			)
		);
		
		$this->addScript($ctrlPrefix, $ctrlId);
		
		$parsedValue = $this->parseValue($value);
		
		return sprintf(
			'<div class="ari-period-element"><input type="text" id="tbx%1$s" class="text_area form-control ari-period-count" size="6" value="%5$d" /> ' .
			JHtml::_('select.genericlist', 
				$periodTypes,
				'',
				'class="form-select"',
				'value',
				'text',
				$parsedValue['type'],
				'ddl%1$s',
				true
			) .
			'<input type="hidden" id="%2$s" name="%3$s" value="%4$s" /></div>',
			$ctrlPrefix,
			$ctrlId,
			$ctrlName,
			$value,
			$parsedValue['count']
		);
	}
	
	function parseValue($value)
	{
		$parsedValue = array(
			'count' => 0,
			'type' => 'day'
		);
		
		if (empty($value))
			return $parsedValue;
			
		$value = json_decode($value);
		if (isset($value->count))
			$parsedValue['count'] = intval($value->count, 10);
			
		if (isset($value->type) && in_array($value->type, array('day', 'week', 'month', 'year')))
			$parsedValue['type'] = $value->type;
		
		return $parsedValue;
	}
	
	function addScript($ctrlPrefix, $hidElId)
	{
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration(
			sprintf('jQuery(function() {
				var oldSubmitHandler = Joomla.submitform;						 	
				Joomla.submitform = function() {
					var count = document.getElementById("tbx%1$s").value,
						type = document.getElementById("ddl%1$s").value,
						selValue = {"count": count, "type": type};

					$("%2$s").value = YAHOO.lang.JSON.stringify(selValue);
					oldSubmitHandler.apply(this, arguments);
				}
			});',
			$ctrlPrefix,
			$hidElId
			)
		);
	}	
}