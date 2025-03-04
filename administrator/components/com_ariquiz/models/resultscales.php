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

class AriQuizModelResultscales extends AriModel
{
	function getScaleCount($filter = null)
	{
		$db =& $this->getDBO();

		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquiz_result_scale MT');

		$db->setQuery((string)$query);
		$count = $db->loadResult();

		if (is_null($count))
		{
			return 0;
		}

		return $count;
	}
	
	function getScaleList($filter = null)
	{
		$db =& $this->getDBO();
		
		if (empty($filter))
			$filter = new AriDataFilter();

		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'ScaleId',
				'ScaleName'
			)
		);
		$query->from('#__ariquiz_result_scale');
		$query = $filter->applyToQuery($query);

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));
		$scales = $db->loadObjectList();

		if (is_null($scales))
		{
			return null;
		}
		
		return $scales;
	}
	
	function deleteScale($idList)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
			
		$db =& $this->getDBO();
		$query = sprintf(
			'DELETE
				S,
				SI
			FROM
				#__ariquiz_result_scale S LEFT JOIN #__ariquiz_result_scale_item SI
					ON S.ScaleId = SI.ScaleId
			WHERE
				S.ScaleId IN (%1$s)
			',
			join(',', $idList)
		);
		
		$db->setQuery($query);
		return $db->execute();
	}
}