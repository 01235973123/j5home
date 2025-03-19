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
	
	function display($params, $tpl = null) 
	{
		$resultInfo = $params['resultInfo'];
		$quizParams = isset($resultInfo['ExtraParams']) ? $resultInfo['ExtraParams'] : null; 
		$parsePluginTags = !empty($quizParams->ParsePluginTag);
		if ($params['isDetailedResultsAvailable'])
			$this->assignRef('dtResults', $this->_getResultsDataTable($resultInfo['StatisticsInfoId'], $params['ticketId'], $resultInfo['DetailedResultsCount'], $parsePluginTags));

		$this->assign('ticketId', $params['ticketId']);
		$this->assign('resultText', $params['resultText']);
		$this->assign('btnEmailVisible', $params['btnEmailVisible']);
		$this->assign('btnPrintVisible', $params['btnPrintVisible']);
		$this->assign('btnCertificateVisible', $params['btnCertificateVisible']);
		
		if ($parsePluginTags)
			$this->_loadPluginsAssets($params['questions']);

		parent::display($tpl);

		$this->_prepareDocument($params['resultInfo']);
	}

	function _getResultsDataTable($sid, $ticketId, $totalCnt, $parsePluginTag)
	{
		AriKernel::import('Web.Controls.Data.MultiPageDataTable');

		$columns = array(
			new AriDataTableControlColumn(
				array(
					'key' => 'QuestionData', 
					'label' => JText::_('COM_ARIQUIZ_LABEL_QUIZRESULTS'), 
					'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQuestionStatData'
				)
			)
		);

		$dataTable = new AriMultiPageDataTableControl(
			'dtResults',
			$columns, 
			array(
				'dataUrl' => 'index.php?option=com_ariquiz&view=quizcomplete&task=ajaxGetResultList&sid=' . $sid . '&ticketId=' . $ticketId . '&parseTag=' . ($parsePluginTag ? '1' : '0'),
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
	
	function _prepareDocument($quizInfo)
	{
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$title = AriUtils::getParam($quizInfo['Metadata'], 'title'); 
		
		$assetsUri = JURI::root(true) . '/components/com_ariquiz/assets/';
		
		$document->addScript($assetsUri . 'js/questions.js?v=' . ARIQUIZ_VERSION);


		if (empty($title))
			$title = $quizInfo['QuizName'];

		$title = AriQuizHelper::formatPageTitle($title);

		$document->setTitle($title);
		
		$metaDescription = AriUtils::getParam($quizInfo['Metadata'], 'description');
		if (empty($metaDescription))
			$metaDescription = $params->get('menu-meta_description');
			
		if (empty($metaDescription))
			$metaDescription = strip_tags($quizInfo['Description']);

		if (!empty($metaDescription))
			$document->setDescription($metaDescription);
			
		$metaKeywords = AriUtils::getParam($quizInfo['Metadata'], 'keywords');
		if (empty($metaKeywords))
			$metaKeywords = $params->get('menu-meta_keywords');

		if (!empty($metaKeywords))
			$document->setMetadata('keywords', $metaKeywords);
	}
}