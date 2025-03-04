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
AriKernel::import('Joomla.Database.DatabaseQuery');

class AriQuizModelQuestiontemplate extends AriModel 
{
	function getTemplate($templateId, $strictLoad = true) 
	{
		if ($strictLoad && $templateId < 1)
			return null;

		$template =& $this->getTable();
		$template->load($templateId);

		if (($strictLoad && empty($template->TemplateName)))
			$template = null;

		return $template;
	}

	function saveTemplate($data)
	{
		$template =& $this->getTable();
		$template->bind($data);

		if (!$template->store())
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_SQL_QUERY', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$template->getQuery(), 
					$template->getError()
				),
				500
			);
			return null;
		}
		
		return $template;
	}
	
	function isUniqueTemplateName($name, $id = null)
	{
		$db =& $this->getDBO();
		
		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquizquestiontemplate');
		
		$query->where('TemplateName = ' . $db->Quote($name));
		if ($id)
			$query->where('TemplateId <> ' . intval($id, 10));
			
		$db->setQuery((string)$query);
			
		$isUnique = $db->loadResult();
		if (is_null($isUnique))
		{
			return null;
		}
		
		return ($isUnique == 0);
	}
}