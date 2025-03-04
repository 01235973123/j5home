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
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/resultscales.php';

AriKernel::import('Data.DataFilter');

class JElementResultscale extends JElement
{
	var	$_name = 'Resultscale';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$scalesModel =& AriModel::getInstance('Resultscales', 'AriQuizModel');

		$filter = new AriDataFilter(
			array(
				'sortField' => 'ScaleName', 
				'dir' => ARI_DATAFILTER_SORT_ASC
			)
		);

		$scales = $scalesModel->getScaleList($filter);

		return JHTML::_(
			'select.genericlist', 
			$scales, 
			$control_name . '[' . $name . ']', 
			'class="inputbox form-select"', 
			'ScaleId', 
			'ScaleName', 
			$value,
			$control_name . $name);		
	}
}