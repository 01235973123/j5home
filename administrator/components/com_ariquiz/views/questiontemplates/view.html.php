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

AriKernel::import('Joomla.Form.Form');
AriKernel::import('Joomla.Html.GenericParameter');

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

class AriQuizViewQuestiontemplates extends AriQuizAdminView 
{
	function displayView($questionTypes, $tpl = null) 
	{
		$this->setToolbar();
		
		$typeForm = new AriForm('common', 'AriGenericParameter');
		$typeForm->load(AriQuizHelper::getFormPath('question', 'question_questiontype'));
		$typeForm->bind(array('QuestionTypeId' => AriQuizHelper::getDefaultQuestionType($questionTypes)));

		$this->assignRef('typeForm', $typeForm);
		$this->assignRef('dtTemplates', $this->_getTemplatesDataTable());

		parent::display($tpl);
	}
	
	function _getTemplatesDataTable()
	{
		AriKernel::import('Web.Controls.Data.MultiPageDataTable');

		$columns = array(
			new AriDataTableControlColumn(
				array(
					'key' => '', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_NUMPOS'), 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatPosition', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'TemplateId', 
					'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatCheckbox', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'TemplateId', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ID'), 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'TemplateName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_NAME'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQTemplate'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuestionType', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUESTIONTYPE'), 
					'sortable' => true, 
					'className' => 'dtCenter dtColTiny'
				)
			),
			
			new AriDataTableControlColumn(
				array(
					'key' => 'AllowEdit',
					'hidden' => true 
				)
			),
		);

		$dataTable = new AriMultiPageDataTableControl(
			'dtQuestions',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=questiontemplates&task=ajaxGetTemplateList'
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUESTIONTEMPLATES'), 'categories.png');
		
		$allowEdit = AriQuizHelper::isAuthorise('questiontemplate.edit');
		$allowCreate = AriQuizHelper::isAuthorise('questiontemplate.create');
		$allowDelete = AriQuizHelper::isAuthorise('questiontemplate.delete');
		
		if ($allowEdit)
			JToolBarHelper::editList();
			
		if ($allowCreate)
			JToolBarHelper::custom('addTemplate', 'new', 'new', JText::_('COM_ARIQUIZ_LABEL_NEW'), false);

		if ($allowEdit || $allowCreate)
			JToolBarHelper::spacer();
		
		if ($allowDelete)
			JToolBarHelper::deleteList(
				JText::_('COM_ARIQUIZ_MESSAGE_DELETE', true), 
				'ajaxDelete', 
				JText::_('COM_ARIQUIZ_LABEL_DELETE')
			);

		if ($allowEdit || $allowCreate || $allowDelete)
			JToolBarHelper::divider();

		JToolBarHelper::custom('quizzes', 'backward-2', 'backward-2', JText::_('COM_ARIQUIZ_LABEL_QUIZZES'), false);

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('Questiontemplates.html');
	}
}