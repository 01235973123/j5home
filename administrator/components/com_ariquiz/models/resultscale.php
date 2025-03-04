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

class AriQuizModelResultscale extends AriModel 
{
	function __construct()
	{
		parent::__construct();

        // import constants
        $this->getTable('resultscale');
    }

    function getScale($scaleId, $strictLoad = true)
	{
		if ($strictLoad && $scaleId < 1)
			return null;

		$scale =& $this->getTable();
		$scale->load($scaleId);

		if ($strictLoad && empty($scale->ScaleId))
			$scale = null;

		return $scale;
	}

	function getScaleItemByScore($scaleId, $percentScore, $score)
	{
        $scale = $this->getScale($scaleId, false);
        $scaleScore = $scale->ScaleType != ARIQUIZ_RESULTSCALE_TYPE_SCORE ? $percentScore : $score;

		$scaleItem = $this->getTable('Resultscaleitem');
		if (!$scaleItem->loadByScore($scaleId, $scaleScore))
			$scaleItem = null;

		return $scaleItem;
	}

	function saveScale($data)
	{
		$scale =& $this->getTable();
		$scale->bind($data);

		if (!$scale->store())
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_SQL_QUERY', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$scale->getQuery(), 
					$scale->getError()
				),
				500
			);
			return null;
		}

		return $scale;
	}
	
	function isUniqueScaleName($name, $id = null)
	{
		$db =& $this->getDBO();

		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquiz_result_scale');

		$query->where('ScaleName = ' . $db->Quote($name));
		if ($id)
			$query->where('ScaleId <> ' . intval($id, 10));

		$db->setQuery((string)$query);

		$isUnique = $db->loadResult();
		if (is_null($isUnique))
		{
			return null;
		}
		
		return ($isUnique == 0);
	}
}