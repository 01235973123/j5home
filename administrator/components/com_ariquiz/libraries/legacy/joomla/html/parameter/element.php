<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Parameter base class
 *
 * The JElement is the base class for all JElement types
 *
 * @package     Joomla.Platform
 * @subpackage  Parameter
 */
class JElement extends JObject
{
	/**
	 * Element name
	 *
	 * This has to be set in the final
	 * renderer classes.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_name = null;

	/**
	 * Reference to the object that instantiated the element
	 *
	 * @var    object
	 * @since  11.1
	 */
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @param   string  $parent  Element parent
	 */
	public function __construct($parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	 * Get the element name
	 *
	 * @return  string  type of the parameter
	 *
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Method to render an xml element
	 *
	 * @param   string  &$xmlElement   Name of the element
	 * @param   string  $value         Value of the element
	 * @param   string  $control_name  Name of the control
	 *
	 * @return  array  Attributes of an element
	 */
	public function render(&$xmlElement, $value, $control_name = 'params')
	{
		$name = (string)$xmlElement['name'];
		$label = (string)$xmlElement['label'];
		$descr = (string)$xmlElement['description'];

		//make sure we have a valid label
		$label = $label ? $label : $name;
		$result[0] = $this->fetchTooltip($label, $descr, $xmlElement, $control_name, $name);
		$result[1] = $this->fetchElement($name, $value, $xmlElement, $control_name);
		$result[2] = $descr;
		$result[3] = $label;
		$result[4] = $value;
		$result[5] = $name;

		return $result;
	}

	/**
	 * Method to get a tool tip from an XML element
	 *
	 * @param   string       $label         Label attribute for the element
	 * @param   string       $description   Description attribute for the element
	 * @param   JXMLElement  &$xmlElement   The element object
	 * @param   string       $control_name  Control name
	 * @param   string       $name          Name attribut
	 *
	 * @return  string
	 */
	public function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
	{
		$output = '<label id="' . $control_name . $name . '-lbl" for="' . $control_name . $name . '"';
		if ($description)
		{
			$output .= ' class="hasTip" title="' . JText::_($label) . '::' . JText::_($description) . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= JText::_($label) . '</label>';

		return $output;
	}

	/**
	 * Fetch an element
	 *
	 * @param   string       $name          Name attribute of the element
	 * @param   string       $value         Value attribute of the element
	 * @param   JXMLElement  &$xmlElement   Element object
	 * @param   string       $control_name  Control name of the element
	 *
	 * @return  void
	 */
	public function fetchElement($name, $value, &$xmlElement, $control_name)
	{
	}
}