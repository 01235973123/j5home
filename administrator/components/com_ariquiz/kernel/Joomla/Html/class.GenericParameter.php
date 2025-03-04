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

AriKernel::import('Joomla.Html.Parameter');
AriKernel::import('Xml.XmlHelper');

class AriGenericParameter extends AriParameter
{
	function isAcceptableParam($param)
	{
		return true;
	}
	
	function getParams($name = 'params', $group = '_default', $recursive = false)
	{
		if (!isset($this->_xml[$group])) 
			return false;

		$results = array();
		foreach ($this->_xml[$group]->children() as $param)  
		{
			if (!$this->isAcceptableParam($param))
				continue ;

			$result = $this->getParam($param, $name);
			$result[6] = $param; 
			$results[] = $result;
			
			if ($recursive) 
			{
				$childResults = $this->getChildParams($param, $name, $group);
				if (count($childResults) > 0)
					$results = array_merge($results, $childResults);
			}
		}

		return $results;
	}
	
	function getChildParams($parentNode, $name = 'params', $group = '_default')
	{
		$results = array();
		
		foreach ($parentNode->children() as $child)
		{
			$tagName = J1_6 ? $child->getName() : $child->name();
			if ($tagName == 'param')
			{
				if ($this->isAcceptableParam($child))
				{
					$result = $this->getParam($child, $name);
					$result[6] = $child; 
					$results[] = $result;
				}
			}
			
			$childResults = $this->getChildParams($child, $name, $group);
			if (count($childResults) > 0)
					$results = array_merge($results, $childResults);
		}
		
		return $results;
	}
	
	function render($title, $name = 'params', $group = '_default', $options = array('paramsPerRow' => 1))
	{
		if (!isset($this->_xml[$group])) 
			return '';

		$params = $this->getParams($name, $group);
		if (empty($params))
			return '';
		
		$paramsPerRow = isset($options['paramsPerRow']) ? intval($options['paramsPerRow']) : 1;
		$cellWidth = floor(40 / $paramsPerRow);
		
		$html = array();
		$html[] = '<table width="100%" border="0" cellpadding="3" cellspacing="0" class="paramlist admintable">';
		$idx = 0;
		$hidden = '';
		foreach ($params as $param)
		{
			$paramNode = $param[6];
			$attrType = AriXmlHelper::getAttribute($paramNode, 'type');
			$isHidden = ($attrType == 'hidden');
			if ($isHidden)
			{
				$hidden = $param[1];
				continue ;
			}

			$hideLabels = (bool)AriXmlHelper::getAttribute($paramNode, 'hide_label');
			$dimension = intval(AriXmlHelper::getAttribute($paramNode, 'dimension', 1), 10);
			
			if ($dimension > 1)
			{
				if ($dimension > $paramsPerRow)
					$dimension = $paramsPerRow;

				$freeSeats = $paramsPerRow - $idx;
				if ($dimension > $freeSeats)
				{
					$html[] = '<td colspan="' . (2 * $freeSeats) . '"></td></tr>';
					$idx = 0;
				}
			}
			
			if ($idx == 0)
				$html[] = '<tr>';		

			$ctrlId = $name . $param[5];
			if (!$hideLabels)
			{
				$descr = '';
				if (!empty($param[2]))
					$descr = ' class="hasTip" title="' . JText::_($param[3]) . '::' . JText::_($param[2]) . '"';

				$html[] = '<td width="' . $cellWidth . '%" class="paramlist_key"><label id="' . $ctrlId . '-lbl" for="' . $ctrlId . '"' . $descr . '>' . JText::_(isset($param[3]) ? $param[3] : '') . '</label></td>';
			}

			$html[] = '<td class="paramlist_value" colspan="' . (2 * $dimension - (!$hideLabels ? 1 : 0)) . '">' . $param[1] . '</td>';
			
			if ($idx == ($paramsPerRow - 1))
				$html[] = '</tr>';
			
			$idx += $dimension;
			$idx %= $paramsPerRow;;
		}
		
		if ($idx > 0)
			$html[] = '<td colspan="' . (2 * ($paramsPerRow - $idx)) . '"></td></tr>';
		
		$html[] = '</table>' . $hidden;
		
		return implode("\n", $html);
	}
}