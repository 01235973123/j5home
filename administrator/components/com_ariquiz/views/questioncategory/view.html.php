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

AriKernel::import('Web.Controls.Advanced.MultiplierControls');

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

class AriQuizViewQuestioncategory extends AriQuizAdminView 
{
	function displayView($category, $activeTab = 0, $tpl = null) 
	{		
		$this->setToolbar();

		AriKernel::import('Joomla.Form.Form');

		$commonSettingsForm = new AriForm('commonSettings');
		$commonSettingsForm->load(AriQuizHelper::getFormPath('questioncategory', 'category'));
		$commonSettingsForm->bind($category, array('_default', 'rules'));
		
		$questionPoolForm = new AriForm('questionPool');
		$questionPoolForm->load(AriQuizHelper::getFormPath('questioncategory', 'questionpool'));
		
		$quizId = JRequest::getInt('quizId');

		$this->assign('activeTab', $activeTab);
		$this->assign('quizId', $quizId);
		$this->assignRef('questionPool', $category->QuestionPool);
		$this->assignRef('commonSettingsForm', $commonSettingsForm);
		$this->assignRef('questionPoolForm', $questionPoolForm);

		parent::display($tpl);
		
		$this->addScript(JURI::root(true) . '/administrator/components/com_ariquiz/assets/js/ari.multiplierControls.js');
	}
	
	function setToolbar() 
	{
		$this->disableMainMenu();
		$id = JRequest::getInt('categoryId');
		$edit = ($id > 0);

		$text = ($edit ? JText::_('COM_ARIQUIZ_LABEL_EDIT') : JText::_('COM_ARIQUIZ_LABEL_NEW'));

		JToolBarHelper::title(JText::_('COM_ARIQUIZ_LABEL_QUESTIONCATEGORY') . ': <small><small>[ ' . $text . ' ]</small></small>', 'categories.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();

		if ($edit) 
			JToolBarHelper::cancel('cancel', JText::_('Close'));
		else 
			JToolBarHelper::cancel();

		JToolBarHelper::divider();
		AriQuizToolbarHelper::ariQuizHelp('Questioncategories2.html');
	}
	
	function getQuestionPoolData($questionPool)
	{
		$poolData = array();
		if (!empty($questionPool))
		{
			foreach ($questionPool as $questionPoolItem)
			{
				$poolData[] = array(
					'poolParamsBankCategoryId' => $questionPoolItem->BankCategoryId,
					'poolParamsQuestionCount' => $questionPoolItem->QuestionCount
				);
			}
		}

		return $poolData;
	}
}