<?php
/*
 *
 * @package		CalendARI
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Joomla.Controllers.Controller');
AriKernel::import('Web.Controls.Data.MultiPageDataTable');
AriKernel::import('Data.DataFilter');

class AriQuizControllerSelectquiz extends AriController
{
    function display($cachable = false, $urlparams = array())
    {
        $jInput = JFactory::getApplication()->input;
        $callback = $jInput->request->get('callback', null, 'RAW');
		$ignoreQuizId = $jInput->request->get('ignoreQuizId', null, 'RAW');

        $view = $this->getView();
        $view->displayView($callback, $ignoreQuizId);
    }

    function ajaxGetQuizList()
    {
        $model = $this->getModel('Quizzes');

        $jInput = JFactory::getApplication()->input;

        $filter = new AriDataFilter(
            array(
                'startOffset' => 0,
                'limit' => ARIQUIZ_GRID_PAGESIZE,
                'sortField' => 'QuizName',
                'dir' => 'asc',
                'filter' => $jInput->get('filter', null, 'RAW')
            ),
            true
        );

        $totalCnt = $model->getQuizCount($filter);
        $filter->fixFilter($totalCnt);
        $items = $model->getQuizList($filter);

        $data = AriMultiPageDataTableControl::createDataInfo($items, $filter, $totalCnt);

        return $data;
    }
}