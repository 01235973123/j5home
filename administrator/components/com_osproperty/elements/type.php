<?php
use Joomla\CMS\Form\FormField;
/*------------------------------------------------------------------------
# type.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

global $mainframe;
$mainframe = Factory::getApplication();
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
class JFormFieldType extends FormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	public	$_name = 'type';
	
	protected function getInput()
	{    
		if ($this->element['value'] > 0) {
    	    $selectedValue = (int) $this->element['value'] ;
    	} else {
    	    $selectedValue = (int) $this->value ;
    	} 
		$typeArr[] = HTMLHelper::_('select.option','','Select property type');
       	$db = Factory::getDbo();
       	$db->setQuery("Select id as value, type_name as text from #__osrs_types where published = '1' order by ordering");
       	$typeObjects = $db->loadObjectList();
       	$typeArr = array_merge($typeArr,$typeObjects);
		return HTMLHelper::_('select.genericlist',$typeArr, $this->name, array(
		    'option.text.toHtml' => false ,
		    'option.value' => 'value', 
		    'option.text' => 'text', 
		    'list.attr' => ' class="input-large form-select" ',
		    'list.select' => $selectedValue  		        		
		));	
	}
}
