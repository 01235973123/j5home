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

class AriQuizViewBankquestion extends AriQuizAdminView 
{
	function displayView($question, $questionViewParams, $questionView, $tpl = null) 
	{
		$this->setToolbar();

		AriKernel::import('Joomla.Form.Form');

		$form = new AriForm('commonSettings');
		$form->load(AriQuizHelper::getFormPath('question', 'bankquestion'));
		
		$specificQuestion = $questionViewParams['specificQuestion'];
		if ($specificQuestion->isScoreSpecific())
			$form->ignore('Score', 'questionversion');

		$form->bind($question);
		$form->bind($question->QuestionVersion, 'questionversion');

		$this->assign('quizId', $question->QuizId);
		$this->assignRef('question', $question);
		$this->assignRef('form', $form);
		$this->assignRef('questionViewParams', $questionViewParams);
		$this->assignRef('questionView', $questionView);

		parent::display($tpl);
	}

	function setToolbar() 
	{
		$this->disableMainMenu();
		$id = JRequest::getInt('questionId');
		$edit = ($id > 0);

		$text = ($edit ? JText::_('COM_ARIQUIZ_LABEL_EDIT') : JText::_('COM_ARIQUIZ_LABEL_NEW'));

		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUESTION') . ': <small><small>[ ' . $text . ' ]</small></small>', 'article.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();

		if ($edit) 
			JToolBarHelper::cancel('cancel', JText::_('Close'));
		else 
			JToolBarHelper::cancel();

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('CreateandEdit2.html');
	}
}