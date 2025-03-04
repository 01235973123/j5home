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

if (J3_0)
	require_once dirname(__FILE__) . DS . 'j30' . DS . 'class.ViewBase.php';
else
	require_once dirname(__FILE__) . DS . 'j15' . DS . 'class.ViewBase.php';