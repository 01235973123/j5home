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

class AriQuizViewEditor extends AriQuizAdminView 
{
	function display($tpl = null) 
	{
		$this->setToolbar();
		
		$config = AriQuizHelper::getConfig();
		$editorType = $config->get('Editor');
		$editor = null;

		if (J4) {
			$editor = Joomla\CMS\Editor\Editor::getInstance($editorType ? $editorType : null);
		} else {
			$editor = JFactory::getEditor($editorType ? $editorType : null);
		}
		$this->assign('editor', $editor);

		parent::display($tpl);
	}

	function setToolbar() 
	{
		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_ABOUT'), 'systeminfo.png');
	}
}