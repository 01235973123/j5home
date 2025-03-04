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

class AriQuizControllerJsmessages extends AriController 
{
	function getMessages()
	{
		AriKernel::import('Joomla.Language.Language');

		$ajaxLang = new AriLanguage();
		$ajaxLang->load('com_ariquiz.ajax');
		$messages = $ajaxLang->getMessages();

		while (@ob_end_clean());
		header('Content-type: text/javascript; charset=UTF-8');
		
		echo ';YAHOO.ARISoft.page._locale["com_ariquiz"] = ' . json_encode($messages) . ';';
		
		exit();
	}
}