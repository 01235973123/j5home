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
AriKernel::import('Utils.ArrayHelper');
AriKernel::import('CSV.CSVParser');
AriKernel::import('Application.ARIQuiz.CSVExport.QuestionFactory');

class AriQuizModelQuestionsCSVExport extends AriModel 
{
	function exportQuizQuestions($idList)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		$quizQuestionModel = AriModel::getInstance('Quizquestion', $this->getFullPrefix());
		$quizQuestionsModel = AriModel::getInstance('Quizquestions', $this->getFullPrefix());
		$categories = $quizQuestionsModel->getCategoriesByQuestionId($idList);
		$fields = array();
		$data = array();
		foreach ($idList as $questionId)
		{
			$question = $quizQuestionModel->getQuestion($questionId);
			if (is_null($question))
				continue;

			$isBankQuestion = !isset($question->BankQuestion);
			$questionType = !$isBankQuestion ? $question->getQuestionType() : $question->QuestionVersion->QuestionType;
			$csvQuestion = AriQuizCSVExportQuestion::getQuestion($questionType->ClassName);
			if (is_null($csvQuestion))
				continue ;
				
			$questionData = $csvQuestion->getData($question, $categories);
			if (!is_array($questionData) || count($questionData) == 0)
				continue ;

			$csvQuestionKeys = array_keys($questionData[0]);
			$diffKeys = array_diff($csvQuestionKeys, $fields);
			if (is_array($diffKeys) && count($diffKeys) > 0)
				$fields = array_merge($fields, $diffKeys);
			
			$data = array_merge($data, $questionData);
			$data[] = array();
		}

		$csvParser = new AriCSVParser();
		$csvData = $csvParser->unparse($data, $fields, false, false, null, true);

		return "\xEF\xBB\xBF" . $csvData;
	}
	
	function exportBankQuestions($idList)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		$bankQuestionModel = AriModel::getInstance('Bankquestion', $this->getFullPrefix());
		$bankQuestionsModel = AriModel::getInstance('Bankquestions', $this->getFullPrefix());
		$categories = $bankQuestionsModel->getCategoriesByQuestionId($idList);
		$fields = array();
		$data = array();
		foreach ($idList as $questionId)
		{
			$question = $bankQuestionModel->getQuestion($questionId);
			if (is_null($question))
				continue;
				
			$csvQuestion = AriQuizCSVExportQuestion::getQuestion($question->QuestionVersion->QuestionType->ClassName);
			if (is_null($csvQuestion))
				continue ;
				
			$questionData = $csvQuestion->getData($question, $categories);
			if (!is_array($questionData) || count($questionData) == 0)
				continue ;

			$csvQuestionKeys = array_keys($questionData[0]);
			$diffKeys = array_diff($csvQuestionKeys, $fields);
			if (is_array($diffKeys) && count($diffKeys) > 0)
				$fields = array_merge($fields, $diffKeys);
			
			$data = array_merge($data, $questionData);
			$data[] = array();
		}

		$csvParser = new AriCSVParser();
		$csvData = $csvParser->unparse($data, $fields, false, false, null, true);

		return "\xEF\xBB\xBF" . $csvData;
	}
}