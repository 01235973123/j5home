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

AriKernel::import('Joomla.Html.GenericParameter');
AriKernel::import('Xml.XmlHelper');

class JElementGroup extends JElement
{
	var	$_name = 'Group';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$parent =& $this->_parent;
		
		$raw = null;
		if (method_exists($parent, 'getRaw'))
			$raw = $parent->getRaw();
		else 
			$raw = $parent->_raw;

		$childParameter = new AriGenericParameter($raw);
		if (empty($raw))
			$childParameter->merge($parent);

		$elementPath = null;
		if (method_exists($parent, 'getElementPath'))
			$elementPath = $parent->getElementPath();
		else 
			$elementPath = $parent->_elementPath;
			
		$paths = $elementPath;
		if (is_array($paths))
			foreach ($paths as $path)
				$childParameter->addElementPath($path);
		$childParameter->setXML($node);

		$visible = AriXmlHelper::getAttribute($node, 'visible');
		$prefix = AriXmlHelper::getAttribute($node, 'prefix');
		$hideHeader = (bool)AriXmlHelper::getAttribute($node, 'hide_header');
		$per_row = intval(AriXmlHelper::getAttribute($node, 'per_row', 1), 10);
		$id = 'group_' . $prefix . '_' . AriXmlHelper::getAttribute($node, 'group_id');

		return sprintf('<div id="%s" class="el-group" style="display: %s;"><div class="el-group-header">%s</div><div>%s</div></div>',
			$id,
			$visible ? 'block' : 'none',
			!$hideHeader ? '<h4>' . JText::_(AriXmlHelper::getAttribute($node, 'label')) . '</h4>' : '',
			$childParameter->render('', $control_name, '_default', array('paramsPerRow' => $per_row)));
	}
	
	function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='')
	{
		return '';
	}
}