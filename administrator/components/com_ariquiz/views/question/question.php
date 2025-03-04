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

require_once dirname(__FILE__) . DS . '..' . DS . 'subview.php';

AriKernel::import('Utils.Utils');

class AriQuizSubViewQuestion extends AriQuizAdminSubView 
{
	function displayView($params, $tpl = null) 
	{
		$this->assignRef('specificQuestion', AriUtils::getParam($params, 'specificQuestion'));
		$this->assign('questionData', AriUtils::getParam($params, 'questionData'));
		$this->assign('questionOverridenData', AriUtils::getParam($params, 'questionOverridenData'));
		$this->assign('basedOnBank', (bool)AriUtils::getParam($params, 'basedOnBank', false));
		$this->assign('files', AriUtils::getParam($params, 'files', array()));

		parent::display($tpl);
	}
}