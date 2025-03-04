<?php
/*
 * ARI Framework Lite
 *
 * @package		ARI Framework Lite
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

 (defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

AriKernel::import('SimpleTemplate.Filters.FilterBase');

class AriSimpleTemplateParseIniFilter extends AriSimpleTemplateFilterBase
{	
	function getFilterName()
	{
		return 'parse_ini';
	}

	function parse($string, ...$args)
	{
		list($keys, $splitter) = array_replace_recursive(['', ''], $args);

		$ini = parse_ini_string($string);
		$keys = explode(',', $keys);
		
		$ret = array();
		foreach ($keys as $key)
		{
			if (isset($ini[$key]))
				$ret[$key] = $ini[$key];
		}
		
		return join($splitter, $ret);
	}
}

new AriSimpleTemplateParseIniFilter();