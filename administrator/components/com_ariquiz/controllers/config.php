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

class AriQuizControllerConfig extends AriController 
{
	function display($cachable = false, $urlparams = []) 
	{
		$config = AriQuizHelper::getConfig();

		$view =& $this->getView();
		$view->displayView($config, JRequest::getInt('quizActiveTab'));
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');

        $activeTab = JRequest::getInt('quizActiveTab');

		$this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=config' . ($activeTab ? '&quizActiveTab=' . $activeTab : '') . '&__MSG=COM_ARIQUIZ_COMPLETE_CONFIGSAVE');
	}

	function _save()
	{
		if (!AriQuizHelper::isAuthorise('core.admin'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
			$this->redirect('index.php?option=com_ariquiz&view=config');
		}

		$data = JRequest::getVar('params', null, 'default', 'none', JREQUEST_ALLOWRAW);

		$this->updateApiKey($data['ApiKey']);

		unset($data['DefaultCategoryId']);
		unset($data['DefaultBankCategoryId']);
		unset($data['Version']);
		unset($data['FilesPath']);
		unset($data['HelpPath']);

		$config = AriQuizHelper::getConfig();
		$group = $config->getGroups();
		$config->bind($data, $group);
		$config->save(true, $group);
	}

	private function updateApiKey($apiKey) {
		$db = Joomla\CMS\Factory::getDbo();
		$db->setQuery(
			sprintf(
				'UPDATE #__update_sites US INNER JOIN #__update_sites_extensions US_E ON US.update_site_id = US_E.update_site_id INNER JOIN #__extensions E ON US_E.extension_id = E.extension_id SET US.extra_query = %2$s WHERE E.element = %1$s',
				$db->quote('com_ariquiz'),
				!empty($apiKey) ? $db->quote(sprintf('api_key=%s', $apiKey)) : $db->quote('')
			)
		);
		$db->execute();
	}
}