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

class AriQuizTableTexttemplate extends AriTable 
{
	var $TemplateId;
	var $Group;
	var $TemplateName;
	var $Value;
	var $Created;
	var $CreatedBy = 0;
	var $Modified = null;
	var $ModifiedBy = 0;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquiz_texttemplate', 'TemplateId', $db);
	}
	
	function parse($params = array())
	{
		AriKernel::import('SimpleTemplate.SimpleTemplate');
		
		return $value = AriSimpleTemplate::parse($this->Value, $params);
	}
}