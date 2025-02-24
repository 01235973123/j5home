<?php

/*------------------------------------------------------------------------
# agent.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
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
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Captcha\Captcha;
use Joomla\Registry\Registry;
use Joomla\CMS\Plugin\PluginHelper;

class OspropertyAgent
{
	/**
	 * Display Default agent layout
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task)
    {
		global $mainframe,$jinput,$configClass;
		$document = Factory::getDocument();
		$cid = $jinput->get('cid',array(),'ARRAY');
		$cid = ArrayHelper::toInteger($cid);
		$id = $jinput->getInt('id');
		$show_top_menus_in = $configClass['show_top_menus_in'];
		$show_top_menus_in = explode("|",$show_top_menus_in);
		if(in_array('agent',$show_top_menus_in))
		{
			echo HelperOspropertyCommon::buildToolbar('agent');
		}
		switch ($task){
			case "agent_listing":
			case "agent_layout":
				OspropertyAgent::agentLayout($option);
			break;
			case "agent_details":
			case "agent_info":
				OspropertyAgent::agentInfo($option,$id);
			break;
			default:
			case "agent_default":
			case "agent_editprofile":
				OspropertyAgent::editProfile($option);
			break;
			case "agent_saveprofile":
				OspropertyAgent::saveProfile($option);
			break;
			case "agent_saveaccount":
				OspropertyAgent::saveAccount($option);
			break;
			case "agent_savepassword":
				OspropertyAgent::savePassword($option);
			break;
			case "agent_editproperty":
				OspropertyAgent::editProperties($option,$cid[0]);
			break;
			case "agent_publishproperties":
				OspropertyListing::propertyChange($option,$cid,1);
			break;
			case "agent_unpublishproperties":
				OspropertyListing::propertyChange($option,$cid,0);
			break;
			case "agent_deleteproperties":
				OspropertyListing::deleteProperties($option,$cid);
			break;
			case "agent_submitcontact":
				OspropertyAgent::submitContact($option,$id);
			break;
			case "agent_requestapproval":
				OspropertyListing::requestApproval($option,$cid);
			break;
			case "agent_register":
				OspropertyAgent::agentRegister($option);
			break;
			case "agent_completeregistration":
				OspropertyAgent::completeRegistration($option);
			break;
            case "agent_testlogin":
                OspropertyAgent::testLogin($option);
            break;
		}
		HelperOspropertyCommon::loadFooter($option);
	}
	
	
	static function insertDB($option)
    {
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		$state = "";
		$stateArr = explode("\n",$state);
		for($i=0;$i<count($stateArr);$i++)
		{
			$state = $stateArr[$i];
			$db->setQuery("INSERT INTO #__osrs_states values (null,66,'$state','$state',0)");
			$db->execute();
		}
	}

	/**
	 * Agent Layout
	 * Show the search and list agents
	 * @param unknown_type $option
	 */
	static function agentLayout($option)
    {
		global $mainframe,$jinput,$configClass,$lang_suffix;
		OSPHelper::generateHeading(1,$configClass['general_bussiness_name']." - ".Text::_('OS_LIST_AGENTS'));
		$db                     = Factory::getDbo();
		$limit                  = $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
		$limitstart	            = OSPHelper::getLimitStart();
        $show_agent_with_properties = $configClass['show_agent_with_properties'];
		$state                  = $jinput->getInt('state',0);
		$agenttype              = $jinput->getInt('usertype',-1);
		$default_sortby         = OSPHelper::getStringRequest('default_sortby','a.ordering');
		$default_orderby        = OSPHelper::getStringRequest('default_orderby','asc');
		$keyword				= OSPHelper::getStringRequest('keyword','');
		$keyword				= $db->escape($keyword);
		$general_default_agents_sort = $default_sortby;
		$general_default_agents_order = $default_orderby;
		$connector				= ($configClass['joomlauser'] == 1) ? 'left' : 'inner';
		$query                  = "Select count(a.id) from #__osrs_agents as a ".$connector." join #__users as b on b.id = a.user_id ";
		if ($show_agent_with_properties == 1)
        {
            $query              .= " inner join #__osrs_properties as c on a.id = c.agent_id";
        }
		$query                  .= " where a.published = '1'";
        if($configClass['use_privacy_policy'] == 1)
        {
            $query              .= " and a.optin= '0'";
        }
        if ($show_agent_with_properties == 1)
        {
            $query              .= " and c.approved = '1' and c.published = '1'";
        }
		if($keyword != "")
		{
			$query      .= " and a.name like '%".$keyword."%'";
		}
	
		$db->setQuery($query);
		$total                  = $db->loadResult();

		if($configClass['overrides_pagination'] == 1)
		{
			$pageNav            = new OSPJPagination($total,$limitstart,$limit);
		}
		else
		{
			$pageNav            = new Pagination($total,$limitstart,$limit);
		}
        if($configClass['use_privacy_policy'] == 1)
        {
            $opinSql            = " and a.optin= '0'";
        }
        else
        {
            $opinSql            = "";
        }
		$connector				= ($configClass['joomlauser'] == 1) ? 'left' : 'inner';
		$query                  = "Select a.* from #__osrs_agents as a ".$connector." join #__users as b on b.id = a.user_id " ;
        if ($show_agent_with_properties == 1)
        {
            $query              .= " inner join #__osrs_properties as c on a.id = c.agent_id";
        }
		$query                  .= " where a.published = '1' ".$opinSql;
        if ($show_agent_with_properties == 1)
        {
            $query              .= " and c.approved = '1' and c.published = '1'";
        }
		if($keyword != "")
		{
			$query      .= " and a.name like '%".$keyword."%'";
		}
		$query                  .= " group by a.id order by ".$general_default_agents_sort." ".$general_default_agents_order;
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows                   = $db->loadObjectList();
		
		if(count($rows) > 0)
		{
			$access             = ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
			for($i=0;$i<count($rows);$i++)
			{
				$row            = $rows[$i];
				$db->setQuery("Select count(id) from #__osrs_properties where agent_id = '$row->id' and approved = '1' and published = '1' ".$access);
				$countlisting   = $db->loadResult();
				$row->countlisting = intval($countlisting);
				
				$db->setQuery("Select state_name$lang_suffix as state_name from #__osrs_states where id = '$row->state'");
				$row->state_name = $db->loadResult();
				$db->setQuery("Select country_name from #__osrs_countries where id = '$row->country'");
				$row->country_name = $db->loadResult();
			}
		}
		HTML_OspropertyAgent::agentLayout($option,$rows,$pageNav,$lists);
	}

	
	/**
	 * Agent Info
	 * Show the details of one agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function agentInfo($option,$id)
	{
		global $mainframe,$jinput,$configClass,$lang_suffix;
		$db                 = Factory::getDbo();

		OSPHelper::loadTooltip();
		if(intval($id) == 0){
			throw new Exception(Text::_('OS_AGENT_NOT_AVAILABLE'), 404);
		}
		$db->setQuery("Select * from #__osrs_agents where id = '$id'");
		$agent              = $db->loadObject();

		$itemid             = OSPRoute::getAgentItemid($id);
		if($agent->published == 0){
			throw new Exception(Text::_('OS_AGENT_NOT_AVAILABLE'), 404);
		}
        $session            = Factory::getSession();
        $session->set('agent_id',$id);
		//pathway
		$pathway            = $mainframe->getPathway();
		$pathway->addItem(Text::_('OS_AGENT'),Route::_('index.php?option=com_osproperty&view=lagents&Itemid='.$itemid));
		$pathway->addItem($agent->name,Route::_('index.php?option=com_osproperty&task=agent_info&id='.$agent->id.'&Itemid='.$jinput->getInt('Itemid',0)));
		
		$document           = Factory::getDocument();
		$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_AGENT_DETAILS')." - ".$agent->name);
		$db->setQuery("Select id,company_name,photo from #__osrs_companies where id = '$agent->company_id'");
		$company            = $db->loadObject();
		$agent->company_name = $company->company_name;
		$agent->company_photo = $company->photo;
		if($agent->company_photo != "")
		{
			$agent->company_photo = Uri::root()."images/osproperty/company/thumbnail/".$agent->company_photo;
		}else{
			$agent->company_photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
		}
		$db->setQuery("Select state_name$lang_suffix as state_name from #__osrs_states where id = '$agent->state'");
		$agent->state_name = $db->loadResult();
		$db->setQuery("Select country_name from #__osrs_countries where id = '$agent->country'");
		$agent->country_name = $db->loadResult();
		
		$db->setQuery("Select count(id) from #__osrs_properties where agent_id = '$id' and published = '1' and approved = '1'");
		$countpro           = $db->loadResult();
		$lists['countpro']  = $countpro;
		if($countpro > 0)
		{ // have the properties
			$db->setQuery("Select id,pro_name$lang_suffix,hits from #__osrs_properties where hits > 0  and agent_id = '$id' and published = '1' and approved = '1' order by hits desc limit 5");
			$lists['mostview'] = $db->loadObjectList();
			
			$db->setQuery("Select id,pro_name$lang_suffix,(total_points/number_votes) as rated from #__osrs_properties where agent_id = '$id' and published = '1' and approved = '1' and number_votes > 0 order by (total_points/number_votes) desc limit 5");
			$lists['mostrate'] = $db->loadObjectList();
		}
		HTML_OspropertyAgent::agentInfoForm($option,$agent,$lists);
	}
	
	/**
	 * Edit profile layout
	 *
	 * @param unknown_type $option
	 */
	static function editProfile($option){
		global $mainframe,$jinput,$configClass,$languages;
		$config         = new JConfig();
		$translatable   = Multilanguage::isEnabled() && count($languages);
		$sef			= OSPHelper::getFieldSuffix();

		$list_limit     = $config->list_limit;
		HTMLHelper::_('behavior.keepalive');
		$db             = Factory::getDBO();
		$user           = Factory::getUser();
		if($user->id == 0){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		if(!HelperOspropertyCommon::isRegisteredAgent()){
			$agent_id   = OSPHelper::registerNewAgent($user,$configClass['default_user_type']);
		}
		$document       = Factory::getDocument();
		$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_EDIT_MY_PROFILE'));

		$limit			= $jinput->getInt('limit',$list_limit);
		$limitstart     = OSPHelper::getLimitStartPost();

		$db->setQuery("Select * from #__osrs_agents where user_id = '$user->id'");
		$agent = $db->loadObject();

		$category_id    = $jinput->getInt('category_id',0);
		$catIds 	    = array();
		$catIds[]	    = $category_id;
		$type_id        = $jinput->getInt('type_id',0);
		$status         = $jinput->getString('status','');
        $featured       = $jinput->getInt('featured_stt',-1);
        $approved       = $jinput->getInt('approved',-1);
		//country

		$lists['country'] = HelperOspropertyCommon::makeCountryList($agent->country,'country','onChange="javascript:loadState(this.value,\''.$agent->state.'\',\''.$agent->city.'\')"',Text::_('OS_SELECT_COUNTRY'),'','input-large form-select');

		if(OSPHelper::userOneState()){
			$lists['state'] = "<input type='hidden' name='state' id='state' value='".$agent->state."'/>";
		}else{
			$lists['state'] = HelperOspropertyCommon::makeStateListAddProperty($agent->country,$agent->state,'state','onChange="javascript:loadCity(this.value,\''.$agent->city.'\')"',Text::_('OS_SELECT_STATE'),'class="input-large form-select"');
		}
		if(intval($agent->state) == 0){
			$agent->state = OSPHelper::returnDefaultState();
		}
		$lists['city']  = HelperOspropertyCommon::loadCityAddProperty($option,$agent->state,$agent->city,"input-large form-select");

		$keyword        = OSPHelper::getStringRequest('filter_search','','post');
		$orderby        = OSPHelper::getStringRequest('orderby','desc','post');
		$sortby         = OSPHelper::getStringRequest('sortby','a.id','post');

		$query = "Select count(a.id) from #__osrs_properties as a"
				." INNER JOIN #__osrs_agents as g on g.id = a.agent_id"
				." LEFT  JOIN #__osrs_types as d on d.id = a.pro_type"
				." INNER JOIN #__osrs_countries as e on e.id = a.country"
				." LEFT JOIN #__osrs_states as s on s.id = a.state"
				." LEFT JOIN #__osrs_cities as c on c.id = a.city"
				." LEFT join #__osrs_expired as ex on ex.pid = a.id"
				." WHERE a.agent_id = '$agent->id'";
		if($keyword != ""){
			$query .= " AND (a.pro_name LIKE '%$keyword%'";
			$query .= " OR a.ref like '%$keyword%'";
			$query .= " OR g.name like '%$keyword%'";
			$query .= " OR d.type_name like '%$keyword%'";
			$query .= " OR s.state_name like '%$keyword%'";
			$query .= " OR c.city like '%$keyword%'";
			$query .= ")";
		}
		if($category_id > 0){
			$query .= " AND a.id in (Select pid from #__osrs_property_categories where category_id = '$category_id')";
		}
		if($type_id > 0){
			$query .= " AND a.pro_type = '$type_id'";
		}
		if($status != ""){
			$query .= " AND a.published = '$status'";
		}
        if($featured > -1){
            $query .= " AND a.isFeatured = '$featured'";
        }
        if($approved > -1){
            $query .= " AND a.approved = '$approved'";
        }
		$db->setQuery($query);
		$count = $db->loadResult();
		
		$pageNav = new OSPJPagination($count,$limitstart,$limit);
		if($translatable)
		{
			$query = "Select a.id, a.ref, a.pro_name".$sef.",a.posted_by,a.company_id, d.id as typeid,d.type_name".$sef." as type_name,g.name as agent_name,a.published,a.approved, a.isFeatured,a.curr,a.price,a.price_call,a.rent_time,a.show_address,a.hits,c.city".$sef." as city,s.state_name".$sef." as state_name,a.address, ex.expired_time,ex.expired_feature_time,a.total_request_info,a.total_points,a.number_votes,a.agent_id from #__osrs_properties as a"
					." INNER JOIN #__osrs_agents as g on g.id = a.agent_id"
					." LEFT  JOIN #__osrs_types as d on d.id = a.pro_type"
					." INNER JOIN #__osrs_countries as e on e.id = a.country"
					." LEFT JOIN #__osrs_states as s on s.id = a.state"
					." LEFT JOIN #__osrs_cities as c on c.id = a.city"
					." LEFT JOIN #__osrs_expired as ex on ex.pid = a.id"
					." WHERE a.agent_id = '$agent->id'";
		}
		else
		{
			$query = "Select a.id, a.ref, a.pro_name,a.posted_by,a.company_id, d.id as typeid,d.type_name as type_name,g.name as agent_name,a.published,a.approved, a.isFeatured,a.curr,a.price,a.price_call,a.rent_time,a.show_address,a.hits,c.city,s.state_name as state_name,a.address, ex.expired_time,ex.expired_feature_time,a.total_request_info,a.total_points,a.number_votes,a.agent_id from #__osrs_properties as a"
					." INNER JOIN #__osrs_agents as g on g.id = a.agent_id"
					." LEFT  JOIN #__osrs_types as d on d.id = a.pro_type"
					." INNER JOIN #__osrs_countries as e on e.id = a.country"
					." LEFT JOIN #__osrs_states as s on s.id = a.state"
					." LEFT JOIN #__osrs_cities as c on c.id = a.city"
					." LEFT JOIN #__osrs_expired as ex on ex.pid = a.id"
					." WHERE a.agent_id = '$agent->id'";
		}
		if($keyword != ""){
			$query .= " AND (a.pro_name LIKE '%$keyword%'";
			$query .= " OR a.ref like '%$keyword%'";
			$query .= " OR g.name like '%$keyword%'";
			$query .= " OR d.type_name like '%$keyword%'";
			$query .= " OR s.state_name like '%$keyword%'";
			$query .= " OR c.city like '%$keyword%'";
			$query .= ")";
		}
		if($category_id > 0){
			$query .= " AND a.id in (Select pid from #__osrs_property_categories where category_id = '$category_id')";
		}
		if($type_id > 0){
			$query .= " AND a.pro_type = '$type_id'";
		}
		if($status != ""){
			$query .= " AND a.published = '$status'";
		}
        if($featured > -1){
            $query .= " AND a.isFeatured = '$featured'";
        }
        if($approved > -1){
            $query .= " AND a.approved = '$approved'";
        }
		$query .= " ORDER BY $sortby $orderby";
		
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		//echo $db->getQuery();
		$rows = $db->loadObjectList();
		if(count($rows) > 0)
		{
			for($i=0;$i<count($rows);$i++)
			{
				$row = $rows[$i];
				$db->setQuery("select count(id) from #__osrs_photos where pro_id = '$row->id'");
				$count = $db->loadResult();
				if($count > 0)
				{
					$row->count_photo = $count;
					$db->setQuery("select image from #__osrs_photos where pro_id = '$row->id' order by ordering");	
					$photo = $db->loadResult();
					if($photo != "")
					{
						if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/thumb/'.$photo))
						{
							$row->photo = Uri::root(true)."/images/osproperty/properties/".$row->id."/thumb/".$photo;
						}
						else
						{
							$row->photo = Uri::root(true)."/media/com_osproperty/assets/images/nopropertyphoto.png";
						}
					}else{
						$row->photo = Uri::root(true)."/media/com_osproperty/assets/images/nopropertyphoto.png";
					}
				}
				else
				{
					$row->count_photo = 0;
					$row->photo = Uri::root(true)."/media/com_osproperty/assets/images/nopropertyphoto.png";
				}//end photo
			}
		}

		if($configClass['active_payment'] == 1)
		{
			$db->setQuery("Select * from #__osrs_orders where agent_id = '$agent->id' and created_by = '0' order by created_on desc");
			$orders = $db->loadObjectList();
			$lists['orders'] = $orders;
		}
		
		$db->setQuery("Select count(id) from #__osrs_properties where agent_id = '$agent->id' and published = '1' and approved = '1'");
		$countpro = $db->loadResult();
		$lists['countpro'] = $countpro;
		if($countpro > 0)
		{ // have the properties
			$db->setQuery("Select id,pro_name,hits from #__osrs_properties where hits > 0  and agent_id = '$agent->id' and published = '1' and approved = '1' order by hits desc limit 5");
			$lists['mostview'] = $db->loadObjectList();
			
			$db->setQuery("Select id,pro_name,(total_points/number_votes) as rated from #__osrs_properties where agent_id = '$agent->id' and published = '1' and approved = '1' and number_votes > 0 order by (total_points/number_votes) desc limit 5");
			$lists['mostrate'] = $db->loadObjectList();
		}
		
		$orderbyArr[] = HTMLHelper::_('select.option','',Text::_('OS_ORDERBY'));
		$orderbyArr[] = HTMLHelper::_('select.option','asc',Text::_('OS_ASC'));
		$orderbyArr[] = HTMLHelper::_('select.option','desc',Text::_('OS_DESC'));
		$lists['orderby'] = HTMLHelper::_('select.genericlist',$orderbyArr,'orderby','class="input-medium form-select" onchange="javascript:this.form.submit();"','value','text',$orderby);
		
		$sortbyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SORTBY'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.ref',Text::_('Ref #'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.pro_name',Text::_('OS_TITLE'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.address',Text::_('OS_ADDRESS'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.state',Text::_('OS_STATE'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.city',Text::_('OS_CITY'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.published',Text::_('OS_PUBLISHED'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.isFeatured',Text::_('OS_FEATURED'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.id',Text::_('ID'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.price',Text::_('OS_PRICE'));
		$lists['sortby'] = HTMLHelper::_('select.genericlist',$sortbyArr,'sortby','class="input-medium form-select" onchange="javascript:this.form.submit();"','value','text',$sortby);
		
		$lists['category'] = OSPHelper::listCategories($category_id,'onChange="this.form.submit();"');
		
		//property types
		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_PROPERTY_TYPES'));
		if($translatable)
		{
			$db->setQuery("SELECT id as value,type_name".$sef." as text FROM #__osrs_types where published = '1' ORDER BY type_name");
		}
		else
		{
			$db->setQuery("SELECT id as value,type_name as text FROM #__osrs_types where published = '1' ORDER BY type_name");
		}
		$protypes = $db->loadObjectList();
		$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'type_id','class="input-large" onChange="this.form.submit();"','value','text',$type_id);
		
		$statusArr = array();
		$statusArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_STATUS'));
		$statusArr[] = HTMLHelper::_('select.option',0,Text::_('OS_UNPUBLISHED'));
		$statusArr[] = HTMLHelper::_('select.option',1,Text::_('OS_PUBLISHED'));
		$lists['status'] = HTMLHelper::_('select.genericlist',$statusArr,'status','class="input-medium form-select" onChange="this.form.submit();"','value','text',$status);

		$optArr = array();
        $optArr[] = HTMLHelper::_('select.option',0,Text::_('OS_YES'));
        $optArr[] = HTMLHelper::_('select.option',1,Text::_('OS_NO'));
        $lists['optin'] = HTMLHelper::_('select.genericlist',$optArr,'optin','class="input-medium form-select"','value','text',(int)$agent->optin);

        $featuredArr = array();
        $featuredArr[] = HTMLHelper::_('select.option','-1',Text::_('OS_FEATURED_STATUS'));
        $featuredArr[] = HTMLHelper::_('select.option','0',Text::_('OS_NON_FEATURED_PROPERTIES'));
        $featuredArr[] = HTMLHelper::_('select.option','1',Text::_('OS_FEATURED_PROPERTIES'));
        $lists['featured'] = HTMLHelper::_('select.genericlist',$featuredArr,'featured_stt','class="input-medium form-select" onChange="this.form.submit();"','value','text',$featured);

        $approvedArr = array();
        $approvedArr[] = HTMLHelper::_('select.option','-1',Text::_('OS_APPROVAL_STATUS'));
        $approvedArr[] = HTMLHelper::_('select.option','0',Text::_('OS_UNAPPROVED'));
        $approvedArr[] = HTMLHelper::_('select.option','1',Text::_('OS_APPROVED'));
        $lists['approved'] = HTMLHelper::_('select.genericlist',$approvedArr,'approved','class="input-medium form-select" onChange="this.form.submit();"','value','text',$approved);
		
		$db->setQuery("select id as value, company_name as text from #__osrs_companies where published = '1' order by company_name");
		$companies 	  = $db->loadObjectList();
		$companyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_COMPANY'));
		$companyArr   = array_merge($companyArr,$companies);
		$lists['company'] = HTMLHelper::_('select.genericlist',$companyArr,'company_id','class="input-large form-select"','value','text',$agent->company_id);
		
		HTML_OspropertyAgent::editProfile($option,$agent,$lists,$rows,$pageNav);
	}
	
	/**
	 * Save profile
	 *
	 * @param unknown_type $option
	 */
	static function saveProfile($option){
		global $mainframe,$jinput;
		$db = Factory::getDBO();
		if(!HelperOspropertyCommon::isAgent()){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		$msg = Text::_('OS_YOUR_PROFILE_HAS_BEEN_SAVED');
		$user = Factory::getUser();
		
		$post = $jinput->post->getArray();
		$post['name']		= OSPHelper::getStringRequest('name', '', 'post');
        $post['username']	= $jinput->getString('username', '');
        $post['password']	= $jinput->getString('password', '');
        $post['password2']	= $jinput->getString('password2', '');
        $post['email']		= $jinput->getString('email', '');
		if (!$user->bind($post)) {
			$msg = $user->getError();
		}
		if (!$user->save()) {
			$msg = $user->getError();
		}
		$needs = array();
		$needs[] = "aeditdetails";
		$needs[] = "agent_default";
		$needs[] = "agent_editprofile";
		$itemid = OSPRoute::getItemid($needs);
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_default&Itemid=".$itemid),$msg);
	}
	
	/**
	 * Save Account
	 *
	 * @param unknown_type $option
	 */
	static function saveAccount($option){
		global $mainframe,$jinput,$configClass,$languages;
		$db = Factory::getDBO();
		jimport('joomla.filesystem.file');
		if(!HelperOspropertyCommon::isAgent()){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		
		$user = Factory::getUser();
		$db->setQuery("Select id from #__osrs_agents where user_id = '$user->id'");
		$agent_id = $db->loadResult();
		
		$row = Table::getInstance('Agent','OspropertyTable');
		$post = $jinput->post->getArray();
		$row->bind($post);
		$row->bio = $_POST['bio'];
		$row->id = $agent_id;
		//store into database
		if (!$row->store()) 
		{
			throw new Exception($row->getError(), 500);
		}
		
		//update for other languages
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language) {	
				$sef = $language->sef;
				$bio_language = $row->bio;
				if($bio_language != ""){
					$row = Table::getInstance('Agent','OspropertyTable');
					$row->id = $agent_id;
					$row->{'bio_'.$sef} = $bio_language;
					$row->store();
				}
			}
		}
		
		$remove_photo = $jinput->getInt('remove_photo',0);
		
		if($configClass['show_agent_image'] == 1){
			if(is_uploaded_file($_FILES['photo']['tmp_name'])){
				if(!HelperOspropertyCommon::checkIsPhotoFileUploaded('photo')){
					$needs = array();
					$needs[] = "agent_editprofile";
					$needs[] = "agent_default";
					$needs[] = "aeditdetails";
					$itemid = OSPRoute::getItemid($needs);
					OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_editprofile&Itemid=".$itemid),Text::_('OS_ONLY_SUPPORT_JPG_IMAGES'));
				}
                $filename = OSPHelper::uploadAndResizePicture($_FILES['photo'],"agent","");
				//save into db
				$db->setQuery("UPDATE #__osrs_agents SET photo = '$filename' WHERE id = '$agent_id'");
				$db->execute();
			}elseif($remove_photo == 1){
				HelperOspropertyCommon::removePhoto($agent_id,2);
				$db->setQuery("UPDATE #__osrs_agents SET photo = '' WHERE id = '$agent_id'");
				$db->execute();
			}
		}
		
		$alias = OSPHelper::getStringRequest('alias','','post');
		$agent_alias = OSPHelper::generateAlias('agent',$agent_id,$alias);
		$db->setQuery("Update #__osrs_agents set alias = '$agent_alias' where id = '$agent_id'");
		$db->execute();
		
		if(intval($row->company_id) > 0){
			$db->setQuery("SELECT COUNT(id) FROM #__osrs_company_agents where agent_id = '$agent_id' AND company_id = '$row->company_id'");
			$count = $db->loadResult();
			if($count == 0){
				$db->setQuery("INSERT INTO #__osrs_company_agents (id, company_id,agent_id) VALUES (NULL,'$row->company_id','$agent_id')");
				$db->execute();
			}
		}else{
			$db->setQuery("DELETE FROM #__osrs_company_agents WHERE agent_id = '$agent_id'");
			$db->execute();
		}
		$needs = array();
		$needs[] = "agent_editprofile";
		$needs[] = "agent_default";
		$needs[] = "aeditdetails";
		$itemid = OSPRoute::getItemid($needs);
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_default&Itemid=".$itemid),Text::_('OS_YOUR_ACCOUNT_HAS_BEEN_SAVED'));
	}
	
	/**
	 * Save Password
	 *
	 * @param unknown_type $option
	 */
	static function savePassword($option){
		global $mainframe,$jinput;
		$db = Factory::getDBO();
		$user = Factory::getUser();
		$user_id = $user->id;
		$password = $jinput->getString('password', '');
		$post = $jinput->post->getArray();
		$post['password'] = $password;
		$post['password_clear'] = $password;
		if (!$user->bind($post)){
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}
		$user->id = $user_id;
		//print_r($user);
		if ( !$user->save() )
		{	
			throw new Exception(Text::_( $user->getError()), 500);

			return false;
		}
		$needs = array();
		$needs[] = "agent_editprofile";
		$needs[] = "agent_default";
		$needs[] = "aeditdetails";
		$itemid = OSPRoute::getItemid($needs);
		OSPHelper::redirect(Route::_("index.php?option=$option&task=agent_default&Itemid=".$itemid),Text::_("New password has been saved"));
	}
	
	
	/**
	 * Show agent listing
	 *
	 * @param unknown_type $option
	 */
	static function agentListing($option){
		global $mainframe,$jinput,$configClass,$lang_suffix;
		$config = new JConfig();
		$list_limit = $config->list_limit;
		$db = Factory::getDBO();
		//check to see if this is agent
		
		if(!HelperOspropertyCommon::isAgent()){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		$document = Factory::getDocument();
		$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('My properties'));
		$user = Factory::getUser();
		//get agent id
		$db->setQuery("Select id from #__osrs_agents where user_id = '$user->id'");
		$agent_id = $db->loadResult();
		
		$limit = $jinput->getInt('limit',$list_limit);
		$limitstart = $jinput->getInt('limitstart',0);
		$orderby = OSPHelper::getStringRequest('orderby','a.created');
		$ordertype = OSPHelper::getStringRequest('ordertype','desc');
		$query = "Select count(a.id) from #__osrs_properties as a"
				." LEFT JOIN #__osrs_types as d on d.id = a.pro_type"
				." INNER JOIN #__osrs_countries as e on e.id = a.country"
				." WHERE a.agent_id = '$agent_id'";
		$db->setQuery($query);
		$total = $db->loadResult();
		$pageNav = new OSPJPagination($total,$limitstart,$limit);
		
		$query = "Select a.*,d.type_name,e.country_name from #__osrs_properties as a"
				." LEFT JOIN #__osrs_types as d on d.id = a.pro_type"
				." INNER JOIN #__osrs_countries as e on e.id = a.country"
				." WHERE a.agent_id = '$agent_id'"
				." ORDER BY $orderby";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		
		if(count($rows) > 0){
			for($i=0;$i<count($rows);$i++){//for
				$row = $rows[$i];
				//process photo
				$db->setQuery("select count(id) from #__osrs_photos where pro_id = '$row->id'");
				$count = $db->loadResult();
				if($count > 0){
					$row->count_photo = $count;
					$db->setQuery("select image from #__osrs_photos where pro_id = '$row->id' order by ordering limit 1");	
					$row->photo = Uri::root()."images/osproperty/properties/thumb/".$db->loadResult();
				}else{
					$row->count_photo = 0;
					$row->photo = Uri::root()."media/com_osproperty/assets/images/noimage.png";
				}//end photo
				
				//get state
				$db->setQuery("Select state_name$lang_suffix as state_name from #__osrs_states where id = '$row->state'");
				$row->state_name = $db->loadResult();
				
				//rating
				if($row->number_votes > 0){
					$points = round($row->total_points/$row->number_votes);
					ob_start();
					?>
					
					<?php
					for($j=1;$j<=$points;$j++){
						?>
						<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/star1.png">
						<?php
					}
					for($j=$points+1;$j<=5;$j++){
						?>
						<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/star2.png">
						<?php
					}
					?>
							
					
					<?php
					$row->rating = ob_get_contents();
					ob_end_clean();
					
				}else{
					ob_start();
					for($j=1;$j<=5;$j++){
						?>
						<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/star2.png">
						<?php
					}
					$row->rating = ob_get_contents();
					ob_end_clean();
				} //end rating
				
				//comments
				$db->setQuery("Select count(id) from #__osrs_comments where pro_id = '$row->id'");
				$ncomment = $db->loadResult();
				if($ncomment > 0){
					$row->comment = $ncomment;
				}else{
					$row->comment = 0;
				}
				
			}//for
		}//if rows > 0
		
		
		$orderbyArr[] = HTMLHelper::_('select.option','',Text::_('Select order by'));
		$orderbyArr[] = HTMLHelper::_('select.option','b.category_name',Text::_('Category name'));
		$orderbyArr[] = HTMLHelper::_('select.option','a.published',Text::_('Status'));
		$orderbyArr[] = HTMLHelper::_('select.option','a.approved',Text::_('Approval'));
		$orderbyArr[] = HTMLHelper::_('select.option','a.publish_down',Text::_('Expired date'));
		$lists['orderby'] = HTMLHelper::_('select.genericlist',$orderbyArr,'orderby','onChange="javascript:document.ftForm.submit()" class="input-small"','value','text',$orderby);
		
		
		$ordertypeArr[] = HTMLHelper::_('select.option','desc',Text::_('Descending'));
		$ordertypeArr[] = HTMLHelper::_('select.option','asc',Text::_('Ascending'));
		$lists['ordertype'] = HTMLHelper::_('select.genericlist',$ordertypeArr,'ordertype','onChange="javascript:document.ftForm.submit()" class="input-small"','value','text',$ordertype);
		
		HTML_OspropertyAgent::agentListing($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * Edit properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function editProperties($option,$id){
		global $mainframe,$jinput;
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_edit&id=$id&Itemid=".$jinput->getInt('Itemid',0)));
	}
	
	
	/**
	 * Submit contact
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function submitContact($option,$id)
	{
		global $mainframe,$jinput,$configClass;
		$db = Factory::getDbo();
		
		if($configClass['show_agent_contact'] == 0){
			$msg = Text::_('OS_THIS_static functionALITY_DOES_NOT_BE_ACTIVATED');
			$itemid = $jinput->getInt('Itemid',0);
			OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_info&id=".$id."&Itemid=".$jinput->getInt('Itemid')),$msg);
		}

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
        $agent_id = $session->get('agent_id',0);
        if(($agent_id == 0) || ($agent_id != $id)){
            $msg = Text::_('OS_SECURITY_CODE_IS_WRONG');
            OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_info&id=".$id."&Itemid=".$jinput->getInt('Itemid')),$msg);
        }

		$date = date("j",time());
		$comment_author = $jinput->getString('comment_author'.$date,'');
		$comment_email = $jinput->getString('comment_email'.$date,'');
		if(($comment_author == "") or ($comment_email == "")){
			$msg = Text::_('OS_EMAIL_CANT_BE_SENT');
			OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_info&id=".$id."&Itemid=".$jinput->getInt('Itemid')),$msg);
		}
		$comment_title  = OSPHelper::getStringRequest('comment_title','','post');
		$message		= $_POST['message'];
		
		$contact['author']  = $comment_author;
		$contact['email']   = $comment_email;
		$contact['title']   = $comment_title;
		$contact['message'] = $message;
		
		//send contact email
		$db->setQuery("Select * from #__osrs_agents where id = '$id'");
		$agent  = $db->loadObject();
		$emailto  = $agent->email;
		$contact['emailto'] = $emailto;
		$receiver =	$agent->name;
		$contact['receiver'] = $receiver;
		
		OspropertyEmail::sendContactEmail($option,$contact);
		
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_info&id=".$id."&Itemid=".$jinput->getInt('Itemid')),Text::_('OS_EMAIL_HAS_BEEN_SENT'));
	}
	
	/**
	 * Agent register
	 *
	 * @param unknown_type $option
	 */
	static function agentRegister($option)
	{
		global $mainframe,$jinput,$configClass,$bootstrapHelper;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		$session = Factory::getSession();
		if($configClass['allow_agent_registration'] == 0){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		if(HelperOspropertyCommon::isCompanyAdmin())
		{
			$needs = array();
			$needs[] = "company_edit";
			$needs[] = "ccompanydetails";
			$itemid = OSPRoute::getItemid($needs);
			$itemid = OSPRoute::confirmItemidArr($itemid,$needs);
			if(!OSPRoute::reCheckItemid($itemid,$needs))
			{
				$itemid = 9999;
			}
			OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_edit&Itemid='.$itemid));
		}
		if(HelperOspropertyCommon::isAgent())
		{
			$needs = array();
			$needs[] = "agent_editprofile";
			$needs[] = "agent_default";
			$needs[] = "aeditdetails";
			$itemid = OSPRoute::getItemid($needs);
			$itemid = OSPRoute::confirmItemidArr($itemid,$needs);
			if(!OSPRoute::reCheckItemid($itemid,$needs)){
				$itemid = 9999;
			}
			OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=agent_editprofile&Itemid='.$itemid));
		}
		OSPHelper::generateHeading(1,$configClass['general_bussiness_name']." - ".Text::_('OS_AGENT_REGISTER'));

		$companyregister= $session->get('companyregister');
		if($companyregister == 1){
			$session->set('post',array());
		}
		$inputLargeClass	= $bootstrapHelper->getClassMapping('input-large');
		$inputMediumClass	= $bootstrapHelper->getClassMapping('input-medium');
		$inputSmallClass	= $bootstrapHelper->getClassMapping('input-small');
		$inputMiniClass		= $bootstrapHelper->getClassMapping('input-mini');
		$menus = Factory::getApplication()->getMenu();
		$menu = $menus->getActive();
		if (is_object($menu)) {
			$params = new Registry() ;
	        //$params->loadString($menu->getParams());
			$params = $menu->getParams();
			$lists['company_id_selected'] = $params->get('company_id',0);
		}

		$post			= $session->get('post');
		$state			= $post['state'];
		$city			= $post['city'];
		$country		= $post['country'];
		$company_id		= $post['company_id'];

		$db->setQuery("select id as value, company_name as text from #__osrs_companies where published = '1' order by company_name");
		$companies 	  = $db->loadObjectList();
		$companyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_COMPANY'));
		$companyArr   = array_merge($companyArr,$companies);
		$lists['company'] = HTMLHelper::_('select.genericlist',$companyArr,'company_id','class="'.$inputLargeClass.'"','value','text',$company_id);

		$lists['country'] = HelperOspropertyCommon::makeCountryList($country,'country','onchange="change_country_company(this.value,0,0)"',Text::_('OS_SELECT_COUNTRY'),'',$inputLargeClass);
		
		if(OSPHelper::userOneState())
		{
			$lists['state'] = "<input type='hidden' name='state' id='state' value='".OSPHelper::returnDefaultState()."'/>".OSPHelper::returnDefaultStateName();
		}
		else
		{
			$lists['state'] = HelperOspropertyCommon::makeStateListAddProperty('',$state,'state','onchange="loadCity(this.value,0)"',Text::_('OS_SELECT_STATE'),'class="'.$inputLargeClass.' form-select"');
		}
		if(OSPHelper::userOneState())
		{
			$default_state = OSPHelper::returnDefaultState();
		}
		else
		{
			$default_state = $state;
		}
		$lists['city'] = HelperOspropertyCommon::loadCity($option,$default_state,$city,$inputLargeClass);
		
		HTML_OspropertyAgent::agentRegisterForm($user,$lists,$companies);
	}
	
	/**
	 * Complete registration
	 *
	 * @param unknown_type $option
	 */
	static function completeRegistration($option)
	{
		global $mainframe,$jinput,$configClass,$languages;
        $msg = array();
		$db = Factory::getDbo();

		OSPHelper::antiSpam();

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
						OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=agent_register&Itemid='.$jinput->getInt('Itemid',0)),Text::_('OS_SECURITY_CODE_IS_WRONG'));
					}
				}
				catch (Exception $e)
				{
					//do the same with case !$res
					OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=agent_register&Itemid='.$jinput->getInt('Itemid',0)),Text::_('OS_SECURITY_CODE_IS_WRONG'));
				}
			}
		}

		//process session
		$session = Factory::getSession();
		$post	 = $jinput->post->getArray();
		$session->set('post',$post);
		$session->set('agentregister',1);
		$session->set('companyregister',0);
		
		$user 		= clone(Factory::getUser());
		$needs = array();
		$needs[] = "aagentregistration";
		$needs[] = "agent_register";
		$itemid = OSPRoute::getItemid($needs);
			
		$userid = $jinput->getInt( 'id', 0, 'post', 'int' );
		$errors = [];
		if(intval($user->id) == 0)
		{
			$email = OSPHelper::getStringRequest('email','','post');
			if($configClass['use_email_as_agent_username'] == 1)
			{
				$username = $email;
			}
			else
			{	
				$username = $jinput->getString('username', $email);
			}

			// Validate username and password
			//if (!$user->id && $config->user_registration)
			//{
			$errors = array_merge($errors, OSPHelper::validateUsername($username));
			$errors = array_merge($errors, OSPHelper::validatePassword($jinput->post->get('password', '', 'raw')));
			if(count($errors))
			{
				OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_register&Itemid=".$itemid),implode("<BR />", $errors));
			}
		
			//}
            
            $db->setQuery("Select count(id) from #__users where email like '$email'");
            $countemail = $db->loadResult();
            if($countemail > 0)
			{
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_register&Itemid=".$itemid),Text::_('OS_EMAIL_IS_ALREADY_EXISTS'));
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
			
			$db->setQuery("Select count(id) from #__users where username like '$username'");
			$countuser = $db->loadResult();
			if($countuser > 0)
			{
				OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_register&Itemid=".$itemid), Text::_('OS_USER_IS_ALREADY_EXISTS'));
			}
			// Prepare the data for the user object.
			$data['username']	= $username;
			$data['email']		= $email;
			$data['email2']		= $email;
            $data['password']	= $jinput->getString('password','');
            $data['password2']	= $jinput->getString('password2','');
            $data['name']		= $jinput->getString('name','');

            $registerReturn		= OSPHelper::registration($data,0);
            $msg = $registerReturn[0]->message;
            $user = $registerReturn[0]->user;
            //login?
            $data['return_url'] = "";
            OSPHelper::login($data);

		}//end check user_id > 0 
		
		// Register new agent account
		$agent_id = OSPHelper::registerNewAgent($user, $jinput->getInt('agent_type',-1));
		
        $emailOpt = array();
		if($configClass['auto_approval_agent_registration'] == 0){
			//send email to admin
			$tmp			= new StdClass();
			$tmp->customer	= $user->name;
			$tmp->agent_id	= $agent_id;
			
			$emailOpt[0]	= $tmps;
			OspropertyEmail::sendAgentApprovalRequest($option,$emailOpt);

			$msg[] = Text::_('OS_THANKYOU_TO_BECOME_AGENT1');
            for($i=0;$i<count($msg);$i++)
			{
                $msg[$i] = "<i class='osicon-ok'></i>&nbsp;".$msg[$i];
            }
            $msg = implode("<BR />",$msg);
			$redirect_url = $configClass['agent_redirect_link'];
			if($redirect_url == "")
			{
				$redirect_url = Uri::root();
			}
			OSPHelper::redirect($redirect_url,$msg);
		}
		else
		{
			$emailOpt['agentid'] = $agent_id;
			$db->setQuery("Select * from #__osrs_agents where id = '$agent_id'");
			$agent = $db->loadObject();
			$emailOpt['agentname'] = $agent->name;
			$emailOpt['agentemail'] = $agent->email;
			OspropertyEmail::sendAgentActiveEmail($option,$emailOpt);
            $msg[] = Text::_('OS_THANKYOU_TO_BECOME_AGENT2');
            for($i=0;$i<count($msg);$i++){
                $msg[$i] = "<i class='osicon-ok'></i>&nbsp;".$msg[$i];
            }
            $msg = implode("<BR />",$msg);
			$redirect_url = $configClass['agent_redirect_link'];
			if($redirect_url == ""){
				$redirect_url = Uri::root();
			}
			OSPHelper::redirect($redirect_url,$msg);
		}
	}
	
	/**
	 * send activation email
	 *
	 * @param unknown_type $user
	 * @param unknown_type $password
	 */
	static function _sendMail(&$user, $password)
	{
		global $mainframe,$jinput;
		$db		=& Factory::getDBO();
		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$usersConfig 	= &ComponentHelper::getParams( 'com_users' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$useractivation = $usersConfig->get( 'useractivation' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= Uri::base();

		$subject 	= sprintf ( Text::_( 'OS_ACCOUNT_DETAILS_FOR' ), $name, $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);

		if ( $useractivation == 1 ){
			$message = sprintf ( Text::_( 'OS_COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY' ), $name, $sitename, $siteURL."index.php?option=com_user&task=activate&activation=".$user->get('activation'), $siteURL, $username, $password);
		} else {
			$message = sprintf ( Text::_( 'OS_COM_USERS_EMAIL_REGISTERED_BODY_NOPW' ), $name, $sitename, $siteURL);
		}

		$message = html_entity_decode($message, ENT_QUOTES);

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}
		$mailer = Factory::getMailer();
		try
		{
			$mailer->sendMail($mailfrom, $fromname, $email, $subject, $message);
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		// Send notification to all administrators
		$subject2 = sprintf ( Text::_( 'OS_ACCOUNT_DETAILS_FOR' ), $name, $sitename);
		$subject2 = html_entity_decode($subject2, ENT_QUOTES);

		// get superadministrators id
		foreach ( $rows as $row )
		{
			if ($row->sendEmail)
			{
				$message2 = sprintf ( Text::_( 'OS_COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_BODY' ), $row->name, $sitename, $name, $email, $username);
				$message2 = html_entity_decode($message2, ENT_QUOTES);
				$mailer = Factory::getMailer();
				try
				{
					$mailer->sendMail($mailfrom, $fromname, $row->email, $subject2, $message2);
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
			}
		}
	}

    static function testLogin($option){
        $data = array();
        $data['username'] = "agent";
        $data['password'] = "agent";
        $data['return']   = Uri::root();
        OSPHelper::login($data);
    }
}

?>
