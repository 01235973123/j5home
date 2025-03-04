<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Joomla.Controllers.Controller');
AriKernel::import('Utils.ArrayHelper');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

if (J3_1)
{
	jimport('cms.installer.helper');
	jimport('cms.installer.installer');
	jimport('cms.installer.manifest.package');
}
else
{
	jimport('joomla.installer.helper');
	jimport('joomla.installer.installer');
	jimport('joomla.installer.packagemanifest');
}

class AriQuizControllerInstall extends AriController
{
	function display($cachable = false, $urlparams = array())
	{
		$addons = $this->_getAddons(JPATH_ADMINISTRATOR . '/components/com_ariquiz/install/install.manifest', JPATH_ADMINISTRATOR . '/components/com_ariquiz/install');
		
		$view = $this->getView();
		$view->displayView($addons);
		
		return ;
	}

	function install()
	{
		set_time_limit(300);
		ini_set('memory_limit', -1);
		
		$jInput = JFactory::getApplication()->input;

		$addons = $this->_getAddons(JPATH_ADMINISTRATOR . '/components/com_ariquiz/install/install.manifest', JPATH_ADMINISTRATOR . '/components/com_ariquiz/install');

		foreach ($addons as $addonGroup)
		{
			$group = $addonGroup['group'];
			$reqAddons = $jInput->request->get($group, null, 'RAW');

			if (!is_array($reqAddons))
				continue ;

			$assocAddons = AriArrayHelper::toAssoc($addonGroup['apps'], 'package');
			$addonDir = JPATH_ADMINISTRATOR . '/components/com_ariquiz/install/' . $addonGroup['directory'];

			foreach ($reqAddons as $reqAddon)
			{
				if (!isset($assocAddons[$reqAddon]))
					continue ;

				if (!$this->_installExt($reqAddon, $addonDir))
				{
					
				}
			}
		}

		$this->redirect('index.php?option=com_ariquiz&__MSG=COM_ARIQUIZ_LABEL_INSTALLCOMPLETE');
	}
	
	function _installExt($ext, $dir)
	{
		$extFile = JFile::makeSafe($ext);
		if (empty($extFile))
			return false;

		$extFile = $dir . '/' . $extFile;
		if (!file_exists($extFile))
			return false;

		$res = JInstallerHelper::unpack($extFile);
		if (empty($res))
			return false;

		$installer = new JInstaller();
		$installer->setOverwrite(true);
			
		$ret = $installer->install($res['extractdir']); 
						
		JFolder::delete($res['extractdir']);

		return $ret;
	}
	
	function _getAddons($manifestFile, $appDir)
	{
		$addons = array();
		
		if (!file_exists($manifestFile) || !is_file($manifestFile))
			return $addons;

		$manifest = json_decode(file_get_contents($manifestFile), true);
		if (!isset($manifest['addons']) || !is_array($manifest['addons']) || count($manifest['addons']) == 0)
			return $addons;
		
		$cfg = AriQuizHelper::getConfig();
		
		$extCurrentVersion = $cfg->get('Version');
		$extPrevVersion = $cfg->get('PrevVersion');
			
		foreach ($manifest['addons'] as $addonsGroup)
		{
			$groupApps = array();
			
			foreach ($addonsGroup['apps'] as $app)
			{
				$package = $app['package'];
				$packagePath = $appDir . '/' . $addonsGroup['directory'] . '/' . $package;

				if (!file_exists($packagePath))
					continue ;

				$meta = $app['meta'];
				$meta['key'] = $meta['type'] . '$' . (!empty($meta['group']) ? $meta['group'] . '$' : '') . $meta['name'];
				
				$sinceVer = $app['since'];
				
				$checked = true;
				if (!empty($extPrevVersion) && version_compare($sinceVer, $extPrevVersion) < 1)
					$checked = false;

				$groupApps[] = array(
					'name' => JText::_($app['name']),
				
					'description' => JText::_($app['description']),
				
					'package' => $package,
				
					'version' => $app['version'],
				
					'checked' => $checked,
				
					'meta' => $meta,
				
					'status' => 'missed'
				);
			}
			
			if (count($groupApps) > 0)
			{
				$addons[] = array(
					'group' => $addonsGroup['group'],
				
					'label' => JText::_($addonsGroup['label']),
				
					'directory' => !empty($addonsGroup['directory']) ? $addonsGroup['directory'] : $addonsGroup['group'],
				
					'apps' => $groupApps
				);
			}
		}
		
		$db = JFactory::getDbo();
		$installedAppsQuery = $db->getQuery(true);
		$installedAppsQuery->select(
			array(
				'CONCAT(type, "$", IF(LENGTH(folder) > 0, CONCAT(folder, "$"), ""), element) AS plg_key',
				'manifest_cache'
			)
		);
		$installedAppsQuery->from('#__extensions');

		foreach ($addons as $addonGroup)
		{
			foreach ($addonGroup['apps'] as $app)
			{
				$meta = $app['meta'];
				
				$plgFilter = sprintf('element = %1$s AND type = %2$s',
					$db->quote($meta['name']),
					$db->quote($meta['type'])
				);
				
				if (!empty($meta['group']))
					$plgFilter .= sprintf(' AND folder = %1$s',
						$db->quote($meta['group'])
					);

				$installedAppsQuery->where('(' . $plgFilter . ')', 'OR');
			}
		}
		
		$db->setQuery($installedAppsQuery);
		$installedApps = $db->loadObjectList('plg_key');
		
		if (is_array($installedApps) && count($installedApps) > 0)
		{
			foreach ($addons as &$addonGroup)
			{
				foreach ($addonGroup['apps'] as &$app)
				{
					$meta = $app['meta'];
					$key = $meta['key'];
					
					if (!isset($installedApps[$key]))
						continue ;

					$installedVer = '0.0.0';
					$manifestCache = $installedApps[$key]->manifest_cache;
					if ($manifestCache)
					{
						$manifestCache = @json_decode($manifestCache, true);
						if (!empty($manifestCache['version']))
							$installedVer = $manifestCache['version'];
					}

					$app['currentVersion'] = $installedVer;
					
					$verCompare = version_compare($installedVer, $app['version']);
					if ($verCompare == 0)
					{
						$app['checked'] = false;
						$app['status'] = 'installed';
					}
					else if ($verCompare == -1)
					{
						$app['checked'] = true;
						$app['status'] = 'updated';
					}
					else
					{
						$app['checked'] = false;
						$app['status'] = 'newest';
					}
				}
			}
		}

		return $addons;
	}
}