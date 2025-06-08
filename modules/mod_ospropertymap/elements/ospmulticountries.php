<?php
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
/*------------------------------------------------------------------------
# ospmulticountries.php - OS Property Map
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

class JFormFieldOSPMulticountries extends JFormFieldList {
	protected $type = 'OSPMulticountries'; //the form field type
    var $options = array();

    protected function getOptions() {

		$path = JPATH_ROOT . '/components/com_osproperty';
        if (is_dir($path)) {        
			$db = Factory::getDBO();
			include_once(JPATH_ROOT.'/components/com_osproperty/helpers/common.php');
			if(HelperOspropertyCommon::checkCountry()){
				$db->setQuery("Select fieldvalue from #__osrs_configuration where fieldname like 'show_country_id'");
				$country_ids = $db->loadResult();
				if($country_ids != ""){
					$extra_sql = " and id in ($country_ids)";
				}else{
					$extra_sql = "";
				}
			}else{
				$country_id = HelperOspropertyCommon::getDefaultCountry();
				$extra_sql = " and id = $country_id";
			}
			// generating query
			$db->setQuery("SELECT id as value, country_name as text from #__osrs_countries where 1=1 $extra_sql order by country_name");
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
