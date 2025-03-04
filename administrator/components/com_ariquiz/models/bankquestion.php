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
AriKernel::import('Joomla.Database.DatabaseQuery');

class AriQuizModelBankquestion extends AriModel 
{
	function getQuestion($questionId, $strictLoad = true) 
	{
		if ($strictLoad && $questionId < 1)
			return null;

		$question =& $this->getTable();
		$question->load($questionId);

		if (!empty($question->QuizId) || ($strictLoad && empty($question->QuestionVersionId)))
			$question = null;

		return $question;
	}

	function saveQuestion($data)
	{
		$question =& $this->getTable();
		$question->bind($data);

		if (!$question->store())
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_SQL_QUERY', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$question->getQuery(), 
					$question->getError()
				),
				500
			);
			return null;
		}
		
		return $question;
	}

	function update($questionId, $fields, $userId)
	{
		$question = $this->getQuestion($questionId);
		if (empty($question))
			return null;

		return $question->update($fields);	
	}
}