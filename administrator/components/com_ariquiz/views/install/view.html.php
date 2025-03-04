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

require_once dirname(__FILE__) . '/../view.php';

class AriQuizViewInstall extends AriQuizAdminView
{
	function displayView($addons, $tpl = null) 
	{
		$this->setToolbar();

		$this->assign('addons', $addons);

		parent::display($tpl);
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_INSTALLATION'), 'module.png');

		JToolBarHelper::custom('install', 'arrow-right-4', 'arrow-right-4', JText::_('COM_ARIQUIZ_LABEL_COMPLETE'), false);
	}
}
