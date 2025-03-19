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

AriKernel::import('Joomla.Menu.MenuHelper');

class AriQuizViewQuizresults extends AriQuizView 
{
	function display($tpl = null) 
	{
		$this->assignRef('dtResults', $this->_getResultsDataTable());
		
		$this->_prepareDocument();
				
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
					'key' => 'QuizName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUIZ'), 
					'sortable' => true 
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'Name', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUIZ'), 
					'sortable' => false 
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
					'key' => 'ResultsLink', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_DETAILS'), 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatUserResultDetails',
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
					'key' => 'TicketId',
					'hidden' => true 
				)
			)
		);
		
		$itemId = AriMenuHelper::getActiveItemId();
		$dataTable = new AriMultiPageDataTableControl(
			'dtQuizzesResults',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=quizresults&task=ajaxGetResultList' . ($itemId ? '&Itemid=' . $itemId : '')
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}
	
	function _prepareDocument()
	{
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$title = $params->get('page_title', ''); 

		if (!empty($title))
		{
			$title = AriQuizHelper::formatPageTitle($title);
			$document->setTitle($title);
		}
		
		$metaDescription = $params->get('menu-meta_description');	
		if (!empty($metaDescription))
			$document->setDescription($metaDescription);

		$metaKeywords = $params->get('menu-meta_keywords');
		if (!empty($metaKeywords))
			$document->setMetadata('keywords', $metaKeywords);
	}
}