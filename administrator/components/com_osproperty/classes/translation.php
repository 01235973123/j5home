<?php

/*------------------------------------------------------------------------
# translation.php - Ossolution Property
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

define('PATH_STORE_PHOTO_AGENT_FULL',JPATH_ROOT.DS."images".DS."osproperty".DS."agent");
define('PATH_STORE_PHOTO_AGENT_THUMB',PATH_STORE_PHOTO_AGENT_FULL.DS.'thumbnail');
define('PATH_URL_PHOTO_AGENT_FULL',str_replace(DS,'/',str_replace(JPATH_SITE,Uri::root(),PATH_STORE_PHOTO_AGENT_FULL)).'/');
define('PATH_URL_PHOTO_AGENT_THUMB',str_replace(DS,'/',str_replace(JPATH_SITE,Uri::root(),PATH_STORE_PHOTO_AGENT_THUMB)).'/');


class OspropertyTranslation{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$jinput;
		switch ($task){
			case "translation_list":
				OspropertyTranslation::translation_list($option);
			break;
			case "translation_save":
				OspropertyTranslation::translation_save($option);
			break;
				
		}
	}
	
	public static function getTotal($lang, $languageFile,$site){
        global $jinput;
		jimport('joomla.filesystem.file');
		$limitstart = $jinput->getInt('limitstart',0);
		$limit      = $jinput->getInt('limit',100);
		$app = Factory::getApplication();
		$search = $jinput->getString('search','');
		$search = strtolower($search);
		$registry = new Registry();
		if($languageFile == "com_osproperty"){
			if ($site == 1)
			{
				
				$languageFolder = JPATH_ROOT . '/administrator/language/';
			}
			else
			{
				$languageFolder = JPATH_ROOT . '/language/';
			}
		}else{
			$languageFolder = JPATH_ROOT . '/language/';
		}
		$path = $languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini';
		
		$registry->loadFile($path, 'INI');
		$enGbItems = $registry->toArray();
		if ($search)
		{
			$search = strtolower($search);
			foreach ($enGbItems as $key => $value)
			{
				if (strpos(strtolower($key), $search) === false && strpos(strtolower($value), $search) === false)
				{
					unset($enGbItems[$key]);
				}
			}
		}
		
		return count($enGbItems);
	}
	
	/**
	 * Get pagination object
	 *
	 * @return JPagination
	 */
	public static function getPagination($lang, $item, $site)
	{
		global $jinput;
		// Lets load the content if it doesn't already exist
		if (empty($pagination))
		{
			jimport('joomla.html.pagination');
			$pagination = new Pagination(self::getTotal($lang, $item,$site), $jinput->getInt('limitstart',0), $jinput->getVar('limit',100));
		}
		
		return $pagination;
	}
	
	/**
	 * agent list
	 *
	 * @param unknown_type $option
	 */
	static function translation_list($option){
		
		global $mainframe,$jinput;
		$db = Factory::getDBO();
		$mainframe = & Factory::getApplication() ;
		
		jimport('joomla.filesystem.file') ;
		jimport('joomla.filesystem.folder');
		$search				= $jinput->getString('search','');
		$search				= strtolower( $search );
		$lists['search'] = $search;
			
		$lang = $jinput->getString('lang', '') ;
		if (!$lang)
			$lang = 'en-GB' ;
		$lists['lang'] = $lang;	
		$site = $jinput->getInt('site', 0) ;
		
		//$element = JRequest::getVar('element','com_osproperty');
		
		$path = JPATH_ROOT.DS.'language' ;
		if ($site) $path = JPATH_ROOT.DS.'administrator'.DS.'language';
				
		$languages = OspropertyTranslation::getLanguages($path);		
		$options = array() ;
		$options[] = HTMLHelper::_('select.option', '', Text::_('Select Language'))	;
		foreach ($languages as $language) {
			$options[] = HTMLHelper::_('select.option', $language, $language) ;		
		}
		$lists['langs'] = HTMLHelper::_('select.genericlist', $options, 'lang', ' class="input-medium form-select imedium" style="display:inline;"  onchange="this.form.submit();" ', 'value', 'text', $lang) ;
		
		$options = array() ;
		$options[] = HTMLHelper::_('select.option', 0, Text::_('Front-End')) ;
		$options[] = HTMLHelper::_('select.option', 1, Text::_('Back-End')) ;
		$lists['site'] = HTMLHelper::_('select.genericlist', $options, 'site', ' class="input-medium form-select imedium"  style="display:inline;" onchange="this.form.submit();" ', 'value', 'text', $site) ;
		
		$element = $jinput->getString('element','com_osproperty');
		$options = array();
		$options[] = HTMLHelper::_('select.option','com_osproperty','Component OS Property');
		if(Folder::exists(JPATH_ROOT.'/modules/mod_ospropertysearch')){
			$options[] = HTMLHelper::_('select.option','mod_ospropertysearch','Module OS Property Search');
		}
		if(Folder::exists(JPATH_ROOT.'/modules/mod_ospropertyrandom')){
			$options[] = HTMLHelper::_('select.option','mod_ospropertyrandom','Module OS Property Random');
		}
		if(Folder::exists(JPATH_ROOT.'/modules/mod_ospropertyslideshow')){
			$options[] = HTMLHelper::_('select.option','mod_ospropertyslideshow','Module OS Property Slideshow');
		}
		if(Folder::exists(JPATH_ROOT.'/modules/mod_ospropertyquicksearch')){
			$options[] = HTMLHelper::_('select.option','mod_ospropertyquicksearch','Module OS Property Quick Search');
		}
		if(Folder::exists(JPATH_ROOT.'/modules/mod_ospropertyloancal')){
			$options[] = HTMLHelper::_('select.option','mod_ospropertyloancal','Module OS Property Loan Calculator');
		}
		if(Folder::exists(JPATH_ROOT.'/modules/mod_ospropertymortgage')){
			$options[] = HTMLHelper::_('select.option','mod_ospropertymortgage','Module OS Property Mortgage');
		}
		if(Folder::exists(JPATH_ROOT.'/modules/mod_osquicksearch')){
			$options[] = HTMLHelper::_('select.option','mod_osquicksearch','Module OS Quick Search');
		}
		if(Folder::exists(JPATH_ROOT.'/modules/mod_osquicksearchrealhomes')){
			$options[] = HTMLHelper::_('select.option','mod_osquicksearchrealhomes','Module OS Quick Search Real Homes');
		}
		if(Folder::exists(JPATH_ROOT.'/modules/mod_os_contentslider')){
			$options[] = HTMLHelper::_('select.option','mod_os_contentslider','Module OS Content Slider');
		}
		if(Folder::exists(JPATH_ROOT.'/modules/mod_ospslideshow')){
			$options[] = HTMLHelper::_('select.option','mod_ospslideshow','OSP Slideshow');
		}
		if(Folder::exists(JPATH_ROOT.'/modules/mod_osmapsearch')){
			$options[] = HTMLHelper::_('select.option','mod_osmapsearch','Map Search module');
		}
		
		$lists['element_list'] = HTMLHelper::_('select.genericlist', $options, 'element', ' class="input-large form-select" style="width:250px;display:inline;"  onchange="this.form.submit();" ', 'value', 'text', $element) ;
		
		$item = $jinput->getString('item', '') ;
		if (!$item) $item = 'com_osproperty' ;
		$trans = self::getTrans($lang, $element, $site);
		
		$lists['item'] = $element;
		
		$pagination = self::getPagination($lang, $element,$site);
		
		HTML_OspropertyTranslation::translation_list($option,$trans,$lists,$pagination);
	}
	
	
	/**
	 * get translate
	 *
	 * @param unknown_type $lang
	 * @param unknown_type $item
	 * @return unknown
	 */
	public static function getTrans($language, $languageFile,$site){
        global $jinput;
		jimport('joomla.filesystem.file');
		$limitstart = $jinput->getInt('limitstart',0);
		$limit      = $jinput->getInt('limit',100);
		$app = Factory::getApplication();
		$search = $jinput->getString('search','');
		$search = strtolower($search);
		$registry = new Registry();
		if($languageFile == "com_osproperty"){
			if ($site == 1)
			{
				
				$languageFolder = JPATH_ROOT . '/administrator/language/';
				//$languageFile = substr($languageFile, 6);
			}
			else
			{
				$languageFolder = JPATH_ROOT . '/language/';
			}
		}else{
			$languageFolder = JPATH_ROOT . '/language/';
		}
		
		$path = $languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini';
		
		$registry->loadFile($path, 'INI');
		$enGbItems = $registry->toArray();
		
		if ($language != 'en-GB')
		{
			$translatedRegistry = new Registry();
			$translatedPath = $languageFolder . $language . '/' . $language . '.' . $languageFile . '.ini';
			if (File::exists($translatedPath))
			{
				$translatedRegistry->loadFile($translatedPath);
				$translatedLanguageItems = $translatedRegistry->toArray();
				//Remove unused language items
				$enGbKeys = array_keys($enGbItems);
				$changed = false;
				foreach ($translatedLanguageItems as $key => $value)
				{
					if (!in_array($key, $enGbKeys))
					{
						unset($translatedLanguageItems[$key]);
						$changed = true;
					}
				}
				if ($changed)
				{
					$translatedRegistry = new Registry();
					$translatedRegistry->loadArray($translatedLanguageItems);
				}
			}
			else
			{
				$translatedLanguageItems = array();
			}
			$translatedLanguageKeys = array_keys($translatedLanguageItems);
			foreach ($enGbItems as $key => $value)
			{
				if (!in_array($key, $translatedLanguageKeys))
				{
					$translatedRegistry->set($key, $value);
					$changed = true;
				}
			}
			File::write($translatedPath, $translatedRegistry->toString('INI'));
		}
		
		if ($search)
		{
			$search = strtolower($search);
			foreach ($enGbItems as $key => $value)
			{
				if (strpos(strtolower($key), $search) === false && strpos(strtolower($value), $search) === false)
				{
					unset($enGbItems[$key]);
				}
			}
		}
		//self::$_total = count($enGbItems);
		$data['en-GB'][$languageFile] = array_slice($enGbItems, $limitstart,$limit);
		if ($language != 'en-GB')
		{
			$path = $languageFolder . $language . '/' . $language . '.' . $languageFile . '.ini';
			
			if (File::exists($path))
			{
				$registry->loadFile($path);
				$languageItems = $registry->toArray();
				//$data[$language][$languageFile] = array_slice($languageItems, $limitstart, $limit);
				$translatedItems = array();
				foreach ($data['en-GB'][$languageFile] as $key => $value)
				{
					$translatedItems[$key] = isset($languageItems[$key]) ? $languageItems[$key] : '';
				}								
				$data[$language][$languageFile] = $translatedItems;
			}
			else
			{
				$data[$language][$languageFile] = array();
			}
		}
		return $data;
	}
	
	/**
	 * get option langguage of site
	 *
	 */
	static function getLanguages($path){
		jimport('joomla.filesystem.folder') ;
		$folders = Folder::folders($path) ;
		$rets = array() ;
		foreach ($folders as $folder)
			if ($folder != 'pdf_fonts')
				$rets[] = $folder ;
		return $rets ;	
	}
	
	/**
	 * save agent
	 *
	 * @param unknown_type $option
	 */
	static function translation_save($option){
		global $mainframe,$configClass,$jinput;
		$limitstart = $jinput->getInt('limitstart',0);
		$limit      = $jinput->getInt('limit',100);
        $site 		= $jinput->getString('site','');
        $lang 		= $jinput->getString('lang','');
        $search 	= $jinput->getString('search','');
		$data 		= $jinput->post->getArray();
		jimport('joomla.filesystem.file');
		$language = $data['lang'];
		$languageFile = $data['element'];
		
		if($languageFile == "com_osproperty"){
			if ($site == 1)
			{
				$languageFolder = JPATH_ROOT . '/administrator/language/';
			}
			else
			{
				$languageFolder = JPATH_ROOT . '/language/';
			}
		}else{
			$languageFolder = JPATH_ROOT . '/language/';
		}
		$registry = new Registry();
		$filePath = $languageFolder . $language . '/' . $language . '.' . $languageFile . '.ini';
		if (File::exists($filePath))
		{
			$registry->loadFile($filePath, 'INI');
		}
		else
		{
			$registry->loadFile($languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini', 'INI');
		}
		//Get the current language file and store it to array
		$keys = $data['keys'];
		$items = $data['items'];
		$content = "";
		foreach ($items as $item)
		{
			$item = trim($item);
			$value = trim($data['item_'.$item]);
			$value = str_replace("\"","'",$value);
			$registry->set($keys[$item], $value);
		}
		if (isset($data['extra_keys']))
		{
			$keys = $data['extra_keys'];
			$values = $data['extra_values'];
			for ($i = 0, $n = count($keys); $i < $n; $i++)
			{
				$key = trim($keys[$i]);
				$value = trim($values[$i]);
				$value = str_replace("\"","'",$value);
				$registry->set($key, $value);
			}
		}
		
		if ($language != 'en-GB')
		{
			//We need to add new language items which are not existing in the current language
			$enRegistry = new Registry();
			$enRegistry->loadFile($languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini', 'INI');
			$enLanguageItems = $enRegistry->toArray();
			$currentLanguageItems = $registry->toArray();
			foreach ($enLanguageItems as $key => $value)
			{
				$currentLanguageKeys = array_keys($currentLanguageItems);
				if (!in_array($key, $currentLanguageKeys))
				{					
					$registry->set($key, $value);
				}
			}
		}
		File::write($filePath, $registry->toString('INI'));

		$mainframe->redirect("index.php?option=com_osproperty&task=translation_list&element=".$languageFile."&site=".$site."&lang=".$lang."&search=".$search."&limitstart=".$limitstart."&limit=".$limit);
	}
}
?>
