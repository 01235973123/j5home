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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class AriQuizControllerBaseFiles extends AriController 
{
	var $_ctrlName;
	var $_foldersModel;
	var $_filesModel;
	
	function display($cachable = false, $urlparams = [])
	{
		$filesModel = $this->_getFilesModel();
		$foldersModel = $this->_getFoldersModel();
		
		$folder = null;
		$folderId = JRequest::getInt('folderId');
		if ($folderId < 1)
		{
			$folder = $foldersModel->getRootFolder();
		}
		else 
		{
			$folder = $foldersModel->getFolder($folderId);
		}

		if (empty($folder) || $folder->Group != $this->getGroup())
		{
			$this->redirect('index.php?option=com_ariquiz');
			exit();
		}
		
		$folderId = $folder->id;
		$path = $foldersModel->getPath($folderId);
		$simplePath = $foldersModel->getSimplePath($path);

		$folders = $foldersModel->getChildFolders($folderId);
		$files = $filesModel->getFiles($this->getGroup(), $folderId);
		$filesDir = AriQuizHelper::getFilesDir($this->getGroup()) . DS . join(DS, $simplePath);
		$folderUri = JURI::root(true) . '/' . AriUtils::absPath2Relative($filesDir) . '/';
		
		$params = array(
			'folder' => $folder,
			'folderId' => $folderId,
			'folderUri' => $folderUri,
			'path' => $path
		);
		
		$view =& $this->getView('files', 'html', '', array('ctrlName' => strtolower($this->getName())));
		$view->displayView($folders, $files, $params);
	}
	
	function changeFolder()
	{
		$folderId = JRequest::getInt('folderId');
		$this->redirect('index.php?option=com_ariquiz&view=' . $this->_getCtrlName() . '&folderId=' . $folderId);
		exit();
	}

	function upload()
	{
		JRequest::checkToken('request') or jexit('Invalid Token');

		$folderId = JRequest::getInt('folderId');
		
		$foldersModel = $this->_getFoldersModel();
		$folder = $foldersModel->getFolder($folderId);
		if (empty($folder) || $folder->Group != $this->getGroup())
		{
			$this->redirect('index.php?option=com_ariquiz&view=' . $this->_getCtrlName());
			exit();
		}

		$file = JRequest::getVar('fileUpload', '', 'files', 'array');
		$fileOverwrite = JRequest::getBool('fileOverwrite');

		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		if (empty($file['name']) || !$this->_isAcceptableFile($file['tmp_name']))
		{
			$this->redirect('index.php?option=com_ariquiz&view=' . $this->_getCtrlName() . '&folderId=' . $folderId . '&__MSG=COM_ARIQUIZ_ERROR_UPLOADFILE');
			return ;
		}

		$model = $this->_getFilesModel();
		$fileEntity = $model->getFileByName($file['name'], $this->getGroup(), $folderId);
		$isFileExist = !is_null($fileEntity);

		if (!$fileOverwrite && $isFileExist && $fileEntity->Status == ARIQUIZ_FILE_STATUS_ACTIVE) 
		{
			$this->redirect('index.php?option=com_ariquiz&view=' . $this->_getCtrlName() . '&folderId=' . $folderId . '&__MSG=COM_ARIQUIZ_ERROR_UPLOADFILEEXIST');
			return;
		}

		$fileVersion = $model->saveFileVersion(
			array(
				'FileSize' => filesize($file['tmp_name'])
			)
		);

		$path = $foldersModel->getSimplePath($foldersModel->getPath($folderId));

		$rootDir = $this->getRootDir();
		$pathInfo = pathinfo($file['name']);
		$versionFileName = $pathInfo['filename'] . '_' . $fileVersion->FileVersionId;
		if (!empty($pathInfo['extension']))
			$versionFileName .= '.' . $pathInfo['extension'];

		$filePath = $rootDir . DS . join(DS, $path) . DS . $versionFileName;
		$fileVersion->FileName = $versionFileName;

		if (!JFile::upload($file['tmp_name'], $filePath)) 
		{
			$model->deleteFileVersion($fileVersion->FileVersionId);
			$this->redirect('index.php?option=com_ariquiz&view=' . $this->_getCtrlName() . '&folderId=' . $folderId . '&__MSG=COM_ARIQUIZ_ERROR_UPLOADFILE');
			return;
		} 
		else 
		{
			$fileData = array();
			if ($isFileExist)
				$fileData = array(
					'FileId' => $fileEntity->FileId,
					'FileVersion' => $fileVersion,
					'Status' => ARIQUIZ_FILE_STATUS_ACTIVE
				);
			else 
				$fileData = array(
					'MimeType' => $file['type'],
					'OriginalName' => $file['name'],
					'Group' => $this->getGroup(),
					'FolderId' => $folderId,
					'FileVersion' => $fileVersion
				);
			$model->saveFile($fileData);

			$this->redirect('index.php?option=com_ariquiz&view=' . $this->_getCtrlName() . '&folderId=' . $folderId . '&__MSG=COM_ARIQUIZ_COMPLETE_FILEUPLOADED');
			return;
		}
	}

	function getGroup()
	{
		return '';
	}

	function getRootDir()
	{
		$filesDir = AriQuizHelper::getFilesDir($this->getGroup());
		
		return $filesDir;
	}

	function createFolder()
	{
		$folderId = JRequest::getInt('folderId');
		$newFolder = JRequest::getString('newFolder');
		
		$foldersModel = $this->_getFoldersModel();
		$folder = $foldersModel->getFolder($folderId);
		if (empty($folder) || $folder->Group != $this->getGroup())
		{
			$this->redirect('index.php?option=com_ariquiz&view=' . $this->_getCtrlName());
			exit();
		}
		
		$result = $this->_createFolder($newFolder, $folderId);
		
		$this->redirect('index.php?option=com_ariquiz&view=' . $this->_getCtrlName() . '&folderId=' . $folderId . '&__MSG=' . ($result ? 'COM_ARIQUIZ_COMPLETE_FOLDERCREATED' : 'COM_ARIQUIZ_ERROR_FOLDERCREATION'));
		return;
	}
	
	function _createFolder($newFolder, $parentFolderId)
	{
		if (empty($newFolder))
			return false;

		$foldersModel = $this->_getFoldersModel();

		return $foldersModel->createFolder($newFolder, $parentFolderId);
	}

	function ajaxDelete()
	{
		$filesModel = $this->_getFilesModel();
		$foldersModel = $this->_getFoldersModel();
		
		$fileIdList = JRequest::getVar('FileId');
		$folderIdList = JRequest::getVar('FolderId');
		
		$result = true;
		if (!empty($fileIdList))
			$result &= $filesModel->deleteFile($fileIdList, $this->getGroup());
			
		if (!empty($folderIdList))
			$result &= $foldersModel->deleteFolders($folderIdList, $this->getGroup());
		
		return $result;
	}
	
	function _isAcceptableFile($file)
	{
		return true;
	}

	function _getFilesModel()
	{
		if (is_null($this->_filesModel))
			$this->_filesModel = $this->getModel('Files');
		
		return $this->_filesModel;
	}
	
	function _getFoldersModel()
	{
		if (is_null($this->_foldersModel))
		{
			$this->_foldersModel = $this->getModel(
				'Folders', 
				'', 
				array(
					'rootDir' => $this->getRootDir(), 
					'group' => $this->getGroup()
				)
			);
		}
		
		return $this->_foldersModel;
	}
	
	function _getCtrlName()
	{
		if (is_null($this->_ctrlName))
			$this->_ctrlName = strtolower($this->getName());

		return $this->_ctrlName;
	}
}