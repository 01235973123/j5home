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

AriKernel::import('Joomla.Controllers.Controller');
AriKernel::import('Web.Controls.Data.MultiPageDataTable');
AriKernel::import('Data.DataFilter');
AriKernel::import('Joomla.Form.Form');
AriKernel::import('Joomla.Form.MassEditForm');
AriKernel::import('Utils.ArrayHelper');
AriKernel::import('Web.Response');

class AriQuizControllerQuizquestions extends AriController 
{
	var $_questionStateKey = 'com_ariquiz.dtQuizQuestiones%d';
    private $_filter = null;
	
	function _getQuestionsStateKey($quizId)
	{
		return sprintf($this->_questionStateKey, $quizId);
	}
	
	function quizzes() 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizzes');
	}

	function add()
	{
		$questionType = JRequest::getString('newQuestionType');
		$quizId = JRequest::getInt('quizId');
		
		$redirectUri = new JURI('index.php?option=com_ariquiz&view=quizquestion&task=add&quizId=' . $quizId);
		switch ($questionType)
		{
			case 'newQuestionQuestionType':
				$form = new AriForm('common');
				$form->load(AriQuizHelper::getFormPath('question', 'question_questiontype'));
				$form->bind(JRequest::getVar('type'));
				$questionTypeId = intval($form->get('QuestionTypeId'), 10);
				$redirectUri->setVar('questionTypeId', $questionTypeId);
				break;
				
			case 'newQuestionQuestionTemplate':
				$form = new AriForm('common');
				$form->load(AriQuizHelper::getFormPath('question', 'question_questiontemplate'));
				$form->bind(JRequest::getVar('questionTemplate'));
				$questionTemplateId = intval($form->get('TemplateId'), 10);
				$redirectUri->setVar('questionTemplateId', $questionTemplateId);
				break;
				
			default:
				throw new Exception(
					__CLASS__ . '::' . __FUNCTION__ . ': Unknown new question type.',
					500
				);
		}
		
		$redirectUri->setVar('newQuestionType', $questionType);
		
		$this->redirect($redirectUri->toString());
	}
	
	function edit()
	{
		$quizId = JRequest::getInt('quizId');
		$questionId = JRequest::getVar('QuestionId');
		if (is_array($questionId) && count($questionId) > 0)
			$questionId = $questionId[0];
			
		$questionId = intval($questionId, 10);
		
		$this->redirect('index.php?option=com_ariquiz&view=quizquestion&task=edit&questionId=' . $questionId . '&quizId=' . $quizId);
	}
	
	function display($cachable = false, $urlparams = []) 
	{
		$quizId = JRequest::getInt('quizId');
		$quizModel = $this->getModel('Quiz');
		
		$quiz = $quizModel->getQuiz($quizId);
		if ($quiz == null)
			$this->redirect('index.php?option=com_ariquiz&view=quizzes');
		
		$qtModel =& $this->getModel('QuestionTypes');
		$questionTypes = $qtModel->getQuestionTypeList();

        $filter = $this->_getFilter(false, true);
        $filterPredicates = $filter->getConfigValue('filter');
		
		$view =& $this->getView();
		$view->displayView($questionTypes, $quiz, $filterPredicates);
	}
	
	function exportCSV()
	{
		$quizId = JRequest::getInt('quizId');
		$questionId = JRequest::getVar('QuestionId');
		$csvExporter = $this->getModel('QuestionsCSVExport');
		$result = $csvExporter->exportQuizQuestions(
			$questionId
		);

		if (empty($result))
		{
			$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId . '&__MSG=COM_ARIQUIZ_ERROR_CSVEXPORT');
			exit();
		}

		AriResponse::sendContentAsAttach($result, 'questions.csv');
		exit();
	}
	
	function uploadCSVImport()
	{
		$quizId = JRequest::getInt('quizId');
		if (!AriQuizHelper::isAuthorise('question.create', 'com_ariquiz.quiz.' . $quizId))
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId);
		}

		$file = JRequest::getVar('importDataCSVFile', '', 'files', 'array');
		$fileName = null;
		if (!empty($file) && $file['size'] > 0)
			$fileName = $file['tmp_name'];

		$result = $this->_CSVImport($fileName);
		
		$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId . '&__MSG=' . ($result ? 'COM_ARIQUIZ_COMPLETE_DATAIMPORT' : 'COM_ARIQUIZ_COMPLETE_DATAIMPORTFAILED'));
	}
	
	function importCSVFromDir()
	{
		$quizId = JRequest::getInt('quizId');
		if (!AriQuizHelper::isAuthorise('question.create', 'com_ariquiz.quiz.' . $quizId))
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId);
		}

		$file = JRequest::getString('importDataCSVDir');
		$result = false;
		if (!empty($file) && @file_exists($file) && @is_file($file))
			$result = $this->_CSVImport($file);
		
		$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId . '&__MSG=' . ($result ? 'COM_ARIQUIZ_COMPLETE_DATAIMPORT' : 'COM_ARIQUIZ_COMPLETE_DATAIMPORTFAILED'));
	}
	
	function ajaxOrderUp()
	{
		$questionId = JRequest::getInt('questionId');
		if (!AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quizquestion.' . $questionId))
		{
			return false;
		}
		
		$model = $this->getModel();
		return $model->changeQuestionOrder($questionId, -1);
	}
	
	function ajaxOrderDown()
	{
		$questionId = JRequest::getInt('questionId');
		if (!AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quizquestion.' . $questionId))
		{
			return false;
		}
		
		$model = $this->getModel();
		return $model->changeQuestionOrder($questionId, 1);
	}
	
	function ajaxImportFromBank()
	{
		$fields = JRequest::getVar('fromBankSettings', array(), 'default', 'none', JREQUEST_ALLOWRAW);
		$quizId = JRequest::getInt('quizId');
		$categoryId = @intval(AriUtils::getParam($fields, 'CategoryId'));
		
		if (($categoryId > 0 && !AriQuizHelper::isAuthorise('question.create', 'com_ariquiz.questioncategory.' . $categoryId))
			||
			($categoryId == 0 && !AriQuizHelper::isAuthorise('question.create', 'com_ariquiz.quiz.' . $quizId))
		)
		{
			return false;
		}

		$settingsForm = new AriForm('common');
		$settingsForm->load(AriQuizHelper::getFormPath('question', 'frombank_settings'));
		if (!$settingsForm->validate($fields))
			return false;
		
		$user =& JFactory::getUser();
		$userId = $user->get('id');

		$score = @intval(AriUtils::getParam($fields, 'Score'));

		$model =& $this->getModel();
		$result = $model->addQuestionsFromBank(
			JRequest::getVar('BankQuestionId'),
			$quizId,
			$categoryId,
			$score,
			$userId
		);

		return $result;
	}
	
	function ajaxCopy()
	{
		$fields = JRequest::getVar('copy', array(), 'default', 'none', JREQUEST_ALLOWRAW);
		$quizId = intval(AriUtils::getParam($fields, 'QuizId'), 10);
		$categoryId = intval(AriUtils::getParam($fields, 'CategoryId'), 10);

		if (($categoryId > 0 && !AriQuizHelper::isAuthorise('question.create', 'com_ariquiz.questioncategory.' . $categoryId))
			||
			($categoryId == 0 && !AriQuizHelper::isAuthorise('question.create', 'com_ariquiz.quiz.' . $quizId))
		)
		{
			return false;
		}

		$copyForm = new AriForm('common');
		$copyForm->load(AriQuizHelper::getFormPath('question', 'question_copymove'));
		if (!$copyForm->validate($fields))
			return false;
		
		$user =& JFactory::getUser();
		$userId = $user->get('id');
		
		$model =& $this->getModel();
		$result = $model->copy(
			JRequest::getVar('QuestionId'),
			$quizId,
			$categoryId,
			$userId
		);
		
		return $result;
	}
	
	function ajaxMove()
	{
		$fields = JRequest::getVar('move', array(), 'default', 'none', JREQUEST_ALLOWRAW);
		$quizId = intval(AriUtils::getParam($fields, 'QuizId'), 10);
		$categoryId = intval(AriUtils::getParam($fields, 'CategoryId'), 10);
		
		if (($categoryId > 0 && !AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.questioncategory.' . $categoryId))
			||
			($categoryId == 0 && !AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quiz.' . $quizId))
		)
		{
			return false;
		}

		$moveForm = new AriForm('common');
		$moveForm->load(AriQuizHelper::getFormPath('question', 'question_copymove'));
		if (!$moveForm->validate($fields))
			return false;
		
		$user =& JFactory::getUser();
		$userId = $user->get('id');
		
		$model =& $this->getModel();
		$result = $model->move(
			JRequest::getVar('QuestionId'),
			$quizId,
			$categoryId,
			$userId
		);

		return $result;
	}
	
	function ajaxCopyToBank()
	{
		$user =& JFactory::getUser();
		$userId = $user->get('id');

		$fields = JRequest::getVar('copyToBank', array(), 'default', 'none', JREQUEST_ALLOWRAW);
		$bankCategoryId = intval(AriUtils::getParam($fields, 'QuestionCategoryId'), 10);
		
		if ($bankCategoryId > 0)
		{
			if (!AriQuizHelper::isAuthorise('bankquestion.create', 'com_ariquiz.bankcategory.' . $bankCategoryId))
				return false;
		}
		else
		{
			if (!AriQuizHelper::isAuthorise('bankquestion.create'))
				return false;
		}

		$model =& $this->getModel();
		$result = $model->copyToBank(
			JRequest::getVar('QuestionId'),
			$fields,
			$userId
		);
		
		return $result;
	}
	
	function ajaxGetQuestionList()
	{
		$model =& $this->getModel();
		$quizId = JRequest::getInt('quizId');

        $filter = $this->_getFilter();

		$totalCnt = $model->getQuestionCount($quizId, $filter);
		$filter->fixFilter($totalCnt);

		$questions = $this->_extendQuizQuestionList(
			$model->getQuestionList($quizId, $filter)
		);
		$data = AriMultiPageDataTableControl::createDataInfo($questions, $filter, $totalCnt); 

		return $data;
	}
	
	function _extendQuizQuestionList($data)
	{
		if (!is_array($data))
			return $data;

		for ($i = 0; $i < count($data); $i++)
		{
			$id = $data[$i]->QuestionId;
			
			$data[$i]->AllowEdit = AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quizquestion.' . $id);
		}

		return $data;
	} 
	
	function ajaxDelete()
	{
		$idList = AriArrayHelper::toInteger(JRequest::getVar('QuestionId'), 1);
		if (count($idList) == 0) 
			return false;

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('question.delete', 'com_ariquiz.quizquestion.' . $id)) 
			{
				// Prune items that you can't change.
				unset($idList[$i]);
			}
		}

		$model =& $this->getModel();

		return $model->deleteQuestions($idList);
	}

	function ajaxMassEdit()
	{
		$fields = JRequest::getVar('massParams', array(), 'default', 'none', JREQUEST_ALLOWRAW);
		
		$massEditForm = new AriMassEditForm('common');
		$massEditForm->load(AriQuizHelper::getFormPath('question', 'quizquestion_massedit'));

		if (!$massEditForm->validate($fields))
			return false;
		
		$categoryId = intval(AriUtils::getParam($fields, 'QuestionCategoryId'), 10);
		if ($categoryId > 0)
		{
			if (!AriQuizHelper::isAuthorise('question.create', 'com_ariquiz.questioncategory.' . $categoryId))
				return false;
		}
			
		$idList = AriArrayHelper::toInteger(JRequest::getVar('QuestionId'), 1);
		if (count($idList) == 0) 
			return false;

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quizquestion.' . $id)) 
			{
				// Prune items that you can't change.
				unset($idList[$i]);
			}
		}

		$user =& JFactory::getUser();
		$userId = $user->get('id');
		
		$model =& $this->getModel();

		return $model->update(
			$idList, 
			$fields,
			$userId,
			true
		);
	}
	
	function ajaxGetBankList()
	{
		$model =& $this->getModel('Bankquestions');
		$quizId = JRequest::getInt('quizId');
	
		$categoryId = JRequest::getInt('fromBankFilterCategoryId');
		$loadUsedQuestion = JRequest::getBool('fromBankFilterLoadUsedQuestion');
		
		$filterPredicates = array('NotLoadUsedQuestions' => !$loadUsedQuestion, 'QuizId' => $quizId);
		if ($categoryId > 0)
			$filterPredicates['CategoryId'] = $categoryId;
		
		$filter = new AriDataFilter(
			array(
				'startOffset' => 0, 
				'limit' => ARIQUIZ_GRID_PAGESIZE, 
				'sortField' => 'Question', 
				'dir' => 'asc',
				'filter' => $filterPredicates
			), 
			true);

		$totalCnt = $model->getQuestionCount($filter);
		$filter->fixFilter($totalCnt);

		$questions = $this->_extendBankQuestionList(
			$model->getQuestionList($filter)
		);
		$data = AriMultiPageDataTableControl::createDataInfo($questions, $filter, $totalCnt); 

		return $data;
	}
	
	function ajaxActivate()
	{
		$idList = AriArrayHelper::toInteger(JRequest::getVar('QuestionId'), 1);
		if (count($idList) == 0) 
			return false;

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quizquestion.' . $id)) 
			{
				// Prune items that you can't change.
				unset($idList[$i]);
			}
		}
		
		$model = $this->getModel();

		return $model->activateQuestion($idList);
	}
	
	function ajaxDeactivate()
	{
		$idList = AriArrayHelper::toInteger(JRequest::getVar('QuestionId'), 1);
		if (count($idList) == 0) 
			return false;

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quizquestion.' . $id)) 
			{
				// Prune items that you can't change.
				unset($idList[$i]);
			}
		}
		
		$model = $this->getModel();

		return $model->deactivateQuestion($idList);
	}
	
	function ajaxSingleDeactivate()
	{
		$questionId = JRequest::getInt('questionId');
		if ($questionId < 1 || !AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quizquestion.' . $questionId)) 
		{
			return false;
		}

		$model = $this->getModel();

		return $model->deactivateQuestion($questionId);
	}
	
	function ajaxSingleActivate()
	{
		$questionId = JRequest::getInt('questionId');
		if ($questionId < 1 || !AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quizquestion.' . $questionId)) 
		{
			return false;
		}
		
		$model = $this->getModel();

		return $model->activateQuestion($questionId);
	}
	
	function _extendBankQuestionList($data)
	{
		if (!is_array($data))
			return $data;

		for ($i = 0; $i < count($data); $i++)
		{
			$id = $data[$i]->QuestionId;
			
			$data[$i]->AllowEdit = AriQuizHelper::isAuthorise('bankquestion.edit', 'com_ariquiz.bankquestion.' . $id);
		}

		return $data;
	}
	
	function _CSVImport($csvFile)
	{
		$user =& JFactory::getUser();
		$quizId = JRequest::getInt('quizId');
		$csvImporter = $this->getModel('QuestionsCSVImport');
		$result = $csvImporter->importQuizQuestions(
			$csvFile,
			$quizId,
			$user->get('id'),
			0
		);
		
		return $result;
	}

    function ajaxFilters()
    {
        $filterData = JRequest::getVar('filter', null, 'default', 'none', JREQUEST_ALLOWRAW);

        $filter = $this->_getFilter(false, true);

        $filterPredicates = array();
        if (!empty($filterData['Id']))
            $filterPredicates['Id'] = intval($filterData['Id'], 10);

        $filter->setConfigValue('filter', $filterPredicates);
        $filter->store();

        return true;
    }

    function _getFilter($bindFromRequest = true, $restore = false)
    {
        if (!is_null($this->_filter))
            return $this->_filter;

        $quizId = JRequest::getInt('quizId');

        $this->_filter = new AriDataFilter(
            array(
                'startOffset' => 0,
                'limit' => ARIQUIZ_GRID_PAGESIZE,
                'sortField' => 'QuestionIndex2',
                'dir' => 'asc'
            ),
            $bindFromRequest,
            $this->_getQuestionsStateKey($quizId)
        );

        if ($restore)
            $this->_filter->restore();

        return $this->_filter;
    }
}