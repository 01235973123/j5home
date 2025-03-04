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

AriKernel::import('Joomla.Compat.Application');

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

function AriQuizRichContentFixBaseUrlHandler()
{
	$app = AriApplication::getApplication();
	$body = $app->getBody();
	
	if (preg_match('/<base[^>]+>/i', $body))
		$app->setBody(preg_replace('/<base[^>]+>/i', '<base href="' . JURI::root() . '" />', $body));
	else
		$app->setBody(
			preg_replace('/(<\/head\s*>)/i', '<base href="' . JURI::root() . '" />' . '$1', $body, 1)
		);
}

class AriQuizViewRichcontent extends AriQuizAdminView 
{
	function display($tpl = null) 
	{
		$doc = JFactory::getDocument();

		$doc->addStyleDeclaration('HTML BODY {padding:0;margin:0;}');

		$app = AriApplication::getApplication();
		$app->registerEvent('onAfterRender', 'AriQuizRichContentFixBaseUrlHandler');		
		
		parent::display($tpl);
	}
}