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

class AriQuizTableFileVersion extends AriTable 
{
	var $FileVersionId;
	var $FileId;
	var $Created;
	var $CreatedBy;
	var $FileName = '';
	var $FileSize;
	var $Params;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquiz_file_versions', 'FileVersionId', $db);
	}
}