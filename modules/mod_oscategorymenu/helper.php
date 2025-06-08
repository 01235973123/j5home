<?php
use Joomla\CMS\Factory;
/*------------------------------------------------------------------------
# helper.php - mod_oscategorymenu
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2010 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class modOspropertyCategoryMenuHelper
{
	static function osGetCategories($parent_id = 0, $params) {
		global $mainframe;
        $db     = Factory::getDBO();
        $user   = Factory::getUser();
        $gid    = $user->get('gid');
		$today  =  JHTML::_('date', 'now', "%Y-%m-%d");
		//$parent=intval($parent);
		$parentfilter = is_numeric($parent_id) ? " AND parent_id = ".(int)$parent_id : "";
        $orderfilter  = $params->get('ordering', 'ordering');


		$query =  "SELECT *, id AS category_id, category_name FROM #__osrs_categories "
				. "\n WHERE published = '1'"
                . $parentfilter
                . " and `access` IN (" . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ")"
                . "\n ORDER BY ".$orderfilter." ASC";
        $db->setQuery($query);
        return $db->loadObjectList();
	}
	
	/**
	 * Count properties of the category
	 *
	 */
	static function countProperties($id,&$total){
		global $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Select count(id) from #__osrs_properties where approved = '1' and published = '1' and id in ( Select pid from #__osrs_property_categories where category_id = '$id')");
		$count = $db->loadResult();
		$total += $count;
		//echo $total;
		$db->setQuery("Select * from #__osrs_categories where parent_id = '$id'");
		$categories = $db->loadObjectList();
		for($i=0;$i<count($categories);$i++){
			$cat = $categories[$i];
			$total = modOspropertyCategoryMenuHelper::countProperties($cat->id,$total);
		}
		return $total;
	}
}

?>
