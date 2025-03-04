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

class AriQuizViewCategories extends AriQuizAdminView 
{
	function display($tpl = null) 
	{
		$this->setToolbar();

		$this->assignRef('dtCategories', $this->_getQuizzesDataTable());

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
					'key' => 'CategoryId', 
					'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatCheckbox', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'CategoryId', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ID'), 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'CategoryName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_NAME'), 
					'sortable' => false, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatCategory'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'CategoryName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ORDER'), 
					'sortable' => false, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatCategoryOrdering',
					'className' => 'dtCenter dtColMin'
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
					'key' => 'NodeType',
					'hidden' => true 
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'level',
					'hidden' => true 
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'lft',
					'hidden' => true 
				)
			),
		);

		$dataTable = new AriMultiPageDataTableControl(
			'dtCategories',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=categories&task=ajaxGetCategoryList'
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_CATEGORIES'), 'categories.png');
		
		$allowCreate = AriQuizHelper::isAuthorise('category.create');
		$allowEdit = AriQuizHelper::isAuthorise('category.edit');
		$allowDelete = AriQuizHelper::isAuthorise('category.delete');
		
		if ($allowEdit)
			JToolBarHelper::editList();

		if ($allowCreate)
			JToolBarHelper::addNew();

		if ($allowDelete)
		{
			if ($allowCreate || $allowEdit)
				JToolBarHelper::spacer();

			JToolBarHelper::deleteList(
				JText::_('COM_ARIQUIZ_MESSAGE_DELETE', true), 
				'ajaxDelete', 
				JText::_('COM_ARIQUIZ_LABEL_DELETE')
			);
		}
		
		if ($allowEdit)
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom('ajaxRebuild', 'refresh', 'refresh', JText::_('COM_ARIQUIZ_LABEL_REBUILD'), false);
		}

		if ($allowCreate || $allowEdit || $allowDelete)
			JToolBarHelper::divider();

		JToolBarHelper::custom('quizzes', 'backward-2', 'backward-2', JText::_('COM_ARIQUIZ_LABEL_QUIZZES'), false);

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('Quizcategories.html');
	}
}