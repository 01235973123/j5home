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

class AriQuizModelQuestiontemplates extends AriModel 
{	
	function getTemplateCount($filter = null)
	{
		$db =& $this->getDBO();
		
		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquizquestiontemplate QQT');
		$query->innerJoin('#__ariquizquestiontype QQTY ON QQT.QuestionTypeId = QQTY.QuestionTypeId');

		$db->setQuery((string)$query);
		$count = $db->loadResult();

		if (is_null($count))
		{
			return 0;
		}

		return $count;		
	}
	
	function getTemplateList($filter = null)
	{
		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		
		$query->select(
			array(
				'QQT.QuestionTypeId',
				'QQT.TemplateName',
				'QQT.TemplateId',
				'QQT.Created',
				'QQT.Modified',
				'QQTY.QuestionType'
			)
		);
		$query->from('#__ariquizquestiontemplate QQT');
		
		$query->innerJoin('#__ariquizquestiontype QQTY ON QQT.QuestionTypeId = QQTY.QuestionTypeId');

		if ($filter)
			$query = $filter->applyToQuery($query);		

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), null/*$filter->getConfigValue('limit')*/);

		return $db->loadObjectList();
	}

	function deleteTemplate($idList)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
		
		$db =& $this->getDBO();
		
		$query = sprintf('DELETE FROM #__ariquizquestiontemplate WHERE TemplateId IN (%s)', 
			join(',', $idList));
			
		$db->setQuery($query);
		return $db->execute();
	}
}