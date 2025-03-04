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

class AriQuizViewBankcategories extends AriQuizAdminView 
{
	function display($tpl = null) 
	{
		$this->setToolbar();

		$this->assignRef('dtCategories', $this->_getCategoriesDataTable());

		parent::display($tpl);
	}
	
	function _getCategoriesDataTable()
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
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatBankCategory'
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
				'dataUrl' => 'index.php?option=com_ariquiz&view=bankcategories&task=ajaxGetCategoryList'
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_BANKCATEGORIES'), 'categories.png');
		
		$allowEdit = AriQuizHelper::isAuthorise('bankcategory.edit');
		$allowCreate = AriQuizHelper::isAuthorise('bankcategory.create');
		$allowDelete = AriQuizHelper::isAuthorise('bankcategory.delete');
		
		if ($allowEdit)
			JToolBarHelper::editList();

		if ($allowCreate)
			JToolBarHelper::addNew();

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
		AriQuizToolbarHelper::ariQuizHelp('Bankcategories.html');
	}
}