<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die('Restricted access');

if (!defined('J2_5'))
{
    $version = new JVersion();
    define('J2_5', version_compare($version->getShortVersion(), '2.5.0', '>='));
}

if (!defined('J3_0'))
{
	$version = new JVersion();
	define('J3_0', version_compare($version->getShortVersion(), '3.0.0', '>='));
}

class com_ariQuizInstallerScript
{
	function preflight($type, $parent)
	{	
		$db = JFactory::getDBO();		
		
		$db->setQuery('DELETE FROM `#__menu` WHERE `link` LIKE "%option=com_ariquiz%" AND client_id = 1 AND menutype = "main"');
		$db->execute();

		return true;
	}
	
	function postflight($type, $parent)
	{
		if ($type === 'uninstall') {
			return;
		}

		if (J3_0 && ($type == 'install' || $type == 'update'))
			$this->_executeInstall();

		$database = JFactory::getDBO();
		// Update assets references
		$adminPath = JPATH_ADMINISTRATOR . '/components/com_ariquiz/';
		
		if (!J3_0 || ($type != 'install' && $type != 'update'))
		{
			require_once $adminPath . 'kernel/class.AriKernel.php';
			require_once $adminPath . 'defines.php';
			require_once $adminPath . 'helper.php';
	
			require_once $adminPath . 'models/bankcategory.php';
			require_once $adminPath . 'tables/bankcategory.php';
			
			require_once $adminPath . 'models/category.php';
			require_once $adminPath . 'tables/category.php';
		}

		$categoryModel =& AriModel::getInstance('category', 'AriQuizModel');
		$rootCategoryTable = AriTable::getInstance('category', 'AriQuizTable');
		$rootCategoryId = $rootCategoryTable->addRoot();
		if ($rootCategoryId)
		{
			$database->setQuery('SELECT id FROM #__assets WHERE name = "com_ariquiz" LIMIT 0,1');
			$ariQuizAssetId = $database->loadResult();
			if ($ariQuizAssetId)
			{
				$assetName = 'com_ariquiz.category.' . $rootCategoryId;
				$database->setQuery(
					sprintf(
						'SELECT id FROM #__assets WHERE name = %1$s LIMIT 0,1',
						$database->Quote($assetName)
					)
				);
				$rootCatAssetId = $database->loadResult();
				if (empty($rootCatAssetId))
				{
					jimport('joomla.database.table');
					
					$asset = AriTable::getInstance('Asset');
			        $asset->name = $assetName;
			        $asset->parent_id = $ariQuizAssetId;
			        $asset->rules = '{}';
			        $asset->title = 'com_ariquiz.category.' . $rootCategoryId;
			        $asset->setLocation($ariQuizAssetId, 'last-child');
			        $asset->store();
				}
			}
		}

		$type = strtolower($type);
		if ($type == 'install')
		{		
			$defaultCategoryId = AriQuizHelper::getDefaultCategoryId();
			$defaultBankCategoryId = AriQuizHelper::getDefaultBankCategoryId();
			
			if ($defaultCategoryId)
			{
				$category = $categoryModel->getCategory($defaultCategoryId);
				if ($category)
					$category->store();
			}
			
			if ($defaultBankCategoryId)
			{
				$bankCategoryModel =& AriModel::getInstance('Bankcategory', 'AriQuizModel');
				$category = $bankCategoryModel->getCategory($defaultBankCategoryId);
				if ($category)
					$category->store();
			}
		}
		else
		{
			if ($rootCategoryId)
			{
				$database->setQuery(
					sprintf(
						'SELECT CategoryId FROM #__ariquizcategory WHERE parent_id = %1$d',
						$rootCategoryId
					)
				);
				$catIdList = $database->loadObjectList();
				if (is_array($catIdList))
				{
					foreach ($catIdList as $cat)
					{
						$category = $categoryModel->getCategory($cat->CategoryId);
						if ($category)
							$category->store();
					}
				}
			}
		}

        if (J2_5 && ($type == 'install' || $type == 'update'))
        {
            $parent->getParent()->setRedirectUrl('index.php?option=com_ariquiz&view=install&hidemainmenu=1');
        }
			
		return true;
	}

	function uninstall()
	{
		if (J3_0)
		{
			require_once dirname(__FILE__) . '/uninstall.php';
	
			if (function_exists('com_uninstall'))
				com_uninstall();
		}
	}

	function _executeInstall()
	{
		require_once dirname(__FILE__) . '/backend/install.php';

		if (function_exists('com_install'))
			com_install();
	}
}