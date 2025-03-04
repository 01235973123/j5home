<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

AriKernel::import('Xml.XmlHelper');

/**
 * Renders a textarea element
 *
 * @package     Joomla.Platform
 * @subpackage  Parameter
 */
class JElementTextarea extends JElement
{
	/**
	 * Element name
	 *
	 * @var    string
	 */
	protected $_name = 'Textarea';

	/**
	 * Fetch the element
	 *
	 * @param   string       $name          Element name
	 * @param   string       $value         Element value
	 * @param   JXMLElement  &$node         JXMLElement node object containing the settings for the element
	 * @param   string       $control_name  Control name
	 *
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$rows = AriXmlHelper::getAttribute($node, 'rows');
		$cols = AriXmlHelper::getAttribute($node, 'cols');
		$class = AriXmlHelper::getAttribute($node, 'class');
		if ($class)
			$class = 'class="' . $class . '"';
		else
			$class = 'class="text_area"';
		// Convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", $value);

		return '<textarea name="' . $control_name . '[' . $name . ']" cols="' . $cols . '" rows="' . $rows . '" ' . $class . ' id="' . $control_name
			. $name . '" >' . $value . '</textarea>';
	}
}