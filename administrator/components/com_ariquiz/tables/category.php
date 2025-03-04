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

jimport('joomla.utilities.arrayhelper');
AriKernel::import('Utils.ArrayHelper');
AriKernel::import('Utils.DateUtility');
AriKernel::import('Joomla.Database.TableNested');

class AriQuizTableCategory extends AriTableNested
{
	var $CategoryId;
	var $CategoryName;
	var $Description = '';
	var $CreatedBy;
	var $Created;
	var $ModifiedBy = 0;
	var $Modified = null;
	var $Metadata = null;
	
	var $asset_id = 0;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizcategory', 'CategoryId', $db);
	}
	
	function getMetaParam($name, $defValue = null)
	{
		return isset($this->Metadata->$name) ? $this->Metadata->$name : $defValue;
	}
	
	function bind($array, $ignore = '') 
	{
		if (isset($array['rules']) && (is_array($array['rules']) || is_object($array['rules'])))
		{
			if (is_object($array['rules']))
				$array['rules'] = Joomla\Utilities\ArrayHelper::fromObject($array['rules']);

			$array['rules'] = AriArrayHelper::removeEmptyValues($array['rules']);
			$rules = new JAccessRules($array['rules']);

			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}
	
	function store($updateNulls = false)
	{
		$now = AriDateUtility::getDbUtcDate();
		$userId = JFactory::getUser()->get('id');

		if (empty($this->parent_id))
		{
			$db = $this->getDBO();
			$rootCategory = JTable::getInstance('category', 'AriQuizTable');
			$rootCategoryId = $rootCategory->addRoot(array(
				'CategoryName' => $db->quote(''),
				'Description' => $db->quote(''),
				'Created' => $db->quote($now),
				'CreatedBy' => $userId,
				'Metadata' => $db->quote(''),
			));
			if ($rootCategoryId === false)
				return false;
			
			$this->parent_id = $rootCategoryId;
		}
		
		$parentId = $this->parent_id;
		
		$this->setLocation($parentId, 'last-child');

		$this->title = $this->CategoryName;
		$this->alias = $this->CategoryName;
		$this->Metadata = $this->Metadata ? json_encode($this->Metadata) : '';
		$this->Created = $now;
		$this->CreatedBy = $userId;
		$this->path = '';

		return parent::store($updateNulls);
	}
	
	function load($oid = null, $reset = true)
	{
		$result = parent::load($oid, $reset);

		if (!$result)
			return $result;
			
		if ($this->Metadata)
			$this->Metadata = json_decode($this->Metadata);

		return $result;
	}
	
	protected function _getAssetName()
	{
		$key = $this->_tbl_key;
		
		return 'com_ariquiz.category.'. (int)$this->$key;        
	}

 	protected function _getAssetTitle()
 	{
 		return $this->CategoryName;
 	}

	protected function _getAssetParentId($table = null, $id = null)
 	{                
		$assetParent = JTable::getInstance('Asset');

 		$assetParentId = $assetParent->getRootId();
 		if (empty($this->lft) || empty($this->parent_id))
 		{                
 			$assetParent->loadByName('com_ariquiz');
 		}
 		else
 		{
 			$assetParent->loadByName('com_ariquiz.category.' . ($this->parent_id));
 		}

		if ($assetParent->id)
			$assetParentId = $assetParent->id;

		return $assetParentId;
	}
}