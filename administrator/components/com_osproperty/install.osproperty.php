<?php

/*------------------------------------------------------------------------
	# install.osproperty.php - Ossolution Property
	# ------------------------------------------------------------------------
	# author    Dang Thuc Dam
	# copyright Copyright (C) 2024 joomdonation.com. All Rights Reserved.
	# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
	# Websites: http://www.joomdonation.com
	# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Filesystem\File;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;

error_reporting(0);

class com_ospropertyInstallerScript 
{
	public static $languageFiles = array('en-GB','de-DE','el-GR','fr-FR','es-ES','pt-PT','nl-NL','tr-TR','ru-RU','it-IT');
	
	/**
	 * Method to run before installing the component	 
	 */
	function preflight($type, $parent)
	{
		//Backup the old language file
		foreach (self::$languageFiles as $languageFile)
		{
			$filename = $languageFile.'.com_osproperty.ini';
			if (File::exists(JPATH_ROOT . '/language/'.$languageFile.'/' . $filename))
			{
				File::copy(JPATH_ROOT . '/language/'.$languageFile.'/' . $filename, JPATH_ROOT . '/language/'.$languageFile.'/bak.' . $filename);
			}

			if (File::exists(JPATH_ROOT . '/administrator/language/'.$languageFile.'/' . $filename))
			{
				File::copy(JPATH_ROOT . '/administrator/language/'.$languageFile.'/' . $filename, JPATH_ROOT . '/administrator/language/'.$languageFile.'/bak.' . $filename);
			}


			/*
			$filename = $languageFile.'.mod_ospropertysearch.ini';
			if (JFile::exists(JPATH_ROOT . '/language/'.$languageFile.'/' . $filename))
			{
				JFile::copy(JPATH_ROOT . '/language/'.$languageFile.'/' . $filename, JPATH_ROOT . '/language/'.$languageFile.'/bak.' . $filename);
			}

			$filename = $languageFile.'.mod_ospropertyloancal.ini';
			if (JFile::exists(JPATH_ROOT . '/language/'.$languageFile.'/' . $filename))
			{
				JFile::copy(JPATH_ROOT . '/language/'.$languageFile.'/' . $filename, JPATH_ROOT . '/language/'.$languageFile.'/bak.' . $filename);
			}
			*/
			/*
			$module_language_file = array($languageFile.'.mod_ospropertysearch.ini',$languageFile.'.mod_ospropertyloancal.ini',$languageFile.'.mod_ospropertymortgage.ini',$languageFile.'.mod_ospslideshow.ini');
			foreach($module_language_file as $filename){
				if (JFile::exists(JPATH_ROOT . '/language/'.$languageFile.'/' . $filename))
				{
					JFile::copy(JPATH_ROOT . '/language/'.$languageFile.'/' . $filename, JPATH_ROOT . '/language/'.$languageFile.'/bak.' . $filename);
				}
			}
			*/
		}			
	}	
	
	
	function install($parent)
	{
		com_install() ;
	}
	
	function update($parent)
	{
		com_install();
	}

	/**
	 * Method to run after installing the component
	 */
	public function postflight($type, $parent)
	{
		//Restore the modified language strings by merging to language files
		foreach (self::$languageFiles as $languageFile)
		{
			$registry = new Registry;
			$filename = $languageFile.'.com_osproperty.ini';
			$backupFile  = JPATH_ROOT . '/language/'.$languageFile.'/bak.' . $filename;
			$currentFile = JPATH_ROOT . '/language/'.$languageFile.'/' . $filename;
			if (File::exists($currentFile) && File::exists($backupFile))
			{
				$registry->loadFile($currentFile, 'INI');
				$currentItems = $registry->toArray();
				$registry->loadFile($backupFile, 'INI');
				$backupItems = $registry->toArray();
				foreach ($backupItems as $key => $value)
				{
					$updateConstant = ['OS_COM_USERS_EMAIL_REGISTERED_BODY_NOPW','OS_COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW','OS_COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW','OS_COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY','OS_COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_BODY'];
					if(in_array($key, $updateConstant))
					{
						unset($backupItems[$key]);
					}
				}
				$items       = array_merge($currentItems, $backupItems);
				$content     = "";
				foreach ($items as $key => $value)
				{
					$content .= "$key=\"$value\"\n";
				}
				File::write($currentFile, $content);
			}
			
			$registry = new Registry;
			$backupFile  = JPATH_ROOT . '/administrator/language/'.$languageFile.'/bak.' . $filename;
			$currentFile = JPATH_ROOT . '/administrator/language/'.$languageFile.'/' . $filename;
			if (File::exists($currentFile) && File::exists($backupFile))
			{
				$registry->loadFile($currentFile, 'INI');
				$currentItems = $registry->toArray();
				$registry->loadFile($backupFile, 'INI');
				$backupItems = $registry->toArray();
				$items       = array_merge($currentItems, $backupItems);
				$content     = "";
				foreach ($items as $key => $value)
				{
					$content .= "$key=\"$value\"\n";
				}
				File::write($currentFile, $content);
			}

			/*
			$module_language_file = array($languageFile.'.mod_ospropertysearch.ini',$languageFile.'.mod_ospropertyloancal.ini',$languageFile.'.mod_ospropertymortgage.ini',$languageFile.'.mod_ospslideshow.ini');
			foreach($module_language_file as $filename){
				$registry = new JRegistry();
				$backupFile  = JPATH_ROOT . '/language/'.$languageFile.'/bak.' . $filename;
				$currentFile = JPATH_ROOT . '/language/'.$languageFile.'/' . $filename;
				if (JFile::exists($currentFile) && JFile::exists($backupFile))
				{
					$registry->loadFile($currentFile, 'INI');
					$currentItems = $registry->toArray();
					$registry->loadFile($backupFile, 'INI');
					$backupItems = $registry->toArray();
					$items       = array_merge($currentItems, $backupItems);
					$content     = "";
					foreach ($items as $key => $value)
					{
						$content .= "$key=\"$value\"\n";
					}
					JFile::write($currentFile, $content);
				}
			}
			*/
		}
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->clear()
			->update('#__extensions')
			->set('enabled = 1')
			->where('element = "osproperty"')
			->where('folder = "installer"');
		$db->setQuery($query)
			->execute();
	}
}

