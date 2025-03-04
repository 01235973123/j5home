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
AriKernel::import('CSV.CSVParser');
AriKernel::import('Application.ARIQuiz.CSVImport.QuestionFactory');

class AriQuizModelQuestionsCSVImport extends AriModel 
{	
	function getQuestionTypes()
	{
		static $types;
		
		if (is_null($types))
		{
			$types = array();
			$qtModel =& AriModel::getInstance('Questiontypes', $this->getFullPrefix());
			$questionTypes = $qtModel->getQuestionTypeList();
			if (is_array($questionTypes))
			{
				foreach ($questionTypes as $type)
				{
					$types[$type->ClassName] = $type;
				}
			}
		}
		
		return $types;
	}
	
	function parseCSVFile($filePath)
	{
		$data = array();
		if (empty($filePath) || !@file_exists($filePath) || !@is_file($filePath))
			return $data;
			
		$csvParser = new AriCSVParser();
		$csvParser->auto($filePath);
		$csvData = $csvParser->data;
		if (empty($csvData))
			return $data;

		$question = null;
		foreach ($csvData as $csvDataItem)
		{
			if (!empty($csvDataItem['Type']))
			{
				if (!is_null($question))
					$data[] = $question;
				
				$question = $csvDataItem;
				$question['_Childs'] = array();

				continue ;
			}

			if (!is_null($question))
				$question['_Childs'][] = $csvDataItem;
		}

		if (!is_null($question))
			$data[] = $question;
		
		return $data;
	}
	
	function importBankQuestions($filePath, $userId = 0, $defaultCategoryId = 0)
	{
		$data = $this->parseCSVFile($filePath);
		if (empty($data))
			return false;

		$result = true;
		$categoryMapping = $this->getBankCategoryMapping($data, $userId);
		foreach ($data as $dataItem)
		{
			$categoryName = trim(AriUtils::getParam($dataItem, 'Category', ''));
			$categoryId = !empty($categoryName) && isset($categoryMapping[$categoryName])
				? $categoryMapping[$categoryName]
				: $defaultCategoryId;

			if (!$this->importBankQuestion($dataItem, $userId, $categoryId))
				$result = false;
		}
		
		return $result;
	}
	
	function getQuizCategoryMapping($data, $quizId, $userId = 0)
	{
		$categoryMapping = array();
		
		$categoryList = $this->getCategoryList($data);
		$questionCategoriesModel =& AriModel::getInstance('Questioncategories', $this->getFullPrefix());
		$questionCategoryModel =& AriModel::getInstance('Questioncategory', $this->getFullPrefix());
		$mapping = $questionCategoriesModel->getCategoryMapping($categoryList, $quizId);
		foreach ($categoryList as $categoryName)
		{
			$categoryId = 0;
			if (!isset($mapping[$categoryName]))
			{
				$cat = $questionCategoryModel->saveCategory(
					array(
						'CategoryName' => $categoryName,
						'QuizId' => $quizId,
						'CreatedBy' => $userId
					)
				);

				if ($cat)
					$categoryId = $cat->QuestionCategoryId; 
			}
			else
			{
				$categoryId = $mapping[$categoryName];
			}
			
			$categoryMapping[$categoryName] = $categoryId;
		}
		
		return $categoryMapping;
	}
	
	function getBankCategoryMapping($data, $userId = 0)
	{
		$categoryMapping = array();
		
		$categoryList = $this->getCategoryList($data);
		$bankCategoriesModel =& AriModel::getInstance('Bankcategories', $this->getFullPrefix());
		$bankCategoryModel =& AriModel::getInstance('Bankcategory', $this->getFullPrefix());
		$mapping = $bankCategoriesModel->getCategoryMapping($categoryList);
		
		foreach ($categoryList as $categoryName)
		{
			$categoryId = 0;
			if (!isset($mapping[$categoryName]))
			{
				$cat = $bankCategoryModel->saveCategory(
					array(
						'CategoryName' => $categoryName,
						'CreatedBy' => $userId
					)
				);
				
				if ($cat)
					$categoryId = $cat->CategoryId; 
			}
			else
			{
				$categoryId = $mapping[$categoryName];
			}
			
			$categoryMapping[$categoryName] = $categoryId;
		}
		
		return $categoryMapping;
	}
	
	function getCategoryList($data)
	{
		$categoryList = array();
		if (is_array($data))
		{
			foreach ($data as $dataItem)
			{
				$categoryName = trim(AriUtils::getParam($dataItem, 'Category', ''));
				if (!empty($categoryName))
					$categoryList[] = $categoryName;
			}
		}
		
		return array_unique($categoryList);
	}
	
	function importQuizQuestions($filePath, $quizId, $userId = 0, $defaultCategoryId = 0)
	{
		if ($quizId < 1)
			return false;

		$data = $this->parseCSVFile($filePath);
		if (!is_array($data) || count($data) == 0)
			return false;

		$result = true;
		$categoryMapping = $this->getQuizCategoryMapping($data, $quizId, $userId);
		foreach ($data as $dataItem)
		{ 
			$categoryName = trim(AriUtils::getParam($dataItem, 'Category', ''));
			$categoryId = !empty($categoryName) && isset($categoryMapping[$categoryName])
				? $categoryMapping[$categoryName]
				: $defaultCategoryId;

			if (!$this->importQuizQuestion($dataItem, $userId, $quizId, $categoryId))
				$result = false;
		}

		return $result;
	}
	
	function importBankQuestion($questionData, $userId, $categoryId = 0)
	{
		$questionModel =& AriModel::getInstance('Bankquestion', $this->getFullPrefix());
		
		return $this->importQuestion($questionModel, $questionData, $userId, 0, $categoryId);
	}

	function importQuizQuestion($questionData, $userId, $quizId = 0, $categoryId = 0)
	{
		$questionModel =& AriModel::getInstance('Quizquestion', $this->getFullPrefix());
		
		return $this->importQuestion($questionModel, $questionData, $userId, $quizId, $categoryId);
	}
	
	function importQuestion($questionModel, $questionData, $userId, $quizId = 0, $categoryId = 0)
	{
		$typeClass = $questionData['Type'];
		$types = $this->getQuestionTypes();
		if (!isset($types[$typeClass]))
			return false;

		$type = $types[$typeClass];
		$question = AriUtils::getParam($questionData, 'Question');
		if (empty($question))
			return false;

		$importQuestionWrapper = AriQuizCSVImportQuestion::getQuestion($typeClass);
		if (is_null($importQuestionWrapper))
			return false;

		$score = @floatval(AriUtils::getParam($questionData, 'Score'));
		if ($score < 0)
			$score = 0;
			
		$penalty = @floatval(AriUtils::getParam($questionData, 'Penalty'));

		$data = $importQuestionWrapper->getXml($questionData);

		return $questionModel->saveQuestion(
			array(
				'CreatedBy' => $userId,
				'QuizId' => $quizId,
				'QuestionCategoryId' => $categoryId,
				'QuestionTypeId' => $type->QuestionTypeId,
				'QuestionVersion' => array(
					'CreatedBy' => $userId,
					'Score' => $importQuestionWrapper->getMaximumQuestionScore($score, $data),
					'Penalty' => $penalty,
    				'Question' => $question,
    				'Note' => AriUtils::getParam($questionData, 'Note', ''),
					'Data' => $data
				)
			)
		);
	}
}