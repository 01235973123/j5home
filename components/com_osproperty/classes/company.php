<?php

/*------------------------------------------------------------------------
# company.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\User\User;
use Joomla\CMS\Plugin\PluginHelper;


class OspropertyCompany
{
	/**
	 * Default company layout
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task)
	{
		global $mainframe,$jinput,$configClass;
		$db = Factory::getDBO();
		$id = $jinput->getInt('id',0);
		$cid = $jinput->get('cid',array(),'ARRAY');
		$cid = ArrayHelper::toInteger($cid);
		define('PATH_STORE_PHOTO_COMPANY_FULL',JPATH_ROOT.DS.'images'.DS.'osproperty'.DS.'company');
		define('PATH_STORE_PHOTO_COMPANY_THUMB',PATH_STORE_PHOTO_COMPANY_FULL.DS.'thumbnail');
		define('PATH_URL_PHOTO_COMPANY_FULL',str_replace(DS,'/',str_replace(JPATH_ROOT,Uri::root(),PATH_STORE_PHOTO_COMPANY_FULL)).'/');
		define('PATH_URL_PHOTO_COMPANY_THUMB',str_replace(DS,'/',str_replace(JPATH_ROOT,Uri::root(),PATH_STORE_PHOTO_COMPANY_THUMB)).'/');
		define('PATH_STORE_PHOTO_AGENT_FULL',JPATH_ROOT.DS."images".DS."osproperty".DS."agent");
		define('PATH_STORE_PHOTO_AGENT_THUMB',PATH_STORE_PHOTO_AGENT_FULL.DS.'thumbnail');
		define('PATH_URL_PHOTO_AGENT_FULL',str_replace(DS,'/',str_replace(JPATH_SITE,Uri::root(),PATH_STORE_PHOTO_AGENT_FULL)).'/');
		define('PATH_URL_PHOTO_AGENT_THUMB',str_replace(DS,'/',str_replace(JPATH_SITE,Uri::root(),PATH_STORE_PHOTO_AGENT_THUMB)).'/');
		$keyword = OSPHelper::getStringRequest('keyword','');
		$show_top_menus_in = $configClass['show_top_menus_in'];
		$show_top_menus_in = explode("|",$show_top_menus_in);
		switch ($task){
			case "company_listing":
				if(in_array('company',$show_top_menus_in)){
					echo HelperOspropertyCommon::buildToolbar('company');
				}
				OspropertyCompany::companyListing($option);
			break;
			case "company_info":
				if(in_array('company',$show_top_menus_in)){
					echo HelperOspropertyCommon::buildToolbar('company');
				}
				OspropertyCompany::companyDetails($option,$id);
			break;
			case "company_submitcontact":
				OspropertyCompany::submitContact($option,$id);
			break;
			case "company_listproperties":
				if(in_array('company',$show_top_menus_in)){
					echo HelperOspropertyCommon::buildToolbar('company');
				}
				OspropertyCompany::listingProperties($option,$id);
			break;
			case "company_edit":
				if(in_array('company',$show_top_menus_in)){
					echo HelperOspropertyCommon::buildToolbar('company');
				}
				OspropertyCompany::editCompany($option);
			break;
			case "company_agent":
				if(in_array('company',$show_top_menus_in)){
					echo HelperOspropertyCommon::buildToolbar('company');
				}
				OspropertyCompany::editAgent($option);
			break;
			case "company_save_info":
				OspropertyCompany::saveCompanyInfo($option);
			break;
			case "company_removeagent":
				OspropertyCompany::removeAgent($option);
			break;
			case "company_searchagent":
				OspropertyCompany::searchAgent($option,$keyword);
			break;
			case "company_addagent":
				OspropertyCompany::addAgent($option);
			break;
			case "company_unpublishagents":
				OspropertyCompany::agentStatus($option,$cid,0);
			break;
			case "company_publishagents":
				OspropertyCompany::agentStatus($option,$cid,1);
			break;
			case "company_featureagents":
				OspropertyCompany::agentFeature($option,$cid,1);
			break;
			case "company_unfeatureagents":
				OspropertyCompany::agentFeature($option,$cid,0);
			break;
			case "company_removeagents":
				OspropertyCompany::removeAgents($option,$cid);
			break;
			case "company_addnew":
				OspropertyCompany::assignNewAgents($option);
			break;
			case "company_addnew1":
				OspropertyCompany::processToAssignAgentToCompany($option);
			break;
			case "company_addagents":
				OspropertyCompany::modifyAgent($option,0);
			break;
			case "company_applyagent":
				OspropertyCompany::saveAgent($option,$id,0);
			break;
			case "company_saveagent":
				OspropertyCompany::saveAgent($option,$id,1);
			break;
			case "company_editagent":
				OspropertyCompany::modifyAgent($option,$id);
			break;
			case "company_properties":
				OspropertyCompany::manageProperties($option);
			break;
			case "company_addproperty":
				OspropertyListing::edit($option,0);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "company_unpublishproperties":
				OspropertyCompany::propertyStatus($option,$cid,0);
			break;
			case "company_publishproperties":
				OspropertyCompany::propertyStatus($option,$cid,1);
			break;
			case "company_removeproperties":
				OspropertyCompany::removeProperties($option,$cid);
			break;
			case "company_register":
				OspropertyCompany::companyRegister($option);
			break;
			case "company_savenew":
				OspropertyCompany::saveNewCompany($option);
			break;
			case "company_plans":
				if(in_array('company',$show_top_menus_in)){
					echo HelperOspropertyCommon::buildToolbar('company');
				}
				OspropertyCompany::myPlans($option);
			break;
			case "company_ordershistory":
				if(in_array('company',$show_top_menus_in)){
					echo HelperOspropertyCommon::buildToolbar('company');
				}
				OspropertyCompany::ordershistory($option);
			break;
			case "company_cancelagent":
				global $mainframe,$jinput;
				OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_agent&Itemid='.$jinput->getInt('Itemid',0)));
			break;
		}
	}
	
	
	/**
	 * Add agent
	 *
	 * @param unknown_type $option
	 */
	static function addAgent($option){
		global $mainframe,$jinput;
		HTMLHelper::_('behavior.keepalive');
		$db = Factory::getDbo();
		$user = Factory::getUser();
		$keyword = OSPHelper::getStringRequest('keyword','','post');
		$keyword = $db->escape($keyword);
		$agent_id = $jinput->getInt('agent_id',0);
		$db->setQuery("Select id from #__osrs_companies where user_id = '$user->id'");
		$cid = $db->loadResult();
		$db->setQuery("INSERT INTO #__osrs_company_agents (id,company_id,agent_id) VALUES (NULL,'$cid',$agent_id)");
		$db->execute();
		$db->setQuery("Update #__osrs_agents set company_id = '$cid' where id = '$agent_id'");
		$db->execute();
		$connector = ($configClass['joomlauser'] == 1) ? 'left' : 'inner';
		$query = "Select a.* from #__osrs_agents as a"
				." ".$connector." join #__users as b on b.id = a.user_id"
				." where a.published = '1' and a.id not in (Select agent_id from #__osrs_company_agents)"
				." and (a.name like '%$keyword%' or a.address like '%$keyword%' or a.email like '%$keyword%') and a.id <> '$agent_id' order by a.name";
		$db->setQuery($query);
		$agents = $db->loadObjectList();
		HTML_OspropertyCompany::showSearchAgentResults($option,$agents);
		echo "@@@";
		OspropertyCompany::showCompanyAgent($option,$cid);
		exit();
	}
	
	/**
	 * Search agent
	 *
	 * @param unknown_type $option
	 */
	static function searchAgent($option,$keyword,$agent_adding = ''){
		global $mainframe,$jinput, $configClass;
		$db = Factory::getDbo();
		$agent_id = $jinput->getInt('agent_id',0);
		$connector = ($configClass['joomlauser'] == 1) ? 'left' : 'inner';
		if($agent_id > 0){
			$query = "Select a.* from #__osrs_agents as a"
					." where a.id = '$agent_id' and a.id <> '$agent_adding'";
		}else{
			$query = "Select a.* from #__osrs_agents as a"
					." ".$connector." join #__users as b on b.id = a.user_id"
					." where a.published = '1' and a.id not in (Select agent_id from #__osrs_company_agents)"
					." and (a.name like '%$keyword%' or a.address like '%$keyword%' or a.email like '%$keyword%') and a.id <> '$agent_adding' order by a.name";
		}
		$db->setQuery($query);
		$agents = $db->loadObjectList();
		HTML_OspropertyCompany::showSearchAgentResults($option,$agents);
		exit();
	}
	
	/**
	 * Remove agent
	 *
	 * @param unknown_type $option
	 */
	static function removeAgent($option){
		global $mainframe,$jinput;
		$user = Factory::getUser();
		$agent_id = $jinput->getInt('agent_id',0);
		
		if(HelperOspropertyCommon::isAgentOfCompany($agent_id)){
			$db = Factory::getDbo();
			$db->setQuery("Select id from #__osrs_companies where user_id = '$user->id'");
			$cid = $db->loadResult();
			$db->setQuery("Delete from #__osrs_company_agents where company_id = '$cid' and agent_id = '$agent_id'");
			$db->execute();
			$db->setQuery("Update #__osrs_agents set company_id = '0' where id = '$agent_id'");
			$db->execute();
		}
		OspropertyCompany::showCompanyAgent($option,$cid);
		exit();
	}
	
	/**
	 * Show company agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $company_id
	 */
	static function showCompanyAgent($option,$company_id)
	{
		global $configClass;
		$connector = ($configClass['joomlauser'] == 1) ? 'left' : 'inner';
		$db = Factory::getDbo();
		$query = "Select a.*,u.username from #__osrs_agents as a"
				." ".$connector." join #__users as u on u.id = a.user_id"
				." where a.id in (Select id from #__osrs_agents where company_id = '$company_id' and published = '1') order by a.name";
		$db->setQuery($query);
		//echo $db->getQuery();
		$agents = $db->loadObjectList();
		if(count($agents) > 0){
			for($i=0;$i<count($agents);$i++){
				$agent = $agents[$i];
				$db->setQuery("Select count(id) from #__osrs_properties where published = '1' and approved ='1' and agent_id = '$agent->agentid'");
				$agent->nproperties = intval($db->loadResult());
			}
		}
		HTML_OspropertyCompany::showCompanyAgent($option,$agents);
	}
	
	/**
	 * Save company information
	 *
	 * @param unknown_type $option
	 */
	static function saveCompanyInfo($option){
		global $mainframe,$jinput,$configClass,$languages;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		$country = $jinput->getInt('country',HelperOspropertyCommon::getDefaultCountry());
		//check to see if user uploaded new state
		$nstate = $jinput->getString('nstate','');
		if($nstate != ""){
			//insert into state table
			$db->setQuery("Insert into #__osrs_states (id,country_id,state_name,state_code) values (NULL,'$country','$nstate','$nstate')");
			$db->execute();
			$state = $db->insertID();
			//JRequest::setVar('state',$state);
			$_POST['state'] = $state;
		}
		
		jimport('joomla.filesystem.file');
		$post = $jinput->post->getArray();
		$post['user_id'] = $user->id;
		// check folder to upload file
		if (!Folder::exists(PATH_STORE_PHOTO_COMPANY_THUMB)) Folder::create(PATH_STORE_PHOTO_COMPANY_THUMB);
		
		// remove if you want
		if (isset($post['remove_photo'])){
			if (is_file(PATH_STORE_PHOTO_COMPANY_FULL.DS.$post['photo'])) unlink(PATH_STORE_PHOTO_COMPANY_FULL.DS.$post['photo']);
			if (is_file(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$post['photo'])) unlink(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$post['photo']);
			$post['photo'] = '';
		}
		
		// upload file
		//check to see if this is JPEG photo
		if ( !empty($_FILES['file_photo']['name']) && $_FILES['file_photo']['error'] == 0 &&  $_FILES['file_photo']['size'] > 0 ) {
			if(!HelperOspropertyCommon::checkIsPhotoFileUploaded('file_photo')){
				$needs = array();
				$needs[] = "ccompanydetails";
				$needs[] = "company_edit";
				$itemid  = OSPRoute::getItemid($needs);
				OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_edit&Itemid=".$itemid),Text::_('OS_ONLY_SUPPORT_JPG_IMAGES'));
			}else{
                $post['photo'] = OSPHelper::uploadAndResizePicture($_FILES['file_photo'],"company",$post['photo']);
			}
		}
	
		// standard file name
		/*
		$filename = str_replace('  ',' ',$post['company_name']);
		$filename = strtolower(str_replace(' ','_',$filename)).'_';
		$filename = uniqid($filename).'.';	
		if ($post['photo'] != ''){
			$filename .= File::getExt(PATH_STORE_PHOTO_COMPANY_FULL.DS.$post['photo']);
			if (is_file(PATH_STORE_PHOTO_COMPANY_FULL.DS.$post['photo']))	
				rename(PATH_STORE_PHOTO_COMPANY_FULL.DS.$post['photo'],PATH_STORE_PHOTO_COMPANY_FULL.DS.$filename);
				
			if (is_file(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$post['photo']))
				rename(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$post['photo'],PATH_STORE_PHOTO_COMPANY_THUMB.DS.$filename);
			$post['photo'] = $filename;
		}
		*/
		
		// store data
		$row = Table::getInstance('Companies','OspropertyTable');
		$row->bind($post);	
		$company_description = $_POST['company_description'];
		$row->company_description = $company_description;
		$row->check();
		$id = $jinput->getInt('id',0);
		
		$msg = Text::_('OS_ITEM_SAVED'); 
	 	if (!$row->store()){
		 	$msg = Text::_('OS_ERROR_SAVING'); ;		 			 	
		}
		
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language) {	
				$sef = $language->sef;
				$company_description_language = $row->company_description;
				if($company_description_language != ""){
					$company = Table::getInstance('Companies','OspropertyTable');
					$company->id = $id;
					$company->user_id = $user->id;
					$company->{'company_description_'.$sef} = $company_description_language;
					$company->store();
				}
			}
		}
		
		$needs = array();
		$needs[] = "ccompanydetails";
		$needs[] = "company_edit";
		$itemid  = OSPRoute::getItemid($needs);
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_edit&Itemid=".$itemid),$msg);
	}
	
	/**
	 * Edit company information
	 *
	 * @param unknown_type $option
	 */
	static function editCompany($option){
		global $mainframe,$jinput,$configClass,$languages;
		HTMLHelper::_('behavior.keepalive');
		$db = Factory::getDbo();
		$user = Factory::getUser();
		if(!HelperOspropertyCommon::isCompanyAdmin())
		{
			if(HelperOspropertyCommon::isAgent($user->id))
			{
				$needs = array();
				$needs[] = "aeditdetails";
				$needs[] = "agent_default";
				$needs[] = "agent_editprofile";
				$itemid = OSPRoute::getItemid($needs);
				OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_default&Itemid=".$itemid),'');
			}
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		$db->setQuery("Select * from #__osrs_companies where user_id = '$user->id'");
		$row = $db->loadObject();
		
		$lists['country'] = HelperOspropertyCommon::makeCountryList($row->country,'country','onchange="change_country_company(this.value,'.$row->state.','.$row->city.')"',Text::_('OS_SELECT_COUNTRY'),'','input-large form-select');

		if(OSPHelper::userOneState()){
			$lists['state'] = "<input type='hidden' name='state' id='state' value='".$row->state."'/>";
		}else{
			$lists['state'] = HelperOspropertyCommon::makeStateListAddProperty($row->country,$row->state,'state','onchange="change_state_company(this.value,'.intval($row->city).')"',Text::_('OS_SELECT_STATE'),'class="input-large form-select"');
		}
		if(intval($row->state) == 0){
			$row->state = OSPHelper::returnDefaultState();
		}
		$lists['city'] = HelperOspropertyCommon::loadCityAddProperty($option,$row->state,$row->city,"input-large form-select");

        $optArr = array();
        $optArr[] = HTMLHelper::_('select.option',0,Text::_('OS_YES'));
        $optArr[] = HTMLHelper::_('select.option',1,Text::_('OS_NO'));
        $lists['optin'] = HTMLHelper::_('select.genericlist',$optArr,'optin','class="input-medium form-select"','value','text',(int)$row->optin);
		
		HTML_OspropertyCompany::editCompany($option,$row,$lists);
	}
	
	/**
	 * Manage agents
	 *
	 * @param unknown_type $option
	 */
	static function editAgent($option)
	{
		global $mainframe,$jinput,$configClass;
		HTMLHelper::_('behavior.keepalive');
		$db = Factory::getDbo();
		$user = Factory::getUser();
		$document = Factory::getDocument();
		$document->setTitle(Text::_('OS_MANAGE_AGENTS'));
		if(!HelperOspropertyCommon::isCompanyAdmin()){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		
		
		$filter_search	= OSPHelper::getStringRequest('filter_search','');
		$filter_search	= $db->escape($filter_search);
		$sortby			= OSPHelper::getStringRequest('sortby','a.name');
		$orderby		= OSPHelper::getStringRequest('orderby','');
		$db->setQuery("Select * from #__osrs_companies where user_id = '$user->id'");
		$row = $db->loadObject();
		$connector		= ($configClass['joomlauser'] == 1) ? 'left' : 'inner';
		$query = "Select a.*,u.username from #__osrs_agents as a"
				." ".$connector." join #__users as u on u.id = a.user_id"
				." where a.id in (Select id from #__osrs_agents where company_id = '$row->id') ";
		if($filter_search != ""){
			$query .= " and (a.name like '%$filter_search%' or u.username like '%$filter_search%')";
		}
		if($sortby != ""){
			$query .= " order by $sortby $orderby";
		}
		$db->setQuery($query);
		
		//echo $db->getQuery();
		$agents = $db->loadObjectList();
		if(count($agents) > 0){
			for($i=0;$i<count($agents);$i++){
				$agent = $agents[$i];
				$db->setQuery("Select count(id) from #__osrs_properties where published = '1' and approved ='1' and agent_id = '$agent->id'");
				$agent->nproperties = intval($db->loadResult());
			}
		}
		
		$orderbyArr[] = HTMLHelper::_('select.option','',Text::_('OS_ORDERBY'));
		$orderbyArr[] = HTMLHelper::_('select.option','asc',Text::_('OS_ASC'));
		$orderbyArr[] = HTMLHelper::_('select.option','desc',Text::_('OS_DESC'));
		$lists['orderby'] = HTMLHelper::_('select.genericlist',$orderbyArr,'orderby','class="input-medium" onchange="javascript:document.manageagent.submit();"','value','text',$orderby);
		
		$sortbyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SORTBY'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.ordering',Text::_('OS_ORDERING'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.name',Text::_('OS_NAME'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.email',Text::_('OS_EMAIL'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.published',Text::_('OS_STATUS'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.id',Text::_('ID'));
		$lists['sortby'] = HTMLHelper::_('select.genericlist',$sortbyArr,'sortby','class="input-medium" onchange="javascript:document.manageagent.submit();"','value','text',$sortby);
		
		HTML_OspropertyCompany::editAgent($option,$row,$lists,$agents);
	}
	
	/**
	 * List properties of company
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function listingProperties($option,$id){
		global $mainframe,$jinput,$configClass,$lang_suffix;
		$db = Factory::getDBO();
		//check to see if this is agent
		//if(!HelperOspropertyCommon::isAgent()){
			//OSPHelper::redirect("index.php",Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		//}
		
		$db->setQuery("Select * from #__osrs_companies where id = '$id'");
		$company = $db->loadObject();
		
		$document = Factory::getDocument();
		$document->setTitle($configClass['general_bussiness_name']." - ".$company->company_name."'".Text::_(' properties'));
		$user = Factory::getUser();
		//get agent id
		$db->setQuery("Select id from #__osrs_agents where user_id = '$user->id'");
		$agent_id = $db->loadResult();
		
		//$limit = $jinput->getInt('limit',5);
		//$limitstart = $jinput->getInt('limitstart',0);
		$limit = $mainframe->getUserStateFromRequest('com_osproperty.company.limit', 'limit', $configClass['general_number_properties_per_page'], 'int');
		$limitstart = $mainframe->getUserStateFromRequest('com_osproperty.company.limitstart', 'limitstart', 0, 'int');
		$orderby = OSPHelper::getStringRequest('orderby','a.created');
		$ordertype = OSPHelper::getStringRequest('ordertype','desc');
		
		$lists['orderby'] = $orderby;
		$lists['ordertype'] = $ordertype;
		$lists['limit'] = $limit;
		$lists['limitstart'] = $limitstart;
		
		HTML_OspropertyCompany::companyProperties($option,$lists,$company);
		
	}
	
	/**
	 * Company listing
	 *
	 * @param unknown_type $option
	 */
	static function companyListing($option)
    {
		global $mainframe,$jinput,$configClass,$lang_suffix;
		OSPHelper::generateHeading(1,$configClass['general_bussiness_name']." - ".Text::_('OS_LIST_COMPANIES'));
		$db             = Factory::getDBO();

		$limit          = $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
		$limitstart	    = OSPHelper::getLimitStart();

		$orderby        = OSPHelper::getStringRequest('orderby','a.company_name');
		$ordertype      = OSPHelper::getStringRequest('ordertype','asc');
		$keyword        = OSPHelper::getStringRequest('keyword','');
		$keyword        = $db->escape($keyword);
		$query          = "Select count(id) from #__osrs_companies where published = '1'";
        if($configClass['use_privacy_policy'] == 1)
        {
                $query .= " and optin = '0' ";
        }
		if($keyword != "")
		{
			$query      .= " and (company_name like '%$keyword%' or address like '%$keyword%' or city like '%$keyword%' or website like '%$keyword%' or company_description$lang_suffix like '%$keyword%')";
		}
		$db->setQuery($query);
		$total = $db->loadResult();

		if($configClass['overrides_pagination'] == 1)
		{
			$pageNav    = new OSPJPagination($total,$limitstart,$limit);
		}
		else
		{
			$pageNav    =  new Pagination($total,$limitstart,$limit);
		}


		$query = "Select a.*,b.country_name,c.state_name$lang_suffix as state_name from #__osrs_companies as a"
				." left join #__osrs_countries as b on b.id = a.country"
				." left join #__osrs_states as c on c.id = a.state";
		$query .= " where a.published = '1'";
		if($configClass['use_privacy_policy'] == 1)
        {
            $query .= " and a.optin = '0' ";
        }
		if($keyword != ""){
			$query .= " and (a.company_name like '%$keyword%' or a.address like '%$keyword%' or a.city like '%$keyword%' or a.website like '%$keyword%' or a.company_description$lang_suffix like '%$keyword%')";
		}
		$query .= " order by $orderby $ordertype";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		//echo $db->getQuery();
		$rows = $db->loadObjectList();
		
		if(count($rows) > 0)
		{
			$connector = ($configClass['joomlauser'] == 1) ? 'left' : 'inner';
			for($i=0;$i<count($rows);$i++)
			{
				$agentlist = "";
				$row = $rows[$i];
				$db->setQuery("Select a.id from #__osrs_agents as a ".$connector." join #__users as b on b.id = a.user_id where a.company_id = '$row->id'");
				$agents = $db->loadObjectList();
				if(count($agents) > 0)
				{
					$agentlist = "";
					for($j=0;$j<count($agents);$j++)
					{
						$agent = $agents[$j];
						$agentlist .= $agent->id.",";
					}
					$agentlist = substr($agentlist,0,strlen($agentlist)-1);
				}
				if($agentlist != "")
				{
					$db->setQuery("Select count(id) from #__osrs_properties where published = '1' and approved = '1' and agent_id in ($agentlist)");
					$row->countlisting = intval($db->loadResult());
				}
				else
				{
					$row->countlisting = 0;
				}
			}
		}
		$orderbyArr[] = HTMLHelper::_('select.option','a.company_name',Text::_('OS_COMPANY_NAME'));
		$orderbyArr[] = HTMLHelper::_('select.option','a.city',Text::_('City'));
		$lists['orderby'] = HTMLHelper::_('select.genericlist',$orderbyArr,'orderby','class="input-medium"','value','text',$orderby);
		
		$ordertypeArr[] = HTMLHelper::_('select.option','asc',Text::_("OS_ASC"));
		$ordertypeArr[] = HTMLHelper::_('select.option','desc',Text::_("OS_DESC"));
		$lists['ordertype'] = HTMLHelper::_('select.genericlist',$ordertypeArr,'ordertype','class="input-medium"','value','text',$ordertype);
		
		HTML_OspropertyCompany::listCompanies($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * Company details
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function companyDetails($option,$id){
		global $mainframe,$jinput,$configClass,$lang_suffix;
		$db = Factory::getDbo();
		if(intval($id) == 0){
			throw new Exception(Text::_( 'OS_COMPANY_NOT_AVAILABLE'), 404);
		}
        $session = Factory::getSession();
        $session->set('company_id',$id);
		$query = "Select a.*,b.country_name,c.state_name$lang_suffix as state_name from #__osrs_companies as a"
				." left join #__osrs_countries as b on b.id = a.country"
				." left join #__osrs_states as c on c.id = a.state"
				." where a.id = '$id'";
		$db->setQuery($query);
		$company = $db->loadObject();
		if($company->published == 0){
			throw new Exception(Text::_( 'OS_COMPANY_NOT_AVAILABLE'), 404);
		}
		
		$document = Factory::getDocument();

		$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_COMPANY_DETAILS')." - ".$company->company_name);
		$query = "Select a.*,b.country_name,c.state_name$lang_suffix as state_name from #__osrs_agents as a"
				." left join #__users as u on u.id = a.user_id"
				." left join #__osrs_countries as b on b.id = a.country"
				." left join #__osrs_states as c on c.id = a.state"
				." where a.published = '1' and a.company_id = '$id'";
		$db->setQuery($query);
		$agents = $db->loadObjectList();
		if(count($agents) >0)
		{
			for($i=0;$i<count($agents);$i++)
			{
				$agent = $agents[$i];
				$db->setQuery("Select count(id) from #__osrs_properties where published = '1' and approved = '1' and agent_id = '$agent->id'");
				$agent->countlisting = $db->loadResult();
			}
		}
		HTML_OspropertyCompany::companyDetailsForm($option,$company,$agents);
	}
	
	/**
	 * Submit contact
	 *
	 * @param unknown_type $option
	 */
	static function submitContact($option,$id)
	{
		global $mainframe,$jinput,$configClass;
		$db = Factory::getDbo();

		$captchaPlugin = $mainframe->getParams()->get('captcha', Factory::getConfig()->get('captcha'));
		if ($captchaPlugin)
		{
			$res           = Captcha::getInstance($captchaPlugin)->checkAnswer($jinput->post->get('recaptcha_response_field', '', 'string'));
			if (!$res)
			{
				$msg = Text::_('OS_YOU_SHOULD_PASS_SECURITY_STEP_FIRST');
				$url = base64_decode($jinput->getString('return',''));
				OSPHelper::redirect($url, $msg);
			}
		}

        $session = Factory::getSession();
        $company_id = $session->get('company_id',0);
        if(($company_id == 0) or ($company_id != $id)){
            $msg = Text::_('OS_SECURITY_CODE_IS_WRONG');
            OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_info&id=".$id."&Itemid=".$jinput->getInt('Itemid',0)),$msg);
        }

		$date = date("j",time());
		$comment_author = $jinput->getString('comment_author'.$date,'');
		$comment_email = $jinput->getString('comment_email'.$date,'');
		if(($comment_author == "") or ($comment_email == "")){
			$msg = Text::_('OS_EMAIL_CANT_BE_SENT');
			OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_info&id=".$id."&Itemid=".$jinput->getInt('Itemid',0)),$msg);
		}
		$comment_title  = $jinput->getString('comment_title','');
		$message		= $_POST['message'];
		
		$contact['author']  = $comment_author;
		$contact['email']   = $comment_email;
		$contact['title']   = $comment_title;
		$contact['message'] = $message;
		
		//send contact email
		$db->setQuery("Select * from #__osrs_companies where id = '$id'");
		$company  = $db->loadObject();
		$emailto  = $company->email;
		$contact['emailto'] = $emailto;
		$receiver =	$company->company_name;
		$contact['receiver'] = $receiver;
		
		OspropertyEmail::sendContactEmail($option,$contact);
		
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_info&id=".$id."&Itemid=".$jinput->getInt('Itemid',0)),Text::_('OS_EMAIL_HAS_BEEN_SENT'));
	}
	
	/**
	 * Change agent status
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function agentStatus($option,$cid,$state){
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		if(count($cid) > 0){
			//$cids = implode(",",$cid);
			//$db->setQuery("Update #__osrs_agents set published = '$state' where id in ($cids)");
			//$db->execute();
			foreach ($cid as $id){
				if(HelperOspropertyCommon::isAgentOfCompany($id)){
					$db->setQuery("Update #__osrs_agents set published = '$state' where id = '$id'");
					$db->execute();
				}
			}
		}
		$msg = Text::_('OS_STATUS_HAVE_BEEN_CHANGED');
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_agent"),$msg);
	}
	
	/**
	 * Change feature statuses of agents
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function agentFeature($option,$cid,$state){
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		if(count($cid) > 0){
			foreach ($cid as $id){
				if(HelperOspropertyCommon::isAgentOfCompany($id)){
					$db->setQuery("Update #__osrs_agents set featured = '$state' where id = '$id'");
					$db->execute();
				}
			}
		}
		$msg = Text::_('OS_STATUS_HAVE_BEEN_CHANGED');
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_agent"),$msg);
	}
	
	
	/**
	 * Remove agents
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function removeAgents($option,$cid){
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		if(count($cid) > 0){
			foreach ($cid as $id){
				if(HelperOspropertyCommon::isAgentOfCompany($id)){
					$db->setQuery("Update #__osrs_agents set company_id = '0' where id = '$id'");
					$db->execute();
				}
			}
		}
		$msg = Text::_('OS_AGENTS_HAVE_BEEN_REMOVE_OUT_OF_YOUR_COMPANY');
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_agent"),$msg);
	}
	
	/**
	 * Assign new agents
	 *
	 * @param unknown_type $option
	 */
	static function assignNewAgents($option){
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		$document = Factory::getDocument();
		$document->setTitle(Text::_('OS_ASSIGN_AGENTS_TO_COMPANY'));
		$keyword = OSPHelper::getStringRequest('keyword','','post');

        $keyword = $db->escape($keyword);
        $query = "Select a.* from #__osrs_agents as a"
                    ." where a.published = '1' and a.company_id = '0'";
        if($keyword != "") {
            $query .=  " and (a.name like '%$keyword%' or a.address like '%$keyword%' or a.email like '%$keyword%') and (a.company_id = '0' or a.company_id  IS NULL) and a.agent_type='0'";
        }
        $query .= " order by a.name limit 50";
        $db->setQuery($query);
        $agents = $db->loadObjectList();
		HTML_OspropertyCompany::showSearchAgentForm($option,$agents);
	}
	
	/**
	 * Process agent to company
	 *
	 * @param unknown_type $option
	 */
	static function processToAssignAgentToCompany($option){
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		if($user->id == 0){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		//find company id
		$db->setQuery("Select id from #__osrs_companies where user_id  = '$user->id'");
		$company_id = $db->loadResult();
		if($company_id == 0){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		$agent_id = $jinput->getInt('agent_id',0);
		if($agent_id == 0){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}else{
			$db->setQuery("Update #__osrs_agents set company_id = '$company_id' where id = '$agent_id'");
			$db->execute();
		}
		OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_addnew'),Text::_('OS_AGENT_HAS_BEEN_ASSIGNED_TO_YOUR_COMPANY'));
	}
	
	/**
	 * Add new agent form
	 *
	 * @param unknown_type $option
	 */
	static function modifyAgent($option,$id){
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		if($user->id == 0){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		//find company id
		$db->setQuery("Select id from #__osrs_companies where user_id  = '$user->id'");
		$company_id = $db->loadResult();
		if($company_id == 0){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		
		if((!HelperOspropertyCommon::isAgentOfCompany($id)) and ($id > 0)){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		
		$document = Factory::getDocument();
		if($id > 0){
			$document->setTitle(Text::_('OS_MODIFY_AGENT'));
		}else{
			$document->setTitle(Text::_('OS_ADD_AGENT'));	
		}
		
		
		if($id > 0){
			$db->setQuery("Select * from #__osrs_agents where id = '$id'");
			$agent = $db->loadObject();
		}
		
		$lists['country'] = HelperOspropertyCommon::makeCountryList($agent->country,'country','onChange="javascript:loadState(this.value,\''.$agent->state.'\',\''.$agent->city.'\')"',Text::_('OS_SELECT_COUNTRY'),'','input-large form-select');
		
		if(OSPHelper::userOneState()){
			$lists['state'] = "<input type='hidden' name='state' id='state' value='".OSPHelper::returnDefaultState()."'/>";
		}else{
			$lists['state'] = HelperOspropertyCommon::makeStateListAddProperty($agent->country,$agent->state,'state','onChange="javascript:loadCity(this.value,\''.$agent->city.'\')"',Text::_('OS_SELECT_STATE'),'class="input-large form-select"');
		}

		if(OSPHelper::userOneState()){
			$default_state = OSPHelper::returnDefaultState();
		}else{
			$default_state = $agent->state;
		}
		$lists['city'] = HelperOspropertyCommon::loadCityAddProperty($option,$default_state,$agent->city,"input-large form-select");
		
		$optionArr = array();
		$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
		$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
		$lists['published']   = HTMLHelper::_('select.genericlist',$optionArr,'published','class="input-mini form-select"','value','text',$agent->published);
		
		$optionArr = array();
		$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
		$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
		$lists['featured']   = HTMLHelper::_('select.genericlist',$optionArr,'featured','class="input-mini form-select"','value','text',$agent->featured);
		
		HTML_OspropertyCompany::showAddAgentForm($option,$lists,$company_id,$agent);
	}
	
	/**
	 * Save Agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 * @param unknown_type $save
	 */
	static function saveAgent($option,$id,$save)
	{
		global $mainframe,$jinput,$configClass,$languages;
		$db = Factory::getDBO();
		//$id = JRequest::getVar('id',0);
		$itemid = $jinput->getInt('Itemid',0);
		//save user without sending email
		if($id == 0){
			$user 		= clone(Factory::getUser());
			$config		= Factory::getConfig();
			//$authorize	=& Factory::getACL();
			$document   =& Factory::getDocument();
			$userid 	= 0;
			//clean request
			$username = $jinput->getString('username', '');
			$db->setQuery("Select count(id) from #__users where username like '$username'");
			$countuser = $db->loadResult();
			if($countuser > 0){
				OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_addagents&Itemid=".$itemid),Text::_('OS_USER_IS_ALREADY_EXISTS'));
			}
			$email = OSPHelper::getStringRequest('email','','post');
			$db->setQuery("Select count(id) from #__users where email like '$email'");
			$countemail = $db->loadResult();
			if($countemail > 0){
				OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_addagents&Itemid=".$itemid),Text::_('OS_EMAIL_IS_ALREADY_EXISTS'));
			}
			
			
			//register new user in the case user is not registered-user
			// Get the form data.
			//$data	= JRequest::getVar('user', array(), 'post', 'array');
			$config = Factory::getConfig();
			$params = ComponentHelper::getParams('com_users');
	
			// Initialise the table with JUser.
			$user = new User;
			$app	= Factory::getApplication();
			$componentParams = $app->getParams('com_users');
			$new_usertype = $componentParams->get('new_usertype', '2');
	
			// Prepare the data for the user object.
			$data['username']	= $username;
			$data['email']		= $email;
			$data['email2']		= $email;
			$data['password']	= $jinput->getString('password','');
			$data['password2']	= $jinput->getString('password1','');
			$data['name']		= $jinput->getString('name','');
			$groups[0]			= $new_usertype;
			$data['groups']	 	= $groups;
			
			$useractivation = 0; //auto approval
			
			// Bind the data.
			if (!$user->bind($data)) {
				OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_addagents&Itemid='.$itemid),Text::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
				return false;
			}		
			// Store the data.
			if (!$user->save()) {
				OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_addagents&Itemid='.$itemid),Text::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
				return false;
			}
		}
		
		$country = $jinput->getInt('country',$configClass['show_country_id']);
		jimport('joomla.filesystem.file');
		$post = $jinput->post->getArray();
		
		// check folder to upload
		if (!Folder::exists(PATH_STORE_PHOTO_AGENT_THUMB)) Folder::create(PATH_STORE_PHOTO_AGENT_THUMB);
		
		// remove file if you want
		if (isset($post['remove_photo'])){
			if (is_file(PATH_STORE_PHOTO_AGENT_FULL.DS.$post['photo'])) unlink(PATH_STORE_PHOTO_AGENT_FULL.DS.$post['photo']);
			if (is_file(PATH_STORE_PHOTO_AGENT_THUMB.DS.$post['photo'])) unlink(PATH_STORE_PHOTO_AGENT_THUMB.DS.$post['photo']);
			$post['photo'] = '';
		}
			
		// upload file
		if(!HelperOspropertyCommon::checkIsPhotoFileUploaded('file_photo')){
			//return to previous page
			?>
			<script type="text/javascript">
			window.history(-1);
			</script>
<?php
		}else{
			if ( !empty($_FILES['file_photo']['name']) && $_FILES['file_photo']['error'] == 0 &&  $_FILES['file_photo']['size'] > 0 ) {
                /*
				$imagename = OSPHelper::processImageName(uniqid().$_FILES['file_photo']['name']);
				if (move_uploaded_file($_FILES['file_photo']['tmp_name'],PATH_STORE_PHOTO_AGENT_FULL.DS.$imagename)){
					
					// copy image before resize
					copy(PATH_STORE_PHOTO_AGENT_FULL.DS.$imagename,PATH_STORE_PHOTO_AGENT_THUMB.DS.$imagename);
					// resize image just copy and replace it selft
					require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'image.php');
					$image = new SimpleImage();
					$image->load(PATH_STORE_PHOTO_AGENT_THUMB.DS.$imagename);
					$imagesize = getimagesize(PATH_STORE_PHOTO_AGENT_FULL.DS.$imagename);
					$owidth = $imagesize[0];
					$oheight = $imagesize[1];
					$nwidth = $configClass['images_thumbnail_width'];
					if($nwidth < $owidth){ //only resize when the image width is smaller
						$nheight = round(($configClass['images_thumbnail_width']*$oheight)/$owidth);
					    $image->resize($nwidth,$nheight);
					    $image->save(PATH_STORE_PHOTO_AGENT_THUMB.DS.$imagename,$configClass['images_quality']);
					}
					// remove old image
					if (is_file(PATH_STORE_PHOTO_AGENT_FULL.DS.$post['photo'])) unlink(PATH_STORE_PHOTO_AGENT_FULL.DS.$post['photo']);
					if (is_file(PATH_STORE_PHOTO_AGENT_THUMB.DS.$post['photo'])) unlink(PATH_STORE_PHOTO_AGENT_THUMB.DS.$post['photo']);
				    // keep file name
				    $post['photo'] = $imagename;
				}
                */
                $post['photo'] = OSPHelper::uploadAndResizePicture($_FILES['file_photo'],"agent",$post['photo']);
			}
		}
			
		// change file name for standard
		$filename = str_replace('  ',' ',$post['name']);
		$filename = strtolower(str_replace(' ','_',$filename)).'_';
		$filename = uniqid($filename).'.';	
		if ($post['photo'] != ''){
			$filename .= File::getExt(PATH_STORE_PHOTO_AGENT_FULL.DS.$post['photo']);
			if (is_file(PATH_STORE_PHOTO_AGENT_FULL.DS.$post['photo']))	
				rename(PATH_STORE_PHOTO_AGENT_FULL.DS.$post['photo'],PATH_STORE_PHOTO_AGENT_FULL.DS.$filename);
				
			if (is_file(PATH_STORE_PHOTO_AGENT_THUMB.DS.$post['photo']))
				rename(PATH_STORE_PHOTO_AGENT_THUMB.DS.$post['photo'],PATH_STORE_PHOTO_AGENT_THUMB.DS.$filename);
			$post['photo'] = $filename;
		}
		
		$row = Table::getInstance('Agent','OspropertyTable');
		$row->bind($post);
		if($id == 0){
			$row->user_id = $user->id;
			$row->company_id = $jinput->getInt('company_id',0);
		}
		// if new item, order last in appropriate group
		if (!$row->id) {
			$where = 'company_id = ' . (int) $row->company_id ;
			$row->ordering = $row->getNextOrder( $where );
		}
		$bio = $_POST['bio'];
		$row->bio = $bio;
		$row->check();
		$msg = Text::_('OS_ITEM_HAS_BEEN_SAVED'); 
	 	if (!$row->store()){
		 	$msg = Text::_('OS_ERROR_SAVING'); ;		 			 	
		}
		
		//update into #__osrs_company_agents
		if($id == 0){
			$id = $db->insertID();
		}
		
		if(intval($configClass['agent_joomla_group_id']) > 0){
			$user_id = $user->id;
			$db->setQuery("Select count(user_id) from #__user_usergroup_map where user_id = '$user_id' and group_id = '".$configClass['agent_joomla_group_id']."'");
			$count = $db->loadResult();
			if($count == 0){
				$db->setQuery("Insert into #__user_usergroup_map (user_id,group_id) values ('$user_id','".$configClass['agent_joomla_group_id']."')");
				$db->execute();
			}
		}
		
		//update for other languages
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language) {	
				$sef = $language->sef;
				$bio_language = $row->bio;
				if($bio_language != ""){
					$row = Table::getInstance('Agent','OspropertyTable');
					$row->id = $id;
					$row->{'bio_'.$sef} = $bio_language;
					$row->store();
				}
			}
		}
		
		$alias = OSPHelper::getStringRequest('alias','','post');
		$agent_alias = OSPHelper::generateAlias('agent',$id,$alias);
		$db->setQuery("Update #__osrs_agents set alias = '$agent_alias' where id = '$id'");
		$db->execute();
		
		$msg = Text::_('OS_AGENT_HAS_BEEN_SAVED');
		if($save == 1){
			OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_agent'),$msg);
		}else{
			OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_editagent&id='.$id),$msg);
		}
	}
	
	/**
	 * Manage properties of agents of companies
	 *
	 * @param unknown_type $option
	 */
	static function manageProperties($option){
		global $mainframe,$jinput,$configClass,$lang_suffix;
		$db         = Factory::getDbo();
		$config     = new JConfig();
		$user       = Factory::getUser();
		$db->setQuery("Select id from #__osrs_companies where user_id  = '$user->id'");
		$company_id = $db->loadResult();
		$document   = Factory::getDocument();
		$document->setTitle(Text::_('OS_MANAGE_PROPERTIES'));
		if(!HelperOspropertyCommon::isCompanyAdmin()){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		
		$filter_search  = OSPHelper::getStringRequest('filter_search','');
		$filter_search  = $db->escape($filter_search);
		$sortby         = OSPHelper::getStringRequest('sortby','a.id');
		$orderby        = OSPHelper::getStringRequest('orderby','desc');
		$category_id    = $jinput->getInt('category_id',0);
		$type_id        = $jinput->getInt('type_id',0);
		$status         = OSPHelper::getStringRequest('status','');
		$list_limit     = $config->list_limit;
		$limit          = $jinput->getInt('limit',$list_limit);
		$limitstart	    = OSPHelper::getLimitStart();
		
		$query = "Select count(a.id) from #__osrs_properties as a"
				." INNER JOIN #__osrs_agents as g on g.id = a.agent_id"
				." INNER JOIN #__osrs_companies as co on co.id = g.company_id"
				." LEFT  JOIN #__osrs_types as d on d.id = a.pro_type"
				." INNER JOIN #__osrs_countries as e on e.id = a.country"
				." INNER JOIN #__osrs_states as s on s.id = a.state"
				." INNER JOIN #__osrs_cities as c on c.id = a.city"
				." WHERE co.id = '$company_id'";
		if($filter_search != ""){
			$query .= " AND (a.pro_name$lang_suffix LIKE '%$filter_search%'";
			$query .= " OR a.ref like '%$filter_search%'";
			$query .= " OR g.name like '%$filter_search%'";
			$query .= " OR d.type_name$lang_suffix like '%$filter_search%'";
			$query .= " OR s.state_name$lang_suffix like '%$filter_search%'";
			$query .= " OR c.city$lang_suffix like '%$filter_search%'";
			$query .= ")";
		}
		if($category_id > 0){
			$query .= " AND a.id in(Select pid from #__osrs_property_categories where category_id = '$category_id')";
		}
		if($type_id > 0){
			$query .= " AND a.pro_type = '$type_id'";
		}
		if($status != ""){
			$query .= " AND a.published = '$status'";
		}
		$db->setQuery($query);
		$total = $db->loadResult();

		if($configClass['overrides_pagination'] == 1){
			$pageNav = new OSPJPagination($total,$limitstart,$limit);
		}else{
			$pageNav = new Pagination($total,$limitstart,$limit);
		}

		//$pageNav = new OSPJPagination($total,$limitstart,$limit);
		
		$query = "Select a.id, a.ref, a.pro_name, d.type_name$lang_suffix as type_name,g.name as agent_name,a.published,a.approved, a.isFeatured,a.curr,a.price,a.price_call,a.rent_time,a.show_address,c.city,s.state_name,a.address from #__osrs_properties as a"
				." INNER JOIN #__osrs_agents as g on g.id = a.agent_id"
				." INNER JOIN #__osrs_companies as co on co.id = g.company_id"
				." LEFT  JOIN #__osrs_types as d on d.id = a.pro_type"
				." INNER JOIN #__osrs_countries as e on e.id = a.country"
				." INNER JOIN #__osrs_states as s on s.id = a.state"
				." INNER JOIN #__osrs_cities as c on c.id = a.city"
				." WHERE co.id = '$company_id'";
		if($filter_search != ""){
			$query .= " AND (a.pro_name$lang_suffix LIKE '%$filter_search%'";
			$query .= " OR a.ref like '%$filter_search%'";
			$query .= " OR g.name like '%$filter_search%'";
			$query .= " OR d.type_name$lang_suffix like '%$filter_search%'";
			$query .= " OR s.state_name$lang_suffix like '%$filter_search%'";
			$query .= " OR c.city$lang_suffix like '%$filter_search%'";
			$query .= ")";
		}
		if($category_id > 0){
			$query .= " AND a.id in(Select pid from #__osrs_property_categories where category_id = '$category_id')";
		}
		if($type_id > 0){
			$query .= " AND a.pro_type = '$type_id'";
		}
		if($status != ""){
			$query .= " AND a.published = '$status'";
		}
		$query .= " ORDER BY $sortby $orderby";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		//echo $db->getQuery();
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			for($i=0;$i<count($rows);$i++){//for
				$row = $rows[$i];
				//process photo
				$db->setQuery("select count(id) from #__osrs_photos where pro_id = '$row->id'");
				$count = $db->loadResult();
				if($count > 0){
					$row->count_photo = $count;
					$db->setQuery("select image from #__osrs_photos where pro_id = '$row->id' order by ordering");	
					$photo = $db->loadResult();
					if($photo != ""){
						if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/thumb/'.$photo)){
							$row->photo = Uri::root()."images/osproperty/properties/".$row->id."/thumb/".$photo;
						}else{
							$row->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
						}
					}else{
						$row->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
					}
				}else{
					$row->count_photo = 0;
					$row->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
				}//end photo

				$db->setQuery("Select * from #__osrs_expired where pid = '$row->id'");
				$expiration = $db->loadObject();
				$row->expiration = $expiration;
			}
		}
		
		$orderbyArr[] = HTMLHelper::_('select.option','',Text::_('OS_ORDERBY'));
		$orderbyArr[] = HTMLHelper::_('select.option','asc',Text::_('OS_ASC'));
		$orderbyArr[] = HTMLHelper::_('select.option','desc',Text::_('OS_DESC'));
		$lists['orderby'] = HTMLHelper::_('select.genericlist',$orderbyArr,'orderby','class="input-medium" onchange="javascript:document.manageagent.submit();"','value','text',$orderby);
		
		$sortbyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SORTBY'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.ref',Text::_('Ref #'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.title',Text::_('OS_TITLE'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.address',Text::_('OS_ADDRESS'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.state',Text::_('OS_STATE'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.city',Text::_('OS_CITY'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.published',Text::_('OS_PUBLISHED'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.isFeatured',Text::_('OS_FEATURED'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.id',Text::_('ID'));
		$lists['sortby'] = HTMLHelper::_('select.genericlist',$sortbyArr,'sortby','class="input-medium" onchange="javascript:document.manageagent.submit();"','value','text',$sortby);
		
		$lists['category'] = OSPHelper::listCategories($category_id,'onChange="this.form.submit();"');
		
		//property types
		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_PROPERTY_TYPES'));
		$db->setQuery("SELECT id as value,type_name$lang_suffix as text FROM #__osrs_types where published = '1' ORDER BY type_name");
		$protypes = $db->loadObjectList();
		$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'type_id','class="input-large" onChange="this.form.submit();"','value','text',$type_id);
		
		$statusArr = array();
		$statusArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_STATUS'));
		$statusArr[] = HTMLHelper::_('select.option',0,Text::_('OS_UNPUBLISHED'));
		$statusArr[] = HTMLHelper::_('select.option',1,Text::_('OS_PUBLISHED'));
		$lists['status'] = HTMLHelper::_('select.genericlist',$statusArr,'status','class="input-medium" onChange="this.form.submit();"','value','text',$status);
		
		HTML_OspropertyCompany::manageProperties($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * Change property status
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function propertyStatus($option,$cid,$state){
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		if(count($cid) > 0){
			foreach ($cid as $id){
				if(HelperOspropertyCommon::isCompanyOwner($id)){
					$db->setQuery("Update #__osrs_properties set published = '$state' where id = '$id'");
					$db->execute();
				}else{
					throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
				}
			}
		}
		$msg = Text::_('OS_STATUS_HAVE_BEEN_CHANGED');
		OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_properties'),$msg);
	}
	
	/**
	 * Remove properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function removeProperties($option,$cid){
		global $mainframe,$jinput;
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$db = Factory::getDbo();
		if(count($cid) > 0){
			for($i=0;$i<count($cid);$i++){
				if(HelperOspropertyCommon::isCompanyOwner($cid[$i])){
					$pid = $cid[$i];
					$db->setQuery("Delete from #__osrs_properties where id = '$pid'");
					$db->execute();
					$db->setQuery("Delete from #__osrs_expired where pid ='$pid'");
					$db->execute();
					$db->setQuery("Delete from #__osrs_queue where pid = '$pid'");
					$db->execute();
					$db->setQuery("Delete from #__osrs_property_field_value where pro_id = '$pid'");
					$db->execute();
					$db->setQuery("Delete from #__osrs_property_amenities where pro_id = '$pid'");
					$db->execute();
					$db->setQuery("Select * from #__osrs_photos where pro_id = '$pid'");
					$photos = $db->loadObjectList();
					if(count($photos) > 0){
						for($j=0;$j<count($photos);$j++){
							$photo = $photos[$j];
							$image_path = JPATH_ROOT.DS."images".DS."osproperty".DS."properties".DS.$pid;
							@unlink($image_path.DS.$photo->image);
							@unlink($image_path.DS."medium".DS.$photo->image);
							@unlink($image_path.DS."thumb".DS.$photo->image);
						}
					}
					Folder::delete(JPATH_ROOT.DS."images".DS."osproperty".DS."properties".DS.$pid);
					$db->setQuery("Delete from #__osrs_photos where pro_id = '$pid'");
					$db->execute();
				}
			}
		}
	}
	
	/**
	 * Company registration
	 *
	 * @param unknown_type $option
	 */
	static function companyRegister($option)
	{
		global $mainframe,$jinput,$configClass;
		
		$user = Factory::getUser();
		$session = Factory::getSession();
		$isAdmin = HelperOspropertyCommon::isAgent();
		$isCompanyAdmin = HelperOspropertyCommon::isCompanyAdmin();
		if($isCompanyAdmin)
		{
			$needs = array();
			$needs[] = "company_edit";
			$needs[] = "ccompanydetails";
			$itemid = OSPRoute::getItemid($needs);
			//$itemid = OSPRoute::confirmItemid($itemid,'company_edit');
			$itemid = OSPRoute::confirmItemidArr($itemid,$needs);
			if(!OSPRoute::reCheckItemid($itemid,$needs)){
				$itemid = 9999;
			}
			OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_edit&Itemid='.$itemid));
		}
		if(($configClass['company_register'] == 0) or ($isAdmin) or ($isCompanyAdmin)){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}

		$agentregister= $session->get('agentregister');
		if($agentregister == 1){
			$session->set('post',array());
		}

		$post = $session->get('post');
		$country = $post['country'];
		$state = $post['state'];
		$city = $post['city'];

		OSPHelper::generateHeading(1,$configClass['general_bussiness_name']." - ".Text::_('OS_COMPANY_REGISTRATION'));
		$lists['country'] = HelperOspropertyCommon::makeCountryList($country,'country','onchange="change_country_company(this.value,\''.$state.'\',\''.$city.'\')"',Text::_('OS_SELECT_COUNTRY'),'input-large form-select');
		
		if(OSPHelper::userOneState()){
			$lists['state'] = "<input type='hidden' name='state' id='state' value='".OSPHelper::returnDefaultState()."'/>";
		}else{
			$lists['state'] = HelperOspropertyCommon::makeStateListAddProperty('',$state,'state','onchange="loadCity(this.value,\''.$city.'\')"',Text::_('OS_SELECT_STATE'),'class="input-large form-select"');
		}
		//$lists['city'] = HelperOspropertyCommon::loadCity($option,$row->state,$row->city);
		if(OSPHelper::userOneState()){
			$default_state = OSPHelper::returnDefaultState();
		}else{
			$default_state = $state;
		}
		$lists['city'] = HelperOspropertyCommon::loadCityAddProperty($option,$default_state,$city,"input-large form-select");
		
		HTML_OspropertyCompany::companyRegisterForm($option,$user,$lists);
	}
	
	/**
	 * Saving company registration
	 *
	 * @param unknown_type $option
	 */
	static function saveNewCompany($option){
		global $mainframe,$jinput,$configClass;
		OSPHelper::antiSpam();

        $language = Factory::getLanguage();
        $current_language = $language->getTag();
        $extension = 'com_users';
        $base_dir = JPATH_SITE;
        $language->load($extension, $base_dir, $current_language);

		Factory::getLanguage()->load('com_users');
		$user = Factory::getUser();
		$db = Factory::getDbo();
	

		if($configClass['captcha_agent_register'] == 1)
		{
			$captchaPlugin = Factory::getApplication()->getParams()->get('captcha', Factory::getConfig()->get('captcha'));
			$plugin		   = PluginHelper::getPlugin('captcha', $captchaPlugin);
			if ($plugin)
			{
				try
				{
					$res   = Captcha::getInstance($captchaPlugin)->checkAnswer($jinput->post->get('recaptcha_response_field', '', 'string'));
					if (!$res)
					{
						OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_register&Itemid='.$jinput->getInt('Itemid',0)),Text::_('OS_SECURITY_CODE_IS_WRONG'));
					}
				}
				catch (Exception $e)
				{
					//do the same with case !$res
					OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_register&Itemid='.$jinput->getInt('Itemid',0)),Text::_('OS_SECURITY_CODE_IS_WRONG'));
				}
			}
		}
		
		$isAdmin = HelperOspropertyCommon::isAgent();
		$isCompanyAdmin = HelperOspropertyCommon::isCompanyAdmin();
		if($configClass['company_register'] == 0 || $isAdmin || $isCompanyAdmin)
		{
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		
		$needs = array();
		$needs[] = "ccompanyregistration";
		$needs[] = "company_register";
		$itemid = OSPRoute::getItemid($needs);

		//process session
		$session = Factory::getSession();
		$post	 = $jinput->post->getArray();
		$session->set('post',$post);
		$session->set('companyregister',1);
		$session->set('agentregister',0);

		$user 		= clone(Factory::getUser());
			
		$userid = $jinput->getInt( 'id', 0, 'post', 'int' );

		$already_registered = 0;
		if(intval($user->id) == 0)
		{
			$email = OSPHelper::getStringRequest('email','','post');
			$db->setQuery("Select count(id) from #__users where email like '$email'");
			$countemail = $db->loadResult();
			if($countemail > 0){
				OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_register&Itemid=".$itemid),Text::_('OS_EMAIL_IS_ALREADY_EXISTS'));
			}
			else
			{
				$domains = ComponentHelper::getParams('com_users')->get('domains');

				if ($domains)
				{
					$emailDomain = explode('@', $email);
					$emailDomain = $emailDomain[1];
					$emailParts  = array_reverse(explode('.', $emailDomain));
					$emailCount  = count($emailParts);
					$allowed     = true;

					foreach ($domains as $domain)
					{
						$domainParts = array_reverse(explode('.', $domain->name));
						$status      = 0;

						// Don't run if the email has less segments than the rule.
						if ($emailCount < count($domainParts))
						{
							continue;
						}

						foreach ($emailParts as $key => $emailPart)
						{
							if (!isset($domainParts[$key]) || $domainParts[$key] == $emailPart || $domainParts[$key] == '*')
							{
								$status++;
							}
						}

						// All segments match, check whether to allow the domain or not.
						if ($status === $emailCount)
						{
							if ($domain->rule == 0)
							{
								$allowed = false;
							}
							else
							{
								$allowed = true;
							}
						}
					}

					// If domain is not allowed, fail validation. Otherwise continue.
					if (!$allowed)
					{
						//$result['success'] = false;
						//$result['message'] = Text::sprintf('JGLOBAL_EMAIL_DOMAIN_NOT_ALLOWED', $emailDomain);
						OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_register&Itemid=".$itemid),Text::sprintf('JGLOBAL_EMAIL_DOMAIN_NOT_ALLOWED', $emailDomain));
					}
				}
			}

			//clean request
			$username = $jinput->getString('username', $email);
			$db->setQuery("Select count(id) from #__users where username like '$username'");
			$countuser = $db->loadResult();
			if($countuser > 0){
				OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=company_register&Itemid=".$itemid),Text::_('OS_USER_IS_ALREADY_EXISTS'));
			}
            // Prepare the data for the user object.
            $data['username']	= $username;
            $data['email']		= $email;
            $data['email2']		= $email;
            $data['password']	= $jinput->getString('password','');
            $data['password2']	= $jinput->getString('password2','');
            $data['name']		= $jinput->getString('name','');

            $registerReturn     = OSPHelper::registration($data,1);
            $msg                = $registerReturn[0]->message;
            $user               = $registerReturn[0]->user;
            //login
            $data['return_url'] = "";
            OSPHelper::login($data);
		}//end check user_id > 0 
		else
		{
			$already_registered = 1;
		}

		jimport('joomla.filesystem.file');
		$post = $jinput->post->getArray();
		if(is_uploaded_file($_FILES['image']['tmp_name']))
		{
			//checking file
			if(!HelperOspropertyCommon::checkIsPhotoFileUploaded('image'))
			{
				//return to previous page
				$msg[] = Text::_('OS_WRONG_PHOTO_EXTENSION');
				//OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_register&Itemid='.$itemid),$msg);
			}
			else
			{ //upload file
                $post['photo'] = OSPHelper::uploadAndResizePicture($_FILES['image'],"company",$post['photo']);
			}
		}
		// store data
		$row = Table::getInstance('Companies','OspropertyTable');
		$row->bind($post);
		$row->user_id = $user->id;	
		if($configClass['auto_approval_company_register_request'] == 1)
		{
			$row->published = 1;
			$row->request_to_approval = 0;
		}
		else
		{
			$row->published = 0;
			$row->request_to_approval = 1;
		}
		$row->company_alias = (string) $row->company_alias;
		$row->check();
		$msg[] = Text::_('OS_YOUR_COMPANY_INFORMATION_HAVE_BEEN_STORED');
	 	if (!$row->store()) 
		{
			throw new Exception($row->getError(), 500);
		}
		
		$id = $db->insertID();
		if($configClass['auto_approval_company_register_request'] == 0){
			//send the notification to administrator
			OspropertyEmail::sendCompanyRequestApproval($id);
		}
		$company_alias = OSPHelper::generateAlias('company',$id,'');
		$db->setQuery("Update #__osrs_companies set company_alias = '$company_alias' where id = '$id'");
		$db->execute();
		
		if(intval($configClass['company_joomla_group_id']) > 0)
		{
			$user_id = $user->id;
			$db->setQuery("Select count(user_id) from #__user_usergroup_map where user_id = '$user_id' and group_id = '".$configClass['company_joomla_group_id']."'");
			$count = $db->loadResult();
			if($count == 0){
				$db->setQuery("Insert into #__user_usergroup_map (user_id,group_id) values ('$user_id','".$configClass['company_joomla_group_id']."')");
				$db->execute();
			}
		}

		if($configClass['auto_approval_company_register_request'] == 1)
		{
            $params = ComponentHelper::getParams('com_users');
            $useractivation = $params->get('useractivation');
            $needs = array();
            $needs[] = "ccompanydetails";
            $needs[] = "company_edit";
            $itemid = OSPRoute::getItemid($needs);
			if ($already_registered == 1){
                for($i=0;$i<count($msg);$i++){
                    $msg[$i] = "<i class='osicon-ok'></i>&nbsp;".$msg[$i];
                }
                $msg = implode("<BR />",$msg);
				OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_edit&Itemid='.$itemid),$msg);
			}
            if($useractivation == 0) {
                for($i=0;$i<count($msg);$i++){
                    $msg[$i] = "<i class='osicon-ok'></i>&nbsp;".$msg[$i];
                }
                $msg = implode("<BR />",$msg);
                OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_edit&Itemid='.$itemid),$msg);
            }else{
				for($i=0;$i<count($msg);$i++){
                    $msg[$i] = "<i class='osicon-ok'></i>&nbsp;".$msg[$i];
                }
                $msg = implode("<BR />",$msg);
                $redirect_url = $configClass['company_redirect_link'];
				if($redirect_url == ""){
					$redirect_url = Uri::root();
				}
				OSPHelper::redirect($redirect_url,$msg);
			}
		}else{
			$msg[] = Text::_('OS_ADMIN_WILL_REVIEW_AND_APPROVE_YOUR_COMPANY_ASAP');
            for($i=0;$i<count($msg);$i++){
                $msg[$i] = "<i class='osicon-ok'></i>&nbsp;".$msg[$i];
            }
            $msg = implode("<BR />",$msg);
			$redirect_url = $configClass['company_redirect_link'];
			if($redirect_url == ""){
				$redirect_url = Uri::root();
			}
			OSPHelper::redirect($redirect_url,$msg);
		}
	}
	
	/**
	 * My Plans
	 * @param unknown_type $option
	 */
	static function myPlans(){
		global $mainframe,$jinput,$configClass;
		if(!HelperOspropertyCommon::isCompanyAdmin()){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		if($configClass['integrate_membership'] == 1){
			if(file_exists(JPATH_ROOT.DS."components".DS."com_osmembership".DS."helper".DS."helper.php")){
				include_once(JPATH_ROOT.DS."components".DS."com_osmembership".DS."helper".DS."helper.php");
			}
            $agentAcc = OspropertyMembership::getUserCredit();
            HTML_OspropertyCompany::listCompanyPlans($agentAcc);
		}else{
			throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
		}
	}

	/**
	* Orders History
	**/
	static function ordershistory($option){
		global $mainframe,$jinput,$configClass;
		$db = Factory::getDbo();
		if(!HelperOspropertyCommon::isCompanyAdmin()){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		$company_id = HelperOspropertyCommon::getCompanyId();

		OSPHelper::generateHeading(2,Text::_('OS_MY_ORDERS_HISTORY'));
				
		HTML_OspropertyCompany::generateNav("company_ordershistory");

		$db->setQuery("Select * from #__osrs_orders where agent_id = '$company_id' and created_by = '1' order by created_on desc");
		$orders = $db->loadObjectList();
		OspropertyPayment::ordersHistory($orders);
	}
}
?>
