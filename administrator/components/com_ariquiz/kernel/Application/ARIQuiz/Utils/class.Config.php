<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

AriKernel::import('Utils.Config');

class AriQuizConfig extends AriConfig
{
	var $_groups = array('_default', 'social');
	var $_table = '#__ariquizconfig';
	var $_modelPath;
	
	function __construct()
	{
		$this->_modelPath = AriQuizHelper::getFormPath('config', 'config');
		$this->_cachePath = JPATH_ROOT . DS . 'cache' . DS . 'ariquiz_config.php'; 
		
		parent::__construct();
	}
	
	function getCacheNS()
	{
		return '_Cache_' . __CLASS__;
	}
}