<?php

/*------------------------------------------------------------------------
# common.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
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
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Associations;

class HelperOspropertyCommon{
	/**
	 * Load Footer
	 *
	 * @param unknown_type $option
	 */
	static function loadFooter($option){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		$db = Factory::getDBO();
		if($configClass['show_footer']==1){
			if(file_exists(JPATH_ROOT."/components/com_osproperty/version.txt")){												
				$fh = fopen(JPATH_ROOT."/components/com_osproperty/version.txt","r");
				$version = fread($fh,filesize(JPATH_ROOT."/components/com_osproperty/version.txt"));
				@fclose($fh);
			}
			?>
			<div class="property_footer">
				<strong><a href="https://www.joomdonation.com/joomla-extensions/os-property-joomla-real-estate.html" target="_blank" title="The best Joomla Real Estate extension">OS Property Real Estate</a></strong> Version <?php echo $version;?>, Copyright (C) 2011 - <?php echo date('Y'); ?> <strong><a href="http://joomdonation.com" target="_blank" title="Joomla Extensions and Templates">Ossolution</a></strong>
			</div>
			<?php
		}
	}
	/**
	 * Get the country_id in the filter page or edit item details page
	 *
	 * @return unknown
	 */
	public static function getDefaultCountry()
    {
		global $configClass;
		$configClass = OSPHelper::loadConfig();
		if($configClass['show_country_id'] != "")
		{
			$countryArr = explode(",",$configClass['show_country_id']);
			if(count($countryArr) == 1)
			{
				return $countryArr[0];
			}
		}
		return 0;
	}

	/**
	 * Check default country
	 * @return boolean
	 * false : Use for one country
	 * true  : use for multiple countries
	 * 
	 */
	public static function checkCountry()
    {
		global $configClass;
		if($configClass['show_country_id'] != "")
		{
			$countryArr = explode(",",$configClass['show_country_id']);
			if(count($countryArr) == 1)
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Make the country list
	 *
	 * @param unknown_type $req_country_id
	 * @param unknown_type $name
	 * @param unknown_type $onChange
	 */
	public static function makeCountryList($req_country_id,$name,$onChange,$firstOption,$style,$class="input-medium form-control")
    {
		global $configClass;
		$db = Factory::getDbo();
		$languages = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable)
		{
			$lang_suffix = OSPHelper::getFieldSuffix();
		}
		else
		{
			$lang_suffix = "";
		}
		if($configClass['show_country_id'] != "")
		{
			if(HelperOspropertyCommon::checkCountry())
			{
				$db->setQuery("Select id as value, country_name".$lang_suffix." as text, country_name from #__osrs_countries where 1=1 and id in (".$configClass['show_country_id'].") order by country_name");
				$countries = $db->loadObjectList();
				if(count($countries))
				{
					foreach($countries as $country)
					{
						if($country->text == "")
						{
							$country->text = $country->country_name;
						}
					}
				}
				if($firstOption != "")
				{
					$countryArr[] = HTMLHelper::_('select.option','',$firstOption);
					$countryArr = array_merge($countryArr,$countries);
				}
				else
				{
					$countryArr = $countries;
				}
				return  HTMLHelper::_('select.genericlist',$countryArr,$name,'class="'.$class.' form-select" '.$onChange.' '.$style,'value','text',$req_country_id);
			}
			else
			{
				return "<input type='hidden' name='$name' value='".$configClass['show_country_id']."' id='$name'>";
			}
		}
		else
		{
			$db->setQuery("Select id as value, country_name".$lang_suffix." as text, country_name from #__osrs_countries where 1=1 order by country_name");
			$countries = $db->loadObjectList();
			if(count($countries))
			{
				foreach($countries as $country)
				{
					if($country->text != "")
					{
						$country->text = $country->country_name;
					}
				}
			}
			if($firstOption != ""){
				$countryArr[] = HTMLHelper::_('select.option','',$firstOption);
				$countryArr = array_merge($countryArr,$countries);
			}else{
				$countryArr = $countries;
			}
			return  HTMLHelper::_('select.genericlist',$countryArr,$name,'class="'.$class.' form-select" '.$onChange.' '.$style,'value','text',$req_country_id);
		}
	}

	/**
	 * Make the state list
	 *
	 * @param unknown_type $req_country_id
	 * @param unknown_type $req_state_id
	 * @param unknown_type $name
	 * @param unknown_type $onChange
	 * @param unknown_type $firstOption
	 * @return unknown
	 */
	public static function makeStateList($req_country_id,$req_state_id,$name,$onChange,$firstOption,$style,$class="input-medium form-select ilarge")
	{
		global $bootstrapHelper, $jinput, $configClass,$languages,$bootstrapHelper;
		$db = Factory::getDbo();
		$class = $bootstrapHelper->getClassMapping($class);
		$stateArr = array();
		$show_available_states_cities = $configClass['show_available_states_cities'];
		
		$lgs = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($lgs);
		$suffix = "";
		if($translatable){
			$suffix = OSPHelper::getFieldSuffix();
		}
		
		if(!HelperOspropertyCommon::checkCountry() || $req_country_id > 0)
		{

			$query  = "Select id as value,state_name".$suffix." as text, state_name from #__osrs_states where published = 1 ";
			if($req_country_id > 0){
				$query .= " and country_id = '$req_country_id'";
			}else{
				$query .= " and country_id = '".$configClass['show_country_id']."'";
			}
			if($show_available_states_cities == 1){
				$query .= " and id in (Select state from #__osrs_properties where approved = '1' and published = '1')";
			}
			$query .= " order by state_name" .$suffix ;
			$db->setQuery($query);
			$states = $db->loadObjectList();
			foreach($states as $state)
			{
				if($state->text == "")
				{
					$state->text = $state->state_name;
				}
			}
			if($firstOption != ""){
				$stateArr[] = HTMLHelper::_('select.option','',$firstOption);
				$stateArr   = array_merge($stateArr,$states);
			}else{
				$stateArr   = $states;
			}
			return HTMLHelper::_('select.genericlist',$stateArr,$name,'class="'.$class.' form-select" '.$onChange.' '.$style,'value','text',$req_state_id);

		}else{
			$stateArr[] = HTMLHelper::_('select.option','',$firstOption);
			return HTMLHelper::_('select.genericlist',$stateArr,$name,'class="'.$class.' form-select" disabled '.$style,'value','text');
		}
	}
	
	
	/**
	 * Load City
	 *
	 * @param unknown_type $option
	 * @param unknown_type $state_id
	 * @param unknown_type $city_id
	 * @return unknown
	 */
	public static function loadCity($option,$state_id,$city_id,$class="input-medium form-select", $name='city')
	{
		global $bootstrapHelper, $jinput, $mainframe,$configClass,$languages,$bootstrapHelper;
		$db = Factory::getDBO();
		$class = $bootstrapHelper->getClassMapping($class);
		$lgs = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($lgs);
		$suffix = "";
		if($translatable){
			$suffix = OSPHelper::getFieldSuffix();
		}
		
		$availSql = "";
		$show_available_states_cities = $configClass['show_available_states_cities'];
		$cityArr = array();
		$cityArr[]= HTMLHelper::_('select.option','',Text::_('OS_ALL_CITIES'));
		if($state_id > 0){
			if($show_available_states_cities == 1){
				$availSql = " and id in (Select city from #__osrs_properties where approved = '1' and published = '1')";
			}
			$db->setQuery("Select id as value, city".$suffix." as text, city from #__osrs_cities where  published = '1' $availSql and state_id = '$state_id' order by city");
			
			$cities = $db->loadObjectList();
			foreach($cities as $city)
			{
				if($city->text == "")
				{
					$city->text = $city->city;
				}
			}
			$cityArr   = array_merge($cityArr,$cities);
			$disabled  = "";
		}else{
			$disabled  = "disabled";
		}
		return HTMLHelper::_('select.genericlist',$cityArr,$name,'class="'.$class.' form-select" '.$disabled,'value','text',$city_id);
	}
	
	
	/**
	 * Make the state list
	 *
	 * @param unknown_type $req_country_id
	 * @param unknown_type $req_state_id
	 * @param unknown_type $name
	 * @param unknown_type $onChange
	 * @param unknown_type $firstOption
	 * @return unknown
	 */
	static function makeStateListAddProperty($req_country_id,$req_state_id,$name,$onChange,$firstOption,$style = 'class="input-medium form-select"'){
		global $bootstrapHelper, $jinput, $configClass,$languages;
		$db = Factory::getDbo();
		$lgs = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($lgs);
		$suffix = "";
		if($translatable){
			$suffix = OSPHelper::getFieldSuffix();
		}
		
		$stateArr = array();
		if((!HelperOspropertyCommon::checkCountry()) or ($req_country_id > 0)){

			$query  = "Select id as value,state_name".$suffix." as text, state_name from #__osrs_states where published = 1 ";
			if($req_country_id > 0){
				$query .= " and country_id = '$req_country_id'";
			}else{
				$query .= " and country_id = '".$configClass['show_country_id']."'";
			}
			$query .= " order by state_name";
			$db->setQuery($query);
			$states = $db->loadObjectList();
			foreach($states as $state)
			{
				if($state->text == "")
				{
					$state->text = $state->state_name;
				}
			}
			if($firstOption != ""){
				$stateArr[] = HTMLHelper::_('select.option','',$firstOption);
				$stateArr   = array_merge($stateArr,$states);
			}else{
				$stateArr   = $states;
			}
			return HTMLHelper::_('select.genericlist',$stateArr,$name,$onChange.' '.$style,'value','text',$req_state_id);

		}else{
			$stateArr[] = HTMLHelper::_('select.option','',$firstOption);
			return HTMLHelper::_('select.genericlist',$stateArr,$name,'disabled'.' '.$style,'value','text');
		}
	}
	
	
	/**
	 * Load City
	 *
	 * @param unknown_type $option
	 * @param unknown_type $state_id
	 * @param unknown_type $city_id
	 * @return unknown
	 */
	static function loadCityAddProperty($option,$state_id,$city_id,$class="input-medium form-control"){
		global $bootstrapHelper, $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		
		$lgs = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($lgs);
		$suffix = "";
		if($translatable){
			$suffix = OSPHelper::getFieldSuffix();
		}
		
		$cityArr = array();
		$cityArr[]= HTMLHelper::_('select.option','',Text::_('OS_ALL_CITIES'));
		if($state_id > 0){
			$db->setQuery("Select id as value, city".$suffix." as text, city from #__osrs_cities  where  published = '1' and state_id = '$state_id' order by city");
			$cities = $db->loadObjectList();
			foreach($cities as $city)
			{
				if($city->text == "")
				{
					$city->text = $city->city;
				}
			}
			$cityArr   = array_merge($cityArr,$cities);
			$disabled  = "";
		}else{
			$disabled  = "disabled";
		}
		return HTMLHelper::_('select.genericlist',$cityArr,'city','class="'.$class.' form-select" '.$disabled,'value','text',$city_id);
	}


	static function loadCityName($city){
		global $bootstrapHelper, $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		$lgs = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($lgs);
		if($translatable){
			$suffix = OSPHelper::getFieldSuffix();
			$db->setQuery("Select city".$suffix." from #__osrs_cities where id = '$city'");
			$city_name = $db->loadResult();
		}else{
			$db->setQuery("Select city from #__osrs_cities where id = '$city'");
			$city_name = $db->loadResult();
		}
		return $city_name;
	}

	static function loadStateName($state){
		global $bootstrapHelper, $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		$lgs = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($lgs);
		if($translatable){
			$suffix = OSPHelper::getFieldSuffix();
			$db->setQuery("Select state_name".$suffix." from #__osrs_states where id = '$state'");
			$state_name = $db->loadResult();
		}else{
			$db->setQuery("Select state_name from #__osrs_states where id = '$state'");
			$state_name = $db->loadResult();
		}
		return $state_name;
	}
	
	/**
	 * Check access permission
	 *
	 * @param unknown_type $access
	 */
	static function checkAccessPersmission($access){
		global $bootstrapHelper, $jinput, $mainframe,$_jversion;
		$db = Factory::getDBO();
		$user = Factory::getUser();
		if($access == 0){ //public
			return true;
		}elseif($access == 1){ //registered
			if(intval($user->id) == 0){
				return false;
			}else{
				return true;
			}
		}elseif($access == 2){ //special
			if(intval($user->id) == 0){
				return false;
			}else{
				$db->setQuery("Select group_id from #__user_usergroup_map where user_id = '$user->id'");
				$group_id = $db->loadResult();
				if(($group_id >=3) and ($group_id <=8)){
					return true;
				}else{
					return false;
				}
			}
		}
	}


	/**
	 * Check to see if user is Agent
	 *
	 * @param unknown_type $option
	 * @return unknown
	 */
	public static function isAgent($agent_id = 0){
		global $bootstrapHelper, $jinput, $mainframe;
		$user = Factory::getUser();
		$db = Factory::getDBO();
		if($agent_id == 0){
			$agent_id = $user->id;
		}
		if(intval($agent_id) == 0){
			return false;
		}else{
			$db->setQuery("Select count(id) from #__osrs_agents where user_id = '$agent_id' and published = '1'");
			$count = $db->loadResult();
			if($count > 0){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * Check to see if user is Agent
	 *
	 * @param unknown_type $option
	 * @return unknown
	 */
	static function isRegisteredAgent($agent_id = 0){
		global $bootstrapHelper, $jinput, $mainframe;
		$user = Factory::getUser();
		$db = Factory::getDBO();
		if($agent_id == 0)
		{
			$agent_id = $user->id;
		}
		if(intval($agent_id) == 0)
		{
			return false;
		}
		else
		{
			$db->setQuery("Select count(id) from #__osrs_agents where user_id = '$agent_id'");
			$count = $db->loadResult();
			if($count > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Get Agent ID
	 *
	 * @return unknown
	 */
	static function getAgentID($agent_id = 0){
		global $bootstrapHelper, $jinput, $mainframe;
		static $userid,$agent_id;
		$user = Factory::getUser();
		if((int)$userid == 0 || $userid != $user->id){
            if($agent_id == 0){
                $agent_id   = $user->id;
            }
            $db             = Factory::getDBO();
            $db->setQuery("Select id from #__osrs_agents where user_id = '$agent_id'");
            $agent_id       = $db->loadResult();
            $userid         = $user->id;
        }
        return (int)$agent_id;
	}

	/**
	 * Check to see if user is admin of current company
	 *
	 */
	static function isCompanyAdmin($company_id = 0){
		global $bootstrapHelper, $jinput, $mainframe;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		if($company_id == 0){
			$company_id = $user->id;
		}
		if(intval($company_id) == 0){
			return false;
		}else{
			$db->setQuery("Select count(id) from #__osrs_companies where user_id = '$company_id' and published = '1'");
			$count = $db->loadResult();
			if($count == 0){
				return false;
			}else{
				return  true;
			}
		}
	}

	/**
	 * Check to see if user is admin of current company
	 *
	 */
	static function isRegisteredCompanyAdmin($company_id = 0){
		global $bootstrapHelper, $jinput, $mainframe;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		if($company_id == 0){
			$company_id = $user->id;
		}
		if(intval($company_id) == 0){
			return false;
		}else{
			$db->setQuery("Select count(id) from #__osrs_companies where user_id = '$company_id'");
			$count = $db->loadResult();
			if($count == 0){
				return false;
			}else{
				return  true;
			}
		}
	}
	
	public static function getCompanyId($company_id = 0){
		global $bootstrapHelper, $jinput, $mainframe;
		static $userid,$company_id;

		$db = Factory::getDbo();
		$user = Factory::getUser();
		if((int)$userid == 0 || $userid != $user->id){
            if($company_id == 0){
                $company_id = $user->id;
            }
            if(intval($company_id) == 0){
                return 0;
            }else{
                $db->setQuery("Select id from #__osrs_companies where user_id = '$company_id'");
                $company_id = $db->loadResult();
                $userid  = $user->id;
            }
        }
        return (int)$company_id;
	}

	/**
	 * remove white space in begin and end of the option in one array
	 *
	 * @param unknown_type $a
	 */
	static function stripSpaceArrayOptions($a){
		global $bootstrapHelper, $jinput, $mainframe;
		if(count($a) > 0){
			for($i=0;$i<count($a);$i++){
				$a[$i] = trim($a[$i]);
			}
		}
		return $a;
	}

	/**
	 * Check to see if this agent is already use the coupon id
	 *
	 * @param unknown_type $coupon_id
	 */
	static function isAlreadyUsed($coupon_id){
		global $bootstrapHelper, $jinput, $mainframe;
		$db = Factory::getDBO();
		$user = Factory::getUser();
		if(! HelperOspropertyCommon::isAgent()){
			return true;
		}else{
			$db->setQuery("Select id from #__osrs_agents where user_id = '$user->id' and published = '1'");
			$agent_id = $db->loadResult();
			$db->setQuery("Select count(id) from #__osrs_user_coupons where agent_id = '$agent_id' and coupon_id = '$coupon_id'");
			$count = $db->loadResult();
			if($count == 0){
				return false;
			}else{
				return true;
			}
		}
		return false;
	}

	/**
	 * Check to see whether if this is user
	 *
	 * @param unknown_type $option
	 */
	static function isUser(){
		global $bootstrapHelper, $jinput, $mainframe;
		$user = Factory::getUser();
		if(intval($user->id) == 0){
			return false;
		}else{
			return true;
		}
	}


	/**
	 * Load Time depend on configuration 
	 *
	 * @param unknown_type $time
	 * @param unknown_type $input_format
	 * @return unknown
	 */
	static function loadTime($time,$input_format){
		$db = Factory::getDbo();
		$db->setQuery("Select fieldvalue from #__osrs_configuration where id = '37'");
		$time_format = $db->loadResult();
		$time_format = str_replace("%","",$time_format);
		if($input_format == 1){
			return date($time_format,$time);
		}else{
			$time = strtotime($time);
			return date($time_format,$time);
		}
	}


	/**
	 * Remove photo
	 * photo_type = 1 : Property
	 * photo_type = 2 : Agent
	 *
	 */
	static function removePhoto($id,$photo_type){
		global $bootstrapHelper, $jinput, $mainframe;
		$db = Factory::getDbo();
		switch ($photo_type) {
			case "1":
				$db->setQuery("Select image from #__osrs_photos where id = '$id'");
				$image = $db->loadResult();
				$db->setQuery("Select pro_id from #__osrs_photos where id = '$id'");
				$pro_id = $db->loadResult();
				@unlink(JPATH_ROOT."/images/osproperty/properties".DS.$pro_id.DS.$image);
				@unlink(JPATH_ROOT."/images/osproperty/properties".DS.$pro_id."/thumb".DS.$image);
				@unlink(JPATH_ROOT."/images/osproperty/properties".DS.$pro_id."/medium".DS.$image);
				$db->setQuery("Delete from #__osrs_photos where id = '$id'");
				$db->execute();
				break;
			case "2":
				$db->setQuery("Select photo from #__osrs_agents where id = '$id'");
				$image = $db->loadResult();
				@unlink(JPATH_ROOT."/images/osproperty/agent".DS.$image);
				@unlink(JPATH_ROOT."/images/osproperty/agent/thumbnail".DS.$image);
				break;
		}
	}


	/**
	 * alphabet List
	 *
	 * @param unknown_type $option
	 * @param unknown_type $formname
	 */
	public static function alphabetList($option,$alphabet,$formname){
		global $bootstrapHelper, $jinput, $mainframe;
		?>
		<script type="text/javascript">
		function submitAlphabetForm(a){
			var form = document.getElementById("<?php echo $formname?>");
			if(form != null){
				form.alphabet.value = a;
				form.submit();
			}
		}
		</script>
		<div id="characters_line" class="characters_line">
			<?php
			$class1 = "character";
			$class2 = "character";
			$class3 = "character";
			$class4 = "character";
			$class5 = "character";
			$class6 = "character";
			$class7 = "character";
			$class8 = "character";
			$class9 = "character";
			$class10 = "character";
			$class11= "character";
			$class12 = "character";
			$class13 = "character";
			$class14 = "character";
			$class15 = "character";
			$class16 = "character";
			$class17 = "character";
			$class18 = "character";
			$class19 = "character";
			$class20 = "character";
			$class21 = "character";
			$class22 = "character";
			$class23 = "character";
			$class24 = "character";
			$class25 = "character";
			$class26 = "character";
			$class27 = "character";

			switch ($alphabet){
				case "0-9":
					$class1 = "character_selected";
					break;
				case "A":
					$class2 = "character_selected";
					break;
				case "B":
					$class3 = "character_selected";
					break;
				case "C":
					$class4 = "character_selected";
					break;
				case "D":
					$class5 = "character_selected";
					break;
				case "E":
					$class6 = "character_selected";
					break;
				case "F":
					$class7 = "character_selected";
					break;
				case "G":
					$class8 = "character_selected";
					break;
				case "H":
					$class9 = "character_selected";
					break;
				case "I":
					$class10 = "character_selected";
					break;
				case "J":
					$class11 = "character_selected";
					break;
				case "K":
					$class12 = "character_selected";
					break;
				case "L":
					$class13 = "character_selected";
					break;
				case "M":
					$class14 = "character_selected";
					break;
				case "N":
					$class15 = "character_selected";
					break;
				case "O":
					$class16 = "character_selected";
					break;
				case "P":
					$class17 = "character_selected";
					break;
				case "Q":
					$class18 = "character_selected";
					break;
				case "R":
					$class19 = "character_selected";
					break;
				case "S":
					$class20 = "character_selected";
					break;
				case "T":
					$class21 = "character_selected";
					break;
				case "U":
					$class22 = "character_selected";
					break;
				case "V":
					$class23 = "character_selected";
					break;
				case "W":
					$class24 = "character_selected";
					break;
				case "X":
					$class25 = "character_selected";
					break;
				case "Y":
					$class26 = "character_selected";
					break;
				case "Z":
					$class27 = "character_selected";
					break;

			}
			?>
			<a href="javascript:submitAlphabetForm('0-9')" class="<?php echo $class1?>">0-9</a>
			<a href="javascript:submitAlphabetForm('A')" class="<?php echo $class2?>">A</a>
			<a href="javascript:submitAlphabetForm('B')" class="<?php echo $class3?>">B</a>
			<a href="javascript:submitAlphabetForm('C')" class="<?php echo $class4?>">C</a>
			<a href="javascript:submitAlphabetForm('D')" class="<?php echo $class5?>">D</a>
			<a href="javascript:submitAlphabetForm('E')" class="<?php echo $class6?>">E</a>
	
			<a href="javascript:submitAlphabetForm('F')" class="<?php echo $class7?>">F</a>
			<a href="javascript:submitAlphabetForm('G')" class="<?php echo $class8?>">G</a>
			<a href="javascript:submitAlphabetForm('H')" class="<?php echo $class9?>">H</a>
			<a href="javascript:submitAlphabetForm('I')" class="<?php echo $class10?>">I</a>
			<a href="javascript:submitAlphabetForm('J')" class="<?php echo $class11?>">J</a>
			<a href="javascript:submitAlphabetForm('K')" class="<?php echo $class12?>">K</a>
	
			<a href="javascript:submitAlphabetForm('L')" class="<?php echo $class13?>">L</a>
			<a href="javascript:submitAlphabetForm('M')" class="<?php echo $class14?>">M</a>
			<a href="javascript:submitAlphabetForm('N')" class="<?php echo $class15?>">N</a>
			<a href="javascript:submitAlphabetForm('O')" class="<?php echo $class16?>">O</a>
			<a href="javascript:submitAlphabetForm('P')" class="<?php echo $class17?>">P</a>
			<a href="javascript:submitAlphabetForm('Q')" class="<?php echo $class18?>">Q</a>
	
			<a href="javascript:submitAlphabetForm('R')" class="<?php echo $class19?>">R</a>
			<a href="javascript:submitAlphabetForm('S')" class="<?php echo $class20?>">S</a>
			<a href="javascript:submitAlphabetForm('T')" class="<?php echo $class21?>">T</a>
			<a href="javascript:submitAlphabetForm('U')" class="<?php echo $class22?>">U</a>
			<a href="javascript:submitAlphabetForm('V')" class="<?php echo $class23?>">V</a>
			<a href="javascript:submitAlphabetForm('W')" class="<?php echo $class24?>">W</a>
	
			<a href="javascript:submitAlphabetForm('X')" class="<?php echo $class25?>">X</a>
			<a href="javascript:submitAlphabetForm('Y')" class="<?php echo $class26?>">Y</a>
			<a href="javascript:submitAlphabetForm('Z')" class="<?php echo $class27?>">Z</a>
		</div>
	
		<!-- dealers list -->
		<!-- dealers list end -->
		<?php

	}


	/**
	 * Contact & Comment form
	 *
	 * @param unknown_type $option
	 */
	static function contactForm($formname, $site_name, $name)
	{
		global $bootstrapHelper, $jinput, $mainframe,$ismobile;
		$user = Factory::getUser();
		//Random string
		$randomStr = md5(microtime());// md5 to generate the random string
		$resultStr = substr($randomStr,0,5);//trim 5 digit
		?>
		<script type="text/javascript">
		 function submitForm(form_id){
			var form = document.getElementById(form_id);
			var temp1,temp2;
			var cansubmit = 1;
			var require_field = form.require_field;
			require_field = require_field.value;
			var require_label = form.require_label;
			require_label = require_label.value;
			var require_fieldArr = require_field.split(",");
			var require_labelArr = require_label.split(",");
			for(i=0;i<require_fieldArr.length;i++){
				temp1 = require_fieldArr[i];
				temp2 = document.getElementById(temp1);
				if(temp2 != null){
					if(temp2.value == ""){
						alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY_FIELD')?>");
						temp2.focus();
						cansubmit = 0;
						return false;
					}else if(temp1 == "comment_security_code"){
						var captcha_str = document.getElementById('captcha_str');
						captcha_str = captcha_str.value;
						if(captcha_str != temp2.value){
							alert(" <?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
							temp2.focus();
							cansubmit = 0;
							return false;
						}
					}
				}
			}
			if(cansubmit == 1)
			{
				form.submit();
			}
		}
		</script>
		<?php
		//Random string
		$RandomStr = md5(microtime());// md5 to generate the random string
		$ResultStr = substr($RandomStr,0,5);//trim 5 digit

		if(!OSPHelper::isJoomla4())
		{
			$startDiv = '<div class="'.$bootstrapHelper->getClassMapping('controls').'">';
			$endDiv   = '</div>';
		}
		?>

		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="contactForm">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                <div class="headercontactform">
                    <?php echo Text::_('OS_CONTACT');?> <?php echo $name;?>
                </div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<?php echo $startDiv; ?>
						<input type="text" id="comment_author" name="comment_author<?php echo date("j",time());?>" maxlength="50" class="input-large form-control" value="<?php echo $user->name; ?>" placeholder="<?php echo Text::_('OS_YOUR_NAME');?>"/>
					<?php echo $endDiv; ?>
				</div>

				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<?php echo $startDiv; ?>
						<input type="text" id="comment_email" name="comment_email<?php echo date("j",time());?>" maxlength="50" class="input-large form-control" value="<?php echo $user->email; ?>" placeholder="<?php echo Text::_('OS_EMAIL');?>"/>
					<?php echo $endDiv; ?>
				</div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<?php echo $startDiv; ?>
						<textarea id="message" rows="2" cols="50" class="input-large form-control" name="message"><?php printf(Text::_('OS_PREDEFINED_CONTACT_MESSAGE'), $name, $site_name);?></textarea>
					<?php echo $endDiv; ?>
				</div>
				<?php
				$captchaPlugin = Factory::getApplication()->getParams()->get('captcha', Factory::getConfig()->get('captcha'));
				if ($captchaPlugin)
				{
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<?php
					echo Captcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
					?>
					</div>
					<?php
				}	
				?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<a href="#" onclick="javascript:submitForm('<?php echo $formname?>')" class="submitcontactform" ><?php echo Text::_("OS_SEND_MESSAGE")?></a>
				</div>
			</div>
		</div>
		<input type="hidden" name="require_field" value="comment_author,comment_email,comment_title,comment_message" />
		<input type="hidden" name="require_label" value="<?php echo Text::_('OS_AUTHOR');?>,<?php echo Text::_('OS_AUTHOR_EMAIL')?>,<?php echo Text::_('OS_TITLE');?>,<?php echo Text::_('Message');?>,<?php echo Text::_('OS_MESSAGE');?>" />
		<input type="hidden" name="return" id="return" value="<?php echo OSPHelper::currentUrl(); ?>" />
<?php
	}

	 static function getDeliciousButton( $title, $link ) {
		$img_url = Uri::base()."/media/com_osproperty/assets/images/socials/delicious.png";
		return '<a href="http://del.icio.us/post?url=' . rawurlencode($link) . '&amp;title=' . rawurlencode( $title ) . '" title="Submit ' . $title . ' in Delicious" target="blank" >
		<img src="' . $img_url . '" alt="Submit ' . $title . ' in Delicious" />
		</a>' ;	
	}
	 static function getDiggButton( $title, $link ) {
		$img_url = Uri::base()."/media/com_osproperty/assets/images/socials/digg.png";
		return '<a href="http://digg.com/submit?url=' . rawurlencode($link) . '&amp;title=' . rawurlencode( $title ) . '" title="Submit ' . $title . ' in Digg" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in Digg" />
        </a>' ;   
	}
	 static function getFacebookButton( $title, $link ) {
		$img_url = Uri::base()."/media/com_osproperty/assets/images/socials/facebook.png";
		return '<a href="http://www.facebook.com/sharer.php?u=' . rawurlencode($link) . '" title="Submit ' . $title . ' in FaceBook" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in FaceBook" />
        </a>' ;    
	}
	static  function getGoogleButton( $title, $link ) {
		$img_url = Uri::base()."/media/com_osproperty/assets/images/socials/google.png";
		return '<a href="http://www.google.com/bookmarks/mark?op=edit&bkmk=' . rawurlencode($link) . '" title="Submit ' . $title . ' in Google Bookmarks" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in Google Bookmarks" />
        </a>' ;    
	}
	 static function getStumbleuponButton( $title, $link ) {
		$img_url = Uri::base()."/media/com_osproperty/assets/images/socials/stumbleupon.png";
		return '<a href="http://www.stumbleupon.com/submit?url=' . rawurlencode($link) . '&amp;title=' . rawurlencode( $title ) . '" title="Submit ' . $title . ' in Stumbleupon" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in Stumbleupon" />
        </a>' ;    
	}
	static  function getTechnoratiButton( $title, $link ) {
		$img_url = Uri::base()."/media/com_osproperty/assets/images/socials/technorati.png";
		return '<a href="http://technorati.com/faves?add=' . rawurlencode($link) . '" title="Submit ' . $title . ' in Technorati" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in Technorati" />
        </a>' ;
	}
	 static function getTwitterButton( $title, $link ) {
		$img_url = Uri::base()."/media/com_osproperty/assets/images/socials/twitter.png";
		return '<a href="http://twitter.com/?status=' . rawurlencode( $title ." ". $link ) . '" title="Submit ' . $title . ' in Twitter" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in Twitter" />
        </a>' ;    
	}

	/**
     * Download pdf file
     *
     * @param unknown_type $filelink
     */
	static function downloadfile($filelink){
		while (@ob_end_clean());
		define('ALLOWED_REFERRER', '');
		// MUST end with slash (i.e. "/" )
		define('BASE_DIR',JPATH_ROOT."/tmp");

		// log downloads? true/false
		define('LOG_DOWNLOADS',false);

		// log file name
		define('LOG_FILE','downloads.log');

		// Allowed extensions list in format 'extension' => 'mime type'
		// If myme type is set to empty string then script will try to detect mime type
		// itself, which would only work if you have Mimetype or Fileinfo extensions
		// installed on server.
		$allowed_ext = array (
		// archives
		'zip' => 'application/zip',
		// documents
		'pdf' => 'application/pdf',
		'doc' => 'application/msword',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		// executables
		'exe' => 'application/octet-stream',
		// images
		'gif' => 'image/gif',
		'png' => 'image/png',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		// audio
		'mp3' => 'audio/mpeg',
		'wav' => 'audio/x-wav',
		// video
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpe' => 'video/mpeg',
		'mov' => 'video/quicktime',
		'avi' => 'video/x-msvideo'
		);

		################################################## ##################
		### DO NOT CHANGE BELOW
		################################################## ##################

		// If hotlinking not allowed then make hackers think there are some server problems
		if (ALLOWED_REFERRER !== ''
		&& (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']),strtoupper(ALLOWED_REFERRER)) === false)
		) {
			die(Text::_("Internal server error. Please contact system administrator."));
		}

		// Make sure program execution doesn't time out
		// Set maximum script execution time in seconds (0 means no limit)
		//set_time_limit(0);

		if (!isset($filelink)) {
			die(Text::_("Please specify file name for download."));
		}

		// Get real file name.
		// Remove any path info to avoid hacking by adding relative path, etc.
		$fname = basename($filelink);

		// Check if the file exists
		// Check in subfolders too
		function find_file ($dirname, $fname, &$file_path) {
			$dir = opendir($dirname);
			while ($file = readdir($dir)) {
				if (empty($file_path) && $file != '.' && $file != '..') {
					if (is_dir($dirname.'/'.$file)) {
						find_file($dirname.'/'.$file, $fname, $file_path);
					}
					else {
						if (file_exists($dirname.'/'.$fname)) {
							$file_path = $dirname.'/'.$fname;
							return;
						}
					}
				}
			}//end while

		} // find_file

		// get full file path (including subfolders)
		$file_path = '';
		find_file(BASE_DIR, $fname, $file_path);

		if (!is_file($file_path)) {
			die(Text::_("File does not exist. Make sure you specified correct file name."));
		}

		// file size in bytes
		$fsize = filesize($file_path);

		// file extension
		$fext = strtolower(substr(strrchr($fname,"."),1));

		// check if allowed extension
		if (!array_key_exists($fext, $allowed_ext)) {
			die(Text::_("Not allowed file type."));
		}

		// get mime type
		if ($allowed_ext[$fext] == '') {
			$mtype = '';
			// mime type is not set, get from server settings
			if ( function_exists('mime_content_type')) {
				$mtype = mime_content_type($file_path);
			}
			else if ( function_exists('finfo_file')) {
				$finfo = finfo_open(FILEINFO_MIME); // return mime type
				$mtype = finfo_file($finfo, $file_path);
				finfo_close($finfo);
			}
			if ($mtype == '') {
				$mtype = "application/force-download";
			}
		}
		else {
			// get mime type defined by admin
			$mtype = $allowed_ext[$fext];
		}

		// Browser will try to save file with this filename, regardless original filename.
		// You can override it if needed.


		$asfname = $fname;

		// set headers
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Type: $mtype");
		header("Content-Disposition: attachment; filename=\"$asfname\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . $fsize);


		if( ! ini_get('safe_mode') ) { // set_time_limit doesn't work in safe mode
			@set_time_limit(0);
		}

		HelperOspropertyCommon::readfile_chunked($file_path);
		exit();
	}

	static function downloadfile1($file_path,$id){
		while (@ob_end_clean());
		$len = @ filesize($file_path);
		$cont_dis ='attachment';

		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression'))  {
			ini_set('zlib.output_compression', 'Off');
		}

		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");

		header("Content-Transfer-Encoding: binary");
		header('Content-Disposition:' . $cont_dis .';'
		. ' filename="property' .$id . '.pdf";'
		. ' size=' . $len .';'
		); //RFC2183
		header("Content-Type: application/pdf");			// MIME type
		header("Content-Length: "  . $len);

		if( ! ini_get('safe_mode') ) { // set_time_limit doesn't work in safe mode
			@set_time_limit(0);
		}
		HelperOspropertyCommon::readfile_chunked($file_path);
		exit();
	}


	static function downloadfile2($file_path,$id){
		while (@ob_end_clean());
		$len = @ filesize($file_path);
		$cont_dis ='attachment';

		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression'))  {
			ini_set('zlib.output_compression', 'Off');
		}

		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");

		header("Content-Transfer-Encoding: binary");
		header('Content-Disposition:' . $cont_dis .';'
		. ' filename="csv' .$id . '.csv";'
		. ' size=' . $len .';'
		); //RFC2183
		header("Content-Length: "  . $len);

		if( ! ini_get('safe_mode') ) { // set_time_limit doesn't work in safe mode
			@set_time_limit(0);
		}
		HelperOspropertyCommon::readfile_chunked($file_path);
		exit();
	}

	static function readfile_chunked($filename,$retbytes=true){
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			@ob_flush();
			flush();
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}

	/**
	 * Load Approval information
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	static function loadApprovalInfo($id){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();
		$db->setQuery("Select * from #__osrs_expired where pid = '$id'");
		$expired = $db->loadObject();
		$current_time = self::getRealTime();
		$expired_time = strtotime($expired->expired_time);
		$html =  "".Text::_('OS_STATUS').": <strong>";
		if($configClass['general_use_expiration_management'] == 1){ //allow to update expired ?
			if($expired_time < $current_time){
				OspropertyListing::unApproved($id);
			}
		}
		if(($expired_time < $current_time) or ($property->approved == 0)) {

			$html .= "<span color='red'>".Text::_('Unapproved')."</span>";
			if($property->request_to_approval == 1){
				$html .= "<BR><span color='blue' class='fontsmall'><i>(";
				$html .= Text::_('OS_REQUEST_APPROVAL');
				$html .= ")</i></span>";
			}
			$html .= "</strong>";
		}else{
			$html .= "<span color='green'>".Text::_('OS_APPROVED')."</span>";
			$html .= "</strong>";
			if($configClass['general_use_expiration_management'] == 1){ //allow to update expired ?
				$html .= "<BR>";
				$html .= Text::_('OS_EXPIRED_ON').": ";
				$html .= HelperOspropertyCommon::loadTime($expired->expired_time,2);
			}
		}

		return $html;
	}

	static function loadFeatureInfo($id){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();
		$db->setQuery("Select * from #__osrs_expired where pid = '$id'");
		$expired = $db->loadObject();
		$current_time = self::getRealTime();
		$expired_time = strtotime($expired->expired_feature_time);
		$html =  "".Text::_('OS_STATUS').": <strong>";
		if($configClass['general_use_expiration_management'] == 1){ //allow to update expired ?
			if($expired_time < $current_time){
				OspropertyListing::unFeatured($id);
			}
		}
		if(($expired_time < $current_time) or ($property->isFeatured == 0)) {

			$html .= "<span color='red'>".Text::_('OS_IS_NOT_FEATURED')."</span>";
			$html .= "</strong>";
		}else{
			$html .= "<span color='green'>".Text::_('OS_FEATURED')."</span>";
			$html .= "</strong>";
			if($configClass['general_use_expiration_management'] == 1){ //allow to update expired ?
				$html .= "<BR>";
				$html .= Text::_('OS_EXPIRED_ON').": ";
				$html .= HelperOspropertyCommon::loadTime($expired->expired_feature_time,2);
			}
		}
		return $html;
	}

	/**
	 * Build toolbar
	 *
	 * @param unknown_type $view
	 * @param unknown_type $extras
	 * @return unknown
	 */
	public static function buildToolbar($view = '') {
		global $bootstrapHelper, $jinput, $mainframe,$configClass,$ismobile;
	}

	static function loadNeighborHood1($pid){
		global $bootstrapHelper;
		$db = Factory::getDbo();
		$query = "Select a.*,b.neighborhood from #__osrs_neighborhood as a"
				." inner join #__osrs_neighborhoodname as b on b.id = a.neighbor_id"
				." where a.pid = '$pid'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
					<?php
					for($i=0;$i<count($rows);$i++){
						$row = $rows[$i];
						?>
						<strong><?php echo Text::_($row->neighborhood)?></strong>&nbsp; 
						<?php 
						if($row->distance > 0)
						{
							echo OSPHelper::showBath((float)$row->distance). ' km';
						}
						else
						{
							echo $row->mins > 0 ?$row->mins. " ".Text::_('OS_MINS')." " : " ";?> <?php echo Text::_('OS_BY')?> &nbsp;
							<?php
							switch ($row->traffic_type){
								case "1":
									echo Text::_('OS_WALK');
								break;
								case "2":
									echo Text::_('OS_CAR');
								break;
								case "3":
									echo Text::_('OS_TRAIN');
								break;
							}
						}
						echo ",  ";
						?>
					<?php
					}
					?>
				</div>
			</div>
			<?php
		}
	}

	static function loadNeighborHood2($pid)
	{
		global $bootstrapHelper;
		$db = Factory::getDbo();
		$query = "Select a.*,b.neighborhood from #__osrs_neighborhood as a"
		." inner join #__osrs_neighborhoodname as b on b.id = a.neighbor_id"
		." where a.pid = '$pid'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$k =0;
		if(count($rows) > 0)
		{
			for($i=0;$i<count($rows);$i++)
			{
				$k++;
				$row = $rows[$i];
				echo "<div class='".$bootstrapHelper->getClassMapping('span3')." neighborhooditem'>";

					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
						<?php
						if($row->distance == 0)
						{
						?>
						<div class="neightborhoodicon">
							<?php
							switch ($row->traffic_type)
							{
								default:
								case "1":
									?>
									<img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/walking.png" />
									<?php
								break;
								case "2":
									?>
									<img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/car.png" />
									<?php
								break;
								case "3":
									?>
									<img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/train.png" />
									<?php
								break;
							}
							?>
						</div>
						<?php
						}		
						?>
						<div class="neightborhoodvalue">
							<?php
							echo "<strong>".Text::_($row->neighborhood)."</strong> ";
							?>
							<BR />
							<?php
							if($row->distance > 0)
							{
								echo OSPHelper::showBath((float)$row->distance). ' km';
							}
							else
							{
								echo $row->mins > 0? $row->mins." ".Text::_('OS_MINS')." " : " ";
							}
							?>
						</div>
					</div>
				<?php
				echo "</div>";
				if($k == 4)
				{
					$k = 0;
					echo "</div><div class='".$bootstrapHelper->getClassMapping('row-fluid')."'>";
				}
			}
		}
	}

	static function loadNeighborHood($pid){
		global $bootstrapHelper;
		$db = Factory::getDbo();
		$query = "Select a.*,b.neighborhood from #__osrs_neighborhood as a"
		." inner join #__osrs_neighborhoodname as b on b.id = a.neighbor_id"
		." where a.pid = '$pid'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$k =0;
		if(count($rows) > 0)
		{
			for($i=0;$i<count($rows);$i++)
			{
				$k++;
				$row = $rows[$i];
				echo "<div class='".$bootstrapHelper->getClassMapping('span6')."'><strong>".Text::_($row->neighborhood)."</strong> ";
				if($row->distance > 0)
				{
					echo OSPHelper::showBath((float)$row->distance). ' km';
				}
				else
				{
					echo $row->mins > 0? $row->mins." ".Text::_('OS_MINS')." " : " ";
					echo Text::_('OS_BY')." ";
					switch ($row->traffic_type){
						case "1":
							echo Text::_('OS_WALK');
						break;
						case "2":
							echo Text::_('OS_CAR');
						break;
						case "3":
							echo Text::_('OS_TRAIN');
						break;
					}
				}
				echo "</div>";
				if($k == 2){
					$k = 0;
					echo "</div><div class='".$bootstrapHelper->getClassMapping('row-fluid')."'>";
				}
			}
		}
	}

	static function checkSpecial(){
		global $bootstrapHelper, $jinput, $mainframe;;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		$specialArr = array("Super Users","Super Administrator","Administrator","Manager");
        $db->setQuery("Select b.title from #__user_usergroup_map as a inner join #__usergroups as b on b.id = a.group_id where a.user_id = '$user->id'");
        $usertype = $db->loadResult();
        if(in_array($usertype,$specialArr)){
            return true;
        }else{
            return false;
        }
	}



	/**
	 * Show the currency Select list
	 *
	 * @param unknown_type $curr
	 */
	static function showCurrencySelectList($curr)
    {
		global $configClass;
		$db = Factory::getDbo();
		$db->setQuery("Select id as value, concat(currency_name,' - ',currency_code,' - ',currency_symbol) as text from #__osrs_currencies where published = '1' order by currency_name");
		$currencies = $db->loadObjectList();
		if(intval($curr) == 0){
			$curr = $configClass['general_currency_default'];
		}
		echo HTMLHelper::_('select.genericlist',$currencies,'curr','class="input-large chosen form-select"','value','text',$curr);
	}

	/**
	 * Load currency
	 *
	 * @param unknown_type $curr
	 */
	public static function loadCurrency($curr = 0)
    {
		static $currency, $currency_symbol;
		global $configClass;
		$configClass = OSPHelper::loadConfig();
		$db = Factory::getDBO();
		if(intval($curr) == 0){
			$curr = $configClass['general_currency_default'];
		}
		if((!$currency) || ($currency != $curr))
		{
			$currency = $curr;
			$db = Factory::getDbo();
			$db->setQuery("Select currency_symbol from #__osrs_currencies where id = '$curr'");
			$curr = $db->loadResult();
			$curr = str_replace("\r","",$curr);
			$curr = str_replace("\n","",$curr);
			$currency_symbol = $curr;
		}
		
		return $currency_symbol;
	}

	public static function loadDefaultCurrency($symbol){
		global $configClass;
		static $default_currency;
		$configClass = OSPHelper::loadConfig();
		$db = Factory::getDbo();
		$curr = $configClass['general_currency_default'];
		
		if($default_currency == ""){
			$db->setQuery("Select currency_code from #__osrs_currencies where id = '$curr'");
			$default_currency = $db->loadResult();
		}
		
		return $default_currency;
	}

	static function checkMembershipIsAvailable(){
		global $bootstrapHelper, $jinput, $configClass;
		$user = Factory::getUser();
		$db = Factory::getDbo();
		include_once(JPATH_ROOT."/components/com_osmembership/helper/helper.php");
		$available_plan_of_agent = OSMembershipHelper::getActiveMembershipPlans($user->id);
		if(count($available_plan_of_agent) == 1){
			return false;
		}else{
			if(self::isAgent()){
				$db->setQuery("Select id from #__osrs_agents where user_id = '$user->id' ");
				$agent_id = $db->loadResult();
				$db->setQuery("Select count(id) from #__osrs_agent_account where agent_id ='$agent_id' and `status` = '1' and nproperties > 0");
			}elseif(self::isCompanyAdmin()){
				$db->setQuery("Select id from #__osrs_companies where user_id = '$user->id' ");
				$company_id = $db->loadResult();
				$db->setQuery("Select count(id) from #__osrs_agent_account where company_id ='$company_id' and `status` = '1' and nproperties > 0");
			}
			$count = $db->loadResult();
			if($count == 0){
				return false;
			}else{
				return true;
			}
		}
	}

	/**
    * This static function is used to get Subscribed plans of user
    * @return array|mixed
    **/
	static function returnAccountValue(){
		global $bootstrapHelper, $jinput, $configClass;
		$db = Factory::getDbo();
		$available_plans = OspropertyMembership::returnAvailablePlans();
		$rows = array();
		if(count($available_plans) > 0){
			$available_plans = implode(",",$available_plans);
			//get from account table
			if(self::isAgent()){
				$agent_id = self::getAgentID();
				$query =  "SELECT a.*,c.title,b.from_date,b.to_date FROM #__osrs_agent_account AS a INNER JOIN #__osmembership_subscribers AS b ON b.id = a.sub_id INNER JOIN #__osmembership_plans AS c ON c.id = b.plan_id WHERE a.agent_id = '$agent_id' AND a.status = '1' AND b.plan_id IN ($available_plans) AND a.nproperties > 0";
				$db->setQuery($query);
			$rows = $db->loadObjectList();
			}elseif(self::isCompanyAdmin()){
				$company_id = self::getCompanyId();
				$query =  "SELECT a.*,c.title,b.from_date,b.to_date FROM #__osrs_agent_account AS a INNER JOIN #__osmembership_subscribers AS b ON b.id = a.sub_id INNER JOIN #__osmembership_plans AS c ON c.id = b.plan_id WHERE a.company_id = '$company_id' AND a.status = '1' AND b.plan_id IN ($available_plans) AND a.nproperties > 0";
				$db->setQuery($query);
				$rows = $db->loadObjectList();
			}
		}
		return $rows;
	}

	/**
    * This static function is used to get Subscribed plans of user
    * @return array|mixed
    **/
	static function returnAccountValueFeatured($nitems){
		global $bootstrapHelper, $jinput, $configClass;
		$db = Factory::getDbo();
		$available_plans = OspropertyMembership::returnAvailableFeaturedPlans($nitems);
		$rows = array();
		if(count($available_plans) > 0){
			$available_plans = implode(",",$available_plans);
			//get from account table
			if(self::isAgent()){
				$agent_id = self::getAgentID();
				$query =  "SELECT a.*,c.title,b.from_date,b.to_date FROM #__osrs_agent_account AS a INNER JOIN #__osmembership_subscribers AS b ON b.id = a.sub_id INNER JOIN #__osmembership_plans AS c ON c.id = b.plan_id WHERE a.agent_id = '$agent_id' AND a.status = '1' AND b.plan_id IN ($available_plans) AND a.nproperties > 0";
				$db->setQuery($query);
			$rows = $db->loadObjectList();
			}elseif(self::isCompanyAdmin()){
				$company_id = self::getCompanyId();
				$query =  "SELECT a.*,c.title,b.from_date,b.to_date FROM #__osrs_agent_account AS a INNER JOIN #__osmembership_subscribers AS b ON b.id = a.sub_id INNER JOIN #__osmembership_plans AS c ON c.id = b.plan_id WHERE a.company_id = '$company_id' AND a.status = '1' AND b.plan_id IN ($available_plans) AND a.nproperties > 0";
				$db->setQuery($query);
				$rows = $db->loadObjectList();
			}
		}
		return $rows;
	}

	//return type 0 -> date
	//return type 1 -> int
	public static function getExpiredNormal($start_time,$return_type){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		$number_days = $configClass['general_time_in_days'];
		$stop_time = $start_time + $number_days*24*3600;
		if($return_type == 0){
			return date("Y-m-d H:i:s",$stop_time);
		}else{
			return $stop_time;
		}
	}

	//return type 0 -> date
	//return type 1 -> int
	static function getExpiredFeature($start_time,$return_type){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		$number_days = $configClass['general_time_in_days_featured'];
		$stop_time = $start_time + $number_days*24*3600;
		if($return_type == 0){
			return date("Y-m-d H:i:s",$stop_time);
		}else{
			return $stop_time;
		}
	}


	/**
	 * Set approval and isFeature from jos_osrs_properties table
	 *
	 * @param unknown_type $type
	 * @param unknown_type $id
	 */
	static function setApproval($type,$id)
	{
		$db = Factory::getDbo();
		if($type == "f")
		{
			$db->setQuery("UPDATE #__osrs_properties SET isFeatured = '1',approved = '1',published = '1' WHERE id = '$id'");
			$db->execute();
		}
		else
		{
			$db->setQuery("UPDATE #__osrs_properties SET approved = '1',published = '1' WHERE id = '$id'");
			$db->execute();
		}
	}


	public static function getRealTime(){
		$config = new JConfig();
		$offset = $config->offset;
		return strtotime(Factory::getDate('now',$offset));
	}
	/**
	 * Set Expired
	 *
	 * @param unknown_type $id
	 * @param unknown_type $type
	 * @param unknown_type $isNew
	 */
	static function setExpiredTime($id,$type,$isNew)
	{
		global $bootstrapHelper, $jinput, $mainframe,$configs,$configClass;
		$db = Factory::getDbo();
		$current_time 	= self::getRealTime();
		$db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
		$count = $db->loadResult();
		if($count == 0)
		{
			//check and calculate the expired and clean db time
			$unpublish_time = intval($configClass['general_time_in_days']);
			$remove_time	= intval($configClass['general_unpublished_days']);
			$feature_time	= intval($configClass['general_time_in_days_featured']);
			if($type == "f"){
				$unpublish_time = $feature_time;
				//calculate the unfeature time
				$feature_time    = $current_time + $feature_time*24*3600;
			}
			$send_appro		= $configClass['send_approximates'];
			$appro_days		= $configClass['approximates_days'];

			$unpublish_time = $current_time + $unpublish_time*24*3600;
			//calculate remove time
			$remove_time    = $unpublish_time + $remove_time*24*3600;

			//allow to send the approximates expired day
			if($send_appro == 1)
			{
				$inform_time = $unpublish_time - $appro_days*24*3600;
				$inform_time = date("Y-m-d H:i:s",$inform_time);
			}
			else
			{
				$inform_time = $db->getNullDate();
			}
			//change to time stamp
			$unpublish_time	= date("Y-m-d H:i:s",$unpublish_time);
			$remove_time	= date("Y-m-d H:i:s",$remove_time);
			$feature_time   = date("Y-m-d H:i:s",$feature_time);
			//insert into #__osrs_expired
			$db->setQuery("Insert into #__osrs_expired (id,pid,inform_time,expired_time,expired_feature_time,remove_from_database) values (NULL,$id,'$inform_time','$unpublish_time','$feature_time','$remove_time')");
			$db->execute();
			//update start publishing today
			OspropertyListing::updateStartPublishing($id);

		}else{
			//in the case this property is already in the expired table
			//check and calculate the expired and clean db time
			$unpublish_time = intval($configClass['general_time_in_days']);
			$remove_time	= intval($configClass['general_unpublished_days']);
			$feature_time	= intval($configClass['general_time_in_days_featured']);
			$send_appro		= $configClass['send_approximates'];
			$appro_days		= $configClass['approximates_days'];

			$db->setQuery("Select * from #__osrs_expired where pid = '$id'");
			$expired = $db->loadObject();
			$expired_time = $expired->expired_time;
			$expired_feature_time = $expired->expired_feature_time;
			$expired_time_int = strtotime($expired_time);
			$expired_feature_int = strtotime($expired_feature_time);

			if($type == "f"){
				if($expired_feature_int > $current_time){
					$current_time = $expired_feature_int;
				}
				$unpublish_time = $feature_time;
				//calculate the unfeature time
				$feature_time    = $current_time + $feature_time*24*3600;
			}

			if($type == "n"){
				if($expired_time_int > $current_time){
					$current_time = $expired_time_int;
				}
			}

			$unpublish_time = $current_time + $unpublish_time*24*3600;
			if($unpublish_time < $expired_time_int){
				$unpublish_time = $expired_time_int;
			}
			//calculate remove time
			$remove_time    = $unpublish_time + $remove_time*24*3600;
			//allow to send the approximates expired day
			if($send_appro == 1){
				$inform_time = $unpublish_time - $appro_days*24*3600;
				$inform_time = date("Y-m-d H:i:s",$inform_time);
			}else{
				$inform_time = "";
			}
			//change to time stamp
			$unpublish_time	= date("Y-m-d H:i:s",$unpublish_time);
			$remove_time	= date("Y-m-d H:i:s",$remove_time);
			$feature_time   = date("Y-m-d H:i:s",$feature_time);
			//insert into #__osrs_expired
			$db->setQuery("UPDATE #__osrs_expired SET inform_time = '$inform_time',expired_time='$unpublish_time',expired_feature_time = '$feature_time',remove_from_database='$remove_time' WHERE pid = '$id'");
			$db->execute();
			//update start publishing today
			OspropertyListing::updateStartPublishing($id);
		}
	}

	/**
	 * Discount subscription
	 *
	 * @param unknown_type $type
	 */
	static function discountSubscription($sub_id,$deduct = 1){
		$db = Factory::getDbo();
        if(self::isAgent()) {
            $agent_id = self::getAgentID();
            $db->setQuery("Select count(id) from #__osrs_agent_account where agent_id = '$agent_id'");
            $count = $db->loadResult();
            if ($count > 0) {
                $db->setQuery("UPDATE #__osrs_agent_account SET nproperties = nproperties - ".$deduct." WHERE agent_id = '$agent_id' and sub_id = '$sub_id'");
                $db->execute();
            }
        }elseif(self::isCompanyAdmin()){
            $company_id = self::getCompanyId();
            $db->setQuery("Select count(id) from #__osrs_agent_account where company_id = '$company_id'");
            $count = $db->loadResult();
            if ($count > 0) {
                $db->setQuery("UPDATE #__osrs_agent_account SET nproperties = nproperties - ".$deduct." WHERE company_id = '$company_id' and sub_id = '$sub_id'");
                $db->execute();
            }
        }
	}

	/**
	 * Slimbox Gallery
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $photos
	 */
	static function slimboxGallery($pid,$photos){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		$property_photo_link = Uri::root()."images/osproperty/properties/".$pid."/";
		?>
		<script type="text/javascript" src="<?php echo Uri::root()?>media/com_osproperty/assets/js/colorbox/jquery.colorbox.js"></script>
		<script type="text/javascript">
		 jQuery(document).ready( function(){
		     jQuery(".propertyphotogroupgallery").colorbox({rel:'colorboxgallery',maxWidth:'95%', maxHeight:'95%'});
		 });
		</script>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<?php
			$k =0;
			for($i=0;$i<count($photos);$i++){
				
				$photo = $photos[$i];
				$title = $photo->image_desc;
				$title = str_replace("\n","",$title);
				$title = str_replace("\r","",$title);
				$title = str_replace("'","\'",$title);
				
				if(file_exists(JPATH_ROOT."/images/osproperty/properties/".$pid."/thumb/".$photos[$i]->image)){
				
					$k++;
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?>">
						<a href="<?php echo $property_photo_link?><?php echo $photos[$i]->image?>" class="propertyphotogroupgallery" title="<?php echo $title;?>" >
							<img src="<?php echo $property_photo_link?>thumb/<?php echo $photos[$i]->image?>" class="border1 padding3" />
						</a>
					</div>
					<?php
				}
				
				if($k == 4){
					?>
					</div>
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<?php
					$k = 0;
				}
			}
			?>
		</div>
		<?php
	}

	/**
	 * Show photo gallery of properties
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $photos
	 */
	static function propertyGallery($property,$photos){
		global $bootstrapHelper, $jinput, $mainframe,$configClass,$ismobile;
		OSPHelperJquery::colorbox('a.osmodal');
		$document = Factory::getDocument();
		$pid = $property->id;
		$property_photo_link = Uri::root()."images/osproperty/properties/".$pid."/";
		?>
		<script type="text/javascript" src="<?php echo Uri::root()?>media/com_osproperty/assets/js/colorbox/jquery.colorbox.js"></script>
		<script type="text/javascript">
		 jQuery(document).ready( function(){
			 jQuery(".propertyphotogroup1").colorbox({rel:'colorbox',maxWidth:'95%', maxHeight:'95%'});
		 });
		</script>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<?php
			if(count($photos) > 0){
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<div class="displayblock relative width100pc" id="img0">
					<a href="<?php echo $property_photo_link?><?php echo $photos[0]->image?>" class="propertyphotogroup1" title="<?php echo $photos[0]->image_desc;?>">
						<img src="<?php echo $property_photo_link?><?php echo $photos[0]->image?>" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?>">
						<?php
						if($photos[0]->image_desc != ""){
						?>
						<h2 style="opacity:0.4;filter:alpha(opacity=40);" class="fontnormal padding10 colorwhite absolute top0 left5 backgroundblack">
							<?php
							echo $photos[0]->image_desc;
							?>
						</h2>
						<?php } ?>
					</a>
				</div>
				<?php
				for($i=1;$i<count($photos);$i++){
					$photo = $photos[$i];
					?>
					<div class="nodisplay relative width100pc" id="img<?php echo $i?>">
						<a href="<?php echo $property_photo_link?><?php echo $photos[$i]->image?>" class="propertyphotogroup1">
							<img src="<?php echo $property_photo_link?><?php echo $photos[$i]->image?>" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?>" title="<?php echo $photos[$i]->image_desc;?>">
							<?php
							if($photos[$i]->image_desc != ""){
							?>
							<h2 class="absolute colorwhite left5 backgroundblack fontnormal padding10" style="opacity:0.4;filter:alpha(opacity=40);">
								<?php
								echo $photos[$i]->image_desc;
								?>
							</h2>
							<?php } ?>
						</a>
					</div>
					<?php
				}
				?>
			</div>
			<div class="clearfix"></div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin" id="thumbPhotos_wrap">
				<div id="thumbPhotos" class="thumbPhotos1">
					<?php
					for($i=0;$i<count($photos);$i++){
						$photo = $photos[$i];
						if($photo->image != ""){
							if(file_exists(JPATH_ROOT."/images/osproperty/properties".DS.$pid."/thumb".DS.$photos[$i]->image)){
							?>
							<div class="thumbPhotos1a">
								<img src="<?php echo $property_photo_link?>thumb/<?php echo $photos[$i]->image?>" width="45" id="thumb<?php echo $i?>">
							</div>
							<script type="text/javascript">
							jQuery(document).ready( function(){
							    jQuery("#thumb<?php echo $i?>").hover( function() {
							      jQuery(this).stop().animate({opacity: "0.5"}, 'fast');
							    },
							     function() {
							      jQuery(this).stop().animate({opacity: "1"}, 'fast');
							    });
							  });
							</script>
							<?php
							}
						}
					}
					?>
				</div>
			</div>
			<?php
			}else{
			?>
				<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/nopropertyphoto.png" />
			<?php
			}
			?>
		</div>
		<script type="text/javascript">
		 function showImage(id){
			var current_image = document.getElementById('current_image');
			cimage = current_image.value;
			var img = document.getElementById('img' + cimage);
			img.style.display = "none";
			current_image.value = id;
			var img = document.getElementById('img' + id);
			img.style.display = "block";
		}
		</script>
		<?php
	}
	
	/**
	 * Get conversion
	 *
	 * @param unknown_type $cur_from
	 * @param unknown_type $cur_to
	 * @return unknown
	 */
	 /*
	static function get_conversion($cur_from,$cur_to){
	    $session = Factory::getSession();
	    $conversions = $session->get('conversion',array());
	    $rate_exists = 0;
	    if(count($conversions) > 0){
	        foreach($conversions as $conversion){
	            if($conversion->cur_from == $cur_from && $conversion->cur_to == $cur_to && $conversion->cur_from != '' && $conversion->cur_to != ''){
	                $rate_exists = 1;
	                return $conversion->rate;
                }
            }
        }

        if($rate_exists == 0) {
            $url = sprintf('https://www.google.com/search?q=1+%s+to+%s', $cur_from, $cur_to);
            $headers = [
                'Accept' => 'text/html',
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0',
            ];
            $http = JHttpFactory::getHttp();
            $response = $http->get($url, $headers);
            if (302 == $response->code && isset($response->headers['Location'])) {
                $response = $http->get($response->headers['Location'], $headers);
            }
            $body = $response->body;
            $exchangedRate = 1;
            try {
                $exchangedRate = static::buildExchangeRate($body);
            } catch (Exception $e) {

            }
            $count = count($conversions);
            $conversions[$count]->cur_from = $cur_from;
            $conversions[$count]->cur_to = $cur_to;
            $conversions[$count]->rate = $exchangedRate;
            $session->set('conversion',$conversions);
            return $exchangedRate;
        }
	}
	*/

	static function get_conversion($cur_from,$cur_to)
	{
		global $configClass;
		$convert_api		= $configClass['convert_api'];
		$http				= HttpFactory::getHttp();
        $url				= 'https://free.currconv.com/api/v7/convert?q='.$cur_from.'_'.$cur_to.'&compact=ultra&apiKey='.$convert_api;
        $response			= $http->get($url);
		$converted			= 1;
		if ($response->code == 200)
        {
            $data = $response->body;
            $returnArr = json_decode($data);
            $converted = $returnArr->{$cur_from.'_'.$cur_to};
        }
        return $converted;
	}

    /**
     * Builds an exchange rate from the response content.
     *
     * @param string $content
     *
     * @return float
     *
     * @throws \Exception
     */
    protected static function buildExchangeRate($content)
    {
        $document = new \DOMDocument();

        if (false === @$document->loadHTML('<?xml encoding="utf-8" ?>' . $content))
        {
            throw new Exception('The page content is not loadable');
        }

        $xpath = new \DOMXPath($document);
        $nodes = $xpath->query('//span[@id="knowledge-currency__tgt-amount"]');

        if (1 !== $nodes->length)
        {
            $nodes = $xpath->query('//div[@class="vk_ans vk_bk" or @class="dDoNo vk_bk"]');
        }

        if (1 !== $nodes->length)
        {
            throw new Exception('The currency is not supported or Google changed the response format');
        }

        $nodeContent = $nodes->item(0)->textContent;

        // Beware of "3 417.36111 Colombian pesos", with a non breaking space
        $bid = strtr($nodeContent, ["\xc2\xa0" => '']);

        if (false !== strpos($bid, ' '))
        {
            $bid = strstr($bid, ' ', true);
        }
        // Does it have thousands separator?
        if (strpos($bid, ',') && strpos($bid, '.'))
        {
            $bid = str_replace(',', '', $bid);
        }
		
        if (!is_numeric($bid))
        {
			
			if (strpos($bid, ',')){
				$bid = str_replace(',', '.', $bid);
			}
            //throw new Exception('The currency is not supported or Google changed the response format');
        }

        return $bid;
    }

	/**
	 * Get Categories list
	 *
	 * @param unknown_type $catid
	 */
	static function getCategoryParent($catid,& $categoryArr){
		global $bootstrapHelper, $jinput, $mainframe;
		$categoryArr[count($categoryArr)] = $catid;
		$db = Factory::getDbo();
		$db->setQuery("Select parent_id from #__osrs_categories where id = '$catid' and published = '1'");
		$parent_id = $db->loadResult();
		if($parent_id > 0){
			$categoryArr = HelperOspropertyCommon::getCategoryParent($parent_id,$categoryArr);
		}
		return $categoryArr;
	}

	/**
	 * Get the list of categories
	 *
	 * @param unknown_type $catid
	 * @param unknown_type $categoryArr
	 */
	static function getSubCategories($catid,& $categoryArr){
		global $bootstrapHelper, $jinput, $mainframe;
		$categoryArr[count($categoryArr)] = $catid;
		$db = Factory::getDbo();
		$db->setQuery("Select id from #__osrs_categories where parent_id = '$catid' and published = '1'");
		$catIds = $db->loadObjectList();
		if(count($catIds) > 0){
			for($i=0;$i<count($catIds);$i++){
				$categoryArr = HelperOspropertyCommon::getSubCategories($catIds[$i]->id,$categoryArr);
			}
		}
		return $categoryArr;
	}

	/**
	 * Check is Photo file
	 * Return false : if it is not the JPEG photo
	 * Return true  : if it is JPEG photo
	 */
	static function checkIsPhotoFileUploaded($element_name){
		$file = $_FILES[$element_name];
		$fname = $file['name'];
		$ftype = end(explode('.', strtolower($fname)));
		$ftype = strtolower($ftype);
		$allowtype = array('jpg','jpeg','gif','png');
		if(!in_array($ftype,$allowtype)){
			return false;
		}else{
			//return true;
			$imageinfo = getimagesize($_FILES[$element_name]['tmp_name']);
			if(strtolower($imageinfo['mime']) != 'image/jpeg' && strtolower($imageinfo['mime']) != 'image/jpg' && strtolower($imageinfo['mime']) != 'image/png' && strtolower($imageinfo['mime']) != 'image/gif') {
			    return false;
			}else{
				return true;
			}
		}
	}

	/**
	 * Check is Document file
	 * Return false : if it is not Doc or PDF file
	 * Return true  : if it is Doc or PDF file
	 */
	static function checkIsDocumentFileUploaded($element_name){
		$file = $_FILES[$element_name];
		$fname = $file['name'];
		$ftype = end(explode('.', strtolower($fname)));
		$ftype = strtolower($ftype);
		$allowtype = array('pdf','doc','docx');
		if(!in_array($ftype,$allowtype)){
			return false;
		}else{
			$type = strtolower($_FILES[$element_name]['type']);
			if (($type == "application/msword") || ($type == "application/pdf")){ 
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * Get the category list
	 *
	 * @param unknown_type $parent_id
	 */
	static function getCatList($parent_id, & $catArr){
		global $bootstrapHelper, $jinput, $mainframe,$lang_suffix;
		$db = Factory::getDbo();
		$db->setQuery("Select id, parent_id,category_name$lang_suffix as category_name from #__osrs_categories where id = '$parent_id'");
		$category = $db->loadObjectList();
		if(count($category) > 0){
			$category = $category[0];
			$count = count($catArr);
			$catArr[$count]	= new stdClass();
			$catArr[$count]->id = $category->id;
			$catArr[$count]->cat_name = $category->category_name;
			$parent_id = $category->parent_id;
			$catArr = self::getCatList($parent_id,$catArr);
		}
		return $catArr;
	}

	/**
	 * Drawn DPE Chart
	 *
	 * @param unknown_type $energy
	 * @param unknown_type $climate
	 * @return unknown
	 */
	static function drawGraph($energy, $climate, $e_class, $c_class)
	{
		global $bootstrapHelper, $configClass;
		$dstyle = 'padding: 0 3px; line-height: 20px; margin-bottom: 2px; height: 20px;';

		if(($energy != 'null') || ($e_class != ""))
		{
			if(($climate != 'null') || ($c_class != ""))
			{
				$cwidth = '50%';
			}
			else
			{
				$cwidth = '100%';
			}
		}
		if(($climate != 'null') || ($c_class != ""))
		{
			if(($energy != 'null') || ($e_class != ""))
			{
				$cwidth = '50%';
			}
			else
			{
				$cwidth = '100%';
			}
		}
		$e_measurement = "kWH/m";
		$c_measurement = "kg/m";

		$dpe_display = '';

		$energy_bg =  array('#3a7b15','#3e8d11','#49a909','#82ad06','#d7d71f','#d6ab28','#de801e','#d51b1c','#c51415','#a9090a','8a0506');

		$dpe_display .= '<div class="'.$bootstrapHelper->getClassMapping('row-fluid').'"><div class="'.$bootstrapHelper->getClassMapping('span6').'">';
		if(((isset($energy) && $energy != 'null') || ($e_class != "")) && (($configClass['energy_class'] != "") && ($configClass['energy_value'] != "")))
		{
			$class_name  = $configClass['energy_class'];
			$class_value = $configClass['energy_value'];

			if(($class_name != "") && ($class_value != ""))
			{

				$class_name_array = explode(",",$class_name);
				$class_value_array = explode(",",$class_value);

				$dpe_display .= '<div class="os_dpe_header"><strong>' . Text::_('OS_ENERGY_HEADER') . ' (' . $e_measurement . ')</strong></div>';
				$r_energy = round((float)$energy);

				$dpe_display .= '<div class="clearfix"></div>
								<div class="os_dpe_energy_container relative">';

				$basic_width = round(70/count($class_name_array));

				for($i=0;$i<count($class_name_array);$i++)
				{
					$class = $class_name_array[$i];
					$width = 20 + $basic_width + $basic_width*$i;
					//checking value;
					if($e_class != "")
					{
						if($e_class == $class)
						{
							$e_height = $i*22;
						}
						if($i==0)
						{
							$value = "< ".$class_value_array[0];
						}
						elseif($i == count($class_value_array))
                        {
							$temp = $class_value_array[$i - 1] + 1;
							$value = " > ".$temp;
						}
						else
						{
							$temp = $class_value_array[$i - 1] + 1;
							$value = $temp." ".Text::_('OS_TO')." ".$class_value_array[$i];
						}
					}
					else
					{
						if($i==0)
						{
							$value = "< ".$class_value_array[0];
							if($r_energy <= $class_value_array[0])
							{
								$e_height = 0;
							}
						}
						elseif($i == count($class_value_array))
                        {
							$temp = $class_value_array[$i - 1] + 1;
							$value = " > ".$temp;
							if($r_energy >= $temp)
							{
								$e_height = $i*22;
							}
						}
						else
						{
							$temp = $class_value_array[$i - 1] + 1;
							$value = $temp." ".Text::_('OS_TO')." ".$class_value_array[$i];
							if(($r_energy >= $temp) && ($r_energy <= $class_value_array[$i]))
							{
								$e_height = $i*22;
							}
						}
					}
					$dpe_display .= '<div style="'.$dstyle.' background: '.$energy_bg[$i].'; width: '.$width.'%;" class="os_dpe_item colorwhite relative">(' . $value . ') <span class="floatright">'.$class.'</span></div>';
				}
				if($e_class != "")
				{
					$dpe_display .= '<div style="' . $dstyle . ' top: ' . $e_height . 'px; width: 10%; background: #ccc;" class="os_dpe_marker m_energy absolute center right0" title="'.Text::_('OS_ENERGY').': '.OSPHelper::showSquare($energy).' '.$e_measurement.'">' . $e_class . '</div></div>';
				}
				else
				{
					$dpe_display .= '<div style="' . $dstyle . ' top: ' . $e_height . 'px; width: 10%; background: #ccc;" class="os_dpe_marker m_energy absolute center right0">' . OSPHelper::showSquare($energy) . '</div>
								</div>';
				}
				$dpe_display .= '<div class="clearfix"></div><div class="os_dpe_footer fontsmall">' . Text::_('OS_ENERGY_FOOTER') . '</div>';
			}
		}
		
		$dpe_display .= '</div>';
		$dpe_display .= '<div class="'.$bootstrapHelper->getClassMapping('span6').'">';
		
		if(((isset($climate) && $climate != 'null') || ($c_class != "")) && (($configClass['climate_class'] != "") && ($configClass['climate_value'] != "")))
		{
			$dpe_display .= '<div class="os_dpe_header"><strong>'.Text::_('OS_CLIMATE_HEADER').' ('.$c_measurement.')</strong></div>';
			$energy_bg =  array('#ede4f5','#e0c2f8','#d4aaf6','#c996f3','#b973ef','#a74deb','#891be0','#7e36b7','#712ba8','#5a2088','#421168');
			$r_climate = round((float)$climate);

			$class_name  = $configClass['climate_class'];
			$class_value = $configClass['climate_value'];

			if(($class_name != "") && ($class_value != ""))
			{

				$class_name_array = explode(",",$class_name);
				$class_value_array = explode(",",$class_value);

				$dpe_display .= '<div class="clearfix"></div>
								<div class="os_dpe_energy_container relative">';

				$basic_width = round(70/count($class_name_array));

				for($i=0;$i<count($class_name_array);$i++)
				{
					$class = $class_name_array[$i];
					$width = 20 + $basic_width + $basic_width*$i;
					//checking value;
					if($c_class != "")
					{
						if($c_class == $class)
						{
							$e_height = $i*22;
						}
						if($i==0)
						{
							$value = "< ".$class_value_array[0];
						}
						elseif($i == count($class_value_array))
                        {
							$temp = $class_value_array[$i - 1] + 1;
							$value = " > ".$temp;
						}
						else
						{
							$temp = $class_value_array[$i - 1] + 1;
							$value = $temp." ".Text::_('OS_TO')." ".$class_value_array[$i];
						}
					}
					else
					{
						if($i==0)
						{
							$value = "< ".$class_value_array[0];
							if($r_climate <= $class_value_array[0])
							{
								$e_height = 0;
							}
						}
						elseif($i == count($class_value_array))
                        {
							$temp = $class_value_array[$i - 1] + 1;
							$value = " > ".$temp;
							if($r_climate >= $temp)
							{
								$e_height = $i*22;
							}
						}
						else
						{
							$temp = $class_value_array[$i - 1] + 1;
							$value = $temp." ".Text::_('OS_TO')." ".$class_value_array[$i];
							if(($r_climate >= $temp) && ($r_climate <= $class_value_array[$i]))
							{
								$e_height = $i*22;
							}
						}
					}
					$dpe_display .= '<div style="'.$dstyle.'background: '.$energy_bg[$i].'; width: '.$width.'%;" class="os_dpe_item colorwhite relative">(' . $value . ') <span class="floatright">'.$class.'</span></div>';
				}
				if($c_class != "")
				{
					$dpe_display .= '<div style="' . $dstyle . ' top: ' . $e_height . 'px; background: #ccc; " class="os_dpe_marker m_energy absolute center right0 width10pc hasTip" title="'.Text::_('OS_CLIMATE').': '.OSPHelper::showSquare($r_climate).' '.$c_measurement.'">' . $c_class . '</div>
								</div>';
				}
				else
				{
					$dpe_display .= '<div style="' . $dstyle . ' top: ' . $e_height . 'px; background: #ccc; " class="os_dpe_marker m_energy absolute center right0 width10pc">' . OSPHelper::showSquare($r_climate) . '</div>
								</div>';
				}
				$dpe_display .= '<div class="clearfix"></div><div class="os_dpe_footer fontsmall">' . Text::_('OS_CLIMATE_FOOTER') . '</div>';
			}
		}
		
		$dpe_display .= '</div></div>';
		
		return $dpe_display;
	}


	/**
	 * Create the photo from main photo
	 *
	 * @param unknown_type $t
	 * @param unknown_type $l
	 * @param unknown_type $h
	 * @param unknown_type $w
	 * @param unknown_type $wall_image
	 */
	static function create_photo($t,$l,$h,$w,$photo_name,$type,$pid){
		global $bootstrapHelper, $jinput, $configClass;
		$configClass = OSPHelper::loadConfig();
		$ext = $ext[count($ext)-1];
		$path = JPATH_ROOT."/images/osproperty/properties".DS.$pid;
		$srcImg  = imagecreatefromjpeg($path.DS.$photo_name);
		$newImg  = imagecreatetruecolor($w, $h);
		imagecopyresampled($newImg, $srcImg, 0, 0, $l, $t, $w, $h, $w, $h);
		if($type == 0){
			imagejpeg($newImg,$path."/thumb".DS.$photo_name);
			//resize if the photo has big size
			$images_thumbnail_width = $configClass['images_thumbnail_width'];
			$images_thumbnail_height = $configClass['images_thumbnail_height'];
			$info = getimagesize($path."/thumb".DS.$photo_name);
			$width = $info[0];
			$height = $info[1];
			if($width > $images_thumbnail_width){
				//resize image to the original thumb width
				$image = new SimpleImage();
			    $image->load($path."/thumb".DS.$photo_name);
			    $image->resize($images_thumbnail_width,$images_thumbnail_height);
			    $image->save($path."/thumb".DS.$photo_name,$configClass['images_quality']);
			}
		}else{
			imagejpeg($newImg,$path."/medium".DS.$photo_name);
			//resize if the photo has big size
			$images_large_width = $configClass['images_large_width'];
			$images_large_height = $configClass['images_large_height'];
			$info = getimagesize($path."/medium".DS.$photo_name);
			$width = $info[0];
			$height = $info[1];
			if($width > $images_large_width){
				//resize image to the original thumb width
				$image = new SimpleImage();
			    $image->load($path."/medium".DS.$photo_name);
			    $image->resize($images_large_width,$images_large_height);
			    $image->save($path."/medium".DS.$photo_name,$configClass['images_quality']);
			}
		}
	}

	/**
	 * Check max size of the image
	 *
	 * @param unknown_type $image_path
	 */
	static function returnMaxsize($image_path){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		$info = getimagesize($image_path);
		$width = $info[0];
		$height = $info[1];
		$max_width_allowed = $configClass['max_width_size'];
		$max_height_allowed = $configClass['max_height_size'];
		
		if(($height > $max_height_allowed) and ($width > $max_width_allowed)){
			$resize = 1;
			//resize to both
			/*
			$return = HelperOspropertyCommon::calResizePhoto($width,$height,$max_width_allowed,$max_height_allowed,$resize);
			//resize image
			$image = new SimpleImage();
		    $image->load($image_path);
		    $image->resize($return[0],$return[1]);
		    $image->save($image_path,100);
		    */
			OSPHelper::resizePhoto($image_path,$max_width_allowed,$max_height_allowed);
		}elseif(($height > $max_height_allowed) and ($width <= $max_width_allowed)){
			$resize = 2;
			//resize to height
			/*
			$return = HelperOspropertyCommon::calResizePhoto($width,$height,$max_width_allowed,$max_height_allowed,$resize);
			//resize image
			$image = new SimpleImage();
		    $image->load($image_path);
		    $image->resize($return[0],$return[1]);
		    $image->save($image_path,100);
		    */
			OSPHelper::resizePhoto($image_path,$width,$max_height_allowed);
		}elseif(($height <= $max_height_allowed) and ($width > $max_width_allowed)){
			$resize = 3;
			//resize to width
			/*
			$return = HelperOspropertyCommon::calResizePhoto($width,$height,$max_width_allowed,$max_height_allowed,$resize);
			//resize image
			$image = new SimpleImage();
		    $image->load($image_path);
		    $image->resize($return[0],$return[1]);
		    $image->save($image_path,100);
		    */
			OSPHelper::resizePhoto($image_path,$max_width_allowed,$height);
		}else{
			//do nothing
		}
	}


	static function calResizePhoto($width,$height, $maxwidth,$maxheight,$resize){
		global $bootstrapHelper, $jinput, $mainframe;
		switch ($resize){
			case "1":
				$return 	= HelperOspropertyCommon::calResizeWidth($width,$height,$maxwidth,$maxheight);
				$newwidth 	= $return[0];
				$newheight 	= $return[1];
				if($newheight > $maxheight){
					$return 	= HelperOspropertyCommon::calResizeHeight($width,$height,$maxwidth,$maxheight);
				}
				break;
			case "2":
				$return 	= HelperOspropertyCommon::calResizeHeight($width,$height,$maxwidth,$maxheight);
				break;
			case "3":
				$return 	= HelperOspropertyCommon::calResizeWidth($width,$height,$maxwidth,$maxheight);
				break;
		}
		return $return;
	}

	static function calResizeWidth($width,$height,$maxwidth,$maxheight){
		$return = array();
		if($width > $maxwidth){
			$newwidth  = $maxwidth;
			$newheight = round($height*$maxwidth/$width);
			$return[0] = $newwidth;
			$return[1] = $newheight;
		}else{
			$return[0] = $width;
			$return[1] = $height;
		}
		return $return;
	}

	static function calResizeHeight($width,$height,$maxwidth,$maxheight){
		$return = array();
		if($height > $maxheight){
			$newheight = $maxheight;
			$newwidth  = round($width*$maxheight/$height);
			$return[0] = $newwidth;
			$return[1] = $newheight;
		}else{
			$return[0] = $width;
			$return[1] = $height;
		}
		return $return;
	}

	/**
	 * Check to see if this user is the owner of the property
	 *
	 * @param unknown_type $pid
	 * @return unknown
	 */
	static function isOwner($pid){
		$user = Factory::getUser();
		if(intval($user->id) > 0){
			$db = Factory::getDbo();
			//check to see if this user is agent
			$db->setQuery("Select count(id) from #__osrs_agents where user_id = '$user->id' and published = '1'");
			$count = $db->loadResult();
			if($count > 0){
				$db->setQuery("Select id from #__osrs_agents where user_id = '$user->id' and published = '1'");
				$agent_id = $db->loadResult();
				$db->setQuery("Select count(id) from #__osrs_properties where agent_id = '$agent_id' and id = '$pid'");
				$count = $db->loadResult();
				if($count > 0){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	static function isCompanyOwner($pid){
		global $bootstrapHelper, $jinput, $mainframe;
		$user = Factory::getUser();
		if(intval($user->id) > 0){
			$db = Factory::getDbo();
			//check to see if this user is agent
			$db->setQuery("Select count(id) from #__osrs_companies where user_id = '$user->id' and published = '1'");
			$count = $db->loadResult();
			if($count > 0){
				$db->setQuery("Select id from #__osrs_companies where user_id = '$user->id' and published = '1'");
				$company_id = $db->loadResult();
				
				$db->setQuery("Select count(id) from #__osrs_properties where agent_id in (Select id from #__osrs_agents where published = '1' and company_id = '$company_id') and id = '$pid'");
				$count = $db->loadResult();
				if($count > 0){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	
	static function isAgentOfCompany($agent_id){
		global $bootstrapHelper, $jinput, $mainframe;
		$user = Factory::getUser();
		if(intval($user->id) > 0){
			$db = Factory::getDbo();
			//check to see if this user is agent
			$db->setQuery("Select count(id) from #__osrs_companies where user_id = '$user->id' and published = '1'");
			$count = $db->loadResult();
			if($count > 0){
				$db->setQuery("Select id from #__osrs_companies where user_id = '$user->id' and published = '1'");
				$company_id = $db->loadResult();
				
				$db->setQuery("Select count(id) from #__osrs_agents where id = '$agent_id' and company_id = '$company_id'");
				$count = $db->loadResult();
				if($count > 0){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * Export data in XML Google Earth KML format
	 *
	 * @param unknown_type $rows
	 */
	static function generateGoogleEarthKML($rows){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		while(ob_end_clean());
		$document 	   = Factory::getDocument();
		$document->setMimeEncoding('application/vnd.google-earth.kml+xml');
		$app = Factory::getApplication();

		$config = Factory::getConfig();

		################################################################################
		# WRITTEN FOR GOOGLE KML 2.2 SPECS (US VERSION)
		# http://code.google.com/apis/kml/documentation/kmlreference.html
		################################################################################
		$temp_name = time().".xml";
		$xml = new XMLWriter();
		$xml->openURI(JPATH_ROOT."/tmp".DS.time().$temp_name);
		$xml->startDocument('1.0');
		$xml->setIndent(true);

		$xml->startElement('kml');
		$xml->writeAttribute('xmlns', 'http://www.opengis.net/kml/2.2');
		$xml->writeAttribute('xmlns:gx', 'http://www.google.com/kml/ext/2.2');
		$xml->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');

		$xml->startElement('Document');
		$xml->startElement('atom:author');
		$xml->writeElement('atom:name', $configClass['business_name']);
		$xml->endElement();

		$xml->writeElement('name', $configClass['business_name']);

		$xml->startElement('Style');
		$xml->startElement('IconStyle');
		$xml->writeElement('href', 'http://maps.google.com/mapfiles/kml/pal4/icon46.png');
		$xml->endElement();
		$xml->writeElement('BalloonStyle', '');
		$xml->endElement();

		if(count($rows) > 0){
			// start listings
			for ($i=0;$i<count($rows);$i++){
				$row 		= $rows[$i];
				$db->setQuery("Select * from #__osrs_photos where pro_id = '$row->id'");
				$images     = $db->loadObjectList();
				//$features	= ipropertyModelFeed::getFeatures($property['id']);
				$query 		= "Select a.id, a.amenities from #__osrs_amenities as a"
				." inner join #__osrs_property_amenities as b on b.amen_id = a.id"
				." where a.published = '1' and b.pro_id = '$row->id'";
				$db->setQuery($query);
				$features 	= $db->loadObjectList();

				// create photo link
				if($images){
					$photo	= Uri::root()."images/osproperty/properties/".$row->id."/".$images[0]->image;
				}else{
					$photo  = '';
				}
				$address = '';
				if($row->show_address == 1){
					$address    .= $row->address;
					if($row->postcode != ""){
						$address    .= ", ".$row->postcode;
					}
					if($row->city > 0){
						$address    .= ", ".HelperOspropertyCommon::loadCityName($row->city);
					}
					if($row->region != ""){
						$address    .= ", ".$row->region;
					}
					$address    .= ", ".$row->start_name;
					$address    .= ", ".$row->country_name;
				}

				$title = $row->pro_name;

				if($row->agent_photo != ""){

					// define vars
					$agent_image = Uri::ROOT() . "images/osproperty/agent/thumbnail/" . $row->agent_photo;
				}

				// build the balloon_text object here.
				$balloon_text = '<div style="width: 670px;">
								<table width="100%" cellspacing="0" cellpadding="5">
								<tr>
								<td valign="top" style="width: 180px; border-right: solid 1px #ccc;">
								<div style="padding-bottom: 5px;"><img src="' . Uri::root() . 'media/com_osproperty/agents/' . $property['agent_photo'] . '" alt="' .$row->agent_name .'" width="78" style="border: solid 1px #666; margin-bottom: 5px;" />
								</div>
								<div style="font-size: 11px; padding-top: 5px; border-top: solid 1px #ccc;">
								<a href="' . Uri::root() . 'index.php?option=com_osproperty&task=agent_info&id=' . $row->agent_id . '" style="color: #ff0000; text-decoration: none; font-size: 12px; font-weight: bold;">' .$row->agent_name . '</a><br />';

				if($row->agent_email) $balloon_text .= '<img src="' . Uri::root() . 'components/com_osproperty/assets/images/icon-email.gif" />' . $row->agent_email . '<br />';

				$balloon_text .= '</div>
								</td>
								<td valign="top" style="width: 470px;">
								<div style="border-bottom: solid 1px #ccc; padding: 0 10px 5px 10px; margin-bottom: 5px; font-size: 16px; font-weight: bold; text-transform: uppercase;">
								<a href="' . Uri::root() . 'index.php?option=com_osproperty&task=property_details&id=' .$row->id. '">' . $address . '</a>
								</div>
								<div>';
				if($row->bed_room != "") $balloon_text .= '<strong>Bedrooms:</strong> ' . $row->bed_room . '<br />';
				if($row->bath_room) $balloon_text .= '<strong>Bathrooms:</strong> ' . $row->bath_room . '<br />';
				if($row->square_feet) $balloon_text .= '<strong>Square FT:</strong> ' . $row->square_feet . '<br />';
				if($row->rooms) $balloon_text .= '<strong>Rooms:</strong> ' . $row->rooms . '<br />';
				if($property['price']) $balloon_text .= '<br /><span style="font-size: 14px; font-weight: bold;">Listing Price:</span><br /><span style="font-size: 24px; font-weight: bold; color: #ff0000;"> ' . HelperOspropertyCommon::loadCurrency($row->curr)." ".OSPHelper::showPrice($row->price);
				if($row->rent_time != ""){
					$balloon_text .= "/".$row->rent_time;
				}
				$balloon_text .= '</span>';

				$balloon_text .= '</div>
								<div style="padding-top: 10px; clear: both;">
								<strong>Property Description:</strong><br /> ' . $row->pro_small_desc . '<br />
								</div>
								</td>
								</tr>
								</table>
								</div>';			


				####################################################################
				# THIS IS WHERE THE ACTUAL PLACEMARK STARTS GETTING BUILT
				####################################################################

				$xml->startElement('Placemark');
				$xml->writeAttribute('id', $row->id);
				// location section
				$xml->writeElement("name", $title);

				$xml->startElement("description");
				$xml->writeCData($row->pro_small_desc);
				$xml->endElement();

				$xml->startElement("Point");
				$xml->writeElement("coordinates",$row->lat_add. "," .$row->lat_add . ",0");
				$xml->endElement();

				$xml->startElement("Style");
				$xml->startElement("IconStyle");
				$xml->startElement("Icon");
				$xml->writeElement("href", $photo );
				$xml->endElement();
				$xml->endElement();
				$xml->startElement("BalloonStyle");
				$xml->startElement("text");
				$xml->writeCData($balloon_text);
				$xml->endElement();
				$xml->endElement();
				$xml->endElement();

				// end listing data
				$xml->endElement(); // item
			}
		}
		$xml->endElement(); // rss
		$xml->endDocument();
		$xml->flush();

		self::processDownload(JPATH_ROOT."/tmp".DS.time().$temp_name,$temp_name);

	}

	/**
	 * Process download a file
	 *
	 * @param string $file : Full path to the file which will be downloaded
	 */
	public static function processDownload($filePath, $filename, $detectFilename = false) {
		jimport ( 'joomla.filesystem.file' );
		$fsize = @filesize ( $filePath );
		$mod_date = date ( 'r', filemtime ( $filePath ) );
		$cont_dis = 'attachment';
		if ($detectFilename) {
			$pos = strpos ( $filename, '_' );
			$filename = substr ( $filename, $pos + 1 );
		}
		$ext = File::getExt ( $filename );
		$mime = self::getMimeType ( $ext );
		// required for IE, otherwise Content-disposition is ignored
		if (ini_get ( 'zlib.output_compression' )) {
			ini_set ( 'zlib.output_compression', 'Off' );
		}
		header ( "Pragma: public" );
		header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header ( "Expires: 0" );
		header ( "Content-Transfer-Encoding: binary" );
		header ( 'Content-Disposition:' . $cont_dis . ';' . ' filename="' . $filename . '";' . ' modification-date="' . $mod_date . '";' . ' size=' . $fsize . ';' ); //RFC2183
		header ( "Content-Type: " . $mime ); // MIME type
		header ( "Content-Length: " . $fsize );

		if (! ini_get ( 'safe_mode' )) { // set_time_limit doesn't work in safe mode
			@set_time_limit ( 0 );
		}
		self::readfile_chunked ( $filePath );
	}

	/**
	 * Get mimetype of a file
	 *
	 * @return string
	 */
	public static function getMimeType($ext) {
		require_once JPATH_ROOT . "/components/com_osproperty/helpers/mime.mapping.php";
		foreach ( $mime_extension_map as $key => $value ) {
			if ($key == $ext) {
				return $value;
			}
		}

		return "";
	}

	/**
	 * Filter Form
	 *
	 * @param unknown_type $lists
	 */
	static function filterForm($lists)
	{
		global $bootstrapHelper, $jinput, $mainframe,$languages,$configClass;
		$session = Factory::getSession();
		$use_filterform = $session->get('use_filterform');
		if($configClass['show_searchform']== 1 && $lists['show_filterform'] == 1) 
		{
            $show_location_div = 0;
            $point = 0;
            if(OSPHelper::checkOwnerExisting() && $lists['show_agenttypefilter']==1)
			{
                $point++;
            }
            if ($lists['show_locationfilter'] == 1) {
                $show_location_div = 1;
                $point++;
            }
            if ($lists['show_pricefilter'] == 1) {
                $point++;
            }
            if ($lists['show_propertytypefilter'] == 1) {
                $point++;
            }
            if ($lists['show_categoryfilter'] == 1) {
                $point++;
            }
            if ($point > 0) {
                $show_filter_button = 1;
            }else{
                $show_filter_button = 0;
            }
            if($point > 2){
                $show_submit = 1;
            }else{
                $show_submit = 0;
            }
            if(HelperOspropertyCommon::checkCountry()) {
                $show_country_dropdown = 1;
            }else{
                $show_country_dropdown = 0;
            }

            ?>
			<script type="text/javascript">
			 function submitFilterForm(){
				var item = document.getElementById('use_filterform');
				item.value = 1;
				var item1 = document.getElementById('restart_filterform');
				item1.value = 1;
				var form = document.getElementById('ftForm');
				form.submit();
			}
			</script>
			<input type="hidden" name="use_filterform" id="use_filterform" value="<?php echo (int) $use_filterform; ?>" />
			<input type="hidden" name="restart_filterform" id="restart_filterform" value="0" />
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <div class="btn-toolbar filter-bar">
						<?php
						$currentUrl = OSPHelper::currentUrl();
						$resetLink = Uri::root()."index.php?option=com_osproperty&tmpl=component&task=property_resetfilterform&return=".$currentUrl;
						?>
                        <?php
                        if($lists['show_keywordfilter'] == 1) {
                            ?>
                            <div class="filter-search btn-group pull-left">
                                <input type="text" class="input-large search-query" name="keyword" id="keyword" value="<?php echo htmlspecialchars($lists['keyword']);?>" />
                            </div>
                            <div class="btn-group pull-left">
                                <input type="button" onClick="javascript:submitFilterForm();" class="btn btn-info" value="<?php echo Text::_('OS_FILTER')?>" />
							
								<a href="<?php echo $resetLink;?>" class="btn btn-warning filterResetLink" title="<?php echo Text::_('OS_RESET')?>"><?php echo Text::_('OS_RESET')?></a>

                                <?php if($show_filter_button == 1){ ?>
                                <button class="btn hasTooltip js-stools-btn-filters" id="btn_search_tool" type="button" data-original-title="Filter the list items">
                                    <?php echo Text::_('OS_SEARCH_TOOL'); ?>
                                    <i class="caret"></i>
                                </button>
                                <?php } ?>
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                        if($lists['show_keywordfilter'] == 0) {
                            ?>
                            <div class="btn-group pull-right">
                                <input type="button" onClick="javascript:submitFilterForm();" class="btn btn-info" value="<?php echo Text::_('OS_FILTER')?>" />

                                <a href="<?php echo $resetLink;?>" class="btn btn-warning filterResetLink" title="<?php echo Text::_('OS_RESET')?>"><?php echo Text::_('OS_RESET')?></a>


                                <?php if($show_filter_button == 1){ ?>
                                    <button class="btn hasTooltip js-stools-btn-filters btn-primary" id="btn_search_tool" type="button" data-original-title="Filter the list items">
                                        <?php echo Text::_('OS_SEARCH_TOOL'); ?>
                                        <i class="caret"></i>
                                    </button>
                                <?php } ?>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="btn-group pull-right">
                            <?php echo $lists['ordertype'];?>
                        </div>
                        <div class="btn-group pull-right">
                            <?php echo $lists['sortby'];?>
                        </div>
					</div>
                </div>
			</div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" style="display:none;" id="filter_tool_div">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
					<div class="filter-bar">
						<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
							<div class="<?php echo $bootstrapHelper->getClassMapping('span12')?>">
								<?php
								if($lists['show_pricefilter'] == 1){
									if ($configClass['price_filter_type'] == 1) {
										$style = "min-width:250px;";
									}else{
										$style = "";
									}
									?>
									<div class="btn-group pull-right" style="<?php echo $style; ?>" id="price_filter"><label>
											<?php
											if($configClass['price_filter_type'] == 0){
												$element_id = "id='pricefilter'";
											}else{
												$element_id = "";
											}
											OSPHelper::showPriceFilter($lists['price_value'],$lists['min_price'],$lists['max_price'],$lists['property_type'],'','list');
											?></label>
									</div>
									<?php
									if ($configClass['price_filter_type'] == 1) {?>
										<div class="btn-group pull-right marginright15"><label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><strong><?php echo Text::_('OS_PRICE');?></strong></label></div>
										<?php
									}
								}
								if($lists['show_propertytypefilter'] == 1){
									?>
									<div class="btn-group pull-left marginleft0">
										<?php echo $lists['type']; ?>
									</div>
								<?php
								if($lists['show_pricefilter'] == 1){
								OSPHelper::showPriceTypesConfig();
								?>
									<script type="text/javascript">
										//filter form with property type and price
										jQuery("#property_type").change(function () {
											updateLocatorPrice(jQuery("#property_type").val(), "<?php echo Uri::root(); ?>");
										});
										function updateLocatorPrice(type_id, live_site) {
											xmlHttp = GetXmlHttpObject();
											url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&task=ajax_updatePrice&type_id=" + type_id + "&option_id=<?php echo $lists['price_value'];?>&min_price=<?php echo $lists['min_price'];?>&max_price=<?php echo $lists['max_price'];?>&module_id=list";
											xmlHttp.onreadystatechange = ajax_updateLocatorSearch;
											xmlHttp.open("GET", url, true)
											xmlHttp.send(null)
										}

										function ajax_updateLocatorSearch() {
											if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete") {
												var mod_osservice_price = document.getElementById("price_filter");
												if (mod_osservice_price != null) {
													mod_osservice_price.innerHTML = xmlHttp.responseText;
													var ptype = jQuery("#property_type").val();
													jQuery.ui.slider.prototype.widgetEventPrefix = 'slider';
													jQuery(function () {
														var min_value = jQuery("#min" + ptype).val();
														min_value = parseFloat(min_value);
														var step_value = jQuery("#step" + ptype).val();
														step_value = parseFloat(step_value);
														var max_value = jQuery("#max" + ptype).val();
														max_value = parseFloat(max_value);
														jQuery("#listsliderange")[0].slide = null;
														jQuery("#listsliderange").slider({
															range: true,
															min: min_value,
															step: step_value,
															max: max_value,
															values: [min_value, max_value],
															slide: function (event, ui) {
																var price_from = ui.values[0];
																var price_to = ui.values[1];
																jQuery("#listprice_from_input1").val(price_from);
																jQuery("#listprice_to_input1").val(price_to);

																price_from = price_from.formatMoney(0, ',', '.');
																price_to = price_to.formatMoney(0, ',', '.');

																jQuery("#listprice_from_input").text(price_from);
																jQuery("#listprice_to_input").text(price_to);
															}
														});
													});
													Number.prototype.formatMoney = function (decPlaces, thouSeparator, decSeparator) {
														var n = this,
															decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
															decSeparator = decSeparator == undefined ? "." : decSeparator,
															thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
															sign = n < 0 ? "-" : "",
															i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
															j = (j = i.length) > 3 ? j % 3 : 0;
														return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
													};
												}
											}
										}
									</script>
									<?php
								}
								} ?>
								<?php
								if(($configClass['active_market_status'] == 1) && ($lists['show_marketstatusfilter'] == 1)){
									?>
									<div class="btn-group pull-left">
										<?php echo $lists['marketstatus']; ?>
									</div>
								<?php } ?>
							</div>
						</div>


						<?php
						if($show_location_div == 1)
						{
							?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
								<div class="<?php echo $bootstrapHelper->getClassMapping('span12')?>">
									<?php
									if($show_country_dropdown == 1){
										?>
										<div class="btn-group pull-left">
											<?php echo $lists['country']; ?>
										</div>
									<?php
									}else{
										echo $lists['country'];
									}
									?>
									<?php
									if(OSPHelper::userOneState()){
										?>
										<input type="hidden" name="state_id" id="state_id" value="<?php echo OSPHelper::returnDefaultState();?>" />
									<?php
									}else{
										?>
										<div class="btn-group pull-left" id="div_state">
											<?php echo $lists['state']; ?>
										</div>
									<?php
									}
									?>
									<div class="btn-group pull-left" id="city_div">
										<?php echo $lists['city']; ?>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
					if($lists['show_categoryfilter'] == 1){
						?>
						<div class="filter-bar <?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
							<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
								<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
									<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
										<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><strong><?php echo Text::_('OS_CATEGORY')?></strong></label>
									</div>
								</div>
								<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
									<?php
									$k = 0;
									foreach($lists['category'] as $cat) {
										$k++;
										?>
										<div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?>"><label class="checkbox filterformlabel"><?php echo $cat;?></label></div>
										<?php
										if($k == 4){
											$k = 0;
											?>
												</div><div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
											<?php
										}
									}
									?>
									<input type="hidden" name="submitCategory" id="submitCategory" value="1"/>
								</div>
							</div>
						</div>
					<?php } ?>
					<?php if($show_submit == 1){ ?>
					<div class="btn-toolbar filter-bar">
						<div class="btn-group pull-right">
							<input type="button" onClick="javascript:submitFilterForm();" class="btn btn-info" value="<?php echo Text::_('OS_FILTER')?>" />

							<a href="<?php echo $resetLink;?>" class="btn btn-warning filterResetLink" title="<?php echo Text::_('OS_RESET')?>"><?php echo Text::_('OS_RESET')?></a>


						</div>
					</div>
					<?php } ?>
				</div>
			</div>
            <script type="text/javascript">
                jQuery("#btn_search_tool").click(function() {
                    jQuery("#filter_tool_div").slideToggle("slow");
					jQuery("#filter_tool_div").removeClass("nodisplay");
                });
            </script>
			<?php
		}
	}
	
	/**
	 * Show advanced search form
	 *
	 * @param unknown_type $groups
	 * @param unknown_type $lists
	 * @param unknown_type $type_id_search
	 */
	static function advsearchForm($groups,$lists,$type_id_search)
    {
		global $bootstrapHelper, $jinput, $configClass,$mainframe;
		$db             = Factory::getDbo();
		$advlayout      = $configClass['advlayout'];
		if($advlayout == 0)
        {
            $layout = "advsearchform";
        }
        else
        {
            $layout = "simpleAdvsearchform";
        }
		$random_id      = "";
		$amenities      = $lists['amenities'];
		if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/'.$layout.'.php'))
		{
			$tpl        = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}
		else
		{
			$tpl        = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$tpl->set('option','com_osproperty');
		$tpl->set('groups',$groups);
		$tpl->set('lists',$lists);
		$tpl->set('amenities',$amenities);	
		$tpl->set('type_id_search',$type_id_search);
		$tpl->set('random_id',$random_id);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$tpl->set('configClass',$configClass);
		$tpl->set('jinput',$jinput);
		$tpl->set('db',$db);
		$body = $tpl->fetch($layout.".php");
		echo $body;
	}

	/**
	 * Generate price dropdown select list
	 *
	 * @param unknown_type $type_id
	 * @param unknown_type $price_id
	 * @return unknown
	 */
	public static function generatePriceList($type_id,$price_id,$classname='input-large form-select form-control')
	{
		global $bootstrapHelper, $jinput, $configClass;
		$db = Factory::getDbo();
		$prices = array();
		if($type_id > 0)
		{
			$db->setQuery("Select * from #__osrs_pricegroups where type_id = '$type_id' and published = '1' order by ordering");
			$prices = $db->loadObjectList();
		}
		if(count($prices) == 0){
			$db->setQuery("Select * from #__osrs_pricegroups where type_id = '0' and published = '1' order by ordering");
			$prices = $db->loadObjectList();
		}
		$priceArr   = array();
		$priceArr[] = HTMLHelper::_('select.option','',Text::_('OS_PRICE_FILTER'));
		for($i=0;$i<count($prices);$i++)
		{
			$price = $prices[$i];
			$text  = "";
			if($price->price_from == "0.00")
			{
				$text .= " < ";
				$text .= $configClass['curr_symbol']." ".OSPHelper::showPrice($price->price_to);
			}
			else
			{
				if($price->price_to != "0.00")
				{
					$text .= $configClass['curr_symbol']." ".OSPHelper::showPrice($price->price_from);
					$text .= " - ";
					$text .= $configClass['curr_symbol']." ".OSPHelper::showPrice($price->price_to);
				}
				else
				{
					$text .= " > ";
					$text .= $configClass['curr_symbol']." ".OSPHelper::showPrice($price->price_from);
				}
			}

			$priceArr[] = HTMLHelper::_('select.option',$price->id,$text);
		}
		return HTMLHelper::_('select.genericlist',$priceArr,'price','class="'.$classname.'"','value','text',$price_id);
	}


	/**
	 * Locator search form
	 *
	 * @param unknown_type $lists
	 * @param unknown_type $type_id
	 * @param unknown_type $configs
	 */
	static function generateLocatorForm($lists, $type_id)
	{
		global $bootstrapHelper, $jinput, $configClass,$ismobile;
		$search_Arr = array();
		if($type_id > 0)
		{
			echo '<input type="hidden"  name="property_type" value="'.$type_id.'" />';
		}
		?>
		<input type="hidden" name="orderby" id="orderby" value="<?php echo $lists['orderby']?>"/>
		<input type="hidden" name="sortby" id="sortby" value="<?php echo $lists['sortby']?>"/>

		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> locatorpage margintop10 osp-container">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                <div class="osnavbar">
                    <div class="osnavbar-inner">
                        <ul class="nav">
                            <li class="active">
                                <input type="text" name="location" id="location" class="input-large form-control" value="<?php echo stripslashes($lists['location']);?>" placeholder="<?php echo Text::_('OS_SEARCH_ADDRESS_EXPLAIN')?>" />
                            </li>
                            <li class="active"><?php echo $lists['radius']; ?></li>
                            <li class="divider-vertical <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>"></li>
							<?php 
							if($configClass['show_my_location'] == 1){
							?>
							<li>
								<a href="javascript:updateMyLocation();"
								 title="<?php echo Text::_('OS_SEARCH_AROUND_MY_LOCATION');?>">
									<?php echo Text::_('OS_MY_LOCATION');?>
								</a>
							</li>
                            <li class="divider-vertical  <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>"></li>
							<?php } ?>
							<li class="dropdown <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?> moreoption">
								<a href="javascript:void(0);" id="linkmoreoption">
									<?php echo Text::_('OS_MORE_OPTION'); ?>
								</a>
							</li>
							<li class="divider-vertical  <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>"></li>
                            <li class="active"><button type="button" onclick="javascript:checkingLocatorForm();" id="applylocatorform" class="btn btn-info"><i class="osicon-search"></i></button></li>
                        </ul>
                    </div>
                </div>
			</div>
		</div>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> nodisplay" id="locatormoredetails">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>">
				<?php
				if($configClass['locator_show_category'] == 1)
				{
				?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
							<?php echo Text::_('OS_CATEGORIES'); ?>
						</label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<?php
							echo $lists['category'];
							?>
						</div>
					</div>
				<?php } ?>
				<?php
				$locator_type_idArrs = $lists['locator_type_idArrs'];
				if(($locator_type_idArrs[0] == 0) and ($configClass['locator_show_type'] == 1)){
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_TYPE'); ?></label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<?php
							echo $lists['type'];
							?>
						</div>
					</div>
					<?php
				}
				if($configClass['active_market_status'] == 1)
				{
				?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_MARKET_STATUS')?>:</label>
					<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
						<?php
						echo $lists['market_status'];
						?>
					</div>
				</div>
				<?php } ?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_PRICE')?>:</label>
					<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
						<?php
						OSPHelper::showPriceFilter($lists['price_value'],$lists['min_price'],$lists['max_price'],$lists['locator_type'],'','adv');
						?>
					</div>
				</div>
			</div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>">
				<?php
				if($configClass['use_bedrooms'] == 1)
				{
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_BEDS')?>:</label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<?php echo $lists['nbed'];?>
						</div>
					</div>
				<?php } ?>
				<?php
				if($configClass['use_bedrooms'] == 1)
				{
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_BATHS')?>:</label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<?php echo $lists['nbath'];?>
						</div>
					</div>
				<?php } ?>
				<?php
				if($configClass['use_rooms'] == 1)
				{
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_ROOMS')?>:</label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<?php echo $lists['nroom'];?>
						</div>
					</div>
				<?php } ?>
				<?php
				if($configClass['use_squarefeet'] == 1)
				{
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
							<?php
							if($configClass['use_square'] == 0){
								echo Text::_('OS_SQUARE_FEET');
							}else{
								echo Text::_('OS_SQUARE_METER');
							}
							?>
							<?php
							echo "(";
							if($configClass['use_square'] == 0){
								echo Text::_('OS_SQFT');
							}else{
								echo Text::_('OS_SQMT');
							}
							echo ")";
							?>
							:
						</label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<input type="text" class="input-mini form-control imini" name="sqft_min" id="sqft_min" placeholder="<?php echo Text::_('OS_MIN')?>" value="<?php echo $lists['sqft_min'];?>" />
                            <span class="seperator">-</span>
							<input type="text" class="input-mini form-control imini" name="sqft_max" id="sqft_max" placeholder="<?php echo Text::_('OS_MAX')?>" value="<?php echo $lists['sqft_max'];?>"/>
						</div>
					</div>
				<?php } ?>
			</div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>">
				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_KEYWORD')?>:</label>
					<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
						<input type="text" class="input-medium form-control imedium" name="keyword" id="keyword" value="<?php echo htmlspecialchars($lists['keyword'])?>" placeholder="<?php echo Text::_('OS_KEYWORD_FITLER_PLACEHOLDER');?>" />
					</div>
				</div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_SORTBY')?>:</label>
					<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
						<?php echo $lists['sort']; ?>
					</div>
				</div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_ORDERBY')?>:</label>
					<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
						<?php echo $lists['order']; ?>
					</div>
				</div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
						<button type="button" onclick="javascript:checkingLocatorForm();" id="applylocatorform" class="btn btn-info"><i class="osicon-search"></i>&nbsp;<?php echo Text::_('OS_SEARCH');?></button>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	static function generateLocatorFormVertical($lists, $type_id){
		global $bootstrapHelper, $jinput, $configClass,$ismobile;
		$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
		$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
		$controlClass	   = $bootstrapHelper->getClassMapping('controls');
		$search_Arr = array();
		if($type_id > 0){
			echo '<input type="hidden"  name="property_type" value="'.$type_id.'" />';
		}
		?>
		<input type="hidden" name="orderby" id="orderby" value="<?php echo $lists['orderby']?>"/>
		<input type="hidden" name="sortby" id="sortby" value="<?php echo $lists['sortby']?>"/>

		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> locatorpagevertical margintop10">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> alignleft">
				<input type="text" name="location" id="location" class="input-large form-control" value="<?php echo stripslashes($lists['location']);?>" placeholder="<?php echo Text::_('OS_SEARCH_ADDRESS_EXPLAIN')?>" />
				<?php echo $lists['radius']; ?>
				<BR />
				<?php
				if($configClass['locator_show_category'] == 1){
				?>

					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<?php echo Text::_('OS_CATEGORIES'); ?>
						</div>

						<div class="<?php echo $controlClass; ?>">
							<?php
							echo $lists['category'];
							?>
						</div>
					</div>

				<?php } ?>
				<?php
				$locator_type_idArrs = $lists['locator_type_idArrs'];
				if(($locator_type_idArrs[0] == 0) and ($configClass['locator_show_type'] == 1)){
					?>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<?php echo Text::_('OS_TYPE'); ?>
						</div>
						<div class="<?php echo $controlClass; ?>">
							<?php
							echo $lists['type'];
							?>
						</div>
					</div>
					<?php
				}
				?>
				<BR />
				<?php
				if($configClass['active_market_status'] == 1){
					?>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<?php echo Text::_('OS_MARKET_STATUS')?>
						</div>
						<div class="<?php echo $controlClass; ?>">
							<?php
							echo $lists['market_status'];
							?>
						</div>
					</div>
					<?php
				}
				?>
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>"><?php echo Text::_('OS_PRICE')?>
					</div>
					<div class="<?php echo $controlClass; ?>">
						<?php
						OSPHelper::showPriceFilter($lists['price_value'],$lists['min_price'],$lists['max_price'],$lists['locator_type'],'','adv');
						?>
					</div>
				</div>
				<?php
				if($configClass['use_bedrooms'] == 1){
					?>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<?php echo Text::_('OS_BEDS')?>
						</div>
						<div class="<?php echo $controlClass; ?>">
							<?php echo $lists['nbed'];?>
						</div>
					</div>
				<?php } ?>
				<?php
				if($configClass['use_bedrooms'] == 1){
					?>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<?php echo Text::_('OS_BATHS')?>
						</div>
						<div class="<?php echo $controlClass; ?>">
							<?php echo $lists['nbath'];?>
						</div>
					</div>
				<?php } ?>
				<BR />
				<button type="button" onclick="javascript:checkingLocatorForm();" id="applylocatorform" class="btn btn-info"><i class="osicon-search"></i>&nbsp;<?php echo Text::_('OS_SHOW_ON_MAP'); ?></button>
			</div>
		</div>

	<?php
	}

	static function generateMembershipForm($agentAcc,$area,$pid){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		if(self::isAgent()){
		    $usertype = 0;
		}elseif(self::isCompanyAdmin()){
		    $usertype = 2;
		}
		$db = Factory::getDbo();
		$expired_time = self::getRealTime();
		$expired_feature_time = self::getRealTime();

		if($agentAcc[0] > 0){
		    $checked1 = "checked";
		    $checked2 = "";
		}elseif($agentAcc[1] > 0){
		    $checked1 = "";
		    $checked2 = "checked";
		}
		if($agentAcc[0] == 0)
        {
            $disabled1 = "disabled";
        }else{
            $disabled1 = "";
        }
        if($agentAcc[1] == 0)
        {
            $disabled2 = "disabled";
        }else{
            $disabled2 = "";
        }
		?>
		<table width="100%" class="table table-striped table-bordered membershiptable" id="membershiptable">
			<thead>
				<tr>
					<th width="30%" class="nowrap  paddingleft10 colorwhite">
						<?php echo Text::_('OS_PROPERTY_TYPE');?>
					</th>
					<th width="25%" class="nowrap  paddingleft10 colorwhite">
						<span class="hasTip" title="<?php echo Text::_('OS_ACCOUNT_REMAINING');?>::<?php echo Text::_('OS_ACCOUNT_REMAINING_EXPLAIN');?>">
							<?php echo Text::_('OS_ACCOUNT_REMAINING');?>
						</span>
					</th>
					<?php
					if($configClass['general_use_expiration_management'] == 1){
					?>
					<th width="45%" class="nowrap paddingleft10 colorwhite">
						<span class="hasTip" title="<?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON');?>::<?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON_EXPLAIN');?>">
							<?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON');?>
						</span>
					</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
			    <tr class="row0">
			        <td width="35%" class="paddingleft10" data-label="<?php echo Text::_('OS_PROPERTY_TYPE');?>">
			            <input type="radio" name="property_type" id="property_type" value="0" <?php echo $checked1;?> <?php echo $disabled1;?>/>
			            <?php

			            echo Text::_('OS_STANDARD_PROPERTY');
			            ?>
			        </td>
			        <td width="20%" class="paddingleft10 center" data-label="<?php echo Text::_('OS_ACCOUNT_REMAINING');?>">
						<?php echo (int)$agentAcc[0]; ?>
			        </td>
			        <?php
                    if($configClass['general_use_expiration_management'] == 1){
                    ?>
                        <td width="45%" class="paddingleft10" data-label="<?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON');?>">
                            <?php
                            echo OSPHelper::returnDateformat(self::getExpiredNormal($expired_time,1));
                            ?>
                        </td>
                    <?php } ?>
			    </tr>
			    <tr class="row1">
			        <td width="35%" class="paddingleft10" data-label="<?php echo Text::_('OS_PROPERTY_TYPE');?>">
			            <input type="radio" name="property_type" id="property_type" value="1" <?php echo $checked2;?> <?php echo $disabled2;?> />
			            <?php
			            echo Text::_('OS_FEATURED_PROPERTY');
			            ?>
			        </td>
			        <td width="20%" class="paddingleft10 center" data-label="<?php echo Text::_('OS_ACCOUNT_REMAINING');?>">
						<?php echo (int)$agentAcc[1]; ?>
						<?php
						if((int)$agentAcc[1] == 0){
                            $link = OspropertyMembership::generateLink($usertype,1,0);
                            ?>
                            <div class="clearfix"></div>
                            <a href="<?php echo $link;?>" title="<?php echo Text::_('OS_PURCHASE_SUBSCRIPTION');?>"><?php echo Text::_('OS_PURCHASE_SUBSCRIPTION');?></a>
                            <?php
						}
						?>
			        </td>
			        <?php
                    if($configClass['general_use_expiration_management'] == 1){
                    ?>
                        <td width="45%" class="paddingleft10" data-label="<?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON');?>">
                            <?php
                            echo OSPHelper::returnDateformat(self::getExpiredFeature($expired_feature_time,1));
                            ?>
                        </td>
                    <?php } ?>
			    </tr>
            </tbody>
		</table>
		<?php
	}


	static function generateMembershipFormUpgradeProperties($agentAcc){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		$expired_time = self::getRealTime();
		$expired_feature_time = self::getRealTime();
		?>
		<table width="100%" class="plantable">
		    <thead>
                <tr>
                    <th width="50%" class="paddingleft20 header_td">
                        <span class="hasTip" title="<?php echo Text::_('OS_ACCOUNT_REMAINING');?>::<?php echo Text::_('OS_ACCOUNT_REMAINING_EXPLAIN');?>">
                            <?php echo Text::_('OS_ACCOUNT_REMAINING');?>
                        </span>
                    </th>
                    <?php
					if($configClass['general_use_expiration_management'] == 1){
					?>
					<th width="45%" class="nowrap header_td">
						<span class="hasTip" title="<?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON');?>::<?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON_EXPLAIN');?>">
							<?php echo Text::_('OS_PROPERTY_WILL_EXPIRED_ON');?>
						</span>
					</th>
					<?php } ?>
                </tr>
			</thead>
			<tbody>
                <tr>
                    <td width="50%" class="paddingleft20 center">
                        <?php echo $agentAcc; ?>
                    </td>
                    <?php
					if($configClass['general_use_expiration_management'] == 1){
					?>
                        <td width="50%" class="paddingleft20 center">
                        <?php
                            echo OSPHelper::returnDateformat(self::getExpiredFeature($expired_feature_time,1));
                        ?>
                        </td>
					<?php } ?>
                </tr>
			</tbody>
		</table>
		<?php
	}
	/**
	 * Compare expired time and feature expired time, if the feature expired time is longer than expired time
	 * Update expired time = feature expired time
	 *
	 * @param unknown_type $pid
	 */
	static function adjustExpiredTime($pid){
		global $bootstrapHelper, $jinput, $mainframe;
		$configClass = OSPHelper::loadConfig();
		$db = Factory::getDbo();
		$db->setQuery("Select isFeatured from #__osrs_properties where id = '$pid'");
		$isFeatured = $db->loadResult();
		if($isFeatured == 1){
			$db->setQuery("Select * from #__osrs_expired where pid = '$pid'");
			$expired = $db->loadObject();
			$expired_time = intval(strtotime($expired->send_expired));
			$expired_feature_time = intval(strtotime($expired->expired_feature_time));
			if($expired_feature_time > $expired_time){
				$expired_time = $expired_feature_time;
			}

			$unpublish_time = $expired_time;
			$remove_time	= intval($configClass['general_unpublished_days']);
			$send_appro		= $configClass['send_approximates'];
			$appro_days		= intval($configClass['approximates_days']);

			$send_appro		= $configClass['send_approximates'];
			$appro_days		= $configClass['approximates_days'];
			//allow to send the approximates expired day
			if($send_appro == 1){
				$inform_time = $unpublish_time - $appro_days*24*3600;
				$inform_time = date("Y-m-d H:i:s",$inform_time);
			}else{
				$inform_time = "";
			}
			$remove_time    = $unpublish_time + $remove_time*24*3600;
			$remove_time	= date("Y-m-d H:i:s",$remove_time);
			$unpublish_time = date("Y-m-d H:i:s",$unpublish_time);
			//insert into #__osrs_expired
			$db->setQuery("UPDATE #__osrs_expired SET inform_time = '$inform_time',expired_time='$unpublish_time',remove_from_database='$remove_time' WHERE pid = '$pid'");
			$db->execute();
		}
	}
	
	/**
	 * List of extra fields
	 *
	 */
	static function getExtrafieldInList(){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		$db = Factory::getDBO();
		$user = Factory::getUser();
		$query = "";
		$query .= ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
		$db->setQuery("Select * from #__osrs_extra_fields where published = '1' $query and show_on_list = '1' order by ordering");
		$rows = $db->loadObjectList();
		return $rows;
	}

    /**
     * Sharing form
     */
    public static function sharingForm($row,$itemid){
		global $bootstrapHelper; 
		$configClass = OSPHelper::loadConfig();
		$user = Factory::getUser();
        ?>
        <div class="leadFormWrap">
            <form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_submittellfriend&Itemid=<?php echo $itemid?>" name="tellfriend_form" id="tellfriend_form" class="form-horizontal">
                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_FRIEND_NAME');?></label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <input class="input-large form-control ilarge" type="text" id="friend_name" name="friend_name" maxlength="50" placeholder="<?php echo Text::_('OS_FRIEND_NAME');?>"/>
                    </div>
                </div>

                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_FRIEND_EMAIL');?></label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <input class="input-large form-control ilarge" type="text" id="friend_email" name="friend_email" maxlength="50" placeholder="<?php echo Text::_('OS_FRIEND_EMAIL');?>" />
                    </div>
                </div>

                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_YOUR_NAME');?></label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <input class="input-large form-control ilarge" type="text" id="your_name" name="your_name" maxlength="50" placeholder="<?php echo Text::_('OS_YOUR_NAME');?>" value="<?php echo $user->name; ?>" />
                    </div>
                </div>

                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_YOUR_EMAIL');?></label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <input type="text" id="your_email" name="your_email" maxlength="50" class="input-large form-control ilarge" placeholder="<?php echo Text::_('OS_YOUR_EMAIL');?>" value="<?php echo $user->email; ?>"/>
                    </div>
                </div>

                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_MESSAGE');?></label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <textarea id="message" name="message" rows="3" cols="50" class="input-large form-control ilarge"></textarea>
                    </div>
                </div>
                <?php
                $captcha = 0;
                if($configClass['captcha_in_tell_friend_form'] == 1){
                    $captcha = 1;
                    if($user->id > 0 && $configClass['pass_captcha_with_logged_user'] == 1){
                        $captcha = 0;
                    }
                }
                if($captcha == 1) {
                    ?>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                        <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_SECURITY_CODE'); ?></label>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                            <span class="grey_small lineheight16"><?php echo Text::_('OS_PLEASE_INSERT_THE_SYMBOL_FROM_THE_INAGE_TO_FIELD_BELOW') ?></span>
                            <div class="clear"></div>
                            <img src="<?php echo Uri::root() ?>index.php?option=com_osproperty&no_html=1&task=property_captcha&ResultStr=<?php echo $row->ResultStr ?>"/>
                            <input type="text" class="input-mini form-control imini" id="sharing_security_code" name="sharing_security_code" maxlength="5"/>
                        </div>
                    </div>
                    <?php
                }
                if ($configClass['use_privacy_policy'])
                {
                    if ($configClass['privacy_policy_article_id'] > 0)
                    {
                        $privacyArticleId = $configClass['privacy_policy_article_id'];

                        if (Multilanguage::isEnabled())
                        {
                            $associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $privacyArticleId);
                            $langCode     = Factory::getLanguage()->getTag();
                            if (isset($associations[$langCode]))
                            {
                                $privacyArticle = $associations[$langCode];
                            }
                        }

                        if (!isset($privacyArticle))
                        {
                            $db    = Factory::getDbo();
                            $query = $db->getQuery(true);
                            $query->select('id, catid')
                                ->from('#__content')
                                ->where('id = ' . (int) $privacyArticleId);
                            $db->setQuery($query);
                            $privacyArticle = $db->loadObject();
                        }

                        JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

                        $link = Route::_(ContentHelperRoute::getArticleRoute($privacyArticle->id, $privacyArticle->catid).'&tmpl=component&format=html');
                    }
                    else
                    {
                        $link = '';
                    }
                    ?>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                        <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                            <?php
                            if ($link)
                            {
                                $extra = ' class="osmodal" ' ;
                                ?>
                                <a href="<?php echo $link; ?>" <?php echo $extra;?> class="eb-colorbox-privacy-policy"><?php echo Text::_('OS_PRIVACY_POLICY');?></a>
                                <?php
                            }
                            else
                            {
                                echo Text::_('OS_PRIVACY_POLICY');
                            }
                            ?>
                        </label>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                            <input type="checkbox" name="agree_privacy_policy" id="agree_privacy_policy" value="1" data-errormessage="<?php echo Text::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>" />
                            <?php
                            $agreePrivacyPolicyMessage = Text::_('OS_AGREE_PRIVACY_POLICY_MESSAGE');
                            if (strlen($agreePrivacyPolicyMessage))
                            {
                                ?>
                                <div class="eb-privacy-policy-message alert alert-info"><?php echo $agreePrivacyPolicyMessage;?></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="clear"></div>

                <button class="btn btn-primary" type="button" name="finish" onclick="javascript:submitForm('tellfriend_form');"/><?php echo Text::_('OS_SEND');?></button>
                <span class="reg_loading" id="tf_loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <input type="hidden" name="captcha_str" id="captcha_str" value="<?php echo $row->ResultStr?>" />
                <input type="hidden" name="option" value="com_osproperty" />
                <input type="hidden" name="task" value="property_submittellfriend" />
                <input type="hidden" name="id" value="<?php echo $row->id;?>" />
                <input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />
                <?php
                if($captcha == 1){
                    $request_field = ",sharing_security_code";
                    $request_label = ",".Text::_('OS_SECURITY_CODE');
                }
                if($configClass['use_privacy_policy']){
                    ?>
                    <input type="hidden" name="require_field" value="friend_name,friend_email<?php echo $request_field;?>,agree_privacy_policy" />
                    <input type="hidden" name="require_label" value="<?php echo Text::_('OS_FRIEND_NAME');?>,<?php echo Text::_('OS_FRIEND_EMAIL');?>,<?php echo Text::_('OS_SECURITY_CODE')?>,<?php echo Text::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>" />
                    <?php
                }else{
                ?>
                    <input type="hidden" name="require_field" value="friend_name,friend_email,sharing_security_code" />
                    <input type="hidden" name="require_label" value="<?php echo Text::_('OS_FRIEND_NAME');?>,<?php echo Text::_('OS_FRIEND_EMAIL');?><?php echo $request_label;?>" />
                <?php
                }
                ?>
                <input type="hidden" name="use_privacy_policy" id="use_privacy_policy" value="<?php echo $configClass['use_privacy_policy']?>" />
            </form>
        </div>
        <?php
    }

    /**
     * Show request More details form
     * @param $row
     * @param $item
     */
    public static function requestMoreDetails($row,$itemid){
		global $bootstrapHelper;
		//HTMLHelper::_('behavior.calendar');
		$db                     = Factory::getDbo();
		$configClass		    = OSPHelper::loadConfig();
		$user				    = Factory::getUser();
		$allowed_subjects	    = trim($configClass['allowed_subjects']);
		$allowed_subjects_array = array();
		if($allowed_subjects != ""){
			$allowed_subjects_array   = explode(",",$allowed_subjects);
		}
        ?>
        <div class="leadFormWrap">
            <form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_requestmoredetails&Itemid=<?php echo $itemid?>" name="requestdetails_form" id="requestdetails_form" class="form-horizontal">
				<?php
				if(count($allowed_subjects_array) > 0){
				?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
						<?php echo Text::_('OS_SUBJECT');?>
						</label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<select name='subject' id='subject' class='input-large form-select ilarge' onchange="javascript:updateRequestForm(this.value)">
								<?php
								$firstoption = array();
								for($i=1;$i<=7;$i++){
									if(in_array($i,$allowed_subjects_array)){
										$firstoption[count($firstoption)] = $i;
										?>
										<option value="<?php echo $i?>"><?php echo Text::_('OS_REQUEST_'.$i)?></option>
										<?php
									}
								}
								$firstoption = $firstoption[0];
								?>
							</select>
						</div>
					</div>
				<?php } 
				if($firstoption == 7){
					$div_requestmessage = "";
					$div_requestcheckin = "";
					$div_requestcheckout = "";
					$div_requestguests = "";
				}else{
					$div_requestmessage = "";
					$div_requestcheckin = "nodisplay";
					$div_requestcheckout = "nodisplay";
					$div_requestguests = "nodisplay";
				}
				?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                        <?php echo Text::_('OS_YOUR_NAME');?>
                    </label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <input class="input-large form-control ilarge" type="text" id="requestyour_name" name="requestyour_name" size="30" maxlength="50"  value="<?php echo $user->name?>" placeholder="<?php echo Text::_('OS_YOUR_NAME')?>"/>
                    </div>
                </div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                        <?php echo Text::_('OS_PHONE');?>
                    </label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <input class="input-large form-control ilarge" type="text" id="your_phone" name="your_phone" maxlength="50" placeholder="<?php echo Text::_('OS_PHONE')?>"/>
                    </div>
                </div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                        <?php echo Text::_('OS_YOUR_EMAIL');?>
                    </label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <input class="input-large form-control ilarge" type="text" id="requestyour_email" name="requestyour_email" size="30" maxlength="50"  value="<?php echo $user->email;?>" placeholder="<?php echo Text::_('OS_YOUR_EMAIL')?>"/>
                    </div>
                </div>

				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?> <?php echo $div_requestcheckin;?>" id="requestcheckin">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                        <?php echo Text::_('OS_CHECKIN');?>
                    </label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <?php echo HTMLHelper::_('calendar','','requestmoredetailscheckin','requestmoredetailscheckin',"%Y-%m-%d",array('class'=>'input-medium form-control'));?>
                    </div>
                </div>

				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?> <?php echo $div_requestcheckout;?>" id="requestcheckout">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                        <?php echo Text::_('OS_CHECKOUT');?>
                    </label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <?php echo HTMLHelper::_('calendar','','requestmoredetailscheckout','requestmoredetailscheckout',"%Y-%m-%d",array('class'=>'input-medium form-control'));?>
                    </div>
                </div>

				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?> <?php echo $div_requestguests;?>" id="requestguests">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                        <?php echo Text::_('OS_NGUEST');?>
                    </label>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <input class="input-large form-control imedium" type="text" id="nguest" name="nguest" placeholder="<?php echo Text::_('OS_NGUEST')?>"/>
                    </div>
                </div>

				<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>" id="requestmessagediv">
                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                        <?php echo Text::_('OS_MESSAGE');?>
                    </label>
					<?php
					$message = Text::_('OS_REQUEST_MSG'.$firstoption);
					?>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                        <textarea class="input-large form-control ilarge" id="requestmessage" name="requestmessage" rows="3" cols="60"><?php echo $message;?> <?php echo ($row->ref != "")? $row->ref.", ":""?><?php echo $row->pro_name?></textarea>
                    </div>
                </div>
                <?php
                $passcaptcha        = 0;
                $googlecaptcha      = 0;
                $captcha            = 0;
                if($configClass['pass_captcha_with_logged_user'] == 1 && $user->id > 0){
                    $passcaptcha    = 1;
                }
                if($configClass['captcha_in_request_more_details'] == 1 && $passcaptcha == 0) {
                    $captcha = 1;
                    ?>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                        <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                            <?php echo Text::_('OS_HUMAN_VERIFICATION'); ?>
                        </label>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                            <?php
                            if($configClass['user_recaptcha_in_request_more_details'] == 1)
							{
                                $captchaPlugin = Factory::getApplication()->getParams()->get('captcha', Factory::getConfig()->get('captcha'));
                                if ($captchaPlugin)
                                {
                                    $googlecaptcha = 1;
                                    echo Captcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
                                }
                            }else {
                                ?>
                                <span class="grey_small lineheight16"><?php echo Text::_('OS_PLEASE_INSERT_THE_SYMBOL_FROM_THE_INAGE_TO_FIELD_BELOW') ?></span>
                                <div class="clearfix"></div>
                                <img src="<?php echo Uri::root() ?>index.php?option=com_osproperty&no_html=1&task=property_captcha&ResultStr=<?php echo $row->ResultStr ?>"/>
                                <input type="text" class="input-mini form-control imini" id="request_security_code" name="request_security_code" maxlength="5"/>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
				if($configClass['request_term_condition'] == 1)
				{
					OSPHelperJquery::colorbox('a.osmodal');
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?> center">
						<input type="checkbox" name="termcondition" id="termcondition" value="1" />
						&nbsp;
						<?php echo Text::_('OS_READ_TERM'); ?> 
						<a href="<?php echo Uri::root()?>index.php?option=com_content&view=article&id=<?php echo $configClass['request_article_id'];?>&tmpl=component" class="osmodal" rel="{handler: 'iframe', size: {x: 600, y: 450}}" title="<?php echo Text::_('OS_TERM_AND_CONDITION');?>"><?php echo Text::_('OS_TERM_AND_CONDITION');?></a>
					</div>
					<?php 
				}
				if ($configClass['use_privacy_policy'])
                {
                    if ($configClass['privacy_policy_article_id'] > 0)
                    {
                        $privacyArticleId = $configClass['privacy_policy_article_id'];

                        if (Multilanguage::isEnabled())
                        {
                            $associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $privacyArticleId);
                            $langCode     = Factory::getLanguage()->getTag();
                            if (isset($associations[$langCode]))
                            {
                                $privacyArticle = $associations[$langCode];
                            }
                        }

                        if (!isset($privacyArticle))
                        {
                            $db    = Factory::getDbo();
                            $query = $db->getQuery(true);
                            $query->select('id, catid')
                                ->from('#__content')
                                ->where('id = ' . (int) $privacyArticleId);
                            $db->setQuery($query);
                            $privacyArticle = $db->loadObject();
                        }

                        JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

                        $link = Route::_(ContentHelperRoute::getArticleRoute($privacyArticle->id, $privacyArticle->catid).'&tmpl=component&format=html');
                    }
                    else
                    {
                        $link = '';
                    }
                    ?>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                        <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                            <?php
                            if ($link)
                            {
                                $extra = ' class="osmodal" ' ;
                                ?>
                                <a href="<?php echo $link; ?>" <?php echo $extra;?> class="eb-colorbox-privacy-policy"><?php echo Text::_('OS_PRIVACY_POLICY');?></a>
                                <?php
                            }
                            else
                            {
                                echo Text::_('OS_PRIVACY_POLICY');
                            }
                            ?>
                        </label>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                            <input type="checkbox" name="agree_privacy_policy" id="agree_privacy_policy" value="1" data-errormessage="<?php echo Text::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>" />
                            <?php
                            $agreePrivacyPolicyMessage = Text::_('OS_AGREE_PRIVACY_POLICY_MESSAGE');
                            if (strlen($agreePrivacyPolicyMessage))
                            {
                                ?>
                                <div class="eb-privacy-policy-message alert alert-info"><?php echo $agreePrivacyPolicyMessage;?></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
				?>
                <div class="clearfix"></div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                    <button class="btn btn-primary" type="button" id="requestbutton" name="requestbutton" onclick="javascript:submitForm('requestdetails_form');"/><?php echo Text::_("OS_REQUEST_BUTTON1")?></button>
                    <input type="hidden" name="csrqt<?php echo intval(date("m",time()))?>" id="csrqt<?php echo intval(date("m",time()))?>" value="<?php echo $row->ResultStr?>" />
                    <input type="hidden" name="captcha_str" id="captcha_str" value="<?php echo $row->ResultStr?>" />
                </div>
                <input type="hidden" name="option" value="com_osproperty" />
                <input type="hidden" name="task" value="property_requestmoredetails" />
                <input type="hidden" name="id" value="<?php echo $row->id?>" />
                <input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />
                <?php
                if($googlecaptcha == 0 && $passcaptcha == 0 && $captcha == 1){
                    $request_field = ",request_security_code";
                    $request_label = ",".Text::_('OS_SECURITY_CODE');
                }
                if($configClass['use_privacy_policy'] == 1){
                    ?>
                    <input type="hidden" name="require_field" value="requestyour_name,requestyour_email<?php echo $request_field; ?>,agree_privacy_policy" />
                    <input type="hidden" name="require_label" value="<?php echo Text::_('OS_YOUR_NAME');?>,<?php echo Text::_('OS_YOUR_EMAIL');?><?php echo $request_label?>,<?php echo Text::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>" />
                    <?php
                }else{
                    ?>
                    <input type="hidden" name="require_field" value="requestyour_name,requestyour_email<?php echo $request_field; ?>" />
                    <input type="hidden" name="require_label" value="<?php echo Text::_('OS_YOUR_NAME');?>,<?php echo Text::_('OS_YOUR_EMAIL');?><?php echo $request_label; ?>" />
                <?php } ?>
                <input type="hidden" name="use_privacy_policy" id="use_privacy_policy" value="<?php echo $configClass['use_privacy_policy']?>" />
            </form>
			<?php
			$property_name = "";
			if(($row->ref != "") and ($configClass['show_ref'] == 1)){
				$property_name = $row->ref.", ".$row->pro_name;
			}else{
				$property_name = $row->pro_name;
			}
			?>
			<script type="text/javascript">
				 function updateRequestForm(subject){
					var message = document.getElementById('requestmessage');
					var requestbutton = document.getElementById('requestbutton');
					if(subject != 7){
						jQuery('#requestcheckin').hide();
						jQuery('#requestcheckout').hide();
						jQuery('#requestguests').hide();
						jQuery('#requestcheckin').addClass('nodisplay');
						jQuery('#requestcheckout').addClass('nodisplay');
						jQuery('#requestguests').addClass('nodisplay');
					}
					if(subject == 1){
						message.value = "<?php echo Text::_('OS_REQUEST_MSG1')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON1')?>";
					}else if(subject == 2){
						message.value = "<?php printf(Text::_('OS_REQUEST_MSG2'),$property_name);?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON2')?>";
					}else if(subject == 3){
						message.value = "<?php echo Text::_('OS_REQUEST_MSG3')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON3')?>";
					}else if(subject == 4){
						message.value = "<?php echo Text::_('OS_REQUEST_MSG4')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON4')?>";
					}else if(subject == 5){
						message.value = "<?php echo Text::_('OS_REQUEST_MSG5')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON5')?>";
					}else if(subject == 6){
						message.value = "<?php echo Text::_('OS_REQUEST_MSG6')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON6')?>";
					}else if(subject == 7){
						jQuery('#requestcheckin').removeClass('nodisplay');
						jQuery('#requestcheckout').removeClass('nodisplay');
						jQuery('#requestguests').removeClass('nodisplay');
						jQuery('#requestcheckin').show();
						jQuery('#requestcheckout').show();
						jQuery('#requestguests').show();
						message.value = "<?php echo Text::_('OS_REQUEST_MSG7')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON7')?>";
					}
				}
			</script>
        </div>
        <?php
    }

    /**
     * Show request More details form
     * @param $row
     * @param $item
     */
    public static function requestMoreDetailsTop($row,$itemid,$classname='input-medium form-control ilarge'){
		global $bootstrapHelper;
		$configClass = OSPHelper::loadConfig();
		$user = Factory::getUser();
		$allowed_subjects	= trim($configClass['allowed_subjects']);
		$allowed_subjects_array = array();
		if($allowed_subjects != ""){
			$allowed_subjects_array   = explode(",",$allowed_subjects);
		}
        ?>
        <div class="leadFormWrap">
            <form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_requestmoredetails&Itemid=<?php echo $itemid?>" name="requestdetails_form" id="requestdetails_form">
                <div class="_leadError ajax-error"></div>
				<?php
				if(count($allowed_subjects_array) > 0){
				?>
                    <select name='subject' id='subject' class='<?php echo $classname;?> form-select' onchange="javascript:updateRequestForm(this.value)">
                        <?php
                        $firstoption = array();
                        for($i=1;$i<=7;$i++){
                            if(in_array($i,$allowed_subjects_array)){
                                $firstoption[count($firstoption)] = $i;
                                ?>
                                <option value="<?php echo $i?>"><?php echo Text::_('OS_REQUEST_'.$i)?></option>
                                <?php
                            }
                        }
                        $firstoption = $firstoption[0];
                        ?>
                    </select>
				<?php } 
				if($firstoption == 7){
					$div_requestcheckin = "";
					$div_requestcheckout = "";
					$div_requestguests = "";
				}else{
					$div_requestcheckin = "nodisplay";
					$div_requestcheckout = "nodisplay";
					$div_requestguests = "nodisplay";
				}
				?>
                <input class="<?php echo $classname;?>" type="text" id="requestyour_name" name="requestyour_name" size="30" maxlength="50"  value="<?php echo $user->name?>" placeholder="<?php echo Text::_('OS_YOUR_NAME')?>"/>
                <input class="<?php echo $classname;?>" type="text" id="your_phone" name="your_phone" maxlength="50" placeholder="<?php echo Text::_('OS_PHONE')?>"/>
                <input class="<?php echo $classname;?>" type="text" id="requestyour_email" name="requestyour_email" size="30" maxlength="50"  value="<?php echo $user->email;?>" placeholder="<?php echo Text::_('OS_YOUR_EMAIL')?>"/>
				<span id="requestcheckin" class="<?php echo $div_requestcheckin;?>">
					<?php echo HTMLHelper::_('calendar','','requestmoredetailscheckin','requestmoredetailscheckin',"%Y-%m-%d",array('class'=>'input-small form-control','placeholder'=>Text::_('OS_CHECKIN')));?>
				</span>
				<span id="requestcheckout" class="<?php echo $div_requestcheckout;?>">
					<?php echo HTMLHelper::_('calendar','','requestmoredetailscheckout','requestmoredetailscheckout',"%Y-%m-%d",array('class'=>'input-small form-control','placeholder'=>Text::_('OS_CHECKOUT')));?>
                </span>
				<span id="requestguests" class="<?php echo $div_requestguests;?>">
                     <input class="input-medium form-control" type="text" id="nguest" name="nguest" placeholder="<?php echo Text::_('OS_NGUEST')?>"/>
                </span>
				<span id="requestmessagediv" class="<?php echo $div_requestmessage;?>">
					<?php
					$message = Text::_('OS_REQUEST_MSG'.$firstoption);
					?>
					<textarea class="<?php echo $classname;?>" id="requestmessage" name="requestmessage" cols="60"><?php echo Text::_('OS_REQUEST_MSG1')?> <?php echo (($row->ref != "") and ($configClass['show_ref'] == 1))? $row->ref.", ":""?><?php echo $row->pro_name?></textarea>
				</span>
                <div class="clearfix"></div>
                <?php
                $passcaptcha        = 0;
                $googlecaptcha      = 0;
                $captcha            = 0;
                if($configClass['pass_captcha_with_logged_user'] == 1 && $user->id > 0){
                    $passcaptcha    = 1;
                }
                if($configClass['captcha_in_request_more_details'] == 1 && $passcaptcha == 0) {
                    $captcha = 1;
                    if($configClass['user_recaptcha_in_request_more_details'] == 1){
                        $captchaPlugin = Factory::getApplication()->getParams()->get('captcha', Factory::getConfig()->get('captcha'));
                        if ($captchaPlugin)
                        {
                            $googlecaptcha = 1;
                            echo Captcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
                        }
                    }else {
                        ?>
                        <span class="grey_small lineheight16"><?php echo Text::_('OS_PLEASE_INSERT_THE_SYMBOL_FROM_THE_INAGE_TO_FIELD_BELOW') ?></span>
                        <div class="clearfix"></div>
                        <img src="<?php echo Uri::root() ?>index.php?option=com_osproperty&no_html=1&task=property_captcha&ResultStr=<?php echo $row->ResultStr ?>"/>
                        <input type="text" class="input-mini form-control imini" id="request_security_code" name="request_security_code"
                               maxlength="5"/>
                        <?php
                    }
                }
				if($configClass['request_term_condition'] == 1)
				{
					OSPHelperJquery::colorbox('a.osmodal');
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?> alignleft">
						<input type="checkbox" name="termcondition" id="termcondition" value="1" />
						&nbsp;
						<?php echo Text::_('OS_READ_TERM'); ?> 
						<a href="<?php echo Uri::root()?>index.php?option=com_content&view=article&id=<?php echo $configClass['request_article_id'];?>&tmpl=component" class="osmodal" rel="{handler: 'iframe', size: {x: 600, y: 450}}" title="<?php echo Text::_('OS_TERM_AND_CONDITION');?>"><?php echo Text::_('OS_TERM_AND_CONDITION');?></a>
					</div>
					<?php 
				}
				if ($configClass['use_privacy_policy'])
                {
                    if ($configClass['privacy_policy_article_id'] > 0)
                    {
                        $privacyArticleId = $configClass['privacy_policy_article_id'];

                        if (Multilanguage::isEnabled())
                        {
                            $associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $privacyArticleId);
                            $langCode     = Factory::getLanguage()->getTag();
                            if (isset($associations[$langCode]))
                            {
                                $privacyArticle = $associations[$langCode];
                            }
                        }

                        if (!isset($privacyArticle))
                        {
                            $db    = Factory::getDbo();
                            $query = $db->getQuery(true);
                            $query->select('id, catid')
                                ->from('#__content')
                                ->where('id = ' . (int) $privacyArticleId);
                            $db->setQuery($query);
                            $privacyArticle = $db->loadObject();
                        }

                        JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

                        $link = Route::_(ContentHelperRoute::getArticleRoute($privacyArticle->id, $privacyArticle->catid).'&tmpl=component&format=html');
                    }
                    else
                    {
                        $link = '';
                    }
                    ?>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                        <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                            <?php
                            if ($link)
                            {
                                $extra = ' class="osmodal" ' ;
                                ?>
                                <a href="<?php echo $link; ?>" <?php echo $extra;?> class="eb-colorbox-privacy-policy"><?php echo Text::_('OS_PRIVACY_POLICY');?></a>
                                <?php
                            }
                            else
                            {
                                echo Text::_('OS_PRIVACY_POLICY');
                            }
                            ?>
                            <input type="checkbox" name="agree_privacy_policy" id="agree_privacy_policy" value="1" data-errormessage="<?php echo Text::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>" />
                            <?php
                            $agreePrivacyPolicyMessage = Text::_('OS_AGREE_PRIVACY_POLICY_MESSAGE');
                            if (strlen($agreePrivacyPolicyMessage))
                            {
                                ?>
                                <div class="eb-privacy-policy-message alert alert-info"><?php echo $agreePrivacyPolicyMessage;?></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
				?>
				<div class="clearfix"></div>
                <input class="btn btn-info" type="button" id="requestbutton" name="requestbutton" value="<?php echo Text::_("OS_REQUEST_BUTTON1")?>" onclick="javascript:submitForm('requestdetails_form');"/>
                <input type="hidden" name="csrqt<?php echo intval(date("m",time()))?>" id="csrqt<?php echo intval(date("m",time()))?>" value="<?php echo $row->ResultStr?>" />
                <input type="hidden" name="captcha_str" id="captcha_str" value="<?php echo $row->ResultStr?>" />
                <input type="hidden" name="option" value="com_osproperty" />
                <input type="hidden" name="task" value="property_requestmoredetails" />
                <input type="hidden" name="id" value="<?php echo $row->id?>" />
                <input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />
                <?php
                if($googlecaptcha == 0 && $passcaptcha == 0 && $captcha == 1){
                    $request_field = ",request_security_code";
                    $request_label = ",".Text::_('OS_SECURITY_CODE');
                }
                if($configClass['use_privacy_policy'] == 1){
                   ?>
                    <input type="hidden" name="require_field" value="requestyour_name,requestyour_email<?php echo $request_field; ?>,agree_privacy_policy" />
                    <input type="hidden" name="require_label" value="<?php echo Text::_('OS_YOUR_NAME');?>,<?php echo Text::_('OS_YOUR_EMAIL');?><?php echo $request_label?>,<?php echo Text::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>" />
                    <?php
                }else{
                ?>
                    <input type="hidden" name="require_field" value="requestyour_name,requestyour_email<?php echo $request_field; ?>" />
                    <input type="hidden" name="require_label" value="<?php echo Text::_('OS_YOUR_NAME');?>,<?php echo Text::_('OS_YOUR_EMAIL');?><?php echo $request_label; ?>" />
                <?php } ?>
                <input type="hidden" name="use_privacy_policy" id="use_privacy_policy" value="<?php echo $configClass['use_privacy_policy']?>" />
            </form>
            <?php
			$property_name = "";
			if(($row->ref != "") and ($configClass['show_ref'] == 1)){
				$property_name = $row->ref.", ".$row->pro_name;
			}else{
				$property_name = $row->pro_name;
			}
			?>
			<script type="text/javascript">
				 function updateRequestForm(subject){
					var message = document.getElementById('requestmessage');
					var requestbutton = document.getElementById('requestbutton');
					if(subject != 7){
						jQuery('#requestcheckin').addClass('nodisplay');
						jQuery('#requestcheckout').addClass('nodisplay');
						jQuery('#requestguests').addClass('nodisplay');
						jQuery('#requestcheckin').hide();
						jQuery('#requestcheckout').hide();
						jQuery('#requestguests').hide();
					}
					if(subject == 1){
						message.value = "<?php echo Text::_('OS_REQUEST_MSG1')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON1')?>";
					}else if(subject == 2){
						message.value = "<?php printf(Text::_('OS_REQUEST_MSG2'),$property_name);?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON2')?>";
					}else if(subject == 3){
						message.value = "<?php echo Text::_('OS_REQUEST_MSG3')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON3')?>";
					}else if(subject == 4){
						message.value = "<?php echo Text::_('OS_REQUEST_MSG4')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON4')?>";
					}else if(subject == 5){
						message.value = "<?php echo Text::_('OS_REQUEST_MSG5')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON5')?>";
					}else if(subject == 6){
						message.value = "<?php echo Text::_('OS_REQUEST_MSG6')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON6')?>";
					}else if(subject == 7){
						jQuery('#requestcheckin').removeClass('nodisplay');
						jQuery('#requestcheckout').removeClass('nodisplay');
						jQuery('#requestguests').removeClass('nodisplay');
						jQuery('#requestcheckin').show();
						jQuery('#requestcheckout').show();
						jQuery('#requestguests').show();
						message.value = "<?php echo Text::_('OS_REQUEST_MSG7')?> <?php echo $property_name; ?>";
						requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON7')?>";
					}
				}
			</script>
        </div>
        <?php
    }

    /**
     * Review form
     */
    public static function reviewForm($row, $itemid, $configClass){
		global $bootstrapHelper;
		$user = Factory::getUser();
        ?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> commentform">
			<div class="block_caption" id="comment_form_caption">
				<strong><?php echo Text::_('OS_ADD_COMMENT')?></strong>
			</div>

			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
				<form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_submitcomment&Itemid=<?php echo $itemid;?>" name="commentForm" id="commentForm" class="form-horizontal">
					<?php
					if($configClass['show_rating'] == 1){
						?>
						<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
								<?php echo Text::_('OS_RATING');?>
							</label>
							<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
								<!--
								<i><?php echo Text::_('OS_WORST');?>
									&nbsp;
									<?php
									for($i=1;$i<=5;$i++){
										if($i==3){
											$checked = "checked";
										}else{
											$checked = "";
										}
										?>
										<input type="radio" name="rating" id="rating<?php echo $i?>" value="<?php echo $i?>" <?php echo $checked?> />
									<?php
									}
									?>
									&nbsp;&nbsp;<?php echo Text::_('OS_BEST');?></i>
									-->
								<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> paddingbottom10">
									<?php
									$rateOption = array();
									for($i=1;$i<=5;$i++){
										$rateOption[] = HTMLHelper::_('select.option',$i,$i);
									}
									for($i=1;$i<5;$i++){
									?>
										<div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
												<div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
													<strong><?php echo Text::_('OS_RATE_OPTION'.$i);?></strong>
												</div>
												<div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
													<?php
													echo HTMLHelper::_('select.genericlist',$rateOption,'rate'.$i ,'class="input-small form-select smallSizeBox"','value','text');
													?>
												</div>
											</div>
										</div>
									<?php 
										if($i==2){
											?>
											</div>
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
											<?php
										}
									} 
									?>
								</div>
							</div>
						</div>
					<?php
					}
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
							<?php echo Text::_('OS_AUTHOR');?>
						</label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<input class="input-large form-control" type="text" id="comment_author" name="comment_author" maxlength="50" value="<?php echo $user->name;?>" />
						</div>
					</div>

					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
							<?php echo Text::_('OS_TITLE');?>
						</label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<input class="input-large form-control" type="text" id="comment_title" name="comment_title" size="40" placeholder="<?php echo Text::_('OS_TITLE');?>" />
						</div>
					</div>

					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
							<?php echo Text::_('OS_MESSAGE');?>
						</label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<textarea id="comment_message" name="comment_message" rows="6" cols="50" class="input-large form-control"></textarea>
						</div>
					</div>

					<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
						<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
							<?php echo Text::_('OS_SECURITY_CODE');?>
						</label>
						<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							<span class="grey_small lineheight16"><?php echo Text::_('OS_PLEASE_INSERT_THE_SYMBOL_FROM_THE_INAGE_TO_FIELD_BELOW')?></span>
							<div class="clearfix"></div>
							<img src="<?php echo Uri::root()?>index.php?option=com_osproperty&no_html=1&task=property_captcha&ResultStr=<?php echo $row->ResultStr?>" />
							<input type="text" class="input-mini form-control imini" id="comment_security_code" name="comment_security_code" maxlength="5"/>
						</div>
					</div>

					<?php
					if ($configClass['use_privacy_policy'])
                    {
                        if ($configClass['privacy_policy_article_id'] > 0)
                        {
                            $privacyArticleId = $configClass['privacy_policy_article_id'];

                            if (Multilanguage::isEnabled())
                            {
                                $associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $privacyArticleId);
                                $langCode     = Factory::getLanguage()->getTag();
                                if (isset($associations[$langCode]))
                                {
                                    $privacyArticle = $associations[$langCode];
                                }
                            }

                            if (!isset($privacyArticle))
                            {
                                $db    = Factory::getDbo();
                                $query = $db->getQuery(true);
                                $query->select('id, catid')
                                    ->from('#__content')
                                    ->where('id = ' . (int) $privacyArticleId);
                                $db->setQuery($query);
                                $privacyArticle = $db->loadObject();
                            }

                            JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

                            $link = Route::_(ContentHelperRoute::getArticleRoute($privacyArticle->id, $privacyArticle->catid).'&tmpl=component&format=html');
                        }
                        else
                        {
                            $link = '';
                        }
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                            <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                <?php
                                if ($link)
                                {
                                    $extra = ' class="osmodal" ' ;
                                    ?>
                                    <a href="<?php echo $link; ?>" <?php echo $extra;?> class="eb-colorbox-privacy-policy"><?php echo Text::_('OS_PRIVACY_POLICY');?></a>
                                    <?php
                                }
                                else
                                {
                                    echo Text::_('OS_PRIVACY_POLICY');
                                }
                                ?>
                            </label>
                            <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                <input type="checkbox" name="agree_privacy_policy" id="agree_privacy_policy" value="1" data-errormessage="<?php echo Text::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>" />
                                <?php
                                $agreePrivacyPolicyMessage = Text::_('OS_AGREE_PRIVACY_POLICY_MESSAGE');
                                if (strlen($agreePrivacyPolicyMessage))
                                {
                                    ?>
                                    <div class="eb-privacy-policy-message alert alert-info"><?php echo $agreePrivacyPolicyMessage;?></div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
					?>
					<div class="clearfix"></div>
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<input onclick="javascript:submitForm('commentForm')" class="btn btn-warning margin0 width100px" type="button" name="finish" value="<?php echo Text::_('OS_SUBMIT')?>" />
						<span id="comment_loading" class="reg_loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
					</div>

					<input type="hidden" name="captcha_str" id="captcha_str" value="<?php echo $row->ResultStr?>" />
					<input type="hidden" name="option" value="com_osproperty" />
					<input type="hidden" name="task" value="property_submitcomment" />
					<input type="hidden" name="id" value="<?php echo $row->id?>" />
					<input type="hidden" name="Itemid" value="<?php echo $itemid?>" />

					<?php
                    if($configClass['use_privacy_policy']){
                    ?>
                        <input type="hidden" name="require_field"  value="comment_author,comment_title,comment_message,comment_security_code,agree_privacy_policy" />
                        <input type="hidden" name="require_label"  value="<?php echo Text::_('OS_AUTHOR');?>,<?php echo Text::_('OS_TITLE');?>,<?php echo Text::_('OS_MESSAGE');?>,<?php echo Text::_('OS_SECURITY_CODE')?>,<?php echo Text::_('OS_SECURITY_CODE')?>" />
                    <?php
                    }else{
                    ?>
                        <input type="hidden" name="require_field"  value="comment_author,comment_title,comment_message,comment_security_code" />
                        <input type="hidden" name="require_label"  value="<?php echo Text::_('OS_AUTHOR');?>,<?php echo Text::_('OS_TITLE');?>,<?php echo Text::_('OS_MESSAGE');?>,<?php echo Text::_('OS_SECURITY_CODE')?>" />
                    <?php } ?>
					<input type="hidden" name="use_privacy_policy" id="use_privacy_policy" value="<?php echo $configClass['use_privacy_policy']?>" />
				</form>
			</div>
		</div>
        <?php
    }

	static function showLabel($name, $title, $tooltip){
		$label = '';
		$text  = $title;

		// Build the class for the label.
		$class = !empty($tooltip) ? 'hasTooltip hasTip' : '';

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $name . '-lbl" for="' . $name . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($tooltip))
		{
			$label .= ' title="' . self::tooltipText(trim($text, ':'), $tooltip, 0) . '"';
		}

		$label .= '>' . $text . '</label>';

		return $label;
	}

	/**
	 * Converts a double colon seperated string or 2 separate strings to a string ready for bootstrap tooltips
	 *
	 * @param   string $title     The title of the tooltip (or combined '::' separated string).
	 * @param   string $content   The content to tooltip.
	 * @param   int    $translate If true will pass texts through JText.
	 * @param   int    $escape    If true will pass texts through htmlspecialchars.
	 *
	 * @return  string  The tooltip string
	 *
	 * @since   2.0.7
	 */
	public static function tooltipText($title = '', $content = '', $translate = 1, $escape = 1)
	{
		// Initialise return value.
		$result = '';

		// Don't process empty strings
		if ($content != '' || $title != '')
		{
			// Split title into title and content if the title contains '::' (old Mootools format).
			if ($content == '' && !(strpos($title, '::') === false))
			{
				list($title, $content) = explode('::', $title, 2);
			}

			// Pass texts through JText if required.
			if ($translate)
			{
				$title   = Text::_($title);
				$content = Text::_($content);
			}

			// Use only the content if no title is given.
			if ($title == '')
			{
				$result = $content;
			}
			// Use only the title, if title and text are the same.
			elseif ($title == $content)
			{
				$result = '<strong>' . $title . '</strong>';
			}
			// Use a formatted string combining the title and content.
			elseif ($content != '')
			{
				$result = '<strong>' . $title . '</strong><br />' . $content;
			}
			else
			{
				$result = $title;
			}

			// Escape everything, if required.
			if ($escape)
			{
				$result = htmlspecialchars($result);
			}
		}

		return $result;
	}

	/**
	 * Render showon string
	 *
	 * @param array $fields
	 *
	 * @return string
	 */
	public static function renderShowon($fields)
	{
		$output = array();

		$i = 0;

		foreach ($fields as $name => $values)
		{
			$i++;

			$values = (array) $values;

			$data = array(
				'field'  => $name,
				'values' => $values
			);

			if (version_compare(JVERSION, '3.6.99', 'ge'))
			{
				$data['sign'] = '=';
			}

			$data['op'] = $i > 1 ? 'AND' : '';

			$output[] = json_encode($data);
		}

		return '[' . implode(',', $output) . ']';
	}

	static function makeCityList($req_country_id,$req_state_id,$req_city_id,$name,$onChange,$firstOption,$style){
		global $configClass;
		$db = Factory::getDbo();
		$lgs = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($lgs);
		$suffix = "";
		if($translatable){
			$suffix = OSPHelper::getFieldSuffix();
		}
		$cityArr = array();
		if($req_state_id > 0){
			$query  = "Select id as value, city".$suffix." as text from #__osrs_cities where published = 1 ";
			$query .= " and state_id = '$req_state_id'";
			$query .= " order by city";
			$db->setQuery($query);
			$cities = $db->loadObjectList();
			if($firstOption != ""){
				$cityArr[] = HTMLHelper::_('select.option','',$firstOption);
				$cityArr   = array_merge($cityArr, $cities);
			}else{
				$cityArr   = $cities;
			}
			return HTMLHelper::_('select.genericlist',$cityArr, $name,' '.$onChange.' '.$style,'value','text',$req_city_id);
		}else{
			$cityArr[] = HTMLHelper::_('select.option','',$firstOption);
			return HTMLHelper::_('select.genericlist',$cityArr, $name,$style.' disabled','value','text');
		}
	}
}
?>
