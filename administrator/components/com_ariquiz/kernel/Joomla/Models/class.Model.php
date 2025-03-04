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

AriKernel::import('Joomla.Models.ModelBase');

class AriModel extends AriModelBase
{
	var $_prefix;
	
	function &getTable($name = '', $prefix = '', $options = array()) 
	{
		if (empty($prefix))
			$prefix = $this->getPrefix() . 'Table';
 
		$ret = parent::getTable($name, $prefix, $options);
		return $ret;
	}
	
	function getPrefix() 
	{
		$prefix = $this->_prefix;

		if (empty($prefix)) 
		{
			$r = null;
			if (!preg_match( '/(.*)Model/i', get_class($this), $r))
				throw new Exception('AriModel::getPrefix() : Cannot get or parse class prefix.', 500);

			$prefix = $r[1];
			$this->_prefix = $prefix;
		}

		return $prefix;
	}
	
	function getFullPrefix() 
	{
		return $this->getPrefix() . 'Model';
	}
}