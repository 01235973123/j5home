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

require_once dirname(__FILE__) . DS . 'texttemplate.php';

class AriQuizTableMailtemplate extends AriTable 
{
	var $MailTemplateId;
	var $Subject;
	var $From;
	var $FromName;
	var $AllowHtml = true;
	var $TextTemplateId;
	var $TextTemplate;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizmailtemplate', 'MailTemplateId', $db);

		$this->TextTemplate = new AriQuizTableTexttemplate($db);

		$this->addRelation('TextTemplateId', 'TextTemplate');
	}
	
	function bind($from, $ignore = array())
	{
		$ignore[] = 'TextTemplate';

		if (parent::bind($from, $ignore) === false)
			return false;

		if ($this->TextTemplate->bind(AriUtils::getParam($from, 'TextTemplate', array()), $ignore) === false)
			return false;

		$this->TextTemplate->TemplateId = $this->TextTemplateId;

		return true;
	}

	function store($updateNulls = null)
	{
		if ($this->isNew()) {
			$this->TextTemplateId = 0;
			if (parent::store($updateNulls) === false) {
				return false;
			}
		}

		$textTemplate =& $this->TextTemplate;
		$textTemplate->TemplateId = $this->TextTemplateId;
		if ($textTemplate->store($updateNulls) === false)
			return false;

		$this->TextTemplateId = $textTemplate->TemplateId;

		return parent::store($updateNulls);
	}
	
	function loadByTextTemplateId($textTemplateId)
	{
		return $this->customLoad(array(&$this, '_loadByTextTemplateId'), array($textTemplateId), 0, 1);
	}
	
	function _loadByTextTemplateId($query, $queryParams, $textTemplateId)
	{
		$tblAlias = $queryParams['tblAlias'];
		
		$query->where(sprintf('%1$s.TextTemplateId = %2$d',
			$tblAlias,
			$textTemplateId));
			
		return $query;
	}
}