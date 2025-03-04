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

AriKernel::import('Joomla.Form.Validators.ValidatorManager');
AriKernel::import('Xml.XmlHelper');

class AriMassEditValidatorManager extends AriValidatorManager
{
	var $_massEditKey = 'massedit';
	
	function isAcceptableValidator($node)
	{
		if (empty($node))
			return false;
	
		$isMassEdit = AriXmlHelper::getAttribute($node, $this->_massEditKey);
			
		return !empty($isMassEdit);
	}
}