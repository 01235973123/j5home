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

AriKernel::import('Application.ARIQuiz.CSVImport.QuestionBase');

class AriQuizCSVImportMultipleFreeTextQuestion extends AriQuizCSVImportQuestionBase
{
    var $_type = 'MultipleFreeTextQuestion';

    /*
    [tbxAnswer_0] => Answer 1
    [tbxScore_0] => 100
    [cbCI_0] => on
    [hidQueId_0] => 4b619688261a36.80002523
    [tblQueContainer_hdnstatus_0] =>
    [tbxAnswer_1] => Answer 2
    [tbxScore_1] => 15
    [hidQueId_1] => 4b619688262b70.31202073
    [tblQueContainer_hdnstatus_1] =>
    [tbxAnswer_2] => Answer 3
    [tbxScore_2] => 1
    [cbCI_2] => on
    [hidQueId_2] => 4b619688263c13.76966047
    [tblQueContainer_hdnstatus_2] =>
     */
    function getXml($data)
    {
        $request = $_REQUEST;

        $questionData = array();
        $prepareData = array();
        $childs = $data['_Childs'];

        foreach ($childs as $child)
        {
            $answer = trim(AriUtils::getParam($child, 'Answers', ''));
            if (empty($answer))
                continue ;

            $pos = AriUtils::getParam($child, 'Position', '');
            $score = floatval(AriUtils::getParam($child, 'Score'));

            if (!isset($prepareData[$pos]))
                $prepareData[$pos] = array();

            $prepareData[$pos][] = array(
                'answer' => $answer,
                'score' => $score
            );
        }

        foreach ($prepareData as $placeholder => $dataItem)
        {
            $questionData[] = array(
                'question' => $placeholder,
                'answers' => $dataItem
            );
        }

        $_REQUEST['questionData'] = json_encode($questionData);

        $xml = $this->_question->getXml();
        $_REQUEST = $request;

        return $xml;
    }
}