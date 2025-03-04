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

AriKernel::import('Joomla.Tables.Table');
AriKernel::import('Utils.Utils');

define('ARIQUIZ_RESULTSCALE_TYPE_PERCENT', 'Percent');
define('ARIQUIZ_RESULTSCALE_TYPE_SCORE', 'Score');

require_once dirname(__FILE__) . DS . 'resultscaleitem.php';

class AriQuizTableResultscale extends AriTable 
{
	var $ScaleId = null;
	var $ScaleName = '';
	var $Created;
	var $CreatedBy = 0;
	var $ModifiedBy = 0;
	var $Modified = null;
	var $ScaleItems = array();
    var $ScaleType = ARIQUIZ_RESULTSCALE_TYPE_PERCENT;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquiz_result_scale', 'ScaleId', $db);
		
		$this->addRelation('ScaleId', 'ScaleItems', ARI_TABLE_RELATION_ONETOMANY, 'AriQuizTableResultscaleitem', 'ScaleId');
	}

	function bind($from, $ignore = array())
	{
		$ignore[] = 'ScaleItems';

		if (parent::bind($from, $ignore) === false)
			return false;

		$scaleItems = AriUtils::getParam($from, 'ScaleItems', array());
		if (!is_array($scaleItems))
			$scaleItems = array($scaleItems);
			
		foreach ($scaleItems as $scaleItem)
		{
			if ((!isset($scaleItem['StartPoint']) || is_null($scaleItem['StartPoint']) || strlen($scaleItem['StartPoint']) == 0) &&
				(!isset($scaleItem['EndPoint']) || is_null($scaleItem['EndPoint']) || strlen($scaleItem['EndPoint']) == 0))
				continue ;
			
			$item = new AriQuizTableResultscaleitem($this->getDBO());
			if ($item->bind($scaleItem) !== false)
			{
				$item->ScaleId = $this->ScaleId;
				$this->ScaleItems[] = $item;
			}
		}

		return true;
	}

	function store($updateNulls = null)
	{	
		if (!$this->isNew())
		{
			$db =& $this->getDBO();
			$db->setQuery(
				sprintf('DELETE FROM #__ariquiz_result_scale_item WHERE ScaleId = %d',
					$this->ScaleId)
			);
			if (!$db->execute())
				return false;
		}
		
		if (parent::store($updateNulls) === false)
			return false;

		$scaleItems =& $this->ScaleItems;
		foreach ($scaleItems as $scaleItem)
		{
			$scaleItem->ScaleId = $this->ScaleId;
			$scaleItem->store($updateNulls);
		}

		return true;
	}
	
	function load($keys = null, $reset = true)
	{
		$result = parent::load($keys, $reset);
		
		if (is_array($this->ScaleItems) && count($this->ScaleItems) > 0)
		{
			$sort = new AriSortUtils('BeginPoint', 'asc');
			usort($this->ScaleItems, array(&$sort, 'sort'));
		}

		return $result;
	}
}