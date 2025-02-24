<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2024 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;

class OSMembershipModelUserfiles extends MPFModel
{
	public function getData()
	{
		$user     = Factory::getApplication()->getIdentity();
		$basePath = JPATH_ROOT . '/media/com_osmembership/userfiles/';

		if (Folder::exists($basePath . $user->id))
		{
			$path  = $basePath . $user->id;
			$files = Folder::files($path);
		}
		elseif (Folder::exists($basePath . $user->username))
		{
			$path  = $basePath . $user->username;
			$files = Folder::files($path);
		}
		else
		{
			$files = [];
			$path  = '';
		}

		if ($this->params->get('sort_direction'))
		{
			rsort($files);
		}

		return [$path, $files];
	}
}
