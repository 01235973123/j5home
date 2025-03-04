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

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldQuizcategory extends JFormField
{
	protected $type = 'Quizcategory';
	
	function getInput()
	{
		return $this->fetchElement(
			$this->element['name'], 
			$this->value, 
			$this->element, 
			$this->name
		);
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		$categoriesModel =& AriModel::getInstance('Categories', 'AriQuizModel');
		
		$size = intval(AriXmlHelper::getAttribute($node, 'size'), 10);
		$multiple = (bool)AriXmlHelper::getAttribute($node, 'multiple');
		$ignoreRoot = !((bool)AriXmlHelper::getAttribute($node, 'include_root'));

		$filter = new AriDataFilter(
			array(
				'sortField' => 'lft', 
				'dir' => ARI_DATAFILTER_SORT_ASC,
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
			if (empty($value))
				$value = AriQuizHelper::getDefaultCategoryId();
			/*
			$noneLabel = $node->attributes('none_label') ? (string)$node->attributes('none_label') : 'COM_ARIQUIZ_LABEL_NONE';
			
			$emptyCat = new stdClass();
			$emptyCat->CategoryId = 0;
			$emptyCat->CategoryName = JText::_($noneLabel);
			array_unshift($categories, $emptyCat);
			*/
		}

		return JHTML::_(
			'select.genericlist', 
			$categories, 
			$control_name . ($multiple ? '[]' : ''), 
			'class="inputbox"' . ($multiple ? ' multiple="multiple"' . ($size ? ' size="' . $size . '"' : '') : ''), 
			'CategoryId', 
			'CategoryName', 
			$value,
			$control_name . $name);		
	}
}