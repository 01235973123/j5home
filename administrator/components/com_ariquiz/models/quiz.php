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

AriKernel::import('Joomla.Models.Model');
AriKernel::import('Joomla.Database.DBUtils');

class AriQuizModelQuiz extends AriModel 
{
	function getQuiz($quizId, $strictLoad = true) 
	{
		if ($strictLoad && $quizId < 1)
			return null;

		$quiz =& $this->getTable();
		$quiz->load($quizId);

		if ($strictLoad && empty($quiz->QuizName))
			$quiz = null;

		return $quiz;
	}

	function getQuizByTicketId($ticketId, $strictLoad = true)
	{
		if ($strictLoad && empty($ticketId))
			return null;
			
		$quiz =& $this->getTable();
		$quiz->loadByTicketId($ticketId);
		
		if ($strictLoad && empty($quiz->QuizName))
			$quiz = null;

		return $quiz;
	}
	
	function saveQuiz($data, $extraData, $metaData)
	{
		if (!is_array($data))
			$data = array();

		$data['ExtraParams'] = $extraData;
		$data['Metadata'] = $metaData;

		$quiz =& $this->getTable();
		$quiz->bind($data);

		if (!$quiz->store())
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_SQL_QUERY', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$quiz->getQuery(), 
					$quiz->getError()
				),
				E_USER_ERROR
			);
			return null;
		}
		
		return $quiz;
	}

	function isUniqueQuizName($name, $id = null)
	{
		$db =& $this->getDBO();

		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquiz');

		$query->where('QuizName = ' . $db->Quote($name));
		if ($id)
			$query->where('QuizId <> ' . intval($id, 10));

		$db->setQuery((string)$query);

		$isUnique = $db->loadResult();
		if (is_null($isUnique))
		{
			return null;
		}
		
		return ($isUnique == 0);
	}

	function copy($quizId, $quizName, $userId)
	{
		$quiz = $this->getQuiz($quizId);
		if (empty($quiz))
			return null;

		$copyQuiz =& $this->getTable('Quiz');
		$copyQuiz->copyFrom($quiz, $quizName, $userId);

		if (!$copyQuiz->store(true, false))
		{
			return null;
		}

		$copyQuizId = $copyQuiz->QuizId;
		
		$questionCategoriesModel =& AriModel::getInstance('Questioncategories', $this->getFullPrefix());
		$categoryMapping = $questionCategoriesModel->copy($quizId, $copyQuizId, $userId);

		$questionModel =& AriModel::getInstance('Quizquestions', $this->getFullPrefix());
		$questionList = $questionModel->getQuestionList(
			$quizId, 
			new AriDataFilter(
				array(
					'sortField' => 'QuestionCategoryId', 
					'sortDirection' => 'asc'
				)
			)
		);

		if (is_array($questionList) && count($questionList) > 0)
		{
			$prevCatId = -1;
			$catQuestionList = array();
			$catQuestionListMapping = array();
			foreach ($questionList as $question)
			{
				$queCatId = !is_null($question->QuestionCategoryId)
					? $question->QuestionCategoryId
					: 0;
				if ($prevCatId != $queCatId)
				{
					$newCatId = $queCatId > 0
						? (isset($categoryMapping[$queCatId]) ? $categoryMapping[$queCatId] : - 1)
						: 0;
										echo $newCatId.'!';
					if ($newCatId > -1)
					{
						$catQuestionListMapping[$newCatId] = array();
						$catQuestionList =& $catQuestionListMapping[$newCatId];
					}
						
					$prevCatId = $queCatId;
				}
					
				$catQuestionList[] = $question->QuestionId;
			}

			foreach ($catQuestionListMapping as $categoryId => $idList)
			{
				$questionModel->copy(
						$idList, 
						$copyQuizId, 
						$categoryId, 
						$userId);
			}
		}
 
		return $copyQuiz;
	}
	
	function update($quizId, $fields, $extraFields, $userId)
	{
		$quiz = $this->getQuiz($quizId);
		if (empty($quiz))
			return false;

		return $quiz->update($fields, $extraFields);
	}
}