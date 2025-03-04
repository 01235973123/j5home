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

class AriQuizCSVImportSingleQuestion extends AriQuizCSVImportQuestionBase
{
	var $_type = 'SingleQuestion';

	/*
	[chkSQRandomizeOrder] => 1
    [ddlSQView] => 1
    [tbxAnswer_0] => answer 1
    [hidQueId_0] => 
    [hidCorrect_0] => 
    [tbxScore_0] => 34
    [tblQueContainer_hdnstatus_0] => 
    [tbxAnswer_1] => answer 2
    [hidQueId_1] => 
    [hidCorrect_1] => 
    [tbxScore_1] => 
    [tblQueContainer_hdnstatus_1] => 
    [rbCorrect] => true
    [tbxAnswer_2] => answer 3
    [hidQueId_2] => 
    [hidCorrect_2] => true
    [tblQueContainer_hdnstatus_2] =>  
	 */
	function getXml($data)
	{
		$request = $_REQUEST;
		$random = AriUtils::parseValueBySample(AriUtils::getParam($data, 'Randomize'), false);
		if ($random)
			$_REQUEST['chkSQRandomizeOrder'] = '1';
		else
		{ 
			JRequest::setVar('chkSQRandomizeOrder');			
		}
		
		$viewType = AriUtils::getParam($data, 'View');
		if ($viewType === '1' || $viewType === '0')
			$_REQUEST['ddlSQView'] = $viewType;
		else
			$_REQUEST['ddlSQView'] = strtolower($viewType) == 'dropdown' ? '1' : '0';

		$childs = $data['_Childs'];
		$correct = false;
		$i = 0;
		foreach ($childs as $child)
		{
			$answer = trim(AriUtils::getParam($child, 'Answers', ''));
			if (empty($answer))
				continue ;
				
			$score = floatval(AriUtils::getParam($child, 'Score'));
			$correct = (!$correct && AriUtils::parseValueBySample(
				AriUtils::getParam($child, 'Correct'),
				false));

			$_REQUEST['tbxAnswer_' . $i] = $answer;
			$_REQUEST['tbxScore_' . $i] = $score;
			$_REQUEST['hidCorrect_' . $i] = $correct ? 'true' : '';
			$_REQUEST['tblQueContainer_hdnstatus_' . $i] = '';

			++$i;
		}

		$xml = $this->_question->getXml();
		$_REQUEST = $request;

		return $xml;
	}
}