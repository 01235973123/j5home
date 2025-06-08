<?php
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
/*------------------------------------------------------------------------
# ospmulticategories.php - OS Property Map
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2014 joomdonation.com. All Rights Reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldOSPMulticategories extends JFormFieldList {
	protected $type = 'OSPMulticategories'; //the form field type
    var $options = array();

    protected function getOptions() {

		$path = JPATH_ROOT . '/components/com_osproperty';
        if (is_dir($path)) {        
			$db = Factory::getDBO();
			
			// generating query
			$db->setQuery("SELECT c.category_name AS name, c.id AS id, c.parent_id AS parent FROM #__osrs_categories AS c WHERE published = 1 ORDER BY c.category_name, c.parent_id ASC");
			// getting results
			$results = $db->loadObjectList();
			
			if(count($results)){
				// iterating
				$temp_options = array();
				
				foreach ($results as $item) {
					array_push($temp_options, array($item->id, $item->name, $item->parent));	
				}

				foreach ($temp_options as $option) {
					if($option[2] == 0) {
						$this->options[] = HTMLHelper::_('select.option', $option[0], $option[1]);
						$this->recursive_options($temp_options, 1, $option[0]);
					}
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

    function recursive_options($temp_options, $level, $parent){
		foreach ($temp_options as $option) {
      		if($option[2] == $parent) {
		  		$level_string = '';
		  		for($i = 0; $i < $level; $i++) $level_string .= '- - ';
        	    $this->options[] = HTMLHelper::_('select.option',  $option[0], $level_string . $option[1]);
       	    	$this->recursive_options($temp_options, $level+1, $option[0]);
			}
       	}
    }
}
