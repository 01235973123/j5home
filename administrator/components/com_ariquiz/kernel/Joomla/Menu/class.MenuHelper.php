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

class AriMenuHelper
{
	static function getActiveItemId()
	{
		$app = JFactory::getApplication();
		$menu =& $app->getMenu('site');
		$activeMenu =& $menu->getActive();
		
		return $activeMenu ? $activeMenu->id : 0;
	}
}