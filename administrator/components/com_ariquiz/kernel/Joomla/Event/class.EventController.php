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

class AriEventController
{
	static public function raiseEvent($event, $params)
	{
		$params = array($params);
		$mainframe =& JFactory::getApplication('site');
		$mainframe->triggerEvent($event, $params);
	}
}