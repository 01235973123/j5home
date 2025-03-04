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

AriKernel::import('Application.ARIQuiz.ThemeLoader');

class AriQuizThemeLoader_Default extends AriQuizThemeLoader 
{
	function load()
	{
		parent::load();
		
		$doc = JFactory::getDocument();
		
		$theme = $this->getName();
		$themeUri = JURI::root(true) . '/components/com_ariquiz/themes/' . $theme . '/';
		
		$doc->addStyleSheet($themeUri . 'css/bootstrap.styles.css?v=' . ARIQUIZ_VERSION);
	}	
}