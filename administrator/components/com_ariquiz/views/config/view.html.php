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

class AriQuizViewConfig extends AriQuizAdminView 
{
	function displayView($config, $activeTab, $tpl = null)
	{
		$this->setToolbar();

		AriKernel::import('Joomla.Form.Form');

		$form = new AriForm('common');
		$form->load(AriQuizHelper::getFormPath('config', 'config'));
		$form->bind($config->getConfig(), $config->getGroups());

		$this->assign('activeTab', $activeTab);
		$this->assignRef('form', $form);

		parent::display($tpl);
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_CONFIG'), 'config.png');
		
		if (AriQuizHelper::isAuthorise('core.admin'))
		{
			JToolBarHelper::save();
		
			if (!J1_5 && AriQuizHelper::isACLEnabled())
			{
				JToolBarHelper::divider();
				JToolBarHelper::preferences('com_ariquiz');
			}
			
			JToolBarHelper::divider();
		}

		AriQuizToolbarHelper::ariQuizHelp('ARIQuizComponentReference1.html');
	}
}