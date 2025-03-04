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

AriKernel::import('SimpleTemplate.Filters.FilterBase');

class AriSimpleTemplateLowerCaseFilter extends AriSimpleTemplateFilterBase
{	
	function getFilterName()
	{
		return 'lower_case';
	}

	function parse($value, ...$args)
	{
		return strtolower($value);
	}
}

new AriSimpleTemplateLowerCaseFilter();