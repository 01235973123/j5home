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

jimport('joomla.utilities.date');

class AriDateUtilityBase extends JDate
{
	static public function getFormat($format = null)
	{
		if (empty($format))
			$format = '%Y-%m-%d %H:%M:%S';
			
		return $format;
	}
}