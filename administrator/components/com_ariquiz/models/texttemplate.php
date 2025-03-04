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
AriKernel::import('Utils.ArrayHelper');

class AriQuizModelTexttemplate extends AriModel 
{
	var $_group = '';
	
	function __construct($config = array())
	{ 
		if (array_key_exists('group', $config))
			$this->_group = $config['group'];

		parent::__construct($config);
	}

	function getTemplate($templateId, $strictLoad = true)
	{
		if ($strictLoad && $templateId < 1)
			return null;

		$template =& $this->getTable();
		$template->load($templateId);

		if ($strictLoad && empty($template->TemplateId))
			$template = null;

		return $template;
	}
	
	function saveTemplate($data)
	{
		$template =& $this->getTable();
		$template->bind($data);
		$template->Group = $this->_group;

		if (!$template->store())
		{
			return null;
		}

		return $template;
	}

	function isUniqueTemplateName($name, $group, $id = null)
	{
		$db =& $this->getDBO();
		
		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquiz_texttemplate');
		
		$query->where('TemplateName = ' . $db->Quote($name));
		$query->where('`Group` = ' . $db->Quote($group));
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