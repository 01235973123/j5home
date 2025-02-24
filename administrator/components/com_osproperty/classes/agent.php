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
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Form\FormHelper;


define('PATH_STORE_PHOTO_AGENT_FULL',JPATH_ROOT."/images/osproperty/agent");
define('PATH_STORE_PHOTO_AGENT_THUMB',PATH_STORE_PHOTO_AGENT_FULL.'/thumbnail');
define('PATH_URL_PHOTO_AGENT_FULL',str_replace(DS,'/',str_replace(JPATH_SITE,Uri::root(),PATH_STORE_PHOTO_AGENT_FULL)).'/');
define('PATH_URL_PHOTO_AGENT_THUMB',str_replace(DS,'/',str_replace(JPATH_SITE,Uri::root(),PATH_STORE_PHOTO_AGENT_THUMB)).'/');


class OspropertyAgent{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $jinput, $mainframe;
		//HTMLHelper::_('behavior.modal','a.osmdal');
		$document = Factory::getDocument();
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/lib.js");
		$cid = $jinput->get( 'cid', array(),'ARRAY');
		switch ($task){
			case "agent_list":
				OspropertyAgent::agent_list($option);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "agent_unpublish":
				OspropertyAgent::agent_change_publish($option,$cid,0);	
			break;
			case "agent_publish":
				OspropertyAgent::agent_change_publish($option,$cid,1);
			break;
			case "agent_changefeatured":
				OspropertyAgent::changeFeatured($option,$cid,1);	
			break;
			case "agent_changeunfeatured":
				OspropertyAgent::changeFeatured($option,$cid,0);
			break;
			case "agent_remove":
				OspropertyAgent::agent_remove($option,$cid);
			break;
			case "agent_orderup":
				OspropertyAgent::agent_change_order($option,$cid[0],-1);
			break;
			case "agent_orderdown":
				OspropertyAgent::agent_change_order($option,$cid[0],1);
			break;
			case "agent_saveorder":
				OspropertyAgent::agent_saveorder($option,$cid);
			break;
			case "agent_saveorderAjax":
				OspropertyAgent::agent_saveorderAjax($option,$cid);
			break;
			case "agent_add":
				OspropertyAgent::agent_edit($option,0);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "agent_edit":
				OspropertyAgent::agent_edit($option,$cid[0]);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case 'agent_cancel':
				$mainframe->redirect("index.php?option=$option&task=agent_list");
			break;	
			case "agent_save":
				OspropertyAgent::agent_save($option,1);
			break;
			case "agent_new":
				OspropertyAgent::agent_save($option,2);
			break;
			case "agent_apply":
				OspropertyAgent::agent_save($option,0);
			break;
			case "agent_getstate":
				OspropertyAgent::agent_getstate($option);
			break;
            case "agent_changedefault":
                OspropertyAgent::changeDefaultStatus($cid[0]);
            break;
		}
	}
	
	/**
	 * agent list
	 *
	 * @param unknown_type $option
	 */
	static function agent_list($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$lists = array();
		$condition = '';
        $config = new JConfig();
        $list_limit = $config->list_limit;
		$agent_type = $jinput->getInt('agent_type',-1);
		
		$filter_order 	 				= $jinput->getString('filter_order','a.ordering');
		$filter_order_Dir 				= $jinput->getString('filter_order_Dir','');
		$filter_full_ordering			= $jinput->getString('filter_full_ordering','a.ordering asc');
		$filter_Arr						= explode(" ",$filter_full_ordering);
		$filter_order					= $filter_Arr[0];
		$filter_order_Dir				= $filter_Arr[1];
		if($filter_order == ""){
			$filter_order = 'a.ordering';
		}
		if ($filter_order != 'c.company_name'){
			$order_by = " ORDER BY c.company_name, $filter_order $filter_order_Dir";
		}else{
			$order_by = " ORDER BY $filter_order $filter_order_Dir";
		}
		
		$lists['filter_order']			= $filter_order;
		$lists['filter_order_Dir']		= $filter_order_Dir;
		
		// filter page
        $limitstart						= $jinput->get('limitstart','');
        if($limitstart == ""){
            $limitstart					= $mainframe->getUserStateFromRequest('agent_list.filter.limitstart','limit_start',0);
        }
        $mainframe->setUserState('agent_list.filter.limitstart',$limitstart);
        
		$limit     	  	 			= $mainframe->getUserStateFromRequest('agent_list.filter.limit','limit',$list_limit);
        if($limit ==  0)
		{
			$limit					= $jinput->getInt('limit',$list_limit);   
        }
        $mainframe->setUserState('agent_list.filter.limitstart',$limitstart);
        $mainframe->setUserState('agent_list.filter.limit',$limit);
		// search 
		$keyword = $jinput->getString('keyword','');
		if($keyword != ""){
			$condition .= " AND (";
			$condition .= " a.name LIKE '%$keyword%'";
			$condition .= " OR a.address LIKE '%$keyword%'";
			$condition .= " )";
		}
		
		if($agent_type >= 0){
			$condition .= " AND a.agent_type = '$agent_type'";
		}
			
		// filter company
		$filter_company = $jinput->getInt('filter_company',0);
		if ($filter_company){
			$condition .= " AND (a.company_id = $filter_company)";
		}
		$option_company = array();
		$option_company[] = HTMLHelper::_('select.option',0,' - '.Text::_('OS_SELECT_COMPANY').' - ');
		$db->setQuery('SELECT id AS value, company_name AS text FROM #__osrs_companies');
		$companies = $db->loadObjectList();
		if (count($companies)){
			$option_company = array_merge($option_company,$companies);
		}
		if($agent_type == 1){
			$disable = "disabled";
		}else{
			$disable = "";
		}
		$lists['filter_company'] = HTMLHelper::_('select.genericlist',$option_company,'filter_company','class="imedium form-select input-medium form-select" onchange="document.adminForm.submit();" '.$disable,'value','text',$filter_company);
		
		// filter request_to_approval
		$filter_request = $jinput->getInt('filter_request','');
		if ($filter_request != ''){
			$condition .= " AND a.request_to_approval = '$filter_request'";
		}
		$option_request = array();
		$option_request[] = HTMLHelper::_('select.option','',' - '.Text::_('OS_REQUEST_TO_APPROVAL').' - ');
		$option_request[] = HTMLHelper::_('select.option',0,Text::_('OS_APPROVAL'));
		$option_request[] = HTMLHelper::_('select.option',1,Text::_('OS_UNAPPROVAL'));
		$lists['filter_request'] = HTMLHelper::_('select.genericlist',$option_request,'filter_request','class="imedium form-select input-medium form-select" onchange="document.adminForm.submit();"','value','text',$filter_request);
			
		$count = "SELECT count(id) FROM #__osrs_agents AS a WHERE 1=1";
		$count .= $condition;
		$db->setQuery($count);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		
		$list  = "SELECT a.*, c.company_name, u.username FROM #__osrs_agents AS a"
				."\n LEFT JOIN #__osrs_companies AS c ON c.id = a.company_id "
				."\n LEFT JOIN #__users AS u ON u.id = a.user_id "
				."\n WHERE 1=1 ";
		$list .= $condition;
		$list .= $order_by;
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		
		if(count($rows) > 0){
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				$alias = $row->alias;
				if($alias == ""){
					$alias = OSPHelper::generateAlias('agent',$row->id);
					$db->setQuery("Update #__osrs_agents set alias = '$alias' where id = '$row->id'");
					$db->execute();
					$row->alias = $alias;
				}
			}
		}
		
		HTML_OspropertyAgent::agent_list($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * publish or unpublish agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function agent_change_publish($option,$cid,$state){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	
		{
			$cids = implode(",",$cid);

			$db->setQuery("UPDATE #__osrs_agents SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();


			for($i=0;$i<count($cid);$i++)
			{
				$agent_id = $cid[$i];
				$db->setQuery("Select name, email,request_to_approval from #__osrs_agents where id = '$agent_id'");
				$agent = $db->loadObject();
				$request_to_approval = $agent->request_to_approval;
				if(($request_to_approval == 1) && ($state == 1))
				{
					//update it to 0
					$db->setQuery("Update #__osrs_agents set request_to_approval= '0' where id = '$agent_id'");
					$db->execute();
					//send email
					$emailOpt['agentid']	= $agent_id;
					$emailOpt['agentname'] 	= $agent->name;
					$emailOpt['agentemail'] = $agent->email;
					OspropertyEmailBackend::sendAgentActiveEmail($option,$emailOpt);
				}
			}
		}
		$msg = Text::_("OS_ITEM_STATUS_HAS_BEEN_CHANGED");
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=agent_list");
	}
	
	/**
	 * remove agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function agent_remove($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("SELECT photo FROM #__osrs_agents WHERE id IN ($cids)");
			$photos = $db->loadColumn();
			if (count($photos)){
				foreach ($photos as $photo) {
					if (is_file(PATH_STORE_PHOTO_AGENT_FULL.'/'.$photo)) unlink(PATH_STORE_PHOTO_AGENT_FULL.'/'.$photo);
					if (is_file(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$photo)) unlink(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$photo);
				}
			}
			
			$db->setQuery("SELECT company_id FROM #__osrs_agents WHERE id IN ($cids)");
			$companies = $db->loadObjectList();
			if(count($companies) > 0){
				for($i=0;$i<count($companies);$i++){
					$company_id = $companies[$i]->company_id;
					$db->setQuery("DELETE FROM #__osrs_company_agents WHERE company_id = '$company_id' and agent_id IN ($cids)");
					$db->execute();
				}
			}
			
			$db->setQuery("DELETE FROM #__osrs_agents WHERE id IN ($cids)");
			$db->execute();
			
			$db->setQuery("Select id from #__osrs_properties where agent_id in ($cids)");
			$rows = $db->loadObjectList();
			$property_id_array = array();
			if(count($rows) > 0){
				for($i=0;$i<count($rows);$i++){
					$property_id_array[$i] = $rows[$i]->id;
				}
				OspropertyProperties::remove($option,$property_id_array,0);
			}
		}
		$msg = Text::_("OS_ITEM_HAS_BEEN_DELETED");
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=agent_list");
	}
	
/**
	 * change order price group
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $direction
	 */
	static function agent_change_order($option,$id,$direction){
		global $jinput, $mainframe;
		
		$row = Table::getInstance('Agent','OspropertyTable');
		$row->load($id);
		$row->move( $direction, ' published >= 0 ' );
		$msg = Text::_( 'OS_NEW_ORDERING_SAVED' );
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=agent_list");
	}
	
	/**
	 * save new order
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function agent_saveorder($option,$cid)
	{
		global $jinput, $mainframe;
		$order 	= $jinput->get( 'order', array(), 'ARRAY' );
		$row = Table::getInstance('Agent','OspropertyTable');
		$groupings = array();
		
		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			// track company
			$groupings[] = $row->company_id;
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$msg = Text::_( 'OS_ERROR_SAVING_ORDERING' );
					$mainframe->enqueueMessage($msg);
					$mainframe->redirect("index.php?option=$option&task=agent_list");
				}
			}
		}
		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('company_id = '.(int) $group);
		}
		
		$msg = Text::_( 'OS_NEW_ORDERING_SAVED' );
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=agent_list");
	}


	static function agent_saveorderAjax($option,$cid){
		global $jinput, $mainframe;
		$order 	= $jinput->get( 'order', array(), 'ARRAY' );
		$row = Table::getInstance('Agent','OspropertyTable');
		$groupings = array();
		
		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			// track company
			$groupings[] = $row->company_id;
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$msg = Text::_( 'OS_ERROR_SAVING_ORDERING' );
					$mainframe->enqueueMessage($msg);
					$mainframe->redirect("index.php?option=$option&task=agent_list");
				}
			}
		}
		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('company_id = '.(int) $group);
		}
	}
	
	
	/**
	 * agent Detail
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function agent_edit($option,$id){
		global $jinput, $mainframe,$configClass,$languages;
		$db = Factory::getDBO();
		$row = Table::getInstance('Agent','OspropertyTable');
		if($id > 0)
		{
			$row->load((int)$id);
		}
		else
		{
			$row->published = 1;
		}
		
		// creat published
		//$lists['published'] = HTMLHelper::_('select.booleanlist', 'published', '', $row->published);
		$lists['published']   = OSPHelper::getBooleanInput('published',$row->published);
			
		// build the html select list for ordering
		$query = " SELECT ordering AS value, name AS text "
				.' FROM #__osrs_agents '
				." WHERE `company_id` = '$row->company_id'"
				." ORDER BY ordering";
		$lists['ordering'] = HTMLHelper::_('list.ordering', 'ordering', $query ,'',$row->ordering);
		//HTMLHelper::_('list.ordering')
		// build the html select list for user
		$option_user = array();
		$option_user[] = HTMLHelper::_('select.option',0,' - '.Text::_('OS_SELECT_AGENT').' - ');
		$db->setQuery("SELECT id, name, username FROM #__users where block = '0' and id not in (Select user_id from #__osrs_companies)");
		$users = $db->loadObjectList();
		foreach ($users as $user) {
			$option_user[] = HTMLHelper::_('select.option',$user->id,$user->username.' ['.$user->name.' - '.$user->id.']');
		}
		$lists['user_id'] = HTMLHelper::_('select.genericlist',$option_user,'user_id','class="input-medium form-select ilarge"','value','text',$row->user_id);
			
		// build the html select list for company
		$option_company = array();
		$option_company[] = HTMLHelper::_('select.option',0,' - '.Text::_('OS_SELECT_COMPANY').' - ');
		$db->setQuery('SELECT id AS value, company_name AS text FROM #__osrs_companies');
		$companies = $db->loadObjectList();
		if (count($companies)){
			$option_company = array_merge($option_company,$companies);
		}
		if($row->agent_type == 1){
			$disable = "disabled";
		}else{
			$disable = "";
		}
		$lists['company_id'] = HTMLHelper::_('select.genericlist',$option_company,'company_id','class="input-medium form-select ilarge" '.$disable,'value','text',$row->company_id);
		unset($option_company);unset($companies);
		
		$lists['country'] = HelperOspropertyCommon::makeCountryList(intval($row->country),'country','onchange="loadStateBackend(this.value,\''.$row->state.'\',\''.$row->city.'\');"','','');
		
		$lists['states'] = HelperOspropertyCommon::makeStateList(intval($row->country),intval($row->state),'state','onchange="loadCityBackend(this.value,'.intval($row->city).')" class="input-medium form-select ilarge"',Text::_('OS_SELECT_STATE'),'');
		
		$lists['city'] = HelperOspropertyCommon::loadCity($option,$row->state,$row->city);
		
		$translatable = Multilanguage::isEnabled() && count($languages);
		
		HTML_OspropertyAgent::editHTML($option,$row,$lists,$translatable);
	}
	
	
	/**
	 * save agent
	 *
	 * @param unknown_type $option
	 */
	static function agent_save($option,$save)
    {
		global $jinput, $mainframe,$configClass,$languages;
		$db                 = Factory::getDBO();
        $jinput             = Factory::getApplication()->input;
        $id                 = $jinput->getInt('id',0);
        if($id == 0)
        {
            $isNew          = 1;
        }
        else
        {
            $isNew          = 0;
        }
		$user_id            = $jinput->getInt('user_id',0);
		$username           = $jinput->getString('username','');
		$password           = $jinput->getString('password','');
		$email	            = $jinput->getString('email','');
		$existing_user      = $jinput->getInt('existing_user',1);
		
		if($username != '' && $password != '' && $existing_user == 0)
		{
			//create new user
			$query = $db->getQuery(true);
			$query->select('count(id)')->from('#__users')->where('username like "'.$username.'" or email like "'.$email.'"');
			$db->setQuery($query);
			$count = $db->loadResult();
			if($count > 0)
			{
				if($isNew == 1)
				{
					$mainframe->enqueueMessage(Text::_('OS_USER_EXISTING'), 'error');
					$mainframe->redirect("index.php?option=com_osproperty&task=agent_new");
				}
				else
				{
					$mainframe->enqueueMessage(Text::_('OS_USER_EXISTING'), 'error');
					$mainframe->redirect("index.php?option=com_osproperty&task=agent_edit&cid[]=".$id);
				}
			}
			else
			{
				$data['username']	= $username;
				$data['email']		= $email;
				$data['email2']		= $email;
				$data['password']	= $password;
				$data['password2']	= $password;
				$data['name']		= $jinput->getString('name','');
				if(OSPHelper::newJoomlaUser($data))
				{
					$query->clear();
					$query->select('id')->from('#__users')->where('username like "'.$username.'" or email like "'.$email.'"');
					$db->setQuery($query);
					$user_id = $db->loadResult();
				}
			}
		}
		else
		{
			//checking user
			$user_id = $jinput->getInt('user_id',0);
			if($user_id > 0)
			{
				$query = $db->getQuery(true);
				if($isNew == 1)
				{
					$query->select('count(id)')->from('#__osrs_agents')->where('user_id = "'.$user_id.'"');
				}
				else
				{
					$query->select('count(id)')->from('#__osrs_agents')->where('user_id = "'.$user_id.'" and id <> "'.$id.'"');
				}
				$db->setQuery($query);
				$count = $db->loadResult();
				if($count > 0)
				{
					if($isNew == 1)
					{
						$mainframe->enqueueMessage(Text::_('OS_JOOMLA_USER_HAS_BEEN_ASSIGNED_TO_ANOTHER_AGENT'), 'error');
						$mainframe->redirect("index.php?option=com_osproperty&task=agent_new");
					}
					else
					{
						$mainframe->enqueueMessage(Text::_('OS_JOOMLA_USER_HAS_BEEN_ASSIGNED_TO_ANOTHER_AGENT'), 'error');
						$mainframe->redirect("index.php?option=com_osproperty&task=agent_edit&cid[]=".$id);
					}
				}

				$query->clear();
				$query->select('count(id)')->from('#__osrs_companies')->where('user_id = "'.$user_id.'"');
				$db->setQuery($query);
				$count = $db->loadResult();
				if($count > 0)
				{
					if($isNew == 1)
					{
						$mainframe->enqueueMessage(Text::_('OS_THIS_JOOMLA_USER_ALREADY_IS_COMPANY_ADMIN'), 'error');
						$mainframe->redirect("index.php?option=com_osproperty&task=agent_new");
					}
					else
					{
						$mainframe->enqueueMessage(Text::_('OS_THIS_JOOMLA_USER_ALREADY_IS_COMPANY_ADMIN'), 'error');
						$mainframe->redirect("index.php?option=com_osproperty&task=agent_edit&cid[]=".$id);
					}
				}
			}
		}

		$country = $jinput->getInt('country',$configClass['show_country_id']);
		jimport('joomla.filesystem.file');
		$post = $jinput->post->getArray();

		//PATH_STORE_PHOTO_AGENT_FULL store full image;
		//PATH_STORE_PHOTO_AGENT_THUMB store thumbnail image
		
		// check folder to upload
		if (!Folder::exists(PATH_STORE_PHOTO_AGENT_THUMB)) Folder::create(PATH_STORE_PHOTO_AGENT_THUMB);
		
		// remove file if you want
		if (isset($post['remove_photo']))
		{
			if (is_file(PATH_STORE_PHOTO_AGENT_FULL.'/'.$post['photo'])) unlink(PATH_STORE_PHOTO_AGENT_FULL.'/'.$post['photo']);
			if (is_file(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$post['photo'])) unlink(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$post['photo']);
			$post['photo'] = '';
		}
			
		// upload file
		if (!empty($_FILES['file_photo']['name']) && $_FILES['file_photo']['error'] == 0 && $_FILES['file_photo']['size'] > 0 )
		{
			if(!HelperOspropertyCommon::checkIsPhotoFileUploaded('file_photo'))
			{
				//return to previous page
				if($isNew == 1)
				{
					$mainframe->enqueueMessage(Text::_('OS_PHOTO_INVALID'), 'error');
					$mainframe->redirect("index.php?option=com_osproperty&task=agent_new");
				}
				else
				{
					$mainframe->enqueueMessage(Text::_('OS_PHOTO_INVALID'), 'error');
					$mainframe->redirect("index.php?option=com_osproperty&task=agent_edit&cid[]=".$id);
				}
			}
			else
			{
				$imagename = OSPHelper::processImageName(uniqid().$_FILES['file_photo']['name']);
				if (move_uploaded_file($_FILES['file_photo']['tmp_name'],PATH_STORE_PHOTO_AGENT_FULL.'/'.$imagename))
				{
					
					// copy image before resize
					copy(PATH_STORE_PHOTO_AGENT_FULL.'/'.$imagename,PATH_STORE_PHOTO_AGENT_THUMB.'/'.$imagename);
					// resize image just copy and replace it selft
					require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/image.php');
					//$image = new SimpleImage();
					//$image->load(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$imagename);
					$imagesize = getimagesize(PATH_STORE_PHOTO_AGENT_FULL.'/'.$imagename);
					$owidth = $imagesize[0];
					$oheight = $imagesize[1];
					$nwidth = $configClass['images_thumbnail_width'];
					if($nwidth < $owidth)
					{ //only resize when the image width is smaller
						$nheight = round(($configClass['images_thumbnail_width']*$oheight)/$owidth);
					    //$image->resize($nwidth,$nheight);
					    //$image->save(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$imagename,$configClass['images_quality']);
						OSPHelper::resizePhoto(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$imagename,$nwidth,$nheight);
					}
					
					// remove old image
					if (is_file(PATH_STORE_PHOTO_AGENT_FULL.'/'.$post['photo'])) unlink(PATH_STORE_PHOTO_AGENT_FULL.'/'.$post['photo']);
					if (is_file(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$post['photo'])) unlink(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$post['photo']);
						
				    // keep file name
				    $post['photo'] = $imagename;
				}
			}
		}
		// change file name for standard
        $user                   = Factory::getUser($user_id);
		if($email == "")
        {
            $email              = $user->email;
        }
		$row                    = Table::getInstance('Agent','OspropertyTable');
		$row->bind($post);
		if($row->email == "")
        {
            $row->email         = $email;
        }
        $agent_name             = $row->name;
        $agent_email            = $row->email;
		// if new item, order last in appropriate group
		if (!$row->id)
		{
			$where = 'company_id = ' . (int) $row->company_id ;
			$row->ordering = $row->getNextOrder( $where );
		}
		$bio                    = $_POST['bio'];
		$row->bio               = $bio;
		$row->user_id           = $user_id;
		$row->alias				= (string) $row->alias;
		$row->state				= (int) $row->state;
		$row->check();
		$msg                    = Text::_('OS_ITEM_HAS_BEEN_SAVED');
	 	if (!$row->store())
	 	{
		 	throw new Exception($row->getError(), 500);
		}
		
		
		//update into #__osrs_company_agents
		if($id == 0)
		{
			$id                 = $db->insertID();
		}
		else
		{
			$db->setQuery("Select name, email,request_to_approval,published from #__osrs_agents where id = '$id'");
			$agent = $db->loadObject();
			$request_to_approval = $agent->request_to_approval;
			if(($request_to_approval == 1) && ($agent->published == 1)){
				//update it to 0
				$db->setQuery("Update #__osrs_agents set request_to_approval= '0' where id = '$id'");
				$db->execute();
				//send email
				$emailOpt['agentid']	= $id;
				$emailOpt['agentname'] 	= $agent->name;
				$emailOpt['agentemail'] = $agent->email;
				OspropertyEmailBackend::sendAgentActiveEmail($option,$emailOpt);
			}
		}
		
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable)
		{
			foreach ($languages as $language)
			{
				$sef = $language->sef;
				$bio_language = $_POST['bio_'.$sef];
				if($bio_language == "")
				{
					$bio_language = $bio;
					if($bio_language != "")
					{
						$row = Table::getInstance('Agent','OspropertyTable');
						$row->id = $id;
						$row->{'bio_'.$sef} = $bio_language;
						$row->store();
					}
				}
			}
		}
		
		$alias = $jinput->getString('alias','');
		$agent_alias = OSPHelper::generateAlias('agent',$id,$alias);
		$db->setQuery("Update #__osrs_agents set alias = '$agent_alias' where id = '$id'");
		$db->execute();
		
		if(intval($row->company_id) > 0)
		{
			$db->setQuery("SELECT COUNT(id) FROM #__osrs_company_agents where agent_id = '$id' AND company_id = '$row->company_id'");
			$count = $db->loadResult();
			if($count == 0)
			{
				$db->setQuery("INSERT INTO #__osrs_company_agents (id, company_id,agent_id) VALUES (NULL,'$row->company_id','$id')");
				$db->execute();
			}
		}
		else
		{
			$db->setQuery("DELETE FROM #__osrs_company_agents where agent_id = '$id'");
			$db->execute();
		}
		
		if(intval($configClass['agent_joomla_group_id']) > 0 && $row->user_id > 0)
		{
			$user_id = $row->user_id;
			$db->setQuery("Select count(user_id) from #__user_usergroup_map where user_id = '$user_id' and group_id = '".$configClass['agent_joomla_group_id']."'");
			$count = $db->loadResult();
			if($count == 0){
				$db->setQuery("Insert into #__user_usergroup_map (user_id,group_id) values ('$user_id','".$configClass['agent_joomla_group_id']."')");
				$db->execute();
			}
		}


        if($isNew == 1)
        {
            //send notification email
			$db->setQuery("SELECT * FROM #__osrs_emails WHERE `email_key` LIKE 'new_agent_created' AND published = '1'");
			$email = $db->loadObject();
			$title = $email->email_title;

			$content = $email->email_content;
			$content = str_replace("{username}", $agent_name, $content);
			$content = str_replace("{business}", $configClass['general_bussiness_name'], $content);

            //$title = Text::_('OS_NEW_AGENT_CREATED');
            //$content = sprintf(Text::_('OS_NEW_AGENT_CREATED_EMAIL_CONTENT'),$agent_name,$configClass['general_bussiness_name']);
            $mailer = Factory::getMailer();
            $config = new JConfig();
			try
			{
				$mailer->sendMail($config->mailfrom,$config->fromname,$agent_email,$title,$content);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
        }
		
		$mainframe->enqueueMessage($msg);
		if($save == 1)
		{
			$mainframe->redirect("index.php?option=$option&task=agent_list");
		}
		elseif($save == 2)
		{
			$mainframe->redirect("index.php?option=$option&task=agent_add");
		}
		else
		{
			$mainframe->redirect("index.php?option=$option&task=agent_edit&cid[]=$id");
		}
	}
	
	
	/**
	 * get state for country of agent
	 *
	 * @param unknown_type $option
	 */
	static function agent_getstate($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$country_id = $jinput->getInt('country_id',0);
		$agent_id 	= $jinput->getInt('agent_id',0);
		
		if ($agent_id){
			$db->setQuery("SELECT state FROM #__osrs_agents WHERE `id` = '$agent_id' ");
			$select_state = $db->loadResult();
		}else{
			$select_state = null;
		}
		
		$option_state = array();
		$option_state[]= HTMLHelper::_('select.option',0,' - '.Text::_('OS_SELECT_STATE').' - ');
		
		if ($country_id){
			$db->setQuery("SELECT id AS value, state_name AS text FROM #__osrs_states WHERE `country_id` = '$country_id' ORDER BY state_name");		
			$states = $db->loadObjectList();
			if (count($states)){
				$option_state = array_merge($option_state,$states);
			}
			$disable = '';
		}else{
			$disable = 'disabled="disabled"';
		}
		
		echo HTMLHelper::_('select.genericlist',$option_state,'state','class="input-medium chosen" '.$disable,'value','text',$select_state);
	}
	
	/**
	 * Change featured status
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function changeFeatured($option,$cid,$state){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("UPDATE #__osrs_agents SET `featured` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$msg = Text::_("OS_ITEM_STATUS_HAS_BEEN_CHANGED");
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=agent_list");
	}

	static function getUserInput($user_id,$agent_id)
	{
		if (version_compare(JVERSION, '3.5', 'le')){
			// Initialize variables.
			$html = array();
			$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=user_id';
			
			// Initialize some field attributes.
			$attr = ' class="inputbox"';

			// Load the modal behavior script.
			HTMLHelper::_('behavior.modal');
			HTMLHelper::_('behavior.modal', 'a.modal_user_id');

			// Build the script.
			$script = array();
			$script[] = '	static function jSelectUser_user_id(id, title) {';
			$script[] = '		var old_id = document.getElementById("user_id").value;';
			$script[] = '		if (old_id != id) {';
			$script[] = '			document.getElementById("user_id").value = id;';
			$script[] = '			document.getElementById("user_id_name").value = title;';
			$script[] = '			var agent_name = document.getElementById("name");';
			$script[] = '			if(agent_name.value == ""){';
			$script[] = '				agent_name.value = title ;';
			$script[] = '			}';
			$script[] = '			' . $onchange;
			$script[] = '		}';
			$script[] = '		SqueezeBox.close();';
			$script[] = '	}';

			// Add the script to the document head.
			Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

			// Load the current username if available.
			$table = Table::getInstance('user');
			
			if ($user_id)
			{
				$table->load($user_id);
			}
			else
			{
				$table->username = Text::_('OS_SELECT_AGENT');
			}

			// Create a dummy text field with the user name.
			$html[] = '<span class="input-append">';
			$html[] = '<input type="text" class="input-medium form-control ilarge" id="user_id_name" value="'.htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') .'" disabled="disabled" size="35" /><a class="modal btn" title="'.Text::_('JLIB_FORM_CHANGE_USER').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.Text::_('JLIB_FORM_CHANGE_USER').'</a>';
			$html[] = '</span>';

			// Create the real field, hidden, that stored the user id.
			$html[] = '<input type="hidden" id="user_id" name="user_id" value="'.$user_id.'" />';

			return implode("\n", $html);
		}
		else
		{
            $field = FormHelper::loadFieldType('User');
            $element = new SimpleXMLElement('<field />');
            $element->addAttribute('name', 'user_id');
            $element->addAttribute('class', 'readonly form-control ilarge');
            if (!$agent_id)
            {
                $element->addAttribute('onchange', 'populateAgentData();');
            }
            $field->setup($element, $user_id);

            return $field->input;
		}
	}

	static function changeDefaultStatus($agent_id)
    {
        global $jinput, $mainframe;
        $status = $jinput->getInt('status');
        $db = Factory::getDbo();
        if ($status == 0)
        {
            $db->setQuery("Update #__osrs_agents set `default_agent` = '0' where id = '$agent_id'");
            $db->execute();
        }
        else
        {
            $db->setQuery("Update #__osrs_agents set `default_agent` = '0'");
            $db->execute();

            $db->setQuery("Update #__osrs_agents set `default_agent` = '1' where id = '$agent_id'");
            $db->execute();
        }
        $mainframe->enqueueMessage(Text::_('OS_DEFAULT_AGENT_STATUS_HAS_BEEN_UPDATED'));
        $mainframe->redirect('index.php?option=com_osproperty&task=agent_list');
    }
}
?>
