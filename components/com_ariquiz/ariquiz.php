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

use Joomla\CMS\Factory;

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/defines.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/helper.php';

AriKernel::import('Joomla.Compat.Request');
AriKernel::import('Joomla.Controllers.Resolver');

$app = Factory::getApplication();

$resolver = new AriControllersResolver(array(
	'path' => dirname(__FILE__) . '/controllers/',
	'controllerPrefix' => 'AriQuiz'
));
$resolver->execute(
	$app->input->getWord('view', 'quizzes'),
	$app->input->getCmd('task', ''));