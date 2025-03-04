<?php
/*
 * ARI Quiz Router
 *
 * @package		ARI Quiz Router
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/helper.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/quiz.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/tables/quiz.php';

AriKernel::import('Utils.Utils');
AriKernel::import('Joomla.Compat.Application');

class AriQuizRouterInt
{
	var $COMPONENT = 'com_ariquiz';
	var $DEFAULT_TASK = 'quizzes';
	var $TASK_ALIAS = array(
		'quiz' => 'Quiz',
		'quizzes' => 'Quizzes',
		'question' => 'Question',
		'quizcomplete' => 'Results',
		'quizresults' => 'Quizzes_Results',
		'category' => 'Categories_Quizzes',
		'message' => 'Quiz_Warning',
		'terminate' => 'Quiz_Terminated'
	);

	var $TASK_INHERIT = array(
		'quizzes' => array('quiz', 'message', 'terminate'),
		'quiz' => array('question', 'quizcomplete', 'message', 'terminate'),
		'category' => array('quiz', 'message', 'terminate'),
		'quizresults' => array('quizresults', 'quiz', 'quizcomplete'),
	);
	
	var $IGNORE_TASK = array(
	);
	
	function getTaskAlias($task)
	{
		return array_key_exists($task, $this->TASK_ALIAS)
			? $this->TASK_ALIAS[$task]
			: $task;
	}
	
	function getItemTask($itemId)
	{
		$task = null;
		if (empty($itemId))
			return $task;

		$app = JFactory::getApplication();
		$menu =& $app->getMenu('site');
		$menuItem = &$menu->getItem($itemId);
		if (isset($menuItem->query['option']) && 
			$menuItem->query['option'] == $this->COMPONENT)
		{
			$task = !empty($menuItem->query['view'])
				? $menuItem->query['view']
				: $this->DEFAULT_TASK;
		}		
		
		return $task;
	}
	
	function build(&$query)
	{
		$segments = array();
		$forceTask = false;
		$task = $this->DEFAULT_TASK;
		if (!empty($query['view']))
			$task = $query['view'];
		else if (!empty($query['Itemid']))
		{
			$app = JFactory::getApplication();
			$menu =& $app->getMenu('site');
			$menuItem = &$menu->getItem($query['Itemid']);
			if (!empty($menuItem->query['view']))
				$task = $menuItem->query['view'];
		}

		if (empty($task))
			return $segments;
		else if (in_array($task, $this->IGNORE_TASK))
		{
			unset($query['Itemid']);
			return $segments;
		}

		if (empty($query['Itemid'])) 
		{
			$segments[] = $this->getTaskAlias($task);
		}
		else 
		{
			$app = JFactory::getApplication();
			$menu =& $app->getMenu('site');
			$menuItem = &$menu->getItem($query['Itemid']);
			if ($menuItem && isset($menuItem->query['view']) && $menuItem->query['view'] != $task)
				$forceTask = true;
			
			if (!isset($menuItem->query['option']) || 
				$menuItem->query['option'] != $this->COMPONENT) 
			{
				$segments[] = $this->getTaskAlias($task);
			}
		}

		if ($task != $this->DEFAULT_TASK)
		{
			if (!$forceTask)
				$forceTask = count($segments) > 0;

			$taskHandler = 'buildTask_' . $task;
			if (method_exists($this, $taskHandler)) 
				$this->$taskHandler($query, $segments, $forceTask);
		}

		unset($query['view']);

		return $this->_prepareSegments($segments);
	}

	function _prepareSegments($segments)
	{
		if (!function_exists('iconv'))
			return $segments;

		setlocale(LC_ALL, 'en_US.UTF8');
		$updatedSegments = array();
		foreach ($segments as $segment)
		{
			$updatedSegments[] = preg_replace(
				'/[^A-z0-9\-\_]/i', 
				'', 
				iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $segment)
			);
		}

		for ($i = 0; $i < count($updatedSegments); $i++)
		{
			$updatedSegments[$i] = str_replace(':', '-', $updatedSegments[$i]);
		}

		return $updatedSegments;
	}
	
	function buildTask_quiz(&$query, &$segments, $forceTask)
	{
		$quizId = intval(AriUtils::getParam($query, 'quizId'), 10);
		if ($quizId < 1)
			return ;

		if ($forceTask)
		{
			$quizModel =& AriModel::getInstance('Quiz', 'AriQuizModel');
			$quiz = $quizModel->getQuiz($quizId);
			$segments[] = $quizId . '-' . $quiz->QuizName;
		}

		unset($query['quizId']);
	}
	
	function buildTask_question(&$query, &$segments, $forceTask)
	{
		$ticketId = AriUtils::getParam($query, 'ticketId');
		$quizModel =& AriModel::getInstance('Quiz', 'AriQuizModel');
		$quiz = $quizModel->getQuizByTicketId($ticketId);
		
		if ($forceTask)
		{
			if (!empty($query['Itemid']))
			{
				$app = JFactory::getApplication();
				$menu =& $app->getMenu('site');
				$menuItem = &$menu->getItem($query['Itemid']);
				if (!empty($menuItem->query['view']) && $menuItem->query['view'] == 'quiz')
					$forceTask = false;
			}
		
			if ($forceTask)
				$segments[] = $quiz->QuizId . '-' . $quiz->QuizName;
		}
		$segments[] = $this->getTaskAlias('question') . '-' . $quiz->QuizId . '-' . $ticketId;

		unset($query['ticketId']);
		unset($query['quizId']);
	}
	
	function buildTask_quizcomplete(&$query, &$segments, $forceTask)
	{
		$ticketId = AriUtils::getParam($query, 'ticketId');
		if ($forceTask)
		{
			if (!empty($query['Itemid']))
			{
				$app = JFactory::getApplication();
				$menu =& $app->getMenu('site');
				$menuItem = &$menu->getItem($query['Itemid']);
				if (!empty($menuItem->query['view']) && ($menuItem->query['view'] == 'quizresults' || $menuItem->query['view'] == 'quiz'))
					$forceTask = false;
			}
		
			if ($forceTask)
			{
				$quizModel =& AriModel::getInstance('Quiz', 'AriQuizModel');
				$quiz = $quizModel->getQuizByTicketId($ticketId);
		
				$segments[] = $quiz->QuizId . '-' . $quiz->QuizName;
			}
		}
		$segments[] = $this->getTaskAlias('quizcomplete') . '-' . $ticketId;
		
		unset($query['ticketId']);
	}
	
	function buildTask_quizresults(&$query, &$segments, $forceTask)
	{
		if ($forceTask)
			$segments[] = $this->getTaskAlias('quizresults');
	}
	
	function buildTask_quizzes(&$query, &$segments, $forceTask)
	{
		if ($forceTask)
			$segments[] = $this->getTaskAlias('quizzes');
	}
	
	function buildTask_message(&$query, &$segments, $forceTask)
	{
		if ($forceTask)
			$segments[] = $this->getTaskAlias('message');
	}

	function buildTask_terminate(&$query, &$segments, $forceTask)
	{
		if ($forceTask)
			$segments[] = $this->getTaskAlias('terminate');
	}
	
	function parse(&$segments)
	{
		$vars = array();
		if (!is_array($segments) || count($segments) == 0)
			return $vars;

		for ($i = 0; $i < count($segments); $i++) {
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		$offset = 0;
		$count = count($segments);
		$forceTask = false;
		$app = AriApplication::getApplication();
		$menu =& $app->getMenu('site');
		$taskInherit = $this->TASK_INHERIT;
		$activeMenuItem =& $menu->getActive();
		if (empty($activeMenuItem))
		{
			$itemId = AriApplication::getInput()->getInt('Itemid');
			if ($itemId)
			{
				$menu->setActive($itemId);
				$activeMenuItem = $menu->getItem($itemId);
			}
		}
		
		$task = null;
		
		if ($activeMenuItem)
		{
			$query = $activeMenuItem->query;
			if (isset($query['option']) && $query['option'] == $this->COMPONENT)
				$task = !empty($query['view']) ? $query['view'] : $this->DEFAULT_TASK;				
		}
 
		$flipAlias = array_flip($this->TASK_ALIAS);
		if (is_null($task))
		{
			$segment = $segments[$offset];
			if (array_key_exists($segment, $flipAlias))
				$segment = $flipAlias[$segment];
			
			if (array_key_exists($segment, $this->TASK_ALIAS))
			{
				$task = $segment;
				$forceTask = true;
			}
			else
			{
				list($task) = explode('-', $segments[$offset]);
				if (array_key_exists($task, $flipAlias))
					$task = $flipAlias[$task];
			}

			++$offset;
		}

		if (!$forceTask)
		{
			for ($i = $offset; $i < $count; $i++)
			{
				$segment = $segments[$i];
				if (array_key_exists($task, $taskInherit))
				{
					$subTask = $taskInherit[$task];
					if (is_array($subTask))
					{
						list($sTask) = explode('-', $segment);
						if (array_key_exists($sTask, $flipAlias))
							$sTask = $flipAlias[$sTask];
							
						$task = in_array($sTask, $subTask)
							? $sTask
							: $subTask[0];
					}
					else
					{
						$task = $subTask;
					}
				}
			}
		}

		if ($task)
		{		
			$taskHandler = 'parseTask_' . $task;
			if (method_exists($this, $taskHandler)) {
				$segment = $segments[$count - 1];
				$aliasPrefix = $this->getTaskAlias($task) . '-';
				if (strpos($segment, $aliasPrefix) === 0) {
					$segment = substr($segment, strlen($aliasPrefix));
				}

				$this->$taskHandler($segment, $vars);
			}
		}

		$vars['view'] = $task;

		return $vars;		
	}

	function parseTask_quiz($page, &$vars)
	{
		@list($quizId) = explode('-', $page);

		$vars['quizId'] = $quizId;
	}
	
	function parseTask_question($page, &$vars)
	{
		@list($quizId, $ticketId) = explode('-', $page);

		$vars['ticketId'] = $ticketId;
		$vars['quizId'] = $quizId;
	}
	
	function parseTask_quizcomplete($page, &$vars)
	{
		$ticketId = explode('-', $page);
		if (is_array($ticketId))
			$ticketId = $ticketId[count($ticketId) - 1];

		$vars['ticketId'] = $ticketId;
	}
}

class AriQuizRouter extends JComponentRouterBase
{
	public function build(&$query)
	{
		$router = new AriQuizRouterInt();

		return $router->build($query);
	}

	public function parse(&$segments)
	{
		$router = new AriQuizRouterInt();

		$vars = $router->parse($segments);
		$segments = array();

		return $vars;
	}
}

function ariquizBuildRoute(&$query)
{
	$router = new AriQuizRouter();

	return $router->build($query);
}

function ariquizParseRoute($segments)
{
	$router = new AriQuizRouter();
	
	return $router->parse($segments);
}