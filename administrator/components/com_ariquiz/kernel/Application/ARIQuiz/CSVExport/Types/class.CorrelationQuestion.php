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

class AriQuizCSVExportCorrelationQuestion extends AriQuizCSVExportQuestionBase
{
	function _prepareCSVData($data, $questionType, $questionData, $questionOverridenData)
	{
		$questionEntity = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
		$questionParams = $questionEntity->getDataFromXml($questionData, false, $questionOverridenData);
		$extraQuestionParams = $questionEntity->getExtraDataFromXml($questionData);

		$data[0]['Randomize'] = !empty($extraQuestionParams['randomizeOrder']) ? '1' : '0';
		$data[0]['Correlation'] = '';

		foreach ($questionParams as $questionAnswer)
		{
			$data[] = array(
				'Answers' => AriUtils::getParam($questionAnswer, 'tbxLabel'),
				'Correlation' => AriUtils::getParam($questionAnswer, 'tbxAnswer')
			);
		}

		return $data;
	}
}