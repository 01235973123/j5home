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

class AriSimpleTemplateRegexReplaceFilter extends AriSimpleTemplateFilterBase
{	
	function getFilterName()
	{
		return 'regexreplace';
	}

	function parse($value, ...$args)
	{
		list($find, $replacement) = array_replace_recursive(['', ''], $args);

		return preg_replace($find, $replacement, $value);
	}
}

new AriSimpleTemplateRegexReplaceFilter();