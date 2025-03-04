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

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

class AriQuizViewQuestioncategories extends AriQuizAdminView 
{
	function displayView($quiz, $tpl = null) 
	{
		$this->setToolbar($quiz);

		$massEditForm = new AriMassEditForm('common');
		$massEditForm->load(AriQuizHelper::getFormPath('questioncategory', 'category'));

		$quizId = JRequest::getInt('quizId');
		
		$this->assign('quizId', $quizId);
		$this->assignRef('massEditform', $massEditForm);
		$this->assignRef('dtCategories', $this->_getCategoriesDataTable($quizId));

		parent::display($tpl);
	}
	
	function _getCategoriesDataTable($quizId)
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
					'key' => 'QuestionCategoryId', 
					'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatCheckbox', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuestionCategoryId', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ID'), 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'CategoryName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_NAME'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuestionCategory'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuizName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_NAME'), 
					'sortable' => ($quizId < 1), 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuiz'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuizId', 
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
			'dtCategories',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=questioncategories&task=ajaxGetCategoryList' . ($quizId > 0 ? '&quizId=' . $quizId : '')
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}

	function setToolbar($quiz) 
	{
		$quizId = !is_null($quiz) ? $quiz->QuizId : 0;
		
		JToolBarHelper::title(
			sprintf('%1$s%2$s',
				JText::_('COM_ARIQUIZ_LABEL_QUESTIONCATEGORIES'),
				!is_null($quiz) 
					? ': <small><small>[ ' . strip_tags($quiz->QuizName) . ' ]</small></small>'
					: ''
			),
			'categories.png'
		);

		$allowEdit = (
			($quizId && AriQuizHelper::isAuthorise('questioncategory.edit', 'com_ariquiz.quiz.' . $quizId)) 
			||
			($quizId == 0 && AriQuizHelper::isAuthorise('questioncategory.edit'))
		);
		$allowCreate = (
			($quizId && AriQuizHelper::isAuthorise('questioncategory.create', 'com_ariquiz.quiz.' . $quizId)) 
			||
			($quizId == 0 && AriQuizHelper::isAuthorise('questioncategory.create'))
		);
		$allowDelete = (
			($quizId && AriQuizHelper::isAuthorise('questioncategory.delete', 'com_ariquiz.quiz.' . $quizId)) 
			||
			($quizId == 0 && AriQuizHelper::isAuthorise('questioncategory.delete'))
		);

		if ($allowEdit)
		{
			JToolBarHelper::custom('mass_edit', 'edit', 'edit', JText::_('COM_ARIQUIZ_LABEL_MASSEDIT'));
			JToolBarHelper::divider();
		}

		if ($allowEdit)
			JToolBarHelper::editList();
			
		if ($allowCreate)
			JToolBarHelper::addNew();

		if ($allowCreate || $allowEdit)
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
		AriQuizToolbarHelper::ariQuizHelp('Questioncategories.html');
	}
}