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

class AriDateUtility extends AriDateUtilityBase
{
	static public function toDbUtcDate($date)
	{
		if (empty($date))
		{
			$db =& JFactory::getDBO();
			return $db->getNullDate();
		}

		$date = new JDate($date, AriDateUtility::getTimeZone2());
		$ts = $date->toUnix();
		$ts -= $date->getOffsetFromGMT();
		
		$utcDate = new JDate($ts, 'UTC');

		return $utcDate->toSql();		
	}
	
	static public function toUnixUTC($date)
	{
		if (empty($date))
			return 0;
			
		$date = new JDate($date, AriDateUtility::getTimeZone2());
		$ts = $date->toUnix();
		$ts -= $date->getOffsetFromGMT();

		return $ts;
	}
	
	static public function getDbUtcDate()
	{
		$date = new JDate();
		
		return $date->toSql();
	}
	
	static public function toUnix2($date, $local = true)
	{
		$ts = $date->toUnix();

		if ($local)
			$ts += $date->getOffsetFromGMT();
			
		return $ts;
	}
	
	static public function formatDate($date, $format = null, $tz = null)
	{	
		if ($date && preg_match("/([0-9]{4})\-([0-9]{2})\-([0-9]{2})[ ]([0-9]{2})\:([0-9]{2})\:([0-9]{2})/", $date, $regs)) 
		{
			$format = AriDateUtilityBase::getFormat($format);
			$d = new JDate('now', AriDateUtility::getTimeZone2($tz));

			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
			$date = $date > -1 ? strftime($format, $date + $d->getOffsetFromGMT()) : '-';
		}
		
		return $date;
	}
	
	/*
	 * static
	 */
	static public function getTimeZone2($tz = null)
	{
		if (!is_null($tz))
			return $tz;

		$user =& JFactory::getUser();
		$userId = $user->get('id');
		$jConfig = new JConfig();
		if ($userId > 0)
		{
			$tz = $user->getParam('timezone', null);
		}
		
		if (is_null($tz))
			if (!empty($jConfig->offset))
				$tz = $jConfig->offset;
				
		if (!is_null($tz))
		{
			$tz = new DateTimeZone($tz);
		}
			
		return $tz;
	}
}