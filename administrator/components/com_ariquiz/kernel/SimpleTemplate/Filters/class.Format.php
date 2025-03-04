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

class AriSimpleTemplateFormatFilter extends AriSimpleTemplateFilterBase
{
	function getFilterName()
	{
		return 'format';
	}

	function parse($value, ...$args)
	{
		list($format) = array_replace_recursive([null], $args);

		return !empty($format) ? sprintf($format, $value) : $value;
	}
}

new AriSimpleTemplateFormatFilter();