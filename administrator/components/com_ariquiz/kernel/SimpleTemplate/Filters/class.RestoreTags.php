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

class AriSimpleTemplateRestoreTagsFilter extends AriSimpleTemplateFilterBase
{	
	function getFilterName()
	{
		return 'restore_tags';
	}

	function parse($value, ...$args)
	{
		if (empty($value)) return $value;
		
		return AriSimpleTemplateRestoreTagsFilter::restoreTags($value);
	}
	
	function restoreTags($input)
	{
  		$opened = array();
		// loop through opened and closed tags in order
  		if (preg_match_all("/<(\/?[^\s>]+)>?/i", $input, $matches)) 
  		{
    		foreach($matches[1] as $tag) 
    		{
    			if (strpos($tag, '/') !== 0)
    			{
    				$opened[] = $tag;
    			}
    			else
    			{
    				array_pop($opened);
    			}
    		}
  		}

		// close tags that are still open
  		if ($opened) 
  		{
    		$tagstoclose = array_reverse($opened);
    		foreach($tagstoclose as $tag) 
    			$input .= "</$tag>";
  		}

  		return $input;
	}
}

new AriSimpleTemplateRestoreTagsFilter();