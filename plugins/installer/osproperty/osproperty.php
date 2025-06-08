<?php

defined('_JEXEC') or die;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_osproperty/com_osproperty.php'))
{
	return;
}

class plgInstallerOsproperty extends CMSPlugin
{
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$uri = Uri::getInstance($url);
		$host       = $uri->getHost();
		$validHosts = ['joomdonation.com', 'www.joomdonation.com'];
		if (!in_array($host, $validHosts))
		{
			return true;
		}
		$documentId = $uri->getVar('document_id');
		if ($documentId != 91)
		{
			return true;
		}
		// Get Download ID and append it to the URL
		require_once JPATH_ROOT . '/components/com_osproperty/helpers/helper.php';
		$config = OSPHelper::loadConfig();
		// Append the Download ID to the download URL
		if (!empty($config['download_id']))
		{
			$uri->setVar('download_id', $config['download_id']);
			$url = $uri->toString();
			// Append domain to URL for logging
			$siteUri = Uri::getInstance();
			$uri->setVar('domain', $siteUri->getHost());
			$uri->setVar('version', OSPHelper::getInstalledVersion());
			$url = $uri->toString();
		}
		return true;
	}
}
?>
