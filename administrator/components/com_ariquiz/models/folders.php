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

AriKernel::import('Joomla.Models.Model');
AriKernel::import('Joomla.Database.DBUtils');
AriKernel::import('Utils.ArrayHelper');

class AriQuizModelFolders extends AriModel 
{
	var $_rootDir;
	var $_group;
	
	function __construct($config = array())
	{ 
		if (array_key_exists('rootDir', $config))
			$this->_rootDir = $config['rootDir'];
			
		if (array_key_exists('group', $config))
			$this->_group = $config['group'];

		parent::__construct($config);
		
		$this->getTable('Folder');
	}
	
	function getFolder($folderId, $strictLoad = true)
	{
		if ($strictLoad && $folderId < 1)
			return null;

		$folder =& $this->getTable('Folder');
		$folder->load($folderId);

		if ($strictLoad && empty($folder->title))
			$folder = null;

		return $folder;
	}
	
	function getPath($folderId)
	{
		$table = $this->getTable('Folder');
		$path = $table->getPath($folderId);
		
		return $path;
	}
	
	function getSimplePath($path)
	{
		if ($path === false)
			return $path;
			
		$pathElements = array();
		foreach ($path as $pathEl)
		{
			if ($pathEl->level > 1)
				$pathElements[] = $pathEl->title;
		}

		return $pathElements;
	}

	function getChildFolders($parentId)
	{
		$db = $this->getDBO();
		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'id',
				'parent_id',
				'title',
				'alias',
				'access'
			)
		);
		$query->from('#__ariquiz_folder');
		$query->where('parent_id = ' . intval($parentId, 10));
		$query->where('Status = ' . ARIQUIZ_FOLDER_STATUS_ACTIVE);
		$query->order('title ASC');
		$db->setQuery((string)$query);
		return $db->loadObjectList();
	}
	
	function getFoldersTreeSet($idList)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return null;
			
		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		$query->select('n.*');
		$query->from('#__ariquiz_folder AS n, #__ariquiz_folder AS p');
		$query->where('n.lft BETWEEN p.lft AND p.rgt');
		$query->where('p.id IN (' . join(',', $idList) . ')');
		$query->order('n.lft');
		$db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	function deleteFolders($idList, $group)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
			
		$foldersSet = $this->getFoldersTreeSet($idList);
		$fullIdList = array();
		if (is_array($foldersSet))
		{
			foreach ($foldersSet as $folder)
				$fullIdList[] = $folder->id;
				
			$fullIdList = array_unique($fullIdList);
		}
		
		$db =& $this->getDBO();		
		$db->setQuery(
			sprintf(
				'UPDATE #__ariquiz_folder SET Status = %1$d AND `Group` = %3$s WHERE id IN (%2$s)',
				ARIQUIZ_FOLDER_STATUS_DELETE,
				join(',', $fullIdList),
				$db->quote($group)
			)
		);

		if (!$db->execute())
		{
			return false;
		}
		
		$filesModel = AriModel::getInstance('files', $this->getFullPrefix());
		if (!$filesModel->deleteFilesFromFolders($fullIdList, $this->getGroup()))
			return false;

		return true;
	}
	
	function getRootDir()
	{
		return $this->_rootDir;
	}

	function getGroup()
	{
		return $this->_group;
	}

	function getFolderByName($name, $parentFolderId)
	{
		$table = $this->getTable('Folder');
		if (!$table->loadByName($name, $parentFolderId))
			$table = null;

		return $table;
	}
	
	function getRootFolder()
	{
		$table = $this->getTable('Folder');
		if (!$table->loadRootGroupFolder($this->getGroup()))
			$table = null;

		return $table;
	}
	
	function createFolder($folderName, $parentFolderId)
	{
		$folderName = trim($folderName);
		if (!$this->isValidFolderName($folderName))
			return false;
		
		$table = $this->getTable('Folder');

		$table->setLocation($parentFolderId, 'last-child');
		$table->bind(
			array(
				'title' => $folderName,
				'alias' => $folderName,
				'parent_id' => $parentFolderId,
				'Group' => $this->getGroup() 
			)
		);
		$table->id = 0;

		if (!$table->check())
			return false;

		$simplePath = $this->getSimplePath($this->getPath($parentFolderId));
		$pathToFolder = $this->getRootDir() . DS . join(DS, $simplePath) . DS . $folderName;
		$dbFolder = $this->getFolderByName($folderName, $parentFolderId);
		$isDeletedFolder = false;
		
		if ($dbFolder)
			if ($dbFolder->Status == ARIQUIZ_FOLDER_STATUS_ACTIVE)
				return false;
			else if ($dbFolder->Status == ARIQUIZ_FOLDER_STATUS_DELETE)
				$isDeletedFolder = true;
		
		$isCreatedNewFolder = false;
		if (!file_exists($pathToFolder) || is_dir($pathToFolder))
			if (!JFolder::create($pathToFolder, 0755))
				return false;
			else
				$isCreatedNewFolder = true;
				
		if ($isDeletedFolder)
		{
			$dbFolder->Status = ARIQUIZ_FOLDER_STATUS_ACTIVE;
			if (!$dbFolder->store())
				return false;
		}
		else if (!$table->store())
		{
			if ($isCreatedNewFolder)
				JFolder::delete($pathToFolder);

			return false;
		}
		
		return true;
	}
	
	function isValidFolderName($name)
	{
		if (empty($name))
			return false;
			
		if (!preg_match('/^[_\-0-9A-z]+$/i', $name))
			return false;
			
		return true;
	}
}