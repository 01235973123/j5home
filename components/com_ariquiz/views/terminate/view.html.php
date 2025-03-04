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

class AriQuizViewTerminate extends AriQuizView 
{
	var $_isFormView = true;

	function display($tpl = null) 
	{
		$quizId = JRequest::getString('quizId');
		$returnUrl = JRequest::getString('rurl');
		if ($returnUrl)
			$returnUrl = urldecode($returnUrl);

		$this->assign('returnUrl', $returnUrl);
		$this->assign('quizId', $quizId);

		parent::display($tpl);
	}
}