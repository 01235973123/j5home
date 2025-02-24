<?php
/*------------------------------------------------------------------------
# theme.php - Ossolution Property
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
use Joomla\Registry\Registry;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Filesystem\Path;
use Joomla\Archive\Archive;


class OspropertyTheme{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $jinput, $mainframe;
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$cid = $jinput->get( 'cid', array(),'ARRAY');
		switch ($task){
			case "theme_list":
				OspropertyTheme::theme_list($option);
			break;
			case "theme_edit":
				OspropertyTheme::theme_modify($option,$cid[0]);
			break;
			case "theme_apply":
				OspropertyTheme::theme_save($option,0);
			break;
			case "theme_save":
				OspropertyTheme::theme_save($option,1);
			break;
			case "theme_install":
				OspropertyTheme::install();
			break;
			case "theme_publish":
				OspropertyTheme::theme_state($option,$cid[0],1);
			break;
			case "theme_unpublish":
				OspropertyTheme::theme_state($option,$cid[0],0);
			break;
			case "theme_remove":
				OspropertyTheme::removeTheme($cid);
			break;
			case "theme_copy":
				OspropertyTheme::copyTheme($cid[0]);
			break;
			case "theme_gotolist":
				$mainframe->redirect("index.php?option=com_osproperty&task=theme_list");
			break;
		}
	}
	
	static function copyTheme($id){
		global $jinput, $mainframe,$config;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_themes where id = '$id'");
		$theme = $db->loadObject();
		
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$newtemplatename = date("d",time()).date("m",time()).date("Y",time()).date("H",time()).date("i",time()).date("s",time()).$theme->name;
		
		$row = Table::getInstance('Theme','OspropertyTable');
		$row->id 					= 0;
		$row->name 					= $newtemplatename;
		$row->title 				= date("d-m-y H:i:s",time()).$theme->title;
		$row->author 				= $theme->author;
		$row->creation_date 		= date("d-m-y H:i:s",time());
		$row->copyright 			= $theme->copyright;
		$row->author_email 			= $theme->author_email;
		$row->author_url 			= $theme->author_url;
		$row->version 				= $theme->version;
		$row->description 			= $theme->description;
		$row->params 				= $theme->params;
		$row->support_mobile_device = $theme->support_mobile_device;
        if(($theme->name == "default") or ($theme->default_duplicate == 1)){
            $row->default_duplicate = 1;
        }else{
            $row->default_duplicate = 0;
        }
		$row->published 			= 0;
		//save
		$row->store();
		//create new template folder
		Folder::copy($theme->name,$newtemplatename,JPATH_ROOT.DS."components".DS."com_osproperty".DS."templates");
		//rename xml file
		File::copy(JPATH_ROOT.DS."components".DS."com_osproperty".DS."templates".DS.$newtemplatename.DS.$theme->name.".xml",JPATH_ROOT.DS."components".DS."com_osproperty".DS."templates".DS.$newtemplatename.DS.$newtemplatename.".xml");
		//remove old file
		File::delete(JPATH_ROOT.DS."components".DS."com_osproperty".DS."templates".DS.$newtemplatename.DS.$theme->name.".xml");
		$msg = "Theme has been copied";
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=theme_list");
	}
	
	/**
	 * Manage themes
	 *
	 * @param unknown_type $option
	 */
	static function theme_list($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$limit = $jinput->getInt('limit',20);
		$limitstart = $jinput->getInt('limitstart',0);
		$db->setQuery("Select count(id) from #__osrs_themes");
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		$db->setQuery("Select * from #__osrs_themes order by id",$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		HTML_OspropertyTheme::listThemes($option,$rows,$pageNav);
	}
	
	/**
	 * Theme modify
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function theme_modify($option,$id){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_themes where id = '$id'");
		$item = $db->loadObject();

		$lists['published']   = OSPHelper::getBooleanInput('published',$item->published);
		
		$registry = new Registry;
		$registry->loadString($item->params);
		$data = new stdClass();
		$data->params = $registry->toArray();

        $form = Form::getInstance('osproperty', JPATH_ROOT . '/components/com_osproperty/templates/' . $item->name . '/' . $item->name . '.xml', array(), false, '//config');
        $form->bind($data);

		//$root = Factory::getXML( JPATH_ROOT . '/components/com_osproperty/templates/' . $item->name . '/' . $item->name . '.xml', true);
		HTML_OspropertyTheme::editTheme($option,$item,$lists,$form);
	}
	
	/**
	 * Save theme
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 * @return unknown
	 */
	static function theme_save($option,$save){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$id = $jinput->getInt('id',0);
		$row = & Table::getInstance('Theme', 'OspropertyTable');
		$data = $jinput->post->getArray();
		if ($id > 0)
			$row->load($id);									
		if (!$row->bind($data)) {
			return false;
		}				
		//Save parameters
		$params	= $jinput->get( 'params', null, 'array' );
		if (is_array($params))
		{
			$txt = array ();
			foreach ($params as $k => $v) {
				if (is_array($v)) {
					$v = implode(',', $v);	
				}
				$v =  str_replace("\r\n", '@@', $v) ;				
				$txt[] = "$k=\"$v\"";
			}
			$row->params = implode("\n", $txt);
		}
		
		
		if (!$row->store())
	 	{
		 	$msg                = Text::_('OS_ERROR_SAVING') . ' '.$row->getError();
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=com_osproperty&task=theme_list");
		}

		$data['id'] = $row->id ;
		
		$published = $jinput->getInt('published',0);
		if($published == 1){
			$db->setQuery("Update #__osrs_themes set published = '0' where id <> '$row->id'");
			$db->execute();
		}
		if($save == 1){
		    $mainframe->enqueueMessage(Text::_('OS_ITEM_HAS_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osproperty&task=theme_list");
		}else{
		    $mainframe->enqueueMessage(Text::_('OS_ITEM_HAS_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osproperty&task=theme_edit&cid[]=$row->id");
		}
	}
	
	/**
	 * Theme installation
	 *
	 */
	static function install(){
		global $jinput, $mainframe,$configClass;
		global $jinput, $mainframe;
		$db = & Factory::getDBO();
		jimport('joomla.filesystem.archive');
		$db = Factory::getDBO();
		//$plugin = $jinput->get('theme_package');
		if (version_compare(JVERSION, '3.4.0', 'ge'))
		{
			$plugin = $jinput->files->get('theme_package', null, 'raw');
		}
		else
		{
			$plugin = $jinput->files->get('theme_package', null, 'none');
		}
		if ($plugin['error'] || $plugin['size'] < 1)
		{
			$jinput->set('msg', Text::_('Upload plugin package error'));
			return false;
		}
		$config = new JConfig();
		$dest = $config->tmp_path . '/' . $plugin['name'];
		if (version_compare(JVERSION, '3.4.4', 'ge'))
		{
			$uploaded = File::upload($plugin['tmp_name'], $dest , false, true);
		}else{
			$uploaded = File::upload($plugin['tmp_name'], $dest);
		}
		if (!$uploaded)
		{
			$jinput->set('msg', Text::_('OS_THEME_UPLOAD_FAILED'));
			return false;
		}
		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');
		$extractdir = Path::clean(dirname($dest) . '/' . $tmpdir);
		//$result = JArchive::extract($dest, $extractdir);
		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			$archive = new Archive(['tmp_path' => $tmpPath]);
			$result  = $archive->extract($dest, $extractdir);
		}
		else
		{
			$result = JArchive::extract($dest, $extractdir);
		}
		if (!$result)
		{
			$jinput->set('msg', Text::_('OS_EXTRACT_THEME_ERROR'));
			return false;
		}
		$dirList = array_merge(Folder::files($extractdir, ''), Folder::folders($extractdir, ''));
		if (count($dirList) == 1)
		{
			if (Folder::exists($extractdir . '/' . $dirList[0]))
			{
				$extractdir = Path::clean($extractdir . '/' . $dirList[0]);
			}
		}
		//Now, search for xml file
		$xmlfiles = Folder::files($extractdir, '.xml$', 1, true);
		if (empty($xmlfiles))
		{
			$jinput->set('msg', Text::_('OS_COULD_NOT_FIND_XML_FILE'));
			return false;
		}
		$file = $xmlfiles[0];
		$root = simplexml_load_file($file);

		$pluginType = $root->attributes()->type;
		$pluginGroup = $root->attributes()->group;
		if ($root->getName() !== 'install')
		{
			$jinput->set('msg', Text::_('OS_INVALID_XML_FILE'));
			return false;
		}
		if ($pluginType != 'osptheme')
		{
			$jinput->set('msg', Text::_('OS_INVALID_OSP_THEME'));
			return false;
		}
		$name = (string) $root->name;
		$title = (string) $root->title;
		$author = (string) $root->author;
		$creationDate = (string) $root->creationDate;
		$copyright = (string) $root->copyright;
		$license = (string) $root->license;
		$authorEmail = (string) $root->authorEmail;
		$authorUrl = (string) $root->authorUrl;
		$version = (string) $root->version;
		$description = (string) $root->description;
		$mobile_device = (int) $root->mobiledevice;
		$row = & Table::getInstance('Theme', 'OspropertyTable') ;		
		$sql = 'SELECT id FROM #__osrs_themes WHERE name="'.$name.'"';
		$db->setQuery($sql);
		$pluginId = (int) $db->loadResult();
		if ($pluginId)
		{
			$row->load($pluginId);
			$row->name = $name;
			$row->title = $title;
			$row->author = $author;
			$row->creation_date = $creationDate;
			$row->copyright = $copyright;
			$row->license = $license;
			$row->author_email = $authorEmail;
			$row->author_url = $authorUrl;
			$row->version = $version;
			$row->description = $description;
			$row->support_mobile_device = $mobile_device;
		}
		else
		{
			$row->name = $name;
			$row->title = $title;
			$row->author = $author;
			$row->creation_date = $creationDate;
			$row->copyright = $copyright;
			$row->license = $license;
			$row->author_email = $authorEmail;
			$row->author_url = $authorUrl;
			$row->version = $version;
			$row->description = $description;
			$row->support_mobile_device = $mobile_device;
			$row->published = 0;
		}
		$row->store();
		$pluginDir = JPATH_ROOT . '/components/com_osproperty/templates';
		//$result = JArchive::extract($dest, $pluginDir);
		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			$archive = new Archive(['tmp_path' => Factory::getConfig()->get('tmp_path')]);
			$result  = $archive->extract($dest, $pluginDir);
		}
		else
		{
			$result = JArchive::extract($dest, $pluginDir);
		}
		Folder::delete($extractdir);
				
		$msg = Text::_('OS_THEME_HAS_BEEN_INSTALLED_SUCCESSFULLY');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=theme_list");
	}
	
	/**
	 * Theme state
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 * @param unknown_type $state
	 */
	static function theme_state($option,$id,$state){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		if($state == 1){
			$db->setQuery("Update #__osrs_themes set published = '0'");
			$db->execute();
			$db->setQuery("Update #__osrs_themes set published = '1' where id = '$id'");
			$db->execute();
		}else{
			$db->setQuery("Update #__osrs_themes set published = '0'");
			$db->execute();
			$db->setQuery("Update #__osrs_themes set published = '1' where name like 'default'");
			$db->execute();
		}
		$msg = Text::_('OS_THEME_HAS_BEEN_CHANGE_STATUS');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=theme_list");
	}
	
	/**
	 * Remove theme
	 *
	 * @param unknown_type $cid
	 */
	static function removeTheme($cid){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		jimport('joomla.filesystem.folder') ;
		$row = & Table::getInstance('Theme', 'OspropertyTable');				
		$pluginDir = JPATH_ROOT.DS.'components'.DS.'com_osproperty'.DS.'templates' ;
		foreach ($cid as $id) {
			$row->load($id);
			if($row->published != 1){
				$name = $row->name ;
				Folder::delete($pluginDir."/".$name);
				$db->setQuery("Delete from #__osrs_themes where id = '$id'");
				$db->execute();
			}
		}
		$msg = Text::_('OS_ITEM_HAVE_BEEN_REMOVED');
		if(count($cid) == 1){
			$row->load($id);
			if($row->published == 1){
				$msg = Text::_('OS_THIS_THEME_CANNOT_BE_REMOVED');		
			}
		}
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=theme_list");
	}
}
?>
