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

class AriQuizViewQuizresults extends AriQuizAdminView 
{
	function displayView($filterData, $tpl = null) 
	{
		$this->setToolbar();

		$form = new AriForm('common', 'AriGenericParameter');
		$form->load(AriQuizHelper::getFormPath('quizresults', 'filter'));
		$form->bind($filterData, array('_default'));
		
		$this->assignRef('form', $form);
		$this->assignRef('dtResults', $this->_getResultsDataTable());

		parent::display($tpl);
	}
	
	function _getResultsDataTable()
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
					'key' => 'StatisticsInfoId', 
					'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatCheckbox', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuizName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUIZ'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuiz'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'Name', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_NAME'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatUser'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'Login', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_LOGIN'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatUser'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'Email', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_EMAIL'), 
					'sortable' => true
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'StartDate2', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_STARTDATE'), 
					'sortable' => true
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'EndDate2', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ENDDATE'), 
					'sortable' => true
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'PercentScore', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_SCORE'), 
					'sortable' => true,
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatResultScore',
					'className' => 'dtCenter dtNoWrap'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'Passed', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_STATUS'), 
					'sortable' => true,
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatPassed',
					'className' => 'dtCenter'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => '', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_DETAILS'), 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatResultDetails',
					'className' => 'dtCenter dtNoWrap'
				)
			),
			
			new AriDataTableControlColumn(
				array(
					'key' => 'UserScore',
					'hidden' => true 
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'MaxScore',
					'hidden' => true 
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'QuizId',
					'hidden' => true 
				)
			)			
		);

		$dataTable = new AriMultiPageDataTableControl(
			'dtResults',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=quizresults&task=ajaxGetResultList'
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUIZZESRESULTS'), 'browser.png');

		JToolBarHelper::custom('csvExport', 'archive', 'archive', JText::_('COM_ARIQUIZ_LABEL_EXPORTTOCSV'));
		
		JToolBarHelper::spacer();
		
		JToolBarHelper::custom('htmlExport', 'archive', 'archive', JText::_('COM_ARIQUIZ_LABEL_EXPORTTOHTML'));
		
		JToolBarHelper::spacer();
		
		JToolBarHelper::custom('excelExport', 'archive', 'archive', JText::_('COM_ARIQUIZ_LABEL_EXPORTTOEXCEL'));
		
		JToolBarHelper::spacer();
		
		JToolBarHelper::custom('wordExport', 'archive', 'archive', JText::_('COM_ARIQUIZ_LABEL_EXPORTTOWORD'));
		
		if (AriQuizHelper::isAuthorise('results.delete'))
		{
			JToolBarHelper::divider();
	
			JToolBarHelper::deleteList(
				JText::_('COM_ARIQUIZ_MESSAGE_DELETE', true), 
				'ajaxDelete', 
				JText::_('COM_ARIQUIZ_LABEL_DELETE')
			);
			
			JToolBarHelper::spacer();
			
			JToolBarHelper::custom('deleteAll', 'delete', 'delete', JText::_('COM_ARIQUIZ_LABEL_DELETEALL'), false);
		}

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('Quizresults.html');
	}
}