<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

AriKernel::import('Utils.Utils');

class AriArrayHelper
{
	static public function toInteger($array, $min = null, $max = null, $unique = true)
	{
		if (!is_array($array))
			$array = array($array);

		$intArray = array();
		$checkMin = is_int($min);
		$checkMax = is_int($max);

		foreach ($array as $k => $v) 
		{
			$i = (int)$v;
			if (($checkMin && $i < $min) || 
				($checkMax && $i > $max))
				continue ;
				
			$intArray[$k] = $i;
		}
		
		if ($unique)
			$intArray = array_unique($intArray);
			
		return $intArray;
	}
	
	static public function walkRecursive(&$data, $callback)
	{
		if (!is_array($data) || count($data) == 0 || empty($callback))
			return ;

		foreach ($data as $key => $value)
		{
			if (!is_array($value))
				$data[$key] = call_user_func_array($callback, array($value));
			else
				AriArrayHelper::walkRecursive($data[$key], $callback);
		}
	}
	
	static public function removeEmptyValues($array)
	{
		if (!is_array($array))
			return $array;

		$newArray = array();
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$newValue = static::removeEmptyValues($value);
				if (count($newValue) > 0)
					$newArray[$key] = $newValue;
			}
			else if (!is_string($value) || strlen($value) > 0)
			{
				$newArray[$key] = $value;
			}
		}
		
		return $newArray;
	}

	static public function toAssoc($array, $key)
	{
		$assocArray = array();

		reset($array);
		foreach ($array as $item)
		{
			$assocKey = AriUtils::getParam($item, $key);
			if (!is_null($assocKey))
				$assocArray[$assocKey] = $item;
		}

		return $assocArray;
	}
}