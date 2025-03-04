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

class AriQuizModelTexttemplates extends AriModel 
{
	var $_group = '';
	
	function getTemplateCount($filter = null)
	{
		$db =& $this->getDBO();

		$query = AriDBUtils::getQuery();
		$query->select('COUNT(TemplateId)');
		$query->from('#__ariquiz_texttemplate');
		$query->where('`Group` = ' . $db->Quote($this->_group));

		$db->setQuery((string)$query);
		$count = $db->loadResult();

		if (is_null($count))
		{
			return 0;
		}

		return $count;
	}
	
	function getTemplateList($filter = null, $group = null)
	{
		$db =& $this->getDBO();
		
		if (is_null($group))
			$group = $this->_group;

		if (empty($filter))
			$filter = new AriDataFilter();

		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'TemplateId',
				'TemplateName'
			)
		);
		$query->from('#__ariquiz_texttemplate');
		$query->where('`Group` = ' . $db->Quote($group));
		$query = $filter->applyToQuery($query);

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));
		$templates = $db->loadObjectList();

		if (is_null($templates))
		{
			return null;
		}
		
		return $templates;
	}
	
	function deleteTemplate($idList)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
		
		$db =& $this->getDBO();
		
		$query = sprintf('DELETE FROM #__ariquiz_texttemplate WHERE `Group` = %1$s AND TemplateId IN (%2$s)',
			$db->Quote($this->_group),
			join(',', $idList));
			
		$db->setQuery($query);
		return $db->execute();
	}
}