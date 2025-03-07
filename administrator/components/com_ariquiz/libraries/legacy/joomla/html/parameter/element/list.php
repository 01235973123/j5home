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
 * Renders a list element
 *
 * @package     Joomla.Platform
 * @subpackage  Parameter
 * @since       11.1
 */
class JElementList extends JElement
{
	/**
	 * Element type
	 *
	 * @var    string
	 */
	protected $_name = 'List';

	/**
	 * Get the options for the element
	 *
	 * @param   JXMLElement  &$node  JXMLElement node object containing the settings for the element
	 *
	 * @return  array
	 */
	protected function _getOptions(&$node)
	{
		$options = array();
		foreach ($node->children() as $option)
		{
			$val = (string)$option['value'];
			$text = J3_0 ? (string)$option : $option->data();
			$options[] = JHtml::_('select.option', $val, JText::_($text));
		}
		return $options;
	}

	/**
	 * Fetch the HTML code for the parameter element.
	 *
	 * @param   string             $name          The field name.
	 * @param   mixed              $value         The value of the field.
	 * @param   JSimpleXMLElement  &$node         The current JSimpleXMLElement node.
	 * @param   string             $control_name  The name of the HTML control.
	 *
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$ctrl = $control_name . '[' . $name . ']';
		$attribs = ' ';

		$v = isset($node['size']) ? (string)$node['size'] : null;
		if ($v)
		{
			$attribs .= 'size="' . $v . '"';
		}
		
		$v = isset($node['class']) ? (string)$node['class'] : null;
		if ($v)
		{
			$attribs .= 'class="' . $v . '"';
		}
		else
		{
			$attribs .= J4 ? 'class="form-select"' : 'class="inputbox"';
		}
		
		$m = isset($node['multiple']) ? (string)$node['multiple'] : null;
		if ($m)
		{
			$attribs .= 'multiple="multiple"';
			$ctrl .= '[]';
		}

		return JHtml::_(
			'select.genericlist',
			$this->_getOptions($node),
			$ctrl,
			array('id' => $control_name . $name, 'list.attr' => $attribs, 'list.select' => $value)
		);
	}
}