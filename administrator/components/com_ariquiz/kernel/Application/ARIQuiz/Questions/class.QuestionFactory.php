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

jimport('joomla.filter.filterinput');

AriKernel::import('Application.ARIQuiz.Questions.QuestionBase');

class AriQuizQuestionFactory
{
	static public function getQuestion($type)
	{
		$question = null;

		$filter = JFilterInput::getInstance();
		$type = $filter->clean($type, 'WORD');
		if (empty($type))
			return $question;

		AriKernel::import('Application.ARIQuiz.Questions.Types.' . $type);
		$className = 'AriQuizQuestion' . $type;
		if (class_exists($className))
			$question = new $className();

		return $question;
	}
}