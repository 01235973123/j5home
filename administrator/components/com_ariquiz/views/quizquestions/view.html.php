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

AriKernel::import('Joomla.Form.MassEditForm');
AriKernel::import('Web.Controls.Data.MultiPageDataTable');

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

class AriQuizViewQuizquestions extends AriQuizAdminView 
{
	function displayView($questionTypes, $quiz, $filterData, $tpl = null) 
	{
		$this->setToolbar($quiz);
		
		$quizId = $quiz->QuizId;
		
		$massEditForm = new AriMassEditForm('common');
		$massEditForm->load(AriQuizHelper::getFormPath('question', 'quizquestion_massedit'));
		$massEditForm->bind(array('QuizId' => $quizId));
		
		$copyToBankForm = new AriForm('common', 'AriGenericParameter');
		$copyToBankForm->load(AriQuizHelper::getFormPath('question', 'copytobank'));
		
		$copyForm = new AriForm('common', 'AriGenericParameter');
		$copyForm->load(AriQuizHelper::getFormPath('question', 'question_copymove'));
		
		$moveForm = new AriForm('common', 'AriGenericParameter');
		$moveForm->load(AriQuizHelper::getFormPath('question', 'question_copymove'));
		
		$templateForm = new AriForm('common', 'AriGenericParameter');
		$templateForm->load(AriQuizHelper::getFormPath('question', 'question_questiontemplate'));
		
		$typeForm = new AriForm('common', 'AriGenericParameter');
		$typeForm->load(AriQuizHelper::getFormPath('question', 'question_questiontype'));
		$typeForm->bind(array('QuestionTypeId' => AriQuizHelper::getDefaultQuestionType($questionTypes)));
		
		$fromBankSettingsForm = new AriForm('common', 'AriGenericParameter');
		$fromBankSettingsForm->load(AriQuizHelper::getFormPath('question', 'frombank_settings'));
		$fromBankSettingsForm->bind(array('QuizId' => $quizId));
		
		$fromBankFilterForm = new AriForm('common', 'AriGenericParameter');
		$fromBankFilterForm->load(AriQuizHelper::getFormPath('question', 'frombank_filter'));
		
		$filterForm = new AriForm('common', 'AriGenericParameter');
		$filterForm->load(AriQuizHelper::getFormPath('question', 'filter'));
		$filterForm->bind($filterData, array('_default'));

		$this->assign('quizId', $quizId);
		$this->assignRef('massEditform', $massEditForm);
		$this->assignRef('copyToBankForm', $copyToBankForm);
		$this->assignRef('copyForm', $copyForm);
		$this->assignRef('moveForm', $moveForm);
		$this->assignRef('templateForm', $templateForm);
		$this->assignRef('typeForm', $typeForm);
		$this->assignRef('fromBankSettingsForm', $fromBankSettingsForm);
		$this->assignRef('fromBankFilterForm', $fromBankFilterForm);
		$this->assignRef('filterForm', $filterForm);
		$this->assignRef('dtQuestions', $this->_getQuestionsDataTable($quizId));
		$this->assignRef('dtBank', $this->_getBankDataTable($quizId));

		parent::display($tpl);
	}
	
