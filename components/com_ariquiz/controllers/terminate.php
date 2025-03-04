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

class AriQuizControllerTerminate extends AriController 
{
	function tryAgain()
	{
		$itemId = $this->input->getInt('Itemid');
		$quizId = $this->input->getInt('quizId');

		$this->redirect(
			JRoute::_('index.php?option=com_ariquiz&view=quiz&quizId=' . $quizId . ($itemId > 0 ? '&Itemid=' . $itemId : ''), false)
		);
	}
	
	function gotopage()
	{
		$returnUrl = $this->input->getString('rurl');
		
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