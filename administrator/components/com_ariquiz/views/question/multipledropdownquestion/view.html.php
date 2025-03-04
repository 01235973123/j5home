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

class AriQuizSubViewQuestionMultipledropdownquestion extends AriQuizSubViewQuestion 
{
	function displayView($params, $tpl = null) 
	{
		JHtml::_('jquery.framework', true);
		$this->addScript(JURI::root(true) . '/administrator/components/com_ariquiz/assets/js/cloner.js');
//		$this->addScript(JURI::root(true) . '/administrator/components/com_ariquiz/assets/js/ari.multiplierControls.js');
	
		parent::displayView($params, $tpl);
	}
}