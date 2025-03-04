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

require_once dirname(__FILE__) . DS . 'texttemplates.php';

class AriQuizModelMailtemplates extends AriModel
{
	var $_group = 'QuizMailResult';

	function getTemplateCount($filter = null)
	{
		$db =& $this->getDBO();

		$query = AriDBUtils::getQuery();
		$query->select('COUNT(MT.MailTemplateId)');
		$query->from('#__ariquizmailtemplate MT');
		$query->innerJoin('#__ariquiz_texttemplate TT ON MT.TextTemplateId = TT.TemplateId');
		$query->where('TT.`Group` = ' . $db->Quote($this->_group));

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
				'MT.MailTemplateId',
				'TT.TemplateName'
			)
		);
		$query->from('#__ariquizmailtemplate MT');
		$query->innerJoin('#__ariquiz_texttemplate TT ON MT.TextTemplateId = TT.TemplateId');
		$query->where('TT.`Group` = ' . $db->Quote($this->_group));
		$query = $filter->applyToQuery($query);

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));
		return $db->loadObjectList();
	}
	
	function deleteTemplate($idList)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
		
		$db =& $this->getDBO();
		
		$query = sprintf('DELETE MT,TT FROM #__ariquizmailtemplate MT INNER JOIN #__ariquiz_texttemplate TT ON MT.TextTemplateId = TT.TemplateId WHERE TT.`Group` = %1$s AND MT.MailTemplateId IN (%2$s)',
			$db->Quote($this->_group),
			join(',', $idList));

		$db->setQuery($query);
		return $db->execute();
	}
}