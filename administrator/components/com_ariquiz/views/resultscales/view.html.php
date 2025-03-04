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

class AriQuizViewResultscales extends AriQuizAdminView 
{
	function display($tpl = null) 
	{
		$this->setToolbar();
		
		$this->assignRef('dtScales', $this->_getScalesDataTable());

		parent::display($tpl);
	}
	
	function _getScalesDataTable()
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
					'key' => 'ScaleId', 
					'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatCheckbox', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'ScaleName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_NAME'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatScale'
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
			'dtScales',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=resultscales&task=ajaxGetScaleList'
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_RESULTSCALES'), 'categories.png');
		
		$allowEdit = AriQuizHelper::isAuthorise('resultscale.edit');
		$allowCreate = AriQuizHelper::isAuthorise('resultscale.create');
		$allowDelete = AriQuizHelper::isAuthorise('resultscale.delete');
		
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
		
		if ($allowCreate || $allowDelete || $allowEdit)
			JToolBarHelper::divider();
		
		AriQuizToolbarHelper::ariQuizHelp('Resultscales.html');
	}
}