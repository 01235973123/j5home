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

define ('ARI_DATEDURATION_YSC', 31556926);
define ('ARI_DATEDURATION_MSC', 2629743);
define ('ARI_DATEDURATION_WSC', 604800);
define ('ARI_DATEDURATION_DSC', 86400);
define ('ARI_DATEDURATION_HSC', 3600);
define ('ARI_DATEDURATION_MINSC', 60);
define ('ARI_DATEDURATION_SSC', 1);

class AriDateDurationUtility
{	
	static public function getPeriods()
	{
		return array(
			'years'     => ARI_DATEDURATION_YSC,
			'months'    => ARI_DATEDURATION_MSC,
			'weeks'     => ARI_DATEDURATION_WSC,
			'days'      => ARI_DATEDURATION_DSC,
			'hours'     => ARI_DATEDURATION_HSC,
			'minutes'   => ARI_DATEDURATION_MINSC,
			'seconds'   => ARI_DATEDURATION_SSC);
	}
	
	static public function getShortDayPeriods()
	{
		return array(
			'd'    => ARI_DATEDURATION_DSC,
			'h'    => ARI_DATEDURATION_HSC,
			'min'  => ARI_DATEDURATION_MINSC,
			'sec'  => ARI_DATEDURATION_SSC);
	}
	
    static public function toString($duration, $periods = null, $spliter = ' ', $ignorePlural = false)
    {
        if (!is_array($duration)) 
            $duration = self::intToArray($duration, $periods);
 
        return self::arrayToString($duration, $spliter, $ignorePlural);
    }
 
    static public function intToArray($seconds, $periods = null)
    {        
        if (!is_array($periods)) 
            $periods = self::getPeriods();

        $values = array();
        if ($seconds == 0 && is_array($periods) && count($periods) > 0)
        {
        	$intPeriods = array_values($periods);
        	sort($intPeriods);
        	$key = array_search($intPeriods[0], $periods);
        	
        	$values[$key] = 0;
        	return $values;
        }

        $seconds = (float) $seconds;
        foreach ($periods as $period => $value) 
        {
            $count = floor($seconds / $value);
 
            if ($count == 0) continue;
 
            $values[$period] = $count;
            $seconds = $seconds % $value;
        }

        if (empty($values)) 
        {
            $values = null;
        }
 
        return $values;
    }

    static public function arrayToString($duration, $spliter = ', ', $ignorePlural = false)
    {
        if (!is_array($duration)) 
        {
            return false;
        }
 
        foreach ($duration as $key => $value) 
        {
            $segment_name = $ignorePlural ? $key : substr($key, 0, -1);
            $segment = $value . ' ' . $segment_name; 

            if (!$ignorePlural && $value > 1) 
            {
                $segment .= 's';
            }
 
            $array[] = $segment;
        }

        return implode($spliter, $array);
    }
}