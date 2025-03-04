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

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

class AriQuizViewQuiz extends AriQuizAdminView 
{
	function displayView($quiz, $quizActiveTab = 0, $tpl = null) 
	{
		$this->setToolbar();

		AriKernel::import('Joomla.Form.Form');

		$formsPath = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_ariquiz' . DS . 'models' . DS . 'forms' . DS . 'quiz' . DS;

		$form = new AriForm('common');
		$form->load(AriQuizHelper::getFormPath('quiz', 'quiz'));
		$form->bind($quiz, array('_default', 'security', 'results', 'rules'));
		$form->bind($quiz->ExtraParams, 'extra');
		$form->bind($quiz->Metadata, 'metadata');
        if ($quiz->QuizId > 0)
            $form->setParamAttribute('PrevQuizId', 'ignore_quiz', $quiz->QuizId, 'security');

        $this->assign('quizActiveTab', $quizActiveTab);
		$this->assign('quizId', $quiz->QuizId);
		$this->assignRef('form', $form);
		
		parent::display($tpl);
	}
	
	function setToolbar() 
	{
		$this->disableMainMenu();
		$id = JRequest::getInt('quizId');
		$edit = ($id > 0);

		$text = ($edit ? JText::_('COM_ARIQUIZ_LABEL_EDIT') : JText::_('COM_ARIQUIZ_LABEL_NEW'));

		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUIZ') . ': <small><small>[ ' . $text . ' ]</small></small>', 'categories.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();

		if ($edit) 
			JToolBarHelper::cancel('cancel', JText::_('Close'));
		else 
			JToolBarHelper::cancel();

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('CrateandEdit.html');
	}
}