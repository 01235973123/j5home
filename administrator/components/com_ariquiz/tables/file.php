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

define('ARIQUIZ_FILE_STATUS_ACTIVE', 1);
define('ARIQUIZ_FILE_STATUS_DELETE', 2);

require_once dirname(__FILE__) . DS . 'fileversion.php';

class AriQuizTableFile extends AriTable 
{
	var $FileId;
	var $FileVersionId = null;
	var $OriginalName;
	var $Created;
	var $CreatedBy;
	var $ModifiedBy = 0;
	var $Modified = null;
	var $MimeType;
	var $FolderId;
	var $Group;
	var $Status = ARIQUIZ_FILE_STATUS_ACTIVE;
	var $FileVersion;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquiz_file', 'FileId', $db);

		$this->FileVersion = new AriQuizTableFileVersion($db);

		$this->addRelation('FileVersionId', 'FileVersion');
	}

	function loadByName($name, $group, $folderId)
	{
		return $this->customLoad(array(&$this, '_loadByName'), array($name, $group, $folderId), 0, 1);
	}
	
	function _loadByName($query, $queryParams, $name, $group, $folderId)
	{
		$db =& $this->getDBO();
		$tblAlias = $queryParams['tblAlias'];
		
		$query->where($tblAlias . '.OriginalName = ' . $db->Quote($name));
		$query->where($tblAlias . '.Group = ' . $db->Quote($group));
		$query->where($tblAlias . '.FolderId = ' . $db->Quote($folderId));

		return $query;
	}
	
	function bind($from, $ignore = array())
	{
		$ignore[] = 'FileVersion';

		if (parent::bind($from, $ignore) === false)
			return false;

		if ($this->FileVersion->bind(AriUtils::getParam($from, 'FileVersion', array()), $ignore) === false)
			return false;

		$this->FileVersion->FileId = $this->FileId;
		$this->FileVersionId = $this->FileVersion->FileVersionId;

		return true;
	}

	function store($updateNulls = null)
	{
		$fileVersion =& $this->FileVersion;
		if (is_null($fileVersion) || empty($fileVersion->FileVersionId))
		{
			return false;
		}
		
		$this->FileVersionId = $fileVersion->FileVersionId;
		if (parent::store($updateNulls) === false)
			return false;

		$fileVersion->FileId = $this->FileId;
		if ($fileVersion->store($updateNulls) === false)
			return false;
			
		return true;
	}
}