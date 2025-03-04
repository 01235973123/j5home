<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die ('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/questiontypes.php';

AriKernel::import('Data.DataFilter');
AriKernel::import('Xml.XmlHelper');

class JElementQuestiontype extends JElement
{
	var	$_name = 'Questiontype';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$disabled = (bool)AriXmlHelper::getAttribute($node, 'disabled');
		$cssClass = AriXmlHelper::getAttribute($node, 'css_class');

		$qtModel =& AriModel::getInstance('Questiontypes', 'AriQuizModel');
		$questionTypes = $qtModel->getQuestionTypeList();
		
		if (is_array($questionTypes))
		{
			foreach ($questionTypes as $i => $questionType)
			{
				$translateKey = 'COM_ARIQUIZ_QUESTIONTYPE_' . strtoupper($questionType->ClassName);
				$translated = JText::_($translateKey);
				if ($translateKey != $translated)
					$questionTypes[$i]->QuestionType = $translated;
			}
		}

		return JHTML::_(
			'select.genericlist', 
			$questionTypes, 
			$control_name . '[' . $name . ']', 
			'class="inputbox form-select' . ($cssClass ? ' ' . $cssClass : '') . '"' . ($disabled ? ' disabled="disabled"' : ''), 
			'QuestionTypeId', 
			'QuestionType', 
			$value,
			$control_name . $name);
	}
}