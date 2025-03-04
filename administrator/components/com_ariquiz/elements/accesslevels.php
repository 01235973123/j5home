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

class JElementAccesslevels extends JElement
{
	var	$_name = 'Accesslevels';

	function fetchElement($name, $value, &$node, $control_name)
	{
		return J1_6 
			? $this->_fetchElement($name, $value, $node, $control_name) 
			: $this->_fetchElementLegacy($name, $value, $node, $control_name);
	}

	function _fetchElement($name, $value, &$node, $control_name)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text');
		$query->from('#__viewlevels AS a');
		$query->group('a.id, a.title, a.ordering');
		$query->order('a.ordering ASC');
		$query->order($query->qn('title') . ' ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();

		$addInherited = (bool)AriXmlHelper::getAttribute($node, 'add_inherited');
		if ($addInherited)
		{
			$inheritedItem = new stdClass();
			$inheritedItem->value = -1;
			$inheritedItem->text = JText::_('COM_ARIQUIZ_LABEL_INHERITED');
			array_unshift($options, $inheritedItem);
		}
		
		return JHtml::_(
			'select.genericlist',
			$options,
			$control_name . '[' . $name . ']',
			array(
				'list.attr' => 'class="inputbox form-select"',
				'list.select' => $value,
				'id' => $control_name . $name
			)
		);
	}
	
	function _fetchElementLegacy($name, $value, &$node, $control_name)
	{
		$size = intval(AriXmlHelper::getAttribute($node, 'size'), 10);
		$multiple = (bool)AriXmlHelper::getAttribute($node, 'multiple');
		$rootGroup = AriXmlHelper::getAttribute($node, 'root_group');
		if (is_null($rootGroup))
			$rootGroup = 'USERS'; 

		$groupTree = array();
		$acl = JFactory::getAcl();
		$groupTree = $acl->get_group_children_tree(null, $rootGroup, true);
		
		$guest_label = AriXmlHelper::getAttribute($node, 'guest_label');
		if ($guest_label)
		{
			$guestItem = new stdClass();
			$guestItem->value = 0;
			$guestItem->text = JText::_($guest_label);
			
			array_unshift($groupTree, $guestItem);
		}
		
		$addInherited = (bool)AriXmlHelper::getAttribute($node, 'add_inherited');
		if ($addInherited)
		{
			$inheritedItem = new stdClass();
			$inheritedItem->value = -1;
			$inheritedItem->text = JText::_('COM_ARIQUIZ_LABEL_INHERITED');
			array_unshift($groupTree, $inheritedItem);
		}

		return JHTML::_(
			'select.genericlist', 
			$groupTree, 
			$control_name . '[' . $name . ']' . ($multiple ? '[]' : ''), 
			'class="inputbox"' . ($multiple ? ' multiple="multiple"' : '') . ($size ? ' size="' . $size . '"' : ''), 
			'value', 
			'text', 
			$value,
			$control_name . $name
		);
	}
}