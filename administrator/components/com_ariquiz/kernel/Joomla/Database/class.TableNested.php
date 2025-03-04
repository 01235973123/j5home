<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

require_once dirname(__FILE__) . '/j33/aritablenested.php';

define('ARI_NESTEDTABLE_ROOT', 'root');
	
class AriTableNested extends AriTableNestedInt
{
	var $id = null;
	var $title = null;
	var $access = 1;
	var $path = null;

	function addRoot($extraFields = array())
	{
		$rootId = $this->getRootId();
		if ($rootId !== false)
			return $rootId;

		$extraFieldsSql = array();
		foreach ($extraFields as $fieldName => $fieldValue) {
			$extraFieldsSql[] = sprintf('`%s` = %s', $fieldName, $fieldValue);
		}

    	$db = $this->getDBO();
    	$query = 'INSERT INTO ' . $this->getTableName()
	        . ' SET parent_id = 0'
	        . ', lft = 0'
	        . ', rgt = 0'
	        . ', level = 0'
	        . ', title = ' . $db->quote(ARI_NESTEDTABLE_ROOT)
	        . ', alias = ' . $db->quote(ARI_NESTEDTABLE_ROOT)
	        . ', access = 1'
	        . ', path = '.$db->quote('')
			. (count($extraFieldsSql) > 0 ? ',' . join(',', $extraFieldsSql) : '')
			;
    	$db->setQuery($query);
    	$db->execute();

    	return $db->insertid();
	}
}