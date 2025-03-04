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
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/bankcategories.php';

AriKernel::import('Data.DataFilter');
AriKernel::import('Xml.XmlHelper');

class JElementBankcategory extends JElement
{
	var	$_name = 'Bankcategory';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$defaultCategoryId = AriQuizHelper::getDefaultBankCategoryId();
		$categoriesModel =& AriModel::getInstance('Bankcategories', 'AriQuizModel');

		$filter = new AriDataFilter(
			array(
				'sortField' => 'CategoryName', 
				'dir' => ARI_DATAFILTER_SORT_ASC
			)
		);

		$categories = $categoriesModel->getCategoryList($filter);

		$class = AriXmlHelper::getAttribute($node, 'class', '');

		$noneLabel = AriXmlHelper::getAttribute($node, 'none_label', '');		
		if ($noneLabel)
		{
			$emptyCat = new stdClass();
			$emptyCat->CategoryId = 0;
			$emptyCat->CategoryName = JText::_($noneLabel);
			array_unshift($categories, $emptyCat);
		}
		else if (empty($value))
				$value = $defaultCategoryId;

		return JHTML::_(
			'select.genericlist', 
			$categories, 
			$control_name . '[' . $name . ']', 
			'class="inputbox form-select' . ($class ? ' ' . $class : '') . '"', 
			'CategoryId',
			'CategoryName', 
			$value,
			$control_name . $name);		
	}
}