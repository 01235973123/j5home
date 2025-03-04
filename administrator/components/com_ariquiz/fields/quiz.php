<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die ('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/quizzes.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/quiz.php';

AriKernel::import('Data.DataFilter');
AriKernel::import('Xml.XmlHelper');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

AriModel::addTablePath(JPATH_ADMINISTRATOR . '/components/com_ariquiz/tables');

class JFormFieldQuiz extends JFormField
{
	protected $type = 'Quiz';
	
	function getInput()
	{
		return $this->fetchElement(
			$this->element['name'], 
			$this->value, 
			$this->element, 
			$this->name
		);
	}
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$size = intval(AriXmlHelper::getAttribute($node, 'size'), 10);

		$multiple = (bool)AriXmlHelper::getAttribute($node, 'multiple');
		$editable = (bool)AriXmlHelper::getAttribute($node, 'editable', true);
		$lazyLoad = (bool)AriXmlHelper::getAttribute($node, 'lazy_load');

		if ($value && !$editable)
		{
			$quizModel = AriModel::getInstance('Quiz', 'AriQuizModel');
			$quiz = $quizModel->getQuiz(intval($value, 10));
			if (!is_null($quiz))
			{
				return sprintf('<div class="ari-el-label">%1$s</div><input type="hidden" id="%2$s" name="%3$s" value="%4$d" />',
					$quiz->QuizName,
					$control_name . $name,
					$control_name . '[' . $name . ']',
					$quiz->QuizId);
			}
		}

		$quizzes = array();
		if ($lazyLoad)
		{
			$initLabel = AriXmlHelper::getAttribute($node, 'init_label', 'COM_ARIQUIZ_LABEL_NONE');
			
			$initQuiz = new stdClass();
			$initQuiz->QuizId = 0;
			$initQuiz->QuizName = JText::_($initLabel);
			
			$quizzes = array($initQuiz);
			
			$this->registerAssets();
			$this->addScript($control_name . $name);
		}
		else 
		{
			$noneLbl = null;
			if (!$multiple)
			{
				$noneLbl = AriXmlHelper::getAttribute($node, 'none_label');
				$noneLbl = !empty($noneLbl) ? (string)$noneLbl : 'COM_ARIQUIZ_LABEL_NONE';
			}
			
			$status = AriXmlHelper::getAttribute($node, 'status');
			$status = !empty($status) ? (string)$status : null;

			$quizzes = $this->getQuizList($noneLbl, $status);
		}

		return JHTML::_(
			'select.genericlist', 
			$quizzes, 
			$control_name . ($multiple ? '[]' : ''), 
			'class="inputbox"' . ($multiple ? ' multiple="multiple"' . ($size ? ' size="' . $size . '"' : '') : ''), 
			'QuizId', 
			'QuizName', 
			$value,
			$control_name . $name);		
	}

	function addScript($ctrlId)
	{
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration(sprintf(
			'ARIQuizElement.initEl("%1$s");',
			$ctrlId
		));
	}
	
	function registerAssets()
	{
		$doc =& JFactory::getDocument();
		
		$uri = JURI::root(true) . '/administrator/components/com_ariquiz/elements/assets/quiz/';
		$doc->addScript($uri . 'quiz.js');
	}
	
	function getQuizList($noneLabel = null, $status = null)
	{
		$filter = new AriDataFilter(
			array(
				'sortField' => 'QuizName', 
				'dir' => ARI_DATAFILTER_SORT_ASC
			)
		);

		AriModel::addTablePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_ariquiz' . DS . 'tables');
		$quizzesModel = AriModel::getInstance('Quizzes', 'AriQuizModel');
		if ($status && defined($status))
			$filter->setConfigValue('filter', array('Status' => constant($status)));
		
		$quizzes = $quizzesModel->getQuizList($filter);

		if (!empty($noneLabel))
		{
			$emptyQuiz = new stdClass();
			$emptyQuiz->QuizId = 0;
			$emptyQuiz->QuizName = JText::_($noneLabel);
			array_unshift($quizzes, $emptyQuiz);
		}

		return $quizzes;
	}
	
	function extGetQuizList()
	{
		$quizzes = $this->getQuizList('COM_ARIQUIZ_LABEL_NONE');
		
		return $quizzes;
	}
}