	function _getQuestionsDataTable($quizId)
	{
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
					'key' => 'QuestionId', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ID'), 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'BankQuestionId', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_BANKID'), 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'Question', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUESTION'), 
					'sortable' => false, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuestions'
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
					'key' => 'Status', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_STATUS'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuestionStatus', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => '', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ORDER'), 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuestionsReorder', 
					'className' => 'dtCenter dtColMin dtNoWrap'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuizId', 
					'label' => '', 
					'hidden' => true
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuestionIndex2', 
					'label' => '', 
					'hidden' => true
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
				'dataUrl' => 'index.php?option=com_ariquiz&view=quizquestions&task=ajaxGetQuestionList&quizId=' . $quizId
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}
	
	function _getBankDataTable($quizId)
	{
		$columns = array(
			new AriDataTableControlColumn(
				array(
					'key' => 'Num', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_NUMPOS'), 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatPosition', 
					'className' => 'dtCenter dtColMin', 
					'width' => 35
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'BankQuestionId', 
					'field' => 'QuestionId',
					'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatCheckbox', 
					'className' => 'dtCenter dtColMin', 
					'width' => 40
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuestionId', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ID'), 
					'className' => 'dtCenter dtColMin',
					'width' => 50,
					'sortable' => true
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'Question', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUESTION'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatBankQuestions', 
					'width' => 380
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuestionTypeClass', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUESTIONTYPE'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuestionType',
					'className' => 'dtCenter', 
					'width' => 180
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'CategoryName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_CATEGORY'), 
					'sortable' => true, 
					'width' => 220
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
			'dtBank',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=quizquestions&task=ajaxGetBankList&quizId=' . $quizId,
				'width' => '930px',
				'height' => '355px'
			),
			AriQuizHelper::getPaginatorOptions(),
			true,
			false
		);

		return $dataTable;
	}

	function setToolbar($quiz) 
	{
		$quizId = $quiz->QuizId;
		$quizAssetName = 'com_ariquiz.quiz.' . $quizId;
		
		$allowCreate = AriQuizHelper::isAuthorise('question.create', $quizAssetName);
		$allowEdit = AriQuizHelper::isAuthorise('question.edit', $quizAssetName);
		$allowDelete = AriQuizHelper::isAuthorise('question.delete', $quizAssetName);
		$allowBankCreate = AriQuizHelper::isAuthorise('bankquestion.create'); 

		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUESTIONS') . ': <small><small>[ ' . strip_tags($quiz->QuizName) . ' ]</small></small>', 'article.png');
		
		if ($allowCreate)
		{
			JToolBarHelper::custom('copy', 'copy', 'copy', JText::_('COM_ARIQUIZ_LABEL_COPY'));
			
			if ($allowEdit)
				JToolBarHelper::spacer();
		}

		if ($allowEdit)
			JToolBarHelper::custom('move', 'move', 'move', JText::_('COM_ARIQUIZ_LABEL_MOVE'));
		
		if ($allowCreate || $allowEdit)
			JToolBarHelper::divider();
		
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
		}
		
		if ($allowBankCreate)
		{
			JToolBarHelper::custom('to_bank', 'upload', 'upload', JText::_('COM_ARIQUIZ_LABEL_TOBANK'));
			
			if ($allowCreate)
				JToolBarHelper::spacer();
		}
		
		if ($allowCreate)
			JToolBarHelper::custom('from_bank', 'upload', 'upload', JText::_('COM_ARIQUIZ_LABEL_FROMBANK'), false);
		
		if ($allowBankCreate || $allowCreate)
			JToolBarHelper::divider();
		
		if ($allowEdit)
			JToolBarHelper::editList();
			
		if ($allowCreate)
			JToolBarHelper::custom('addQuestion', 'new', 'new', JText::_('COM_ARIQUIZ_LABEL_NEWQUESTION'), false);
			
		if ($allowEdit)
		{
			JToolBarHelper::divider();

			JToolBarHelper::publishList('ajaxActivate', JText::_('COM_ARIQUIZ_LABEL_ACTIVATE'));
			JToolBarHelper::unpublishList('ajaxDeactivate', JText::_('COM_ARIQUIZ_LABEL_DEACTIVATE'));
		}

		if ($allowEdit || $allowCreate)
			JToolBarHelper::spacer();
		
		if ($allowDelete)
			JToolBarHelper::deleteList(
				JText::_('COM_ARIQUIZ_MESSAGE_DELETE', true), 
				'ajaxDelete', 
				JText::_('COM_ARIQUIZ_LABEL_DELETE')
			);

		if ($allowCreate || $allowEdit || $allowDelete)
			JToolBarHelper::divider();
		
		JToolBarHelper::custom('quizzes', 'backward-2', 'backward-2', JText::_('COM_ARIQUIZ_LABEL_QUIZZES'), false);

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('Questions.html');
	}
}