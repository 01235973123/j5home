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

class AriQuizModelFiles extends AriModel 
{
	function __construct()
	{
		parent::__construct();

		// import constants
		$this->getTable('file');
	}
	
	function getFiles($group, $folderId)
	{
		$db =& $this->getDBO();
		$db->setQuery(
			sprintf(
				'SELECT
					F.FileId,
					F.OriginalName,
					FV.FileName
				FROM #__ariquiz_file F INNER JOIN #__ariquiz_file_versions FV
					ON F.FileVersionId = FV.FileVersionId
				WHERE 
					`Group` = %1$s
					AND
					FolderId = %2$d
					AND
					`Status` = %3$d
				ORDER BY F.OriginalName ASC',
				$db->Quote($group),
				$folderId,
				ARIQUIZ_FILE_STATUS_ACTIVE
			)
		);
		return $db->loadObjectList();
	}
	
	function getFile($fileId, $group = null, $strictLoad = true)
	{
		$file =& $this->getTable('File');
		$file->load($fileId);

		if (($strictLoad && empty($file->OriginalName)) ||
			(!is_null($group) && $file->Group != $group))
		{
			$file = null;
		}
		
		return $file;
	}

	function getFileByName($name, $group, $folderId, $strictLoad = true)
	{
		$file =& $this->getTable('File');
		$file->loadByName($name, $group, $folderId);

		if ($strictLoad && empty($file->OriginalName))
			$file = null;
		
		return $file;
	}
	
	function saveFileVersion($data, $extraData = array())
	{
		if (!is_array($data))
			$data = array();

		$data['Params'] = count($extraData) > 0 ? $extraData : '';
		$data = array_merge(
			array('FileId' => 0),
			$data,
		);

		$fileVersion =& $this->getTable('FileVersion');
		$fileVersion->bind($data);

		if (!$fileVersion->store())
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_SQL_QUERY', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$fileVersion->getQuery(), 
					$fileVersion->getError()
				),
				E_USER_ERROR
			);
			return null;
		}
		
		return $fileVersion;
	}
	
	function saveFile($data)
	{
		$file =& $this->getTable('File');
		if (!empty($data['FileId']))
		{
			$file->load($data['FileId']);
		}

		$file->bind($data, array());
		if (!$file->store())
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_SQL_QUERY', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$file->getQuery(), 
					$file->getError()
				),
				500
			);
			return null;
		}
		
		return $file;
		
	}
	
	function deleteFilesFromFolders($idList, $group)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		$db =& $this->getDBO();
		$query = sprintf(
			'UPDATE #__ariquiz_file SET `Status` = %1$d WHERE FolderId IN (%2$s) AND `Group` = %3$s',
			ARIQUIZ_FILE_STATUS_DELETE,
			join(',', $idList),
			$db->Quote($group)
		);
		$db->setQuery($query);
		return $db->execute();
	}
	
	function deleteFile($idList, $group)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		$db =& $this->getDBO();
		$query = sprintf(
			'UPDATE #__ariquiz_file SET `Status` = %1$d WHERE FileId IN (%2$s) AND `Group` = %3$s',
			ARIQUIZ_FILE_STATUS_DELETE,
			join(',', $idList),
			$db->Quote($group)
		);
		$db->setQuery($query);
		return $db->execute();
	}
	
	function deleteFileVersion($fileVersionId)
	{
		$db =& $this->getDBO();
		$db->setQuery(
			sprintf(
				'DELETE FROM #__ariquiz_file_versions WHERE FileVersionId = %d',
				$fileVersionId
			)
		);
		return $db->execute();
	}
}