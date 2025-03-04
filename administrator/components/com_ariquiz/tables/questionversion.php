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

AriKernel::import('Joomla.Tables.Table');

require_once dirname(__FILE__) . DS . 'questiontype.php';

class AriQuizTableQuestionVersion extends AriTable 
{
	var $QuestionVersionId;
	var $QuestionId;
	var $QuestionCategoryId = 0;
	var $QuestionType;
	var $QuestionTypeId = 0;
	var $Question = '';
	var $QuestionTime = 0;
	var $HashCode = '';
	var $Created;
	var $CreatedBy;
	var $Data = '';
	var $Score = 0;
	var $Penalty = 0;
	var $BankQuestionId = 0;
	var $Note;
	var $OnlyCorrectAnswer = 0;
	var $HasFiles = 0;
	var $AttemptCount = 0;
	var $Files = array();

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizquestionversion', 'QuestionVersionId', $db);

		$this->QuestionType = new AriQuizTableQuestionType($db);

		$this->addRelation('QuestionTypeId', 'QuestionType');
	}
	
	function bind($from, $ignore = array())
	{
		if (isset($from['Files']) && !is_array($from['Files']))
			unset($from['Files']);

		if (is_array($from) && array_key_exists('Note', $from) && empty($from['Note']))
			$from['Note'] = '';

		return parent::bind($from, $ignore);
	}
	
	function store($updateNulls = null)
	{
		$files = $this->Files;
		$this->HasFiles = (is_array($files) && count($files) > 0) ? 1 : 0;

		if (empty($this->QuestionTypeId)) {
			$this->QuestionTypeId = 0;
		}

		if (empty($this->Penalty)) {
			$this->Penalty = 0;
		}

		if (empty($this->AttemptCount)) {
			$this->AttemptCount = 0;
		}

		if (parent::store($updateNulls) === false)
			return false;
			
		return $this->_saveFiles();
	}
	
	function getSimpleFiles()
	{
		$files = array();
		if (empty($this->QuestionVersionId) || !$this->HasFiles)
			return $files;
		
		$db =& $this->getDBO();
		$db->setQuery(
			sprintf(
				'SELECT FileId,Alias FROM #__ariquiz_question_version_files WHERE QuestionVersionId = %d',
				$this->QuestionVersionId
			)
		);
		$questionFiles = $db->loadAssocList();
		if (is_null($questionFiles))
		{
			return $files;
		}
		
		if (is_array($questionFiles))
		{
			foreach ($questionFiles as $file)
			{
				$files[$file['Alias']] = $file['FileId']; 
			}
		}

		return $files;
	}
	
	function _saveFiles()
	{
		if (!$this->HasFiles) 
			return true;

		$db =& $this->getDBO();
		$files = $this->Files;
		$data = array();
		foreach ($files as $alias => $fileId)
		{
			$data[] = sprintf("(%d,%d,%d,%s)",
				$fileId,
				$this->QuestionVersionId,
				$this->QuestionId,
				$db->Quote($alias));
		}

		$query = 'INSERT INTO #__ariquiz_question_version_files (FileId,QuestionVersionId,QuestionId,Alias) VALUES' . join(',', $data);		
		$db->setQuery($query);
		return $db->execute();
	}
}