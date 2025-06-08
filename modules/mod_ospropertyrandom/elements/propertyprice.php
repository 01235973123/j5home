<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
/*------------------------------------------------------------------------
# propertyprice.php - mod_ospropertyrandom
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2010 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');



class JElementPropertyprice extends JElement
{
	var	$_name = 'Propertyprice';

	function fetchElement($name, $value, &$node, $control_name)
	{
		
		$db = Factory::getDBO();
		$db->setQuery("SELECT `price`, `display_price` FROM #__osrs_pricegroups WHERE `published` = 1 ORDER BY price ASC");		
		$prices = $db->loadObjectList();
		$option_p = array();
		$option_p[] = JHTML::_("select.option",'',Text::_('OSPROPERTY_SELECT_PRICE'));
		if (count($prices)){
			$option_p[] = JHTML::_("select.option",'< '.$prices[0]->price,'< '.$prices[0]->display_price);			
			for ($i=1; $i <count($prices); $i++){
				$option_p[] = JHTML::_("select.option",$prices[$i-1]->price.' - '.$prices[$i]->price ,$prices[$i-1]->display_price.' - '.$prices[$i]->display_price);
			}
			if ($i > 1){
				$option_p[] = JHTML::_("select.option",'> '.$prices[$i-1]->price,'> '.$prices[$i-1]->display_price);
			}		
		}
	    return JHTML::_('select.genericlist',  $option_p, ''.$control_name.'['.$name.']', 'class=""input-large form-control form-select""', 'value', 'text', $value, $control_name.$name );
	}
}
