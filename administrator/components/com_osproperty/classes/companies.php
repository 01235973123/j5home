<?php

/*------------------------------------------------------------------------
# companies.php - Ossolution Property
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
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;

define('PATH_STORE_PHOTO_COMPANY_FULL',JPATH_ROOT.'/images/osproperty/company');
define('PATH_STORE_PHOTO_COMPANY_THUMB',PATH_STORE_PHOTO_COMPANY_FULL.DS.'thumbnail');
define('PATH_URL_PHOTO_COMPANY_FULL',Uri::root().'/images/osproperty/company/');
define('PATH_URL_PHOTO_COMPANY_THUMB',Uri::root().'/images/osproperty/company/thumbnail/');

class OspropertyCompanies{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	// for company
	
	static function display($option,$task){
		global $jinput, $mainframe;
		$document = Factory::getDocument();
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/lib.js");
		$cid = $jinput->get( 'cid', array(),'ARRAY');
		switch ($task){
			case "companies_list":
				OspropertyCompanies::companies_list($option);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "companies_unpublish":
				OspropertyCompanies::companies_change_publish($option,$cid,0);	
			break;
			case "companies_publish":
				OspropertyCompanies::companies_change_publish($option,$cid,1);
			break;
			case "companies_remove":
				OspropertyCompanies::companies_remove($option,$cid);
			break;
			case "companies_add":
				OspropertyCompanies::companies_edit($option,0);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "companies_edit":
				OspropertyCompanies::companies_edit($option,$cid[0]);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case 'companies_cancel':
				$mainframe->redirect("index.php?option=$option&task=companies_list");
			break;	
			case "companies_save":
				OspropertyCompanies::companies_save($option,1);
			break;
			case "companies_apply":
				OspropertyCompanies::companies_save($option,0);
			break;
			case "companies_new":
				OspropertyCompanies::companies_save($option,2);
			break;
			case "companies_getstate":
				OspropertyCompanies::companies_getstate($option);
			break;
		}
	}
	
	/**
	 * Companies list
	 *
	 * @param unknown_type $option
	 */
	static function companies_list($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$lists = array();
		$condition = '';
		
		$filter_order = $jinput->getString('filter_order','id');
		$filter_order_Dir = $jinput->getString('filter_order_Dir','');
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
        $config = new JConfig();
        $list_limit = $config->list_limit;
        $limitstart						= $jinput->get('limitstart','');
        if($limitstart == ""){
            $limitstart					= $mainframe->getUserStateFromRequest('company_list.filter.limitstart','limit_start',0);
        }
        $mainframe->setUserState('company_list.filter.limitstart',$limitstart);
        
		$limit     	  	 				= $mainframe->getUserStateFromRequest('company_list.filter.limit','limit',$list_limit);
        if($limit == 0)
		{
			$limit							= $jinput->getInt('limit',$list_limit);    
        }
        $mainframe->setUserState('company_list.filter.limitstart',$limitstart);
        $mainframe->setUserState('company_list.filter.limit',$limit);
		$keyword = $jinput->getString('keyword','');
		if($keyword != ""){
			$condition .= " AND (";
			$condition .= " company_name LIKE '%$keyword%' ";
			$condition .= " OR address LIKE '%$keyword%' ";
			//$condition .= " OR state LIKE '%$keyword%' ";
			//$condition .= " OR city LIKE '%$keyword%' ";
			//$condition .= " OR country LIKE '%$keyword%' ";
			$condition .= " OR company_description LIKE '%$keyword%' ";
			$condition .= ")";
		}
		
		$count = "SELECT count(id) FROM #__osrs_companies WHERE 1=1";
		$count .= $condition;
		$db->setQuery($count);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		
		$list  = "SELECT p.*, c.country_name, s.state_name FROM #__osrs_companies AS p"
				." LEFT JOIN #__osrs_countries AS c ON c.id = p.country"
				." LEFT JOIN #__osrs_states AS s ON s.id = p.state"
				." WHERE 1=1 ";
		
		$list .= $condition;
		$list .= " ORDER BY $filter_order $filter_order_Dir";
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		
		
		if(count($rows) > 0){
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				$alias = $row->company_alias;
				if($alias == ""){
					$alias = OSPHelper::generateAlias('company',$row->id);
					$db->setQuery("Update #__osrs_companies set company_alias = '$alias' where id = '$row->id'");
					$db->execute();
					$row->company_alias = $alias;
				}
			}
		}
		
		HTML_OspropertyCompanies::companies_list($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * publish or unpublish companies
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function companies_change_publish($option,$cid,$state){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("Update #__osrs_companies SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$msg = Text::_('OS_ITEM_STATUS_HAS_BEEN_CHANGED');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=companies_list");
	}
	
	/**
	 * remove companies
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function companies_remove($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("SELECT photo FROM #__osrs_companies WHERE id IN ($cids)");
			$photos = $db->loadColumn();
			if (count($photos)){
				foreach ($photos as $photo) {
					if (is_file(PATH_STORE_PHOTO_COMPANY_FULL.DS.$photo)) unlink(PATH_STORE_PHOTO_COMPANY_FULL.DS.$photo);
					if (is_file(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$photo)) unlink(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$photo);
				}
			}
			$db->setQuery("DELETE FROM #__osrs_companies WHERE id IN ($cids)");
			$db->execute();
		}
		$msg = Text::_('OS_ITEM_HAS_BEEN_DELETED');
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=companies_list");
	}
	
	
	/**
	 * companies Detail
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function companies_edit($option,$id){
		global $jinput, $mainframe,$configClass,$languages;
		$db = Factory::getDBO();
		$row = Table::getInstance('Companies','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
		}
		
		//$lists['published'] = HTMLHelper::_('select.booleanlist', 'published', '', $row->published);
		$lists['published']   = OSPHelper::getBooleanInput('published',$row->published);
		
		$optionArr = array();
		$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_YES'));
		$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_NO'));
		$lists['approval']   = HTMLHelper::_('select.genericlist',$optionArr,'approval','class="input-mini form-select"','value','text',$row->request_to_approval);
		
		$lists['country'] = HelperOspropertyCommon::makeCountryList($row->country,'country','onchange="loadStateBackend(this.value,\''.$row->state.'\',\''.$row->city.'\')"','','');
		
		$lists['states'] = HelperOspropertyCommon::makeStateList($row->country,$row->state,'state','onchange="loadCityBackend(this.value,'.intval($row->city).')" class="input-medium form-select ilarge"',Text::_('OS_SELECT_STATE'),'');
		$lists['city'] = HelperOspropertyCommon::loadCity($option,$row->state,$row->city);
		
		// build the html select list for user
		$option_user = array();
		$option_user[] = HTMLHelper::_('select.option',0,' - '.Text::_('OS_SELECT_COMPANY_ADMIN').' - ');
		$db->setQuery("SELECT id, name, username FROM #__users where block = '0' and id not in (Select user_id from #__osrs_agents where agent_type = '0')");
		$users = $db->loadObjectList();
		foreach ($users as $user) {
			$option_user[] = HTMLHelper::_('select.option',$user->id,$user->username.' ['.$user->name.' - '.$user->id.']');
		}
		$lists['user_id'] = HTMLHelper::_('select.genericlist',$option_user,'user_id','class="input-small form-select ilarge"','value','text',$row->user_id);
		
		$query = "Select a.id as value, a.name as text from #__osrs_agents as a"
				." inner join #__users as b on b.id = a.user_id"
				." where b.block = '0' and agent_type = '0'"
				." and b.id not in (Select user_id from #__osrs_companies)"
				." and a.id not in (Select id from #__osrs_agents where company_id > 0 and published = '1') order by a.name";
		$db->setQuery($query);
		$lists['agentsnotinCompany'] = $db->loadObjectList();
		
		if($row->id > 0){
			$query = "Select a.id as value, a.name as text from #__osrs_agents as a where a.company_id = '$id' and a.agent_type='0' order by a.name";
			$db->setQuery($query);
			$lists['agentinCompany'] = $db->loadObjectList();
		}else{
			$lists['agentinCompany'] = array();
		}
		$translatable = Multilanguage::isEnabled() && count($languages);
		HTML_OspropertyCompanies::editHTML($option,$row,$lists,$translatable);
	}
	
	
	static function getUserInput($user_id,$company_id)
	{
		if (version_compare(JVERSION, '3.5', 'le')){
			// Initialize variables.
			$html = array();
			//$groups = $this->getGroups();
			//$excluded = $this->getExcluded();
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
				$table->username = Text::_('OS_SELECT_COMPANY_ADMIN');
			}

			// Create a dummy text field with the user name.
			$html[] = '<span class="input-append">';
			$html[] = '<input type="text" class="input-medium" id="user_id_name" value="'.htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') .'" disabled="disabled" size="35" /><a class="modal btn" title="'.Text::_('JLIB_FORM_CHANGE_USER').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.Text::_('JLIB_FORM_CHANGE_USER').'</a>';
			$html[] = '</span>';

			// Create the real field, hidden, that stored the user id.
			$html[] = '<input type="hidden" id="user_id" name="user_id" value="'.$user_id.'" />';

			return implode("\n", $html);
		}else{
				$field = FormHelper::loadFieldType('User');
				$element = new SimpleXMLElement('<field />');
				$element->addAttribute('name', 'user_id');
				$element->addAttribute('class', 'readonly');
				if (!$company_id)
				{
					$element->addAttribute('onchange', 'populateCompanyData();');
				}
				$field->setup($element, $user_id);

				return $field->input;
		}
	}
	
	/**
	 * save companies
	 *
	 * @param unknown_type $option
	 */
	static function companies_save($option,$save)
	{
		global $jinput, $mainframe,$configClass,$languages;
		$db                 = Factory::getDBO();
        $jinput             = Factory::getApplication()->input;
        $country = $jinput->get('country',$configClass['show_country_id']);
		//check to see if user uploaded new state

        $existing_user      = $jinput->getInt('existing_user',0);
		
		jimport('joomla.filesystem.file');
		$post = $jinput->post->getArray();

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
					$mainframe->redirect("index.php?option=com_osproperty&task=companies_add");
				}
				else
				{
                    $mainframe->enqueueMessage(Text::_('OS_USER_EXISTING'), 'error');
					$mainframe->redirect("index.php?option=com_osproperty&task=companies_edit&cid[]=".$id);
				}
			}
			else
			{
				$data['username']	= $username;
				$data['email']		= $email;
				$data['email2']		= $email;
				$data['password']	= $password;
				$data['password2']	= $password;
				$data['name']		= $jinput->getString('company_name','');
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
				$query->select('count(id)')->from('#__osrs_agents')->where('user_id = "'.$user_id.'"');
				$db->setQuery($query);
				$count = $db->loadResult();
				if($count > 0)
				{
					if($isNew == 1)
					{
                        $mainframe->enqueueMessage(Text::_('OS_JOOMLA_USER_HAS_BEEN_ASSIGNED_TO_ANOTHER_AGENT'), 'error');
						$mainframe->redirect("index.php?option=com_osproperty&task=companies_add");
					}
					else
					{
                        $mainframe->enqueueMessage(Text::_('OS_JOOMLA_USER_HAS_BEEN_ASSIGNED_TO_ANOTHER_AGENT'), 'error');
						$mainframe->redirect("index.php?option=com_osproperty&task=companies_edit&cid[]=".$id);
					}
				}

				$query->clear();
				if($isNew == 1)
				{
					$query->select('count(id)')->from('#__osrs_companies')->where('user_id = "'.$user_id.'"');
				}
				else
				{
					$query->select('count(id)')->from('#__osrs_companies')->where('user_id = "'.$user_id.'" and id <> "'.$id.'"');
				}
				$db->setQuery($query);
				$count = $db->loadResult();
				if($count > 0)
				{
					if($isNew == 1)
					{
                        $mainframe->enqueueMessage(Text::_('OS_THIS_JOOMLA_USER_ALREADY_IS_AGENT'), 'error');
						$mainframe->redirect("index.php?option=com_osproperty&task=companies_add");
					}
					else
					{
                        $mainframe->enqueueMessage(Text::_('OS_THIS_JOOMLA_USER_ALREADY_IS_AGENT'), 'error');
						$mainframe->redirect("index.php?option=com_osproperty&task=companies_edit&cid[]=".$id);
					}
				}
			}
		}

		// check folder to upload file
		if (!Folder::exists(PATH_STORE_PHOTO_COMPANY_THUMB)) Folder::create(PATH_STORE_PHOTO_COMPANY_THUMB);
		
		// remove if you want
		if (isset($post['remove_photo']))
		{
			if (is_file(PATH_STORE_PHOTO_COMPANY_FULL.DS.$post['photo'])) unlink(PATH_STORE_PHOTO_COMPANY_FULL.DS.$post['photo']);
			if (is_file(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$post['photo'])) unlink(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$post['photo']);
			$post['photo'] = '';
		}
		
		// upload file
		if(!HelperOspropertyCommon::checkIsPhotoFileUploaded('file_photo'))
		{
			//return to previous page
			//do nothing
		}
		else
		{
			if ( !empty($_FILES['file_photo']['name']) && $_FILES['file_photo']['error'] == 0 &&  $_FILES['file_photo']['size'] > 0 )
			{
				$imagename = OSPHelper::processImageName(time()."".str_replace(" ","",$_FILES['file_photo']['name']));
				if (move_uploaded_file($_FILES['file_photo']['tmp_name'],PATH_STORE_PHOTO_COMPANY_FULL.DS.$imagename))
				{
					
					// copy image before resize
					copy(PATH_STORE_PHOTO_COMPANY_FULL.DS.$imagename,PATH_STORE_PHOTO_COMPANY_THUMB.DS.$imagename);
					// resize image just copy and replace it selft
					require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers/image.php');
					//$image			= new SimpleImage();
					//$image->load(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$imagename);
					$imagesize		= getimagesize(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$imagename);
					$owidth			= $imagesize[0];
					$oheight		= $imagesize[1];
					$nwidth			= $configClass['images_thumbnail_width'];
					if($nwidth < $owidth){ //only resize when the image width is smaller
						$nheight	= round(($nwidth*$oheight)/$owidth);
					    //$image->resize($nwidth,$nheight);
					    //$image->save(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$imagename,$configClass['images_quality']);
						OSPHelper::resizePhoto(PATH_STORE_PHOTO_COMPANY_THUMB.'/'.$imagename,$nwidth,$nheight);
					}
					    
					// remove old image
					if (is_file(PATH_STORE_PHOTO_COMPANY_FULL.DS.$post['photo'])) unlink(PATH_STORE_PHOTO_COMPANY_FULL.DS.$post['photo']);
					if (is_file(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$post['photo'])) unlink(PATH_STORE_PHOTO_COMPANY_THUMB.DS.$post['photo']);
						
				    // set new name
				    $post['photo']	= $imagename;
				}
			}
		}

        $user                   = Factory::getUser($user_id);
        if($email == "")
        {
            $email              = $user->email;
        }
		// store data
		$row                    = Table::getInstance('Companies','OspropertyTable');
		$row->user_id           = $user_id;
		$row->bind($post);
        if($row->email == "")
        {
            $row->email         = $email;
        }
		if($configClass['auto_approval_company_register_request'] == 0)
		{
			$approval = $jinput->getInt('approval',0);
			if($approval == 0)
			{
				$row->request_to_approval = 0;
				$row->published			  = $jinput->getInt('published',0);
			}
			else
			    {
				$row->request_to_approval = 1;
				$row->published			  = 0;
			}
		}
		else
		    {
			$row->published			  = $jinput->getInt('published',0);
			$row->request_to_approval = 0;
		}
		$company_description        = $_POST['company_description'];
		$row->company_description   = $company_description;
		$row->user_id               = $user_id;
		$row->state					= (int) $row->state;
		$row->city					= (int) $row->city;
		$row->check();
		$msg                        = Text::_('OS_ITEM_SAVED');
	 	if (!$row->store())
		{
		 	throw new Exception($row->getError(), 500);
		}

        $company_name               = $row->company_name;
        $company_email              = $row->email;

		$id                         = $jinput->getInt('id',0); // JRequest::getVar('id',0);
		if($id == 0){
			$id                     = $db->insertID();
            $isNew                  = 1;
		}else{
            $isNew                  = 0;
        }
		
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable)
		{
			foreach ($languages as $language)
			{
				$sef = $language->sef;
				$company_description_language = $_POST['company_description_'.$sef];
				if($company_description_language == "")
				{
					$company_description_language = $row->company_description;
					if($company_description_language != "")
					{
						$company = Table::getInstance('Companies','OspropertyTable');
						$company->id = $id;
						$company->user_id = $row->user_id;
						$company->{'company_description_'.$sef} = $company_description_language;
						$company->store();
					}
				}
			}
		}
		
		$company_alias = $jinput->getString('company_alias','');
		$company_alias = OSPHelper::generateAlias('company',$id,$company_alias);
		$db->setQuery("Update #__osrs_companies set company_alias = '$company_alias' where id = '$id'");
		$db->execute();
		
		if(intval($configClass['company_joomla_group_id']) > 0)
		{
			$user_id = $row->user_id;
			$db->setQuery("Select count(user_id) from #__user_usergroup_map where user_id = '$user_id' and group_id = '".$configClass['company_joomla_group_id']."'");
			$count = $db->loadResult();
			if($count == 0)
			{
				$db->setQuery("Insert into #__user_usergroup_map (user_id,group_id) values ('$user_id','".$configClass['company_joomla_group_id']."')");
				$db->execute();
			}
		}
		
		$db->setQuery("Update #__osrs_agents set company_id = '0' where company_id = '$id'");
		$db->execute();
		
		$users_selected  = $jinput->get('users_selected',array(),'ARRAY');
		if(count($users_selected) > 0){
			for($i=0;$i<count($users_selected);$i++){
				$db->setQuery("Update #__osrs_agents set company_id = '$id' where id = '".$users_selected[$i]."'");
				$db->execute();
			}
		}
		
		//check company admin 
		$company_admin = $row->user_id;
		//is agent
		
		$db->setQuery("Select count(id) from #__osrs_agents where user_id = '$company_admin' and agent_type='0'");
		$count = $db->loadResult();
		$isagent = ($count > 0 ? 1:0);
			 
		//is admin of other company
		$db->setQuery("Select count(id) from #__osrs_companies where user_id = '$company_admin' and id <> '$id'");
		$count = $db->loadResult();
		$isdifferentcompany = ($count > 0 ? 1:0);
		
		if(($isagent == 1) or ($isdifferentcompany == 1)){
			$db->setQuery("Update #__osrs_companies set user_id = '0' where id = '$id'");
			$db->execute();
			$msg = Text::_('OS_COMPANY_ADMIN_IS_NOT_ALLOWED');
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=$option&task=companies_edit&cid[]=$id");
		}
		
		if($configClass['auto_approval_company_register_request'] == 0){
			if($row->request_to_approval == 0){ 
				//send the notification to user
				OspropertyEmailBackend::sendActivateCompany($id);
			}
		}

        if($isNew == 1){
            //send notification email
            //$title = Text::_('OS_NEW_COMPANY_CREATED');
            //$content = sprintf(Text::_('OS_NEW_COMPANY_CREATED_EMAIL_CONTENT'),$company_name,$configClass['general_bussiness_name']);

			$db->setQuery("SELECT * FROM #__osrs_emails WHERE `email_key` LIKE 'new_company_created' AND published = '1'");
			$email = $db->loadObject();
			$title = $email->email_title;

			$content = $email->email_content;
			$content = str_replace("{username}", $company_name, $content);
			$content = str_replace("{business}", $configClass['general_bussiness_name'], $content);

            $mailer = Factory::getMailer();
            $config = new JConfig();
			try
			{
				$mailer->sendMail($config->mailfrom,$config->fromname,$company_email,$title,$content);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
        }

        $mainframe->enqueueMessage($msg);
		if($save == 1){
			$mainframe->redirect("index.php?option=$option&task=companies_list");
		}elseif($save == 2){
			$mainframe->redirect("index.php?option=$option&task=companies_add");
		}else{
			$mainframe->redirect("index.php?option=$option&task=companies_edit&cid[]=$id");
		}
	}
	
	/**
	 * get state when change country
	 *
	 * @param unknown_type $option
	 */
	static function companies_getstate($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$country_id = $jinput->getInt('country_id',0);
		$company_id = $jinput->getInt('company_id',0);
		
		if ($company_id){
			$db->setQuery("SELECT state FROM #__osrs_companies WHERE `id` = '$company_id' ");
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
		
		echo HTMLHelper::_('select.genericlist',$option_state,'state','class="input-small" '.$disable,'value','text',$select_state);
		?>
		<font class="small_text"><?php echo Text::_('New state')?>:</font> <input type="text" name="nstate" id="nstate" size="10" class="input-small">
		<?php
	}
}
?>
