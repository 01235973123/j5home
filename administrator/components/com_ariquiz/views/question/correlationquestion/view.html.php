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

require_once dirname(__FILE__) . DS . '..' . DS . 'question.php';

AriKernel::import('Web.Controls.Advanced.MultiplierControls');

class AriQuizSubViewQuestionCorrelationquestion extends AriQuizSubViewQuestion 
{
	function displayView($params, $tpl = null) 
	{
		$this->addScript(JURI::root(true) . '/administrator/components/com_ariquiz/assets/js/ari.multiplierControls.js');
	
		parent::displayView($params, $tpl);
	}
}