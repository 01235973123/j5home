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

require_once dirname(__FILE__) . '/../view.php';

AriKernel::import('Joomla.Form.Form');
AriKernel::import('Joomla.Html.GenericParameter');

if (!J4) JHtml::_('behavior.core');

class AriQuizViewSelectquiz extends AriQuizAdminView
{
	function displayView($callback, $ignoreQuizId, $tpl = null)
	{
		$this->setToolbar();

        $filterForm = new AriForm('common', 'AriGenericParameter');
        $filterForm->load(AriQuizHelper::getFormPath('quiz', 'filter'));

        $this->assignRef('filterForm', $filterForm);
        $this->assign('callback', $callback);
        $this->assign('dtQuizzes', $this->_getDataTable($ignoreQuizId));

		parent::display($tpl);
	}

    function _getDataTable($ignoreQuizId)
    {
        AriKernel::import('Web.Controls.Data.MultiPageDataTable');

        $columns = array(
            new AriDataTableControlColumn(
                array(
                    'key' => '',
                    'label' => JText::_('COM_ARIQUIZ_LABEL_NUMPOS'),
                    'formatter' => 'YAHOO.ARISoft.widgets.DataTable.formatters.formatPosition',
                    'className' => 'dtCenter dtColMin'
                )
            ),
            new AriDataTableControlColumn(
                array(
                    'key' => 'QuizId',
                    'label' => JText::_('COM_ARIQUIZ_LABEL_ID'),
                    'sortable' => true,
                    'className' => 'dtCenter dtColMin'
                )
            ),
            new AriDataTableControlColumn(
                array(
                    'key' => 'QuizName',
                    'label' => JText::_('COM_ARIQUIZ_LABEL_NAME'),
                    'sortable' => true,
                    'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatSelectQuiz'
                )
            ),
            new AriDataTableControlColumn(
                array(
                    'key' => 'CategoryName',
                    'label' => JText::_('COM_ARIQUIZ_LABEL_CATEGORY'),
                    'sortable' => true
                )
            ),
        );

        $dataTable = new AriMultiPageDataTableControl(
            'dtQuizzes',
            $columns,
            array(
                'dataUrl' => 'index.php?option=com_ariquiz&view=selectquiz&task=ajaxGetQuizList' . ($ignoreQuizId ? '&filter[IgnoreQuizId]=' . $ignoreQuizId : '')
            ),
            AriQuizHelper::getPaginatorOptions()
        );

        return $dataTable;
    }

    function setToolbar()
    {
        JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUIZZES'), 'categories.png');
    }
}