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
 * Renders a hidden element
 *
 * @package     Joomla.Platform
 * @subpackage  Parameter
 */
class JElementHidden extends JElement
{
	/**
	 * Element name
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_name = 'Hidden';

	/**
	 * Fetch a hidden element
	 *
	 * @param   string       $name          Element name
	 * @param   string       $value         Element value
	 * @param   JXMLElement  &$node         JXMLElement node object containing the settings for the element
	 * @param   string       $control_name  Control name
	 *
	 * @return  string
	 *
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$class = AriXmlHelper::getAttribute($node, 'class');
		if ($class)
			$class = 'class="' . $class . '"';
		else
			$class = 'class="text_area"';

		return '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . $value . '" ' . $class
			. ' />';
	}

	/**
	 * Fetch tooltip for a hidden element
	 *

	 * @param   string       $label         Element label
	 * @param   string       $description   Element description (which renders as a tool tip)
	 * @param   JXMLElement  &$xmlElement   Element object
	 * @param   string       $control_name  Control name
	 * @param   string       $name          Element name
	 *
	 * @return  string
	 */
	public function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
	{
		return false;
	}
}