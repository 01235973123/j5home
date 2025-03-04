<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Joomla.Views.ViewBase');
AriKernel::import('Joomla.Compat.Application');

class AriView extends AriViewBase
{
	function addScript($scriptPath)
	{
		$doc =& JFactory::getDocument();
		$doc->addScript($scriptPath);
	}

	function disableMainMenu()
	{
		if (class_exists('Joomla\CMS\Factory')) {
			AriApplication::getInput()->set('hidemainmenu', true);
		} else {
			JRequest::setVar('hidemainmenu', true);
		}
	}
}