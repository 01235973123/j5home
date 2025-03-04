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

require_once dirname(__FILE__) . DS . 'texttemplate.php';

class AriQuizModelMailtemplate extends AriModel 
{
	var $_group = 'QuizMailResult';
	
	function getTemplate($templateId, $strictLoad = true)
	{
		if ($strictLoad && $templateId < 1)
			return null;

		$template =& $this->getTable();
		$template->load($templateId);

		if ($strictLoad && empty($template->MailTemplateId))
			$template = null;

		return $template;
	}
	
	function getTemplateByTextTemplateId($textTemplateId)
	{
		if ($textTemplateId < 1)
			return null;

		$template =& $this->getTable();
		if (!$template->loadByTextTemplateId($textTemplateId))
			$template = null;

		return $template;
	}
	
	function saveTemplate($data)
	{
		$template =& $this->getTable();
		$template->bind($data);
		$template->TextTemplate->Group = $this->_group;

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
		$query->select('COUNT(MT.*)');
		$query->from('#__ariquizmailtemplate MT');
		$query->innerJoin('#__ariquiz_texttemplate TT ON MT.TextTemplateId = TT.TemplateId');
		$query->where('TT.`Group` = ' . $db->Quote($this->_group));
		$query->where('TT.TemplateName = ' . $db->Quote($name));
		$query->where('TT.Group = ' . $db->Quote($this->_group));
		if ($id)
			$query->where('MT.MailTemplateId <> ' . intval($id, 10));
			
		$db->setQuery((string)$query);
			
		$isUnique = $db->loadResult();
		if (is_null($isUnique))
		{
			return null;
		}
		
		return ($isUnique == 0);
	}
}