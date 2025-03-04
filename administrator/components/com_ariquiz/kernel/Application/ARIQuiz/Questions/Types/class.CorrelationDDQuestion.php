<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

require_once dirname(__FILE__) . DS . 'class.CorrelationQuestion.php';

AriKernel::import('Utils.Utils');

class AriQuizQuestionCorrelationDDQuestion extends AriQuizQuestionCorrelationQuestion 
{
	function getFrontXml($questionId)
	{
		$correlation = JRequest::getVar('hidCorrelation_' . $questionId, array(), 'default', 'none', JREQUEST_ALLOWRAW); 
		$correlation = json_decode($correlation);

		$variant = array();
		if (!empty($correlation))
		{
			foreach ($correlation as $item)
			{
				$variant[AriUtils::getParam($item, 'labelId')] = AriUtils::getParam($item, 'answerId');
			}
		}
		
		return $this->_createFrontXml($variant);
	}
}