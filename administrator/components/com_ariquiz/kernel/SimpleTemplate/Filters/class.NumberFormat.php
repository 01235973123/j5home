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

class AriSimpleTemplateNumberFormatFilter extends AriSimpleTemplateFilterBase
{	
	function getFilterName()
	{
		return 'number_format';
	}

	function parse($value, ...$args)
	{
		list($decimals, $dec_point, $thousands_sep) = array_replace_recursive([0, '.', ','], $args);

		return number_format($value, $decimals, $dec_point, $thousands_sep);
	}
}

new AriSimpleTemplateNumberFormatFilter();