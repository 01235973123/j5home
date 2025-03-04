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

class AriQuizViewQuizresult extends AriQuizAdminView 
{
	function displayView($sid, $tpl = null) 
	{
		$this->setToolbar();
		
		$this->assign('sid', $sid);
		$this->assignRef('dtQuestions', $this->_getQuestionsDataTable($sid));

		parent::display($tpl);
	}
	
	function _getQuestionsDataTable($sid)
	{
		AriKernel::import('Web.Controls.Data.MultiPageDataTable');

		$columns = array(
			new AriDataTableControlColumn(
				array(
					'key' => 'QuestionData', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUIZRESULTS'), 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuestionStatData' 
				)
			)
		);

		$dataTable = new AriMultiPageDataTableControl(
			'dtQuestions',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=quizresult&task=ajaxGetResult&statisticsInfoId=' . $sid,
				'disableHighlighting' => true
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}
	
	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUIZRESULT'), 'browser.png');
		
		JToolBarHelper::custom('csvExport', 'archive', 'archive', JText::_('COM_ARIQUIZ_LABEL_EXPORTTOCSV'), false);
		
		JToolBarHelper::spacer();
		
		JToolBarHelper::custom('htmlExport', 'archive', 'archive', JText::_('COM_ARIQUIZ_LABEL_EXPORTTOHTML'), false);
		
		JToolBarHelper::spacer();
		
		JToolBarHelper::custom('excelExport', 'archive', 'archive', JText::_('COM_ARIQUIZ_LABEL_EXPORTTOEXCEL'), false);
		
		JToolBarHelper::spacer();
		
		JToolBarHelper::custom('wordExport', 'archive', 'archive', JText::_('COM_ARIQUIZ_LABEL_EXPORTTOWORD'), false);
		
		JToolBarHelper::divider();
		
		JToolBarHelper::custom('quizresults', 'backward-2', 'backward-2', JText::_('COM_ARIQUIZ_LABEL_QUIZRESULTS'), false);
	}
}