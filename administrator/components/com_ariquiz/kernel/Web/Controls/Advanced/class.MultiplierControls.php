<?php
/*
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

class WebControls_MultiplierControls
{
	static public function getData($containerId, $keys, $idKey = null, $stripSlashes = false)
	{
		$i = 0;
		$data = array();

		while (static::isSetTemplateItem($containerId, $i))
		{
			$dataItem = array();

			if (!empty($keys))
			{
				foreach ($keys as $key)
				{
					$itemKey = static::getTemplateItemKey($key, $i);
					$dataItem[$key] = null;
					if (isset($_REQUEST[$itemKey]))
					{
						$dValue = $_REQUEST[$itemKey];
						
						$dataItem[$key] = $dValue;
					}
					else if (isset($_FILES[$itemKey]))
					{
						$dataItem[$key] = $_FILES[$itemKey];
					}
				}
			}

			$itemIdKey = !empty($idKey) ? static::getTemplateItemKey($idKey, $i) : null;
			if (!empty($itemIdKey) && isset($dataItem[$itemIdKey]))
			{
				$data[$dataItem[$itemIdKey]] = $dataItem;
			}
			else
			{
				$data[] = $dataItem;
			}

			++$i;
		}

		return $data;
	}

	static public function isSetTemplateItem($containerId, $index)
	{
		return isset($_REQUEST[$containerId . '_hdnstatus_' . $index]);
	}

	static public function getTemplateItemKey($key, $index)
	{
		return $key . '_' . $index;
	}
	
	static public function dataToJson($data)
	{
		return json_encode($data); 
	}
}