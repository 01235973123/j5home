<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

define('ARI_INSTALL_ERROR_EXECUTEQUERY', 'Couldn\'t execute query. Error: %s.');
define('ARI_INSTALL_ERROR_CHMOD', 'Couldn\'t change permission for directory "%s" permission "%s".');
define('ARI_INSTALL_ERROR_PLUGIN', 'Couldn\'t install "%s" plugin.');
define('ARI_INSTALL_ERROR_MODULE', 'Couldn\'t install "%s" module.');
define('ARI_INSTALL_SUCCESFULLY', 'The component succesfully installed');
define('ARI_INSTALL_FAILED', 'The component installation failed');

jimport('joomla.installer.helper');
jimport('joomla.installer.installer');
jimport('joomla.filesystem.path');

AriKernel::import('Xml.XmlHelper');

class AriInstallerBase extends JObject
{
	var $_db;
	var $option;
	var $adminPath;
	var $_installErrors;
	
	function __construct($options)
	{		
		if (array_key_exists('option', $options))
			$this->option = $options['option'];

		$this->_db = JFactory::getDBO();
		$this->basePath = JPATH_ROOT . DS . 'components' . DS . $this->option . DS;
		$this->adminPath = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->option . DS;
	}
	
	function errorHandler($errNo, $errStr, $errFile, $errLine)
	{
		$stopPhpHandler = false;
		if ($errNo == E_USER_ERROR)
		{			
			$this->_installErrors .= "\r\n" . $errStr;
			$stopPhpHandler = true;
		}
		
		return $stopPhpHandler;
	}
	
	function install()
	{
		@set_time_limit(-1);
		@ini_set('memory_limit', -1);
		@ini_set('display_errors', true);
		$errorReporting = E_ALL;
		if (version_compare(PHP_VERSION, '5.3.0') >= 0)
			$errorReporting &= ~E_STRICT;

		error_reporting($errorReporting);
		ignore_user_abort(true);

		$this->_installErrors = '';
		
		set_error_handler(array(&$this, 'errorHandler'));
		
		$result = $this->installSteps();
		
		restore_error_handler();
		
		return $this->_getInstallationResult();
	}
	
	function isSuccess()
	{
		return empty($this->_installErrors);
	}
	
	function _getInstallationResult()
	{
		$success = empty($this->_installErrors);
		$return = '';
		
		if ($success)
		{ 
			$return = sprintf('<div style="color: green; font-weight: bold; text-align: center;">%s</div>',
				ARI_INSTALL_SUCCESFULLY);
		}
		else
		{
			$return = sprintf('<div style="color: red; font-weight: bold; text-align: center;">%s</div><div style="color: red;">%s</div>',
				ARI_INSTALL_FAILED,
				$this->_installErrors);
		}
		
		return $return;
	}
	
	function installSteps()
	{
		return true;
	}
	
	function isDbSupportUtf8()
	{
		$database = $this->_db;
		
		$query = 'SHOW CHARACTER SET LIKE "utf8"';
		$database->setQuery($query);
		try {
			$result = $database->loadAssocList();
		} catch (Exception $ex) {
			$error = sprintf(
				ARI_INSTALL_ERROR_EXECUTEQUERY, 
				$ex->getMessage()
			);
			trigger_error($error, E_USER_ERROR);
			return false;
		}
		
		return (!empty($result) && count($result) > 0);
	}
	
	/*
	 $dirForChmod = array(
			$adminPath . 'cache/files' => 0777,
			$adminPath . 'cache/files/thumb' => 0777, 
			$adminPath . 'cache/files/lbackend' => 0777,
			$adminPath . 'cache/files/i18n/lbackend' => 0777); 
	 */
	function setPermissions($dirForChmod)
	{
		$errors = array();
		foreach ($dirForChmod as $dir => $perm)
		{
			if (!JPath::canChmod($dir) || !JPath::setPermissions($dir, null, $perm))
			{
				$errors[] = sprintf(ARI_INSTALL_ERROR_CHMOD, $dir, $perm); 
			}
		}
		
		if (count($errors) > 0)
		{
			trigger_error(join("\r\n", $errors), E_USER_ERROR);
			return false;
		}
		
		return true;
	}
	
