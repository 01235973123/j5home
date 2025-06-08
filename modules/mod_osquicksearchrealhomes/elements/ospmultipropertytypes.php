<?php
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
/*------------------------------------------------------------------------
# ospmultipropertytypes.php - OS Property Map
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2015 joomdonation.com. All Rights Reserved.
# @license - see license.php
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldOSPMultipropertytypes extends JFormFieldList {
	protected $type = 'OSPMultipropertytypes'; //the form field type
    var $options = array();

    protected function getOptions() {

		$path = JPATH_ROOT . '/components/com_osproperty';
        if (is_dir($path)) {        
			$db = Factory::getDBO();
			
			// generating query
			$db->setQuery("SELECT id as value, type_name as text from #__osrs_types where published = '1' order by ordering");
			// getting results
			$results = $db->loadObjectList();
			
			if(count($results)){
				// iterating
				$temp_options = array();
				
				foreach ($results as $item) {
					array_push($temp_options, array($item->value, $item->text));	
				}

				foreach ($temp_options as $option) {
					$this->options[] = HTMLHelper::_('select.option', $option[0], $option[1]);
				}		

				return $this->options;
			}
		}
        return $this->options;
		
	}
 	// bind function to save
    function bind( $array, $ignore = '' ) {
        if (key_exists( 'field-name', $array ) && is_array( $array['field-name'] )) {
        	$array['field-name'] = implode( ',', $array['field-name'] );
        }
        
        return parent::bind( $array, $ignore );
    }
}
