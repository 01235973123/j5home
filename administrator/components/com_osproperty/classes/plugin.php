<?php
/*------------------------------------------------------------------------
# plugin.php - OS Property
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\Filesystem\Path;
use Joomla\Archive\Archive;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Form\Form;


class OspropertyPlugin{
	static function display($option,$task){
		global $jinput, $mainframe;
		$mainframe = Factory::getApplication();
		$cid = $jinput->get( 'cid', array(),'ARRAY');
		switch ($task){
			default:
			case "plugin_list":
				OspropertyPlugin::plugin_list($option);
			break;
			case "plugin_unpublish":
				OspropertyPlugin::plugin_state($option,$cid,0);
			break;
			case "plugin_publish":
				OspropertyPlugin::plugin_state($option,$cid,1);
			break;	
			case "plugin_remove":
				OspropertyPlugin::plugin_remove($cid);
			break;
			case "plugin_edit":
				OspropertyPlugin::plugin_modify($option,$cid[0]);
			break;
			case "plugin_apply":
				OspropertyPlugin::plugin_save($option,0);
			break;
			case "plugin_save":
				OspropertyPlugin::plugin_save($option,1);
			break;
			case "goto_index":
				$mainframe = Factory::getApplication();
				$mainframe->redirect("index.php");
			break;
			case "plugin_orderup":
				OspropertyPlugin::plugin_order($option,$cid[0],-1);
			break;
			case "plugin_orderdown":
				OspropertyPlugin::plugin_order($option,$cid[0],1);
			break;
			case "plugin_saveorder":
				OspropertyPlugin::plugin_saveorder($option,$cid);
			break;
			case "plugin_gotolist":
				$mainframe = Factory::getApplication();
				$mainframe->redirect("index.php?option=com_osproperty&task=plugin_list");
			break;
			case "plugin_install":
				OspropertyPlugin::install();
			break;
		}
	}
	
	/**
	 * Install payment plugin
	 *
	 */
	static function install(){
		global $jinput, $mainframe;
		$db = & Factory::getDBO();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		$db = Factory::getDBO();
		if (version_compare(JVERSION, '3.4.0', 'ge'))
		{
			$plugin = $jinput->files->get('plugin_package', null, 'raw');
		}
		else
		{
			$plugin = $jinput->files->get('plugin_package', null, 'none');
		}
		if ($plugin['error'] || $plugin['size'] < 1)
		{
			$jinput->set('msg', Text::_('Upload plugin package error'));
			$mainframe->enqueueMessage($jinput->getString('msg'));
			$mainframe->redirect("index.php?option=com_osproperty&task=plugin_list");
		}
		$config = new JConfig();
		$dest = $config->tmp_path . '/' . $plugin['name'];
		//$uploaded = File::upload($plugin['tmp_name'], $dest);

		if (version_compare(JVERSION, '3.4.4', 'ge'))
		{
			$uploaded = File::upload($plugin['tmp_name'], $dest , false, true);
		}else{
			$uploaded = File::upload($plugin['tmp_name'], $dest);
		}


		if (!$uploaded)
		{
            $jinput->set('msg', Text::_('Upload plugin package fail'));
			$mainframe->enqueueMessage($jinput->getString('msg'));
			$mainframe->redirect("index.php?option=com_osproperty&task=plugin_list");
		}


		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');
		$extractdir = Path::clean(dirname($dest) . '/' . $tmpdir);
		//$result = JArchive::extract($dest, $extractdir);
		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
        {
            $archive = new Archive(array('tmp_path' => Factory::getConfig()->get('tmp_path')));
            $result  = $archive->extract($dest, $extractdir);
        }
        else
        {
            $result = JArchive::extract($dest, $extractdir);
        }
		if (!$result)
		{
            $jinput->set('msg', Text::_('Extract plugin error'));
			$mainframe->enqueueMessage($jinput->getString('msg'));
			$mainframe->redirect("index.php?option=com_osproperty&task=plugin_list");
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
			$jinput->set('msg', Text::_('Could not find XML file'));
			$mainframe->enqueueMessage($jinput->getString('msg'));
			$mainframe->redirect("index.php?option=com_osproperty&task=plugin_list");
		}
		$file = $xmlfiles[0];
		$root	   = simplexml_load_file($file);
		$pluginType = $root->attributes()->type;
		$pluginGroup = $root->attributes()->group;
		if ($root->getName() !== 'install')
		{
            $jinput->set('msg', Text::_('Invalid XML file'));
			$mainframe->enqueueMessage($jinput->getString('msg'));
			$mainframe->redirect("index.php?option=com_osproperty&task=plugin_list");
		}
		if ($pluginType != 'osplugin')
		{
            $jinput->set('msg', Text::_('Invalid OS Property payment plugin'));
			$mainframe->redirect("index.php?option=com_osproperty&task=plugin_list",$jinput->getString('msg'));
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
		$row = & Table::getInstance('Plugins', 'OspropertyTable') ;
		$sql = 'SELECT id FROM #__osrs_plugins WHERE name="'.$name.'"';
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
			$row->published = 0;
			$row->ordering = $row->getNextOrder('published=1');
		}
		
		if (!$row->store())
	 	{
		 	$msg                = Text::_('OS_ERROR_SAVING') . ' '.$row->getError();
			throw new Exception($row->getError(), 500);
		}
		$pluginDir = JPATH_ROOT . '/components/com_osproperty/plugins';
		File::move($file, $pluginDir . '/' . basename($file));
		$files = $root->files->children();
		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$file = $files[$i];
			if ($file->getName() == 'filename')
			{
				$fileName = $file;
				if (!File::exists($pluginDir . '/' . $fileName))
				{
					File::copy($extractdir . '/' . $fileName, $pluginDir . '/' . $fileName);
				}
			}elseif ($file->getName() == 'logo')
			{
				$fileName = $file;
				if (!File::exists($pluginDir . '/' . $fileName))
				{
					File::copy($extractdir . '/' . $fileName,JPATH_ROOT . '/images/osproperty/plugins/' . $fileName);
				}
			}
			elseif ($file->getName() == 'folder')
			{
				$folderName = $file;
				if (Folder::exists($extractdir . '/' . $folderName))
				{
					Folder::move($extractdir . '/' . $folderName, $pluginDir . '/' . $folderName);
				}
			}
		}
		Folder::delete($extractdir);
				
		$msg = Text::_('OS_PAYMENT_PLUGIN_HAS_BEEN_INSTALLED_SUCCESSFULLY');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=plugin_list");
	}
	
	/**
	 * Save plugin
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 * @return unknown
	 */
	static function plugin_save($option,$save){
		global $jinput, $mainframe;
		$id = $jinput->getInt('id',0);
		$row = & Table::getInstance('Plugins', 'OspropertyTable');
		$data = $jinput->post->getArray();
		if ($id > 0)
			$row->load($id);									
		if (!$row->bind($data)) {
			return false;
		}				
		//Save parameters
		$params		= $jinput->get( 'params', array(), 'array' );
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
			throw new Exception($row->getError(), 500);
		}			
		$data['id'] = $row->id ;
		
		if($save == 1){
			$mainframe->enqueueMessage(Text::_('OS_ITEM_HAS_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osproperty&task=plugin_list");
		}else{
			$mainframe->enqueueMessage(Text::_('OS_ITEM_HAS_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osproperty&task=plugin_edit&cid[]=$row->id");
		}
	}
	
	/**
	 * Remove payment plugins
	 *
	 * @param unknown_type $cid
	 */
	static function plugin_remove($cid){
		global $jinput, $mainframe;
		jimport('joomla.filesystem.folder') ;
		jimport('joomla.filesystem.file') ;
		$row = & Table::getInstance('Plugins', 'OspropertyTable');				
		$pluginDir = JPATH_ROOT.'/components/com_osproperty/plugins' ;
		foreach ($cid as $id) {
			$row->load($id);
			$name = $row->name ;			
			$file = $pluginDir.'/'.$name.'.xml' ;
			$root	   = simplexml_load_file($file);
			$files = $root->files->children();
			//$pluginDir = JPATH_ROOT . '/components/com_osproperty/plugins';
			for ($i = 0, $n = count($files); $i < $n; $i++)
			{
				$file = $files[$i];
				if ($file->getName() == 'filename')
				{
					$fileName = $file;
					if (File::exists($pluginDir . '/' . $fileName))
					{
						File::delete($pluginDir . '/' . $fileName);
					}
				}
				elseif ($file->getName() == 'folder')
				{
					$folderName = $file;
					if ($folderName)
					{
						if (Folder::exists($pluginDir . '/' . $folderName))
						{
							Folder::delete($pluginDir . '/' . $folderName);
						}
					}
				}
			}
			$files = $root->languages->children();
			$languageFolder = JPATH_ROOT . '/language';
			for ($i = 0, $n = count($files); $i < $n; $i++)
			{
				$fileName = $files[$i];
				$pos = strpos($fileName, '.');
				$languageSubFolder = substr($fileName, 0, $pos);
				if (File::exists($languageFolder . '/' . $languageSubFolder . '/' . $fileName))
				{
					File::delete($languageFolder . '/' . $languageSubFolder . '/' . $fileName);
				}
			}
			File::delete($pluginDir . '/' . $name . '.xml');
			$row->delete();	
		}				
		$mainframe->enqueueMessage(Text::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		OspropertyPlugin::plugin_list($option);
	}
	
	/**
	 * change order price group
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $direction
	 */
	static function plugin_order($option,$id,$direction){
		global $jinput, $mainframe;
		$mainframe = Factory::getApplication();
		$row = Table::getInstance('Plugins','OspropertyTable');
		$row->load($id);
		$row->move( $direction);
		$row->reorder();
		$mainframe->enqueueMessage(Text::_("OS_NEW_ORDERING_SAVED"),'message');
		OspropertyPlugin::plugin_list($option);
	}
	
	/**
	 * save new order
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function plugin_saveorder($option,$cid){
		global $jinput, $mainframe;
		$mainframe = Factory::getApplication();
		$msg = Text::_("OS_NEW_ORDERING_SAVED");
		$order 	= $jinput->get( 'order', array(), 'array' );
		JArrayHelper::toInteger($order);
		$row = Table::getInstance('Plugins','OspropertyTable');
		
		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]){
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$msg = Text::_("OS_ERROR_SAVING_ORDERING");
					break;
				}
			}
		}
		// execute updateOrder
		$row->reorder();
		$mainframe->enqueueMessage($msg,'message');
		OspropertyPlugin::plugin_list($option);
	}
	
	/**
	 * List all plugins
	 *
	 * @param unknown_type $option
	 */
	static function plugin_list($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		// filte sort
		$filter_order 				= $mainframe->getUserStateFromRequest($option.'.plugin.filter_order','filter_order','ordering','string');
		$filter_order_Dir 			= $mainframe->getUserStateFromRequest($option.'.plugin.filter_order_Dir','filter_order_Dir','','string');
		$lists['order'] 			= $filter_order;
		$lists['order_Dir'] 		= $filter_order_Dir;
		$order_by 					= " ORDER BY $filter_order $filter_order_Dir";
		$limitstart = $jinput->getInt('limitstart',0);
		$limit = $jinput->getInt('limit',20);
		$keyword = $jinput->getString('keyword','');
		$query = "Select count(id) from #__osrs_plugins where 1=1";
		if($keyword != ""){
			$query .= " and name like '%$keyword%'";
		}
		$db->setQuery($query);
		$count = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($count,$limitstart,$limit);
		$query = "Select * from #__osrs_plugins where 1=1";
		if($keyword != ""){
			$query .= " and name like '%$keyword%'";
		}
		$query .= $order_by;
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		HTML_OspropertyPlugin::listPlugins($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * Plugin modification
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function plugin_modify($option, $id){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_plugins where id = '$id'");
		$item = $db->loadObject();
		$optionState[] = HTMLHelper::_('select.option',1,Text::_('OS_PUBLISH'));
		$optionState[] = HTMLHelper::_('select.option',0,Text::_('OS_UNPUBLISH'));
		$lists['published'] = HTMLHelper::_('select.genericlist',$optionState,'published','class="inputbox"','value','text',$row->published);
		
		$registry = new Registry;
		$registry->loadString($item->params);
		$data = new stdClass();
		$data->params = $registry->toArray();
		$form = Form::getInstance('osproperty', JPATH_ROOT . '/components/com_osproperty/plugins/' . $item->name . '.xml', array(), false, '//config');
		$form->bind($data);
		
		HTML_OspropertyPlugin::editPlugin($option,$item,$lists,$form);
	}
	
	/**
	 * Plugin change state
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function plugin_state($option,$cid,$state){
		global $jinput, $mainframe;
		$db 		= Factory::getDBO();
		if(count($cid)>0)	{
			$cids 	= implode(",",$cid);
			$db->setQuery("UPDATE #__osrs_plugins SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(Text::_("OS_STATUS_CHANGED"),'message');
		OspropertyPlugin::plugin_list($option);
	}
}
?>
