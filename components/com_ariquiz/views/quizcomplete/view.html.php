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

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

class AriQuizViewQuizcomplete extends AriQuizView 
{
	var $_isFormView = true;
	
	function displayView($params, $tpl = null) 
	{
		$resultInfo = $params['resultInfo'];
		$quizParams = isset($resultInfo['ExtraParams']) ? $resultInfo['ExtraParams'] : null;
        $btnTryAgainVisible = (bool)AriUtils::getParam($quizParams, 'TryAgain');
		$parsePluginTags = !empty($quizParams->ParsePluginTag);
		if ($params['isDetailedResultsAvailable'])
		{
			$this->assignRef(
				'dtResults', 
				$this->_getResultsDataTable(
					$resultInfo['StatisticsInfoId'], 
					$params['ticketId'], 
					$resultInfo['DetailedResultsCount'], 
					$parsePluginTags,
					(bool)$resultInfo['HideCorrectAnswers']
				)
			);
		}

        $this->assign('socialMessage', $params['socialMessage']);
		$this->assign('ticketId', $params['ticketId']);
		$this->assign('resultText', $params['resultText']);
		$this->assign('btnEmailVisible', $params['btnEmailVisible']);
		$this->assign('btnPrintVisible', $params['btnPrintVisible']);
		$this->assign('btnCertificateVisible', $params['btnCertificateVisible']);
        $this->assign('isOwnResult', $params['isOwnResult']);
		$this->assign('shareResults', (bool)AriUtils::getParam($resultInfo, 'ShareResults'));
        $this->assign('btnTryAgainVisible', $btnTryAgainVisible);

        if ($btnTryAgainVisible)
            $this->assign('quizLink', $this->getQuizLink($resultInfo));
		
		if ($parsePluginTags)
			$this->_loadPluginsAssets($params['questions']);

		parent::display($tpl);

		$this->_prepareDocument($params['resultInfo'], $params['quizParams'], $params['socialMessage']);
	}

    function getQuizLink($resultInfo)
    {
        $input = JFactory::getApplication()->input;
        $tmpl = $input->get('tmpl');
        $itemId = $input->get('Itemid', null, 'INT');

        return JRoute::_('index.php?option=com_ariquiz&view=quiz&quizId=' . $resultInfo['QuizId'] . ($tmpl ? '&tmpl=' . $tmpl : '') . ($itemId > 0 ? '&Itemid=' . $itemId : ''));
    }

	function displayPrint($params, $tpl = null)
	{
		$this->assign('content', $params['content']);

		if ($params['isDetailedResultsAvailable'])
		{
			$resultInfo = $params['resultInfo'];
			$quizParams = isset($resultInfo['ExtraParams']) ? $resultInfo['ExtraParams'] : null; 
			$parsePluginTags = !empty($quizParams->ParsePluginTag);
			
			$this->assignRef(
				'dtResults', 
				$this->_getResultsDataTable(
					$resultInfo['StatisticsInfoId'], 
					$params['ticketId'], 
					$resultInfo['DetailedResultsCount'], 
					$parsePluginTags,
					(bool)$resultInfo['HideCorrectAnswers'],
					true
				)
			);
			
			if ($parsePluginTags)
				$this->_loadPluginsAssets($params['questions']);
		}

		parent::display('print');

		$this->_prepareDocument($params['resultInfo'], $params['quizParams']);
		$this->_preparePrintDocument();
	}

	function _getResultsDataTable($sid, $ticketId, $totalCnt, $parsePluginTag, $hideCorrectAnswers = false, $print = false)
	{
		AriKernel::import('Web.Controls.Data.MultiPageDataTable');

		$columns = array(
			new AriDataTableControlColumn(
				array(
					'key' => 'QuestionData', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUIZRESULTS'), 
					'formatter' => $hideCorrectAnswers
						? 'YAHOO.ARISoft.Quiz.formatters.formatQuestionStatDataWithoutCorrectAnswers'
						: 'YAHOO.ARISoft.Quiz.formatters.formatQuestionStatData'
				)
			)
		);

		$dataTable = new AriMultiPageDataTableControl(
			'dtResults',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=quizcomplete&task=ajaxGetResultList&sid=' . $sid . '&ticketId=' . $ticketId . '&parseTag=' . ($parsePluginTag ? '1' : '0') . ($print ? '&print=1' : ''),
				'disableHighlighting' => true
			),
			$this->_getPaginatorOptions($totalCnt)
		);

		return $dataTable;
	}
	
	function _getPaginatorOptions($totalCnt)
	{
		$rowsPerPage = array(1);
		if ($totalCnt > 1)
		{
			$pageCnt = floor($totalCnt / 5);
			for ($i = 0; $i < $pageCnt; $i++)
			{
				$rowsPerPage[] = 5 * ($i + 1); 
			}
			
			if ($totalCnt % 5 > 0) $rowsPerPage[] = $totalCnt;
		}

		$pagRowsPerPage = $totalCnt < 5 ? $totalCnt : 5; 

		$options = array('rowsPerPageOptions' => $rowsPerPage, 'rowsPerPage' => $pagRowsPerPage);
		$defOptions = AriQuizHelper::getPaginatorOptions();
		
		return array_merge($defOptions, $options);
	}
	
	function _loadPluginsAssets($questions)
	{
		AriKernel::import('Joomla.Plugins.PluginProcessHelper');
		AriKernel::import('Document.DocumentIncludesManager');

		$includesManager = new AriDocumentIncludesManager();

		// process
		$content = '';
		foreach ($questions as $question)
		{
			$content .= $question->Question;
			if (!empty($question->QuestionNote))
				$content .= $question->QuestionNote;
		}
		AriPluginProcessHelper::processTags($content);

		$includes = $includesManager->getDifferences(true, array('script'));
		AriDocumentHelper::addCustomTagsToDocument($includes);
	}
	
	function _prepareDocument($quizInfo, $quizParams, $shareSummary = null)
	{
        $shareMode = JRequest::getBool('share');

        if (J3_0)
            JHtml::_('bootstrap.framework');

		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$title = AriUtils::getParam($quizInfo['Metadata'], 'title'); 
		
		$assetsUri = JURI::root(true) . '/components/com_ariquiz/assets/';
		
		$document->addScript($assetsUri . 'js/questions.js?v=' . ARIQUIZ_VERSION);
		$document->addScriptDeclaration(
			sprintf(
				';QUIZ_EXTRA_PARAMS = %1$s;',
				$quizParams ? json_encode($quizParams) : '{}'
			)
		);

		if (empty($title))
			$title = $quizInfo['QuizName'];

		$title = AriQuizHelper::formatPageTitle($title);

		$document->setTitle($title);

        $metaDescription = null;
        if ($shareMode && $shareSummary)
        {
            $metaDescription = $shareSummary;
        }
		else
        {
            $metaDescription = AriUtils::getParam($quizInfo['Metadata'], 'description');
            if (empty($metaDescription))
                $metaDescription = $params->get('menu-meta_description');

            if (empty($metaDescription))
                $metaDescription = strip_tags($quizInfo['Description']);
        }

		if (!empty($metaDescription))
			$document->setDescription($metaDescription);
			
		$metaKeywords = AriUtils::getParam($quizInfo['Metadata'], 'keywords');
		if (empty($metaKeywords))
			$metaKeywords = $params->get('menu-meta_keywords');

		if (!empty($metaKeywords))
			$document->setMetadata('keywords', $metaKeywords);
	}
	
	function _preparePrintDocument()
	{
		$document = JFactory::getDocument();
		
		$document->addStyleDeclaration('.aq-dt-results .yui-pg-container{display:none;}');
	}
}