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

class AriQuizCSVExportFreeTextQuestion extends AriQuizCSVExportQuestionBase
{
	var $_type = 'FreeTextQuestion';

	function _prepareCSVData($data, $questionType, $questionData, $questionOverridenData)
	{
		$questionEntity = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
		$questionParams = $questionEntity->getDataFromXml($questionData, false, $questionOverridenData);

		$data[0]['Case Insensitive'] = '';

		foreach ($questionParams as $questionAnswer)
		{
			$data[] = array(
				'Answers' => AriUtils::getParam($questionAnswer, 'tbxAnswer'),
				'Score' => intval(AriUtils::getParam($questionAnswer, 'tbxScore'), 10) . '%',
				'Case Insensitive' => AriUtils::getParam($questionAnswer, 'cbCI') ? '1' : ''
			);
		}

		return $data;
	}
}