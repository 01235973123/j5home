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

class AriQuizTableBankcategory extends AriTable
{
	var $CategoryId;
	var $CategoryName;
	var $Description = '';
	var $CreatedBy;
	var $Created;
	var $ModifiedBy = 0;
	var $Modified = null;
	
	var $asset_id = 0;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizbankcategory', 'CategoryId', $db);
	}
	
	protected function _getAssetName()
	{
		$key = $this->_tbl_key;
		
		return 'com_ariquiz.bankcategory.'. (int)$this->$key;        
	}

 	protected function _getAssetTitle()
 	{
 		return $this->CategoryName;
 	}

	protected function _getAssetParentId($table = null, $id = null)
 	{                
		$assetParent = JTable::getInstance('Asset');

 		$assetParentId = $assetParent->getRootId();                
 		$assetParent->loadByName('com_ariquiz');

		if ($assetParent->id)
			$assetParentId = $assetParent->id;

		return $assetParentId;
	}
}