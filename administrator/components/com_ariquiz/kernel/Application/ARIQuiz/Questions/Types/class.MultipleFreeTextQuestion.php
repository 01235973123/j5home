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

/*
<items>
  <item>
<label></label>
<answers>
 <answer score=""></answer>
</answer>
</item>
</items>
 */

define ('ARIQUIZ_MULTIPLEFREETEXTQUESTION_DOC_TAG', 'items');
define ('ARIQUIZ_MULTIPLEFREETEXTQUESTION_ITEM_TAG', 'item');
define ('ARIQUIZ_MULTIPLEFREETEXTQUESTION_RANDOM_ATTR', 'random');
define ('ARIQUIZ_MULTIPLEFREETEXTQUESTION_ANSWERS_TAG', 'answers');
define ('ARIQUIZ_MULTIPLEFREETEXTQUESTION_LBLITEM_TAG', 'label');
define ('ARIQUIZ_MULTIPLEFREETEXTQUESTION_ANSITEM_TAG', 'answer');
define ('ARIQUIZ_MULTIPLEFREETEXTQUESTION_ID_ATTR', 'id');
define ('ARIQUIZ_MULTIPLEFREETEXTQUESTION_SCORE_ATTR', 'score');

AriKernel::import('Application.ARIQuiz.Questions.QuestionBase');
AriKernel::import('Web.Controls.Advanced.MultiplierControls');
AriKernel::import('Xml.XmlHelper');

if (J3_4)
{
    jimport('vendor.joomla.string.src.phputf8.utf8');
    jimport('vendor.joomla.string.src.phputf8.strcasecmp');
}
else
{
    jimport('phputf8.utf8');
    jimport('phputf8.strcasecmp');
}

class AriQuizQuestionMultipleFreeTextQuestion extends AriQuizQuestionBase
{
    function isScoreSpecific()
    {
        return true;
    }

    function calculateMaximumScore($score, $xml, $overrideXml = null)
    {
        $score = 0;
        $data = $this->getDataFromXml($xml, $overrideXml);

        if (is_array($data))
        {
            foreach ($data as $dataItem)
            {
                $answerMaximumScore = 0;
                foreach ($dataItem['answers'] as $answer)
                {
                    $answerScore = $answer['score'];
                    if ($answerScore > $answerMaximumScore)
                        $answerMaximumScore = $answerScore;
                }

                $score += $answerMaximumScore;
            }
        }

        return $score;
    }

