<?php
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
/**
 * @version 1.5.0 2011-11-11
 * @package Joomla
 * @subpackage OS-Property
 * @copyright (C)  2016 the Ossolution
 * @license see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class modOspropertyOspropertyStatesHelper
{
	public static function getData($params,$module_id){
		$data  = self::Cache( 'ospropertystates.json', 7200,$params,$module_id );
		$items = json_decode($data);
		return $items;
	}
	/**
    * Simple caching function
    * @version  1.3
    * @param string $file
    * @param string | array $datafn                  e.g:  functionname |  array( object, function) ,
    * @param array  $datafnarg    default is array  e.g:   array( arg1, arg2, ...) ,       
    * @param mixed $time         default is 900  = 15 min
    * @param mixed $onerror      string function or array(object, method )
    * @return string
    */
    public static function Cache( $file, $time=900,$params,$module_id)
    {
    	jimport('joomla.filesystem.file');
    	jimport('joomla.filesystem.folder');
    	$moduledir = basename(dirname(__FILE__));
    	
        if (is_writable(JPATH_CACHE))
        {
            // check cache dir or create cache dir
            if (!Folder::exists(JPATH_CACHE.'/'.$moduledir.$module_id))
            {
                Folder::create(JPATH_CACHE.'/'.$moduledir.$module_id.'/'); 
            }
            $cache_file = JPATH_CACHE.'/'.$moduledir.$module_id.'/'.$file;
			//echo date("d-m-Y H:i:s",filemtime($cache_file));
			//echo "<BR />";
			//echo date("d-m-Y H:i:s",time());
            // check cache file, if not then write cache file
            if ( !File::exists($cache_file) )
            {
                $data = self::osGetStates($params);
                $data = json_encode($data);
                File::write($cache_file, $data);
            }  
            // if cache file expires, then write cache
            elseif ( filesize($cache_file) == 0 || ((filemtime($cache_file) + (int) $time ) < time()) )
            {
                $data = self::osGetStates($params);
                $data = json_encode($data);
                File::write($cache_file, $data);
            }
            // read cache file
            $data =  file_get_contents($cache_file);
            return $data;
        }
    }

	static function osGetStates($params) {
		global $mainframe;
		$lang_suffix		= OSPHelper::getFieldSuffix();
        $db					= Factory::getDBO();
        $list_type			= $params->get('list_type',0);
        $country_id			= $params->get('country',0);
        $hasproperties		= $params->get('hasproperties',0);
        $stateIds			= $params->get('stateIds','');
        if($list_type == 0)
		{
	        if($country_id > 0)
			{
	        	$sql = "";
	        	if($hasproperties == 1)
				{
	        		$sql	= "and id in (Select state from #__osrs_properties where approved = '1' and published = '1')";
	        	}
	        	$db->setQuery("Select id,state_name$lang_suffix as name from #__osrs_states where country_id = '$country_id' $sql order by state_name");
	        	$states = $db->loadObjectList();
	        	if(count($states) > 0)
				{
	        		for($i=0;$i<count($states);$i++)
					{
						$state = $states[$i];
						$db->setQuery("Select count(id) from #__osrs_properties where approved = '1' and published = '1' and state= '$state->id'");
						$states[$i]->nproperties = intval($db->loadResult());
	        		}
	        	}
	        	return $states;
	        }
        }
		elseif($list_type == 1)
		{
        	if($stateIds != "")
			{
	        	$sql = "";
	        	if($hasproperties == 1)
				{
	        		$sql = "and id in (Select city from #__osrs_properties where approved = '1' and published = '1')";
	        	}
				if($country_id > 0)
				{
					$countrySql = " and country_id = '$country_id'";
				}
				else
				{
					$countrySql = "";
				}
	        	$db->setQuery("Select id,city$lang_suffix as name from #__osrs_cities where 1=1 $countrySql and state_id in ($stateIds) $sql order by city");
	        	$cities = $db->loadObjectList();
	        	if(count($cities) > 0)
				{
	        		for($i=0;$i<count($cities);$i++)
					{
						$city = $cities[$i];
						$db->setQuery("Select count(id) from #__osrs_properties where approved = '1' and published = '1' and city= '$city->id'");
						$cities[$i]->nproperties = intval($db->loadResult());
	        		}
	        	}
	        	return $cities;
	        }
        }
		elseif($list_type == 2)
		{
			$configClass		= OSPHelper::loadConfig();
			$defaultcountry		= $configClass['show_country_id'];
			$db->setQuery("Select id,country_name$lang_suffix as name from #__osrs_countries where id in (".$defaultcountry.")");
			$countries			= $db->loadObjectList();
			if(count($countries))
			{
				foreach($countries as $country)
				{
					$db->setQuery("Select count(id) from #__osrs_properties where approved = '1' and published = '1' and country= '$country->id'");
					$country->nproperties = intval($db->loadResult());
				}
			}
			return $countries;
		}
	}
}

?>