	/*
	 * $menuInfo = array({'link', 'image'}, ...)
	 */
	function updateMenuIcons($menuInfo)
	{
		$database = $this->_db;
		
		$queryList = array();
		foreach ($menuInfo as $menuInfoItem)
		{
			$link = $menuInfoItem['link'];
			$img = $menuInfoItem['image'];
			
			$queryList[] = sprintf('UPDATE #__components' .
			  	' SET admin_menu_img=%s' .
			  	' WHERE admin_menu_link=%s',
				$database->Quote($img),
				$database->Quote($link)); 
		}

		foreach ($queryList as $queryItem)
		{
			$database->setQuery($queryItem);
			$database->execute();
			try {
				$database->execute();
			} catch (Exception $ex) {
				trigger_error(sprintf(ARI_INSTALL_ERROR_EXECUTEQUERY, $ex->getMessage()), E_USER_ERROR);
				return false;
			}
		}
		
		return true;
	}
	
	function installPlugin($plgPath, $enable = true)
	{
		if (is_file($plgPath))
		{
			$installResult = JInstallerHelper::unpack($plgPath);
			if (empty($installResult)) 
			{
				trigger_error(sprintf(ARI_INSTALL_ERROR_PLUGIN, $plgPath), E_USER_ERROR);
				return false;
			}
			
			$plgPath = $installResult['extractdir'];
		}

		$installer = new JInstaller();
		$installer->setOverwrite(true);
		if (!$installer->install($plgPath)) 
		{
			trigger_error(sprintf(ARI_INSTALL_ERROR_PLUGIN, $plgPath), E_USER_ERROR);
			return false;
		}

		if ($enable)
		{		
			$pluginName = '';
			$type = 'plugin';
			$manifest = $installer->getManifest();
			if (count($manifest->files->children()))
			{
				foreach ($manifest->files->children() as $file)
				{
					if ((string) $file->attributes()->$type)
					{
						$pluginName = (string) $file->attributes()->$type;
						break;
					}
				}
			} 
			
			if ($pluginName)
			{
				$db = $this->_db;
				
				$db->setQuery('UPDATE #__extensions SET enabled = 1 WHERE `type` = "plugin" AND `element` = ' . $db->quote($pluginName));

				$db->execute();
			}
		}

		return true;
	}

	function installModule($modulePath)
	{
		if (is_file($modulePath))
		{
			$installResult = JInstallerHelper::unpack($modulePath);
			if (empty($installResult)) 
			{
				trigger_error(sprintf(ARI_INSTALL_ERROR_MODULE, $modulePath), E_USER_ERROR);
				return false;
			}
			
			$modulePath = $installResult['extractdir'];
		}
		
		$installer = new JInstaller();

		$installer->setOverwrite(true);
		$installer->install($modulePath);
	
		return true;
	}
	
	function _isColumnExists($table, $column)
	{
		$database = $this->_db;
		
		$query = sprintf('SHOW COLUMNS FROM %s LIKE "%s"',
			$table,
			$column);
		$database->setQuery($query);
		$columnsList = $database->loadObjectList();
		$isColumnExists = (!empty($columnsList) && count($columnsList) > 0);
		
		return $isColumnExists; 
	}

	function _isIndexExists($table, $index)
	{
		$database = $this->_db;
		
		$query = 'SHOW INDEX FROM ' . $table;
		$database->setQuery($query);
		$keys = $database->loadAssocList();
		if (is_array($keys))
		{
			foreach ($keys as $keyInfo)
			{
				if (isset($keyInfo['Key_name']) && $keyInfo['Key_name'] == $index)
				{
					return true;
				}
			}
		}
		
		return false;
	}
	
	function _applyUpdates($version)
	{
		$updateSig = '_updateTo_';
		$lowerUpdateSig = strtolower($updateSig);
		$methods = get_class_methods(get_class($this));
		$updateMethods = array();
		
		foreach ($methods as $method)
		{
			$lowerMethod = strtolower($method);
			if (strpos($lowerMethod, $lowerUpdateSig) === 0)
			{
				$methodVer = str_replace(array($updateSig, $lowerUpdateSig, '_'), array('', '', '.'), $method);
				if (version_compare($methodVer, $version, '>'))
				{
					$updateMethods[$methodVer] = $method;
				}
			}
		}

		if (count($updateMethods) > 0)
		{
			uksort($updateMethods,  'version_compare');
			
			foreach ($updateMethods as $updateMethod)
			{
				$this->$updateMethod();
			}
		}
	}
}