    function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false, $initData = null)
    {
        $data = $this->getDataFromXml($xml, $decodeHtmlEntity, null, $initData);
        $clientData = array();
        if ($data)
        {
            foreach ($data as $dataItem)
            {
                $placeHolder = $dataItem['question'];
                $placeHolderId = $dataItem['questionId'];
                $clientData[$placeHolderId] = array(
                    'placeHolderId' => $placeHolderId,
                    'placeHolder' => $placeHolder,
                    'options' => array(),
                    'answer' => ''
                );

                foreach ($dataItem['answers'] as $answer)
                {
                    $clientData[$placeHolderId]['options'][] = array(
                        'hidQueId' => $answer['answerId']
                    );
                }
            }

            $clientData = $this->applyUserData($clientData, $userXml, $decodeHtmlEntity);
        }

        return $clientData;
    }

    function applyUserData($data, $userXml, $decodeHtmlEntity = false)
    {
        if (empty($data) || empty($userXml)) return $data;

        $userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
        if (is_array($userData) && count($userData) > 0)
        {
            foreach ($userData as $dataItem)
            {
                $placeHolderId = $dataItem['questionId'];
                $answer = '';

                if (isset($dataItem['answers'][0]['answer']) && strlen($dataItem['answers'][0]['answer']))
                    $answer = $dataItem['answers'][0]['answer'];

                if (strlen($answer) > 0 && isset($data[$placeHolderId]))
                {
                    $data[$placeHolderId]['answer'] = $answer;
                }
            }
        }

        return $data;
    }

    function getDataFromXml($xml, $htmlSpecialChars = true, $overrideXml = null, $initData = null)
    {
        $data = null;
        if (!empty($xml))
        {
            $xmlHandler = AriXmlHelper::getXML($xml, false);
            $xmlDoc =& $xmlHandler->document;
            if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_MULTIPLEFREETEXTQUESTION_DOC_TAG)
                return $data;

            $childs = $xmlDoc->children();
            if (!empty($childs))
            {
                if ($htmlSpecialChars)
                    AriKernel::import('String.String');

                $data = array();
                foreach ($childs as $child)
                {
                    $answersTag = ARIQUIZ_MULTIPLEFREETEXTQUESTION_ANSWERS_TAG;
                    $labelTag = ARIQUIZ_MULTIPLEFREETEXTQUESTION_LBLITEM_TAG;

                    $answers = $child->$answersTag;
                    $answers = $answers ? $answers[0] : null;

                    $label = $child->$labelTag;
                    $label = $label ? $label[0] : null;

                    $labelStr = AriXmlHelper::getData($label);
                    if ($htmlSpecialChars)
                    {
                        $labelStr = AriString::htmlSpecialChars($labelStr);
                    }

                    $lblId = AriXmlHelper::getAttribute($label, ARIQUIZ_MULTIPLEFREETEXTQUESTION_ID_ATTR);

                    $answersData = array();
                    if ($answers)
                    {
                        $answerNodeList = $answers->children();
                        foreach ($answerNodeList as $answerNode)
                        {
                            $answerStr = AriXmlHelper::getData($answerNode);
                            if ($htmlSpecialChars)
                            {
                                $answerStr = AriString::htmlSpecialChars($answerStr);
                            }

                            $answerId = AriXmlHelper::getAttribute($answerNode, ARIQUIZ_MULTIPLEFREETEXTQUESTION_ID_ATTR);
                            $score = floatval(AriXmlHelper::getAttribute($answerNode, ARIQUIZ_MULTIPLEFREETEXTQUESTION_SCORE_ATTR));

                            $answersData[] = array(
                                'answer' => $answerStr,
                                'answerId' => $answerId,
                                'score' => $score
                            );
                        }
                    }

                    $data[$lblId] = array(
                        'question' => $labelStr,
                        'questionId' => $lblId,
                        'answers' => $answersData,
                    );
                }

                if (!empty($initData['labels']))
                {
                    $sortedData = array();

                    foreach ($initData['labels'] as $dataItem)
                    {
                        $id = $dataItem['id'];

                        $sortedData[] = $data[$id];
                    }

                    $data = $sortedData;
                }
                else
                {
                    $data = array_values($data);
                }
            }
        }

        return $data;
    }

    function getFrontXml($questionId)
    {
        $tbxAnswer = JRequest::getVar('tbxAnswer_' . $questionId, array(), 'default', 'none', JREQUEST_ALLOWRAW);
        $variant = array();

        if (is_array($tbxAnswer) && count($tbxAnswer) > 0)
        {
            foreach ($tbxAnswer as $id => $value)
            {
                $variant[$id] = $value;
            }
        }

        return $this->_createFrontXml($variant);
    }

    function _createFrontXml($correlation)
    {
        $xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_MULTIPLEFREETEXTQUESTION_DOC_TAG), false);
        $xmlDoc = $xmlHandler->document;

        if (is_array($correlation))
        {
            foreach ($correlation as $key => $value)
            {
                $xmlItem =& $xmlDoc->addChild(ARIQUIZ_MULTIPLEFREETEXTQUESTION_ITEM_TAG);

                $labelXmlItem =& $xmlItem->addChild(ARIQUIZ_MULTIPLEFREETEXTQUESTION_LBLITEM_TAG);
                $labelXmlItem->addAttribute(ARIQUIZ_MULTIPLEFREETEXTQUESTION_ID_ATTR, $key);

                $answersXmlItem =& $xmlItem->addChild(ARIQUIZ_MULTIPLEFREETEXTQUESTION_ANSWERS_TAG);
                $answersXmlItem->addChild(ARIQUIZ_MULTIPLEFREETEXTQUESTION_ANSITEM_TAG, $value);
            }
        }

        return AriXmlHelper::toString($xmlDoc);
    }

    function getScore($xml, $baseXml, $score, $penalty = 0.00, $overrideXml = null, $noPenaltyForEmptyAnswer = false)
    {
        $userScore = $noPenaltyForEmptyAnswer ? 0.00 : -$penalty;
        if (!empty($xml) && !empty($baseXml))
        {
            $data = $this->getDataFromXml($baseXml);
            $scoreMapping = array();
            if (!empty($data))
            {
                foreach ($data as $dataItem)
                {
                    $labelId = $dataItem['questionId'];
                    $scoreMapping[$labelId] = array();

                    $scoreMapping[$labelId] = $dataItem['answers'];
                }
            }

            $xData = $this->getDataFromXml($xml);
            if (count($scoreMapping) > 0)
            {
                if ($xData)
                {
                    foreach ($xData as $dataItem)
                    {
                        $questionId = $dataItem['questionId'];
                        $answer = $dataItem['answers'][0]['answer'];

                        if (key_exists($questionId, $scoreMapping))
                        {
                            foreach ($scoreMapping[$questionId] as $answerItem)
                            {
                                $answerVariant = $answerItem['answer'];
                                $isCorrectAnswer = (utf8_strcasecmp($answer, $answerVariant) === 0);

                                if ($isCorrectAnswer)
                                {
                                    $userScore += $answerItem['score'];
                                    break;
                                }
                            }
                        }
                    }
                }
                else if ($userScore > $score)
                    $userScore = $score;
            }

            if ($noPenaltyForEmptyAnswer && is_array($xData) && count($xData) > 0)
                $userScore = -$penalty;
        }

        return $userScore;
    }

    function isCorrect($xml, $baseXml, $overrideXml = null)
    {
        $isCorrect = false;
        if (!empty($xml) && !empty($baseXml))
        {
            $data = $this->getDataFromXml($baseXml);
            $xData = $this->getDataFromXml($xml);

            if (is_array($data) && is_array($xData))
            {
                $scoreMapping = array();
                foreach ($data as $dataItem)
                {
                    $labelId = $dataItem['questionId'];

                    $scoreMapping[$labelId] = $dataItem['answers'];
                }

                $userAnswersMapping = array();
                foreach ($xData as $dataItem)
                {
                    $questionId = $dataItem['questionId'];
                    $answer = $dataItem['answers'][0]['answer'];

                    $userAnswersMapping[$questionId] = $answer;
                }

                $isCorrect = true;
                foreach ($scoreMapping as $questionId => $answers)
                {
                    $isCorrect = false;
                    if (isset($userAnswersMapping[$questionId]))
                    {
                        $userAnswer = $userAnswersMapping[$questionId];
                        foreach ($answers as $answerItem)
                        {
                            $answerVariant = $answerItem['answer'];
                            $isCorrectAnswer = (utf8_strcasecmp($userAnswer, $answerVariant) === 0);

                            if ($isCorrectAnswer)
                            {
                                $isCorrect = true;
                                break;
                            }
                        }
                    }

                    if (!$isCorrect)
                        break;
                }
            }
        }

        return $isCorrect;
    }

    function getXml()
    {
        $jInput = JFactory::getApplication()->input;
        $questionData = $jInput->get('questionData', null, 'RAW');
        $questionData = $questionData ? json_decode($questionData, true) : null;

        $xmlStr = null;
        if (is_array($questionData))
        {
            $xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_MULTIPLEFREETEXTQUESTION_DOC_TAG), false);
            $xmlDoc = $xmlHandler->document;

            $randomizeOrder = JRequest::getBool('chkCQRandomizeOrder', false);
            if ($randomizeOrder)
            {
                $xmlDoc->addAttribute(ARIQUIZ_MULTIPLEFREETEXTQUESTION_RANDOM_ATTR, 'true');
            }

            foreach ($questionData as $questionItem)
            {
                $label = trim($questionItem['question']);
                if (empty($label))
                    continue ;

                $xmlItem =& $xmlDoc->addChild(ARIQUIZ_MULTIPLEFREETEXTQUESTION_ITEM_TAG);

                $labelId = !empty($questionItem['questionId'])
                    ? $questionItem['questionId']
                    : str_replace('.', '', uniqid('', true));

                $labelNode =& $xmlItem->addChild(ARIQUIZ_MULTIPLEFREETEXTQUESTION_LBLITEM_TAG);
                $labelNode->addAttribute(ARIQUIZ_MULTIPLEFREETEXTQUESTION_ID_ATTR, $labelId);
                AriXmlHelper::setData($labelNode, $label);

                $answersNode =& $xmlItem->addChild(ARIQUIZ_MULTIPLEFREETEXTQUESTION_ANSWERS_TAG);

                if (!empty($questionItem['answers']))
                {
                    foreach ($questionItem['answers'] as $answerItem)
                    {
                        $answer = trim($answerItem['answer']);
                        if (strlen($answer) == 0)
                            continue ;

                        $score = floatval($answerItem['score']);
                        $answerId = !empty($answerItem['answerId'])
                            ? $answerItem['answerId']
                            : str_replace('.', '', uniqid('', true));

                        $answerNode = $answersNode->addChild(ARIQUIZ_MULTIPLEFREETEXTQUESTION_ANSITEM_TAG);
                        $answerNode->addAttribute(ARIQUIZ_MULTIPLEFREETEXTQUESTION_ID_ATTR, $answerId);
                        $answerNode->addAttribute(ARIQUIZ_MULTIPLEFREETEXTQUESTION_SCORE_ATTR, $score);
                        AriXmlHelper::setData($answerNode, $answer);
                    }
                }
            }

            $xmlStr = AriXmlHelper::toString($xmlDoc);
        }

        return $xmlStr;
    }

    function isEmptyAnswer($xml)
    {
        $isEmpty = parent::isEmptyAnswer($xml);
        if ($isEmpty)
            return true;

        $xData = $this->getDataFromXml($xml);
        foreach ($xData as $dataItem)
        {
            if (isset($dataItem['answers'][0]['answer']) && strlen($dataItem['answers'][0]['answer']) > 0)
                return false;
        }

        return true;
    }
}