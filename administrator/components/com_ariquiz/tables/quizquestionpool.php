<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Joomla.Tables.Table');

class AriQuizTableQuizquestionpool extends AriTable 
{
	var $QuestionPoolId = null;
	var $QuizId = 0;
	var $QuestionCategoryId = 0;
	var $BankCategoryId = 0;
	var $QuestionCount = 0;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquiz_quiz_questionpool', 'QuestionPoolId', $db);
	}
}