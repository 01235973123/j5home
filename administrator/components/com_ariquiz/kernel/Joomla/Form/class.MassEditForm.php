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

AriKernel::import('Joomla.Html.MassEditParameter');
AriKernel::import('Joomla.Form.Validators.MassEditValidatorManager');
AriKernel::import('Joomla.Form.Form');

class AriMassEditForm extends AriForm
{
	function __construct($name, $formType = 'AriMassEditParameter', $validatorManagerType = 'AriMassEditValidatorManager')
	{
		parent::__construct($name, $formType, $validatorManagerType);
	}
}