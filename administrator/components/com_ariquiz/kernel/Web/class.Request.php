<?php
/*
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

class AriRequest 
{
	static public function getIP()
	{
		$ip = getenv('HTTP_X_FORWARDED_FOR')
    		? getenv('HTTP_X_FORWARDED_FOR')
    		: getenv('REMOTE_ADDR');

    	return $ip;
	}
}