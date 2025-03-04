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
jimport('joomla.utilities.date');

class AriSimpleTemplateDateFormatFilter extends AriSimpleTemplateFilterBase
{	
	function getFilterName()
	{
		return 'date_format';
	}

	function parse($date, ...$args) 
	{
		list($dateFormat) = array_replace_recursive(['%b %e, %Y'], $args);

		if (empty($date))
			return $date;

        $date = new JDate($date);

        return J3_0 ? $date->format($dateFormat) : $date->toFormat($dateFormat);
       } 
}

new AriSimpleTemplateDateFormatFilter();