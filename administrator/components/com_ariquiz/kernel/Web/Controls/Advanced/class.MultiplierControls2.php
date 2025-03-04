<?php
/*
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

class WebControls_MultiplierControls2
{
	function getData($containerTree, $inputData = null)
	{
		if (is_null($inputData)) $inputData = $_REQUEST;
		$data = array();

		if (!is_array($inputData)) return $data;

		WebControls_MultiplierControls2::_getDataRecurive($containerTree, $inputData, $data);

		return $data;
	}
	
	function _getDataRecurive($containerTree, $inputData, &$data, $prefix = '')
	{
		foreach ($containerTree as $key => $value)
		{
			$data[$key] = array();
			$idList = WebControls_MultiplierControls2::_getTemplateIdList($prefix, $key, $inputData);
			$childs = isset($value['childs']) ? $value['childs'] : null;
			foreach ($idList as $id)
			{
				$newPrefix = WebControls_MultiplierControls2::_getPrefixByTemplateId($id, $key);
				$subData = array();
				
				if ($childs)
				{
					WebControls_MultiplierControls2::_getDataRecurive($childs, $inputData, $subData, $newPrefix);
				}
				
				$data[$key][] = array(
					'data' => WebControls_MultiplierControls2::_getItemData(
						$inputData, 
						$newPrefix, 
						$value['keys']),
					'childs' => $subData);
			}			
		}
	}
	
	function _getTemplateIdList($prefix, $templateId, $inputData)
	{
		$key = sprintf('%s%s_hdnStatus', $prefix, $templateId);
		$idList = array();
		if (isset($inputData[$key]))
		{
			$idList = explode(':', $inputData[$key]);
		}
		
		return $idList;
	}
	
	function _getItemData($inputData, $prefix, $keys)
	{
		$data = array();
		if ($keys)
		{
			foreach ($keys as $key)
			{
				$inputKey = $prefix . $key;
				$data[$key] = isset($inputData[$inputKey]) ? $inputData[$inputKey] : null;
			}
		}
		
		return $data;
	}

	function _getPrefixByTemplateId($rTemplateId, $templateId)
	{
		$prefix = substr($rTemplateId, 0, strlen($rTemplateId) - strlen($templateId));
		
		return $prefix;
	}
}