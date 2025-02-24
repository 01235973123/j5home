<?php
use Joomla\CMS\Form\FormField;
/*------------------------------------------------------------------------
# category.php - Ossolution Property
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
error_reporting(E_ERROR||E_PARSE);
$mainframe = Factory::getApplication();
define('DS', DIRECTORY_SEPARATOR);
include_once(JPATH_ADMINISTRATOR."/components/com_osproperty/classes/property.php");

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldOsCategory extends FormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	public	$_name = 'oscategory';
	
	protected function getInput()
	{    
		//print_r($this->element['value']);
		//die();
		if ($this->element['value'] > 0) {
    	    $selectedValue = $this->element['value'] ;
    	} else {
    	    $selectedValue = $this->value ;
    	}
		$categories = OspropertyProperties::loadCategoryOptions('','');
		return HTMLHelper::_('select.genericlist',$categories, $this->name.'[]', array(
		    'option.text.toHtml' => false ,
		    'option.value' => 'value', 
		    'option.text' => 'text', 
		    'list.attr' => ' class="inputbox form-select" multiple',
		    'list.select' => $selectedValue    		        		
		));	
	}
}

?>
