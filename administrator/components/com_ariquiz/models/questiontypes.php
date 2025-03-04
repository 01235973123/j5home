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

AriKernel::import('Joomla.Models.Model');
AriKernel::import('Joomla.Database.DBUtils');
AriKernel::import('Utils.ArrayHelper');

class AriQuizModelQuestiontypes extends AriModel 
{
	function getQuestionTypeList($forTemplate = false)
	{
		$db =& $this->getDBO();

		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'QuestionTypeId',
				'QuestionType',
				'ClassName'
			)
		);
		$query->from('#__ariquizquestiontype');

		if ($forTemplate)
			$query->where('CanHaveTemplate = 1');

		$query->order('`Default` DESC');
		$query->order('QuestionType ASC');

		$db->setQuery((string)$query);
		$types = $db->loadObjectList();

		return $types;
	}	
}