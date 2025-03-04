<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Joomla.Views.View');

class AriSubView extends AriView 
{
	var $_parentPrefix = '';
	
	function __construct($config = array())
	{
		if (array_key_exists('parent_prefix', $config)) {
			$this->_parentPrefix = $config['parent_prefix'];
		}
		
		if (!array_key_exists('template_path', $config) && $this->_parentPrefix) {
			$config['template_path'] = JPATH_COMPONENT . $this->_basePath. DS . 'views' . DS . $this->_parentPrefix . DS . $this->getName() . DS . 'tmpl';
		}

		parent::__construct($config);
	}
	
	function getName()
	{
		$name = parent::getName();
		if ($this->_parentPrefix && strpos($name, $this->_parentPrefix) === 0)
			$name = substr($name, strlen($this->_parentPrefix));
		
		return $name;
	}
}