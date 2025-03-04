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

class AriQuizCSVExportMultipleFreeTextQuestion extends AriQuizCSVExportQuestionBase
{
    var $_type = 'MultipleFreeTextQuestion';

    function _prepareCSVData($data, $questionType, $questionData, $questionOverridenData)
    {
        $questionEntity = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
        $questionParams = $questionEntity->getDataFromXml($questionData, false, $questionOverridenData);

        $data[0]['Position'] = '';

        foreach ($questionParams as $questionItem)
        {
            foreach ($questionItem['answers'] as $questionAnswer)
            {
                $data[] = array(
                    'Answers' => AriUtils::getParam($questionAnswer, 'answer'),
                    'Score' => floatval(AriUtils::getParam($questionAnswer, 'score')),
                    'Position' => $questionItem['question']
                );
            }
        }

        return $data;
    }
}