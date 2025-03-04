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

class AriSimpleTemplateEmptyFilter extends AriSimpleTemplateFilterBase
{
	function getFilterName()
	{
		return 'empty';
	}

	function parse($value, ...$args)
	{
		list($replaceValue) = array_replace_recursive([''], $args);

		return empty($value) ? $replaceValue : $value;
	}
}

new AriSimpleTemplateEmptyFilter();