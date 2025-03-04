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

require_once dirname(__FILE__) . DS . 'questiontype.php';

class AriQuizTableQuestionTemplate extends AriTable
{
	var $TemplateId;
	var $TemplateName;
	var $QuestionTypeId;
	var $Data;
	var $Created;
	var $CreatedBy;
	var $Modified = null;
	var $ModifiedBy = 0;
	var $DisableValidation = 0;
	
	var $QuestionType;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizquestiontemplate', 'TemplateId', $db);
		
		$this->QuestionType = new AriQuizTableQuestionType($db);
	}
}