<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

AriKernel::import('Application.ARIQuiz.Questions.QuestionFactory');

class AriQuizCSVExportQuestionBase extends JObject
{
	function getData($question, $categories)
	{
		if (empty($question))
			return null;

		$isBankQuestion = !isset($question->BankQuestion);
		$bankQuestion = !$isBankQuestion ? $question->BankQuestion : null;
		$bankQuestionVersion = !$isBankQuestion ? $bankQuestion->QuestionVersion : null;
		$questionVersion = $question->QuestionVersion;
		$questionType = !$isBankQuestion ? $question->getQuestionType() : $questionVersion->QuestionType;
		$basedOnBank = !$isBankQuestion ? $question->isBasedOnBankQuestion() : false;
		$baseQuestionVersion = $basedOnBank
			? $bankQuestionVersion
			: $questionVersion;
		$questionData = $baseQuestionVersion->Data;
		$questionOverridenData = $basedOnBank ? $questionVersion->Data : null;
		
		$score = $questionVersion->Score != 0 ? $questionVersion->Score : $baseQuestionVersion->Score;
		$penalty = $questionVersion->Penalty != 0 ? $questionVersion->Penalty : $baseQuestionVersion->Penalty;
		$catId = $questionVersion->QuestionCategoryId;
		
		$data = array();
		$data[] = array(
			'Type' => $questionType->ClassName,
			'Question' => $baseQuestionVersion->Question,
			'Note' => $baseQuestionVersion->Note,
			'Category' => isset($categories[$catId]) ? $categories[$catId] : '',
			'Randomize' => '',
			'View' => '',
			'Answers' => '',
			'Score' => $score,
			'Penalty' => $penalty,
			'Correct' => ''
		);

		return $this->_prepareCSVData($data, $questionType, $questionData, $questionOverridenData);
	}
	
	function _prepareCSVData($data, $questionType, $questionData, $questionOverridenData)
	{
		return $data;
	}
}