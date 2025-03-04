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

class AriQuizCSVImportCorrelationQuestion extends AriQuizCSVImportQuestionBase
{
	var $_type = 'CorrelationQuestion';

	/*
	[chkCQRandomizeOrder] => 1
    [tbxLabel_0] => ans 1
    [hidLabelId_0] => 
    [tbxAnswer_0] => cor 1
    [hidAnswerId_0] => 
    [tblQueContainer_hdnstatus_0] => 
    [tbxLabel_1] => ans 2
    [hidLabelId_1] => 
    [tbxAnswer_1] => cor 2
    [hidAnswerId_1] => 
    [tblQueContainer_hdnstatus_1] => 
    [tbxLabel_2] => ans 3
    [hidLabelId_2] => 
    [tbxAnswer_2] => cor 3
    [hidAnswerId_2] => 
    [tblQueContainer_hdnstatus_2] =>
	 */
	function getXml($data)
	{
		$request = $_REQUEST;
		
		$random = AriUtils::parseValueBySample(AriUtils::getParam($data, 'Randomize'), false);
		if ($random)
			$_REQUEST['chkCQRandomizeOrder'] = '1';

		$childs = $data['_Childs'];
		$correct = false;
		$i = 0;
		foreach ($childs as $child)
		{
			$answer = trim(AriUtils::getParam($child, 'Answers', ''));
			$corellation = trim(AriUtils::getParam($child, 'Correlation', ''));
			if (empty($answer) || empty($corellation))
				continue ;

			$_REQUEST['tbxLabel_' . $i] = $answer;
			$_REQUEST['tbxAnswer_' . $i] = $corellation;
			$_REQUEST['tblQueContainer_hdnstatus_' . $i] = '';

			++$i;
		}

		$xml = $this->_question->getXml();
		$_REQUEST = $request;

		return $xml;
	}
}