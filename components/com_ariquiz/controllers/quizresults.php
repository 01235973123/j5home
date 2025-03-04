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

class AriQuizControllerQuizresults extends AriController 
{
	function __construct($config = array()) 
	{
		if (!array_key_exists('model_path', $config))
			$config['model_path'] = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_ariquiz' . DS . 'models';

		parent::__construct($config);
	}
	
	function display($cachable = false, $urlparams = [])
	{
		$user =& JFactory::getUser();
		$userId = $user->get('id');
		if ($userId < 1)
		{
			$this->redirect(
				JRoute::_('index.php?option=com_ariquiz&view=message&msg=COM_ARIQUIZ_ERROR_ACCESSDENIED', false)
			);
		}
		
		$view =& $this->getView();
		$view->displayView();
	}

	function ajaxGetResultList()
	{
		$user =& JFactory::getUser();
		$userId = $user->get('id');

		if ($userId < 1)
			return null;

        $app = JFactory::getApplication();
        $params = $app->getParams();
        $sortField = 'StartDate2';
        $sortDir = 'desc';
        if (!empty($params))
        {
            $field = $params->get('sortfield');
            if (in_array($field, array('StartDate2', 'EndDate2')))
                $sortField = $field;

            $dir = strtolower($params->get('sortdir'));
            if (in_array($dir, array('asc', 'desc')))
                $sortDir = $dir;
        }

		$model =& $this->getModel();

		$filter = new AriDataFilter(
			array(
				'startOffset' => 0, 
				'limit' => 10, 
				'sortField' => $sortField,
				'sortDirection' => $sortDir,
				'filter' => array(
					'UserId' => $userId
				)
			), 
			true);
			
		$totalCnt = $model->getResultCount($filter);
		$filter->fixFilter($totalCnt);

		$results = $model->getResultList($filter);
		$results = $this->_modifyResults($results);
		
		$data = AriMultiPageDataTableControl::createDataInfo($results, $filter, $totalCnt); 

		return $data;
	}
	
	function _modifyResults($results)
	{
		if (empty($results))
			return $results;
			
		for ($i = 0; $i < count($results); $i++)
		{
			$result = $results[$i];
			
			$result->ResultsLink = JRoute::_('index.php?option=com_ariquiz&view=quizcomplete&ticketId=' . $result->TicketId);
			$result->StartDate = AriDateUtility::formatDate($result->StartDate);
			$result->EndDate = AriDateUtility::formatDate($result->EndDate);
			$result->StartDate2 = AriDateUtility::formatDate($result->StartDate2);
			$result->EndDate2 = AriDateUtility::formatDate($result->EndDate2);
			
			$results[$i] = $result;
		}

		return $results;
	}
}