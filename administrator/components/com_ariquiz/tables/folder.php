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

AriKernel::import('Joomla.Database.TableNested');

define('ARIQUIZ_FOLDER_STATUS_ACTIVE', 1);
define('ARIQUIZ_FOLDER_STATUS_DELETE', 2);

class AriQuizTableFolder extends AriTableNested
{
	var $Status = ARIQUIZ_FOLDER_STATUS_ACTIVE;
	var $Group;
	
	function __construct(&$db) 
	{
		parent::__construct('#__ariquiz_folder', 'id', $db);
	}
	
	function loadByName($name, $parentFolderId)
	{
		$db = $this->getDBO();
		$db->setQuery(
			sprintf(
				'SELECT * FROM `%1$s` WHERE alias=%2$s AND parent_id=%3$s LIMIT 0,1',
				$this->getTableName(),
				$db->quote($name),
				$parentFolderId
			)
		);
		$data = $db->loadAssoc();
		if (is_null($data))
		{
			return false;
		}

		if (is_null($data) || !$this->bind($data))
			return false;
			
		return true;
	}
	
	function loadRootGroupFolder($group)
	{
		$db = $this->getDBO();
		$db->setQuery(
			sprintf(
				'SELECT * FROM `%1$s` WHERE alias=%2$s AND level=1 LIMIT 0,1',
				$this->getTableName(),
				$db->quote($group)
			)
		);
		$data = $db->loadAssoc();
		if (is_null($data))
		{
			return false;
		}

		if (is_null($data) || !$this->bind($data))
			return false;
			
		return true;
	}
}