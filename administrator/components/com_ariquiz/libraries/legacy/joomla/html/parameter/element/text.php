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
 * Renders a text element
 *
 * @package     Joomla.Platform
 * @subpackage  Parameter
 */
class JElementText extends JElement
{
	/**
	 * Element name
	 *
	 * @var    string
	 */
	protected $_name = 'Text';

	/**
	 * Fetch the text field element
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
		$size = AriXmlHelper::getAttribute($node, 'size', '');
		if ($size)
			$size = 'size="' . $size . '"';

		$class = AriXmlHelper::getAttribute($node, 'class');
		if ($class)
			$class = 'class="' . $class . '"';
		else {
			$class = J4 ? 'class="form-control"' : 'class="text_area"';
		}
		
		$placeholder = AriXmlHelper::getAttribute($node, 'placeholder');
		if ($placeholder)
			$placeholder = JText::_($placeholder);

		// Required to avoid a cycle of encoding &

		$value = htmlspecialchars(htmlspecialchars_decode($value, ENT_QUOTES), ENT_QUOTES, 'UTF-8');
		if ($placeholder)
			$placeholder = htmlspecialchars(htmlspecialchars_decode($placeholder, ENT_QUOTES), ENT_QUOTES, 'UTF-8');

		return '<input type="text" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . $value . '" ' . ($placeholder ? ' placeholder="' . $placeholder . '" ' : '') . $class
			. ' ' . $size . ' />';
	}
}