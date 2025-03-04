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

AriKernel::import('Application.ARIQuiz.Questions.QuestionFactory');

class AriQuizCSVImportQuestionBase extends JObject
{
	var $_type;
	
	function __construct()
	{
		$this->_question = AriQuizQuestionFactory::getQuestion($this->_type);
	}
	
	function getXml($data)
	{
		return null;
	}
	
	function getMaximumQuestionScore($score, $xml)
	{
		return $this->_question->getMaximumQuestionScore($score, $xml);
	}
}