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
AriKernel::import('Joomla.Form.MassEditForm');

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

class AriQuizViewBankquestions extends AriQuizAdminView 
{
	function displayView($questionTypes, $filterData, $tpl = null) 
	{
		$this->setToolbar();
		
		$massEditForm = new AriMassEditForm('common');
		$massEditForm->load(AriQuizHelper::getFormPath('question', 'bankquestion_massedit'));
		
		$filterForm = new AriForm('common', 'AriGenericParameter');
		$filterForm->load(AriQuizHelper::getFormPath('bank', 'filter'));
		$filterForm->bind($filterData, array('_default'));
		
		$typeForm = new AriForm('common', 'AriGenericParameter');
		$typeForm->load(AriQuizHelper::getFormPath('question', 'question_questiontype'));
		$typeForm->bind(array('QuestionTypeId' => AriQuizHelper::getDefaultQuestionType($questionTypes)));

		$this->assignRef('filterForm', $filterForm);
		$this->assignRef('massEditform', $massEditForm);
		$this->assignRef('typeForm', $typeForm);
		$this->assignRef('dtQuestions', $this->_getQuestionsDataTable());

		parent::display($tpl);
	}
	
	function _getQuestionsDataTable()
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
					'key' => 'QuestionId', 
					'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatCheckbox', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuestionId2', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ID'), 
					'className' => 'dtCenter dtColMin',
					'sortable' => true
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'Question', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUESTION'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatBankQuestions'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'CategoryName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_CATEGORY'), 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatStripHtml'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuestionTypeClass', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUESTIONTYPE'),
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuestionType' 
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
				'dataUrl' => 'index.php?option=com_ariquiz&view=bankquestions&task=ajaxGetQuestionList'
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUESTIONSBANK'), 'article.png');
		
		$allowCreate = AriQuizHelper::isAuthorise('bankquestion.create');
		$allowEdit = AriQuizHelper::isAuthorise('bankquestion.edit');
		$allowDelete = AriQuizHelper::isAuthorise('bankquestion.delete');
		
		if ($allowCreate)
		{
			JToolBarHelper::custom('csv_import', 'upload', 'upload', JText::_('COM_ARIQUIZ_LABEL_CSVIMPORT'), false);
			JToolBarHelper::divider();
		}
		
		if (J3_0)
			JToolBarHelper::custom('csv_export', 'download', 'download', JText::_('COM_ARIQUIZ_LABEL_CSVEXPORT'));
		else
			JToolBarHelper::custom('csv_export', 'save', 'save', JText::_('COM_ARIQUIZ_LABEL_CSVEXPORT'));
		JToolBarHelper::divider();
		
		if ($allowEdit)
		{
			JToolBarHelper::custom('mass_edit', 'edit', 'edit', JText::_('COM_ARIQUIZ_LABEL_MASSEDIT'));
			JToolBarHelper::divider();
			JToolBarHelper::editList();
		}
			
		if ($allowCreate)
			JToolBarHelper::custom('addQuestion', 'new', 'new', JText::_('COM_ARIQUIZ_LABEL_NEWQUESTION'), false);

		if ($allowCreate || $allowEdit)
			JToolBarHelper::spacer();
		
		if ($allowDelete)
			JToolBarHelper::deleteList(
				JText::_('COM_ARIQUIZ_MESSAGE_DELETEBANKQUESTIONS', true), 
				'ajaxDelete', 
				JText::_('COM_ARIQUIZ_LABEL_DELETE')
			);
			
		if ($allowCreate || $allowEdit || $allowDelete)
			JToolBarHelper::divider();

		JToolBarHelper::custom('quizzes', 'backward-2', 'backward-2', JText::_('COM_ARIQUIZ_LABEL_QUIZZES'), false);

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('Questionbank.html');
	}
}