function com_install() 
{
	jimport('joomla.filesystem.file') ;
    jimport('joomla.filesystem.folder') ;
    define('DS',DIRECTORY_SEPARATOR);
    $db = & Factory::getDBO();
	$db->setQuery("Select count(extension_id) from #__extensions where `element` = 'jdonation' and `folder` = 'installer'");
	$count = $db->loadResult();
    
    $config = new JConfig();
    $dbname = $config->db;
    $prefix = $config->dbprefix;
    
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_configuration'");
    $count = $db->loadResult();
    if($count == 0){
    	$configSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/install.osproperty.sql' ;
    	$sql = file_get_contents($configSql) ;
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
    }
    
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_neighborhood'");
    $count = $db->loadResult();
    if($count == 0){
    	$configSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/neighborhood.osproperty.sql' ;
    	$sql = file_get_contents($configSql) ;
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
    }

	$db->setQuery("SHOW COLUMNS FROM #__osrs_neighborhood");
	$fields = $db->loadObjectList();
    if(count($fields) > 0)
	{
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++)
		{
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('distance',$fieldArr))
		{
    		$db->setQuery("ALTER TABLE `#__osrs_neighborhood` ADD `distance` DECIMAL(7,2) NULL DEFAULT '0' ;");
    		$db->execute();
    	}
    }
    
    $db->setQuery("ALTER TABLE `#__osrs_configuration` CHANGE `fieldvalue` `fieldvalue` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ");
    $db->execute();
    
    
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_company_agents'");
    $count = $db->loadResult();
    if($count == 0){
    	$configSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/companyagents.osproperty.sql' ;
    	$sql = file_get_contents($configSql) ;
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
    }

    
    //update city
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_cities'");    
    $count = $db->loadResult();
    if($count == 0){ //the city tables doesn't exists
    	$configSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/cities.osproperty.sql' ;
    	$sql = file_get_contents($configSql) ;
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
    }else{
    	$db->setQuery("SELECT COUNT(id) FROM #__osrs_cities");
    	$count = $db->loadResult();
    	if($count == 0){
    		$configSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/cities.osproperty.sql' ;
	    	$sql = file_get_contents($configSql) ;
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
    	}
    }
    
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_currencies'");    
    $count = $db->loadResult();
    if($count == 0){ //the currency tables doesn't exists
	    //currency
	    $configSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/currency.osproperty.sql' ;
		$sql = file_get_contents($configSql) ;
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
    }
    
    $db->setQuery("SELECT COUNT(id) FROM #__osrs_currencies");
    $count = $db->loadResult();
    if($count == 0){//in case count currency = 0, import currency data
    	$configSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/currency.osproperty.sql' ;
		$sql = file_get_contents($configSql) ;
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
    }
    
    $db->setQuery("ALTER TABLE `#__osrs_currencies` CHANGE `currency_symbol` `currency_symbol` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");
    $db->execute();
    //update Russian Rubbie
    $db->setQuery("Update #__osrs_currencies set currency_symbol = '&#1088;&#1091;&#1073;' where id = '44'");
    $db->execute();

	$db->setQuery("SHOW COLUMNS FROM #__osrs_currencies");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('published',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_currencies` ADD `published` tinyint(1) NOT NULL DEFAULT '1' ;");
    		$db->execute();
    	}
    }

	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_property_bath_values'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_property_bath_values` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `pid` int(11) NOT NULL DEFAULT '0',
					  `bath_label` varchar(255) DEFAULT NULL,
					  `bath_value` decimal(5,2) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }

	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_request_logs'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_request_logs` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `pid` int(11) DEFAULT NULL,
					  `request_type` tinyint(1) NOT NULL,
					  `request_name` varchar(50) DEFAULT NULL,
					  `request_email` varchar(50) DEFAULT NULL,
					  `request_phone` varchar(50) DEFAULT NULL,
					  `subject` varchar(200) DEFAULT NULL,
					  `request_body` text DEFAULT NULL,
					  `number_adults` tinyint(2) UNSIGNED DEFAULT NULL,
					  `number_childs` tinyint(2) UNSIGNED DEFAULT NULL,
					  `check_in` date DEFAULT NULL,
					  `check_out` date DEFAULT NULL,
					  `requested_on` date DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_report'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_report` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `report_ip` varchar(50) DEFAULT NULL,
					  `item_type` tinyint(1) unsigned DEFAULT NULL,
					  `report_reason` varchar(255) DEFAULT NULL,
					  `report_details` text,
					  `report_email` varchar(100) DEFAULT NULL,
					  `item_id` int(11) DEFAULT NULL,
					  `frontend_url` varchar(255) DEFAULT NULL,
					  `backend_url` varchar(255) DEFAULT NULL,
					  `report_on` int(11) DEFAULT '0',
					  `is_checked` tinyint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }

	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_property_price_history'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_property_price_history` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `pid` int(11) DEFAULT NULL,
					  `date` date DEFAULT NULL,
					  `event` varchar(255) DEFAULT NULL,
					  `price` decimal(12,2) DEFAULT NULL,
					  `source` varchar(255) DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    

	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_property_open'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_property_open` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `pid` int(11) DEFAULT NULL,
					  `start_from` datetime DEFAULT NULL,
					  `end_to` datetime DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }

	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_property_history_tax'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_property_history_tax` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `pid` int(11) DEFAULT NULL,
					  `tax_year` int(4) DEFAULT NULL,
					  `property_tax` decimal(10,2) DEFAULT NULL,
					  `tax_change` decimal(10,2) DEFAULT NULL,
					  `tax_assessment` decimal(10,2) DEFAULT NULL,
					  `tax_assessment_change` decimal(10,2) DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    
	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_extra_field_types'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_extra_field_types` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `fid` int(11) DEFAULT NULL,
						  `type_id` int(11) DEFAULT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
						");
    	$db->execute();
    	
    	$db->setQuery("Select id from #__osrs_types");
    	$types = $db->loadObjectList();
    	
    	$db->setQuery("Select id from #__osrs_extra_fields");
    	$fields = $db->loadObjectList();
    	
    	foreach ($fields as $field){
    		foreach ($types as $type){
    			$db->setQuery("Insert into #__osrs_extra_field_types (id,fid,type_id) values (NULL,'$field->id','$type->id')");
    			$db->execute();
    		}
    	}
    }

    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_tags'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_tags` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `keyword` varchar(255) DEFAULT NULL,
					  `published` tinyint(1) unsigned DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    
	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_property_categories'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_property_categories` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `pid` int(11) DEFAULT NULL,
						  `category_id` int(11) DEFAULT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    	
    	$db->setQuery("Select id,category_id from #__osrs_properties");
    	$properties = $db->loadObjectList();
    	if(count($properties) > 0){
    		foreach ($properties as $property){
    			$db->setQuery("Insert into #__osrs_property_categories (id, pid, category_id) values (NULL,'$property->id','$property->category_id')");
    			$db->execute();
    		}
    	}
    }
    
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_tag_xref'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_tag_xref` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `tag_id` int(11) DEFAULT NULL,
					  `pid` int(1) DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_themes'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_themes` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(255) DEFAULT NULL,
					  `title` varchar(100) DEFAULT NULL,
					  `author` varchar(60) DEFAULT NULL,
					  `creation_date` varchar(50) DEFAULT NULL,
					  `copyright` varchar(100) DEFAULT NULL,
					  `license` varchar(255) DEFAULT NULL,
					  `author_email` varchar(50) DEFAULT NULL,
					  `author_url` varchar(50) DEFAULT NULL,
					  `version` varchar(40) DEFAULT NULL,
					  `description` text,
					  `params` text,
					  `support_mobile_device` tinyint(1) unsigned DEFAULT NULL,
					  `published` tinyint(1) unsigned DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
        
    $db->setQuery("Select count(id) from #__osrs_themes");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("INSERT INTO `#__osrs_themes` (`id`, `name`, `title`, `author`, `creation_date`, `copyright`, `license`, `author_email`, `author_url`, `version`, `description`, `params`, `support_mobile_device`, `published`) VALUES
(1, 'default', 'Default theme', 'Dang Thuc Dam', '2013-03-01 00:00:00', 'http://joomdonation.com', NULL, 'damdt@joomservices.com', 'http://osproperty.ext4joomla.com', '1.0', 'This is default template of OS Property component', NULL, 1, 1),
(3, 'theme1', 'OS Property Template 1', 'Dang Thuc Dam', '26-05-2013', 'Copyright 2007-2013 Ossolution Team', 'http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2', 'damdt@joomservices.com', 'www.joomdonation.com', '1.0', 'Template 1 for OS Property component', NULL, 1, 0),
(5, 'theme2', 'OSP Responsive theme 2 - Bootstrap twitter supported', 'Dang Thuc Dam', '26-05-2013', 'Copyright 2007-2013 Ossolution Team', 'http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2', 'damdt@joomservices.com', 'www.joomdonation.com', '1.0', 'OSP Responsive theme 2 - Bootstrap twitter supported. This theme uses Twitter Bootstrap 2.3.2, it can display correctly if you''re using a Joomla template which isn''t a responsive design.', NULL, 1, 0);");
    	$db->execute();
    }

	$db->setQuery("Select count(id) from #__osrs_themes where `name` like 'theme1'");
	$count = $db->loadResult();
    if($count == 0){
		$db->setQuery("INSERT INTO `#__osrs_themes` (`id`, `name`, `title`, `author`, `creation_date`, `copyright`, `license`, `author_email`, `author_url`, `version`, `description`, `params`, `support_mobile_device`, `published`) VALUES (NULL, 'theme1', 'OSP Responsive theme - Bootstrap twitter supported', 'Dang Thuc Dam', '26-05-2013', 'Copyright 2007-2013 Ossolution Team', 'http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2', 'damdt@joomservices.com', 'www.joomdonation.com', '1.0', 'OSP Responsive theme - Bootstrap twitter supported. This theme uses Twitter Bootstrap 2.3.2, it can display correctly if you are using a Joomla template which is not a responsive design.', NULL, 1, 0);");
		$db->execute();
	}

    $db->setQuery("Select count(id) from #__osrs_themes where `name` like 'theme2'");
	$count = $db->loadResult();
    if($count == 0){
		$db->setQuery("INSERT INTO `#__osrs_themes` (`id`, `name`, `title`, `author`, `creation_date`, `copyright`, `license`, `author_email`, `author_url`, `version`, `description`, `params`, `support_mobile_device`, `published`) VALUES (NULL, 'theme2', 'OSP Responsive theme 2 - Bootstrap twitter supported', 'Dang Thuc Dam', '26-05-2013', 'Copyright 2007-2013 Ossolution Team', 'http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2', 'damdt@joomservices.com', 'www.joomdonation.com', '1.0', 'OSP Responsive theme 2 - Bootstrap twitter supported. This theme uses Twitter Bootstrap 2.3.2, it can display correctly if you are using a Joomla template which is not a responsive design.', NULL, 1, 0);");
		$db->execute();
	}
	
	$db->setQuery("Select count(id) from #__osrs_themes where `name` like 'theme_black'");
	$count = $db->loadResult();
    if($count == 0){
		$db->setQuery("INSERT INTO `#__osrs_themes` (`id`, `name`, `title`, `author`, `creation_date`, `copyright`, `license`, `author_email`, `author_url`, `version`, `description`, `params`, `support_mobile_device`, `published`) VALUES (NULL, 'theme_black', 'OSP Responsive Black Transparent theme', 'Dang Thuc Dam', '26-05-2013', 'Copyright 2007-2013 Ossolution Team', 'http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2', 'damdt@joomservices.com', 'www.joomdonation.com', '1.0', 'OSP Responsive Black Transparent theme - Bootstrap twitter supported. This theme uses Twitter Bootstrap 2.3.2, it can display correctly if you are using a Joomla template which is not a responsive design.', NULL, 1, 0);");
		$db->execute();
	}
	
	$db->setQuery("Select count(id) from #__osrs_themes where `name` like 'blue'");
	$count = $db->loadResult();
    if($count == 0){
		$db->setQuery("INSERT INTO `#__osrs_themes` (`id`, `name`, `title`, `author`, `creation_date`, `copyright`, `license`, `author_email`, `author_url`, `version`, `description`, `params`, `support_mobile_device`, `published`) VALUES(NULL, 'blue', 'OSP Blue Responsive - Bootstrap twitter supported', 'Dang Thuc Dam', '26-09-2013', 'Copyright 2007-2013 Ossolution Team', 'http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2', 'damdt@joomservices.com', 'www.joomdonation.com', '1.0', 'OSP Blue Responsive - Bootstrap twitter supported. This theme uses Twitter Bootstrap 2.3.2, it can display correctly if you\'re using a Joomla template which isn\'t a responsive design.', NULL, 0, 0);");
		$db->execute();
	}
	
	$db->setQuery("Select count(id) from #__osrs_themes where `name` like 'theme3'");
	$count = $db->loadResult();
    if($count == 0){
		$db->setQuery("INSERT INTO `#__osrs_themes` (`id`, `name`, `title`, `author`, `creation_date`, `copyright`, `license`, `author_email`, `author_url`, `version`, `description`, `params`, `support_mobile_device`, `published`) VALUES(NULL, 'theme3', 'Theme 3', 'Dang Thuc Dam', '24-04-14 15:38:11', 'Copyright 2007-2014 Ossolution Team', NULL, 'damdt@joomservices.com', 'www.joomdonation.com', '1.0', 'Theme 3 - CSS 3 theme', 'ncolumns=\"1\"\nthemeBackgroundColor=\"#88C354\"', 1, 0);");
		$db->execute();
	}
	
    //create table #__osrs_user_list
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_user_list'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_user_list` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `user_id` int(11) NOT NULL DEFAULT '0',
					  `list_name` varchar(255) NOT NULL,
					  `receive_email` tinyint(1) NOT NULL DEFAULT '0',
					  `lang` varchar(20) NOT NULL,
					  `created_on` datetime NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    
    
    //create table #__osrs_user_list_details
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_init'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list_details tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_init` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(255) NOT NULL,
					  `value` int(255) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }   

    
    //create table #__osrs_user_list_details
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_user_list_details'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list_details tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_user_list_details` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `list_id` int(11) NOT NULL DEFAULT '0',
						  `field_id` varchar(100) NOT NULL,
						  `field_type` tinyint(1) NOT NULL DEFAULT '0',
						  `search_param` varchar(100) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    
    $db->setQuery("SHOW COLUMNS FROM #__osrs_user_list_details");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('search_type',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_user_list_details` ADD `search_type` varchar(50) NOT NULL AFTER `field_type` ;");
    		$db->execute();
    	}
    }

	$db->setQuery("SHOW COLUMNS FROM #__osrs_user_list");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('receive_email',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_user_list` ADD `receive_email` tinyint(1) NOT NULL DEFAULT '0' NOT NULL AFTER `list_name` ;");
    		$db->execute();
    	}
		if(!in_array('lang',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_user_list` ADD `lang` varchar(20) NOT NULL AFTER `receive_email` ;");
    		$db->execute();
    	}
    }
    
    //create table #__osrs_urls
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_urls'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list_details tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_urls` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `md5_key` text,
						  `query` text,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    
    //create table #__osrs_menus
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_menus'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_menus` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `menu_name` varchar(255) NOT NULL,
					  `menu_icon` varchar(255) NOT NULL,
					  `parent_id` int(11) NOT NULL DEFAULT '0',
					  `menu_task` varchar(255) NOT NULL,
					  `ordering` int(11) NOT NULL DEFAULT '0',
					  `published` tinyint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    
    $db->setQuery("Select count(id) from `#__osrs_menus`");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("INSERT INTO `#__osrs_menus` (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES
						(1, 'OS_DASHBOARD', 'icon-home', 0, 'cpanel_list', 1, 1),
						(2, 'OS_PROPERTY_INFORMATION', 'icon-list', 0, '', 1, 1),
						(3, 'OS_MANAGE_PROPERTY_TYPES', '', 2, 'type_list', 2, 1),
						(4, 'OS_MANAGE_CATEGORIES', '', 2, 'categories_list', 3, 1),
						(5, 'OS_MANAGE_PROPERTIES', '', 2, 'properties_list', 4, 1),
						(6, 'OS_MANAGE_EXTRA_FIELD_GROUPS', '', 2, 'fieldgroup_list', 5, 1),
						(7, 'OS_MANAGE_EXTRA_FIELDS', '', 2, 'extrafield_list', 6, 1),
						(8, 'OS_PROPERTY_OWNER', 'icon-user', 0, '', 3, 1),
						(9, 'OS_MANAGE_COMPANIES', '', 8, 'companies_list', 1, 1),
						(10, 'OS_MANAGE_AGENTS', '', 8, 'agent_list', 2, 1),
						(11, 'OS_LOCATION', 'icon-location', 0, '', 3, 1),
						(12, 'OS_MANAGE_STATES', '', 11, 'state_list', 1, 1),
						(13, 'OS_MANAGE_CITY', '', 11, 'city_list', 2, 1),
						(14, 'OS_OTHER', 'icon-wrench', 0, '', 4, 1),
						(15, 'OS_MANAGE_PRICELIST', '', 14, 'pricegroup_list', 1, 1),
						(16, 'OS_MANAGE_EMAIL_FORMS', '', 14, 'email_list', 2, 1),
						(17, 'OS_MANAGE_COMMENTS', '', 14, 'comment_list', 3, 1),
						(18, 'OS_TRANSLATION', '', 14, 'translation_list', 4, 1),
						(19, 'OS_CSV_IMPORT', '', 14, 'form_default', 9, 1),
						(20, 'OS_MANAGE_THEMES', '', 14, 'theme_list', 6, 1),
						(23, 'OS_CONFIGURATION', 'icon-cog', 0, 'configuration_list', 6, 1),
						(24, 'OS_MANAGE_CONVENIENCE', '', 14, 'amenities_list', 5, 1),
						(25, 'OS_EXPORT_CSV', '', 14, 'csvexport_default', 10, 1),
						(26, 'OS_MANAGE_TAGS', '', 14, 'tag_list', 11, 1),
						(27, 'OS_IMPORT_XML', '', 14, 'xml_defaultimport', 10, 1),
						(28, 'OS_EXPORT_XML', '', 14, 'xml_default', 10, 1),
						(29, 'JTOOLBAR_HELP', 'icon-support', 0, 'configuration_help', 8, 1),
						(30, 'OS_MANAGE_PAYMENT_PLUGINS', '', 14, 'plugin_list', 12, 1),
						(31, 'OS_MANAGE_TRANSACTION', '', 14, 'transaction_list', 13, 1),
						(32, 'OS_TOOLS', 'icon-tools', 0, '', 7, 1),
						(33, 'OS_OPTIMIZE_SEF_URLS', '', 32, 'properties_sefoptimize', 1, 1),
						(34, 'OS_FIX_DATABASE_SCHEMA', '', 32, 'properties_fixdatabase', 2, 1),
						(35, 'OS_SHARE_TRANSLATION', '', 32, 'properties_sharetranslation', 3, 1),
						(36, 'OS_REMOVE_ORPHAN_PROPERTIES', '', 32, 'properties_removeorphan', 4, 1),
						(37, 'OS_MANAGE_COUNTRIES', '', 11, 'country_list', 0, 1);");
    	$db->execute();
    }

	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_hit_statistic'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_hit_statistic` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `pid` int(11) DEFAULT NULL,
					  `hit_date` date DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_property_listing_layout'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_user_list tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_property_listing_layout` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `itemid` int(11) DEFAULT NULL,
					  `category_id` int(11) DEFAULT NULL,
					  `type_id` int(11) DEFAULT NULL,
					  `country_id` int(11) DEFAULT NULL,
					  `company_id` int(11) DEFAULT NULL,
					  `featured` tinyint(1) unsigned DEFAULT NULL,
					  `sold` tinyint(1) NOT NULL DEFAULT '0',
					  `state_id` int(11) DEFAULT NULL,
					  `agenttype` tinyint(2) unsigned DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
    
	$db->setQuery("SHOW COLUMNS FROM #__osrs_property_listing_layout");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('sold',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_property_listing_layout` ADD `sold` tinyint(1) NOT NULL DEFAULT '0' AFTER `featured` ;");
    		$db->execute();
    	}
	}

    $db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_MANAGE_CONVENIENCE'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_MANAGE_CONVENIENCE','','14','amenities_list',9,1)");
    	$db->execute();
    }
    
    $db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_EXPORT_CSV'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_EXPORT_CSV','','14','csvexport_default',10,1)");
    	$db->execute();
    }
    
    $db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_MANAGE_TAGS'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_MANAGE_TAGS','','14','tag_list',11,1)");
    	$db->execute();
    }

	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_IMPORT_XML'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_IMPORT_XML','','14','xml_defaultimport',10,1)");
    	$db->execute();
    }

	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_EXPORT_XML'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_EXPORT_XML','','14','xml_default',10,1)");
    	$db->execute();
    }

	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'JTOOLBAR_HELP'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'JTOOLBAR_HELP','icon-support','0','configuration_help',7,1)");
    	$db->execute();
    }

	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_MANAGE_PAYMENT_PLUGINS'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_MANAGE_PAYMENT_PLUGINS','','14','plugin_list',12,1)");
    	$db->execute();
    }

	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_MANAGE_TRANSACTION'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_MANAGE_TRANSACTION','','14','transaction_list',13,1)");
    	$db->execute();
    }

	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_TOOLS'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_TOOLS','icon-tools','0','',7,1)");
    	$db->execute();
		$tool_menu_id = $db->insertId();
    }else{
		$db->setQuery("Select id from #__osrs_menus where `menu_name` like 'OS_TOOLS'");
		$tool_menu_id = $db->loadResult();
	}

	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_OPTIMIZE_SEF_URLS'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_OPTIMIZE_SEF_URLS','', '$tool_menu_id', 'properties_sefoptimize', 1, 1)");
    	$db->execute();
    }
	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_FIX_DATABASE_SCHEMA'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_FIX_DATABASE_SCHEMA','', '$tool_menu_id', 'properties_fixdatabase', 2, 1)");
    	$db->execute();
    }
	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_SHARE_TRANSLATION'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_SHARE_TRANSLATION','', '$tool_menu_id', 'properties_sharetranslation', 3, 1)");
    	$db->execute();
    }
	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_MANAGE_COUNTRIES'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_MANAGE_COUNTRIES','', '11', 'country_list', 0, 1)");
    	$db->execute();
    }
	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_REMOVE_ORPHAN_PROPERTIES'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_REMOVE_ORPHAN_PROPERTIES','', '32', 'properties_removeorphan', 4, 1)");
    	$db->execute();
    }
	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_UPDATE_COORDINATES'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_UPDATE_COORDINATES','', '32', 'properties_updatecoordinates', 5, 1)");
    	$db->execute();
    }

	$db->setQuery("Select count(id) from #__osrs_menus where `menu_name` like 'OS_REQUESTS_LOG'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("Insert into #__osrs_menus (`id`, `menu_name`, `menu_icon`, `parent_id`, `menu_task`, `ordering`, `published`) VALUES (NULL,'OS_REQUESTS_LOG','', '32', 'request_logs', 6, 1)");
    	$db->execute();
    }

	$db->setQuery("Delete from #__osrs_menus where `menu_name` = 'OS_BACKUP'");
	$db->execute();

	$db->setQuery("Delete from #__osrs_menus where `menu_name` = 'OS_RESTORE'");
	$db->execute();

	$db->setQuery("Update #__osrs_menus set `ordering` = '9' where menu_name like 'OS_CSV_IMPORT'");
	$db->execute();

	$db->setQuery("Update #__osrs_menus set `ordering` = '5' where menu_name like 'OS_MANAGE_CONVENIENCE'");
	$db->execute();

	$db->setQuery("Update #__osrs_menus set `menu_icon` = 'icon-location' where menu_name like 'OS_LOCATION'");
	$db->execute();

	$db->setQuery("Update #__osrs_menus set `menu_icon` = 'icon-list' where menu_name like 'OS_PROPERTY_INFORMATION'");
	$db->execute();

	$db->setQuery("Update #__osrs_menus set `menu_icon` = 'icon-wrench' where menu_name like 'OS_OTHER'");
	$db->execute();

	$db->setQuery("Update #__osrs_menus set `menu_icon` = 'icon-cog' where menu_name like 'OS_CONFIGURATION'");
	$db->execute();

	$db->setQuery("Update #__osrs_menus set `ordering` = '8' where menu_name like 'JTOOLBAR_HELP'");
	$db->execute();

	//create table #__osrs_plugins
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_plugins'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_plugins` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(255) DEFAULT NULL,
					  `title` varchar(100) DEFAULT NULL,
					  `author` varchar(250) DEFAULT NULL,
					  `creation_date` datetime DEFAULT NULL,
					  `copyright` varchar(255) DEFAULT NULL,
					  `license` varchar(255) DEFAULT NULL,
					  `author_email` varchar(50) DEFAULT NULL,
					  `author_url` varchar(50) DEFAULT NULL,
					  `version` varchar(50) DEFAULT NULL,
					  `description` varchar(255) DEFAULT NULL,
					  `params` text,
					  `ordering` int(11) DEFAULT NULL,
					  `published` tinyint(3) unsigned DEFAULT NULL,
					  `support_recurring_subscription` tinyint(4) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
	
    $db->setQuery("SHOW COLUMNS FROM #__osrs_properties");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('ref',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `ref` varchar(50) NOT NULL DEFAULT '' AFTER `id` ;");
    		$db->execute();
    	}
    	if(!in_array('pro_alias',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `pro_alias` varchar(50) NOT NULL DEFAULT '' AFTER `pro_name` ;");
    		$db->execute();
    	}
		if(!in_array('pro_browser_title',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `pro_browser_title` varchar(50) NOT NULL DEFAULT '' AFTER `pro_alias` ;");
    		$db->execute();
    	}
    	if(!in_array('total_request_info',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `total_request_info` INT(11) NOT NULL DEFAULT '0' AFTER `total_points` ;");
    		$db->execute();
    	}
		if(!in_array('isSold',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `isSold` tinyint(1) NOT NULL DEFAULT '0' AFTER `isFeatured` ;");
    		$db->execute();
    	}
		if(!in_array('soldOn',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `soldOn` date NOT NULL AFTER `isSold` ;");
    		$db->execute();
    	}
  		if(!in_array('lot_size',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `lot_size` decimal(7,2) NOT NULL DEFAULT '0.00' AFTER `square_feet`;");
    		$db->execute();
    	}
		if(!in_array('posted_by',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `posted_by` tinyint(1) NOT NULL DEFAULT '0' AFTER `request_featured`;");
    		$db->execute();
    	}
		if(!in_array('company_id',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `company_id` int(11) NOT NULL DEFAULT '0' AFTER `agent_id`;");
    		$db->execute();
    	}

		if(!in_array('living_areas',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `living_areas` varchar(50) NOT NULL DEFAULT '';");
    		$db->execute();
    	}

		if(!in_array('panorama',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `panorama` TEXT NOT NULL DEFAULT '' AFTER `pro_pdf_file`;");
    		$db->execute();
    	}

		if(!in_array('price_text',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `price_text` TEXT NOT NULL DEFAULT '';");
    		$db->execute();
    	}

		if(!in_array('c_class',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `c_class` varchar(2) NOT NULL;");
    		$db->execute();
    	}

		if(!in_array('e_class',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `e_class` varchar(2) NOT NULL;");
    		$db->execute();
    	}

		for($p=1;$p<=9;$p++)
		{
			if(!in_array('pro_pdf_file'.$p,$fieldArr))
			{
				$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `pro_pdf_file".$p."` TEXT NOT NULL;");
				$db->execute();
			}
		}
	
		$varchar255 = array('garage_description','house_style','house_construction','exterior_finish','roof','flooring','basement_foundation','percent_finished','subdivision','land_holding_type','lot_dimensions','frontpage','depth','takings','returns','net_profit','business_type','stock','fixtures','fittings','percent_office','percent_warehouse','loading_facilities','fencing','rainfall','soil_type','grazing','cropping','irrigation','water_resources','carrying_capacity','storage');
		foreach($varchar255 as $field){
			if(!in_array($field,$fieldArr)){
				$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `".$field."` varchar(255) NOT NULL DEFAULT '';");
				$db->execute();
			}
		}

		$int4 = array('built_on','remodeled_on');
		foreach($int4 as $field){
			if(!in_array($field,$fieldArr)){
				$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `".$field."` int(4) NOT NULL DEFAULT '0';");
				$db->execute();
			}
		}

		$decimal10 = array('floor_area_lower','floor_area_main_level','floor_area_upper','floor_area_total','basement_size','land_area','total_acres');
		foreach($decimal10 as $field){
			if(!in_array($field,$fieldArr)){
				$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `".$field."` decimal(12,2) NOT NULL DEFAULT '0';");
				$db->execute();
			}
		}

		if(!in_array('ordering',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `ordering` INT(11) NOT NULL DEFAULT '0' ;");
    		$db->execute();
    	}
		
    }

    $db->setQuery("ALTER TABLE `#__osrs_properties` CHANGE `bath_room` `bath_room` DECIMAL(4,2) NULL;");
    $db->execute();

	$db->setQuery("ALTER TABLE `#__osrs_properties` CHANGE `soldOn` `soldOn` DATE NULL; ");
    $db->execute();

	$db->setQuery("ALTER TABLE `#__osrs_properties` CHANGE `remove_date` `remove_date` DATE NULL; ");
    $db->execute();

	$db->setQuery("ALTER TABLE `#__osrs_properties` CHANGE `pro_pdf_file` `pro_pdf_file` VARCHAR(255) NULL DEFAULT ''; ");
    $db->execute();

	$db->setQuery("ALTER TABLE `#__osrs_extra_fields` CHANGE `maxlength` `maxlength` INT(4) NOT NULL DEFAULT '0';");
    $db->execute();

	$db->setQuery("SHOW COLUMNS FROM #__osrs_orders");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('agent_id',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `agent_id` int(11) DEFAULT '0' AFTER `id` ;");
    		$db->execute();
    	}
		if(!in_array('created_by',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `created_by` tinyint(1) NOT NULL DEFAULT '0' AFTER `agent_id` ;");
    		$db->execute();
    	}
		if(!in_array('payment_method',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `payment_method` varchar(50) NOT NULL AFTER `created_by` ;");
    		$db->execute();
    	}
		if(!in_array('x_card_num',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `x_card_num` varchar(20) NOT NULL AFTER `payment_method` ;");
    		$db->execute();
    	}
		if(!in_array('x_card_code',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `x_card_code` int(3) NOT NULL AFTER `x_card_num` ;");
    		$db->execute();
    	}
		if(!in_array('card_holder_name',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `card_holder_name` varchar(100) NOT NULL AFTER `x_card_code` ;");
    		$db->execute();
    	}
		if(!in_array('exp_year',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `exp_year` int(4) NOT NULL AFTER `card_holder_name` ;");
    		$db->execute();
    	}
		if(!in_array('exp_month',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `exp_month` tinyint(2) NOT NULL AFTER `exp_year` ;");
    		$db->execute();
    	}
		if(!in_array('card_type',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `card_type` varchar(50) NOT NULL AFTER `exp_month` ;");
    		$db->execute();
    	}
		if(!in_array('curr',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `curr` int(3) NOT NULL AFTER `total` ;");
    		$db->execute();
    	}
		if(!in_array('direction',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `direction` tinyint(1) NOT NULL DEFAULT '0' AFTER `curr` ;");
    		$db->execute();
    	}
		if(!in_array('payment_made',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `payment_made` tinyint(1) NOT NULL DEFAULT '0' AFTER `direction` ;");
    		$db->execute();
    	}
		if(!in_array('stripe_token',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `stripe_token` VARCHAR (255) NOT NULL DEFAULT '' after `message` ;");
    		$db->execute();
    	}
		if(!in_array('nonce',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_orders` ADD `nonce` VARCHAR (255) NOT NULL DEFAULT '' after `stripe_token` ;");
    		$db->execute();
    	}
    }

	$db->setQuery("SHOW COLUMNS FROM #__osrs_order_details");
	$fields = $db->loadObjectList();
	if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('type',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_order_details` ADD `type`  tinyint(1) NOT NULL DEFAULT '0' AFTER `pid` ;");
    		$db->execute();
    	}
    }

    $db->setQuery("SHOW COLUMNS FROM #__osrs_property_field_value");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('value_integer',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_property_field_value` ADD `value_integer` int(11) NOT NULL DEFAULT '0' AFTER `value` ;");
    		$db->execute();
    	}
    	if(!in_array('value_decimal',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_property_field_value` ADD `value_decimal` decimal(12,2) NOT NULL DEFAULT '0.0' AFTER `value_integer` ;");
    		$db->execute();
    	}
    	if(!in_array('value_date',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_property_field_value` ADD `value_date` date DEFAULT NULL AFTER `value_decimal` ;");
    		$db->execute();
    	}
    }

	$db->setQuery("ALTER TABLE `#__osrs_property_field_value` CHANGE `value_integer` `value_integer` INT(11) NULL DEFAULT '0', CHANGE `value_decimal` `value_decimal` DECIMAL(12,2) NOT NULL DEFAULT '0', CHANGE `value_date` `value_date` DATE NULL DEFAULT NULL; ");
	$db->execute();
    
    $db->setQuery("SHOW COLUMNS FROM #__osrs_categories");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('category_alias',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_categories` ADD `category_alias` varchar(255) NOT NULL DEFAULT '' AFTER `category_name` ;");
    		$db->execute();
    	}
		if(!in_array('category_meta',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_categories` ADD `category_meta` TEXT AFTER `category_alias` ;");
    		$db->execute();
    	}
		if(!in_array('category_ordering',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_categories` ADD `category_ordering` TINYINT(3) NOT NULL DEFAULT '0'; ");
    		$db->execute();
    	}
    }
    $db->setQuery("SHOW COLUMNS FROM #__osrs_types");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('type_alias',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_types` ADD `type_alias` varchar(255) NOT NULL DEFAULT '' AFTER `type_name` ;");
    		$db->execute();
    	}
		
   		if(!in_array('ordering',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_types` ADD `ordering` int(11) NOT NULL DEFAULT '0' AFTER `type_description` ;");
    		$db->execute();
    		//set up the ordering for property types
    		$db->setQuery("Select id from #__osrs_types");
    		$types = $db->loadObjectList();
    		if(count($types) > 0){
    			for($j=0;$j<count($types);$j++){
    				$db->setQuery("Update #__osrs_types set ordering = '$j' where id = '".$types[$j]->id."'");
    				$db->execute();
    			}
    		}
    	}

		if(!in_array('price_type',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_types` ADD `price_type` tinyint(1) NOT NULL DEFAULT '0' AFTER `type_description` ;");
    		$db->execute();
    	}

		if(!in_array('type_icon',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_types` ADD `type_icon` varchar(255) NOT NULL DEFAULT '' AFTER `price_type` ;");
    		$db->execute();
    	}
    }
    
    $db->setQuery("SHOW COLUMNS FROM #__osrs_agents");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('alias',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agents` ADD `alias` varchar(255) NOT NULL DEFAULT '' AFTER `name` ;");
    		$db->execute();
    	}
		if(!in_array('featured',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agents` ADD `featured` tinyint(1) unsigned DEFAULT '0' AFTER `published` ;");
    		$db->execute();
    	}
		if(!in_array('bio',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agents` ADD `bio` TEXT AFTER `request_to_approval` ;");
    		$db->execute();
    	}
    	if(!in_array('agent_type',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agents` ADD `agent_type` tinyint(1) NOT NULL DEFAULT '0' AFTER `id` ;");
    		$db->execute();
    	}
		if(!in_array('optin',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agents` ADD `optin` tinyint(1) NOT NULL DEFAULT '0';");
    		$db->execute();
    	}
		if(!in_array('default_agent',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agents` ADD `default_agent` tinyint(1) NOT NULL DEFAULT '0';");
    		$db->execute();
    	}
    }
    
    //create the image folder for each properties
    $db->setQuery("Select id from #__osrs_properties");
    $pids = $db->loadOBjectList();
    if(count($pids) > 0){
    	require_once(JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
    	for($i=0;$i<count($pids);$i++){
    		$pid = $pids[$i];
    		OSPHelper::createPhotoDirectory($pid->id);
    		OSPHelper::movingPhoto($pid->id);
    	}
    }
    
	//price group table
	$db->setQuery("SHOW COLUMNS FROM #__osrs_pricegroups");
    $fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(in_array('display_price',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_pricegroups` DROP `display_price`;");
    		$db->execute();
    	}
    	if(in_array('price',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_pricegroups` DROP `price`;");
    		$db->execute();
    	}
    	if(!in_array('price_to',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_pricegroups` ADD `price_to` DECIMAL( 16, 2 ) NOT NULL DEFAULT '0' AFTER `id` ");
    		$db->execute();
    	}
    	if(!in_array('price_from',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_pricegroups` ADD `price_from` DECIMAL( 16, 2 ) NOT NULL DEFAULT '0' AFTER `id` ");
    		$db->execute();
    	}
    	if(!in_array('type_id',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_pricegroups` ADD `type_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `id` ;");
    		$db->execute();
    	}
    }
	
    $db->setQuery("ALTER TABLE `#__osrs_pricegroups` CHANGE `price_to` `price_to` DECIMAL( 16, 2 ) NULL DEFAULT NULL ");
    $db->execute();
    $db->setQuery("ALTER TABLE `#__osrs_pricegroups` CHANGE `price_from` `price_from` DECIMAL( 16, 2 ) NULL DEFAULT NULL ");
    $db->execute();
    
    $db->setQuery("SHOW COLUMNS FROM #__osrs_companies");
    $fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('user_id',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_companies` ADD `user_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `id` ;");
    		$db->execute();
    	}
    	if(!in_array('company_alias',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_companies` ADD `company_alias` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `company_name` ;");
    		$db->execute();
    	}
    	if(!in_array('request_to_approval',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_companies` ADD `request_to_approval` tinyint(1) NOT NULL DEFAULT '0' AFTER `company_description` ;");
    		$db->execute();
    	}
		if(!in_array('optin',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_companies` ADD `optin` tinyint(1) NOT NULL DEFAULT '0';");
    		$db->execute();
    	}
    }
    
    
    //ALTER TABLE `#__osrs_properties` ADD `curr` INT( 11 ) NOT NULL DEFAULT '0' AFTER `price_original` ;
    $db->setQuery("SHOW COLUMNS FROM #__osrs_properties");
    $fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('curr',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `curr` INT( 11 ) NOT NULL DEFAULT '0' AFTER `price_original` ;");
    		$db->execute();
    	}
    	
    	if(!in_array('energy',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `energy` DECIMAL( 6, 2 ) NOT NULL AFTER `parking` ;");
    		$db->execute();
    	}
    	
    	if(!in_array('climate',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_properties` ADD `climate` DECIMAL( 6, 2 ) NOT NULL AFTER `energy` ;");
    		$db->execute();
    	}
    	
    }
    
    $db->setQuery("SHOW COLUMNS FROM #__osrs_comments");
    $fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('ip_address',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_comments` ADD `ip_address` varchar(255) NOT NULL AFTER `content` ;");
    		$db->execute();
    	}
    	if(!in_array('country',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_comments` ADD `country` varchar(50) NOT NULL AFTER `content` ;");
    		$db->execute();
    	}
		$update_comment = 0;
		if(!in_array('rate1',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_comments` ADD `rate1` tinyint(1) NOT NULL DEFAULT 0 AFTER `user_id` ;");
    		$db->execute();
			$update_comment = 1;
    	}
		if(!in_array('rate2',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_comments` ADD `rate2` tinyint(1) NOT NULL DEFAULT 0 AFTER `rate1` ;");
    		$db->execute();
			$update_comment = 1;
    	}
		if(!in_array('rate3',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_comments` ADD `rate3` tinyint(1) NOT NULL DEFAULT 0 AFTER `rate2` ;");
    		$db->execute();
			$update_comment = 1;
    	}
		if(!in_array('rate4',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_comments` ADD `rate4` tinyint(1) NOT NULL DEFAULT 0 AFTER `rate3` ;");
    		$db->execute();
			$update_comment = 1;
    	}
		if($update_comment == 1){
			$db->setQuery("Update #__osrs_comments set rate1 = rate,rate2 = rate,rate3 = rate,rate4 = rate");
			$db->execute();
		}
    }
	$db->setQuery("ALTER TABLE `#__osrs_comments` CHANGE `country` `country` VARCHAR(50) NULL DEFAULT ''; ");
	$db->execute();
    
    $db->setQuery("SHOW COLUMNS FROM #__osrs_extra_fields");
    $fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('show_on_list',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_extra_fields` ADD `show_on_list` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `displaytitle` ;");
    		$db->execute();
    	}
    	if(!in_array('access',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_extra_fields` ADD `access` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `show_on_list` ;");
    		$db->execute();
    	}
    	if(!in_array('value_type',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_extra_fields` ADD `value_type` tinyint(1) NOT NULL DEFAULT '0' AFTER `maxlength` ;");
    		$db->execute();
    	}
		if(!in_array('clickable',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_extra_fields` ADD `clickable` tinyint(1) NOT NULL DEFAULT '0' AFTER `size` ;");
    		$db->execute();
    	}
    }
	
	$db->setQuery("SHOW COLUMNS FROM #__osrs_amenities");
    $fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
		if(!in_array('ordering',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_amenities` ADD `ordering` INT( 11 ) NOT NULL DEFAULT '0' AFTER `amenities` ;");
    		$db->execute();
    	}
    	if(!in_array('category_id',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_amenities` ADD `category_id` tinyint(2) NOT NULL DEFAULT '0' AFTER `id` ;");
    		$db->execute();
    	}
		if(!in_array('icon',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_amenities` ADD `icon` varchar(50) NOT NULL DEFAULT '' AFTER `amenities` ;");
    		$db->execute();
    	}
    }

	//check and add missing amenities
	/*
	addConvenience('Gas Hot Water','4');
	addConvenience('Central Air','6');
	addConvenience('Cable Internet','0');
	addConvenience('Cable TV','0');
	addConvenience('Electric Hot Water','0');
	addConvenience('Freezer','2');
	addConvenience('Swimming Pool','3');
	addConvenience('Skylights','0');
	addConvenience('Microwave','2');
	addConvenience('Sprinkler System','0');
	addConvenience('Wood Stove','0');
	addConvenience('Fruit Trees','5');
	addConvenience('Washer/Dryer','7');
	addConvenience('Dishwasher','2');
	addConvenience('Landscaping','7');
	addConvenience('Boat Slip','5');
	addConvenience('Burglar Alarm','8');
	addConvenience('Carpet Throughout','6');
	addConvenience('Central Vac','6');
	addConvenience('Covered Patio','5');
	addConvenience('Exterior Lighting','5');
	addConvenience('Fence','5');
	addConvenience('Fireplace','4');
	addConvenience('Garage','5');
	addConvenience('Garbage Disposal','2');
	addConvenience('Gas Fireplace','4');
	addConvenience('Gas Stove','4');
	addConvenience('Gazebo','5');
	addConvenience('Grill Top','2');
	addConvenience('Handicap Facilities','1');
	addConvenience('Jacuzi Tub','6');
	addConvenience('Lawn','7');
	addConvenience('Open Deck','5');
	addConvenience('Pasture','5');
	addConvenience('Pellet Stove','4');
	addConvenience('Propane Hot Water','4');
	addConvenience('Range/Oven','2');
	addConvenience('Refrigerator','2');
	addConvenience('RO Combo Gas/Electric','2');
	addConvenience('RV Parking','5');
	addConvenience('Satellite Dish','0');
	addConvenience('Spa/Hot Tub','5');
	addConvenience('Sprinkler System','8');
	addConvenience('Tennis Court','3');
	addConvenience('Football ground','3');
	addConvenience('Trash Compactor','2');
	addConvenience('Water Softener','0');
	addConvenience('Wheelchair Ramp','1');
	addConvenience('Wood Stove','4');
	*/
    
    $db->setQuery("SHOW COLUMNS FROM #__osrs_fieldgroups");
    $fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('access',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_fieldgroups` ADD `access` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `group_name` ;");
    		$db->execute();
    	}
    }
    
    
    //#__osrs_agent_account
    $db->setQuery("SHOW COLUMNS FROM #__osrs_agent_account");
    $fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(in_array('deadline_time',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` DROP `deadline_time` ;");
    		$db->execute();
    	}
    	
    	//number_listings
    	if(in_array('number_listings',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` DROP `number_listings` ;");
    		$db->execute();
    	}
    	
    	if(in_array('nplan',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` DROP `nplan` ;");
    		$db->execute();
    	}
    	
    	if(in_array('normal_listing',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` DROP `normal_listing` ;");
    		$db->execute();
    	}
    	
    	if(in_array('feature_listing',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` DROP `feature_listing` ;");
    		$db->execute();
    	}
    	
    	if(in_array('fplan',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` DROP `fplan` ;");
    		$db->execute();
    	}
    	
    	if(!in_array('sub_id',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` ADD `sub_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `id` ;");
    		$db->execute();
    	}
    	
    	if(!in_array('type',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` ADD `type` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `agent_id` ;");
    		$db->execute();
    	}
    	
    	if(!in_array('nproperties',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` ADD `nproperties` INT( 11 ) NOT NULL DEFAULT '0' AFTER `type` ;");
    		$db->execute();
    	}
    	
    	if(!in_array('status',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` ADD `status` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `nproperties` ;");
    		$db->execute();
    	}

		if(!in_array('company_id',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_agent_account` ADD company_id INT( 11 ) NOT NULL DEFAULT '0' AFTER `agent_id` ;");
    		$db->execute();
    	}
    }
    
    	
	$sql = 'SELECT COUNT(*) FROM #__osrs_configuration';
	$db->setQuery($sql) ;	
	$total = $db->loadResult();
	if (!$total) {		
		$configSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/configuration.osproperty.sql' ;
		$sql = file_get_contents($configSql) ;
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
	}
	
	//ALTER TABLE `#__osrs_companies` ADD `user_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `id` ;
    $db->setQuery("SHOW COLUMNS FROM #__osrs_states");
    $fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('published',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_states` ADD `published` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `state_code` ;");
    		$db->execute();
    	}
    }
    $db->setQuery("ALTER TABLE `#__osrs_states` CHANGE `state_name` `state_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ");
    $db->execute();
    $db->setQuery("ALTER TABLE `#__osrs_states` CHANGE `state_code` `state_code` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ");
    $db->execute();
    
    $db->setQuery("Select count(id) from #__osrs_states");
    $count = $db->loadResult();
    if($count == 0){
		$stateSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/states.osproperty.sql' ;
		$sql = file_get_contents($stateSql) ;
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
    }
	
	$sql = "SELECT COUNT(*) FROM #__osrs_amenities";
	$db->setQuery($sql);
	$total = $db->loadResult();
	
	if(! $total){
		$amenitiesSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/amenities.osproperty.sql' ;
		$sql = file_get_contents($amenitiesSql) ;
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
	}	

	
	$sql = "SELECT COUNT(*) FROM #__osrs_emails";
	$db->setQuery($sql);
	$total = $db->loadResult();
	
	if(! $total){
		$emailSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/emails.osproperty.sql' ;
		$sql = file_get_contents($emailSql) ;
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
	}

	$db->setQuery("ALTER TABLE `#__osrs_emails` CHANGE `email_content` `email_content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL; ");
	//$db->execute();

	$db->setQuery("Select count(id) from #__osrs_emails where email_key like 'email_alert'");
	$count_email_alert = $db->loadResult();
	if($count_email_alert == 0)
	{
		$content = '<h1 style=\"text-align: center;\"><strong>New properties uploaded</strong></h1>\r\n<p 
style=\"text-align: center;\">Dear customer, new properties have been uploaded that suit with your Saved Search list <strong>{listname}
</strong>. Please take a look at this them bellow</p>\r\n<p style=\"text-align: center;\"> {new_properties}</p>\r\n<p style=\"text-align: 
left;\"><em>If you do not want to receive this email, please click this link</em> {cancel_alert_email_link}</p>';
		$content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

		$db->setQuery("INSERT INTO `#__osrs_emails` (`id`, `email_key`, `email_title`, `email_content`, `published`) VALUES (NULL, 'email_alert', 'New properties uploaded', '".$content."', 1);");
		$db->execute();
	}

	$db->setQuery("Select count(id) from #__osrs_emails where email_key like 'offline_payment'");
	$count_offline_payment = $db->loadResult();
	if($count_offline_payment == 0)
	{
		$content = '<p>Dear {username},</p><p>Thank you for using our service</p><p>Payment details: Gateway: {gateway}</p><p>Item: {item}</p><p>Item Price: {price}</p><p>Date: {date}</p><p>Please send the offline payment ASAP to our bank account . Information of our bank account is as follow :</p><p><strong>Tuan Pham Ngoc, Ngan Hang Ngoai Thuong Vietcombank, Account Number XXX045485467</strong></p><p>We are looking forward to receiving your payment .</p><p>______________________________</p><p>Thank you, {site_name}</p><p>Administration Team</p>';
		$content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

		$db->setQuery("INSERT INTO `#__osrs_emails` (`id`, `email_key`, `email_title`, `email_content`, `published`) VALUES (NULL, 'offline_payment', 'Offline payment information', '".$content."', 1);");
		$db->execute();
	}

	$db->setQuery("Select count(id) from #__osrs_emails where email_key like 'payment_inform_to_administrator'");
	$count_offline_payment = $db->loadResult();
	if($count_offline_payment == 0)
	{
		$content = '<p>Dear Administrator,</p>\r\n<p>Your payment has been successfully accepted.</p>\r\n<p>Payment details: Gateway: {gateway}</p>\r\n<p>Transaction ID: {txn}</p>\r\n<p>Item: {item}</p>\r\n<p>Item Price: {price}</p>\r\n<p>Date: {date}</p>\r\n<p>______________________________</p>\r\n<p>Thank you, {site_name} Administration Team</p>';
		$content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

		$db->setQuery("INSERT INTO `#__osrs_emails` (`id`, `email_key`, `email_title`, `email_content`, `published`) VALUES(NULL, 'payment_inform_to_administrator', 'New payment made on your site', '".$content."', 1);");
		$db->execute();
	}

	$db->setQuery("Select count(id) from #__osrs_emails where email_key like 'new_agent_created'");
	$count_offline_payment = $db->loadResult();
	if($count_offline_payment == 0)
	{
		$content = 'Hi {username}, \r\n You have just been added as agent in our {business} system. \r\n Thanks. \r\n Admin';
		$content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

		$db->setQuery("INSERT INTO `#__osrs_emails` (`id`, `email_key`, `email_title`, `email_content`, `published`) VALUES(NULL, 'new_agent_created', 'New Agent Added', '".$content."', 1);");
		$db->execute();
	}

	$db->setQuery("Select count(id) from #__osrs_emails where email_key like 'new_company_created'");
	$count_offline_payment = $db->loadResult();
	if($count_offline_payment == 0)
	{
		$content = 'Hi {username}, \r\n You have just been added as company in our {business} system. \r\n Thanks. \r\n Admin';
		$content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

		$db->setQuery("INSERT INTO `#__osrs_emails` (`id`, `email_key`, `email_title`, `email_content`, `published`) VALUES(NULL, 'new_company_created', 'New Company Added', '".$content."', 1);");
		$db->execute();
	}
	
	//import csv if the csv tables doesn't exists
	$csvSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/csv.osproperty.sql' ;
	$sql = file_get_contents($csvSql) ;
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
	
	$db->setQuery("SHOW COLUMNS FROM #__osrs_csv_forms");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('yes_value',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `yes_value` varchar(50) NOT NULL DEFAULT '' AFTER `last_import`;");
    		$db->execute();
    	}
    	if(!in_array('no_value',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `no_value` varchar(50) NOT NULL DEFAULT '' AFTER `yes_value`;");
    		$db->execute();
    	}
    	if(!in_array('ftype',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `ftype` TINYINT(1) NOT NULL DEFAULT '0' AFTER `no_value`;");
    		$db->execute();
    	}
    	if(!in_array('type_id',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `type_id` INT(11) NOT NULL DEFAULT '0' AFTER `ftype`;");
    		$db->execute();
    	}
    	if(!in_array('fcategory',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `fcategory` TINYINT(1) NOT NULL DEFAULT '0' AFTER `type_id`;");
    		$db->execute();
    	}
    	if(!in_array('category_id',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `category_id` INT(11) NOT NULL DEFAULT '0' AFTER `fcategory`;");
    		$db->execute();
    	}
    	if(!in_array('agent_id',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `agent_id` INT(11) NOT NULL DEFAULT '0' AFTER `category_id`;");
    		$db->execute();
    	}
    	if(!in_array('country',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `country` INT(11) NOT NULL DEFAULT '0' AFTER `agent_id`;");
    		$db->execute();
    	}
    	if(!in_array('fstate',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `fstate` TINYINT(1) NOT NULL DEFAULT '0' AFTER `country`;");
    		$db->execute();
    	}
    	if(!in_array('state',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `state` INT(11) NOT NULL DEFAULT '0' AFTER `fstate`;");
    		$db->execute();
    	}
    	if(!in_array('fcity',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `fcity` TINYINT(1) NOT NULL DEFAULT '0' AFTER `state`;");
    		$db->execute();
    	}
    	if(!in_array('city',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `city` INT(11) NOT NULL DEFAULT '0' AFTER `fcity`;");
    		$db->execute();
    	}
		if(!in_array('image_type',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `image_type` tinyint(1) NOT NULL DEFAULT '0' AFTER `city`;");
    		$db->execute();
    	}
    	if(!in_array('update_type',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `update_type` tinyint(1) NOT NULL DEFAULT '0' AFTER `image_type`;");
    		$db->execute();
    	}
		if(!in_array('active_cron_import',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `active_cron_import` tinyint(1) NOT NULL DEFAULT '0' AFTER `update_type`;");
    		$db->execute();
    	}
		if(!in_array('csv_file',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_csv_forms` ADD `csv_file` varchar(255) NOT NULL DEFAULT '' AFTER `active_cron_import`;");
    		$db->execute();
    	}
    }
	
	
	$sql = "SELECT COUNT(*) FROM #__osrs_types";
	$db->setQuery($sql);
	$total = $db->loadResult();
	
	if(! $total){
		$typesSql = JPATH_ADMINISTRATOR.'/components/com_osproperty/sql/types.osproperty.sql' ;
		$sql = file_get_contents($typesSql) ;
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
	}
	
	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_importlog_properties'");
    $count = $db->loadResult();
    if($count == 0){ //the #__osrs_property_field_opt_value tables doesn't exists
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_importlog_properties` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `form_id` int(11) NOT NULL DEFAULT '0',
					  `pid` int(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
	
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_watermark'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_watermark` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `pid` int(11) NOT NULL DEFAULT '0',
					  `image` varchar(100) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }

	//create table #__osrs_list_properties
    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_list_properties'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_list_properties` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `pid` int(11) DEFAULT NULL,
					  `list_id` int(11) DEFAULT NULL,
					  `sent_notify` tinyint(1) unsigned DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }

	//create table `#__osrs_new_properties` 
	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_new_properties`'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_new_properties` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `pid` int(11) DEFAULT NULL,
					  `processed` tinyint(1) unsigned DEFAULT '0',
					  `is_new` tinyint(1) DEFAULT '1',
					  PRIMARY KEY (`id`),
					  KEY `pid` (`pid`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }

	$db->setQuery("SHOW COLUMNS FROM #__osrs_new_properties");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('is_new',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_new_properties` ADD `is_new` tinyint(1) DEFAULT '1' ;");
    		$db->execute();
    	}
    }

    $db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_xml'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_xml` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `filename` varchar(255) DEFAULT NULL,
					  `publish_properties` tinyint(1) NOT NULL DEFAULT '0',
					  `imported` int(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }

	$db->setQuery("SHOW COLUMNS FROM #__osrs_themes");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('default_duplicate',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_themes` ADD `default_duplicate` tinyint(1) NOT NULL DEFAULT '0' ;");
    		$db->execute();
    	}
    }

	$db->setQuery("SHOW COLUMNS FROM #__osrs_xml");
	$fields = $db->loadObjectList();
    if(count($fields) > 0){
    	$fieldArr = array();
    	for($i=0;$i<count($fields);$i++){
    		$field = $fields[$i];
    		$fieldname = $field->Field;
    		$fieldArr[$i] = $fieldname;
    	}
    	if(!in_array('publish_properties',$fieldArr)){
    		$db->setQuery("ALTER TABLE `#__osrs_xml` ADD `publish_properties` tinyint(1) NOT NULL DEFAULT '0'");
    		$db->execute();
    	}
	}

	$db->setQuery("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '".$prefix."osrs_xml_details'");
    $count = $db->loadResult();
    if($count == 0){
    	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__osrs_xml_details` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `xml_id` int(11) DEFAULT NULL,
					  `obj_content` text,
					  `imported` tinyint(1) unsigned zerofill DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    	$db->execute();
    }
 

	$db->setQuery("Select count(id) from #__osrs_countries");
	$count = $db->loadResult();
	if($count == 0){
		$db->setQuery("INSERT INTO `#__osrs_countries` (id,country_name,country_code) VALUES (1, 'Afghanistan', 'AF'),
						(2, 'Aland islands', 'AX'),
						(3, 'Albania', 'AL'),
						(4, 'Algeria', 'DZ'),
						(5, 'Andorra', 'AD'),
						(6, 'Angola', 'AO'),
						(7, 'Anguilla', 'AI'),
						(8, 'Antigua and Barbuda', 'AG'),
						(9, 'Argentina', 'AR'),
						(10, 'Armenia', 'AM'),
						(11, 'Aruba', 'AW'),
						(12, 'Australia', 'AU'),
						(13, 'Austria', 'AT'),
						(14, 'Azerbaijan', 'AZ'),
						(15, 'Bahamas', 'BS'),
						(16, 'Bahrain', 'BH'),
						(17, 'Bangladesh', 'BD'),
						(18, 'Barbados', 'BB'),
						(19, 'Belarus', 'BY'),
						(20, 'Belgium', 'BE'),
						(21, 'Belize', 'BZ'),
						(22, 'Benin', 'BJ'),
						(23, 'Bermuda', 'BM'),
						(24, 'Bhutan', 'BT'),
						(25, 'Bolivia', 'BO'),
						(26, 'Bosnia and Herzegovina', 'BA'),
						(27, 'Botswana', 'BW'),
						(28, 'Brazil', 'BR'),
						(29, 'Brunei Darussalam', 'BN'),
						(30, 'Bulgaria', 'BG'),
						(31, 'Burkina Faso', 'BF'),
						(32, 'Burundi', 'BI'),
						(33, 'Cambodia', 'KH'),
						(34, 'Cameroon', 'CM'),
						(35, 'Canada', 'CA'),
						(36, 'Cape Verde', 'CV'),
						(37, 'Central african republic', 'CF'),
						(38, 'Chad', 'TD'),
						(39, 'Chile', 'CL'),
						(40, 'China', 'CN'),
						(41, 'Colombia', 'CO'),
						(42, 'Comoros', 'KM'),
						(43, 'Republic of Congo', 'CG'),
						(44, 'The Democratic Republic of the Congo', 'CD'),
						(45, 'Costa Rica', 'CR'),
						(46, 'Cote d''Ivoire', 'CI'),
						(47, 'Croatia', 'HR'),
						(48, 'Cuba', 'CU'),
						(49, 'Cyprus', 'CY'),
						(50, 'Czech Republic', 'CZ'),
						(51, 'Denmark', 'DK'),
						(52, 'Djibouti', 'DJ'),
						(53, 'Dominica', 'DM'),
						(54, 'Dominican Republic', 'DO'),
						(55, 'Ecuador', 'EC'),
						(56, 'Egypt', 'EG'),
						(57, 'El salvador', 'SV'),
						(58, 'Equatorial Guinea', 'GQ'),
						(59, 'Eritrea', 'ER'),
						(60, 'Estonia', 'EE'),
						(61, 'Ethiopia', 'ET'),
						(62, 'Faeroe Islands', 'FO'),
						(63, 'Falkland Islands', 'FK'),
						(64, 'Fiji', 'FJ'),
						(65, 'Finland', 'FI'),
						(66, 'France', 'FR'),
						(67, 'French Guiana', 'GF'),
						(68, 'Gabon', 'GA'),
						(69, 'Gambia, the', 'GM'),
						(70, 'Georgia', 'GE'),
						(71, 'Germany', 'DE'),
						(72, 'Ghana', 'GH'),
						(73, 'Greece', 'GR'),
						(74, 'Greenland', 'GL'),
						(75, 'Grenada', 'GD'),
						(76, 'Guadeloupe', 'GP'),
						(77, 'Guatemala', 'GT'),
						(78, 'Guinea', 'GN'),
						(79, 'Guinea-Bissau', 'GW'),
						(80, 'Guyana', 'GY'),
						(81, 'Haiti', 'HT'),
						(82, 'Honduras', 'HN'),
						(83, 'Hong Kong', 'HK'),
						(84, 'Hungary', 'HU'),
						(85, 'Iceland', 'IS'),
						(86, 'India', 'IN'),
						(87, 'Indonesia', 'ID'),
						(88, 'Iran', 'IR'),
						(89, 'Iraq', 'IQ'),
						(90, 'Ireland', 'IE'),
						(91, 'Israel', 'IL'),
						(92, 'Italy', 'IT'),
						(93, 'Jamaica', 'JM'),
						(94, 'Japan', 'JP'),
						(95, 'Jordan', 'JO'),
						(96, 'Kazakhstan', 'KZ'),
						(97, 'Kenya', 'KE'),
						(98, 'North Korea', 'KP'),
						(99, 'South Korea', 'KR'),
						(100, 'Kuwait', 'KW'),
						(101, 'Kyrgyzstan', 'KG'),
						(102, 'Lao People''s Democratic Republic', 'LA'),
						(103, 'Latvia', 'LV'),
						(104, 'Lebanon', 'LB'),
						(105, 'Lesotho', 'LS'),
						(106, 'Liberia', 'LR'),
						(107, 'Libya', 'LY'),
						(108, 'Liechtenstein', 'LI'),
						(109, 'Lithuania', 'LT'),
						(110, 'Luxembourg', 'LU'),
						(111, 'Macedonia', 'MK'),
						(112, 'Madagascar', 'MG'),
						(113, 'Malawi', 'MW'),
						(114, 'Malaysia', 'MY'),
						(115, 'Mali', 'ML'),
						(116, 'Malta', 'MT'),
						(117, 'Martinique', 'MQ'),
						(118, 'Mauritania', 'MR'),
						(119, 'Mauritius', 'MU'),
						(120, 'Mexico', 'MX'),
						(121, 'Moldova', 'MD'),
						(122, 'Mongolia', 'MN'),
						(123, 'Montenegro', 'ME'),
						(124, 'Montserrat', 'MS'),
						(125, 'Morocco', 'MA'),
						(126, 'Mozambique', 'MZ'),
						(127, 'Myanmar', 'MM'),
						(128, 'Namibia', 'NA'),
						(129, 'Nepal', 'NP'),
						(130, 'Netherlands', 'NL'),
						(131, 'New Caledonia', 'NC'),
						(132, 'New Zealand', 'NZ'),
						(133, 'Nicaragua', 'NI'),
						(134, 'Niger', 'NE'),
						(135, 'Nigeria', 'NG'),
						(136, 'Norway', 'NO'),
						(137, 'Oman', 'OM'),
						(138, 'Pakistan', 'PK'),
						(139, 'Palau', 'PW'),
						(140, 'Palestinian Territories', 'PS'),
						(141, 'Panama', 'PA'),
						(142, 'Papua New Guinea', 'PG'),
						(143, 'Paraguay', 'PY'),
						(144, 'Peru', 'PE'),
						(145, 'Philippines', 'PH'),
						(146, 'Poland', 'PL'),
						(147, 'Portugal', 'PT'),
						(148, 'Puerto rico', 'PR'),
						(149, 'Qatar', 'QA'),
						(150, 'Reunion', 'RE'),
						(151, 'Romania', 'RO'),
						(152, 'Russian Federation', 'RU'),
						(153, 'Rwanda', 'RW'),
						(154, 'Saint Kitts and Nevis', 'KN'),
						(155, 'Saint Lucia', 'LC'),
						(156, 'Samoa', 'WS'),
						(157, 'Sao Tome and Principe', 'ST'),
						(158, 'Saudi Arabia', 'SA'),
						(159, 'Senegal', 'SN'),
						(160, 'Serbia', 'RS'),
						(161, 'Sierra Leone', 'SL'),
						(162, 'Singapore', 'SG'),
						(163, 'Slovakia', 'SK'),
						(164, 'Slovenia', 'SI'),
						(165, 'Solomon Islands', 'SB'),
						(166, 'Somalia', 'SO'),
						(167, 'South Africa', 'ZA'),
						(168, 'South Georgia and the South Sandwich Islands', 'GS'),
						(169, 'Spain', 'ES'),
						(170, 'Sri Lanka', 'LK'),
						(171, 'Sudan', 'SD'),
						(172, 'Suriname', 'SR'),
						(173, 'Svalbard and Jan Mayen', 'SJ'),
						(174, 'Swaziland', 'SZ'),
						(175, 'Sweden', 'SE'),
						(176, 'Switzerland', 'CH'),
						(177, 'Syrian Arab Republic', 'SY'),
						(178, 'Taiwan', 'TW'),
						(179, 'Tajikistan', 'TJ'),
						(180, 'Tanzania', 'TZ'),
						(181, 'Thailand', 'TH'),
						(182, 'Timor-Leste', 'TL'),
						(183, 'Togo', 'TG'),
						(184, 'Tonga', 'TO'),
						(185, 'Trinidad and Tobago', 'TT'),
						(186, 'Tunisia', 'TN'),
						(187, 'Turkey', 'TR'),
						(188, 'Turkmenistan', 'TM'),
						(189, 'Turks and Caicos Islands', 'TC'),
						(190, 'Uganda', 'UG'),
						(191, 'Ukraine', 'UA'),
						(192, 'United Arab Emirates', 'AE'),
						(193, 'United Kingdom', 'GB'),
						(194, 'United States', 'US'),
						(195, 'Uruguay', 'UY'),
						(196, 'Uzbekistan', 'UZ'),
						(197, 'Vanuatu', 'VU'),
						(198, 'Venezuela', 'VE'),
						(199, 'Viet nam', 'VN'),
						(200, 'Virgin Islands, British', 'VG'),
						(201, 'Western Sahara', 'EH'),
						(202, 'Yemen', 'YE'),
						(203, 'Zambia', 'ZM'),
						(204, 'Zimbabwe', 'ZW'),
						(205, 'KKTC', 'KKTC'),
						(206, 'Maldives', 'MV'),
						(207, 'Scotland', 'SL'),
						(208, 'Northen Ireland', 'NR'),
						(209, 'Wales', 'WA');");
		$db->execute();
	}
   
	
	//version 2.8.4 - Joomla User groups
	//Update properties, categories, extra fields and extra field groups
	$db->setQuery("Update #__osrs_properties set `access` = '1' where `access` = '0'");
    $db->execute();

	$db->setQuery("Update #__osrs_categories set `access` = '1' where `access` = '0'");
    $db->execute();

	$db->setQuery("Update #__osrs_extra_fields set `access` = '1' where `access` = '0'");
    $db->execute();

	$db->setQuery("Update #__osrs_fieldgroups set `access` = '1' where `access` = '0'");
    $db->execute();

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'default_access_level'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'default_access_level','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'min_price_slider'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'min_price_slider','0')");
		$db->execute();
	}
	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'min_price_slider' and fieldvalue like ''");
	$count = $db->loadResult();
	if(intval($count) > 0){
		$db->setQuery("UPDATE #__osrs_configuration set fieldvalue = '0' where fieldname like 'min_price_slider'");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'max_price_slider'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'max_price_slider','500000')");
		$db->execute();
	}
	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'max_price_slider' and fieldvalue like ''");
	$count = $db->loadResult();
	if(intval($count) > 0){
		$db->setQuery("UPDATE #__osrs_configuration set fieldvalue = '500000' where fieldname like 'max_price_slider'");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'price_step_amount'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'price_step_amount','1000')");
		$db->execute();
	}
	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'price_step_amount' and fieldvalue like ''");
	$count = $db->loadResult();
	if(intval($count) > 0){
		$db->setQuery("UPDATE #__osrs_configuration set fieldvalue = '1000' where fieldname like 'price_step_amount'");
		$db->execute();
	}
	
	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'default_sort_properties_by'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'default_sort_properties_by','a.isFeatured')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'default_sort_properties_type'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'default_sort_properties_type','desc')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'basement_foundation'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'basement_foundation','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'use_business'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'use_business','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'use_rural'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'use_rural','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'use_miscellaneous'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'use_miscellaneous','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'load_lazy'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'load_lazy','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'energy_class'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'energy_class','A,B,C,D,E,F,G')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'energy_value'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'energy_value','50,90,150,230,330,450')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'climate_class'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'climate_class','A,B,C,D,E,F,G')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'climate_value'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'climate_value','5,10,20,35,55,80')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'pdf_font'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'pdf_font','times')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'adddress_required'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'adddress_required','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'short_desc_required'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'short_desc_required','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'show_ref'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'show_ref','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'grabimages_backend'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'grabimages_backend','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'grabimages_frontend'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'grabimages_frontend','0')");
		$db->execute();
	}

	$db->setQuery("Update #__osrs_configuration set `fieldvalue` = 'AIzaSyA4t1no8tjaqP95plUJHYUewFow7RGTlEI' where fieldname like 'goole_aip_key' and `fieldvalue` = ''");
	$db->execute();

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'frontend_upload_type'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'frontend_upload_type','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'show_my_location'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'show_my_location','1')");
		$db->execute();
	}

	$db->setQuery("SELECT COUNT(id) FROM #__osrs_configuration WHERE fieldname like 'allowed_subjects'");
	$count = $db->loadResult();
	if(intval($count) == 0){
		$db->setQuery("INSERT INTO #__osrs_configuration (id,fieldname,fieldvalue) VALUES (NULL,'allowed_subjects','1,2,3,4,5,6,7')");
		$db->execute();
	}

	$db->setQuery("ALTER TABLE `#__osrs_properties` CHANGE `living_areas` `living_areas` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '', CHANGE `garage_description` `garage_description` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `built_on` `built_on` INT(4) NULL, CHANGE `remodeled_on` `remodeled_on` INT(4) NULL, CHANGE `house_style` `house_style` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `house_construction` `house_construction` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `exterior_finish` `exterior_finish` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `roof` `roof` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `flooring` `flooring` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `floor_area_lower` `floor_area_lower` DECIMAL(10,2) NULL, CHANGE `floor_area_main_level` `floor_area_main_level` DECIMAL(10,2) NULL, CHANGE `floor_area_upper` `floor_area_upper` DECIMAL(10,2) NULL, CHANGE `floor_area_total` `floor_area_total` DECIMAL(10,2) NULL, CHANGE `basement_foundation` `basement_foundation` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `basement_size` `basement_size` DECIMAL(12,2) NULL, CHANGE `percent_finished` `percent_finished` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `subdivision` `subdivision` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `land_holding_type` `land_holding_type` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `land_area` `land_area` DECIMAL(10,2) NULL, CHANGE `total_acres` `total_acres` DECIMAL(10,2) NULL, CHANGE `lot_dimensions` `lot_dimensions` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `frontpage` `frontpage` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `depth` `depth` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `takings` `takings` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `returns` `returns` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `net_profit` `net_profit` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `business_type` `business_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `stock` `stock` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `fixtures` `fixtures` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `fittings` `fittings` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `percent_office` `percent_office` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `percent_warehouse` `percent_warehouse` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `loading_facilities` `loading_facilities` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `fencing` `fencing` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `rainfall` `rainfall` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `soil_type` `soil_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `grazing` `grazing` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `cropping` `cropping` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `irrigation` `irrigation` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `water_resources` `water_resources` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `carrying_capacity` `carrying_capacity` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `storage` `storage` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `e_class` `e_class` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `c_class` `c_class` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");
	$db->execute();

	$db->setQuery("ALTER TABLE `#__osrs_property_listing_layout` CHANGE `agenttype` `agenttype` TINYINT(2) NULL DEFAULT NULL;");
	$db->execute();

	//update comment table

	//add index into tables
	//configuration 
	$sql = 'SHOW INDEX FROM `#__osrs_configuration`';
	$db->setQuery($sql);
	$rows   = $db->loadObjectList();
	$fields = array();
	$fields = array();
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row      = $rows[$i];
		$fields[] = $row->Column_name;
	}
	if (!in_array('fieldname', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_configuration` ADD INDEX ( `fieldname` )';
		$db->setQuery($sql);
		$db->execute();
	}

	//agents
	$sql = 'SHOW INDEX FROM `#__osrs_agents`';
	$db->setQuery($sql);
	$rows   = $db->loadObjectList();
	$fields = array();
	$fields = array();
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row      = $rows[$i];
		$fields[] = $row->Column_name;
	}
	if (!in_array('id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_agents` ADD INDEX ( `id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('published', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_agents` ADD INDEX ( `published` )';
		$db->setQuery($sql);
		$db->execute();
	}

	//categories
	$sql = 'SHOW INDEX FROM `#__osrs_categories`';
	$db->setQuery($sql);
	$rows   = $db->loadObjectList();
	$fields = array();
	$fields = array();
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row      = $rows[$i];
		$fields[] = $row->Column_name;
	}
	if (!in_array('id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_categories` ADD INDEX ( `id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('parent_id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_categories` ADD INDEX ( `parent_id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('access', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_categories` ADD INDEX ( `access` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('published', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_categories` ADD INDEX ( `published` )';
		$db->setQuery($sql);
		$db->execute();
	}

	//cities
	$sql = 'SHOW INDEX FROM `#__osrs_cities`';
	$db->setQuery($sql);
	$rows   = $db->loadObjectList();
	$fields = array();
	$fields = array();
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row      = $rows[$i];
		$fields[] = $row->Column_name;
	}
	if (!in_array('id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_cities` ADD INDEX ( `id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('city', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_cities` ADD INDEX ( `city` )';
		$db->setQuery($sql);
		$db->execute();
	}

	//countries
	$sql = 'SHOW INDEX FROM `#__osrs_countries`';
	$db->setQuery($sql);
	$rows   = $db->loadObjectList();
	$fields = array();
	$fields = array();
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row      = $rows[$i];
		$fields[] = $row->Column_name;
	}
	if (!in_array('id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_countries` ADD INDEX ( `id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('country_name', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_countries` ADD INDEX ( `country_name` )';
		$db->setQuery($sql);
		$db->execute();
	}

	//properties
	$sql = 'SHOW INDEX FROM `#__osrs_properties`';
	$db->setQuery($sql);
	$rows   = $db->loadObjectList();
	$fields = array();
	$fields = array();
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row      = $rows[$i];
		$fields[] = $row->Column_name;
	}
	if (!in_array('id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_properties` ADD INDEX ( `id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('published', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_properties` ADD INDEX ( `published` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('approved', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_properties` ADD INDEX ( `approved` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('access', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_properties` ADD INDEX ( `access` )';
		$db->setQuery($sql);
		$db->execute();
	}

	//property_categories
	$sql = 'SHOW INDEX FROM `#__osrs_property_categories`';
	$db->setQuery($sql);
	$rows   = $db->loadObjectList();
	$fields = array();
	$fields = array();
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row      = $rows[$i];
		$fields[] = $row->Column_name;
	}
	if (!in_array('pid', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_property_categories` ADD INDEX ( `pid` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('category_id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_property_categories` ADD INDEX ( `category_id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	
	//states
	$sql = 'SHOW INDEX FROM `#__osrs_states`';
	$db->setQuery($sql);
	$rows   = $db->loadObjectList();
	$fields = array();
	$fields = array();
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row      = $rows[$i];
		$fields[] = $row->Column_name;
	}
	if (!in_array('id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_states` ADD INDEX ( `id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('country_id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_states` ADD INDEX ( `country_id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('published', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_states` ADD INDEX ( `published` )';
		$db->setQuery($sql);
		$db->execute();
	}

	//property types
	$sql = 'SHOW INDEX FROM `#__osrs_types`';
	$db->setQuery($sql);
	$rows   = $db->loadObjectList();
	$fields = array();
	$fields = array();
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row      = $rows[$i];
		$fields[] = $row->Column_name;
	}
	if (!in_array('id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_types` ADD INDEX ( `id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('published', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_types` ADD INDEX ( `published` )';
		$db->setQuery($sql);
		$db->execute();
	}

	//photos
	$sql = 'SHOW INDEX FROM `#__osrs_photos`';
	$db->setQuery($sql);
	$rows   = $db->loadObjectList();
	$fields = array();
	$fields = array();
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row      = $rows[$i];
		$fields[] = $row->Column_name;
	}
	if (!in_array('id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_photos` ADD INDEX ( `id` )';
		$db->setQuery($sql);
		$db->execute();
	}
	if (!in_array('pro_id', $fields))
	{
		$sql = 'ALTER TABLE `#__osrs_photos` ADD INDEX ( `pro_id` )';
		$db->setQuery($sql);
		$db->execute();
	}

	//empty #__osrs_urls table
	$db->setQuery("Delete from `#__osrs_urls`");
	$db->execute();
	
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');
	
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
	if(!Folder::exists(JPATH_ROOT."/images/osproperty/plugins")){
		Folder::create(JPATH_ROOT."/images/osproperty/plugins");
		File::copy($htmlfile,JPATH_ROOT."/images/osproperty/plugins/index.html");
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
	if(!Folder::exists(JPATH_ROOT."/images/osproperty/properties/panorama")){
		Folder::create(JPATH_ROOT."/images/osproperty/properties/panorama");
		File::copy($htmlfile,JPATH_ROOT."/images/osproperty/properties/panorama/index.html");
	}

	$db->setQuery("Select count(id) from `#__osrs_plugins`");
    $count = $db->loadResult();
    if($count == 0)
	{
    	$db->setQuery("INSERT INTO `#__osrs_plugins` (`id`, `name`, `title`, `author`, `creation_date`, `copyright`, `license`, `author_email`, `author_url`, `version`, `description`, `params`, `ordering`, `published`, `support_recurring_subscription`) VALUES
(1, 'os_paypal', 'Paypal', 'Dang Thuc Dam', '2015-10-01 00:00:00', 'Copyright 2010-2015 Ossolution Team', 'http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2', 'damdt@joomservices.com', 'www.joomdonation.com', '1.0', 'Paypal payment plugin', 'paypal_mode=\"0\"\npaypal_id=\"manuni_1241974805_per@yahoo.com\"\npaypal_currency=\"USD\"', 2, 1, 1),
(3, 'os_offline', 'Offline Payment', 'Dang Thuc Dam', '2015-10-01 00:00:00', 'Copyright 2010-2015 Ossolution Team', 'http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2', 'damdt@joomservices.com', 'www.joomdonation.com', '1.0', 'Offline Payment Plugin For OS Property Extension', 'order_status=\"1\"', 4, 1, 0),
(4, 'os_stripe', 'Stripe', 'Dang Thuc Dam', '2015-10-01 00:00:00', 'Copyright 2010-2015 Ossolution Team', 'http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2', 'damdt@joomservices.com', 'www.joomdonation.com', '1.0', 'Stripe Payment Plugin For OS Property Extension', NULL, 5, 1, 0);");
    	$db->execute();

		if(file_exists(JPATH_ROOT."/media/com_osproperty/assets/images/os_offline.png"))
		{
			File::copy(JPATH_ROOT."/media/com_osproperty/assets/images/os_offline.png",JPATH_ROOT."/images/osproperty/plugins/os_offline.png");
		}
		if(file_exists(JPATH_ROOT."/media/com_osproperty/assets/images/os_paypal.png"))
		{
			File::copy(JPATH_ROOT."/media/com_osproperty/assets/images/os_paypal.png",JPATH_ROOT."/images/osproperty/plugins/os_paypal.png");
		}
		if(file_exists(JPATH_ROOT."/media/com_osproperty/assets/images/os_stripe.png"))
		{
			File::copy(JPATH_ROOT."/media/com_osproperty/assets/images/os_stripe.png",JPATH_ROOT."/images/osproperty/plugins/os_stripe.png");
		}
    }

	if(!Folder::exists(JPATH_ROOT."/tmp/osupload")){
		Folder::create(JPATH_ROOT."/tmp/osupload");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/addproperty")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/addproperty");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/agents")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/agents");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/category")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/category");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/companies")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/companies");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/compare")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/compare");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/default")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/default");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/editdetails")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/editdetails");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/favoriteproperties")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/favoriteproperties");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/manageproperties")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/manageproperties");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/properties")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/properties");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/search")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/search");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/type")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/type");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/city")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/city");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/searchlist")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/searchlist");
	}
	if(Folder::exists(JPATH_ROOT."/components/com_osproperty/views/companydetails")){
		Folder::delete(JPATH_ROOT."/components/com_osproperty/views/companydetails");
	}
	if(!Folder::exists(JPATH_ROOT."/media/com_osproperty")){
		Folder::create(JPATH_ROOT."/media/com_osproperty");
		File::copy($htmlfile,JPATH_ROOT."/media/com_osproperty/index.html");
	}
	
	/*
	JFile::copy(JPATH_ROOT."/components/com_osproperty/backup/flags.zip",JPATH_ROOT."/media/com_osproperty/flags.zip");
	if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
	{
		$archive = new Joomla\Archive\Archive(array('tmp_path' => JFactory::getConfig()->get('tmp_path')));
		$result  = $archive->extract(JPATH_ROOT."/media/com_osproperty/flags.zip",JPATH_ROOT."/media/com_osproperty");
	}
	else
	{
		JArchive::extract(JPATH_ROOT."/media/com_osproperty/flags.zip",JPATH_ROOT."/media/com_osproperty");
	}
	*/

	if(!Folder::exists(JPATH_ROOT."/media/com_osproperty/style")){
		Folder::create(JPATH_ROOT."/media/com_osproperty/style");
	}
	if(!File::exists(JPATH_ROOT."/media/com_osproperty/style/custom.css")){
		File::write(JPATH_ROOT."/media/com_osproperty/style/custom.css","");
	}
	?>
	<script language="javascript">
	function installSampleData(){
		location.href = "index.php?option=com_osproperty&task=properties_prepareinstallsample";
	}
	</script>
	<div style="width:95%;padding:10px;border:1px solid #55F489;background-color:#D3FFE1;">
		<center>
			<strong>Do you want to install sample data?</strong>
			<BR>
			<input type="button" class="button btn btn-primary" value="INSTALL SAMPLE DATA" onclick="javascript:installSampleData();">
		</center>
	</div>
	<?php
}

function addConvenience($name,$category_id){
	$db = Jfactory::getDbo();
	$db->setQuery("Select count(id) from #__osrs_amenities where `amenities` like '$name'");
	$count = $db->loadResult();
	if($count == 0){
		$db->setQuery("Insert into #__osrs_amenities (id,category_id,amenities,published) values (NULL,'$category_id','$name',1)");
		$db->execute();
	}
}
?>
