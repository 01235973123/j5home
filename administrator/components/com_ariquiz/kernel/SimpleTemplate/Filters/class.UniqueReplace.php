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

class AriSimpleTemplateUniqueReplaceFilter extends AriSimpleTemplateFilterBase
{	
	function getFilterName()
	{
		return 'unique_replace';
	}

	function parse($value, ...$args)
	{
		list($find, $replacement) = array_replace_recursive(['', ''], $args);

		return str_replace($find, str_replace('@uniqid', uniqid('adt_', false), $replacement), $value);
	}
}

new AriSimpleTemplateUniqueReplaceFilter();