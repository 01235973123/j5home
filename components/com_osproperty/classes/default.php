<?php
/*------------------------------------------------------------------------
# default.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class OspropertyDefault{
	/**
	 * Osproperty default
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$configClass;
		$document = Factory::getDocument();
		
		switch ($task){
			case "default_getstate":
				OspropertyDefault::defaultgetstate($option);
			break;
            case "default_mlssearch":
                OspropertyDefault::mlsSearch($option);
				HelperOspropertyCommon::loadFooter($option);
            break;
			case "default_suggestion":
				OspropertyDefault::suggestion();
			break;
			default:
				$show_top_menus_in = $configClass['show_top_menus_in'];
				$show_top_menus_in = explode("|",$show_top_menus_in);
				if(in_array('frontpage',$show_top_menus_in)){
					echo HelperOspropertyCommon::buildToolbar('default');
				}
				OspropertyDefault::defaultLayout($option);
				HelperOspropertyCommon::loadFooter($option);
			break;
		}
	}

	static function suggestion()
	{
		global $mainframe,$jinput,$configClass;
		$keyword = $jinput->getString('term','');
		$return = array();
		
		$db = Factory::getDbo();
		//$keyword = $db->quote($keyword);
		if($keyword != ""){
			$query = $db->getQuery(true);
			$query->select('distinct(address),city, state,region,postcode')->from('#__osrs_properties')->where("published = 1 and approved = 1 and show_address = 1 and (address like '%".$keyword."%' or region like '%".$keyword."%' or postcode like '%".$keyword."%') and address <> ''");
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			if(count($rows) > 0){
				foreach($rows as $row){
					$count = count($return);
					$return[$count]['value'] = OSPHelper::generateAddress($row);
					$return[$count]['data'] = OSPHelper::generateAddress($row);
				}
			}
			$query->clear();
			
			if(!HelperOspropertyCommon::checkCountry()){
				$country_id = HelperOspropertyCommon::getDefaultCountry();
			}else{
				$country_id = $configClass['show_country_id'];
			}
			
			$query->select('a.city, b.state_name,a.country_id')->from('#__osrs_cities as a')->join('inner',' #__osrs_states as b on a.state_id = b.id')->where("a.country_id in (".$country_id.") and a.city like '%".$keyword."%'");
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			if(count($rows) > 0){
				foreach($rows as $row){
					$count = count($return);
					$return[$count]['value'] = $row->city.", ".$row->state_name.", ".OSPHelper::loadCountryName($row->country_id);
					$return[$count]['data'] = $row->city.", ".$row->state_name.", ".OSPHelper::loadCountryName($row->country_id);
				}
			}
			$query->clear();
			$query->select('state_name,country_id')->from('#__osrs_states')->where("country_id in (".$country_id.") and state_name like '%".$keyword."%'");
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			if(count($rows) > 0){
				foreach($rows as $row){
					$count = count($return);
					$return[$count]['value'] = $row->state_name.", ".OSPHelper::loadCountryName($row->country_id);
					$return[$count]['data'] = $row->state_name.", ".OSPHelper::loadCountryName($row->country_id);
				}
			}
		}
		echo json_encode($return);
		$mainframe->close();
	}
	
	/**
	 * Default layout
	 *
	 * @param unknown_type $option
	 */
	static function defaultLayout($option){
		global $mainframe,$configs,$configClass,$lang_suffix;
		$db = Factory::getDBO();
		
		$document = Factory::getDocument();
		OSPHelper::generateHeading(1,$configClass['general_bussiness_name']);

        if($configClass['show_random_feature'] == 1) {
            $query = "Select a.*, b.state_name$lang_suffix as state_name,e.type_name$lang_suffix as type_name from #__osrs_properties as a"
                . " inner join #__osrs_states as b on a.state = b.id"
                . " inner join #__osrs_types as e on e.id = a.pro_type"
                . " where a.published = '1' and a.approved = '1' and b.published = '1' and e.published = '1' and a.isFeatured = '1' order by rand() limit 1";
            $db->setQuery($query);
            $property = $db->loadObject();
            if ($property->id > 0) {
                $db->setQuery("Select * from #__osrs_photos where pro_id = '$property->id'");
                $property->photos = $db->loadObjectList();
            }
        }
		
		$lists = array();
		
		$lists['country'] = HelperOspropertyCommon::makeCountryList('','country_id','onchange="change_country_state(this.value)"',Text::_('OS_SELECT_COUNTRY'),'','input-large');

		if(OSPHelper::userOneState()){
			$lists['state'] = "<input type='hidden' name='state_id' id='state_id' value='".OSPHelper::returnDefaultState()."'/>";
		}else{
			$lists['state'] = HelperOspropertyCommon::makeStateList('','','state_id','onchange="javascript:loadCity(this.value,\'\')"',Text::_('OS_SELECT_STATE'),'');
		}
		
		$default_state = 0;
		if(OSPHelper::userOneState()){
			$default_state = OSPHelper::returnDefaultState();
		}else{
			$default_state = 0;
		}
		$lists['city'] = HelperOspropertyCommon::loadCity($option,$default_state,0);

		//property types
		$db->setQuery("SELECT id as value,type_name$lang_suffix as text FROM #__osrs_types where published = '1' ORDER BY ordering");
		$typeArr = $db->loadObjectList();
		array_unshift($typeArr,HTMLHelper::_('select.option','',Text::_('OS_PROPERTY_TYPE')));
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'property_type','class="input-large form-select"','value','text');
		
		HTML_OspropertyDefault::defaultLayout($option,$property,$lists,$configs);
	}
	
	/**
	 * 
	 *
	 * @param unknown_type $option
	 */
	static  function defaultgetstate($option){
		global $mainframe,$lang_suffix,$jinput;
		$db = Factory::getDBO();
		$country_id = $jinput->getInt('country_id',0);
		$option_state = array();
		$option_state[]= HTMLHelper::_('select.option',0,' - '.Text::_('OS_ANY').' - ');
		
		if ($country_id){
			$db->setQuery("SELECT id AS value, state_name$lang_suffix AS text FROM #__osrs_states WHERE `country_id` = '$country_id' ORDER BY state_name$lang_suffix");		
			$states = $db->loadObjectList();
			if (count($states)){
				$option_state = array_merge($option_state,$states);
			}
			$disable = '';
		}else{
			$disable = 'disabled="disabled"';
		}
		
		echo HTMLHelper::_('select.genericlist',$option_state,'state_id','onChange="javascript:loadCity(this.value,0)" class="input-small" '.$disable,'value','text');
	}

    /**
     * MLS Search
     */
    static function mlsSearch($option){
        global $mainframe,$configClass;
        OSPHelper::generateHeading(1,$configClass['general_bussiness_name']." - ".Text::_('OS_MLS_SEARCH'));
        OSPHelper::generateHeading(2,Text::_('OS_MLS_SEARCH'));
        $language = Factory::getLanguage();
        $language = $language->getTag();
        $language = explode("-",$language);
        $lang_prefix = strtoupper($language[0]);
        ?>
        <iframe id="iframemlssearch" src="http://www.crmls.org/servlet/lDisplayListings?LA=<?php echo $lang_prefix;?>" marginwidth=""  frameborder="0" marginheight="0"></iframe>
    <?php
    }
}
?>