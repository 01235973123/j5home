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

class AriQuizViewQuestiontemplate extends AriQuizAdminView 
{
	function displayView($template, $questionViewParams, $questionView, $tpl = null) 
	{
		$this->setToolbar();

		AriKernel::import('Joomla.Form.Form');

		$commonSettingsForm = new AriForm('commonSettings');
		$commonSettingsForm->load(AriQuizHelper::getFormPath('question', 'questiontemplate'));
		$commonSettingsForm->bind($template);

		$this->assignRef('commonSettingsForm', $commonSettingsForm);
		$this->assignRef('questionViewParams', $questionViewParams);
		$this->assignRef('questionView', $questionView);

		parent::display($tpl);
	}
	
	function setToolbar() 
	{
		$this->disableMainMenu();
		$id = JRequest::getInt('templateId');
		$edit = ($id > 0);

		$text = ($edit ? JText::_('COM_ARIQUIZ_LABEL_EDIT') : JText::_('COM_ARIQUIZ_LABEL_NEW'));

		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUESTIONTEMPLATE') . ': <small><small>[ ' . $text . ' ]</small></small>', 'categories.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();

		if ($edit) 
			JToolBarHelper::cancel('cancel', JText::_('Close'));
		else 
			JToolBarHelper::cancel();

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('CreateandEdit5.html');
	}
}