<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/kernel/class.AriKernel.php';

require_once dirname(__FILE__) . '/defines.php';
require_once dirname(__FILE__) . '/helper.php';

AriKernel::import('Joomla.Compat.Request');

if (!defined('ARIQUIZ_GRID_PAGESIZE')) {
    $pageSize = intval(AriQuizHelper::getConfig()->get('PageSize', 10), 10);
    define('ARIQUIZ_GRID_PAGESIZE', max(1, $pageSize));
}

// Access check
if (!AriQuizHelper::isAuthorise('core.manage'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
	$app = JFactory::getApplication();
	$app->redirect('index.php');
}

AriKernel::import('Joomla.Controllers.Resolver');

$resolver = new AriControllersResolver(array(
	'path' => dirname(__FILE__) . '/controllers/',
	'controllerPrefix' => 'AriQuiz'
));
$resolver->execute(
	JRequest::getWord('view', 'quizzes'),
	JRequest::getCmd('task', '')
);