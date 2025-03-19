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

AriKernel::import('Joomla.Controllers.Controller');

class AriQuizControllerMessage extends AriController 
{
	function gotopage()
	{
		$returnUrl = JRequest::getString('rurl');
		
		if ($returnUrl)
			$returnUrl = urldecode($returnUrl);
		else 
		{
			$config = AriQuizHelper::getConfig();
			$returnUrl = $config->get('ReturnUrl');
		}
		
		$this->redirect($returnUrl);
	}
}