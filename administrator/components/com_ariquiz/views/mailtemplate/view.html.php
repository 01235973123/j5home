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

class AriQuizViewMailtemplate extends AriQuizAdminView 
{
	function displayView($template, $tpl = null) 
	{
		$this->setToolbar();

		AriKernel::import('Joomla.Form.Form');

		$form = new AriForm('commonSettings');
		$form->load(AriQuizHelper::getFormPath('mailtemplate', 'mailtemplate'));
		$form->bind($template);
		$form->bind($template->TextTemplate, 'texttemplate');

		$this->assignRef('form', $form);

		parent::display($tpl);
	}
	
	function setToolbar() 
	{
		$this->disableMainMenu();
		$id = JRequest::getInt('templateId');
		$edit = ($id > 0);

		$text = ($edit ? JText::_('COM_ARIQUIZ_LABEL_EDIT') : JText::_('COM_ARIQUIZ_LABEL_NEW'));

		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_MAILTEMPLATE') . ': <small><small>[ ' . $text . ' ]</small></small>', 'inbox.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();

		if ($edit) 
			JToolBarHelper::cancel('cancel', JText::_('Close'));
		else 
			JToolBarHelper::cancel();

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('CreateandEdit8.html');
	}
}