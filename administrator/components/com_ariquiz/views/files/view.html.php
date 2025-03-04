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

AriKernel::import('Joomla.Html.HtmlHelper');
AriKernel::import('Web.HtmlHelper');

if (J4) {
	Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal');
}

class AriQuizViewFiles extends AriQuizAdminView 
{
	function displayView($folders, $files, $params, $tpl = null) 
	{
		if (!J4) JHTML::_('behavior.modal', 'a.modal');

		$this->assign('folders', $folders);
		$this->assign('files', $files);
		$this->assign('params', $params);
		
		parent::display($tpl);
	}
}