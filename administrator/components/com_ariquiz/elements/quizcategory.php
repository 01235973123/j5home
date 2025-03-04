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
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/defines.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/helper.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/categories.php';

AriKernel::import('Data.DataFilter');
AriKernel::import('Xml.XmlHelper');

class JElementQuizcategory extends JElement
{
	var	$_name = 'Quizcategory';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$ignoreCatId = 0;
		$ignoreCatField = AriXmlHelper::getAttribute($node, 'ignore_category_field');
		if ($ignoreCatField)
		{
			$ignoreCatField = $this->_parent->get($ignoreCatField);
			$ignoreCatId = @intval($ignoreCatField, 10);
		}

		$defaultCategoryId = AriQuizHelper::getDefaultCategoryId();
		$categoriesModel =& AriModel::getInstance('Categories', 'AriQuizModel');
		
		$size = intval(AriXmlHelper::getAttribute($node, 'size'), 10);
		$multiple = (bool)AriXmlHelper::getAttribute($node, 'multiple');
		$ignoreRoot = !((bool)AriXmlHelper::getAttribute($node, 'include_root'));

		$catFilter = array();
		if ($ignoreCatId > 0)
			$catFilter['IgnoreCategoryId'] = $ignoreCatId;
		
		$filter = new AriDataFilter(
			array(
				'sortField' => 'lft', 
				'dir' => ARI_DATAFILTER_SORT_ASC,
				'filter' => $catFilter
			)
		);

		$categories = $categoriesModel->getCategoryList($filter, $ignoreRoot);
		if (is_array($categories))
			for ($i = 0; $i < count($categories); $i++)
			{
				if ($categories[$i]->level == 0 && empty($categories[$i]->CategoryName))
				{
					$categories[$i]->CategoryName = JText::_(AriXmlHelper::getAttribute($node, 'root_lbl'));
				}					
				else
					$categories[$i]->CategoryName = str_repeat('- ', $categories[$i]->level - ($ignoreRoot ? 1 : 0)) . $categories[$i]->CategoryName;
			}

		if (!$multiple)
		{
			$noneLabel = AriXmlHelper::getAttribute($node, 'none_label');
			if ($noneLabel)
			{
				$emptyCat = new stdClass();
				$emptyCat->CategoryId = 0;
				$emptyCat->CategoryName = JText::_($noneLabel);
				array_unshift($categories, $emptyCat);
			}
			else if (empty($value))
				$value = $defaultCategoryId; 
		}

		return JHTML::_(
			'select.genericlist', 
			$categories, 
			$control_name . '[' . $name . ']' . ($multiple ? '[]' : ''), 
			'class="inputbox form-select"' . ($multiple ? ' multiple="multiple"' . ($size ? ' size="' . $size . '"' : '') : ''), 
			'CategoryId', 
			'CategoryName', 
			$value,
			$control_name . $name);		
	}
}