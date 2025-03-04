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

class AriQuizCSVExportSingleQuestion extends AriQuizCSVExportQuestionBase
{
	function _prepareCSVData($data, $questionType, $questionData, $questionOverridenData)
	{
		$questionEntity = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
		$questionParams = $questionEntity->getDataFromXml($questionData, false, $questionOverridenData);
		$extraQuestionParams = $questionEntity->getExtraDataFromXml($questionData);

		$data[0]['Randomize'] = !empty($extraQuestionParams['randomizeOrder']) ? '1' : '0';
		$data[0]['View'] = !empty($extraQuestionParams['view']) ? $extraQuestionParams['view'] : ARIQUIZ_SINGLEQUESTION_VIEWTYPE_RADIO;

		foreach ($questionParams as $questionAnswer)
		{
			$score = floatval(AriUtils::getParam($questionAnswer, 'tbxScore'));
			$isCorrect = !!AriUtils::getParam($questionAnswer, 'hidCorrect');

			$data[] = array(
				'Answers' => AriUtils::getParam($questionAnswer, 'tbxAnswer'),
				'Correct' => $isCorrect ? '1' : '',
				'Score' => ($score && !$isCorrect) ? $score . '%' : ''
			);
		}

		return $data;
	}
}