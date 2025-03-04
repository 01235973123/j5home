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

AriKernel::import('Application.ARIQuiz.CSVExport.QuestionBase');

class AriQuizCSVExportMultipleQuestion extends AriQuizCSVExportQuestionBase
{
	function _prepareCSVData($data, $questionType, $questionData, $questionOverridenData)
	{
		$questionEntity = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
		$questionParams = $questionEntity->getDataFromXml($questionData, false, $questionOverridenData);
		$extraQuestionParams = $questionEntity->getExtraDataFromXml($questionData);
		$questionScoreParams = $questionEntity->getScoreDataFromXml($questionData, $questionOverridenData);
		$isMultipleScore = is_array($questionScoreParams) && count($questionScoreParams) > 0;

		$data[0]['Randomize'] = !empty($extraQuestionParams['randomizeOrder']) ? '1' : '0';
		if ($isMultipleScore)
		{
			for ($i = 0; $i < count($questionScoreParams); $i++)
			{
				$score = intval(AriUtils::getParam($questionScoreParams[$i], 'score'), 10);
				$data[0]['Correct_' . ($i + 1)] = '';
				$data[0]['Score_' . ($i + 1)] = $score . '%';
			}
		}

		foreach ($questionParams as $questionAnswer)
		{
			$dataItem = array(
				'Answers' => AriUtils::getParam($questionAnswer, 'tbxAnswer'),
				'Correct' => AriUtils::getParam($questionAnswer, 'cbCorrect') ? '1' : ''
			);
			
			if ($isMultipleScore)
			{
				$answerId = AriUtils::getParam($questionAnswer, 'hidQueId');
				if ($answerId)
				{
					for ($i = 0; $i < count($questionScoreParams); $i++)
					{
						$correctAnswers = AriUtils::getParam($questionScoreParams[$i], 'correct');
						if (is_array($correctAnswers) && in_array($answerId, $correctAnswers))
							$dataItem['Correct_' . ($i + 1)] = '1';
					}
				}
			}
			
			$data[] = $dataItem;
		}

		return $data;
	}
}