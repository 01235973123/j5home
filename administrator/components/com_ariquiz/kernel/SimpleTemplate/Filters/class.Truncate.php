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

class AriSimpleTemplateTruncateFilter extends AriSimpleTemplateFilterBase
{	
	function getFilterName()
	{
		return 'truncate';
	}

	function parse($value, ...$args)
	{
		list($length, $etc) = array_replace_recursive([null, '...'], $args);

		$length = @intval($length, 10);
		if ($length < 1) return $value;
		
		if (empty($etc) || strlen($value) <= $length) $etc = '';

		return substr($value, 0, $length) . $etc;
	}
}

new AriSimpleTemplateTruncateFilter();