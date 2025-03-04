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

class AriQuizViewMailtemplates extends AriQuizAdminView 
{
	function display($tpl = null) 
	{
		$this->setToolbar();
		
		$this->assignRef('dtTemplates', $this->_getTemplatesDataTable());

		parent::display($tpl);
	}
	
	function _getTemplatesDataTable()
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
					'key' => 'MailTemplateId', 
					'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 
					'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatCheckbox', 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'MailTemplateId', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_ID'), 
					'className' => 'dtCenter dtColMin'
				)
			),
			new AriDataTableControlColumn(
				array(
					'key' => 'TemplateName', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_NAME'), 
					'sortable' => true, 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatMailTemplate'
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
			'dtTemplates',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=mailtemplates&task=ajaxGetTemplateList'
			),
			AriQuizHelper::getPaginatorOptions()
		);

		return $dataTable;
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_MAILTEMPLATES'), 'inbox.png');
		
		$allowEdit = AriQuizHelper::isAuthorise('mailtemplate.edit');
		$allowCreate = AriQuizHelper::isAuthorise('mailtemplate.create');
		$allowDelete = AriQuizHelper::isAuthorise('mailtemplate.delete');
		
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

		AriQuizToolbarHelper::ariQuizHelp('Mailtemplates.html');
	}
}