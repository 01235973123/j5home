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

class AriQuizCSVImportMultipleQuestion extends AriQuizCSVImportQuestionBase
{
	var $_type = 'MultipleQuestion';

	/*
	[chkMQRandomizeOrder] => 1
    [cbCorrect_0] => true
    [tbxAnswer_0] => Answer 1
    [hidQueId_0] => 4b618dac069f50.10167037
    [tblQueContainer_hdnstatus_0] => 
    [tbxAnswer_1] => Answer 2
    [hidQueId_1] => 4b618dac06b096.99581238
    [tblQueContainer_hdnstatus_1] => 
    [cbCorrect_2] => true
    [tbxAnswer_2] => Answer 3
    [hidQueId_2] => 4b618dac06bf19.66739017
    [tblQueContainer_hdnstatus_2] => 
    [hidPercentScore] => [{"correct":[false,true,false],"id":"","override":false,"score":31},{"correct":[true,false,true],"id":"","override":false,"score":34}]  
	 */
	function getXml($data)
	{
		$request = $_REQUEST;
		
		$random = AriUtils::parseValueBySample(AriUtils::getParam($data, 'Randomize'), false);
		if ($random)
			$_REQUEST['chkMQRandomizeOrder'] = '1';

		$childs = $data['_Childs'];
		$i = 0;
		foreach ($childs as $child)
		{
			$answer = trim(AriUtils::getParam($child, 'Answers', ''));
			if (empty($answer))
				continue ;
				
			$score = intval(AriUtils::getParam($child, 'Score'), 10);
			$correct = AriUtils::parseValueBySample(
				AriUtils::getParam($child, 'Correct'),
				false);

			$_REQUEST['tbxAnswer_' . $i] = $answer;
			$_REQUEST['tblQueContainer_hdnstatus_' . $i] = '';
			if ($correct)
				$_REQUEST['cbCorrect_' . $i] = 'true';

			++$i;
		}
		
		$i = 1;
		$additionalData = array();
		while (array_key_exists('Correct_' . $i, $data) && array_key_exists('Score_' . $i, $data))
		{
			$correctList = array();
			$score = intval(AriUtils::getParam($data, 'Score_' . $i), 10);
			$correctKey = 'Correct_' . $i;
			foreach ($childs as $child)
			{
				$answer = trim(AriUtils::getParam($child, 'Answers', ''));
				if (empty($answer))
					continue ;
					
				$correct = AriUtils::parseValueBySample(
					AriUtils::getParam($child, $correctKey),
					false);
				$correctList[] = $correct;
			}

			$additionalData[] = array(
				'correct' => $correctList,
				'override' => false,
				'score' => $score);
			
			
			++$i;
		}
		$_REQUEST['hidPercentScore'] = json_encode($additionalData);

		$xml = $this->_question->getXml();
		$_REQUEST = $request;

		return $xml;
	}
}