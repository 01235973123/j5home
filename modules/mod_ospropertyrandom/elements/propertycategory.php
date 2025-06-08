<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
/*------------------------------------------------------------------------
# propertycategory.php - mod_ospropertyrandom
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2010 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
error_reporting(0);
define('DS',DIRECTORY_SEPARATOR);
global $mainframe;
$mainframe = Factory::getApplication();

include_once(JPATH_ADMINISTRATOR."/components/com_osproperty/classes/property.php");

jimport('joomla.form.formfield');
class JFormFieldPropertycategory extends FormField
{
	var	$_name = 'Propertycategory';
	function getInput()
	{
		$db = Factory::getDBO();
		$query = 'SELECT id, parent_id AS parent, category_name AS name' .
				 ' FROM #__osrs_categories ' .
				 ' WHERE published = 1' .
			 	 ' ORDER BY parent_id, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();

		// establish the hierarchy of the menu
		$children = array();

		if ( $mitems )
		{
			// first pass - collect children
			foreach ( $mitems as $v )
			{
				$pt 	= $v->parent;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		// assemble menu items to the array
		$parentArr 	= array();
		$parentArr[] 	= JHTML::_('select.option',  '', Text::_( 'OSPROPERTY_SELECT_CATEGORY' ) );
		foreach ( $list as $item ) {
			if($item->treename != ""){
				$item->treename = str_replace("&nbsp;","",$item->treename);
			}
			$var = explode("-",$item->treename);
			$treename = "";
			for($i=0;$i<count($var)-1;$i++){
				$treename .= " - ";
			}
			if($treename == ""){
				$treename = str_replace("&#160;","-",$item->treename);
			}
			$text = $treename.$item->name;
			$parentArr[] = JHTML::_('select.option',  $item->id,$text);
		}
		 
	    return JHTML::_('select.genericlist',  $parentArr, $this->name, 'class= "input-large form-control form-select"', 'value', 'text', $this->value, $control_name.$name );
	}
}
	
