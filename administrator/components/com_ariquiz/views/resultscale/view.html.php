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

AriKernel::import('Web.Controls.Advanced.MultiplierControls');

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

class AriQuizViewResultscale extends AriQuizAdminView 
{
	function displayView($scale, $tpl = null) 
	{
		$this->setToolbar();

		AriKernel::import('Joomla.Form.Form');

		$scaleItemsData = $this->_getScaleItemsData($scale);
		
		$form = new AriForm('commonSettings');
		$form->load(AriQuizHelper::getFormPath('resultscale', 'resultscale'));
		$form->bind($scale);

		$this->assignRef('scale', $scale);
		$this->assignRef('form', $form);
		$this->assignRef('scaleItemsData', $scaleItemsData);

		parent::display($tpl);
		
		$this->addScript(JURI::root(true) . '/administrator/components/com_ariquiz/assets/js/ari.multiplierControls.js');
	}
	
	function _getScaleItemsData($scale)
	{
		$scaleItemsData = array();
		
		if (empty($scale) || empty($scale->ScaleItems))
			return $scaleItemsData;
			
		foreach ($scale->ScaleItems as $scaleItem)
		{
			$scaleItemsData[] = $scaleItem->toArray();
		}
			
		return $scaleItemsData;
	}
	
	function setToolbar() 
	{
		$this->disableMainMenu();
		$id = JRequest::getInt('scaleId');
		$edit = ($id > 0);

		$text = ($edit ? JText::_('COM_ARIQUIZ_LABEL_EDIT') : JText::_('COM_ARIQUIZ_LABEL_NEW'));

		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_RESULTSCALE') . ': <small><small>[ ' . $text . ' ]</small></small>', 'categories.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();

		if ($edit) 
			JToolBarHelper::cancel('cancel', JText::_('Close'));
		else 
			JToolBarHelper::cancel();

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('CreateandEdit6.html');
	}
}