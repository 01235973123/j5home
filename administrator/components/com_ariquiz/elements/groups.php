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

require_once dirname(__FILE__) . '/../kernel/class.AriKernel.php';

AriKernel::import('Xml.XmlHelper');
AriKernel::import('Joomla.Html.ParameterLoader');
AriKernel::import('Joomla.Html.HtmlHelper');

class JElementGroups extends JElement
{
	var	$_name = 'Groups';

	function fetchElement($name, $value, &$node, $control_name)
	{
		AriJoomlaHtmlHelper::loadJQuery();

		$selectId = $control_name . $name;
		$this->_addGroupAttrs($node, $value, $selectId);
		$parent =& $this->_parent;

		$raw = null;
		if (method_exists($parent, 'getRaw'))
			$raw = $parent->getRaw();
		else 
			$raw = $parent->_raw;

		$childParameter = new JParameterGroups($raw);
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
		$this->_includeAssets();

		$containerId = uniqid('groups', false);
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration(
			sprintf('jQuery(function(){ new ARIElementGroups("%s", %s); });',
				$containerId,
				json_encode(array('selectId' => $selectId))));
				
		return sprintf('<div id="%1$s" class="ari-groups"><fieldset><legend><label for="%2$s">%3$s</label>&nbsp;&nbsp;%4$s</legend><div class="ari-params-container">%5$s</div></fieldset></div>',
			$containerId,
			$selectId,
			JText::_(AriXmlHelper::getAttribute($node, 'label')),
			JHTML::_(
				'select.genericlist', 
				$this->_getOptionsGroup($node), 
				$control_name . '[' . $name . ']', 
				' class="inputbox form-select ari-group-params"', 
				'value', 
				'text', 
				$value, 
				$selectId), 
			$childParameter->render($control_name));
	}

	function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='')
	{
		return '';
	}
	
	function _includeAssets()
	{
		static $loaded;
		
		if ($loaded)
			return ;

		$filePath = str_replace(DS == '\\' ? '/' : '\\', DS, dirname(__FILE__));
		if (strlen(JPATH_ROOT) > 1)
			$filePath = str_replace(JPATH_ROOT, '', $filePath);
			
		$uri = JURI::root(true) . str_replace(DS, '/', $filePath) . '/';
			
		$document =& JFactory::getDocument();
		$document->addScript($uri . 'groups.js');
		$document->addStyleSheet($uri . 'groups.css', 'text/css', null, array());
			
		$loaded = true;
	}
	
	function _addGroupAttrs(&$node, $selectedGroup, $selectId)
	{
		if (empty($node->group))
			return $options;
			
		$hideHeaders = (bool)AriXmlHelper::getAttribute($node, 'hide_headers');

		foreach ($node->children() as $group)
		{
			$tagName = AriXmlHelper::getTagName($group);
			if ($tagName != 'group')
				continue ;
			
			$group_id = AriXmlHelper::getAttribute($group, 'group_id');
			$group->addAttribute('visible', $group_id == $selectedGroup ? '1' : '0');
			$group->addAttribute('prefix', $selectId);
			if ($hideHeaders)
				$group->addAttribute('hide_header', '1'); 
		}
	}
	
	function _getOptionsGroup(&$node)
	{
		$options = array();
		
		if (empty($node->group))
			return $options; 

		foreach ($node->group as $group)
		{
			$options[] = JHTML::_(
				'select.option', 
				AriXmlHelper::getAttribute($group, 'group_id'), 
				JText::_(AriXmlHelper::getAttribute($group, 'label'))
			);
		}

		return $options;
	}
}

class JParameterGroups extends AriJParameterBase
{
	function getRaw()
	{
		return $this->_raw;
	}
	
	function getElementPath()
	{
		return $this->_elementPath;
	}
	
	function render($name = 'params', $group = '_default')
	{
		if (!isset($this->_xml[$group]))
			return false;

		$params = $this->getParams($name, $group);
		$html = array();
		foreach ($params as $param)
		{
			$html[] = '' . $param[1] . '';
		}

		return implode("\n", $html);
	}
}