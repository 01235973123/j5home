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

class AriQuizViewQuizzes extends AriQuizAdminView 
{
	function displayView($filterData, $tpl = null) 
	{
		$this->setToolbar();
		
		$massEditForm = new AriMassEditForm('common');
		$massEditForm->load(AriQuizHelper::getFormPath('quiz', 'quiz'));
		
		$filterForm = new AriForm('common', 'AriGenericParameter');
		$filterForm->load(AriQuizHelper::getFormPath('quiz', 'filter'));
		$filterForm->bind($filterData, array('_default'));
		
		$copyForm = new AriForm('common', 'AriGenericParameter');
		$copyForm->load(AriQuizHelper::getFormPath('quiz', 'copy'));

		$this->assignRef('filterForm', $filterForm);
		$this->assignRef('copyForm', $copyForm);
		$this->assignRef('massEditform', $massEditForm);
		$this->assignRef('dtQuizzes', $this->_getQuizzesDataTable());

		parent::display($tpl);
	}
	
	function _getQuizzesDataTable()
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
					'key' => 'QuizId', 
					'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatCheckbox', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuizId', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ID'), 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuizName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_NAME'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuiz'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'CategoryName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_CATEGORY'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatStripHtml'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'Status', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_STATUS'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuizStatus', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuizId', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ACTIONS'), 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuizActions', 
					'className' => 'dtCenter cellQuizRelatedLinks'
				)
			),
			
			new AriDataTableControlColumn(
				array(
					'key' => 'AllowEdit',
					'hidden' => true 
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'AllowEditState',
					'hidden' => true 
				)
			),
		);

		$dataTable = new AriMultiPageDataTableControl(
			'dtQuizzes',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=quizzes&task=ajaxGetQuizList'
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUIZZES'), 'categories.png');

		$allowCreate = AriQuizHelper::isAuthorise('quiz.create');
		$allowEdit = AriQuizHelper::isAuthorise('quiz.edit');
		
		if ($allowCreate)
			JToolBarHelper::custom('copy', 'copy', 'copy', JText::_('COM_ARIQUIZ_LABEL_COPY'));

		if ($allowEdit)
		{
			if ($allowCreate)
				JToolBarHelper::divider();

			JToolBarHelper::custom('mass_edit', 'edit', 'edit', JText::_('COM_ARIQUIZ_LABEL_MASSEDIT'));
		}

		if ($allowCreate || $allowEdit)
			JToolBarHelper::divider();

		JToolBarHelper::custom('questions', 'edit', 'edit', JText::_('COM_ARIQUIZ_LABEL_QUESTIONS'));

		if (AriQuizHelper::isAuthorise('quiz.edit.state'))
		{
			JToolBarHelper::divider();
			
			JToolBarHelper::publishList('ajaxActivate', JText::_('COM_ARIQUIZ_LABEL_ACTIVATE'));
			JToolBarHelper::unpublishList('ajaxDeactivate', JText::_('COM_ARIQUIZ_LABEL_DEACTIVATE'));
		}

		if (AriQuizHelper::isAuthorise('quiz.edit') || AriQuizHelper::isAuthorise('quiz.create'))
		{
			JToolBarHelper::divider();

			if (AriQuizHelper::isAuthorise('quiz.edit'))
				JToolBarHelper::editList();

			if (AriQuizHelper::isAuthorise('quiz.create'))
				JToolBarHelper::addNew();
		}

		if (AriQuizHelper::isAuthorise('quiz.delete'))
		{
			JToolBarHelper::divider();
			
			JToolBarHelper::deleteList(
				JText::_('COM_ARIQUIZ_MESSAGE_DELETE', true), 
				'ajaxDelete', 
				JText::_('COM_ARIQUIZ_LABEL_DELETE')
			);
		}

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('Quizzes.html');	
	}
}