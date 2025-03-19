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

class AriQuizControllerQuizfile extends AriController 
{
	function __construct($config = array()) 
	{
		if (!array_key_exists('model_path', $config))
			$config['model_path'] = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_ariquiz' . DS . 'models';

		parent::__construct($config);
	}
	
	function showFile()
	{
		$alias = JRequest::getString('alias');
		$ticketId = JRequest::getString('ticketId');
		$questionId = JRequest::getInt('questionId');

		$userQuizModel =& $this->getModel('Userquiz');
		$file = $userQuizModel->getFile($ticketId, $questionId, $alias);
		if (empty($file))
		{
			header("HTTP/1.0 404 Not Found");
			exit();
		}
		
		$dir = AriQuizHelper::getFilesDir($file['Group']);
		$foldersModel = $this->getModel(
			'Folders', 
			'',
			array(
				'rootDir' => $dir, 
				'group' => $file['Group']
			)
		);
		$path = $foldersModel->getSimplePath($foldersModel->getPath($file['Folder']));
		$filePath = $dir . DS . join(DS, $path) . DS . $file['FileName'];

		$handle = fopen($filePath, "rb");			 
		$content = fread($handle, filesize($filePath));
		fclose($handle);

		if (!empty($file['MimeType']))
			header('Content-type: ' . $file['MimeType']);

		while (@ob_end_clean());
		echo $content;
		exit();
	}
}