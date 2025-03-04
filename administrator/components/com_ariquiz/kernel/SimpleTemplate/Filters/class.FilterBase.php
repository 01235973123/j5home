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

class AriSimpleTemplateFilterBase
{
	function __construct()
	{
		$this->_register();
	}
	
	function _register()
	{
		 AriSimpleTemplate::registerFilter($this->getFilterName(), $this->getClassName());
	}
	
	function getFilterName()
	{
		return '';
	}
	
	function parse($val, ...$args)
	{
		
	}
	
	function getClassName()
	{
		$class = isset($this) ? strtolower(get_class($this)) : null;

		return $class;
	}
}