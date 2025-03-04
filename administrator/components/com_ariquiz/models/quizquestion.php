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

class AriQuizModelQuizquestion extends AriModel 
{
	function getQuestion($questionId, $strictLoad = true) 
	{
		if ($strictLoad && $questionId < 1)
			return null;

		$question =& $this->getTable();
		$question->load($questionId);
		if ($strictLoad && (empty($question->QuestionVersionId) && empty($question->BankQuestionId)))
			$question = null;

		return $question;
	}
	
	function saveQuestion($data)
	{
		$question =& $this->getTable();
		if (!empty($data['QuestionId']))
		{
			$question->load($data['QuestionId']);
			$data['QuizId'] = $question->QuizId;
		}

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
	
	function copy($questionId, $quizId, $questionCategoryId, $questionIndex, $userId)
	{
		$question = $this->getQuestion($questionId);
		if (empty($question))
			return null;
			
		$copyQuestion =& $this->getTable();
		if (!$copyQuestion->copyFrom($question, $quizId, $questionCategoryId, $questionIndex, $userId))
			return null;

		if (!$copyQuestion->store())
			return null;
		
		return $copyQuestion;
	}
	
	function update($questionId, $fields, $userId)
	{
		$question = $this->getQuestion($questionId);
		if (empty($question))
			return false;
			
		return $question->update($fields);
	}
	
	function copyToBank($questionId, $qIndex, $categoryId, $basedOnBank, $userId)
	{
		$question = $this->getQuestion($questionId);
		if (empty($question) || $question->BankQuestionId)
			return null;
			
		$bankQuestion =& $this->getTable('Bankquestion');
		if (!$bankQuestion->copyFrom($question, 0, $categoryId, $questionIndex, $userId))
			return null;

		if (!$bankQuestion->store())
			return null;
			
		if ($basedOnBank)
		{
			$question->BankQuestionId = $bankQuestion->QuestionId;
			$question->QuestionVersionId = 0;
			$question->QuestionVersion->QuestionVersionId = 0;
			$question->QuestionVersion->Score = 0;
			$question->QuestionVersion->Data = null;
			
			$question->store();
		}
			
		return $bankQuestion;
	}
	
	function addQuestionFromBank($questionId, $quizId, $categoryId, $score, $userId)
	{
		$bankModel = AriModel::getInstance('Bankquestion', $this->getFullPrefix());
		$bankQuestion = $bankModel->getQuestion($questionId);
		if (empty($bankQuestion))
			return null;

		$question =& $this->getTable();
		$question->BankQuestionId = $questionId;
		$question->QuizId = $quizId;
		$question->QuestionCategoryId = $question->QuestionVersion->QuestionCategoryId = $categoryId;
		$question->QuestionTypeId = $question->QuestionVersion->QuestionTypeId = $bankQuestion->QuestionTypeId;
		$question->QuestionVersion->Score = $score;
		$question->QuestionVersion->Data = null;
		$question->CreatedBy = $question->QuestionVersion->CreatedBy = $userId;

		if (!$question->store())
			return null;

		return $question;
	}
}