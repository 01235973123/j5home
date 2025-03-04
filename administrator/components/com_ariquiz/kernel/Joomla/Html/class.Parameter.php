<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Joomla.Html.ParameterLoader');
AriKernel::import('Xml.XmlHelper');
	
class AriParameter extends AriJParameterBase
{ 
	var $_ignore = array();

	function getRaw()
	{
		return $this->_raw;
	}

	function getElementPath()
	{
		return $this->_elementPath;
	}
	
	function renderToArray($name = 'params', $group = '_default', $recursive = true)
	{
		if (!isset($this->_xml[$group]))
			return false;

		return $this->_renderToArray($this->_xml[$group], $name, $group, $recursive);
	}
	
	function _renderToArray($root, $name, $group, $recursive)
	{
		$results = array();
		foreach ($root->children() as $param)  
		{
			$pName = AriXmlHelper::getTagName($param);
			if ($pName == 'param')
			{
				$result = $this->getParam($param, $name, $group);
				$results[$result[5]] = $result;
			}
			
			if ($recursive)
			{
				$subResults = $this->_renderToArray($param, $name, $group, $recursive);
				if (count($subResults) > 0)
					$results = array_merge($results, $subResults);
			}
		}

		return $results;
	}
	
	function getParams($name = 'params', $group = '_default', $recursive = false)
	{
		if (!isset($this->_xml[$group]))
			return false;

		$results = array();
		foreach ($this->_xml[$group]->children() as $param)
		{
			$paramName = AriXmlHelper::getAttribute($param, 'name');
			if ($paramName && !empty($this->_ignore[$group]) && in_array($paramName, $this->_ignore[$group]))
				continue ;

			$results[] = $this->getParam($param, $name, $group);
		}

		return $results;
	}
	
	function ignore($name, $group = '_default')
	{
		if (!isset($this->_ignore[$group]))
			$this->_ignore[$group] = array();
		
		$this->_ignore[$group][] = $name;
	}
	
	function render($title, $name = 'params', $group = '_default', $options = array())
	{
		return '<fieldset class="adminform ari-form">' . parent::renderInt($name, $group) . '</fieldset>';
	}
	
	function bind($data, $group = '_default') 
	{
		$data = $this->_clearBindData($data, $group);

		return parent::bind($data, $group);
	}
	
	function _clearBindData($data, $group)
	{
		if (!isset($this->_xml[$group]))
			return array();
		
		if (!is_array($data) && !is_object($data))
			return $data;

		$supportedParams = $this->_collectParams($this->_xml[$group]);
		$clearData = array();
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				if (in_array($key, $supportedParams))
					$clearData[$key] = $value;
			}
		}
		else if (is_object($data))
		{
			$vars = get_object_vars($data);
			foreach ($vars as $key => $value)
			{
				if (in_array($key, $supportedParams))
					$clearData[$key] = $value;
			}
		}

		$data = $clearData;
		
		return $data;
	}
	
	function _collectParams($xmlNode)
	{
		$params = array();
		if (empty($xmlNode))
			return $params;
			
		foreach ($xmlNode->children() as $child)
		{
			$childTag = AriXmlHelper::getTagName($child);
			if ($childTag != 'param' && $childTag != 'group')
				continue ;

			$childName = AriXmlHelper::getAttribute($child, 'name');
			if ($childTag == 'param' && $childName)
				$params[] = $childName;

			$subParams = $this->_collectParams($child);
			if (count($subParams) > 0)
				$params = array_merge($params, $subParams);
		}
		
		return $params;
	}
	
	function setParamAttribute($key, $attribute, $val, $group = '_default')
	{
		if (!isset($this->_xml[$group]))
		{
			return false;
		}

		foreach ($this->_xml[$group]->children() as $param)
		{
			$name = (string)AriXmlHelper::getAttribute($param, 'name');
			if ($name == $key)
			{
				$param->addAttribute($attribute, $val);
				return true;
			}
		}
		
		return false;
	}
}