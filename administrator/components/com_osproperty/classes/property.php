<?php

/*------------------------------------------------------------------------
# property.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Archive\Archive;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Version;

class OspropertyProperties
{
	static function display($option,$task)
    {
		global $jinput, $mainframe;
		$user = Factory::getUser();
		$db = Factory::getDbo();
		$query = $db->getQuery(TRUE);
		$query->update("#__osrs_properties")->set("published = '0'")->where("publish_down != '0000-00-00' and publish_down < '".date("Y-m-d",time())."'");
		$db->setQuery($query);
		$db->execute();
		
		$document = Factory::getDocument();
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/lib.js");
		$cid = $jinput->get('cid',array(),'ARRAY');
		switch ($task){
			case "properties_uploadzipfile":
				OspropertyProperties::uploadZipfile();
			break;
			case "properties_list":
				OspropertyProperties::properties_list($option);
			break;
			case "properties_saveorderAjax":
				OspropertyProperties::saveorderAjax($option);
			break;
			case "properties_add":
				OspropertyProperties::properties_edit($option,0);
			break;
			case "properties_edit":
				OspropertyProperties::properties_edit($option,$cid[0]);
			break;
			case "properties_showphotosinzipfile":
				OspropertyProperties::showphotosinzipfile($option);
			break;
			case "properties_save_photos":
				OspropertyProperties::savephotosinzipfile($option);
			break;
			case "properties_loadState":
				OspropertyProperties::loadStates($option);
			break;
			case "properties_gotolist":
				OspropertyProperties::gotolist($option);
			break;
			case "properties_save":
				OspropertyProperties::save($option,1);
			break;
			case "properties_apply":
				OspropertyProperties::save($option,0);
			break;
			case "properties_new":
				OspropertyProperties::save($option,2);
			break;
			case "properties_changeType":
				OspropertyProperties::changeType($option,$cid[0]);
			break;
			case "properties_approval":
				OspropertyProperties::approval($option,$cid);
			break;
			case "properties_unapproval":
				OspropertyProperties::unapproval($option,$cid);
			break;
			case "properties_publish":
				OspropertyProperties::changState($option,$cid,1);
			break;
			case "properties_unpublish":
				OspropertyProperties::changState($option,$cid,0);
			break;
			case "properties_remove":
				OspropertyProperties::remove($option,$cid,1);
			break;
			case "properties_print":
				OspropertyProperties::properties_print($option,$cid[0]);
			break;
			case "properties_backup":
				OspropertyProperties::backup($option);
			break;
			case "properties_dobackup":
				OspropertyProperties::dobackup($option);
			break;
			case "properties_restore":
				OspropertyProperties::restore($option);
			break;
			case "properties_dorestore":
				OspropertyProperties::dorestore($option);
			break;
			case "properties_prepareinstallsample":
				OspropertyProperties::properties_prepareinstallsample($option);
			break;
			case "properties_installdata":
				OspropertyProperties::installSampleData($option);
			break;
			case "properties_uploadphotopackages":
				OspropertyProperties::uloadPhotoPackages($option);
			break;
			case "properties_douploadphotopackages":
				OspropertyProperties::doUploadPhotoPackages($option);
			break;
			case "properties_listcity":
				OspropertyProperties::listCities($option);
			break;
			case "properties_setupfolder":
				OspropertyProperties::setup($option);
			break;
			case "properties_copy":
				OspropertyProperties::propertycopy($option,$cid[0]);
			break;
			case "properties_updatelocation":
				OspropertyProperties::updateLocationForm($option);
			break;
			case "properties_doimportlanguage":
				OspropertyProperties::importLanguages($option);
			break;
			case "properties_exportlocation":
				OspropertyProperties::exportlocation($option);
			break;
			case "properties_changeLocation":
				OspropertyProperties::changeLocator($option);
			break;
			case "properties_generatephoto":
				OspropertyProperties::generatePhoto($option);
			break;
			case "properties_savephoto":
				OspropertyProperties::savingPhoto($option);
			break;
			case "properties_completesaving":
				OspropertyProperties::skipgeneratePhoto($option);
			break;
			case "properties_sefoptimize":
				if (!Factory::getUser()->authorise('sef', 'com_osproperty'))
				{
					//return JError::raise(E_WARNING, 404, Text::_('JERROR_ALERTNOAUTHOR'));
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
				}
				OspropertyProperties::optimizesef($option);
			break;
			case "properties_doOptimizeSefUrls":
				OspropertyProperties::doOptimizeSefUrls($option);
			break;
			case "properties_syncdatabase":
				OspropertyProperties::syncdatabase($option);
			break;
			case "properties_doSyncdatabase":
				OspropertyProperties::doSyncdatabase($option);
			break;
			case "properties_reGeneratePictures":
				if (!Factory::getUser()->authorise('picture', 'com_osproperty')) 
				{
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
				}
				OspropertyProperties::reGeneratePictures($option);
			break;
			case "properties_doReGeneratePictures":
				OspropertyProperties::doReGeneratePictures($option);
			break;
            case "properties_delayReGeneratePictures":
                OspropertyProperties::delayReGeneratePictures($option);
            break;
			case "properties_exportlocationdata":
				OspropertyProperties::exportlocationdata($option);
			break;
            case "properties_fixdatabase":
                OspropertyProperties::fixdatabase($option);
            break;
            case "properties_sharetranslation":
                OspropertyProperties::share_translation();
            break;
			case "properties_removeorphan":
				OspropertyProperties::removeorphan();
			break;
            case "properties_doremoveorphan":
                OspropertyProperties::doremoveOrphan();
            break;
			case "properties_updatecoordinates":
				OspropertyProperties::updatecoordinates();
			break;
			case "properties_newupload":
				$session = Factory::getSession();
				$sRandomText = $session->get('randomText','');
				$randomText = $jinput->getString('randomText','');
				if($randomText == '')
				{
					$mainframe->redirect(Uri::root());
				}
				elseif($sRandomText != $randomText)
                {
					$mainframe->redirect(Uri::root());
				}
				else
				{
					DJUploadHelper::upload();
				}
			break;
            case "properties_statistic":
                OspropertyProperties::statistic();
            break;
		}
	}


	static function statistic()
    {
        $db = Factory::getDbo();

        //properties no view
        $db->setQuery("Select id, pro_name from #__osrs_properties where published = '1' and approved = '1' and hits = '0' limit 10");
        $lists['noview'] = $db->loadObjectList();

        //properties top view
        $db->setQuery("Select id, pro_name from #__osrs_properties where published = '1' and approved = '1' order by hits desc limit 10");
        $lists['topview'] = $db->loadObjectList();

        //agent no properties upload
        $db->setQuery("Select id, `name` from #__osrs_agents where published = '1' and id not in (Select distinct(agent_id) from #__osrs_properties where published = '1' and approved = '1') limit 10");
        $lists['agentnoproperties'] = $db->loadObjectList();

        //companies no properties upload
        $db->setQuery("Select id, company_name from #__osrs_companies where published = '1' and id not in (Select distinct(company_id) from #__osrs_properties where published = '1' and approved = '1') limit 10");
        $lists['companynoproperties'] = $db->loadObjectList();

        //properties will be unpublish soon

        //new properties
        $now = Factory::getDate()->toSql();
        $db->setQuery("Select id, pro_name, DATEDIFF('$now', created) AS number_created_days from #__osrs_properties where published = '1' and approved = '1' having number_created_days <= '10' limit 10");
        $lists['newproperties'] = $db->loadObjectList();

        //just updated properties
        $db->setQuery("Select id, pro_name, DATEDIFF('$now', modified) AS number_modified_days from #__osrs_properties where published = '1' and approved = '1' having number_modified_days <= '10' limit 10");
        $lists['modifiedproperties'] = $db->loadObjectList();

        //new agents
        $db->setQuery("Select id, `name` from #__osrs_agents where published = '1' order by id desc limit 10");
        $lists['newagents'] = $db->loadObjectList();

        //new companies
        $db->setQuery("Select id, company_name from #__osrs_companies where published = '1' order by id desc limit 10");
        $lists['newcompanies'] = $db->loadObjectList();

        HTML_OspropertyProperties::htmlStatistic($lists);
    }
	
	/**
	 * List properties
	 *
	 * @param unknown_type $option
	 */
	static function properties_list($option)
	{
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDBO();
		$config = new JConfig();
		$list_limit = $config->list_limit;

        //Update access level to Public for all existing categories
        $db->setQuery("Update #__osrs_properties set `access` = '1' where `access` = '0'");
        $db->execute();


		if(OSPHelper::isJoomla4())
		{
			$class = "imedium form-select";
		}
		else
		{
			$class = "chosen";
		}
		//check the relation between properties and categories
		
		$limitstart						= $jinput->get('limitstart','');
		if($limitstart == "")
		{
			$limitstart					= $mainframe->getUserStateFromRequest('pro_list.filter.limitstart','limit_start',0);
		}
		$mainframe->setUserState('pro_list.filter.limitstart',$limitstart);
		$limit     	  	 				= $mainframe->getUserStateFromRequest('pro_list.filter.limit','limit',$list_limit);
		
		if($limit ==  0)
		{
			$limit						= $jinput->getInt('limit',$list_limit);
		}
        $mainframe->setUserState('pro_list.filter.limitstart',$limitstart);
        $mainframe->setUserState('pro_list.filter.limit',$limit);

		$agentType    	  				= $mainframe->getUserStateFromRequest('pro_list.filter.agent_type','agent_type',-1);
		$mainframe->setUserState('pro_list.filter.agent_type',$agentType);
		$keyword    	  				= $mainframe->getUserStateFromRequest('pro_list.filter.keyword','keyword','');
		$mainframe->setUserState('pro_list.filter.keyword',$keyword);
		$agent_id  	   	  				= $mainframe->getUserStateFromRequest('pro_list.filter.agent_id','agent_id','');
		$mainframe->setUserState('pro_list.filter.agent_id',$agent_id);
		$pro_type  	      				= $mainframe->getUserStateFromRequest('pro_list.filter.pro_type','pro_type','');
		$mainframe->setUserState('pro_list.filter.pro_type',$pro_type);
		$category_id	  				= $mainframe->getUserStateFromRequest('pro_list.filter.category_id','category_id','');
		$mainframe->setUserState('pro_list.filter.category_id',$category_id);
		$country_id 	  				= $mainframe->getUserStateFromRequest('pro_list.filter.country_id','country_id',HelperOspropertyCommon::getDefaultCountry());
		$mainframe->setUserState('pro_list.filter.country_id',$country_id);

		if(!HelperOspropertyCommon::checkCountry())
		{
			$country_id = HelperOspropertyCommon::getDefaultCountry();
		}

        $city_id		  				= $mainframe->getUserStateFromRequest('pro_list.filter.city_id','city_id','');
        $mainframe->setUserState('pro_list.filter.city_id',$city_id);
		$state_id		  				= $mainframe->getUserStateFromRequest('pro_list.filter.state_id','state_id','');
		$mainframe->setUserState('pro_list.filter.state_id',$state_id);
		$company_id 	  				= $mainframe->getUserStateFromRequest('pro_list.filter.company_id','company_id','');
		$mainframe->setUserState('pro_list.filter.company_id',$company_id);
		$nbath			  				= $mainframe->getUserStateFromRequest('pro_list.filter.nbath','nbath','');
		$mainframe->setUserState('pro_list.filter.nbath',$nbath);
		$nbed       	  				= $mainframe->getUserStateFromRequest('pro_list.filter.nbed','nbed','');
		$mainframe->setUserState('pro_list.filter.nbed',$nbed);
		$nrooms      	  				= $mainframe->getUserStateFromRequest('pro_list.filter.nrooms','nrooms','');
		$mainframe->setUserState('pro_list.filter.nrooms',$nrooms);
		$request 		  				= $mainframe->getUserStateFromRequest('pro_list.filter.request','request','');
		$mainframe->setUserState('pro_list.filter.request',$request);
		$state 			  				= $mainframe->getUserStateFromRequest('pro_list.filter.state','state','');
		$mainframe->setUserState('pro_list.filter.state',$state);
		$state_approval		  			= $mainframe->getUserStateFromRequest('pro_list.filter.state_approval','state_approval','');
		$mainframe->setUserState('pro_list.filter.state_approval',$state_approval);
		$request_to_approval 			= $jinput->get('request_to_approval','');
		$mainframe->setUserState('pro_list.filter.request_to_approval',$request_to_approval);
		$properties_posted				= $jinput->get('properties_post',0);
		$mainframe->setUserState('pro_list.filter.properties_posted',$properties_posted);

		$from							= $jinput->get('from','');
		$property_in_types = "";
		$id_in_types = "";
		$lists['from'] = $from;
		if($from == "oscalendar")
		{
			$show_date_search_in = $configClass['show_date_search_in'];
			if($show_date_search_in != "")
			{
				$property_in_types = " and a.pro_type in (".str_replace("|",",",$show_date_search_in).")";
				$id_in_types = " and id in (".str_replace("|",",",$show_date_search_in).")";
			}
		}

        $show_form = 0;

		$lists['request_to_approval']   = $request_to_approval;
			
		$filter_order 	  = $jinput->getString('filter_order','a.ordering');

		if($filter_order == "")
		{
			$filter_order = "a.ordering";
		}
		$filter_order_Dir = $jinput->getString('filter_order_Dir','');

		$filter_full_ordering	= $jinput->getString('filter_full_ordering','');
		if($filter_full_ordering == '')
		{
			$filter_full_ordering = $mainframe->getUserStateFromRequest('pro_list.filter.filter_full_ordering','filter_full_ordering','a.ordering asc');
		}
		else
		{
			$lists['filter_full_ordering'] = $filter_full_ordering;
			$mainframe->setUserState('pro_list.filter.filter_full_ordering',$filter_full_ordering);
		}
		$filter_Arr						= explode(" ",$filter_full_ordering);
		$filter_order					= $filter_Arr[0];
		$filter_order_Dir				= $filter_Arr[1];

		if($filter_order == "")
		{
			$filter_order				= 'a.ordering';
		}
		$lists['filter_order']			= $filter_order;
		$lists['filter_order_Dir']		= $filter_order_Dir;
		
		$lists['filter_order'] 	= $filter_order;
		$lists['filter_order_Dir'] = $filter_order_Dir;
		
		$isfeature		  = $jinput->get('isfeature','');
		
		$query = "Select count(a.id) from #__osrs_properties as a"
				." LEFT JOIN #__osrs_agents as c on c.id = a.agent_id"
				." LEFT JOIN #__osrs_types as d on d.id = a.pro_type"
				." LEFT JOIN #__osrs_countries as e on e.id = a.country"
				." WHERE 1=1";
		$keyword = $db->escape($keyword);
		if($keyword != ""){
			$query .= " AND (";
			$query .= " a.pro_name like '%$keyword%' OR";
			$query .= " a.pro_alias like '%$keyword%' OR";
			$query .= " a.pro_small_desc like '%$keyword%' OR";
			$query .= " a.pro_full_desc like '%$keyword%' OR";
			$query .= " a.address like '%$keyword%' OR";
			$query .= " a.state like '%$keyword%' OR";
			$query .= " a.region like '%$keyword%' OR";
			$query .= " a.postcode like '%$keyword%' OR";
			$query .= " a.bed_room like '%$keyword%' OR";
			$query .= " a.bath_room like '%$keyword%' OR";
			$query .= " a.ref like '%$keyword%'";
			$query .= " )";
		}
		//oscalendar
		$query .= $property_in_types;
		if($category_id != ""){
            $show_form = 1;
			$query .= " AND a.id in (Select pid from #__osrs_property_categories where category_id = '$category_id')";
		}
		if($agent_id != ""){
            $show_form = 1;
			$query .= " AND a.agent_id = '$agent_id'";
		}
		if((int)$country_id > 0){
            if(HelperOspropertyCommon::checkCountry()) {
                $show_form = 1;
            }
			$query .= " AND a.country = '$country_id'";
		}
		if($state_id != ""){
            $show_form = 1;
			$query .= " AND a.state = '$state_id'";
		}
		if($city_id != ""){
		    $show_form = 1;
		    $query .= " AND a.city = '$city_id'";
        }
		if($pro_type != ""){
            $show_form = 1;
			$query .= " AND a.pro_type = '$pro_type'";
		}
		if($company_id != ""){
            $show_form = 1;
			$query .= " AND c.company_id = '$company_id'";
		}
		if($nbath != ""){
            $show_form = 1;
			$query .= " AND a.bath_room >= '$nbath'";
		}
		if($nbed != ""){
            $show_form = 1;
			$query .= " AND a.bed_room >= '$nbed'";
		}
		if($nrooms != ""){
            $show_form = 1;
			$query .= " AND a.bath_room >= '$nrooms'";
		}
		if($isfeature != ""){
            $show_form = 1;
			$query .= " AND a.isFeatured = '$isfeature'";
		}
		if ($request != ''){
            $show_form = 1;
			$query .= " AND a.request_to_approval = '$request'";
		}
		if ($state != ''){
            $show_form = 1;
			$query .= " AND a.published = '$state'";
		}
		if ($state_approval != ''){
            $show_form = 1;
			$query .= " AND a.approved = '$state_approval'";
		}
		if ($request_to_approval != ""){
            $show_form = 1;
			$query .= " AND a.request_to_approval = '$request_to_approval'";
		}
		if($agentType >= 0){
            $show_form = 1;
			$query .= " AND c.agent_type = '$agentType'";
		}
		if($properties_posted > 0){
			$date = new DateTime();
			$properties_posted_time = $properties_posted*3600*24;
			$properties_posted_time = $date->getTimestamp() - $properties_posted_time;
			$show_form = 1;
			$query .= " AND a.created >= '".date("Y-m-d",$properties_posted_time)."'";
		}

        $lists['show_form'] = $show_form;

		$db->setQuery($query); 
		$total = $db->loadResult();
		
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		
		$query = "Select a.*,c.name as agent_name,d.type_name,e.country_name,g.state_name,h.city as city_name from #__osrs_properties as a"
				." LEFT JOIN #__osrs_agents as c on c.id = a.agent_id"
				." LEFT JOIN #__osrs_types as d on d.id = a.pro_type"
				." LEFT JOIN #__osrs_countries as e on e.id = a.country"
				." LEFT JOIN #__osrs_states as g on g.id = a.state"
				." LEFT JOIN #__osrs_cities as h on h.id = a.city"
				." WHERE 1=1";
		if($keyword != ""){
			$query .= " AND (";
			$query .= " a.pro_name like '%$keyword%' OR";
			$query .= " a.pro_alias like '%$keyword%' OR";
			$query .= " a.pro_small_desc like '%$keyword%' OR";
			$query .= " a.pro_full_desc like '%$keyword%' OR";
			$query .= " a.address like '%$keyword%' OR";
			$query .= " a.city like '%$keyword%' OR";
			$query .= " a.state like '%$keyword%' OR";
			$query .= " a.region like '%$keyword%' OR";
			$query .= " a.postcode like '%$keyword%' OR";
			$query .= " a.bed_room like '%$keyword%' OR";
			$query .= " a.bath_room like '%$keyword%' OR";
			$query .= " a.ref like '%$keyword%'";
			$query .= " )";
		}
		//oscalendar
		$query .= $property_in_types;
		if($category_id != ""){
			$query .= " AND a.id in (Select pid from #__osrs_property_categories where category_id = '$category_id')";
		}
		if($agent_id != ""){
			$query .= " AND a.agent_id = '$agent_id'";
		}
		if((int)$country_id > 0){
			$query .= " AND a.country = '$country_id'";
		}
		if($state_id != ""){
			$query .= " AND a.state = '$state_id'";
		}
        if($city_id != ""){
            $query .= " AND a.city = '$city_id'";
        }
		if($pro_type != ""){
			$query .= " AND a.pro_type = '$pro_type'";
		}
		if($company_id != ""){
			$query .= " AND c.company_id = '$company_id'";
		}
		if($nbath != ""){
			$query .= " AND a.bath_room >= '$nbath'";
		}
		if($nbed != ""){
			$query .= " AND a.bed_room >= '$nbed'";
		}
		if($nrooms != ""){
			$query .= " AND a.bath_room >= '$nrooms'";
		}
		if($isfeature != ""){
			$query .= " AND a.isFeatured = '$isfeature'";
		}
		if ($request != ''){
			$query .= " AND a.request_to_approval = '$request'";
		}
		if ($state_approval != ''){
			$query .= " AND a.approved = '$state_approval'";
		}
		if ($state != ''){
			$query .= " AND a.published = '$state'";
		}
		if ($request_to_approval != ""){
			$query .= " AND a.request_to_approval = '$request_to_approval'";
		}
		if($agentType >= 0){
			$query .= " AND c.agent_type = '$agentType'";
		}
		if($properties_posted > 0)
		{
			$date = new DateTime();
			$properties_posted_time = $properties_posted*3600*24;
			$properties_posted_time = $date->getTimestamp() - $properties_posted_time;
			$show_form = 1;
			$query .= " AND a.created >= '".date("Y-m-d",$properties_posted_time)."'";
		}
		$query .= " ORDER BY $filter_order $filter_order_Dir";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		//echo $db->getQuery();
		$rows = $db->loadObjectList();
		
		if(count($rows) > 0)
		{
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				$db->setQuery("Select count(id) from #__osrs_report where item_type = '0' and item_id = '$row->id'");
				$count_report = (int) $db->loadResult();
				$row->count_report = $count_report;

				$alias = $row->pro_alias;
				if($alias == ""){
					$alias = OSPHelper::generateAlias('property',$row->id,'');
					$db->setQuery("Update #__osrs_properties set pro_alias = '$alias' where id = '$row->id'");
					$db->execute();
					$row->pro_alias = $alias;
				}
				
				$db->setQuery("Select company_id from #__osrs_agents where id = '$row->agent_id'");
				$companyId = $db->loadResult();
				if($companyId > 0)
				{
					$row->company_id = $companyId;
					$db->setQuery("Select company_name,photo from #__osrs_companies where id = '$companyId'");
					$company = $db->loadObject();
					$row->company_name = $company->company_name;
					if(($company->photo != "") and (file_exists(JPATH_ROOT.'/images/osproperty/company/thumbnail/'.$company->photo))){
						$row->company_photo = Uri::root().'/images/osproperty/company/thumbnail/'.$company->photo;
					}else{
						$row->company_photo = "";
					}
				}

				//rating
				if($configClass['show_rating'] == 1){
					if($row->number_votes > 0){
						$points = round($row->total_points/$row->number_votes);
						ob_start();
						?>
							<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-<?php echo $points;?>.png" />	
						<?php
						$row->rating = ob_get_contents();
						ob_end_clean();
						
					}else{
						ob_start();
						
						?>
						<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-0.png" />
						<?php
					
						$row->rating = ob_get_contents();
						ob_end_clean();
					} //end rating
				}
			}
		}
		
		//Company
		$companyArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_COMPANIES'));
		$db->setQuery("Select id as value,company_name as text from #__osrs_companies where published = '1' order by company_name");
		$companies = $db->loadObjectList();
		$companyArr = array_merge($companyArr,$companies);
		$lists['company'] = HTMLHelper::_('select.genericlist',$companyArr,'company_id','class="'.$class.' input-medium form-select" onChange="javascript:document.adminForm.submit();"','value','text',$company_id);
		
		//agent
		$agentArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_AGENTS'));
		$query  = "Select a.id as value,a.name as text from #__osrs_agents as a inner join #__users as b on b.id = a.user_id where a.published = '1' ";
		if($company_id != ""){
			$query .= " and a.company_id = '$company_id'";
		}
		$query .= " order by a.name";
		$db->setQuery($query);
		$agents = $db->loadObjectList();
		$agentArr   = array_merge($agentArr,$agents);
		$lists['agent'] = HTMLHelper::_('select.genericlist',$agentArr,'agent_id','class="'.$class.' input-medium form-select" onChange="javascript:document.adminForm.submit();"','value','text',$agent_id);
		
		//property types
		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_PROPERTY_TYPES'));
		$db->setQuery("Select id as value,type_name as text from #__osrs_types where published = '1' $id_in_types order by type_name");
		$protypes = $db->loadObjectList();
		$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'pro_type','class="'.$class.' input-large form-select" onChange="javascript:document.adminForm.submit();"','value','text',$pro_type);
		
		//categories
		$lists['category'] = OspropertyProperties::listFilterCategories($category_id,'onChange="javascript:document.adminForm.submit();"');
		//country	
		$lists['country'] = HelperOspropertyCommon::makeCountryList($country_id,'country_id','onChange="javascript:document.adminForm.submit();"',Text::_('OS_ALL_COUNTRIES'),'class="'.$class.' input-medium form-select"');
		//state
		$lists['states'] = HelperOspropertyCommon::makeStateList($country_id,$state_id,'state_id','onChange="javascript:document.adminForm.submit();"',Text::_('OS_ALL_STATES'),'class="'.$class.' input-medium form-select"');

		$lists['cities'] = HelperOspropertyCommon::makeCityList($country_id,$state_id, $city_id, 'city_id','onChange="javascript:document.adminForm.submit();"',Text::_('OS_ALL_CITIES'),'class="'.$class.' input-medium form-select"');
		//number bed rooms
		$bedArr[] = HTMLHelper::_('select.option','',Text::_('OS_MIN_BEDS'));
		for($i=1;$i<=20;$i++){
			$bedArr[] = HTMLHelper::_('select.option',$i,$i);
		}
		$lists['nbed'] = HTMLHelper::_('select.genericlist',$bedArr,'nbed','style="width:130px;"  class="'.$class.' input-mini form-select" onChange="javascript:document.adminForm.submit();"','value','text',$nbed);
		
		//number bath rooms
		$bathArr[] = HTMLHelper::_('select.option','',Text::_('OS_MIN_BATHS'));
		for($i=1;$i<=20;$i++){
			$bathArr[] = HTMLHelper::_('select.option',$i,$i);
		}
		$lists['nbath'] = HTMLHelper::_('select.genericlist',$bathArr,'nbath','style="width:130px;"  class="'.$class.' input-mini form-select" onChange="javascript:document.adminForm.submit();"','value','text',$nbath);
		
		//number rooms
		$roomsArr[] = HTMLHelper::_('select.option','',Text::_('OS_MIN_ROOMS'));
		for($i=1;$i<=20;$i++){
			$roomsArr[] = HTMLHelper::_('select.option',$i,$i);
		}
		$lists['nrooms'] = HTMLHelper::_('select.genericlist',$roomsArr,'nrooms','style="width:130px;" class="'.$class.' input-mini form-select" onChange="javascript:document.adminForm.submit();"','value','text',$nrooms);
		
		//feature
		$featureArr	   = [];
		$featureArr[]  = HTMLHelper::_('select.option','',Text::_('OS_FEATURED_PROPERTIES'));
		$featureArr[]  = HTMLHelper::_('select.option','0',Text::_('OS_NO'));
		$featureArr[]  = HTMLHelper::_('select.option','1',Text::_('OS_YES'));
		$lists['isfeature'] = HTMLHelper::_('select.genericlist',$featureArr,'isfeature','class="'.$class.' input-medium form-select" onChange="javascript:document.adminForm.submit();"','value','text',$isfeature);
		
		//feature
		$requestApprovalArr	   = [];
		$requestApprovalArr[]  = HTMLHelper::_('select.option','',Text::_('OS_REQUEST_TO_APPROVAL'));
		$requestApprovalArr[]  = HTMLHelper::_('select.option','0',Text::_('OS_NO'));
		$requestApprovalArr[]  = HTMLHelper::_('select.option','1',Text::_('OS_YES'));
		$lists['request_to_approval'] = HTMLHelper::_('select.genericlist',$requestApprovalArr,'request_to_approval','class="'.$class.' input-medium form-select" onChange="javascript:document.adminForm.submit();"','value','text',$request_to_approval);
		
		//published
		$stateArr = [];
		$stateArr[]  = HTMLHelper::_('select.option','',Text::_('OS_PUBLISH_STATE'));
		$stateArr[]  = HTMLHelper::_('select.option','1',Text::_('OS_PUBLISHED'));
		$stateArr[]  = HTMLHelper::_('select.option','0',Text::_('OS_UNPUBLISHED'));
		$lists['state'] = HTMLHelper::_('select.genericlist',$stateArr,'state','style="width:120px;" class="'.$class.' input-medium form-select" onChange="javascript:document.adminForm.submit();"','value','text',$state);
		
		// Request
		$option_request = [];
		$option_request[] = HTMLHelper::_('select.option','',' - '.Text::_('OS_STATE_APPROVAL').' - ');
		$option_request[] = HTMLHelper::_('select.option',1,Text::_('OS_APPROVAL'));
		$option_request[] = HTMLHelper::_('select.option',0,Text::_('OS_UNAPPROVAL'));
		$lists['state_approval'] = HTMLHelper::_('select.genericlist',$option_request,'state_approval','style="width:120px;" class="input-small form-select" onchange="document.adminForm.submit();"','value','text',$state_approval);

		//properties posted from
		$propertiespostedArr[] = HTMLHelper::_('select.option','',Text::_('OS_PROPERTIES_POST_FROM'));
		$propertiespostedArr[] = HTMLHelper::_('select.option',1,"1 ".Text::_('OS_DAYS'));
		$propertiespostedArr[] = HTMLHelper::_('select.option',5,"5 ".Text::_('OS_DAYS'));
		$propertiespostedArr[] = HTMLHelper::_('select.option',10,"10 ".Text::_('OS_DAYS'));
		$propertiespostedArr[] = HTMLHelper::_('select.option',15,"15 ".Text::_('OS_DAYS'));
		$propertiespostedArr[] = HTMLHelper::_('select.option',30,"30 ".Text::_('OS_DAYS'));
		$lists['propertiesposted'] = HTMLHelper::_('select.genericlist',$propertiespostedArr,'properties_post','class="'.$class.' input-medium form-select" onChange="javascript:document.adminForm.submit();"','value','text',$properties_posted);
		
		//agent type
		/*
		$optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option','-1',Text::_('OS_SELECT_USER_TYPE'));
		$optionArr[] = HTMLHelper::_('select.option','0',Text::_('OS_AGENT'));
		$optionArr[] = HTMLHelper::_('select.option','1',Text::_('OS_OWNER'));
		$lists['agentType'] = HTMLHelper::_('select.genericlist',$optionArr,'agentType','class="chosen input-medium" onChange="javascript:document.adminForm.submit();"','value','text',$agentType);
		*/
			
		HTML_OspropertyProperties::listProperties($option,$rows,$pageNav,$lists);
	}

	static function saveorderAjax($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$cid 	= $jinput->get( 'cid', [], 'array' );
	
		$row = Table::getInstance('Property','OspropertyTable');
		
		$groupings	= [];

		$order		= $jinput->get( 'order', [], 'array' );

		// update ordering values
		for( $i=0; $i < count($cid); $i++ ) 
		{
			$row->load( $cid[$i] );
			// track parents
			$groupings[] = $row->ordering;
			if ($row->ordering != $order[$i]) 
			{
				$row->ordering = $order[$i];
				if (!$row->store()) 
				{
					$this->setError($row->getError());
					return false;
				}
			} // if
		} // for

		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		//foreach ($groupings as $group){
			//$row->reorder(' ordering ');
		//}
	}

	static function uploadZipfile(){
		global $jinput, $mainframe;
		if(is_uploaded_file($_FILES['zipfile'][tmp_name])){
			move_uploaded_file($_FILES['zipfile'][tmp_name],JPATH_ROOT.'/tmp/'.$_FILES['zipfile'][tmp_name]);
		}
	}
	
	static function exportlocation($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$country_code = "de";
		$db->setQuery("Select * from t_region where fk_c_country_code = '$country_code'");
		$regions = $db->loadObjectList();
		$exportArr = [];
		for($i=0;$i<count($regions);$i++){
			$export = "";
			$region = $regions[$i];
			$db->setQuery("Select * from t_city where fk_c_country_code = '$country_code' and fk_i_region_id = '$region->pk_i_id'");
			$cities = $db->loadObjectList();
			$cityArr = [];
			for($j=0;$j<count($cities);$j++){
				$city = $cities[$j];
				$cityArr[] = $city->s_name;
			}
			$city_value = implode(",",$cityArr);
			$export = $region->s_name.":".$city_value;
			$exportArr[] = $export;
		}
		$location = implode("\n",$exportArr);
		$filename = "de_germany.txt";
		$fh = fopen(JPATH_ROOT."/tmp/".$filename, 'w');
		@fwrite($fh,$location);
		fclose($fh);
	}
	
	/**
	 * Update location form
	 *
	 * @param unknown_type $option
	 */
	static function updateLocationForm($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$country_id = $jinput->getInt('country_id',0);
		$db->setQuery("Select * from #__osrs_countries where id = '$country_id'");
		$country = $db->loadObject();
		$db->setQuery("Select count(id) from #__osrs_states where country_id = '$country_id'");
		$country->nstates = $db->loadResult();
		$db->setQuery("Select count(id) from #__osrs_cities where country_id = '$country_id'");
		$country->ncities = $db->loadResult();
		
		$optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option','1',Text::_('OS_PUBLISHED'));
		$optionArr[] = HTMLHelper::_('select.option','0',Text::_('OS_UNPUBLISHED'));
		$lists['state'] = HTMLHelper::_('select.genericlist',$optionArr,'state','class="input-medium form-select"','value','text');
		
		HTML_OspropertyProperties::updateLocationForm($option,$country,$lists);
	}
	
	/**
	 * Import languages
	 *
	 * @param unknown_type $option
	 */
	static function importLanguages($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$state = $jinput->getInt('state',0);
		$country_id = $jinput->getInt('country_id',0);
		$db->setQuery("Delete from #__osrs_states where country_id = '$country_id'");
		$db->execute();
		$db->setQuery("Delete from #__osrs_cities where country_id = '$country_id'");
		$db->execute();
		if(is_uploaded_file($_FILES['filename']['tmp_name'])){
			$filename = $_FILES['filename']['name'];
			move_uploaded_file($_FILES['filename']['tmp_name'],JPATH_ROOT."/tmp/".$filename);
			$fh = fopen(JPATH_ROOT."/tmp/".$filename, 'r');
			$filesize = filesize(JPATH_ROOT."/tmp/".$filename);
			$location = fread($fh, $filesize);
			fclose($fh);
			//echo $location;
			$location = self::file_get_contents_utf8(JPATH_ROOT."/tmp/".$filename);
			$locationArr = explode("\n",$location);
			if(count($locationArr) > 0){
				for($i=0;$i<count($locationArr);$i++){
					$location = $locationArr[$i];
					$values = explode(":",$location);
					$state_name = $values[0];
					$cities = $values[1];
					if(function_exists('mb_convert_encoding')){
						$state_name = mb_convert_encoding($state_name, 'HTML-ENTITIES', "UTF-8");
					}

					if($state_name != ""){
						$db->setQuery("Select count(id) from #__osrs_states where country_id = '$country_id' and state_name like '$state_name'");
						$count = $db->loadResult();
						if($count == 0){
							$db->setQuery("INSERT INTO #__osrs_states (id,country_id,state_name,state_code,published) VALUES (NULL,'$country_id','".$state_name."','".$state_name."','$state')");
							$db->execute();
							$state_id = $db->insertid();
						}else{
							$db->setQuery("Select id from #__osrs_states where country_id = '$country_id' and state_name like '".$state_name."'");
							$state_id = $db->loadResult();
						}
						
						//check the cities now
						
						$cities = explode(",",$cities);
						if(count($cities) > 0){
							$db->setQuery("Delete from #__osrs_cities where state_id = '$state_id' and country_id = '$country_id'");
							$db->execute();
							$cityArr = [];
							for($j=0;$j<count($cities);$j++){
								$city = $cities[$j];
								if(function_exists('mb_convert_encoding')){
									$city = mb_convert_encoding($city, 'HTML-ENTITIES', "UTF-8");
								}
								if($city != ""){							
									$cityArr[] = "(NULL,'".$city."','$country_id','$state_id','$state')";
								}
							}
							$city_sql = implode(",",$cityArr);
							if($city_sql != ""){
								$sql = "INSERT INTO #__osrs_cities (id,city,country_id,state_id,published) VALUES $city_sql;";
								$db->setQuery($sql);
								$db->execute();
							}
						}
					}
				}
			}
		}
		//redirect to the home page
		$msg = Text::_('OS_LOCATION_DB_HAVE_BEEN_IMPORTED_SUCCESSFULLY');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty");
	}

	public static function file_get_contents_utf8($fn) {
		 $content = file_get_contents($fn);
		 if(function_exists('mb_convert_encoding')){
			return mb_convert_encoding($content, 'UTF-8',mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
		 }else{
			 return $content;
		 }
	}
	
	static function setup($option){
		global $jinput, $mainframe;
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		//check to see if the images folder are existing
		$htmlfile = JPATH_ROOT."/components/com_osproperty/index.html";
		if(!Folder::exists(JPATH_ROOT."/images/osproperty")){
			Folder::create(JPATH_ROOT."/images/osproperty");
			File::copy($htmlfile,JPATH_ROOT."/images/osproperty/index.html");
		}
		if(!Folder::exists(JPATH_ROOT."/images/osproperty/agent")){
			Folder::create(JPATH_ROOT."/images/osproperty/agent");
			File::copy($htmlfile,JPATH_ROOT."/images/osproperty/agent/index.html");
		}
		if(!Folder::exists(JPATH_ROOT."/images/osproperty/agent/thumbnail")){
			Folder::create(JPATH_ROOT."/images/osproperty/agent/thumbnail");
			File::copy($htmlfile,JPATH_ROOT."/images/osproperty/agent/thumbnail/index.html");
		}
		if(!Folder::exists(JPATH_ROOT."/images/osproperty/company")){
			Folder::create(JPATH_ROOT."/images/osproperty/company");
			File::copy($htmlfile,JPATH_ROOT."/images/osproperty/company/index.html");
		}
		if(!Folder::exists(JPATH_ROOT."/images/osproperty/company/thumbnail")){
			Folder::create(JPATH_ROOT."/images/osproperty/company/thumbnail");
			File::copy($htmlfile,JPATH_ROOT."/images/osproperty/company/thumbnail/index.html");
		}
		if(!Folder::exists(JPATH_ROOT."/images/osproperty/properties")){
			Folder::create(JPATH_ROOT."/images/osproperty/properties");
			File::copy($htmlfile,JPATH_ROOT."/images/osproperty/properties/index.html");
		}
		if(!Folder::exists(JPATH_ROOT."/images/osproperty/properties/thumb")){
			Folder::create(JPATH_ROOT."/images/osproperty/properties/thumb");
			File::copy($htmlfile,JPATH_ROOT."/images/osproperty/properties/thumb/index.html");
		}
		if(!Folder::exists(JPATH_ROOT."/images/osproperty/properties/medium")){
			Folder::create(JPATH_ROOT."/images/osproperty/properties/medium");
			File::copy($htmlfile,JPATH_ROOT."/images/osproperty/properties/medium/index.html");
		}
		if(!Folder::exists(JPATH_ROOT."/images/osproperty/category")){
			Folder::create(JPATH_ROOT."/images/osproperty/category");
			File::copy($htmlfile,JPATH_ROOT."/images/osproperty/category/index.html");
		}
		if(!Folder::exists(JPATH_ROOT."/images/osproperty/category/thumbnail")){
			Folder::create(JPATH_ROOT."/images/osproperty/category/thumbnail");
			File::copy($htmlfile,JPATH_ROOT."/images/osproperty/category/thumbnail/index.html");
		}
	}

	
	/**
	 * List cities
	 *
	 * @param unknown_type $option
	 */
	static function listCities($option){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		$limitstart = $jinput->getInt('limitstart',0);
		$limit = $jinput->getInt('limit',20);
		$keyword = $jinput->getString('keyword','');
		$country_id = $jinput->getInt('country_id',$configClass['show_country_id']);
		$state_id = $jinput->getInt('state_id',0);
		$query = "Select count(a.id) from #__osrs_cities as a inner join #__osrs_states as b on b.id = a.state_id inner join #__osrs_countries as c on c.id = a.country_id where a.published = '1' ";
		if($keyword != ""){
			$query .= " and a.city like '%$keyword%'";
		}
		if($state_id > 0){
			$query .= " and a.state_id = '$state_id'";
		}
		if($country_id > 0){
			$query .= " and a.country_id = '$country_id'";
		}
		$db->setQuery($query);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		$query = "Select a.*,b.state_name,c.country_name from #__osrs_cities as a inner join #__osrs_states as b on b.id = a.state_id inner join #__osrs_countries as c on c.id = a.country_id where a.published = '1' ";
		if($keyword != ""){
			$query .= " and a.city like '%$keyword%'";
		}
		if($state_id > 0){
			$query .= " and a.state_id = '$state_id'";
		}
		if($country_id > 0){
			$query .= " and a.country_id = '$country_id'";
		}
		$query .= " order by c.country_name,b.state_name,a.city ";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
	
		$rows = $db->loadObjectList();
		
		
		//country
		$countryArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_COUNTRY'));
		$db->setQuery("Select id as value, country_name as text from #__osrs_countries order by country_name");
		$countries = $db->loadObjectList();
		$countryArr = array_merge($countryArr,$countries);
		$lists['country'] = HTMLHelper::_('select.genericlist',$countryArr,'country_id','class="input-medium form-select" onChange="javascript:document.adminForm.submit();"','value','text',$country_id);
		
		//state
		$stateArr = [];
		$stateArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_STATE'));
		$query  = "Select id as value,state_name as text from #__osrs_states where 1=1 ";
		if($country_id != ""){
			$query .= " and country_id = '$country_id'";
			$disabled = "";
		}else{
			$disabled = "disabled";
		}
		$query .= " order by state_name";
		$db->setQuery($query);
		$states = $db->loadObjectList();
		$stateArr   = array_merge($stateArr,$states);
		$lists['states'] = HTMLHelper::_('select.genericlist',$stateArr,'state_id','class="input-medium" onChange="javascript:document.adminForm.submit();" '.$disabled,'value','text',$state_id);
		
		HTML_OspropertyProperties::listCities($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * Show the form to inform about the term and condition when install sample data
	 *
	 * @param unknown_type $option
	 */
	static function properties_prepareinstallsample($option){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		$lists['ccompany'] = 0;
		$lists['cuser'] = 0;
		$lists['cagent'] = 0;
		$lists['auser'] = 0;
		$db->setQuery("Select count(id) from #__users where username like 'company'");
		$lists['cuser'] = intval($db->loadResult());
		if($lists['cuser'] == 1){
			$db->setQuery("Select id from #__users where username like 'company'");
			$cuser = $db->loadResult();
			$db->setQuery("Select count(id) from #__osrs_companies where user_id = '$cuser'");
			$lists['ccompany'] = intval($db->loadResult());
		}
		
		$db->setQuery("Select count(id) from #__users where username like 'agent'");
		$lists['auser'] = intval($db->loadResult());
		if($lists['auser'] == 1){
			$db->setQuery("Select id from #__users where username like 'agent'");
			$auser = $db->loadResult();
			$db->setQuery("Select count(id) from #__osrs_agents where user_id = '$auser'");
			$lists['cagent'] = intval($db->loadResult());
		}
		
		//select sample location
		$country_id = HelperOspropertyCommon::getDefaultCountry();
		$lists['country'] = HelperOspropertyCommon::makeCountryList($country_id,'country','onchange="change_country_agent(this.value,0,0);"',Text::_('OS_SELECT_COUNTRY'),'');
		
		$lists['states'] = HelperOspropertyCommon::makeStateList($country_id,0,'state','onchange="change_state(this.value,0)" class="input-medium form-select"',Text::_('OS_SELECT_STATE'),'');
		
		$lists['city'] = HelperOspropertyCommon::loadCity($option,0,0);
		
		HTML_OspropertyProperties::prepareInstallSampleForm($option,$lists);
	}
	
	/**
	 * Upload photo packages
	 *
	 * @param unknown_type $option
	 */
	static function uloadPhotoPackages($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		HTML_OspropertyProperties::uploadPhotoPackages($option);
	}
	
	/**
	 * Process upload photo packages
	 *
	 * @param unknown_type $option
	 */
	static function doUploadPhotoPackages($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		 
		if(!HelperOspropertyCommon::checkIsArchiveFileUploaded('photopackage')){
			//return to previous page
			?>
			<script type="text/javascript">
			window.history(-1);
			</script>
<?php
		}else{
			$allowedExt = array('jpg','jpeg','gif','png');
			if(is_uploaded_file($_FILES['photopackage']['tmp_name'])){
				$filename = time().$_FILES['photopackage']['name'];
				move_uploaded_file($_FILES['photopackage']['tmp_name'],JPATH_ROOT."/tmp/".$filename);
                if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
                {
                    $archive = new Archive(array('tmp_path' => Factory::getConfig()->get('tmp_path')));
                    $result  = $archive->extract(JPATH_ROOT."/tmp/".$filename,JPATH_ROOT."/tmp/osphotos");
                }
                else
                {
                    $result = JArchive::extract(JPATH_ROOT."/tmp/".$filename,JPATH_ROOT."/tmp/osphotos");
                }

				//move the files to the correct folder
				//category folder
				$category_link = JPATH_ROOT."/tmp/osphotos/photos/category";
				$category_thumb_link = JPATH_ROOT."/tmp/osphotos/photos/category/thumbnail";
				
				if($handle = opendir($category_link)){
					while (false !== ($entry = readdir($handle))) {
						if(($entry != ".") AND ($entry != "..")){
				        	$entryArr = explode(".",$entry);
				        	$ext = strtolower($entryArr[count($entryArr)-1]);
				        	if(in_array($ext,$allowedExt)){
				        		File::copy($category_link."/".$entry,JPATH_ROOT."/images/osproperty/category/".$entry);
				        	}
						}
				    }
				    closedir($handle);
				}
				
				if($handle = opendir($category_thumb_link)){
					while (false !== ($entry = readdir($handle))) {
						if(($entry != ".") AND ($entry != "..")){
				        	$entryArr = explode(".",$entry);
				        	$ext = strtolower($entryArr[count($entryArr)-1]);
				        	if(in_array($ext,$allowedExt)){
				        		File::copy($category_thumb_link."/".$entry,JPATH_ROOT."/images/osproperty/category/thumbnail/".$entry);
				        	}
						}
				    }
				    closedir($handle);
				}
	
				$property_link = JPATH_ROOT."/tmp/osphotos/photos/property";
				$property_thumb_link = JPATH_ROOT."/tmp/osphotos/photos/property/thumb";
				$property_medium_link = JPATH_ROOT."/tmp/osphotos/photos/property/medium";
				
				if($handle = opendir($property_link)){
					while (false !== ($entry = readdir($handle))) {
						if(($entry != ".") AND ($entry != "..")){
				        	$entryArr = explode(".",$entry);
				        	$ext = strtolower($entryArr[count($entryArr)-1]);
				        	if(in_array($ext,$allowedExt)){
				        		File::copy($property_link."/".$entry,JPATH_ROOT."/images/osproperty/properties/".$entry);
				        	}
						}
				    }
				    closedir($handle);
				}
				
				if($handle = opendir($property_thumb_link)){
					while (false !== ($entry = readdir($handle))) {
						if(($entry != ".") AND ($entry != "..")){
				        	$entryArr = explode(".",$entry);
				        	$ext = strtolower($entryArr[count($entryArr)-1]);
				        	if(in_array($ext,$allowedExt)){
				        		File::copy($property_thumb_link."/".$entry,JPATH_ROOT."/images/osproperty/properties/thumb/".$entry);
				        	}
						}
				    }
				    closedir($handle);
				}
				
				
				if($handle = opendir($property_medium_link)){
					while (false !== ($entry = readdir($handle))) {
						if(($entry != ".") AND ($entry != "..")){
				        	$entryArr = explode(".",$entry);
				        	$ext = strtolower($entryArr[count($entryArr)-1]);
				        	if(in_array($ext,$allowedExt)){
				        		File::copy($property_medium_link."/".$entry,JPATH_ROOT."/images/osproperty/properties/medium/".$entry);
				        	}
						}
				    }
				    closedir($handle);
				}
				
				//create the image folder for each properties
			    $db->setQuery("Select id from #__osrs_properties");
			    $pids = $db->loadOBjectList();
			    if(count($pids) > 0){
			    	include_once(JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
			    	for($i=0;$i<count($pids);$i++){
			    		$pid = $pids[$i];
			    		OSPHelper::createPhotoDirectory($pid->id);
			    		OSPHelper::movingPhotoSampleData($pid->id);
			    	}
			    }
				
				File::delete(JPATH_ROOT."/tmp/".$filename);
				Folder::delete(JPATH_ROOT."/tmp/osphotos");
			}
		}
		$mainframe->enqueueMessage(Text::_('OS_COMPLETE_INSTALL_SAMPLE_DATA'));
		$mainframe->redirect("index.php?option=com_osproperty");
	}
	/**
	 * Installing sameple data
	 *
	 * @param unknown_type $option
	 */
	static function installSampleData($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder') ;
		//create company 
		//user : company - Test company
		
		$country = $jinput->getInt('country',0);
		$state   = $jinput->getInt('state',0);
		$city    = $jinput->getInt('city',0);
		
		$db->setQuery("Select count(id) from #__users where username like 'company'");
		$count = $db->loadResult();
		if($count == 0){
			//insert new user company
			$uid = HelperOspropertyCommon::createUser('company','Sample Company');
			//create company Item
			$row = & Table::getInstance('Companies','OspropertyTable');
			$row->id = 0;
			$row->company_name = "Sample Company";
			$row->company_alias = "sample_company";
			$row->user_id = $uid;
			$row->address = "5 Sinnamon Beach Way ";
			$row->state = $state;
			$row->country = $country;
			$row->city = $city;
			$row->postcode = "";
			$row->email = "company@osproperty.com";
			$row->website = "http://osproperty.ext4joomla.com";
			$row->photo = "companylogo.png";
			$row->company_description ="Sample company";
			$row->published = 1;
			$row->store();
			
			File::copy(JPATH_ROOT."/components/com_osproperty/images/assets/companylogo.png",JPATH_ROOT."/images/osproperty/company/companylogo.png");
			File::copy(JPATH_ROOT."/components/com_osproperty/images/assets/companylogo.png",JPATH_ROOT."/images/osproperty/company/thumbnail/companylogo.png");
			$company_id = $db->insertid();
		}
		else
		{
			$db->setQuery("Select id from #__users where username like 'company'");
			$uid = $db->loadResult();
			$db->setQuery("Select count(id) from #__osrs_companies where user_id = '$uid'");
			$company_id = $db->loadResult();
			if(intval($company_id) == 0){ // user is already exists but company is not 
				//create company Item
				$row = & Table::getInstance('Companies','OspropertyTable');
				$row->id = 0;
				$row->company_name = "Sample Company";
				$row->company_alias = "sample_company";
				$row->user_id = $uid;
				$row->address = "5 Sinnamon Beach Way ";
				$row->state = $state;
				$row->country = $country;
				$row->city = $city;
				$row->postcode = "100000";
				$row->email = "company@osproperty.com";
				$row->website = "http://osproperty.ext4joomla.com";
				$row->photo = "companylogo.png";
				$row->company_description ="Sample company";
				$row->published = 1;
				$row->store();
				$company_id = $db->insertID();
			}
		}
		
		//create agent
		
		$db->setQuery("Select count(id) from #__users where username like 'agent'");
		$count = $db->loadResult();
		if($count == 0){
			//insert new user agent
			$uid = HelperOspropertyCommon::createUser('agent','Sample Agent');
			//create agent Item
			$row = & Table::getInstance('Agent','OspropertyTable');
			$row->id = 0;
			$row->company_id = $company_id;
			$row->name = "Sample Agent";
			$row->alias = "sample_agent";
			$row->user_id = $uid;
			$row->phone = "123456789";
			$row->mobile = "987654321";
			$row->address = "5 Sinnamon Beach Way ";
			$row->state = $state;
			$row->country = $country;
			$row->city = $city;
			$row->email = "agent@osproperty.com";
			$row->photo = "user.png";
			$row->license ="Sample agent license";
			$row->skype = "osproperty";
			$row->facebook = "http://facebook.com";
			$row->published = 1;
			$row->request_to_approval = 0;
			$row->store();
			$agent_id = $row->id;
			
			File::copy(JPATH_ROOT."/components/com_osproperty/images/assets/user.png",JPATH_ROOT."/images/osproperty/agent/user.png");
			File::copy(JPATH_ROOT."/components/com_osproperty/images/assets/user.png",JPATH_ROOT."/images/osproperty/agent/thumbnail/user.png");
			
			$db->setQuery("INSERT INTO #__osrs_company_agents (id,company_id,agent_id) VALUES (NULL,'$company_id','$agent_id')");
			$db->execute();
		}else{
			$db->setQuery("Select id from #__users where username like 'agent'");
			$uid = $db->loadResult();
			$db->setQuery("Select id from #__osrs_agents where user_id = '$uid'");
			$agent_id = $db->loadResult();
			if(intval($agent_id) == 0)
			{
				//create agent Item
				$row = Table::getInstance('Agent','OspropertyTable');
				$row->id = 0;
				$row->company_id = $company_id;
				$row->name = "Sample Agent";
				$row->alias = "sample_agent";
				$row->user_id = $uid;
				$row->phone = "123456789";
				$row->mobile = "987654321";
				$row->address = "5 Sinnamon Beach Way ";
				$row->state = $state;
				$row->country = $country;
				$row->city = $city;
				$row->email = "agent@osproperty.com";
				$row->photo = "user.png";
				$row->license ="Sample agent license";
				$row->skype = "osproperty";
				$row->facebook = "http://facebook.com";
				$row->published = 1;
				$row->request_to_approval = 0;
				$row->store();
				$agent_id = $row->id;
				
				$db->setQuery("INSERT INTO #__osrs_company_agents (id,company_id,agent_id) VALUES (NULL,'$company_id','$agent_id')");
				$db->execute();
			}
		}
		
		//create categories
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_categories");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_categories");
			$db->execute();
		}
		$db->setQuery("INSERT INTO `#__osrs_categories` (`id`, `parent_id`, `category_name`,`category_alias`, `category_image`, `category_description`, `access`, `ordering`, `published`,`category_meta`) VALUES
(1, 0, 'Additions & Remodels','', 'categoryimage.jpg', '<p>Additions &amp; Remodels category</p>', 1, 1, 1,''),
(3, 0, 'Roofing, Siding & Gutters','', 'categoryimage.jpg', 'Roofing, Siding &amp; Gutters category description', 1, 2,1,''),
(4, 0, 'Flooring', 'categoryimage.jpg','', 'Flooring category description', 1, 2,  1,''),
(5, 0, 'Home Construction','', 'categoryimage.jpg', 'Home Construction category description', 1, 3, 1,''),
(6, 0, 'Advertising listing','', 'categoryimage.jpg', 'Advertising listing category', 1, 1,  1,''),
(7, 0, 'Landscape, Decks & Fences','', 'categoryimage.jpg', 'Landscape, Decks &amp; Fences category description', 1, 4, 1,''),
(8, 0, 'Roofing, Siding & Gutters','', 'categoryimage.jpg', 'Roofing, Siding &amp; Gutters category description', 1, 5, 1,''),
(9, 0, 'Walls & CeilingsWalls & Ceilings','', 'categoryimage.jpg', 'Walls &amp; Ceilings category description', 1, 7, 1,''),
(10, 0, 'Builders, Architects & Designers','', 'categoryimage.jpg', 'Builders, Architects &amp; Designers category description', 1, 6,  1,''),
(11, 0, 'Expert Advice','', 'categoryimage.jpg', 'Expert Advice category description', 0, 8, 1,'');");
		$db->execute();
		//property types
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_types");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_types");
			$db->execute();
		}
		$db->setQuery("INSERT INTO `#__osrs_types` (`id`, `type_name`, `type_alias`, `type_description`, `published`,`type_icon`) VALUES
						(1, 'For sale', 'for-sale', '', 1,''),
						(2, 'For lease', 'for-lease', '', 1,''),
						(3, 'For rent', 'for-rent', '', 1,''),
						(4, 'Pending', '', '', 1, ''),
						(5, 'Sold', '', '', 1,''),
						(6, 'For sale or lease', '', '', 1,'');");
		$db->execute();
		
		//amenities
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_amenities");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_amenities");
			$db->execute();
		}
		$db->setQuery("INSERT INTO `#__osrs_amenities` (`id`, `category_id`, `amenities`, `ordering`, `published`) VALUES
					(1, 4, 'Gas Hot Water', 1, 1),
					(2, 6, 'Central Air', 1, 1),
					(3, 0, 'Cable Internet', 1, 1),
					(4, 0, 'Cable TV', 2, 1),
					(5, 0, 'Electric Hot Water', 3, 1),
					(6, 2, 'Freezer', 1, 1),
					(7, 3, 'Swimming Pool', 1, 1),
					(8, 0, 'Skylights', 4, 1),
					(9, 2, 'Microwave', 2, 1),
					(10, 0, 'Sprinkler System', 5, 1),
					(11, 0, 'Wood Stove', 6, 1),
					(12, 5, 'Fruit Trees', 1, 1),
					(13, 7, 'Skylights', 1, 1),
					(14, 2, 'Washer/Dryer', 3, 1),
					(15, 2, 'Dishwasher', 3, 1),
					(16, 7, 'Landscaping', 2, 1),
					(17, 5, 'Boat Slip', 2, 1),
					(18, 8, 'Burglar Alarm', 1, 1),
					(19, 6, 'Carpet Throughout', 2, 1),
					(20, 6, 'Central Vac', 3, 1),
					(21, 5, 'Covered Patio', 3, 1),
					(22, 5, 'Exterior Lighting', 4, 1),
					(23, 5, 'Fence', 5, 1),
					(24, 4, 'Fireplace', 2, 1),
					(25, 5, 'Garage', 6, 1),
					(26, 2, 'Garbage Disposal', 4, 1),
					(27, 4, 'Gas Fireplace', 3, 1),
					(28, 4, 'Gas Stove', 4, 1),
					(29, 5, 'Gazebo', 7, 1),
					(30, 2, 'Grill Top', 5, 1),
					(31, 1, 'Handicap Facilities', 1, 1),
					(32, 6, 'Jacuzi Tub', 4, 1),
					(33, 7, 'Lawn', 3, 1),
					(34, 5, 'Open Deck', 8, 1),
					(35, 5, 'Pasture', 9, 1),
					(36, 4, 'Pellet Stove', 5, 1),
					(37, 4, 'Propane Hot Water', 6, 1),
					(38, 2, 'Range/Oven', 6, 1),
					(39, 2, 'Refrigerator', 7, 1),
					(40, 2, 'RO Combo Gas/Electric', 8, 1),
					(41, 5, 'RV Parking', 10, 1),
					(42, 0, 'Satellite Dish', 7, 1),
					(43, 5, 'Spa/Hot Tub', 11, 1),
					(44, 8, 'Sprinkler System', 2, 1),
					(45, 3, 'Tennis Court', 2, 1),
					(46, 3, 'Football ground', 3, 1),
					(47, 2, 'Trash Compactor', 9, 1),
					(48, 0, 'Water Softener', 8, 1),
					(49, 1, 'Wheelchair Ramp', 2, 1),
					(50, 4, 'Wood Stove', 7, 1);");
		$db->execute();
		
		//groups
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_fieldgroups");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_fieldgroups");
			$db->execute();
		}
		$db->setQuery("INSERT INTO `#__osrs_fieldgroups` (`id`, `group_name`, `access`, `ordering`, `published`) VALUES
						(1, 'Property area details', 1, 1, 0),
						(2, 'Sale additional information', 0, 2, 0),
						(3, 'Other features information', 0, 3, 0),
						(4, 'Facts', 0, 4, 1),
						(5, 'Construction', 0, 5, 1),
						(6, 'Other info', 0, 6, 1);");
		$db->execute();
		//extra fields
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_extra_fields");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_extra_fields");
			$db->execute();
		}
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_extra_field_options");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_extra_field_options");
			$db->execute();
		}
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_extra_field_types");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_extra_field_types");
			$db->execute();
		}
		$db->setQuery("SELECT COUNT(id) FROM `#__osrs_property_open`");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM `#__osrs_property_open`");
			$db->execute();
		}
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_neighborhood");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_neighborhood");
			$db->execute();
		}
		$db->setQuery("SELECT COUNT(id) FROM `#__osrs_property_price_history`");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM `#__osrs_property_price_history`");
			$db->execute();
		}
		$extrafieldsSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/extrafields.osproperty.sql' ;
    	$sql = file_get_contents($extrafieldsSql) ;
		$queries = $db->splitSql($sql);
		if (count($queries)) {
			foreach ($queries as $query) {
			$query = trim($query);
			if ($query != '' && $query[0] != '#') {
					$db->setQuery($query);
					$db->execute();						
				}	
			}
		}
		//properties
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_properties");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_properties");
			$db->execute();
		}
		$propertiesSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/properties.osproperty.sql' ;
    	$sql = file_get_contents($propertiesSql) ;
    	$sql = str_replace("@",$agent_id,$sql);
    	$sql = str_replace("city_id",$city,$sql);
    	$sql = str_replace("state_id",$state,$sql);
    	$sql = str_replace("country_id",$country,$sql);
		$queries = $db->splitSql($sql);
		if (count($queries)) {
			foreach ($queries as $query) {
			$query = trim($query);
			if ($query != '' && $query[0] != '#') {
					$db->setQuery($query);
					$db->execute();						
				}	
			}
		}
		
		//properties - amenities
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_property_amenities");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_property_amenities");
			$db->execute();
		}
		$propertyAmenitiesSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/propertyamenities.osproperty.sql' ;
    	$sql = file_get_contents($propertyAmenitiesSql) ;
		$queries = $db->splitSql($sql);
		if (count($queries)) {
			foreach ($queries as $query) {
			$query = trim($query);
			if ($query != '' && $query[0] != '#') {
					$db->setQuery($query);
					$db->execute();						
				}	
			}
		}
		//properties - field values
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_property_field_value");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_property_field_value");
			$db->execute();
		}
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_property_field_opt_value");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_property_field_opt_value");
			$db->execute();
		}
		$fieldvalueSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/fieldvalue.osproperty.sql' ;
    	$sql = file_get_contents($fieldvalueSql) ;
		$queries = $db->splitSql($sql);
		if (count($queries)) {
			foreach ($queries as $query) {
			$query = trim($query);
			if ($query != '' && $query[0] != '#') {
					$db->setQuery($query);
					$db->execute();						
				}	
			}
		}
		//propertoes - photos
		$db->setQuery("SELECT COUNT(id) FROM #__osrs_photos");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("DELETE FROM #__osrs_photos");
			$db->execute();
		}
		$photoSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/photos.osproperty.sql' ;
    	$sql = file_get_contents($photoSql) ;
		$queries = $db->splitSql($sql);
		if (count($queries)) {
			foreach ($queries as $query) {
			$query = trim($query);
			if ($query != '' && $query[0] != '#') {
					$db->setQuery($query);
					$db->execute();						
				}	
			}
		}
		
		$mainframe->redirect("index.php?option=com_osproperty&task=properties_uploadphotopackages");
	}
	
	/**
	 * Build the select list for parent menu item
	 */
	static function listCategories($category_id,$onChangeScript){
		global $jinput, $mainframe;
		$parentArr = OspropertyProperties::loadCategoryOptions($category_id,$onChangeScript);
		$output = HTMLHelper::_('select.genericlist', $parentArr, 'category_id', 'style="width:250px;" class="input-medium form-select" '.$onChangeScript, 'value', 'text', $category_id );
		
		return $output;
	}

	/**
	 * Build the select list for parent menu item
	 */
	static function listFilterCategories($category_id,$onChangeScript)
	{
		global $jinput, $mainframe;
		if(OSPHelper::isJoomla4())
		{
			$class = "imedium form-select";
		}
		else
		{
			$class = "chosen";
		}
		$parentArr = OspropertyProperties::loadCategoryFilterOptions($category_id,$onChangeScript);
		$output = HTMLHelper::_('select.genericlist', $parentArr, 'category_id', 'class="'.$class.' input-large form-select" '.$onChangeScript, 'value', 'text', $category_id );
		return $output;
	}
	
    static function loadCategoryFilterOptions($category_id,$onChangeScript){
		global $jinput, $mainframe;
		$db =& Factory::getDBO();
		// get a list of the menu items
		// excluding the current cat item and its child elements
		$query = 'SELECT *, id as value,category_name AS title,parent_id as parent ' .
				 ' FROM #__osrs_categories ' .
				 ' WHERE published = 1' .
			 	 ' ORDER BY parent_id, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();

		// establish the hierarchy of the menu
		$children = [];

		if ( $mitems )
		{
			// first pass - collect children
			foreach ( $mitems as $v )
			{
				$pt 	= $v->parent_id;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = HTMLHelper::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		// assemble menu items to the array
		$parentArr 	= [];
		$parentArr[] 	= HTMLHelper::_('select.option',  '', Text::_( 'OS_ALL_CATEGORIES' ) );
		
		foreach ( $list as $item ) {
			if($item->treename != ""){
				//$item->treename = str_replace("&nbsp;","",$item->treename);
			}
			$var = explode("-",$item->treename);
			$treename = "";
			for($i=0;$i<count($var)-1;$i++){
				$treename .= " - ";
			}
			$text = $item->treename;
			$parentArr[] = HTMLHelper::_('select.option',  $item->id,$text);
		}
		return $parentArr;
	}
	
	static function loadCategoryOptions($category_id,$onChangeScript){
		global $jinput, $mainframe;
		$db =& Factory::getDBO();
		// get a list of the menu items
		// excluding the current cat item and its child elements
//		$query = 'SELECT *' .
		$query = 'SELECT *, id as value,category_name AS title,parent_id as parent ' .
				 ' FROM #__osrs_categories ' .
				 ' WHERE published = 1' .
			 	 ' ORDER BY parent_id, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();

		// establish the hierarchy of the menu
		$children = [];

		if ( $mitems )
		{
			// first pass - collect children
			foreach ( $mitems as $v )
			{
				$pt 	= $v->parent_id;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = HTMLHelper::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		// assemble menu items to the array
		$parentArr 	= [];
		//$parentArr[] 	= HTMLHelper::_('select.option',  '', Text::_( 'OS_ALL_CATEGORIES' ) );
		
		foreach ( $list as $item ) {
			if($item->treename != ""){
				//$item->treename = str_replace("&nbsp;","",$item->treename);
			}
			$var = explode("-",$item->treename);
			$treename = "";
			for($i=0;$i<count($var)-1;$i++){
				$treename .= " - ";
			}
			$text = $item->treename;
			$parentArr[] = HTMLHelper::_('select.option',  $item->id,$text);
		}
		return $parentArr;
	}
	
	/**
	 * Build the multiple select list for parent menu item
	 */
	static function listCategoriesCheckboxes($categoryArr){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Select count(id) from #__osrs_categories where published = '1'");
		$count_categories = $db->loadResult();
		$parentArr = OspropertyProperties::loadCategoryBoxes($categoryArr);
	 	ob_start();
	 	?>
	 	<input type="checkbox" name="check_all_cats" id="check_all_cats" value="1" checked onclick="javascript:checkCats()" />&nbsp;&nbsp;<strong><?php echo Text::_('OS_CATEGORIES')?></strong>
	 	<input type="hidden" name="count_categories" id="count_categories" value="<?php echo $count_categories?>" />
	 	<BR />
	 	<?php
	 	for($i=0;$i<count($parentArr);$i++){
	 		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$parentArr[$i];
	 		echo "<BR />";
	 	}
	 	$output = ob_get_contents();
	 	ob_end_clean();
		return $output;
	}
	
	static function loadCategoryBoxes($categoryArr){
		global $jinput, $mainframe;
		$db =& Factory::getDBO();
		// get a list of the menu items
		// excluding the current cat item and its child elements
//		$query = 'SELECT *' .
		$query = 'SELECT *, id as value,category_name AS title,parent_id as parent ' .
				 ' FROM #__osrs_categories ' .
				 ' WHERE published = 1' .
			 	 ' ORDER BY parent_id, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();

		// establish the hierarchy of the menu
		$children = [];

		if ( $mitems )
		{
			// first pass - collect children
			foreach ( $mitems as $v )
			{
				$pt 	= $v->parent_id;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = HTMLHelper::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		// assemble menu items to the array
		$parentArr 	= [];
		
		foreach ( $list as $item ) {
			if($item->treename != ""){
				$item->treename = str_replace("&nbsp;","",$item->treename);
			}
			$var = explode("-",$item->treename);
			$treename = "";
			for($i=0;$i<count($var)-1;$i++){
				$treename .= "- -";
			}
			$text = $treename.$item->category_name;
			if(in_array($item->value,$categoryArr)){
				$checked = "checked";
			}elseif(count($categoryArr) == 0){
				$checked = "checked";
			}else{
				$checked = "";
			}
			$parentArr[] = '<input type="checkbox" id="all_categories'.$item->value.'" name="categoryArr[]" '.$checked.' value="'.$item->value.'" />&nbsp;&nbsp;'.$text .'';
		}
		return $parentArr;
	}
	
	/**
	 * Property Edit/Add
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function properties_edit($option,$id)
	{
		global $jinput, $mainframe,$configClass,$languages;
		$db = Factory::getDBO();
		jimport('joomla.filesystem.folder');
		$document = Factory::getDocument();
		$row = Table::getInstance('Property','OspropertyTable');
		if($id > 0){
			//remove temp folder in tmp
			if(Folder::exists(JPATH_ROOT.'/tmp/osphoto_'.$id)){
				Folder::delete(JPATH_ROOT.'/tmp/osphoto_'.$id);
			}
			
			$row->load((int)$id);
			$db->setQuery("Select amen_id from #__osrs_property_amenities where pro_id = '$row->id'");
			$amenitylists = $db->loadOBjectList();
			$amenitylists1 = [];
			if(count($amenitylists) > 0){
				for($i=0;$i<count($amenitylists);$i++){
					$amenitylists1[$i] = $amenitylists[$i]->amen_id;
				}
				$amenitylists = [];
				$amenitylists = $amenitylists1;
			}
			
			$db->setQuery("Select * from #__osrs_photos where pro_id = '$id' order by ordering");
			$photos = $db->loadobjectList();
			$row->photo = $photos;
			
			OSPHelper::createPhotoDirectory($id);
			OSPHelper::movingPhoto($id);
		}
		else
		{
			$row->published			= 1;
			$row->access			= 0;
			$row->isFeatured		= 0;
			$row->show_address		= 1;
			$row->approved			= 1;
			$row->agent_id			= OSPHelper::getDefaultAgent();
		}
		$optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
		$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
		
		//$lists['state'] = HTMLHelper::_('select.genericlist',$optionArr,'published','class="input-mini form-control"','value','text',$row->published);
		$lists['state'] = OSPHelper::getBooleanInput('published',$row->published);
		//$lists['featured'] = HTMLHelper::_('select.genericlist',$optionArr,'isFeatured','class="input-mini form-control"','value','text',$row->isFeatured);
		$lists['featured'] = OSPHelper::getBooleanInput('isFeatured',$row->isFeatured);
		//$lists['approved'] = HTMLHelper::_('select.genericlist',$optionArr,'approved','class="input-mini form-control"','value','text',$row->approved);
		$lists['approved'] = OSPHelper::getBooleanInput('approved',$row->approved);

		$optionArr1 = [];
		$optionArr1[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
		$optionArr1[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
		$lists['price_call'] = HTMLHelper::_('select.genericlist',$optionArr,'price_call','class="input-mini form-select" onChange="javascript:showPriceFields()"','value','text',(int)$row->price_call);

		$lists['show_address'] = OSPHelper::getBooleanInput('show_address',$row->show_address);
		//$lists['property_sold'] = HTMLHelper::_('select.genericlist',$optionArr,'isSold','class="input-mini form-control"','value','text',$row->isSold);
		//$lists['property_sold'] = OSPHelper::getBooleanInput('isSold',$row->isSold);
		//agent
		$agentArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_AGENT'));
		$query  = "Select a.id as value,a.name as text from #__osrs_agents as a inner join #__users as b on b.id = a.user_id where published = '1' ";
		$query .= " order by a.name";
		$db->setQuery($query);
		$agents = $db->loadObjectList();
		$agentArr   = array_merge($agentArr,$agents);
		$lists['agent'] = HTMLHelper::_('select.genericlist',$agentArr,'agent_id','class="input-small form-select"','value','text',$row->agent_id);
		
		//property types
		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_PROPERTY_TYPE'));
		$db->setQuery("Select id as value,type_name as text from #__osrs_types where published = '1' order by type_name");
		$protypes = $db->loadObjectList();
		$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'pro_type','class="input-large form-select ilarge"','value','text',$row->pro_type);
		
		//categories
		//$lists['category'] = OspropertyProperties::listCategories($row->category_id,'');
		$categoryIds = OSPHelper::getCategoryIdsOfProperty($row->id);
		$lists['category'] = OSPHelper::getChoicesJsSelect(OSPHelper::dropdownCategory('categoryIds[]',$categoryIds,'input-large form-select'));
		
		$lists['country'] = HelperOspropertyCommon::makeCountryList($row->country,'country','onChange="javascript:loadStateBackend(this.value,\''.$row->state.'\',\''.$row->city.'\')"','','class="input-large form-select"');
		
		if(OSPHelper::userOneState()){
			$lists['states'] = OSPHelper::returnDefaultStateName()."<input type='hidden' name='state' id='state' value='".OSPHelper::returnDefaultState()."'/>";
		}else{
			$lists['states'] = HelperOspropertyCommon::makeStateList($row->country,$row->state,'state','onChange="javascript:loadCityBackend(this.value,\''.$row->city.'\')"',Text::_('OS_SELECT_STATE'),'class="input-large form-select ilarge"');
		}
		
		if($id > 0){
			if(intval($row->state) == 0){
				$row->state = OSPHelper::returnDefaultState();
			}
			$lists['city'] = HelperOspropertyCommon::loadCity($option,$row->state,$row->city,'input-large form-select');
		}else{
			$default_state = 0;
			if(OSPHelper::userOneState()){
				$default_state = OSPHelper::returnDefaultState();
			}
			$lists['city'] = HelperOspropertyCommon::loadCity($option,$default_state,0,'input-large form-select');
		}
		
		//number bed rooms
		$lists['nbed'] 		= OSPHelper::dropdropBed('bed_room',$row->bed_room,'input-small form-select','','OS_BED');
		//number bath rooms
		$lists['nbath'] 	= OSPHelper::dropdropBath('bath_room',$row->bath_room,'input-small form-select','','OS_BATH');
		//number rooms
		$lists['nrooms'] 	= OSPHelper::dropdropRoom('rooms',$row->rooms,'input-small form-select','','OS_ROOMS');
        //number floors
        $lists['nfloors'] 	= OSPHelper::dropdropFloor('number_of_floors',$row->number_of_floors,'input-small form-select','','OS_FLOORS');

        $lists['access'] 	= OSPHelper::accessDropdown('access',$row->access);

		$db->setQuery("select * from #__osrs_amenities where published = '1' order by ordering");
		$amenities = $db->loadObjectList();
		
		
		$db->setQuery("Select * from #__osrs_fieldgroups where id in (Select group_id from #__osrs_extra_fields) and published = '1' order by ordering");
		$groups = $db->loadObjectList();
		if(count($groups) > 0){
			for($i=0;$i<count($groups);$i++){
				$group = $groups[$i];
				$extraSql = "";
				$db->setQuery("Select * from #__osrs_extra_fields where published = '1' and group_id = '$group->id' $extraSql order by ordering");
				$fields = $db->loadObjectList();
				$groups[$i]->fields = $fields;
			}
		}
		
		$timeArr[] = HTMLHelper::_('select.option','',Text::_('OS_NOT_APPLICABLE'));
		$timeArr[] = HTMLHelper::_('select.option','OS_PER_NIGHT',Text::_('OS_PER_NIGHT'));
		$timeArr[] = HTMLHelper::_('select.option','OS_PER_WEEK',Text::_('OS_PER_WEEK'));
		$timeArr[] = HTMLHelper::_('select.option','OS_PER_MONTH',Text::_('OS_PER_MONTH'));
		$timeArr[] = HTMLHelper::_('select.option','OS_PER_SQUARE_FEET',Text::_('OS_PER_SQUARE_FEET'));
		$timeArr[] = HTMLHelper::_('select.option','OS_PER_SQUARE_METRE',Text::_('OS_PER_SQUARE_METRE'));
		$lists['time'] = HTMLHelper::_('select.genericlist',$timeArr,'rent_time','class="input-medium form-select"','value','text',$row->rent_time);
		
		$db->setQuery("Select * from #__osrs_neighborhoodname");
		$neighborhoods = $db->loadObjectList();
		
		$translatable = Multilanguage::isEnabled() && count($languages);
        
        if($row->id){
        	$query = "Select a.keyword from #__osrs_tags as a inner join #__osrs_tag_xref as b on b.tag_id = a.id where b.pid = '$row->id'";
        	$db->setQuery($query);
        	$row->tags = $db->loadColumn();
        }
        
        $db->setQuery("Select id from #__osrs_types order by ordering");
        $types = $db->loadObjectList();
        if(count($types) > 0){
        	foreach ($types as $type){
        		$db->setQuery("Select a.fid from #__osrs_extra_field_types as a left join #__osrs_extra_fields as b on b.id = a.fid where a.type_id = '$type->id' and b.published = '1'");
        		$type->fields = $db->loadColumn(0);

				$db->setQuery("Select a.fid from #__osrs_extra_field_types as a left join #__osrs_extra_fields as b on b.id = a.fid where a.type_id = '$type->id' and b.published = '1' and b.required = '1'");
        		$type->required_fields = $db->loadColumn(0);

				$db->setQuery("Select b.field_name from #__osrs_extra_field_types as a left join #__osrs_extra_fields as b on b.id = a.fid where a.type_id = '$type->id' and b.published = '1' and b.required = '1'");
        		$type->required_fields_name = $db->loadColumn(0);

				$db->setQuery("Select b.field_label from #__osrs_extra_field_types as a left join #__osrs_extra_fields as b on b.id = a.fid where a.type_id = '$type->id' and b.published = '1' and b.required = '1'");
        		$type->required_fields_label = $db->loadColumn(0);
        	}
        }
		$lists['types'] = $types;
		if($row->id > 0){
			$query = $db->getQuery(true);
			$query->select("*")->from("#__osrs_property_price_history")->where("pid = '$row->id'");
			$db->setQuery($query);
			$lists['history'] = $db->loadObjectList();
			
			$query = $db->getQuery(true);
			$db->setQuery("Select a.* from #__osrs_tags as a inner join #__osrs_tag_xref as b on a.id = b.tag_id where b.pid = '$row->id'");
			//$db->setQuery($query);
			$lists['tags'] = $db->loadObjectList();
			
			$query = $db->getQuery(true);
			$query->select("*")->from("#__osrs_property_history_tax")->where("pid = '$row->id'");
			$db->setQuery($query);
			$lists['tax'] = $db->loadObjectList();
			
			$query = $db->getQuery(true);
			$query->select("*")->from("#__osrs_property_open")->where("pid = '$row->id'")->order("start_from desc");
			$db->setQuery($query);
			$lists['open'] = $db->loadObjectList();
		}

		if(($row->posted_by == 1) and ($row->company_id > 0)){
			$db->setQuery("Select company_name from #__osrs_companies where id = '$row->company_id'");
			$row->company_name = $db->loadResult();
		}else{
			$db->setQuery("Select name from #__osrs_agents where id = '$row->agent_id'");
			$row->agent_name = $db->loadResult();
		}

		if($configClass['more_bath_infor']== 1 && $configClass['use_bathrooms']== 1)
		{
			$bathInfor = [];
			if($id > 0)
			{
				$bathLabelArray = ['OS_FULL','OS_THREE_QUARTER','OS_HALF','OS_QUARTER','OS_ENSUITE'];
				foreach($bathLabelArray as $label)
				{
					$bathInfor[$label] = 0;
					$db->setQuery("Select `bath_value` from #__osrs_property_bath_values where pid = '$id' and bath_label like '$label'");
					$bath_value = $db->loadResult();
					if($bath_value > 0)
					{
						$bathInfor[$label] = $bath_value;
					}
				}
			}
			$lists['bathInfor'] = $bathInfor;
		}

		
		$document->addScript(Uri::root().'media/com_osproperty/assets/js/djuploader.js');
		$settings					= [];
		$settings['max_file_size']	= '10240kb';
		$settings['chunk_size']		= '1024kb';
		$settings['resize']			= false;
		$settings['width']			= $configClass['max_width_size'];
		$settings['height']			= $configClass['max_height_size'];
		$settings['quality']		= 100;
		$settings['filter']			= 'jpg,png,gif,jpeg,webp';
		$settings['onUploadedEvent'] = 'injectUploaded';
		$settings['onAddedEvent']	= 'startUpload';
		$settings['label_generate'] = 1;
		//$settings['debug'] = true;
		$lists['uploader'] = DJUploadHelper::getUploader('uploader', $settings);

		HTML_OspropertyProperties::editProperty($option,$row,$lists,$amenities,$amenitylists,$groups,$neighborhoods,$translatable);
	}
	
	static function getUserInput($agent_id){
		if (version_compare(JVERSION, '3.5', 'le'))
        {
			$onchange = "";
			// Initialize variables.
			$html = [];
			//$groups = $this->getGroups();
			//$excluded = $this->getExcluded();
			$link = 'index.php?option=com_osproperty&task=agent_list&tmpl=component&field=agent_id';

			// Initialize some field attributes.
			$attr = ' class="inputbox"';
			//$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

			// Initialize JavaScript field attributes.
			//$onchange = (string) $this->element['onchange'];

			// Load the modal behavior script.
			HTMLHelper::_('behavior.modal');
			HTMLHelper::_('behavior.modal', 'a.modal_user_id');

			// Build the script.
			$script = [];
			$script[] = '	static function jSelectUser_agent_id(id, title, object) {';
			$script[] = '		var old_id = document.getElementById("agent_id").value;';
			$script[] = '		if (old_id != id) {';
			$script[] = '			document.getElementById("agent_id").value = id;';
			$script[] = '			document.getElementById("agent_id_name").value = title;';
			$script[] = '			' . $onchange;
			$script[] = '		}';
			$script[] = '		SqueezeBox.close();';
			$script[] = '	}';

			// Add the script to the document head.
			Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

			// Load the current username if available.
			$table = Table::getInstance('Agent','OspropertyTable');
			
			if ($agent_id)
			{
				$table->load($agent_id);
			}
			else
			{
				$table->username = Text::_('OS_SELECT_AGENT');
			}

			// Create a dummy text field with the user name.
			$html[] = '<span class="input-append">';
			$html[] = '<input type="text" class="input-medium" id="agent_id_name" value="'.htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') .'" disabled="disabled" size="35" /><a class="modal btn" title="'.Text::_('JLIB_FORM_CHANGE_USER').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.Text::_('JLIB_FORM_CHANGE_USER').'</a>';
			$html[] = '</span>';

			// Create the real field, hidden, that stored the user id.
			$html[] = '<input type="hidden" id="agent_id" name="agent_id" value="'.$agent_id.'" />';

			return implode("\n", $html);
		}
		else
		{
			HTMLHelper::_('jquery.framework');
            FormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_osproperty/fields');

            if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
            {
                FormHelper::addFieldPrefix('Joomla\Component\Osproperty\Administrator\Field');
            }
            $field = FormHelper::loadFieldType('Modal_Agent');
            $element = new SimpleXMLElement('<field />');
            $element->addAttribute('name', 'agent_id');
            $element->addAttribute('select', 'true');
            $element->addAttribute('clear', 'true');
            $element->addAttribute('class', 'readonly');
            $field->setup($element, $agent_id);

            return $field->input;
		}
	}
	
	/**
	 * Load States
	 *
	 * @param unknown_type $option
	 */
	static function loadStates($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$country_id = $jinput->getInt('country_id',0);
		$stateArr = [];
		$stateArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_STATE'));
		$query  = "Select id as value,state_name as text from #__osrs_states where 1=1 ";
		if($country_id != ""){
			$query .= " and country_id = '$country_id'";
		}
		$query .= " order by state_name";
		$db->setQuery($query);
		$states = $db->loadObjectList();
		$stateArr   = array_merge($stateArr,$states);
		echo HTMLHelper::_('select.genericlist',$stateArr,'state','class="input-small form-control"','value','text');
		echo '<span class="required">(*)</span>';
	}
	
	/**
	 * Go to list
	 *
	 * @param unknown_type $option
	 */
	static function gotolist($option){
		global $jinput, $mainframe;
		$mainframe->redirect("index.php?option=com_osproperty&task=properties_list");
	}
	
	/**
	 * Copy properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function propertycopy($option,$id){
		global $jinput, $mainframe,$languages,$lang_suffix;
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$db                     = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property               = $db->loadObject();
		
		$row                    = Table::getInstance('Property','OspropertyTable');
		$row->id                = 0;
		$row->pro_name          = Text::_('OS_COPY').": ".$property->pro_name;
		$row->pro_alias			= "";
		$row->ref               = "COPY".$property->ref;
		$row->pro_browser_title = $property->pro_browser_title;
		$row->agent_id          = $property->agent_id;
		$row->category_id       = $property->category_id;
		$row->price             = $property->price;
		$row->price_original    = $property->price_original;
		$row->curr              = $property->curr;
		$row->pro_small_desc    = $property->pro_small_desc;
		$row->pro_full_desc     = $property->pro_full_desc;
		$row->pro_type          = $property->pro_type;
		$row->isFeatured 	    = $property->isFeatured 	;
		$row->note              = $property->note;
		$row->lat_add           = $property->lat_add;
		$row->long_add          = $property->long_add;
		$row->gbase_address     = $property->gbase_address;
		$row->price_call        = $property->price_call;
		$row->price_text        = $property->price_text;
		$row->e_class           = $property->e_class;
		$row->c_class           = $property->c_class;
		$row->pro_video         = $property->pro_video;
		$row->address           = $property->address;
		$row->city              = $property->city;
		$row->state             = $property->state;
		$row->region            = $property->region;
		$row->country           = $property->country;
		$row->province          = $property->province;
		$row->postcode          = $property->postcode;
		$row->show_address      = $property->show_address;
		$row->metakey           = $property->metakey;
		$row->created           = date("Y-m-d",time());
		$row->created_by        = $property->created_by;
		$row->access 	        = $property->access ;
		$row->publish_up 	    = $property->publish_up ;
		$row->publish_down 	    = $property->publish_down ;
		$row->published 	    = $property->published ;
		$row->panorama 			= $property->panorama ;
		$row->approved 	        = $property->approved ;
		$row->pro_pdf 	        = $property->pro_pdf ;
		$row->pro_pdf_file 	    = $property->pro_pdf_file ;
		for($i = 1; $i<10; $i++)
		{
			$row->{'pro_pdf_file'.$i} = (string)$property->{'pro_pdf_file'.$i};
		}
		$row->bed_room 	        = $property->bed_room ;
		$row->bath_room         = $property->bath_room 	 ;
		$row->rooms 	        = $property->rooms ;
		$row->parking 	        = $property->parking ;
		$row->square_feet 	 	= $property->square_feet 	 ;
		$row->lot_size	        = $property->lot_size;
		$row->number_of_floors 	= $property->number_of_floors ;
		$row->posted_by         = $property->posted_by;
		$row->living_areas      = $property->living_areas;
		$row->garage_description = $property->garage_description;
		$row->built_on          = $property->built_on;
		$row->remodeled_on      = $property->remodeled_on;
		$row->house_style       = $property->house_style;
		$row->house_construction = $property->house_construction;
		$row->exterior_finish   = $property->exterior_finish;
		$row->roof              = $property->roof;
		$row->flooring          = $property->flooring;
        $row->floor_area_lower  = $property->floor_area_lower;
        $row->floor_area_main_level = $property->floor_area_main_level;
        $row->floor_area_upper  = $property->floor_area_upper;
        $row->floor_area_total  = $property->floor_area_total;
        $row->basement_foundation = $property->basement_foundation;
        $row->basement_size     = $property->basement_size;
        $row->percent_finished  = $property->percent_finished;
        $row->subdivision       = $property->subdivision;
        $row->land_holding_type = $property->land_holding_type;
        $row->land_area         = $property->land_area;
        $row->total_acres       = $property->total_acres;
        $row->lot_dimensions    = $property->lot_dimensions;
        $row->depth             = $property->depth;
        $row->takings           = $property->takings;
        $row->returns           = $property->returns;
        $row->net_profit        = $property->net_profit;
        $row->business_type     = $property->business_type;
        $row->stock             = $property->stock;
        $row->fixtures          = $property->fixtures;
        $row->fittings          = $property->fittings;
        $row->percent_office    = $property->percent_office;
        $row->percent_warehouse = $property->percent_warehouse;
        $row->loading_facilities= $property->loading_facilities;
        $row->fencing           = $property->fencing;
        $row->rainfall          = $property->rainfall;
        $row->soil_type         = $property->soil_type;

        $row->grazing          = $property->grazing;
        $row->cropping         = $property->cropping;
        $row->irrigation       = $property->irrigation;
        $row->water_resources  = $property->water_resources;
        $row->carrying_capacity= $property->carrying_capacity;
        $row->storage          = $property->storage;

		$row->number_votes 	    = 0 ;
		$row->total_points 	    = 0 ;
		$row->request_to_approval 	 = $property->request_to_approval ;
		$row->request_featured 	= $property->request_featured ;
        $row->energy            = $property->energy;
        $row->climate           = $property->climate;
		$row->hits              = 0;
		$row->isSold            = $property->isSold;
		$row->soldOn            = $property->soldOn;
		$row->rent_time			= $property->rent_time;
		//$row->inform_time		=
		if (!$row->store()) 
		{
			//JError::raiseError(500, $row->getError() );
			throw new Exception($row->getError(), 500);
		}
		
		$newId                  = $db->insertid();
		//add into #__osrs_new_properties
        OSPHelper::addPropertyToQueue($newId,true);

		//multiple language?
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable)
		{
			foreach ($languages as $language)
			{
				$sef = $language->sef;
				$pro_name_language 			= $property->{'pro_name_'.$sef};
				$pro_alias_language 		= $property->{'pro_alias_'.$sef};
				$pro_small_desc_language 	= $property->{'pro_small_desc_'.$sef};
				$pro_full_desc_language 	= $property->{'pro_full_desc_'.$sef};
				$pro_browser_title          = $property->{'pro_browser_title_'.$sef};
				if($pro_name_language != ""){
					$new_property 						= Table::getInstance('Property','OspropertyTable');
					$new_property->id 					= $newId;
					$new_property->category_id 			= $property->category_id;
					$new_property->access 				= $property->access;
					$new_property->{'pro_name_'.$sef} 	= $pro_name_language;
					$new_property->store();
				}
				if($pro_alias_language != "")
				{
					$new_property 						= Table::getInstance('Property','OspropertyTable');
					$new_property->id 					= $newId;
					$new_property->category_id 			= $property->category_id;
					$new_property->access 				= $property->access;
					$new_property->{'pro_alias_'.$sef} 	= $pro_alias_language;
					$new_property->store();
				}
				if($pro_small_desc_language != "")
				{
					$new_property 							= Table::getInstance('Property','OspropertyTable');
					$new_property->id 						= $newId;
					$new_property->category_id 				= $property->category_id;
					$new_property->access 					= $property->access;
					$new_property->{'pro_small_desc_'.$sef} = $pro_small_desc_language;
					$new_property->store();
				}
				if($pro_full_desc_language != "")
				{
					$new_property 							= Table::getInstance('Property','OspropertyTable');
					$new_property->id 						= $newId;
					$new_property->category_id 				= $property->category_id;
					$new_property->access 					= $row->access;
					$new_property->{'pro_full_desc_'.$sef} 	= $pro_full_desc_language;
					$new_property->store();
				}
                if($pro_browser_title != "")
                {
                    $new_property 							= Table::getInstance('Property','OspropertyTable');
                    $new_property->id 						= $newId;
                    $new_property->category_id 				= $property->category_id;
                    $new_property->access 					= $row->access;
                    $new_property->{'pro_browser_title_'.$sef} 	= $pro_browser_title;
                    $new_property->store();
                }
			}
		}
		
		$alias = $property->pro_alias;
		$alias = $alias."_".$newId;
		$db->setQuery("Update #__osrs_properties set pro_alias = '$alias' where id = '$newId'");
		$db->execute();
		
		//update extra photos
		jimport('joomla.filesystem.file');
		$db->setQuery("Select * from #__osrs_photos where pro_id = '$id'");
		$photos = $db->loadObjectList();
		
		//prepare copy photos
		//create photo folder
		if(!Folder::exists(JPATH_ROOT."/images/osproperty/properties/".$newId)){
			Folder::create(JPATH_ROOT."/images/osproperty/properties/".$newId);
			Folder::create(JPATH_ROOT."/images/osproperty/properties/".$newId."/medium");
			Folder::create(JPATH_ROOT."/images/osproperty/properties/".$newId."/thumb");
		}
		if(count($photos) > 0){
			for($i=0;$i<count($photos);$i++){
				$photo = $photos[$i];
				$image = $photo->image;
				$image_desc = $photo->image_desc;
				$ordering = $photo->ordering;
				$newimage = "copy".$image;
				$imagepath = JPATH_ROOT."/images/osproperty/properties/".$id;
				$newimagepath = JPATH_ROOT."/images/osproperty/properties/".$newId;
				if(File::exists($imagepath."/".$image)){
					File::copy($imagepath."/".$image,$newimagepath."/".$newimage);
					File::copy($imagepath."/thumb/".$image,$newimagepath."/thumb/".$newimage);
					File::copy($imagepath."/medium/".$image,$newimagepath."/medium/".$newimage);
					$image_desc = addslashes($image_desc);
					$db->setQuery("INSERT INTO #__osrs_photos (id,pro_id,image,image_desc,ordering) VALUES (NULL,'$newId','$newimage','$image_desc','$ordering')");
					$db->execute();
				}
			}
		}
		
		//amenities
		$db->setQuery("Select * from #__osrs_property_amenities where pro_id = '$id'");
		$amens = $db->loadObjectList();
		if(count($amens) > 0){
			for($i=0;$i<count($amens);$i++){
				$amen = $amens[$i];
				$amen_id = $amen->amen_id;
				$db->setQuery("INSERT INTO #__osrs_property_amenities (id,pro_id,amen_id) VALUES (NULL,'$newId','$amen_id')");
				$db->execute();
			}
		}
		
		$db->setQuery("Select * from #__osrs_property_field_value where pro_id = '$id'");
		$fieldvalues = $db->loadOBjectList();
		if(count($fieldvalues) > 0){
			for($i=0;$i<count($fieldvalues);$i++){
				$field_id = $fieldvalues[$i]->field_id;
				$value = $fieldvalues[$i]->value;
				$db->setQuery("INSERT INTO #__osrs_property_field_value (id,pro_id,field_id,`value`) VALUES (NULL,'$newId','$field_id','$value')");
				$db->execute();
			}
		}

        $db->setQuery("Select * from #__osrs_property_field_opt_value where pid = '$id'");
        $fieldvalues = $db->loadOBjectList();
        if(count($fieldvalues) > 0){
            for($i=0;$i<count($fieldvalues);$i++){
                $fid = $fieldvalues[$i]->fid;
                $oid = $fieldvalues[$i]->oid;
                $db->setQuery("INSERT INTO #__osrs_property_field_opt_value (id,pid,fid,`oid`) VALUES (NULL,'$newId','$fid','$oid')");
                $db->execute();
            }
        }
		
		//copies categories
		$db->setQuery("Select category_id from #__osrs_property_categories where pid = '$id'");
		$category_ids = $db->loadColumn(0);
		if(count($category_ids) > 0){
			foreach ($category_ids as $category_id){
				$db->setQuery("INSERT INTO #__osrs_property_categories (id,pid,category_id) VALUES (NULL,'$newId','$category_id')");
				$db->execute();
			}
		}
		
		$db->setQuery("Select * from #__osrs_property_open where pid = '$id'");
		$opens = $db->loadOBjectList();
		if(count($opens) > 0){
			for($i=0;$i<count($opens);$i++){
				$open = $opens[$i];
				$start_from = $open->start_from;
				$end_to = $open->end_to;
				
				$db->setQuery("INSERT INTO #__osrs_property_open (id,pid,start_from,end_to) VALUES (NULL,'$newId','$start_from','$end_to')");
				$db->execute();
			}
		}
		
		$db->setQuery("Select * from #__osrs_property_history_tax where pid = '$id'");
		$taxes = $db->loadOBjectList();
		if(count($taxes) > 0){
			for($i=0;$i<count($taxes);$i++){
				$tax = $taxes[$i];
				$tax_year = $tax->tax_year;
				$property_tax = $tax->property_tax;
				$tax_change  = $tax->tax_change;
				$tax_assessment = $tax->tax_assessment;
				$tax_assessment_change = $tax->tax_assessment_change;
				
				$db->setQuery("INSERT INTO #__osrs_property_history_tax (id,pid,tax_year,property_tax,tax_change,tax_assessment,tax_assessment_change) VALUES (NULL,'$newId','$tax_year','$property_tax','$tax_change','$tax_assessment','$tax_assessment_change')");
				$db->execute();
			}
		}
		
		$db->setQuery("Select * from #__osrs_property_price_history where pid = '$id'");
		$prices = $db->loadOBjectList();
		if(count($prices) > 0){
			for($i=0;$i<count($prices);$i++){
				$price = $prices[$i];
				$date = $price->date;
				$event = $price->event;
				$price  = $price->price;
				$source  = $price->source;
				
				$db->setQuery("INSERT INTO #__osrs_property_price_history (id,pid,date,event,`price`,`source`) VALUES (NULL,'$newId','$date','$event','$price','$source')");
				$db->execute();
			}
		}
		
		$db->setQuery("Select * from #__osrs_neighborhood where pid = '$id'");
		$neighborhoods = $db->loadOBjectList();
		if(count($neighborhoods) > 0){
			for($i=0;$i<count($neighborhoods);$i++){
				$neighborhood = $neighborhoods[$i];
				$neighbor_id = $neighborhood->neighbor_id;
				$mins = $neighborhood->mins;
				$traffic_type = $neighborhood->traffic_type;
				
				$db->setQuery("INSERT INTO #__osrs_neighborhood (id,pid,neighbor_id,mins,traffic_type) VALUES (NULL,'$newId','$neighbor_id','$mins','$traffic_type')");
				$db->execute();
			}
		}
		
		$db->setQuery("Select * from #__osrs_tag_xref where pid = '$id'");
		$tags = $db->loadObjectList();
		if(count($tags) > 0){
			foreach($tags as $tag){
				$db->setQuery("INSERT INTO #__osrs_tag_xref (id,tag_id,pid) VALUES (NULL,'$tag->tag_id','$newId')");
				$db->execute();
			}
		}
		
		$db->setQuery("Select * from #__osrs_expired where pid = '$id'");
		$expired		= $db->loadObject();
		$inform_time	= $expired->inform_time;
		$expired_time	= $expired->expired_time;
		$send_inform	= $expired->send_inform;
		$send_expired	= $expired->send_expired;
		$expired_feature_time = $expired->expired_feature_time;
		$send_featured	= $expired->send_featured;
		$remove_from_database = $expired->remove_from_database;
		try
		{
			$db->setQuery("Insert into #__osrs_expired (id,pid,inform_time,send_inform,send_expired,expired_time,remove_from_database,send_featured,expired_feature_time) values (NULL,$newId,'$inform_time','$send_inform','$send_expired','$expired_time','$remove_from_database','$send_featured','$expired_feature_time')");
			$db->execute();
		}
		catch (Exception $e)
		{
			//do nothing
		}
		
		$msg = Text::_('OS_COPY_COMPLETED');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=properties_list");
	}
	
	
	/**
	 * Save
	 * Step 1: Save Information
	 * Step 2: Save image and extra fields
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 */
	static function save($option,$save)
    {
		global $jinput, $mainframe,$configClass,$languages;
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$limit_photo = $configClass['limit_upload_photos'];
		if(intval($limit_photo) == 0)
		{
			$limit_photo = 24;
		}
		$db = Factory::getDBO();
		jimport('joomla.filesystem.file');
		//check to see if user uploaded new state
		$country = $jinput->getInt('country',$configClass['show_country_id']);
		$nstate = $jinput->get('nstate','');
		if($nstate != "")
		{
			//insert into state table
			$db->setQuery("Insert into #__osrs_states (id,country_id,state_name,state_code) values (NULL,'$country','$nstate','$nstate')");
			$db->execute();
			$state = $db->insertID();
			$jinput->set('state',$state);
		}
		
		$db->setQuery("Select * from #__osrs_configuration");
		$configs = $db->loadObjectList();
		
		
		$row = Table::getInstance('Property','OspropertyTable');
		$post = $jinput->post->getArray();
		$row->bind($post);
		$id = $jinput->getInt('id',0);
		if($id > 0)
		{
			$isNew 		= 0;
			$db->setQuery("Select * from #__osrs_properties where id = '$id'");
			$property 	= $db->loadObject();
			$approved 	= $property->approved;
			$isFeatured = $property->isFeatured;
		}
		else
		{
			$isNew 		= 1;
			$approved 	= 0;
		}
		
		
		
		for($f = 0; $f < 10; $f++)
		{
			if($f == 0)
			{
				$fname = "";
			}
			else
			{
				$fname = $f;
			}
			$name = "pro_pdf_file".$fname;

			if(is_uploaded_file($_FILES[$name]['tmp_name']))
			{
				if(!HelperOspropertyCommon::checkIsDocumentFileUploaded($name))
				{
					//do nothing
				}
				else
				{
					if($id > 0)
					{
						$db->setQuery("Select `".$name."` from #__osrs_properties where id = '$id'");
						$document_file = $db->loadResult();
					}
					if($document_file != "")
					{
						if(file_exists(JPATH_ROOT."/components/com_osproperty/document/".$document_file))
						{
							@unlink(JPATH_ROOT."/components/com_osproperty/document/".$document_file);
						}
						elseif(file_exists(JPATH_ROOT."/media/com_osproperty/document/".$document_file))
						{
							@unlink(JPATH_ROOT."/media/com_osproperty/document/".$document_file);
						}
					}
					$pro_pdf_file = time()."_".$_FILES[$name]['name'];
					move_uploaded_file($_FILES[$name]['tmp_name'],JPATH_ROOT."/media/com_osproperty/document/".$pro_pdf_file);
					$row->{$name} = $pro_pdf_file;
				}
			}
			$remove_pdf = $jinput->getInt('remove_pdf'.$fname,0);
			if($remove_pdf == 1)
			{
				if(file_exists(JPATH_ROOT."/components/com_osproperty/document/".$document_file))
				{
					@unlink(JPATH_ROOT."/components/com_osproperty/document/".$document_file);
				}
				elseif(file_exists(JPATH_ROOT."/media/com_osproperty/document/".$document_file))
				{
					@unlink(JPATH_ROOT."/media/com_osproperty/document/".$document_file);
				}
				$row->{$name} = "";
			}
		}
		

		//panorama
		$remove_panorama = $jinput->getInt('remove_panorama',0);
		//echo $remove_panorama;die();
		if($id > 0)
		{
			$db->setQuery("Select panorama from #__osrs_properties where id = '$id'");
			$panorama_file = $db->loadResult();
		}
		if(is_uploaded_file($_FILES['panorama']['tmp_name']))
		{
			if(is_uploaded_file($_FILES['panorama']['tmp_name']))
			{
				if($document_file != "")
				{ // remove old file
					@unlink(JPATH_ROOT."/images/osproperty/properties/panorama/".$document_file);
				}
				$panorama = time()."_".$_FILES['panorama']['name'];
				move_uploaded_file($_FILES['panorama']['tmp_name'],JPATH_ROOT."/images/osproperty/properties/panorama/".$panorama);
				$row->panorama = $panorama;
			}
		}
		elseif($remove_panorama == 1)
		{
			if($panorama_file != "")
			{ // remove old file
				@unlink(JPATH_ROOT."/images/osproperty/properties/panorama/".$document_file);
			}
			$panorama_file = "";
			$row->panorama = "";
		}
		elseif($id > 0)
		{
			$row->panorama = $panorama_file;
		}
		
		$pro_video = $_POST['pro_video'];
		$row->pro_video = $pro_video;
		$pro_small_desc = $_POST['pro_small_desc'];
		$row->pro_small_desc = $pro_small_desc;
		$pro_full_desc = $_POST['pro_full_desc'];
		$row->pro_full_desc = $pro_full_desc;
		$note = $_POST['note'];
		$row->note = $note;
		$metakey = $_POST['metakey'];
		$row->metakey = $metakey;
		$metadesc = $_POST['metadesc'];
		$row->metadesc = $metadesc;
		
		$user = Factory::getUser();
		if($id == 0)
		{
			$row->created = date("Y-m-d",time());
			$row->created_by = $user->id;
			$row->hits =  0;
			$row->modified = $row->created;
		}
		else
		{
			$row->modified = date("Y-m-d",time());
			$row->modified_by = $user->id;
		}
		
		$lat_add = $jinput->getFloat('lat_add','');
		$long_add = $jinput->getFloat('long_add','');
		if(($lat_add == "") || ($long_add == ""))
		{
			$city = $jinput->getInt('city',0);
			if($city > 0)
			{
				$db->setQuery("Select city from #__osrs_cities where id = '$city'");
				$city_name = $db->loadResult();
			}
			$address_search = $jinput->getString('address','').", ".$city_name;
			$state = $jinput->getInt('state','');
			$db->setQuery("Select state_name from #__osrs_states where id = '$state'");
			$sname = $db->loadResult();
			$address_search .= ", ".$sname;
			$country = $jinput->getInt('country','');
			$db->setQuery("Select country_name from #__osrs_countries where id = '$country'");
			$cname = $db->loadResult();
			$address_search .= ", ".$cname;
//			echo $address_search;
			//$q = urlencode($address_search);
            if($configClass['map_type'] == 0)
            {
                include_once(JPATH_SITE."/components/com_osproperty/helpers/googlemap.lib.php");
                $return = HelperOspropertyGoogleMap::findAddress($option,'',$address_search,1);
            }
            else
            {
                include_once(JPATH_SITE."/components/com_osproperty/helpers/openstreetmap.lib.php");
                $return = HelperOspropertyOpenStreetMap::findAddress($option,'',$address_search,1);
            }

			if($return[2] == "OK")
			{
				$lat_add = $return[0];
				$long_add = $return[1];
			}
		}
		
		$row->lat_add			= $lat_add;
		$row->long_add			= $long_add;

		if($row->publish_up == "" || $row->publish_up == "0000-00-00 00:00:00")
		{
			$row->publish_up = "0000-00-00";
		}
		if($row->publish_down == "")
		{
			$row->publish_down = "0000-00-00";
		}

		$row->built_on			= (int) $row->built_on;
		$row->remodeled_on		= (int) $row->remodeled_on;
		$row->floor_area_lower	= (float) $row->floor_area_lower;
		$row->floor_area_main_level = (float) $row->floor_area_main_level;
		$row->floor_area_upper  = (float) $row->floor_area_upper;
		$row->floor_area_total	= (float) $row->floor_area_total;
		$row->basement_size     = (float) $row->basement_size;
		$row->total_acres		= (float) $row->total_acres;
		$row->price				= (float) $row->price;
		$row->bath_room			= (float) $row->bath_room;
		$row->square_feet		= (float) $row->square_feet;
		$row->lot_size			= (float) $row->lot_size;
		$row->energy			= (float) $row->energy;
		$row->climate			= (float) $row->climate;
		$row->bed_room			= (int) $row->bed_room;
		$row->rooms				= (int) $row->rooms;
		$row->number_of_floors	= (int) $row->number_of_floors;
		$row->state				= (int) $row->state;
		$row->city				= (int) $row->city;
		$row->rent_time			= (string) $row->rent_time;
		$access					= $jinput->getInt('access', 0);
		if($access == 0)
		{
			$access				= (int)$configClass['default_access_level'];
		}
		$row->access			= $access;
		$row->panorama			= (string) $row->panorama;
		$row->hits				= (int) $row->hits;
		for($i = 1; $i<10; $i++)
		{
			if($id > 0)
			{
				$db->setQuery("Select `pro_pdf_file".$i."` from #__osrs_properties where id = '$id'");
				$file_already_upload = $db->loadResult();
				if((string) $row->{'pro_pdf_file'.$i} == "" && $file_already_upload != "")
				{
					//do nothing
					if($jinput->getInt('remove_pdf'.$i, 0) == 1)
					{
						$row->{'pro_pdf_file'.$i} = "";
					}
					else
					{

						$row->{'pro_pdf_file'.$i} = $file_already_upload;
					}
				}
				elseif((string) $row->{'pro_pdf_file'.$i} != "" && $file_already_upload == "")
				{
					//do nothing
				}
				elseif((string) $row->{'pro_pdf_file'.$i} == "" && $file_already_upload == "")
				{
					$row->{'pro_pdf_file'.$i} = (string)$row->{'pro_pdf_file'.$i};
				}
			}
			else
			{
				$row->{'pro_pdf_file'.$i} = (string)$row->{'pro_pdf_file'.$i};
			}
		}
		//store into database
		if (!$row->store()) 
		{
			//JError::raiseError(500, $row->getError() );
			throw new Exception($row->getError(), 500);
		}
		//print_r($row);
		//die();
		//get id
		if($id == 0){
			$id = $db->insertID();
			$isNew = 1;
            //update ref field
            if($configClass['ref_field'] == 1){
				$ref_prefix = $configClass['ref_prefix'];
                $db->setQuery("Update #__osrs_properties set ref = '".$ref_prefix.$id."' where id = '$id'");
                $db->execute();
            }

            //from version 2.7.5
            //add ID of property into New Properties Table
            //$db->setQuery("Insert into #__osrs_new_properties (id,pid) values (NULL,'$id')");
            //$db->execute();
            OSPHelper::addPropertyToQueue($id, true);

			Folder::create(JPATH_ROOT."/images/osproperty/properties/".$id);
			Folder::create(JPATH_ROOT."/images/osproperty/properties/".$id."/thumb");
			Folder::create(JPATH_ROOT."/images/osproperty/properties/".$id."/medium");
			if($row->approved == 1){
				//setup the expired time
				OspropertyProperties::updateStatus($option,"approved",1,$id);
			}
			if($row->isFeatured == 1){
				//setup the expired time for feature
				OspropertyProperties::updateStatus($option,"isFeatured",1,$id);
			}
		}
		else
		{
			OSPHelper::addPropertyToQueue($id, false);
			$isNew = 0;
			if($row->ref == "" && $configClass['ref_field'] == 1)
			{
				$ref_prefix = $configClass['ref_prefix'];
				$db->setQuery("Update #__osrs_properties set ref = '".$ref_prefix.$id."' where id = '$id'");
				$db->execute();
			}
			if($row->approved == 1 && $approved == 0)
			{
				//set up expired time
				OspropertyProperties::updateStatus($option,"approved",1,$id);
			}
			if($row->isFeatured == 1 && $isFeatured == 0)
			{
				//set up expired time for feature
				OspropertyProperties::updateStatus($option,"isFeatured",1,$id);
			}
		}
		
		
		//Update into Property table
		$categoryIds = $jinput->get('categoryIds',array(),'ARRAY');
		$db->setQuery("Delete from #__osrs_property_categories where pid = '$id'");
		$db->execute();
		if(count($categoryIds) > 0){
			foreach ($categoryIds as $catid){
				$db->setQuery("Insert into #__osrs_property_categories (id,pid,category_id) values (NULL,'$id','$catid')");
				$db->execute();
			}
		}
		
		//alias
		$pro_alias = $jinput->getString('pro_alias','');
		if($pro_alias == ""){
			$pro_alias = OSPHelper::generateAlias('property',$id,'');
		}else{
			$pro_alias = OSPHelper::generateAlias('property',$id,$pro_alias);
		}
		$db->setQuery("Update #__osrs_properties set pro_alias = '$pro_alias' where id = '$id'");
		$db->execute();
		
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language){	
				$sef = $language->sef;
				
				$pro_name_language 			= $jinput->getString('pro_name_'.$sef,'');
                $address_language 			= $jinput->getString('address_'.$sef,'');
				$pro_small_desc_language 	= $_POST['pro_small_desc_'.$sef];
				$pro_full_desc_language 	= $_POST['pro_full_desc_'.$sef];
				
				$metakey_language			= $_POST['metakey_'.$sef];
				$metadesc_language			= $_POST['metadesc_'.$sef];
				
				if($pro_name_language == ""){
					$pro_name_language = $row->pro_name;
					$property = Table::getInstance('Property','OspropertyTable');
					$property->id = $id;
					$property->{'pro_name_'.$sef} = $pro_name_language;
					$property->store();
				}

                if($address_language == ""){
                    $address_language = $row->address;
                    $property = Table::getInstance('Property','OspropertyTable');
                    $property->id = $id;
                    $property->{'address_'.$sef} = $address_language;
                    $property->store();
                }
				
				if($pro_small_desc_language == ""){
					$pro_small_desc_language = $row->pro_small_desc;
				}
				if($pro_small_desc_language != ""){
					$property = Table::getInstance('Property','OspropertyTable');
					$property->id = $id;
					$property->access = $row->access;
					$property->category_id = $row->category_id;
					$property->{'pro_small_desc_'.$sef} = $pro_small_desc_language;
					$property->store();
				}
				
				if($pro_full_desc_language == ""){
					$pro_full_desc_language = $row->pro_full_desc;
				}
				if($pro_full_desc_language != ""){
					$property = Table::getInstance('Property','OspropertyTable');
					$property->id = $id;
					$property->access = $row->access;
					$property->category_id = $row->category_id;
					$property->{'pro_full_desc_'.$sef} = $pro_full_desc_language;
					$property->store();
				}
				
				if($metadesc_language == ""){
					$metadesc_language = $metadesc;
				}
				if($metadesc_language != ""){
					$db->setQuery("Update #__osrs_properties set `metadesc_".$sef."` = ".$db->quote($metadesc_language)." where  id = '$id'");
					$db->execute();
				}
				
				if($metakey_language == ""){
					$metakey_language = $metakey;
				}
				if($metakey_language != ""){
					$db->setQuery("Update #__osrs_properties set `metakey_".$sef."` = ".$db->quote($metakey_language)." where  id = '$id'");
					$db->execute();
				}
				
				$pro_alias = $jinput->getString('pro_alias_'.$sef);
				$pro_alias = OSPHelper::generateAliasMultipleLanguages('property',$id,$pro_alias,$sef);
				$db->setQuery("Update #__osrs_properties set pro_alias_".$sef." = '$pro_alias' where id = '$id'");
				$db->execute();
			}
		}

		if($configClass['more_bath_infor']== 1 && $configClass['use_bathrooms']== 1)
		{
			$db->setQuery("Delete from #__osrs_property_bath_values where pid = '$id'");
			$db->execute();
			
			$bathLabelArray = array('OS_FULL','OS_THREE_QUARTER','OS_HALF','OS_QUARTER','OS_ENSUITE');
			foreach($bathLabelArray as $label)
			{
				$bathvalue = $jinput->getFloat(strtolower($label),0);
				//echo $bathvalue;
				//echo "<BR />";
				if($bathvalue > 0)
				{
					$db->setQuery("Insert into #__osrs_property_bath_values (id,pid,`bath_label`,`bath_value`) values (NULL, $id, '$label','$bathvalue')");
					$db->execute();
				}
			}
			//die();
		}

		//collect the id of the photos
		$photoIds = [];
		
		//save photos and extra fields
		if($isNew == 0)
		{
			$db->setQuery("Select * from #__osrs_photos where pro_id = '$id' order by ordering");
			$photos = $db->loadObjectList();
			if(count($photos) > 0){
				for($i=0;$i<count($photos);$i++){
					$j = $i + 1;
					$photo = $photos[$i];
					$remove_name   = "remove_".$photo->id;
					$photo_name    = "photo_".$j;
					$desc_name     = "photodesc_".$j;
					$ordering_name = "ordering_".$j;
					$remove        = $jinput->getInt($remove_name,0);
					$photorecord   = Table::getInstance('Photo','OspropertyTable');
					$photorecord->id = $photo->id;
					$description   = $_POST[$desc_name];
					$photorecord->image_desc = $description;
					$photorecord->ordering   = $jinput->getInt($ordering_name,0);
					
					if(is_uploaded_file($_FILES[$photo_name]['tmp_name']))
					{
						if(!HelperOspropertyCommon::checkIsPhotoFileUploaded($photo_name))
						{
							//return to previous page
							$msg = Text::_('OS_PICTURE_TYPE_IS_NOT_ALLOWED');
							HelperOspropertyCommon::redirectPropertyEdit($jinput->getInt('id',0),$msg);
						}
						else
						{
							$image_name		= $_FILES[$photo_name]['name'];
							$image_name		= OSPHelper::processImageName($id.time().$image_name);
							$original_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/".$image_name;
							move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);

							OSPHelper::adjustPhotoOriginal($original_image_link);

							//check to resize the max (width or height) size
							HelperOspropertyCommon::returnMaxsize($original_image_link);
							//copy and resize
							//thumb
							$thumb_width = $configClass['images_thumbnail_width'];
							$thumb_height = $configClass['images_thumbnail_height'];
							$thumb_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/thumb/".$image_name;
							File::copy($original_image_link,$thumb_image_link);
							OSPHelper::resizePhoto($thumb_image_link,$thumb_width,$thumb_height);
							
							//medium
						    $medium_width = $configClass['images_large_width'];
						    $medium_height = $configClass['images_large_height'];
						    $medium_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/medium/".$image_name;
						    File::copy($original_image_link,$medium_image_link);
						    OSPHelper::resizePhoto($medium_image_link,$medium_width,$medium_height);
						    
						    $photorecord->image = $image_name;
						    
						    //add into the array
						    $photoIds[] = $photo->id;
						}
					}
					
					if($remove == 1){
						$db->setQuery("Select image from #__osrs_photos where id = '$photo->id'");
						$image_link = $db->loadResult();
						$original_image = JPATH_ROOT."/images/osproperty/properties/".$id."/".$image_link;
						$medium_image = JPATH_ROOT."/images/osproperty/properties/".$id."/medium/".$image_link;
						$thumb_image = JPATH_ROOT."/images/osproperty/properties/".$id."/thumb/".$image_link;
						@unlink($original_image);
						@unlink($medium_image);
						@unlink($thumb_image);
						
						$db->setQuery("Delete from #__osrs_photos where id = '$photo->id'");
						$db->execute();
						$db->setQuery("Delete from #__osrs_watermark where pid = '$id' and image like '$image_link'");
						$db->execute();
					}else{
						//save the image
						$photorecord->store();
					}
				}
			}
		}//end edit photos of property

		//with new photos of the property
		$newphoto = $jinput->getInt('newphoto',0);
		
		for($i=$newphoto;$i<=$limit_photo;$i++)
		{
			$j = $i + 1;
			$photo_name    = "photo_".$j;
			$desc_name     = "photodesc_".$j;
			$ordering_name = "ordering_".$j;
			$photorecord   = Table::getInstance('Photo','OspropertyTable');
			$photorecord->id = 0;
			$description   = $_POST[$desc_name];
			$photorecord->image_desc = $description;
			$photorecord->pro_id     = $id;
			$db->setQuery("Select ordering from #__osrs_photos where pro_id = '$id' order by ordering desc limit 1");
			$ordering = $db->loadResult();
			$photorecord->ordering   = $ordering +1;

			if(is_uploaded_file($_FILES[$photo_name]['tmp_name'])){
				if(!HelperOspropertyCommon::checkIsPhotoFileUploaded($photo_name)){
					//return to previous page
					//header('Location: ' . $_SERVER['HTTP_REFERER']);
					$msg = Text::_('OS_PICTURE_TYPE_IS_NOT_ALLOWED');
					HelperOspropertyCommon::redirectPropertyEdit($jinput->getInt('id',0),$msg);
				}else{
					$image_name = $_FILES[$photo_name]['name'];
					$image_name = OSPHelper::processImageName($id.time().$image_name);
					$original_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/".$image_name;
					move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);

					OSPHelper::adjustPhotoOriginal($original_image_link);
					
					HelperOspropertyCommon::returnMaxsize($original_image_link);
					//copy and resize
					//thumb
					$thumb_width = $configClass['images_thumbnail_width'];
					$thumb_height = $configClass['images_thumbnail_height'];
					$thumb_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/thumb/".$image_name;
					File::copy($original_image_link,$thumb_image_link);
					OSPHelper::resizePhoto($thumb_image_link,$thumb_width,$thumb_height);
					
					//medium
				    $medium_width = $configClass['images_large_width'];
				    $medium_height = $configClass['images_large_height'];
				    $medium_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/medium/".$image_name;
				    File::copy($original_image_link,$medium_image_link);
				    OSPHelper::resizePhoto($medium_image_link,$medium_width,$medium_height);
					
				    $photorecord->image = $image_name;
				    //save the image
					$photorecord->store();
					
					$new_photo_id = $db->insertID();
					$photoIds[] = $new_photo_id;
				}
			}
		}
		//end new photos of the property

		//grab image
		require_once JPATH_ROOT.'/components/com_osproperty/classes/listing.php';
		$grabImages = $jinput->get('grabImages',array(),'ARRAY');
		OspropertyListing::saveGrabmages($id,$grabImages);

		//save picture ajax upload
		$img_ids = Factory::getApplication()->input->get('img_id',array(),'array');
    	$img_captions = Factory::getApplication()->input->get('img_caption',array(),'array');
    	$img_images = Factory::getApplication()->input->get('img_image',array(),'array');

		$db->setQuery("Select * from #__osrs_photos where pro_id = '$id'")	;
		$item_images = $db->loadObjectList();

		$img_ord = 1;
    	$img_to_insert = 0;
		$db->setQuery("Select ordering from #__osrs_photos where pro_id = '$id' order by ordering desc limit 1");
		$img_ord = $db->loadResult();
		$img_ord = (int)$img_ord + 1;

    	$query_img = "INSERT INTO #__osrs_photos(`id`,`pro_id`,`image`,`image_desc`,`ordering`) VALUES ";
    	$new_img_path = JPATH_SITE."/images/osproperty/properties/".$id."/";
    	for($im = 0;$im<count($img_ids);$im++)
		{    		    		
    		if($img_ids[$im])
			{	
    			if($item_images[$img_ids[$im]]->ordering!=$img_ord || $item_images[$img_ids[$im]]->caption!=$img_captions[$im])
				{
    				$query = "UPDATE #__osrs_photos SET ordering='".$img_ord."', image_desc='".$db->escape($img_captions[$im])."' WHERE pro_id=".$id." AND id=".$img_ids[$im]." ";
    				$db->setQuery($query);
    				$db->execute();
    			}
    		}
			else
			{	
    			$new_img_name = explode(';',$img_images[$im]);    			
    			if(is_array($new_img_name))
				{
    				$new_img_name_u =JPATH_ROOT.'/tmp/osupload/'.$new_img_name[0];
    				if (File::exists($new_img_name_u))
					{
    					if(getimagesize($new_img_name_u))
						{
    						$new_img_n = $id.'_'.str_ireplace(' ', '_',$new_img_name[1]);
    						$new_img_n = strtolower($new_img_n);
    						$new_img_n = File::makeSafe($new_img_n);
    							
    						$new_path_check = $new_img_path.$new_img_n;
    						$nimg= 0;
    						while(File::exists($new_path_check))
							{
    							$nimg++;
    							$new_img_n = $id.'_'.$nimg.'_'.str_ireplace(' ', '_',$new_img_name[1]);
    							$new_img_n = strtolower($new_img_n);
    							$new_img_n = File::makeSafe($new_img_n);
    							$new_path_check = $new_img_path.$new_img_n;
    						} 
    							
    						rename($new_img_name_u, $new_img_path.$new_img_n);
							//echo $new_img_name_u;
							//echo "<BR />";
							//echo $new_img_path.$new_img_n;
							//die();
    						$name_parts = pathinfo($new_img_n);
    						$img_name = $name_parts['filename'];
    						$img_ext = $name_parts['extension'];
    						$query_img .= "(NULL,'".$id."','".$new_img_n."','".$db->escape($img_captions[$im])."','".$img_ord."'), ";

							$original_image_link = $new_img_path.$new_img_n;
							HelperOspropertyCommon::returnMaxsize($new_img_path.$new_img_n);
							//copy and resize
							//thumb
							$thumb_width = $configClass['images_thumbnail_width'];
							$thumb_height = $configClass['images_thumbnail_height'];
							$thumb_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/thumb/".$new_img_n;
							File::copy($original_image_link,$thumb_image_link);
							OSPHelper::resizePhoto($thumb_image_link,$thumb_width,$thumb_height);
							
							//medium
						    $medium_width = $configClass['images_large_width'];
						    $medium_height = $configClass['images_large_height'];
						    $medium_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/medium/".$new_img_n;
						    File::copy($original_image_link,$medium_image_link);
						    OSPHelper::resizePhoto($medium_image_link,$medium_width,$medium_height);

    						$img_to_insert++;
    					}
    				}
    			}
    		}
    		$img_ord++;
    	}
		if($img_to_insert){
    		$query_img = substr($query_img, 0, -2).';';
    		$db->setQuery($query_img);
    		$db->execute();
    	}  
		//let save extra fields
		
		$db->setQuery("Select * from #__osrs_extra_fields where published = '1'");
		$fields = $db->loadObjectList();
		if(count($fields) > 0){
			//delete all data from value table
			$db->setQuery("Delete from #__osrs_property_field_value where pro_id = '$id'");
			$db->execute();
			for($i=0;$i<count($fields);$i++){
				$field = $fields[$i];
				HelperOspropertyFields::saveField($field,$id);
			}
		}
		//end save extra field
		
		//save convenience
		$amenities = $jinput->get('amenities',array(),'ARRAY');
		$db->setQuery("Delete from #__osrs_property_amenities where pro_id = '$id'");
		$db->execute();
		if(count($amenities) > 0){
			for($i=0;$i<count($amenities);$i++){
				$amen_id = $amenities[$i];
				$db->setQuery("Insert into #__osrs_property_amenities (id,pro_id,amen_id) values (NULL,'$id','$amen_id')");
				$db->execute();
			}
		}
		
		//save neighborhood
		$db->setQuery("Delete from #__osrs_neighborhood where pid = '$id'");
		$db->execute();
		$db->setQuery("Select id from #__osrs_neighborhoodname");
		$neighborhoods = $db->loadObjectList();
		if(count($neighborhoods) > 0)
		{
			for($i=0;$i<count($neighborhoods);$i++)
			{
				$neighborhood = $neighborhoods[$i];
				$neighborhood_checkbox_name = "nei_".$neighborhood->id;
				$neighborhood_checkbox_value = $jinput->getInt($neighborhood_checkbox_name,0);
				if($neighborhood_checkbox_value == 1)
				{
					$mins_name		= "mins_nei_".$neighborhood->id;
					$mins			= $jinput->getInt($mins_name,0);
					$distance_name	= "distance_nei_".$neighborhood->id;
					$distance		= $jinput->getFloat($distance_name, 0);
					$traffic_name	= "traffic_type_".$neighborhood->id;
					$traffic_type	= $jinput->getInt($traffic_name,'0');
					$db->setQuery("Insert into #__osrs_neighborhood (id,pid,neighbor_id,mins,traffic_type,distance) values (NULL,'$id','$neighborhood->id','$mins','$traffic_type','$distance')");
					$db->execute();
				}
			}
		}
		
		//upload Zip file
		$zipfile = $_FILES['zip_file']['name'];
		if ($zipfile != '')
		{
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			$pathExtrart = JPATH_ROOT."/tmp/osphotos_".$id;
			if (Folder::exists($pathExtrart)) Folder::delete($pathExtrart);
			self::upload_zipfile($id);
		}
		
		//update tags
		$query = $db->getQuery(true);
		$query->delete("#__osrs_tag_xref")->where("pid = '$id'");
		$db->setQuery($query);
		$db->execute();
		
		if($translatable){
			$keyword = $jinput->get('keyword',array(),'ARRAY');
			if(count($keyword) > 0){
				for($i=0;$i<count($keyword);$i++){
					$tag = htmlspecialchars($keyword[$i]);
					$tag = $db->escape($tag);
					if($tag != ""){
						$sql = "Select count(id) from #__osrs_tags where keyword like '$tag'";
						foreach ($languages as $language){	
							$sef = $language->sef;
							$temp_keyword = $jinput->get('keyword_'.$sef,array(),'ARRAY');
							$temp_keyword = htmlspecialchars($temp_keyword[$i]);
							$temp_keyword = $db->escape($temp_keyword);
							$sql .= " and keyword_".$sef." like '$temp_keyword'";
						}
						$db->setQuery($sql);
						$count = $db->loadResult();
						if($count == 0){
							$tagobj = Table::getInstance('Tag','OspropertyTable');
							$tagobj->id = 0;
							$tagobj->keyword = $tag;
							$tagobj->published = 1;
							foreach ($languages as $language){	
								$sef = $language->sef;
								$temp_keyword = $jinput->get('keyword_'.$sef,array(),'ARRAY');
								$temp_keyword = htmlspecialchars($temp_keyword[$i]);
								$tagobj->{'keyword_'.$sef} = $temp_keyword;
							}
							$tagobj->store();
							$tagid = $db->insertID();
						}else{ 
							$sql = "Select id from #__osrs_tags where keyword like '$tag'";
							foreach ($languages as $language){	
								$sef = $language->sef;
								$temp_keyword = $jinput->get('keyword_'.$sef,array(),'ARRAY');
								$temp_keyword = htmlspecialchars($temp_keyword[$i]);
								$temp_keyword = $db->escape($temp_keyword);
								$sql .= " and keyword_".$sef." like '$temp_keyword'";
							}
							$db->setQuery($sql);
							$tagid = $db->loadResult();
						}
						$db->setQuery("Insert into #__osrs_tag_xref (id,pid,tag_id) values (NULL,'$id','$tagid')");
						$db->execute();
					}
				}
			}
		}
		else
		{
			$keyword = $jinput->get('keyword',array(),'ARRAY');
			if(count($keyword) > 0){
				foreach ($keyword as $tag){
					$tag = htmlspecialchars($tag);
					$tag = $db->escape($tag);
					if($tag != ""){
						$db->setQuery("Select count(id) from #__osrs_tags where keyword like '$tag'");
						$count = $db->loadResult();
						if($count == 0){
							$tagobj = Table::getInstance('Tag','OspropertyTable');
							$tagobj->id = 0;
							$tagobj->keyword = $tag;
							$tagobj->published = 1;
							$tagobj->store();
							$tagid = $db->insertID();
						}else{ 
							$db->setQuery("Select id from #__osrs_tags where keyword like '$tag'");
							$tagid = $db->loadResult();
						}
						$db->setQuery("Insert into #__osrs_tag_xref (id,pid,tag_id) values (NULL,'$id','$tagid')");
						$db->execute();
					}
				}
			}
		}
		
		//update property history
		if($configClass['use_property_history']== 1){
			$query = $db->getQuery(true);
			$query->delete("#__osrs_property_price_history")->where("pid = '$id'");
			$db->setQuery($query);
			$db->execute();

            $history_date   = $jinput->get('history_date',array(),'ARRAY');
            $history_event  = $jinput->get('history_event',array(),'ARRAY');
            $history_price  = $jinput->get('history_price',array(),'ARRAY');
            $history_source = $jinput->get('history_source',array(),'ARRAY');
			if(count($history_date) > 0){
				for($i=0;$i<count($history_date);$i++){
					if(($history_date[$i] != "") and ($history_event[$i] != "") and ($history_price [$i] != "")){
						$query = $db->getQuery(true);
						$columns = array('id','pid','date','event','price','source');
						$query->insert("#__osrs_property_price_history")->columns($columns)->values('NULL,'.$id.','.$db->quote($history_date[$i]).','.$db->quote($history_event[$i]).','.$db->quote($history_price [$i]).','.$db->quote($history_source[$i]).'');
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
			
			$query = $db->getQuery(true);
			$query->delete("#__osrs_property_history_tax")->where("pid = '$id'");
			$db->setQuery($query);
			$db->execute();

            $tax_year   			= $jinput->get('tax_year',array(),'ARRAY');
            $tax_value  			= $jinput->get('tax_value',array(),'ARRAY');
            $tax_change  			= $jinput->get('tax_change',array(),'ARRAY');
            $tax_assessment 		= $jinput->get('tax_assessment',array(),'ARRAY');
            $tax_assessment_change 	= $jinput->get('tax_assessment_change',array(),'ARRAY');
			if(count($tax_year) > 0){
				for($i=0;$i<count($tax_year);$i++){
					if(($tax_year[$i] != "") and ($tax_value[$i] != "")){
						if($tax_assessment [$i] == ""){
							$tax_assessment [$i] = 0;
						}
						if($tax_assessment_change [$i] == ""){
							$tax_assessment_change [$i] = 0;
						}
						$query = $db->getQuery(true);
						$columns = array('id','pid','tax_year','property_tax','tax_change','tax_assessment','tax_assessment_change');
						$query->insert("#__osrs_property_history_tax")->columns($columns)->values('NULL,'.$id.','.$db->quote($tax_year[$i]).','.$db->quote($tax_value[$i]).','.$db->quote($tax_change[$i]).','.$db->quote($tax_assessment[$i]).','.$db->quote($tax_assessment_change[$i]).'');
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
		
		if($configClass['use_open_house'] == 1){
			$query = $db->getQuery(true);
			$query->delete("#__osrs_property_open")->where("pid = '$id'");
			$db->setQuery($query);
			$db->execute();

            $start_from = $jinput->get('start_from',array(),'ARRAY');
            $end_to     = $jinput->get('end_to',array(),'ARRAY');
			
			if(count($start_from) > 0){
				for($i=0;$i<count($start_from);$i++){
					if(($start_from[$i] != "") and ($end_to[$i]!= "")){
						$query = $db->getQuery(true);
						$columns = array('id','pid','start_from','end_to');
						$query->insert('#__osrs_property_open')->columns($columns)->values('NULL,'.$id.','.$db->quote($start_from[$i]).','.$db->quote($end_to[$i]).'');
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}

        if($row->published == 1 && $row->approved == 1)
		{
            OSPHelper::updateFacebook($row, $isNew);
            OSPHelper::updateTweet($row, $isNew);
        }

		//trigger event when saving property
		PluginHelper::importPlugin('osproperty');
		//$dispatcher = JEventDispatcher::getInstance();
		if ($isNew == 1)
		{
			Factory::getApplication()->triggerEvent('onAfterStoreProperty', array($row));
		}

		//check to see if the system use expired mode
		//in that case, check if the property has been published automatically
		//we should insert the expired date into the database
		//check to see if this property has been saved in expired table
		$db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
		$count = $db->loadResult();
		if($count == 0){
			OspropertyProperties::setexpired($option,$id);
		}
		
		//Complete saving;
		if($zipfile != "")
		{
			$mainframe->redirect("index.php?option=com_osproperty&task=properties_showphotosinzipfile&pid=$id&save=$save");
		}
		elseif(count($photoIds) == 0)
		{
			//generate water maker image
			OSPHelper::generateWaterMark($id);
			$msg = Text::_('OS_ITEM_SAVED');
			$mainframe->enqueueMessage($msg);
			if($save == 1)
			{
				$mainframe->redirect("index.php?option=com_osproperty&task=properties_list");
			}
			elseif($save == 2)
			{
				$mainframe->redirect("index.php?option=com_osproperty&task=properties_add");
			}
			else
			{
				$mainframe->redirect("index.php?option=com_osproperty&task=properties_edit&cid[]=".$id);
			}
		}
		elseif($configClass['custom_thumbnail_photo']==1)
		{
			$mainframe->redirect("index.php?option=com_osproperty&task=properties_generatephoto&pid=$id&save=$save&photoIds=".implode(",",$photoIds));
		}
		else
		{
			//generate water maker image
			OSPHelper::generateWaterMark($id);
			$msg = Text::_('OS_ITEM_SAVED');
            $mainframe->enqueueMessage($msg);
			if($save == 1){
				$mainframe->redirect("index.php?option=com_osproperty&task=properties_list");
			}elseif($save == 2){
				$mainframe->redirect("index.php?option=com_osproperty&task=properties_add");
			}else{
				$mainframe->redirect("index.php?option=com_osproperty&task=properties_edit&cid[]=".$id);
			}
		}
	}
	
	/**
	 * Process Quick upload photo property
	 *
	 * @param unknown_type $option
	 */
	static function upload_zipfile($id)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		$pathExtrart = JPATH_ROOT."/tmp/osphotos_".$id;
		if (!Folder::exists($pathExtrart)) Folder::create($pathExtrart,0777);
		if(is_uploaded_file($_FILES['zip_file']['tmp_name']))
		{
			$filename = time().$_FILES['zip_file']['name'];
			@move_uploaded_file($_FILES['zip_file']['tmp_name'],JPATH_ROOT."/tmp/".$filename);
            if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
            {
                $archive = new Archive(array('tmp_path' => Factory::getConfig()->get('tmp_path')));
                $result  = $archive->extract(JPATH_ROOT."/tmp/".$filename,$pathExtrart);
            }
            else
            {
                $result = JArchive::extract(JPATH_ROOT."/tmp/".$filename,$pathExtrart);
            }


			File::delete(JPATH_ROOT."/tmp/".$filename);
			$folder = JPATH_ROOT."/tmp/osphotos_".$id;
			$images = OSPHelper::getImages($folder);
			foreach ($images as $image)
			{
				OSPHelper::checkImage($folder."/".$image->name);
			}
		}	
	}
	
	/**
	 * Show photos in the zip file, allow administrator to select to import photos
	 *
	 * @param unknown_type $option
	 */
	static function showphotosinzipfile($option){
		global $jinput, $mainframe;
		$id = $jinput->getInt('pid',0);
		$save = $jinput->getInt('save',0);
		$folder = JPATH_ROOT."/tmp/osphotos_".$id;
		$images = OSPHelper::getImages($folder);
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();
		HTML_OspropertyProperties::showPhotoinZipFile($property,$images);
	}
	
	/**
	 * Save photos in zip file
	 *
	 * @param unknown_type $option
	 */
	static function savephotosinzipfile($option){
		global $jinput, $configClass, $mainframe;
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		jimport('joomla.filesystem.file') ;
		jimport('joomla.filesystem.folder') ;
		$newPhotos = $jinput->get('newphotos',array(),'ARRAY');
		$id = $jinput->getInt('id',0);
		$save = $jinput->getInt('save',0);
		$path 		= JPATH_SITE.'/images/osproperty/properties/'.$id;
		$thumbPath 	= JPATH_SITE.'/images/osproperty/properties/'.$id.'/thumb';
		$mediumPath = JPATH_SITE.'/images/osproperty/properties/'.$id.'/medium';
		if (!Folder::exists($path)) Folder::create($path,777);
		if (!Folder::exists($thumbPath)) Folder::create($thumbPath,777);
		if (!Folder::exists($mediumPath)) Folder::create($mediumPath,777);
		for ($i = 0; $i < count($newPhotos); $i++)
		{
			
			$srcNewPhotos 	= $newPhotos[$i];
			$descNewPhotos  = uniqid().$srcNewPhotos;
			$src 		= JPATH_SITE.'/tmp/osphotos_'.$id.'/'.$srcNewPhotos;
			$desc 		= $path.'/'.strtolower(str_replace(" ","",$descNewPhotos));
			$descThumb	= $thumbPath.'/'.strtolower(str_replace(" ","",$descNewPhotos));
			$descMedium = $mediumPath.'/'.strtolower(str_replace(" ","",$descNewPhotos));

			
			$max_width_allowed = $configClass['max_width_size'];
			$max_height_allowed = $configClass['max_height_size'];
			File::copy($src,$desc);
			//check to resize the max (width or height) size
			HelperOspropertyCommon::returnMaxsize($desc);
			
			//copy and resize
			//thumb
			$thumb_width = $configClass['images_thumbnail_width'];
			$thumb_height = $configClass['images_thumbnail_height'];
			File::copy($src,$descThumb);
			OSPHelper::resizePhoto($descThumb,$thumb_width,$thumb_height);
			
			//medium
		    $medium_width = $configClass['images_large_width'];
		    $medium_height = $configClass['images_large_height'];
		    File::copy($src,$descMedium);
		    OSPHelper::resizePhoto($descMedium,$medium_width,$medium_height);
		    
		    $photorecord   = Table::getInstance('Photo','OspropertyTable');
			$photorecord->id = 0;
			$description   = $_POST['photodesc_'.$i];
			$photorecord->image_desc = $description;
			$photorecord->pro_id     = $id;
			$query->clear();
			$query->select('ordering')
				  ->from('#__osrs_photos')
				  ->where('pro_id='.(int)$id)
				  ->order('ordering DESC LIMIT 1')
			;
			$db->setQuery($query);
			$ordering = $db->loadResult();
			$photorecord->ordering   = $ordering +1;
			$photorecord->image = strtolower(str_replace(" ","",$descNewPhotos));
			$photorecord->store();
			$new_photo_id = $db->insertID();
			$photoIds[] = $new_photo_id;
		}
		Folder::delete(JPATH_SITE.'/tmp/osphotos_'.$id);
		//Complete saving;
		if(count($photoIds) == 0)
		{
			//generate water maker image
			OSPHelper::generateWaterMark($id);
			$msg = Text::_('OS_ITEM_SAVED');
            $mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=$option&task=properties_edit&cid[]=$id");
		}
		elseif($configClass['custom_thumbnail_photo']==1)
		{
			$mainframe->redirect("index.php?option=$option&task=properties_generatephoto&pid=$id&save=$save&photoIds=".implode(",",$photoIds));
		}
		else
		{
			//generate water maker image
			OSPHelper::generateWaterMark($id);
			$msg = Text::_('OS_ITEM_SAVED');
            $mainframe->enqueueMessage($msg);
			if($save == 1)
			{
				$mainframe->redirect("index.php?option=$option&task=properties_list");
			}
			else
			{
				$mainframe->redirect("index.php?option=$option&task=properties_edit&cid[]=$id");
			}
		}
	}
	
	/**
	 * Set expired time for properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function setexpired($option,$id){
		global $jinput, $mainframe,$configs,$configClass;
		$db = Factory::getDbo();
		$db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
		$count = $db->loadResult();
		$use_expired = $configClass['general_use_expiration_management'];
		if($use_expired == 1){
			if($count == 0){
				//check and calculate the expired and clean db time
				$unpublish_time = intval($configClass['general_time_in_days']);
				$remove_time	= intval($configClass['general_unpublished_days']);
				$send_appro		= $configClass['send_approximates'];
				$appro_days		= $configClass['approximates_days'];
				
				$current_time 	= time();
				$unpublish_time = $current_time + $unpublish_time*24*3600;
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
				//insert into #__osrs_expired
				$db->setQuery("Insert into #__osrs_expired (id,pid,inform_time,expired_time,remove_from_database) values (NULL,$id,'$inform_time','$unpublish_time','$remove_time')");
				$db->execute();
				//update start publishing today
				OspropertyProperties::updateStartPublishing($id);
				
			}else{//in the case this property is already in the expired table
				//check and calculate the expired and clean db time
				$unpublish_time = intval($configClass['general_time_in_days']);
				$remove_time	= intval($configClass['general_unpublished_days']);
				$send_appro		= $configClass['send_approximates'];
				$appro_days		= $configClass['approximates_days'];
				
				$current_time 	= time();
				$unpublish_time = $current_time + $unpublish_time*24*3600;
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
				//insert into #__osrs_expired
				$db->setQuery("UPDATE #__osrs_expired SET inform_time = '$inform_time',expired_time='$unpublish_time',remove_from_database='$remove_time' WHERE pid = '$id'");
				$db->execute();
				//update start publishing today
				OspropertyProperties::updateStartPublishing($id);
			}
		}
	}	
	
	/**
	 * Crop photos
	 *
	 * @param unknown_type $option
	 */
	static function generatePhoto($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$document = Factory::getDocument();
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/yuiloader-min.js");
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/yahoo-dom-event.js");
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/element-min.js");
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/dragdrop-min.js");
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/imagecropper-min.js");
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/resize-min.js");
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/event-min.js");
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/dom-min.js");
		
		$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/yui/style/fonts-min.css");
		$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/yui/style/imagecropper.css");
		$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/yui/style/resize.css");
		$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/yui/style/yui.css");
		$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/yui/style/dpSyntaxHighlighter.css");
		
		$id = $jinput->getInt('pid',0);
		$db->setQuery("Select pro_name from #__osrs_properties where id = '$id'");
		$pro_name = $db->loadResult();
		$photoIds = $jinput->get('photoIds','');
		$save = $jinput->getInt('save',0);
		
		HTML_OspropertyProperties::generatePhotoCrop($option,$id,$photoIds,$save,$pro_name);
	}
	
	
	static function skipgeneratePhoto($option){
		global $jinput, $mainframe;
		$save = $jinput->getInt('save',0);
		$pid = $jinput->getInt('pid');
		//generate water maker image
		OSPHelper::generateWaterMark($pid);
		$msg = Text::_('OS_ITEM_SAVED');
        $mainframe->enqueueMessage($msg);
		if($save == 1){
			$mainframe->redirect("index.php?option=com_osproperty&task=properties_list");
		}else{
			$mainframe->redirect("index.php?option=com_osproperty&task=properties_edit&cid[]=$pid");
		}
	}
	/**
	 * saving photo
	 *
	 * @param unknown_type $option
	 */
	static function savingPhoto($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$pid = $jinput->getInt('pid');
		$photoIds = $jinput->get('photoIds','');
		$save = $jinput->getInt('save',0);
		if($photoIds != ""){
			$photoArr = explode(",",$photoIds);
			if(count($photoArr) > 0){
				for($i=0;$i<count($photoArr);$i++){
					$photo_id = $photoArr[$i];
					$db->setQuery("Select image from #__osrs_photos where id = '$photo_id'");
					$photo_name = $db->loadResult();
					//thumbnail first
					$tb = $jinput->getInt('tb_'.$photo_id,0);
                    if($tb == 1){
                        $top    = $jinput->getInt('tb_t_'.$photo_id,0);
                        $left   = $jinput->getInt('tb_l_'.$photo_id,0);
                        $height = $jinput->getInt('tb_h_'.$photo_id,0);
                        $width  = $jinput->getInt('tb_w_'.$photo_id,0);
                        HelperOspropertyCommon::create_photo($top,$left,$height,$width,$photo_name,0,$pid);
                    }

                    $me = $jinput->getInt('me_'.$photo_id,0);
                    if($me == 1){
                        $top    = $jinput->getInt('me_t_'.$photo_id,0);
                        $left   = $jinput->getInt('me_l_'.$photo_id,0);
                        $height = $jinput->getInt('me_h_'.$photo_id,0);
                        $width  = $jinput->getInt('me_w_'.$photo_id,0);
						HelperOspropertyCommon::create_photo($top,$left,$height,$width,$photo_name,1,$pid);
					}
				}
			}
		}
		//generate water maker image
		OSPHelper::generateWaterMark($pid);
		
		$msg = Text::_('OS_ITEM_SAVED');
        $mainframe->enqueueMessage($msg);
		if($save == 1){
			$mainframe->redirect("index.php?option=com_osproperty&task=properties_list");
		}else{
			$mainframe->redirect("index.php?option=com_osproperty&task=properties_edit&cid[]=$pid");
		}
	}
	
	/**
	 * Approval the list of properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function approval($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		if(count($cid) > 0){
			for($i=0;$i<count($cid);$i++){
				$id = $cid[$i];
				$db->setQuery("Update #__osrs_properties set approved = '1', `request_to_approval` = '0' where id = '$id'");
				$db->execute();
				OspropertyProperties::updateStatus($option,'approved',1,$id);
			}
		}
		$msg = Text::_('OS_PROPERTY_STATUS_HAVE_BEEN_CHANGED');
		$limit = $jinput->getInt('limit',20);
		$limitstart = $jinput->getInt('limitstart',0);
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=properties_list&limitstart=$limitstart&limit=$limit");
	}
	
	/**
	 * UnApproval the list of properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function unapproval($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		if(count($cid) > 0){
			for($i=0;$i<count($cid);$i++){
				$id = $cid[$i];
				$db->setQuery("Update #__osrs_properties set approved = '0', `request_to_approval` = '0' where id = '$id'");
				$db->execute();
				OspropertyProperties::updateStatus($option,'approved',0,$id);
			}
		}
		
		$msg = Text::_('OS_PROPERTY_STATUS_HAVE_BEEN_CHANGED');
		$limit = $jinput->getInt('limit',20);
		$limitstart = $jinput->getInt('limitstart',0);
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=properties_list&limitstart=$limitstart&limit=$limit");
	}
	
	/**
	 * Change other information type
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function changeType($option,$id){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDBO();
		$type  = $jinput->getString('type','');
		$value = $jinput->get('v','');
		$db->setQuery("Update #__osrs_properties set $type = '$value', `request_to_approval` = '0' where id = '$id'");
		$db->execute();
		
		$db->setQuery("Select * from #__osrs_configuration");
		$configs = $db->loadObjectList();
		
		//in the case the property has been approved
		if(($type == "approved") or ($type == "isFeatured")){
			OspropertyProperties::updateStatus($option,$type,$value,$id);
		}
		
		$first_letter = substr($type,0,1);
		$remain_letters = substr($type,1);
		$type = strtoupper($first_letter).$remain_letters;
		$msg = $type." ".Text::_('OS_STATUS_CHANGED');
		$limit = $jinput->getInt('limit',20);
		$limitstart = $jinput->getInt('limitstart',0);
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=properties_list&limitstart=$limitstart&limit=$limit");
	}
	
	/**
	 * Update status
	 *
	 * @param unknown_type $option
	 * @param unknown_type $type
	 * @param unknown_type $state
	 * @param unknown_type $id
	 */
	static function updateStatus($option,$type,$state,$id)
	{
		global $jinput, $mainframe,$configClass;
		require_once JPATH_ROOT .'/components/com_osproperty/helpers/route.php';
		$db = Factory::getDbo();
		$db->setQuery("Select a.pro_name,b.* from #__osrs_properties as a inner join #__osrs_agents as b on b.id = a.agent_id where a.id = '$id'");
		$agent = $db->loadObject();
		$emailopt['agentname'] = $agent->name;
		$emailopt['agentemail'] = $agent->email;
		$emailopt['property'] = $agent->pro_name;
		$needs = ['property_details', $id];

		$pItemid = OSPRoute::getItemid($needs);

		$link = Uri::root()."index.php?option=com_osproperty&task=property_details&id=$id&Itemid=".$pItemid;
		$emailopt['link'] = "<a href='$link'>".$link."</a>";
		switch ($type){
			case "approved":
				if($state == 0){
					//send unactived email
					OspropertyEmailBackend::sendActivedEmail($option,$id,'listing_deactivated',$emailopt);
				}else{
					//update expired table
					if($configClass['general_use_expiration_management'] == 1){ //allow to update expired ?
						$unpublish_time = $configClass['general_time_in_days'];
						$remove_time	= $configClass['general_unpublished_days'];
						$send_appro		= $configClass['send_approximates'];
						$appro_days		= $configClass['approximates_days'];
						
						$current_time 	= time();
						$unpublish_time = $current_time + $unpublish_time*24*3600;
						$remove_time    = $unpublish_time + $remove_time*24*3600;
						if($send_appro == 1){
							$inform_time = $unpublish_time - $appro_days*24*3600;
							$inform_time = date("Y-m-d H:i:s",$inform_time);
						}else{
							$inform_time = "";
						}
						$unpublish_time = date("Y-m-d H:i:s",$unpublish_time);
						$remove_time    = date("Y-m-d H:i:s",$remove_time);
						$db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
						$count = $db->loadResult();
						if($count == 0){
							$db->setQuery("INSERT INTO #__osrs_expired (id, pid, inform_time,expired_time,remove_from_database) VALUES (NULL,'$id','$inform_time','$unpublish_time','$remove_time')");
							$db->execute();
						}else{
							$db->setQuery("UPDATE #__osrs_expired SET inform_time = '$inform_time',send_inform='0',expired_time='$unpublish_time',send_expired = '0',remove_from_database = '$remove_time' WHERE pid = '$id'");
							$db->execute();
						}
					}
					//send actived email
					OspropertyEmailBackend::sendActivedEmail($option,$id,'listing_activated',$emailopt);
					//update start publishing today
					OspropertyProperties::updateStartPublishing($id);
				}
			break;
			case "isFeatured":
				if($state == 0){
					//send unactived email
					OspropertyEmailBackend::sendActivedEmail($option,$id,'featured_listing_deactivated',$emailopt);
				}else{
					//update expired table
					$db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
					$count = $db->loadResult();
					$use_expired = $configClass['general_use_expiration_management'];
					if($use_expired == 1){
						if($count == 0){
							//$general_approval = $configs[12]->fieldvalue;
							//check and calculate the expired and clean db time
							$unpublish_time  = intval($configClass['general_time_in_days']);
							$remove_time	 = intval($configClass['general_unpublished_days']);
							$feature_time    = intval($configClass['general_time_in_days_featured']);
							$send_appro	     = $configClass['send_approximates'];
							$appro_days		 = $configClass['approximates_days'];
							
							$current_time 	 = time();
							$unpublish_time  = $current_time + $unpublish_time*24*3600;
							$feature_time    = $current_time + $feature_time*24*3600;
							$remove_time     = $unpublish_time + $remove_time*24*3600;
							//allow to send the approximates expired day
							if($send_appro == 1){
								$inform_time = $unpublish_time - $appro_days*24*3600;
								$inform_time = date("Y-m-d H:i:s",$inform_time);
							}else{
								$inform_time = "";
							}
							$unpublish_time	 = date("Y-m-d H:i:s",$unpublish_time);
							$remove_time	 = date("Y-m-d H:i:s",$remove_time);
							$feature_time	 = date("Y-m-d H:i:s",$feature_time);
							//insert into #__osrs_expired
							$db->setQuery("Insert into #__osrs_expired (id,pid,inform_time,expired_time,expired_feature_time,remove_from_database) values (NULL,$id,'$inform_time','$unpublish_time','$feature_time','$remove_time')");
							$db->execute();
							//update start publishing today
							OspropertyProperties::updateStartPublishing($id);
						}else{
							//$general_approval = $configs[12]->fieldvalue;
							//check and calculate the expired and clean db time
							$db->setQuery("Select * from #__osrs_expired where pid = '$id'");
							$row = $db->loadObject();
							$expired_time    = $row->expired_time;
							$unpublish_time  = strtotime($expired_time);
							
							$remove_time	 = intval($configClass['general_unpublished_days']);
							$feature_time    = intval($configClass['general_time_in_days_featured']);
							$send_appro	     = $configClass['send_approximates'];
							$appro_days		 = $configClass['approximates_days'];
							
							$current_time 	 = time();
							$feature_time    = $current_time + $feature_time*24*3600;
							if($current_time > $unpublish_time){
								$new = 1;
								$unpublish_time = intval($configClass['general_time_in_days']);
								$unpublish_time  = $current_time + $unpublish_time*24*3600;
							}else{
								$new = 0;
								if($feature_time > $unpublish_time){
									$unpublish_time = $feature_time;
								}
							}
							$remove_time     = $unpublish_time + $remove_time*24*3600;
							$remove_time	 = date("Y-m-d H:i:s",$remove_time);
							//allow to send the approximates expired day
							if($send_appro == 1){
								$inform_time = $unpublish_time - $appro_days*24*3600;
								$inform_time = date("Y-m-d H:i:s",$inform_time);
							}else{
								$inform_time = "";
							}
							$unpublish_time	 = date("Y-m-d H:i:s",$unpublish_time);
							$feature_time	 = date("Y-m-d H:i:s",$feature_time);
							//insert into #__osrs_expired
							$db->setQuery("UPDATE #__osrs_expired SET inform_time = '$inform_time',expired_time='$unpublish_time',expired_feature_time= '$feature_time',remove_from_database='$remove_time' WHERE pid = '$id'");
							$db->execute();
							
							//update start publishing today
							$db->setQuery("Select publish_up from #__osrs_properties where id = '$id'");
							$publish_up = $db->loadResult();
							if(($publish_up == "0000-00-00") or ($new == 1)){
								OspropertyProperties::updateStartPublishing($id);
							}
						}
					}
					//send actived email
					OspropertyEmailBackend::sendActivedEmail($option,$id,'featured_listing_activated',$emailopt);
				}
			break;
		}
	}
	
	/**
	 * Update start publishing
	 *
	 * @param unknown_type $id
	 */
	static function updateStartPublishing($id){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$time = date("Y-m-d",time());
		$db->setQuery("Update #__osrs_properties set publish_up = '$time' where id = '$id'");
		$db->execute();
	}
	
	
	/**
	 * Change status of the extra field(s)
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function changState($option,$cid,$state){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if($cid){
			$cids = implode(",",$cid);
			$db->setQuery("Update #__osrs_properties set published = '$state' where id in ($cids)");
			$db->execute();
		}
		$msg = Text::_("OS_ITEM_STATUS_HAS_BEEN_CHANGED");
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=properties_list");
	}
	
	/**
	 * Remove static function
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
 	 public static function remove($option,$cid,$redirect){
		global $jinput, $mainframe;
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		OSPHelper::removeProperties($cid);
		if($redirect == 1){
			$msg = Text::_('OS_ITEM_HAS_BEEN_DELETED');
            $mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=com_osproperty&task=properties_list");
		}
	}
	
	/**
	 * Print static function
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function properties_print($option,$id){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$row = Table::getInstance('Property','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
			$db->setQuery("Select amen_id from #__osrs_property_amenities where pro_id = '$row->id'");
			$amenitylists = $db->loadOBjectList();
			$amenitylists1 = [];
			if(count($amenitylists) > 0){
				for($i=0;$i<count($amenitylists);$i++){
					$amenitylists1[$i] = $amenitylists[$i]->amen_id;
				}
				$amenitylists = [];
				$amenitylists = $amenitylists1;
			}
			
			$db->setQuery("Select * from #__osrs_photos where pro_id = '$id' order by ordering");
			$photos = $db->loadobjectList();
			$row->photo = $photos;
		}else{
			$row->published = 1;
			$row->access = 0;
		}
		
		$lists['show_address'] = HTMLHelper::_('select.booleanlist', 'show_address', '', $row->show_address);
		$lists['price_call'] = HTMLHelper::_('select.booleanlist', 'price_call', '', $row->price_call);
		
		//agent
		$db->setQuery("SELECT * FROM #__osrs_agents WHERE `published` = '1' AND `id` = '$row->agent_id'");
		$agent = $db->loadObject();
		//agent country;
		$db->setQuery("select country_name from #__osrs_countries where id = '$agent->country'");
		$agent->country_name = $db->loadResult();
		$row->agent = $agent;
		
		//agent state;
		$db->setQuery("select state_name from #__osrs_states where id = '$agent->state'");
		$agent->state_name = $db->loadResult();
		
		//property types
		$db->setQuery("SELECT type_name FROM #__osrs_types WHERE `published` = '1' AND `id` = '$row->pro_type'");
		$lists['type'] = $db->loadResult();
		
		//categories
		//$db->setQuery("SELECT `category_name` FROM #__osrs_categories WHERE `id` = '$row->category_id'");
		$lists['category'] = OSPHelper::getCategoryNamesOfProperty($row->id);
		
		//country
		$db->setQuery("SELECT country_name FROM #__osrs_countries WHERE `id` = '$row->country'");
		$lists['country'] = $db->loadResult();
		
		//states
		$db->setQuery("SELECT state_name FROM #__osrs_states WHERE `id` = '$row->state'");
		$lists['states'] = $db->loadResult();
		
		// access
			$lists['access'][0] = Text::_('Public');
			$lists['access'][1] = Text::_('Registered');
			$lists['access'][2] = Text::_('Special');
		
		$db->setQuery("SELECT * FROM #__osrs_amenities WHERE published = '1' order by amenities");
		$amenities = $db->loadObjectList();
		
		$db->setQuery("Select * from #__osrs_fieldgroups where published = '1' order by ordering");
		$groups = $db->loadObjectList();
		if(count($groups) > 0){
			for($i=0;$i<count($groups);$i++){
				$group = $groups[$i];
				$db->setQuery("Select * from #__osrs_extra_fields where published = '1' and group_id = '$group->id' order by ordering");
				$fields = $db->loadObjectList();
				$groups[$i]->fields = $fields;
			}
		}
		
		HTML_OspropertyProperties::printProperties($option,$row,$lists,$amenities,$amenitylists,$groups);
	}
	
	/**
	 * Backup
	 *
	 * @param unknown_type $option
	 */
	static function backup($option){
		global $jinput, $mainframe;
		HTML_OspropertyProperties::backupForm($option);
	}
	
	/**
	 * Do backup
	 *
	 * @param unknown_type $option
	 */
	static function dobackup($option){
        global $mainframe;
        
		jimport('joomla.filesystem.file');
	        
        $database 		= &Factory::getDBO();
        
        $mailfrom	    = $mainframe->getCfg( 'mailfrom' );
        $fromname	    = $mainframe->getCfg( 'fromname' );
        $livesite	    = $mainframe->getCfg( 'live_site' );
        $host		    = $mainframe->getCfg( 'host' );
        $user		    = $mainframe->getCfg( 'user' );
        $password	    = $mainframe->getCfg( 'password' );
        $db			    = $mainframe->getCfg( 'db' );
        $dp				= $mainframe->getCfg( 'dbprefix' );
        $dbtype         = $mainframe->getCfg( 'dbtype');
        
        $pluginParams   = '';
        $test		= false;

        $backupPath		= JPATH_ROOT.'/components/com_osproperty/backup';
        $checkfileName	= 'ip_checkfile_';
        $current_date 	= date("Y-m-d");
        $dateCheckFile	= $checkfileName.$current_date;
        $continue	= true;

        if (is_writable($backupPath) )  
        {
            if (!$test)
            {
                if (!touch($backupPath.'/'.$dateCheckFile))
                {
                    $mainframe->setError(sprintf(Text::_('OS_CHECK_FILE_NOT_CREATED'), $backupPath));
                    $continue = false;
                    return false;
                }
            }
        }else{
            $mainframe->setError(sprintf(Text::_('OS_BACKUP_NOT_CREATED'), $backupPath));
            $continue = false;
            return false;
        }

        if ($continue)
        {
            File::delete($backupPath."/".$dateCheckFile);
            $deletefile		= false;
            $compress		= 1;
            $backuppath		= 0;
            $verbose		= 1;

            if($dbtype == "mysqli")
            {
                $objBackup 	= new mysqliBackup();
            }
            else
            {
                $objBackup 	= new mysqlBackup();
            }

            //$dp             =& $database->_table_prefix;
            $objBackup->tablesToInclude = array(
                    $dp.'osrs_agents',
                    $dp.'osrs_agent_account',
                    $dp.'osrs_amenities',
                    $dp.'osrs_categories',
                    $dp.'osrs_cities',
                    $dp.'osrs_comments',
                    $dp.'osrs_companies',
                    $dp.'osrs_company_agents',
                    $dp.'osrs_configuration',
                    $dp.'osrs_countries',
                    $dp.'osrs_coupon',
                    $dp.'osrs_csv_forms',
                    $dp.'osrs_emails',
                    $dp.'osrs_email_log',
                    $dp.'osrs_expired',
                    $dp.'osrs_extra_fields',
                    $dp.'osrs_extra_field_options',
                    $dp.'osrs_extra_field_types',
                    $dp.'osrs_photos',
                    $dp.'osrs_favorites',
                    $dp.'osrs_fieldgroups',
                    $dp.'osrs_form_fields',
                    $dp.'osrs_lastcron',
                    $dp.'osrs_order_details',
                    $dp.'osrs_pricegroups',
                    $dp.'osrs_properties',
                    $dp.'osrs_property_amenities',
                    $dp.'osrs_property_categories',
                    $dp.'osrs_property_field_opt_value',
                    $dp.'osrs_property_history_tax',
                    $dp.'osrs_property_open',
                    $dp.'osrs_property_price_history',
                    $dp.'osrs_property_field_value',
                    $dp.'osrs_themes',
                    $dp.'osrs_tags',
                    $dp.'osrs_tag_xref',
                    $dp.'osrs_queue',
                    $dp.'osrs_states',
                    $dp.'osrs_tag_cloud',
                    $dp.'osrs_types',
                    $dp.'osrs_user_coupons',
                    $dp.'osrs_neighborhood',
					$dp.'extra_field_options',
                    $dp.'osrs_user_list',
                    $dp.'osrs_user_list_details',
                    $dp.'osrs_neighborhoodname'
                    );

            //print_r($objBackup->tablesToInclude);
            $result		       = OspropertyProperties::ipBackup($objBackup,$host,$user,$password,$db,$pluginParams,$backupPath,$fromname,$compress,$backuppath);
            $backupFile		   = $objBackup->ip_file_name;

            if($deletefile == "1" && !empty($backupFile) )
            {
                if ($test){
                    echo "Deleting backup file $backupFile";
                    unlink($backupFile);
                }
            }else if($test){
                echo "Not deleting backup file $backupFile";
            }
            $msg = Text::_( 'OS_BACKUP_FILE_READY_TO_DOWNLOAD' ).' - '.$objBackup->ip_file_name;
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect("index.php?option=com_osproperty");
        }else{
            $msg = Text::_( 'OS_BACKUP_FILE_IS_NOT_COMPLETED' );
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect("index.php?option=com_osproperty");
        }        
    }

    static function ipBackup(&$objBackup,$host,$user,$password,$db,$pluginParams,$backupPath,$fromname,$compress,$backuppath)
    {
    	global $jinput, $mainframe;
        $sqlContent			= 'Mysql backup from'.$fromname;
        $drop_tables 		= 0;
        $create_tables 		= 0;
        $struct_only 		= 0;
        $locks 				= 1;
        $comments 			= 1;

        if(!empty($backuppath) && is_dir($backuppath) && @is_writable($backuppath)){
            $backup_dir = $backuppath;
        }else{
            $backup_dir = $backupPath;
        }

        $objBackup->server 	        = $host;
        $objBackup->port 		    = 3306;
        $objBackup->username 	    = $user;
        $objBackup->password 	    = $password;
        $objBackup->database 	    = $db;
        $objBackup->tables          = [];
        $objBackup->drop_tables 	= $drop_tables;
        $objBackup->create_tables 	= $create_tables;
        $objBackup->struct_only 	= $struct_only;
        $objBackup->locks 			= $locks;
        $objBackup->comments 		= $comments;
        $objBackup->backup_dir 	    = $backup_dir.DS;
        $objBackup->fname_format 	= 'm_d_Y__H_i_s';
        $objBackup->null_values 	= array( );
        $savetask                   = MSX_SAVE;
        $filename                   = '';
        $result_bk                  = $objBackup->Execute($savetask, $filename, $compress);
        if (!$result_bk)
        {
            $output = $objBackup->error;
        }else{
            $output = $sqlContent.': ' . strftime('%A %d %B %Y  - %T ') . ' ';
        }
        return array('result'=>$result_bk,'output'=>$output);
    }
    
    
    /**
     * Restore database
     *
     * @param unknown_type $option
     */
    static function restore($option){
    	global $jinput, $mainframe;
    	jimport('joomla.filesystem.folder');
    	$filelist      = Folder::files(JPATH_SITE.'/components/com_osproperty/backup', '.sql.gz');
		$filelistArr   = [];
		$i                 = 1;
		foreach ($filelist as $file):
			$filelistArr[] = HTMLHelper::_('select.option', $i, $file);
			$i++;
		endforeach;
		
		$lists = [];
		$lists['bkfiles'] = HTMLHelper::_('select.genericlist', $filelistArr, 'bkfile', 'size="10" class="input-small form-control" style="width: 300px;"', 'text', 'text');
		HTML_OspropertyProperties::restoreForm($option,$lists);
    }
    
    /**
     * Restore database
     *
     * @param unknown_type $option
     */
    static function dorestore($option)
    {
    	global $jinput, $mainframe;
    	jimport('joomla.filesystem.files');
    	jimport('joomla.filesystem.archive');
        if(!$jinput->get('bkfile'))
        {
            $msg = Text::_('OS_NO_FILE_SELECTED');
            $type = 'notice';
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect('index.php?option=com_osproperty&task=properties_restore');
        }
		
		$bak_file      = JPATH_SITE.'/components/com_osproperty/backup/'.$jinput->get('bkfile');
		
       //echo $bak_file;
       //die();
       //echo JPATH_SITE.'/components/com_osproperty/backup';
       //die();
        if(!JArchive::extract($bak_file, JPATH_SITE.'/components/com_osproperty/backup'))
        {
            $msg = sprintf(Text::_('OS_COULD_NOT_EXTRACT_FILE'), $bak_file);
            $type = 'notice';
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect('index.php?option=com_osproperty&task=properties_restore');
        }
		$text_bak_file = substr($bak_file, 0, strlen($bak_file)-3);
       
        if(!$bquery = file_get_contents($text_bak_file)){
            $msg = sprintf(Text::_('OS_COULD_NOT_READ_BACKUP'), $text_bak_file);
            $type = 'notice';
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect('index.php?option=com_osproperty&task=properties_restore');
        }
        
		File::delete($text_bak_file);

		$database        = & Factory::getDBO();
	    $emptying_query  = '';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_agents;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_agent_account;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_amenities;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_categories;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_cities;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_comments;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_companies;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_company_agents;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_configuration;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_countries;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_coupon;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_csv_forms;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_emails;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_email_log;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_expired;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_extra_fields;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_extra_field_options;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_extra_field_types;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_photos;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_favorites;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_fieldgroups;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_form_fields;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_lastcron;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_order_details;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_pricegroups;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_properties;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_property_amenities;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_property_categories;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_property_field_opt_value;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_property_history_tax;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_property_open;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_property_price_history;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_property_field_value;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_themes;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_tags;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_tag_xref;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_queue;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_states;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_tag_cloud;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_types;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_user_coupons;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_neighborhood;';
	    $emptying_query .= 'TRUNCATE TABLE #__osrs_extra_field_options;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_user_list;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_user_list_details;';
        $emptying_query .= 'TRUNCATE TABLE #__osrs_neighborhoodname;';
        
		$jversion = new Version();
        if(version_compare($jversion->getShortVersion(), '3.0', 'ge')){
        	$queries = $database->splitSql($emptying_query);
	        foreach($queries as $query){
		        $database->setQuery($query);
		        if(!$database->execute()){
		        	$msg = Text::_('OS_QUERY_EXE_FAILED').' Error1 - '.$database->stderr();
		            $type = 'notice';
                    $mainframe->enqueueMessage($msg);
		            $mainframe->redirect('index.php?option=com_osproperty&task=properties_restore');
		        }
	        }
        }else{
        	$database->setQuery ($emptying_query);
	        if(!$database->QueryBatch()) {
	            $msg = Text::_('OS_QUERY_EXE_FAILED').' Error1 - '.$database->stderr();
	            $type = 'notice';
                $mainframe->enqueueMessage($msg);
	            $mainframe->redirect('index.php?option=com_osproperty&task=properties_restore');
	        }
        }
		
		
		$database->setQuery("SET CHARACTER SET 'utf8'");
		if(!$database->execute()){
            $msg = Text::_('OS_QUERY_EXE_FAILED').' Error2 - '.$database->stderr();
            $type = 'notice';
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect('index.php?option=com_osproperty&task=properties_restore');
        }

    	$database->setQuery("SET NAMES 'utf8'");
    	if(!$database->execute()){
            $msg = Text::_('OS_QUERY_EXE_FAILED').' Error3 - '.$database->stderr();
            $type = 'notice';
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect('index.php?option=com_osproperty&task=properties_restore');
        }

        $tmp = [];
        if(version_compare($jversion->getShortVersion(), '3.0', 'ge')){
        	$queries = $database->splitSql($bquery);
	        foreach($queries as $query){
	        	$query = trim($query);
	        	if($query != "")
	        	{
			        $database->setQuery(utf8_decode($query));
			        if(!$database->execute())
			        {
			        	$msg = Text::_('OS_QUERY_EXE_FAILED').' Error4 - '.$database->stderr();
			            $type = 'notice';
                        $mainframe->enqueueMessage($msg);
			            $mainframe->redirect('index.php?option=com_osproperty&task=properties_restore');
			        }
	        	}
	        }
        }
        else
        {
        	$database->setQuery(utf8_decode($bquery));
			if(!$database->QueryBatch())
			{
	            $msg = Text::_('OS_QUERY_EXE_FAILED').' Error4 - '.$database->stderr();
	            $type = 'notice';
                $mainframe->enqueueMessage($msg);
	            $mainframe->redirect('index.php?option=com_osproperty&task=properties_restore');
			}
        }
        
		
		$msg = Text::_('OS_QUERY_EXED_SUCCESSFULLY');
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect('index.php?option=com_osproperty');
    	
    }
    
    /**
     * Update state of location
     *
     * @param unknown_type $option
     */
    static function changeLocator($option){
    	global $jinput, $mainframe;
		$country_id = $jinput->getInt('country_id');
		$s = $jinput->getInt('s','0');
		$db = Factory::getDbo();
		$db->setQuery("UPDATE #__osrs_states set published = '$s' WHERE country_id = '$country_id'");
		$db->execute();
		$db->setQuery("UPDATE #__osrs_cities set published = '$s' WHERE country_id = '$country_id'");
		$db->execute();
        $mainframe->enqueueMessage("Location state has been saved");
		$mainframe->redirect("index.php?option=com_osproperty");
    }
    
    /**
     * Optimize sef urls form
     *
     * @param unknown_type $option
     */
    static function optimizesef($option){
    	global $jinput, $mainframe;
    	
    	HTML_OspropertyProperties::optimizeSefForm($option);
    }
    
    /**
     * Do Optimize sef urls
     *
     * @param unknown_type $option
     */
    static function doOptimizeSefUrls($option)
	{
    	global $jinput, $mainframe;
    	$db = Factory::getDbo();
    	$db->setQuery("Delete from #__osrs_urls");
    	$db->execute();

		// Lấy danh sách property_id đã được publish
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__osrs_properties'))
			->where($db->quoteName('published') . ' = 1');

		$db->setQuery($query);
		$properties = $db->loadColumn();

		if (!empty($properties)) {
			// Chuyển mảng ID thành chuỗi để sử dụng trong câu lệnh IN()
			$propertyIds = implode(',', array_map('intval', $properties));

			// Truy vấn lấy thông tin chi tiết của các property
			$query = $db->getQuery(true)
				->select([
					'a.id AS pid',
					'a.agent_id',
					'a.pro_type',
					'a.city',
					'a.state',
					'a.country',
					'a.isFeatured',
					'b.agent_type',
					'c.company_id'
				])
				->from($db->quoteName('#__osrs_properties', 'a'))
				->innerJoin($db->quoteName('#__osrs_agents', 'b') . ' ON b.id = a.agent_id')
				->leftJoin($db->quoteName('#__osrs_company_agents', 'c') . ' ON c.agent_id = b.id')
				->where('a.id IN (' . $propertyIds . ')');

			$db->setQuery($query);
			$propertyList = $db->loadObjectList('pid'); // Trả về danh sách property theo ID

			// Kiểm tra nếu Multilanguage được kích hoạt
			$langs = Multilanguage::isEnabled() ? OSPHelper::getLanguages() : [];

			foreach ($propertyList as $pid => $property) {
				if($pid > 0)
				{
					$category_id = (array)OSPHelper::getCategoryIdsOfProperty($pid);

					if (!empty($langs)) {
						foreach ($langs as $lang) {
							$language_sql = " AND (`language` LIKE '{$lang->lang_code}' OR `language` LIKE '*' OR `language` = '')";
							self::findPropertyItemId($property, $category_id, $language_sql, $lang->lang_code);
						}
					} else {
						self::findPropertyItemId($property, $category_id, '', '');
					}
				}
			}
		}

    	$mainframe->enqueueMessage(Text::_('OS_SEF_URLS_OPTIMIZATION_HAS_BEEN_COMPLETED'));
    	$mainframe->redirect("index.php?option=com_osproperty&task=cpanel_list");
    }


	public static function findPropertyItemId($property, $category_id , $language_sql, $language = '')
	{
		global $mainframe;
		$db				= Factory::getDbo();
		$pid			= $property->pid;
		$pro_type		= $property->pro_type;
		$state			= $property->state;
		$city			= $property->city;
		$agent_id		= $property->agent_id;
		$country		= $property->country;
		$isFeatured		= $property->isFeatured;
		$company_id		= (int)$property->company_id;
		$agent_type		= (int) $property->agent_type;

		//checking details link directly
		$db->setQuery("Select * from #__menu where published = '1' and ((`link` like '%view=ldetails&id=".$pid."%') or (`link` like '%view=ldetails&ampid=".$pid."%')) $language_sql");
		$founded_menu = $db->loadObject();
		if($founded_menu->id > 0)
		{
			//return $founded_menu;
			$db->setQuery("Select count(id) from #__osrs_property_itemid where pid = '$pid' and itemid = '$founded_menu->id'");
			$count = $db->loadResult();
			if((int) $count == 0)
			{
				$db->setQuery("insert into #__osrs_property_itemid (id, pid, itemid, lang, `access`, created) values (NULL, '$pid', '$founded_menu->id','$language', '$founded_menu->access','".gmdate("Y-m-d")."')");
				$db->execute();
			}

		}

		$db->setQuery("Select * from #__menu where published = '1' and `home` = '0' and `link` like '%view=ltype%' $language_sql");
		$menus_found = $db->loadObjectList();

		if(count($menus_found) == 0)
		{
			$db->setQuery("Select * from #__menu where published = '1' and `home` = '0' and `link` like '%view=lcity%' and `link` like '%id=".$property->city."%' $language_sql");
			$menus_found = $db->loadObjectList();
			if(count($menus_found) > 0)
			{
				$founded_menu = $menus_found[0];

				$db->setQuery("Select count(id) from #__osrs_property_itemid where pid = '$pid' and itemid = '$founded_menu->id'");
				$count = $db->loadResult();
				if((int) $count == 0)
				{
					$db->setQuery("insert into #__osrs_property_itemid (id, pid, itemid, lang, `access`, created) values (NULL, '$pid', '$founded_menu->id','$language', '$founded_menu->access','".gmdate("Y-m-d")."')");
					$db->execute();
				}
			}
		}
		
		$jmenu = Factory::getApplication()->getMenu();
		
		if(count($menus_found) > 0)
		{
			$tmp = [];
			foreach($menus_found as $found)
			{
				$tmp[] = $found->id;
			}
			$menuArr = [];
			$menus	= $mainframe->getMenu('site');
			$active = $menus->getActive();
			

			if(in_array($active->id, $tmp))
			{
				$menuid_active = $active->id;
			}
			else
			{
				$menuid_active = $tmp[0];
			}


			for($i=0;$i<count($menus_found);$i++)
			{
				$return				= 0;
				$menu				= $menus_found[$i];
				
				$mid				= $menu->id;
				$mobj				= $jmenu->getItem( $mid );

				$find_pro_type		=  $mobj->query['type_id'];
				$find_category_id	= (array)$mobj->query['catIds'];
				$find_company_id	= $mobj->query['company_id'];
				$find_country		= $mobj->query['country_id'];

				$params				= $menu->params;
				$params				= json_decode($params);
				$find_isFeatured	= $params->isFeatured;
				$find_state_id		= $params->state_id;
				$find_city_id		= $params->city_id;
				
				$find_agent_type	= $params->agenttype;
				//find itemid now
				if($find_pro_type > 0)
				{
					if($find_pro_type == $pro_type){ //ok
						$type = 1;
						$return++;
					}
					else
					{
						$type = 0;
					}
				}else{
					$type = 0;
				}
				if(count($find_category_id) > 0 && count($category_id) > 0)
				{
					$show = 0;
					foreach($category_id as $cid)
					{
						if(in_array($cid,$find_category_id))
						{
							$show = 1;
						}
					}
					if($show == 1){
						$cat = 1;
						$return++;
						
						if(count($find_category_id) == count($category_id)){
							$return++; //use for case: Parent menu contains several sub cats. And there is other link for one sub cat. The system must get Itemid of that sub cat. 
						}
					}else{
						$return = -1000; //we won't care this menu
					}
				}else{
					$cat = 0;
				}
				if($find_country > 0){
					if($find_country == $country){ //ok
						$c = 1;
						$return++;
					}else{
						$c = 0;
					}
				}else{
					$c = 0;
				}
				
				if($find_state_id > 0){
					if($find_state_id == $state){ //ok
						$s = 1;
						$return++;
					}else{
						$s = 0;
					}
				}else{
					$s = 0;
				}

				if($find_city_id > 0){
					if($find_city_id == $city){ //ok
						$s = 1;
						$return++;
					}else{
						$s = 0;
					}
				}else{
					$s = 0;
				}
				
				
				if($find_company_id > 0){
					if($find_company_id == $company_id){ //ok
						$company = 1;
						$return++;
					}else{
						$company = 0;
					}
				}else{
					$company = 0;
				}
				
				if($find_isFeatured > 0){
					if($find_isFeatured == $isFeatured){ //ok
						$featured = 1;
						if($return > 0){
							$return = $return + 2;
						}
					}else{
						$featured = 0;
					}
				}else{
					$featured = 0;
				}

				if($find_agent_type > 0){
					if($find_agent_type == $agent_type){ //ok
						if($return > 0){
							$return = $return + 1;
						}
					}
				}
				
				$count = count($menuArr);
				$menuArr[$count] = new stdClass();
				$menuArr[$count]->point = $return;
				$menuArr[$count]->menu_id = $menu->id;
				
			}//end for
			$max = 0;
			//$menus	= $app->getMenu('site');
			$menuid = $default_itemid;
			if($menuid == 0){
				$menuid = $default_itemid;
			}

			for($i=0;$i<count($menuArr);$i++)
			{
				if($menuArr[$i]->point > $max)
				{
					$max = $menuArr[$i]->point;
					$menuid = $menuArr[$i]->menu_id;
				}
			}
			if($max == 0)
			{
				$menuid = $menuid_active;
			}
			if($menuid > 0)
			{
				//return $menuid;
				$db->setQuery("Select * from #__menu where id = '$menuid'");
				$founded_menu = $db->loadObject();

				$db->setQuery("Select count(id) from #__osrs_property_itemid where pid = '$pid' and itemid = '$founded_menu->id'");
				$count = $db->loadResult();
				if((int) $count == 0)
				{
					$db->setQuery("insert into #__osrs_property_itemid (id, pid, itemid, lang, `access`, created) values (NULL, '$pid', '$founded_menu->id','$language', '$founded_menu->access','".gmdate("Y-m-d")."')");
					$db->execute();
				}
			}
			
		}
	}
    
    
    static function syncdatabase($option){
    	HTML_OspropertyProperties::syncdatabaseForm($option);
    }
    
    static function doSyncdatabase($option){
    	global $jinput, $mainframe,$lang_suffix, $languages;
    	$db = Factory::getDbo();
    	//osrs_types
		$db->setQuery("Select * from #__osrs_types");
		$types = $db->loadObjectList();
		//osrs_categories
		$db->setQuery("Select * from #__osrs_categories");
		$categories = $db->loadObjectList();
		//osrs_amenities
		$db->setQuery("Select * from #__osrs_amenities");
		$amenities = $db->loadObjectList();
		//osrs_fieldgroups
		$db->setQuery("Select * from #__osrs_fieldgroups");
		$fieldgroups = $db->loadOBjectList();
		//osrs_extra_fields
		$db->setQuery("Select * from #__osrs_extra_fields");
		$extra_fields = $db->loadObjectList();
		//osrs_field_options
		$db->setQuery("Select * from #__osrs_extra_field_options");
		$field_options = $db->loadObjectList();
		//osrs_property_field_value
		$db->setQuery("Select * from #__osrs_property_field_value");
		$field_values = $db->loadObjectList();
		//osrs_properties
		$db->setQuery("Select * from #__osrs_properties");
		$properties = $db->loadObjectList();
		//osrs_agents
		$db->setQuery("Select * from #__osrs_agents");
		$agents = $db->loadObjectList();
		//osrs_companies
		$db->setQuery("Select * from #__osrs_companies");
		$companies = $db->loadObjectList();
		
    	foreach ($languages as $language) {						
			$sef = $language->sef;
			
			//osrs_types
			for($i=0;$i<count($types);$i++){
				$row = $types[$i];
				$type_name_language = $row->{'type_name_'.$sef};
				if($type_name_language == ""){
					$type_name_language = $row->type_name;
					$type = Table::getInstance('Type','OspropertyTable');
					$type->id = $row->id;
					$type->{'type_name_'.$sef} = $type_name_language;
					$type->store();
				}
				$type_alias_language = $row->{'type_alias_'.$sef};
				if($type_alias_language == ""){
					$type_alias_language = $row->type_alias;
					$type = Table::getInstance('Type','OspropertyTable');
					$type->id = $row->id;
					$type->{'type_alias_'.$sef} = $type_alias_language;
					$type->store();
				}
			}
			
			//osrs_categories
			for($i=0;$i<count($categories);$i++){
				$row = $categories[$i];
				$category_name_language = $row->{'category_name_'.$sef};
				$category_alias_language = $row->{'category_alias_'.$sef};
				$category_description_language = $row->{'category_description_'.$sef};
				if($category_name_language == ""){
					$category_name_language = $row->category_name;
					$category = Table::getInstance('Category','OspropertyTable');
					$category->id = $row->id;
					$category->access = $row->access;
					$category->{'category_name_'.$sef} = $category_name_language;
					$category->store();
				}
				if($category_alias_language == ""){
					$category_alias_language = $row->category_alias;
					$category = Table::getInstance('Category','OspropertyTable');
					$category->id = $row->id;
					$category->access = $row->access;
					$category->{'category_alias_'.$sef} = $category_alias_language;
					$category->store();
				}
				if($category_description_language == ""){
					$category_description_language = $row->category_description;
					if($category_description_language != ""){
						$category = Table::getInstance('Category','OspropertyTable');
						$category->id = $row->id;
						$category->access = $row->access;
						$category->{'category_description_'.$sef} = $category_description_language;
						$category->store();
					}
				}
			}
			
			//osrs_amenities
			for($i=0;$i<count($amenities);$i++){
				$row = $amenities[$i];
				$amenities_language = $row->{'amenities_'.$sef};
				if($amenities_language == ""){
					$amenities_language = $row->amenities;
					$amenity = Table::getInstance('Amenities','OspropertyTable');
					$amenity->id = $row->id;
					$amenity->{'amenities_'.$sef} = $amenities_language;
					$amenity->store();
				}
			}
			
			//osrs_fieldgroups
			for($i=0;$i<count($fieldgroups);$i++){
				$row = $fieldgroups[$i];
				$group_name_language = $row->{'group_name_'.$sef};
				if($group_name_language == ""){
					$group_name_language = $row->group_name;
					$group = Table::getInstance('Fieldgroup','OspropertyTable');
					$group->id = $row->id;
					$group->{'group_name_'.$sef} = $group_name_language;
					$group->store();
				}
			}
			
			//osrs_extra_fields
			for($i=0;$i<count($extra_fields);$i++){
				$row = $extra_fields[$i];
				$field_label_language = $row->{'field_label_'.$sef};
				$field_description_language = $row->{'field_description_'.$sef};
				if($field_label_language == ""){
					$field_label_language = $row->field_label;
					$field = Table::getInstance('Extrafield','OspropertyTable');
					$field->id = $row->id;
					$field->access = $row->access;
					$field->{'field_label_'.$sef} = $field_label_language;
					$field->store();
				}
				
				if($field_description_language == ""){
					$field_description_language 		= $row->field_description;
					if($field_description_language != ""){
						$field 								= Table::getInstance('Extrafield','OspropertyTable');
						$field->id 							= $row->id;
						$field->{'field_description_'.$sef} = $field_description_language;
						$field->store();
					}
				}
			}
			
			//osrs_extra_fields
			for($i=0;$i<count($properties);$i++){
				$row = $properties[$i];
				
				$pro_name_language 			= $row->{'pro_name_'.$sef};
				$pro_alias_language 		= $row->{'pro_alias_'.$sef};
				$pro_small_desc_language 	= $row->{'pro_small_desc_'.$sef};
				$pro_full_desc_language 	= $row->{'pro_full_desc_'.$sef};
				if($pro_name_language == ""){
					$pro_name_language = $row->pro_name;
					$property = Table::getInstance('Property','OspropertyTable');
					$property->id = $row->id;
					$property->category_id = $row->category_id;
					$property->access = $row->access;
					$property->{'pro_name_'.$sef} = $pro_name_language;
					$property->store();
				}
				
				if($pro_alias_language == ""){
					$pro_alias_language = $row->pro_alias;
					$property = Table::getInstance('Property','OspropertyTable');
					$property->id = $row->id;
					$property->category_id = $row->category_id;
					$property->access = $row->access;
					$property->{'pro_alias_'.$sef} = $pro_alias_language;
					$property->store();
				}
				
				if($pro_small_desc_language == ""){
					$pro_small_desc_language = $row->pro_small_desc;
					$property = Table::getInstance('Property','OspropertyTable');
					$property->id = $row->id;
					$property->category_id = $row->category_id;
					$property->access = $row->access;
					$property->{'pro_small_desc_'.$sef} = $pro_small_desc_language;
					$property->store();
				}
				
				if($pro_full_desc_language == ""){
					$pro_full_desc_language = $row->pro_full_desc;
				}
				if($pro_full_desc_language != ""){
					$property = Table::getInstance('Property','OspropertyTable');
					$property->id = $row->id;
					$property->category_id = $row->category_id;
					$property->access = $row->access;
					$property->{'pro_full_desc_'.$sef} = $pro_full_desc_language;
					$property->store();
				}
			}
			
			//osrs_agents
			for($i=0;$i<count($agents);$i++){
				$row = $agents[$i];
				$bio_language = $row->{'bio_'.$sef};
				if($bio_language == ""){
					$bio_language 		= $row->bio;
					if($bio_language != ""){
						$agent 								= Table::getInstance('Agent','OspropertyTable');
						$agent->id 							= $row->id;
						$agent->{'bio_'.$sef} 				= $bio_language;
						$agent->store();
					}
				}
			}
			
			//osrs_companies
			for($i=0;$i<count($companies);$i++){
				$row = $agents[$i];
				$company_description_language = $row->{'company_description_'.$sef};
				if($company_description_language == ""){
					$company_description_language 		= $row->company_description;
					if($company_description_language != ""){
						$company 								= Table::getInstance('Company','OspropertyTable');
						$company->id 							= $row->id;
						$company->user_id 						= $row->user_id;
						$company->{'company_description_'.$sef} = $company_description_language;
						$company->store();
					}
				}
			}
			
			//osrs_extra_field_options
			for($i=0;$i<count($field_options);$i++){
				$row = $field_options[$i];
				$field_option_language = $row->{'field_option_'.$sef};
				if($field_option_language == ""){
					$field_option_language = $row->field_option;
					$fieldOption = Table::getInstance('Fieldoption','OspropertyTable');
					$fieldOption->id = $row->id;
					$fieldOption->{'field_option_'.$sef} = $field_option_language;
					$fieldOption->store();
				}
			}
			
			//osrs_extra_field_options
			for($i=0;$i<count($field_values);$i++){
				$row = $field_values[$i];
				$value_language = $row->{'value_'.$sef};
				if($value_language == ""){
					$value_language = $row->value;
					$fieldValue = Table::getInstance('Fieldvalue','OspropertyTable');
					$fieldValue->id = $row->id;
					$fieldValue->{'value_'.$sef} = $value_language;
					$fieldValue->store();
				}
			}
    	}
    	$mainframe->enqueueMessage(Text::_('OS_DATABASE_SYNCHRONOUS_COMPLETED'));
    	$mainframe->redirect("index.php?option=com_osproperty");
    }
    
    /**
     * Re-generate the pictures of properties
     *
     * @param unknown_type $option
     */
    static function reGeneratePictures($option){
    	global $jinput, $mainframe;
    	HTML_OspropertyProperties::reGeneratePicturesForm($option);
    }
    
    /**
     * do generate pictures
     *
     * @param unknown_type $option
     */
    static function doReGeneratePictures($option){
    	global $jinput, $mainframe,$configClass;
    	ini_set('memory_limit','999M');
    	ini_set('max_execution_time','3000');
    	jimport('joomla.filesystem.file');
    	jimport('joomla.fileststem.folder');
    	$db = Factory::getDbo();
        $last_id = $jinput->getInt('last_id',0);
    	$db->setQuery("Select id from #__osrs_properties order by id limit $last_id,5");
    	$properties = $db->loadObjectList();
        $last_id = $last_id + 5;
    	$nphotos = 0;
    	if(count($properties) > 0){
    		for($i=0;$i<count($properties);$i++){
    			$property = $properties[$i];
    			if(! Folder::exists(JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/medium')){
					Folder::create(JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/medium');
					File::copy(JPATH_ROOT.'/images/osproperty/properties/index.html',JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/medium/index.html');
				}
				if(! Folder::exists(JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/thumb')){
					Folder::create(JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/thumb');
					File::copy(JPATH_ROOT.'/images/osproperty/properties/index.html',JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/thumb/index.html');
				}
    			$db->setQuery("Select * from #__osrs_photos where pro_id = '$property->id'");
    			$photos = $db->loadObjectList();
    			if(count($photos) > 0){
    				for($j = 0;$j<count($photos);$j++){
    					$nphotos++;
    					$photo = $photos[$j];
    					$db->setQuery("Delete from #__osrs_watermark where image like '$photo->image' and pid = '$property->id'");
    					$db->execute();
						$backupurl = JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/original/'.$photo->image;
						$originalurl = JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/'.$photo->image;
						if(File::exists($backupurl)){
							File::copy($backupurl,$originalurl);
						}
    					$mediumurl = JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/medium/'.$photo->image;
	    				$thumburl  = JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/thumb/'.$photo->image;
    					if(File::exists($originalurl)){
	    					if(File::exists($mediumurl)){
	    						File::delete($mediumurl);
	    					}
	    					File::copy($originalurl,$mediumurl);
	    					$medium_width = $configClass['images_large_width'];
						    $medium_height = $configClass['images_large_height'];
						    OSPHelper::resizePhoto($mediumurl,$medium_width,$medium_height);
						    
						    if(File::exists($thumburl)){
	    						File::delete($thumburl);
	    					}
	    					File::copy($originalurl,$thumburl);
	    					$thumb_width = $configClass['images_thumbnail_width'];
							$thumb_height = $configClass['images_thumbnail_height'];
							OSPHelper::resizePhoto($thumburl,$thumb_width,$thumb_height);
    					}
    				}
    			}
    			//generate water maker image
				OSPHelper::generateWaterMark($property->id);
    		}
            $msg = Text::_('OS_GENERATE_PICTURES')." ".$nphotos." ".Text::_('OS_PICTURES')." ".Text::_('OS_OF')." ".count($properties)." ".Text::_('OS_PROPERTIES');
    		$mainframe->enqueueMessage($msg);
            $mainframe->redirect("index.php?option=com_osproperty&task=properties_delayReGeneratePictures&last_id=".$last_id);
    	}else{
            $msg = Text::_('OS_GENERATE_PICTURES')." ".Text::_('OS_COMPLETED');
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect("index.php?option=com_osproperty&task=cpanel_list");
        }
    }

    /**
     * Delay Re-Generate Pictures
     */
    static function delayReGeneratePictures($option){
        global $jinput, $mainframe;
        $last_id = $jinput->getInt('last_id');
        ?>
        <div class="row-fluid">
            <div class="span12">
                <center>
                    <h2>
                        <?php echo Text::_('OS_PROCESSING_PLEASE_DO_NOT_CLOSE_BROWSER_UNTIL_COMPLETE');?>...
                    </h2>
                </center>
            </div>
        </div>
        <meta http-equiv="refresh" content="0;URL=<?php echo Uri::base()."index.php?option=com_osproperty&task=properties_doReGeneratePictures&last_id=".$last_id;?>" />
    <?php
    }

    /**
     * Fix database schema:
     * In case customer use one country in OS Property system, this task will unpublish properties of different country
     */
    static function fixdatabase(){
        global $jinput, $mainframe;
        $db = Factory::getDbo();
        if(!HelperOspropertyCommon::checkCountry()){
            $default_country = HelperOspropertyCommon::getDefaultCountry();
            if($default_country > 0){
                $db->setQuery("Update #__osrs_properties set `published` = '0' where country <> '$default_country'");
                $db->execute();
            }
        }
        $mainframe->enqueueMessage(Text::_('OS_DATABASE_SCHEMA_HAS_BEEN_FIXED'));
        $mainframe->redirect("index.php?option=com_osproperty");
    }

    /**
     * Method to allow sharing language files for OS Property
     */
    public static function share_translation()
    {
        global $jinput, $mainframe;
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('lang_code')
            ->from('#__languages')
            ->where('published = 1')
            ->where('lang_code != "en-GB"')
            ->order('ordering');
        $db->setQuery($query);
        $languages = $db->loadObjectList();

        if (count($languages))
        {
            $mailer   = Factory::getMailer();
            $jConfig  = Factory::getConfig();
            $mailFrom = $jConfig->get('mailfrom');
            $fromName = $jConfig->get('fromname');
            $mailer->setSender(array($mailFrom, $fromName));
            $mailer->addRecipient('thucdam84@gmail.com');
            $mailer->setSubject('Language Packages for OS Property shared by ' . Uri::root());
            $mailer->setBody('Dear OS Property support \n. I am happy to share my language packages for OS Property.\n Enjoy!');
            foreach ($languages as $language)
            {
                $tag = $language->lang_code;
                if (file_exists(JPATH_ROOT . '/language/' . $tag . '/' . $tag . '.com_osproperty.ini'))
                {
                    $mailer->addAttachment(JPATH_ROOT . '/language/' . $tag . '/' . $tag . '.com_osproperty.ini', $tag . '.com_osproperty.ini');
                }
                if (file_exists(JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_osproperty.ini'))
                {
                    echo JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_osproperty.ini';
                    $mailer->addAttachment(JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_osproperty.ini', 'admin.' . $tag . '.com_osproperty.ini');
                }
            }
            $mailer->Send();
            $msg = 'Thanks so much for sharing your language files to OS Property Community';
        }
        else
        {
            $msg = 'Thanks so willing to share your language files to OS Property Community. However, you don"t have any none English language file to share';
        }
        $mainframe->enqueueMessage($msg);
        $mainframe->redirect('index.php?option=com_osproperty');
    }

	/**
	* Remove Orphan properties information form
	**/
	static function removeorphan(){
        HTML_OspropertyProperties::removeOrphanForm();
	}

    /**
     * Remove Orphan Properties
     */
    static function doremoveOrphan(){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        //remove properties if Property types aren't available
        $query->select('id')->from('#__osrs_properties')->where('pro_type not in (Select id from #__osrs_types)');
        $db->setQuery($query);
        $cid = $db->loadColumn(0);
        if(count($cid) > 0){
            self::remove('com_osproperty',$cid,'');
        }

        //remove properties if Agents aren't available
        $query->clear();
        $query->select('id')->from('#__osrs_properties')->where('agent_id not in (Select id from #__osrs_agents)');
        $db->setQuery($query);
        $cid = $db->loadColumn(0);
        if(count($cid) > 0){
            self::remove('com_osproperty',$cid,'');
        }

        //remove properties if Categories aren't available
        $query->clear();
        $query->select('category_id')->from('#__osrs_property_categories')->where('category_id not in (Select id from #__osrs_categories)');
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if(count($rows) > 0){
            foreach($rows as $row){
                $query->clear();
                $query->select('pid')->from('#__osrs_property_categories')->where('category_id = "'.$row->category_id.'"');
                $db->setQuery($query);
                $properties = $db->loadObjectList();
                $tempArr = [];
                foreach($properties as $property){
                    $tempArr[] = $property->pid;
                }
                self::remove('com_osproperty',$tempArr,'');
            }
        }
        Factory::getApplication()->enqueueMessage(Text::_('OS_ORPHAN_PROPERTIES_HAVE_BEEN_REMOVED'));
        Factory::getApplication()->redirect('index.php?option=com_osproperty');
    }

	static function updatecoordinates(){
		global $mainframe;
		$db = Factory::getDbo();
		include_once(JPATH_SITE."/components/com_osproperty/helpers/googlemap.lib.php");
        include_once(JPATH_SITE."/components/com_osproperty/helpers/openstreetmap.lib.php");
		$db->setQuery("Select * from #__osrs_properties where show_address = '1' and lat_add = '' and long_add = ''");
		$properties = $db->loadObjectList();
		if(count($properties) > 0){
			foreach($properties as $property){
				if($property->city > 0){
					$db->setQuery("Select city from #__osrs_cities where id = '$property->city'");
					$cityname = $db->loadResult();
				}
				if($property->state > 0){
					$db->setQuery("Select state_name from #__osrs_states where id = '$property->state'");
					$statename = $db->loadResult();
				}
				if($property->country > 0){
					$db->setQuery("Select country_name from #__osrs_countries where id = '$property->country'");
					$countryname = $db->loadResult();
				}
				$address_search = $property->address.", ".$cityname.", ".$statename.", ".$countryname;
				$return = HelperOspropertyGoogleMap::findAddress($option,'',$address_search,1);
				if($return[2] == "OK"){
					$lat_add = $return[0];
					$long_add = $return[1];
					$db->setQuery("Update #__osrs_properties set lat_add = '$lat_add', long_add = '$long_add' where id = '$property->id'");
					$db->execute();
				}
			}
		}
		$mainframe->enqueueMessage("Coordinates have been updated");
		$mainframe->redirect("index.php?option=com_osproperty");
	}
}
?>
