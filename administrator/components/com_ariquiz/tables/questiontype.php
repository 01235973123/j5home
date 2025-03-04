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

class AriQuizTableQuestionType extends AriTable 
{	
	var $QuestionTypeId;
	var $QuestionType;
	var $ClassName;
	var $CanHaveTemplate = 1;
	var $TypeOrder = 0;
	var $Default;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizquestiontype', 'QuestionTypeId', $db);
	}
}