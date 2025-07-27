<?php

/*------------------------------------------------------------------------
# extrafields.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// no direct access
defined('_JEXEC') or die;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Table\Table;

class HelperOspropertyFields{
	/**
	 * Show field information for searching
	 *
	 * @param unknown_type $field
	 */
	static function showFieldinSearchModule($field)
	{
		global $mainframe;
		switch ($field->field_type)
		{
			case "textarea":
			case "text":
				HelperOspropertyFields::moduleSearchTextField($field,$inputbox_width_site);
				break;
			case "date":
				HelperOspropertyFields::moduleSearchDateField($field,$inputbox_width_site);
				break;
			case "radio":
				HelperOspropertyFields::moduleSearchRadioField($field,$inputbox_width_site);
				break;
			case "checkbox":
				HelperOspropertyFields::moduleSearchCheckboxField($field,$inputbox_width_site);
				break;
			case "singleselect":
				HelperOspropertyFields::moduleSearchSelectField($field,$inputbox_width_site);
				break;
			case "multipleselect":
				HelperOspropertyFields::moduleSearchMselectField($field,$inputbox_width_site);
				break;
		}
	}

	/**
	 * Show the text field for searching
	 *
	 * @param unknown_type $field
	 */
	static function moduleSearchTextField($field,$inputbox_width_site){
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		echo "<tr><td width='30%' align='left' style='padding:3px;'>";
		echo OSPHelper::getLanguageFieldValue($field,'field_label').": ";
		echo "</td><td width='70%' style='padding:3px;'>";
		$value = $db->escape($jinput->getString($field->field_name,''));
		echo "<input type='text' class='input-small' style='width:".$inputbox_width_site."px' size='20' value='".htmlentities($value)."' name='$field->field_name'>";
		echo "</td></tr>";
	}

	/**
	 * Show date field for searching
	 *
	 * @param unknown_type $field
	 */
	static function moduleSearchDateField($field){
		global $mainframe,$jinput;
		echo "<tr><td width='30%' align='left' style='padding:3px;'>";
		echo OSPHelper::getLanguageFieldValue($field,'field_label').": ";
		echo "</td><td width='70%' style='padding:3px;'>";
		$value = $db->escape($jinput->getString($field->field_name,''));
		echo HTMLHelper::_('calendar', $value, $field->field_name, $field->field_name, '%Y-%m-%d', array('class'=>'input-small', 'size'=>$inputbox_width_site));
		echo "</td></tr>";
	}



	static function moduleSearchRadioField($field,$inputbox_width_site){
		global $jinput;
		echo "<tr><td width='100%' align='left' style='padding:3px;' valign='top'>";
		echo OSPHelper::getLanguageFieldValue($field,'field_label').": ";
		echo "</td></tr><tr><td width='100%' style='padding:3px;'>";
		//$options = $field->options;
		//$options = $field->options;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$optionArr = $db->loadObjectList();
		if(count($optionArr) > 0){
			//$optionArr = explode("\n",$options);
			//remove white space in begin and end of the options of this array
			//$optionArr = HelperOspropertyCommon::stripSpaceArrayOptions($optionArr);
			//if(count($optionArr)){
			?>
			<table  width="100%">
				<?php
				$j = 0;
				$value = $jinput->getInt($field->field_name,0);
				for($i=0;$i<count($optionArr);$i++){
					echo "<tr>";
					$opt = $optionArr[$i];
					$j++;
					if($value == $opt->id){
						$checked = "checked";
					}else{
						$checked = "";
					}
					?>
					<td width="33%" align="left" style="padding:5px;">
						<input type="radio" value="<?php echo $opt->id;?>" name="<?php echo $field->field_name?>" id="<?php echo $field->field_name.$i?>" <?php echo $checked?>>&nbsp; <?php echo OSPHelper::getLanguageFieldValue($opt,'field_option');?>
					</td>
					<?php
					echo "</tr>";
				}
				?>
			</table>
			<?php
			//}
		}
		echo "</td></tr>";
	}

	static function moduleSearchSelectField($field,$inputbox_width_site){
		global $jinput;
		if($field->size == 0){
			$field->size = 180;
		}
		echo "<tr><td width='30%' align='left' style='padding:3px;'  valign='top'>";
		echo OSPHelper::getLanguageFieldValue($field,'field_label').": ";
		echo "</td><td width='70%' style='padding:3px;'>";
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$optionArr = $db->loadObjectList();
		if(count($optionArr) > 0){
		?>
		<select name="<?php echo $field->field_name?>" id="<?php echo $field->field_name?>" style="width:<?php echo $inputbox_width_site?>px;" class="input-small form-select" >
			<option value=""><?php echo Text::_('OS_ANY')?></option>
			<?php
			$value = $jinput->getInt($field->field_name,'');
			for($i=0;$i<count($optionArr);$i++){
				$opt = $optionArr[$i];
				if($value == $opt->id){
					$selected = "selected";
				}else{
					$selected = "";
				}
				?>
				<option value="<?php echo $opt->id?>" <?php echo $selected?>><?php echo OSPHelper::getLanguageFieldValue($opt,'field_option');?></option>
				<?php
			}
			?>
		</select>
		<?php
		}
		?>
		</td></tr>
		<?php
	}

	static function moduleSearchMselectField($field,$inputbox_width_site){
		global $jinput;
		echo "<tr><td width='30%' align='left' style='padding:3px;' >";
		echo OSPHelper::getLanguageFieldValue($field,'field_label').": ";
		echo "</td><td width='70%' style='padding:3px;'>";
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$optionArr = $db->loadObjectList();
		if(count($optionArr) > 0){
			$value = $jinput->get($field->field_name,array(),'ARRAY');
			$value	= ArrayHelper::toInteger($value);
			if($field->size >  0){
				$size = "width:".$field->size."px;";
			}else{
				$size = "";
			}
			?>
			<select name="<?php echo $field->field_name?>[]" id="<?php echo $field->field_name?>" style="<?php  echo $size?>" class="input-large" multiple>
			
			<?php

			for($i=0;$i<count($optionArr);$i++){
				$opt = $optionArr[$i];
				if(in_array($opt->id,$value)){
					$selected = "selected";
				}else{
					$selected = "";
				}
				?>
				<option value="<?php echo $opt->id?>" <?php echo $selected?>><?php echo OSPHelper::getLanguageFieldValue($opt,'field_option');?></option>
				<?php
			}
			echo "</select>";
		}
		echo "</td></tr>";
	}

	static function moduleSearchCheckboxField($field,$inputbox_width_site){
		global $jinput;
		echo "<tr><td width='30%' align='left' style='padding:3px;'  valign='top'>";
		echo OSPHelper::getLanguageFieldValue($field,'field_label').": ";
		echo "</td><td width='70%' style='padding:3px;'>";
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$optionArr = $db->loadObjectList();
		if(count($optionArr) > 0){
			?>
			<table  width="100%">
				<?php
				$j = 0;
				$value = $jinput->get($field->field_name,array(),'ARRAY');
				$value	= ArrayHelper::toInteger($value);
				for($i=0;$i<count($optionArr);$i++){
					echo "<tr>";
					$opt = $optionArr[$i];
					$j++;
					if(in_array($opt->id,$value)){
						$checked = "checked";
					}else{
						$checked = "";
					}
					?>
					<td width="33%" align="left" style="padding:5px;">
						<input type="checkbox" value="<?php echo $opt->id?>" name="<?php echo $field->field_name?>[]" id="<?php echo $field->field_name.$i?>" <?php echo $checked?>>&nbsp; <?php echo OSPHelper::getLanguageFieldValue($opt,'field_option');?>
					</td>
					<?php
					echo "</tr>";
				}
				?>
			</table>
			<?php
		}
		echo "</td></tr>";
	}
	/**
	 * check to see if the group field has the field that have data
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $gid
	 * @param unknown_type $access_sql
	 */
	static function checkFieldData($pid, $gid){
		global $mainframe;
		$db = Factory::getDbo();
		$user = Factory::getUser();

        $access_sql = ' and b.`access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';

		$query = "Select b.id,b.field_type from #__osrs_extra_fields as b"
		." WHERE b.published = '1' AND b.group_id = '$gid' $access_sql order by b.ordering";
		$db->setQuery($query);
		$fields = $db->loadObjectList();
		$return = 0;
		if(count($fields) > 0){
			for($i=0;$i<count($fields);$i++){
				$field = $fields[$i];
				$field_type = $field->field_type;
				switch ($field_type){
					case "textarea":
					case "text":
						if(HelperOspropertyFields::checkTextFieldValue($pid,$field->id)){
							$return = 1;
						}
						break;
					case "date":
						if(HelperOspropertyFields::checkDateFieldValue($pid,$field->id)){
							$return = 1;
						}
						break;
					case "radio":
					case "singleselect":
					case "checkbox":
					case "multipleselect":
						if(HelperOspropertyFields::checkOptionsFieldValue($pid,$field->id)){
							$return = 1;
						}
						break;
				}
			}
		}else{
			$return = 0;
		}
		return $return;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $gid
	 */
	static function getFieldsData($pid,$gid){
		global $mainframe;
		$db = Factory::getDbo();
		$user = Factory::getUser();

        $access_sql = ' and b.`access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';

		$query = "Select b.* from #__osrs_extra_fields as b"
		." WHERE b.published = '1' AND b.group_id = '$gid' $access_sql order by b.ordering";
		$db->setQuery($query);
		$returnArr = array();
		$fields = $db->loadObjectList();
		if(count($fields) > 0){
			for($i=0;$i<count($fields);$i++)
			{
				$field = $fields[$i];
				$field_type = $field->field_type;
				switch ($field_type)
				{
					case "date":
						if(HelperOspropertyFields::checkDateFieldValue($pid,$field->id)){
							$value = HelperOspropertyFields::getDateFieldValue($pid,$field->id);
							$count = count($returnArr);
							$returnArr[$count] = new stdClass();
							$returnArr[$count]->id = $field->id;
							$returnArr[$count]->field_label = OSPHelper::getLanguageFieldValue($field,'field_label');
							$returnArr[$count]->displaytitle = $field->displaytitle;
							$returnArr[$count]->field_type = $field->field_type;
							$returnArr[$count]->value = $value;
							$returnArr[$count]->field_description = $field->field_description;
						}
						break;
					case "textarea":
						if(HelperOspropertyFields::checkTextareaFieldValue($pid,$field->id)){
							$value = HelperOspropertyFields::getTextFieldValue($pid,$field->id);
							$count = count($returnArr);
							$returnArr[$count] = new stdClass();
							$returnArr[$count]->id = $field->id;
							$returnArr[$count]->field_label = OSPHelper::getLanguageFieldValue($field,'field_label');
							$returnArr[$count]->displaytitle = $field->displaytitle;
							$returnArr[$count]->field_type = $field->field_type;
							$returnArr[$count]->value = $value;
							$returnArr[$count]->field_description = $field->field_description;
						}
						break;
					case "text":
						if(HelperOspropertyFields::checkTextFieldValue($pid,$field->id)){
							$value = HelperOspropertyFields::getTextFieldValue($pid,$field->id);
							$count = count($returnArr);
							$returnArr[$count] = new stdClass();
							$returnArr[$count]->id = $field->id;
							$returnArr[$count]->field_label = OSPHelper::getLanguageFieldValue($field,'field_label');
							$returnArr[$count]->displaytitle = $field->displaytitle;
							$returnArr[$count]->field_type = $field->field_type;
							if($field->clickable == 1){
								if(strpos($value,'@') !== false){
									$returnArr[$count]->value = "<a href='mailto:".$value."' target='_blank' title='".OSPHelper::getLanguageFieldValue($field,'field_label')."'>".$value."</a>";
								}else{
									$returnArr[$count]->value = "<a href='".$value."' target='_blank' title='".OSPHelper::getLanguageFieldValue($field,'field_label')."'>".$value."</a>";
								}
							}else{
								$returnArr[$count]->value = $value;
							}
							$returnArr[$count]->field_description = $field->field_description;
						}
						break;
					case "radio":
					case "singleselect":
					case "checkbox":
					case "multipleselect":
						if(HelperOspropertyFields::checkOptionsFieldValue($pid,$field->id)){
							$value = HelperOspropertyFields::getOptionsFieldValue($pid,$field->id);
							$count = count($returnArr);
							$returnArr[$count] = new stdClass();
							$returnArr[$count]->id = $field->id;
							$returnArr[$count]->field_label = OSPHelper::getLanguageFieldValue($field,'field_label');
							$returnArr[$count]->displaytitle = $field->displaytitle;
							$returnArr[$count]->field_type = $field->field_type;
							$returnArr[$count]->value = $value;
							$returnArr[$count]->field_description = $field->field_description;
						}
						break;
				}
			}
		}
		//print_r($returnArr);
		
		return $returnArr;
	}

	static function checkDateFieldValue($pid,$fid){
		$db = Factory::getDbo();
		$db->setQuery("Select `value_date` from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$fid'");
		$value = $db->loadResult();
		if(trim($value) == ""){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * Check field
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $fid
	 * @return unknown
	 */
	static function checkTextareaFieldValue($pid,$fid)	{
		$db = Factory::getDbo();
		$db->setQuery("Select `value` from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$fid'");
		$value = $db->loadResult();
		if(trim($value) == ""){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * Check field
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $fid
	 * @return unknown
	 */
	static function checkTextFieldValue($pid,$fid)	{
		$db = Factory::getDbo();
		$lgs = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($lgs);
		$suffix = "";
        if ($translatable) {
            $suffix = OSPHelper::getFieldSuffix();
        }
		$db->setQuery("Select pro_type from #__osrs_properties where id = '$pid'");
		$pro_type = $db->loadResult();
	
		$db->setQuery("Select count(id) from #__osrs_extra_field_types where fid = '$fid' and type_id = '$pro_type'");
		$count = $db->loadResult();
		if($count == 0){
			return false;
		}else{

			$db->setQuery("Select * from #__osrs_extra_fields where id = '$fid'");
			$field = $db->loadObject();

			if($field->value_type == 0){
				$db->setQuery("Select `value".$suffix."` from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$fid'");
				$value = $db->loadResult();
				if(trim($value) == ""){
					return false;
				}else{
					return true;
				}
			}elseif($field->value_type == 1){
				$db->setQuery("Select `value_integer` from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$fid'");
				$value = $db->loadResult();
				if($value > 0){
					return true;
				}else{
					return false;
				}
			}elseif($field->value_type == 2){
				$db->setQuery("Select `value_decimal` from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$fid'");
				$value = $db->loadResult();
				if($value > 0){
					return true;
				}else{
					return false;
				}
			}
		}
	}

	/**
	 * Check 'options' field
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $fid
	 * @return unknown
	 */
	static function checkOptionsFieldValue($pid,$fid){
		$db = Factory::getDbo();
		$db->setQuery("Select pro_type from #__osrs_properties where id = '$pid'");
		$pro_type = $db->loadResult();
	
		$db->setQuery("Select count(id) from #__osrs_extra_field_types where fid = '$fid' and type_id = '$pro_type'");
		$count = $db->loadResult();
		if($count == 0){
			return false;
		}else{
			$db->setQuery("Select count(id) from #__osrs_property_field_opt_value where pid = '$pid' and fid = '$fid'");
			$count = $db->loadResult();
			if($count > 0){
				return true;
			}else{
				return false;
			}
		}
	}


	static function getDateFieldValue($pid,$fid){
		$db = Factory::getDbo();

		$db->setQuery("Select `value_date` from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$fid'");
		$value = $db->loadResult();
		return $value;
	}

	/**
	 * Get text field
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $fid
	 * @return unknown
	 */
	static function getTextFieldValue($pid,$fid){
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_fields where id = '$fid'");
		$field = $db->loadObject();
		$db->setQuery("Select * from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$fid'");
		$value = $db->loadObject();
		if($value->id > 0){
			if($field->value_type == 0){
				return trim(OSPHelper::getLanguageFieldValue($value,'value'));
			}elseif($field->value_type == 1){
				return $value->value_integer;
			}elseif($field->value_type == 2){
				return $value->value_decimal;
			}
		}
	}

	/**
	 * Get options field
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $fid
	 * @return unknown
	 */
	static function getOptionsFieldValue($pid,$fid){
		$db = Factory::getDbo();
		$query = "Select a.* from #__osrs_extra_field_options as a inner join #__osrs_property_field_opt_value as b on b.oid = a.id where b.pid = '$pid' and b.fid = '$fid' order by a.ordering";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			$return = array();
			for($i=0;$i<count($rows);$i++){
				$return[$i] = trim(OSPHelper::getLanguageFieldValue($rows[$i],'field_option'));
			}
			$return = implode(", ",$return);
			return $return;
		}
	}


	static function setFieldValue($list_detail){
		global $mainframe;
		$db = Factory::getDbo();
		if($list_detail->field_type == 1){
			$db->setQuery("Select * from #__osrs_extra_fields where id = '$list_detail->field_id'");
			$field = $db->loadObject();
			switch ($field->field_type){
				case "date":
				case "text":
					Factory::getApplication()->input->set('type_'.$field->field_name,$list_detail->search_type);
					Factory::getApplication()->input->set($field->field_name,$list_detail->search_param);

					break;
				case "textarea":
				case "radio":
				case "singleselect":
					Factory::getApplication()->input->set($field->field_name,$list_detail->search_param);
					break;
				case "checkbox":
				case "multipleselect":
					$search_param = explode(",",$list_detail->search_param);
					Factory::getApplication()->input->set($field->field_name,$search_param);
					break;
			}
		}
	}
	/**
	 * Get field param
	 *
	 * @param unknown_type $field
	 */
	static function getFieldParam($field){
		global $jinput;
		switch ($field->field_type){
			case "date":
			case "text":
				$value = $jinput->getString($field->field_name,'');
				$field_type = $_POST['type_'.$field->field_name];
				if($field_type == ""){
					$field_type = $_GET['type_'.$field->field_name];
				}
				return $field->id.":".$field_type.":".$value;
			break;
			case "textarea":
			case "radio":
			case "singleselect":
				$value = $jinput->getInt($field->field_name,'');
				return $field->id.":".$value;
			break;
			case "checkbox":
			case "multipleselect":
				$value = $jinput->get($field->field_name,array(),'ARRAY');
				$value = ArrayHelper::toInteger($value);
				$value = implode(",",$value);
				return $field->id.":"."".":".$value;
			break;
		}
	}
	/**
	 * Build query
	 *
	 * @param unknown_type $field
	 * @return unknown
	 */
	static function buildQuery($field){
		global $mainframe,$languages,$lang_suffix,$jinput;
		$db = Factory::getDbo();
		$translatable = Multilanguage::isEnabled() && count($languages);

		switch ($field->field_type){
			case "textarea":
				$value = OSPHelper::getStringRequest($field->field_name,'','');
				$type = $_POST['type_'.$field->field_name];
				if($type == ""){
					$type = $_GET['type_'.$field->field_name];
				}
				$type = $db->escape($type);
				$sql = "";
				if($value != ""){
					switch ($type){
						case "LIKE":
						case "NOT LIKE":
							$type_sql = "$type ".$db->quote($value);
							break;
						case "LIKE %...%":
							$type_sql = str_replace("%...%",$db->quote("%".$value."%"),$type);
							break;
						case "IN (...)":
						case "NOT IN (...)":
							$type_sql = str_replace("...",$value,$type);
							break;
					}
					if($type != ""){
						$sql = " a.id in (Select pro_id from #__osrs_property_field_value where field_id = '$field->id' and `value".$lang_suffix."` $type_sql')";
					}
				}
				return $sql;
				break;
			case "text":
				$type_sql = "";
				$type = $_POST['type_'.$field->field_name];
				if($type == ""){
					$type = $_GET['type_'.$field->field_name];
				}
				$type = $db->escape($type);
				if($field->value_type == 0){
					$value = OSPHelper::getStringRequest($field->field_name,'','');
					if($value != ""){
						switch ($type){
							case "LIKE":
							case "NOT LIKE":
								$type_sql = "`value".$lang_suffix."` ".$type." ".$db->quote($value);
								break;
							case "LIKE %...%":
								$type_sql = "`value".$lang_suffix."` ".str_replace("%...%",$db->quote("%".$value."%"),$type);
								break;
							case "IN (...)":
							case "NOT IN (...)":
								$type_sql = "`value".$lang_suffix."` ".str_replace("...",$value,$type);
								break;
						}
					}
				}elseif($field->value_type == 1){
					$value = $jinput->getInt($field->field_name,-1);
					if($value >= 0){
						$type_sql = " `value_integer` ".$type." '".$value."'";
					}
				}elseif($field->value_type == 2){
					$value = $jinput->getFloat($field->field_name,-1);
					if($value >= 0){
						$type_sql = " `value_decimal` ".$type." '".$value."'";
					}
				}
				if(($type != "") && ($type_sql != "")){
					$sql = " a.id in (Select pro_id from #__osrs_property_field_value where field_id = '$field->id' and $type_sql)";
				}
				return $sql;
			case "date":
				$type = $_POST['type_'.$field->field_name];
				if($type == ""){
					$type = $_GET['type_'.$field->field_name];
				}
				$type = $db->escape($type);
				$value = OSPHelper::getStringRequest($field->field_name,'','');
				if(($type != "") && ($value != "")){
					$sql = " a.id in (Select pro_id from #__osrs_property_field_value where field_id = '$field->id' and `value_date` $type ".$db->quote($value).")";
				}
				return $sql;
				break;
			case "radio":
			case "singleselect":
				$value = $jinput->getInt($field->field_name,0);
				if($value > 0){
					$sql = " a.id in (Select pid from #__osrs_property_field_opt_value where fid = '$field->id' and `oid` = '$value')";
					return $sql;
				}else{
					return '';
				}
				break;
			case "checkbox":
			case "multipleselect":
				$sql = "";
				$value = $jinput->get($field->field_name,array(),'ARRAY');
				$value = ArrayHelper::toInteger($value);
				if(count($value) > 0){
					$extraArr = array();
					for($i=0;$i<count($value);$i++){
						$value[$i] = "'".$value[$i]."'";
					}
					$valueSql = implode(",",$value);
					$sql = " a.id in (Select pid from #__osrs_property_field_opt_value where fid = '$field->id' and `oid` in ($valueSql) group by pid having count(pid) = '".count($value)."')";
				}
				return $sql;
				break;
		}
	}
	/**
	 * Check field for submitting data
	 *
	 * @param unknown_type $field
	 * @return unknown
	 */
	static function checkField($field){
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		switch ($field->field_type){
			case "text":
				switch($field->value_type){
					case "0":
						$value = OSPHelper::getStringRequest($field->field_name,'','');
						if($value != ""){
							return true;
						}else{
							return false;
						}
					break;
					case "1":
						$value = $jinput->getInt($field->field_name,0);
						if($value > 0){
							return true;
						}else{
							return false;
						}
					break;
					case "2":
						$value = $jinput->getFloat($field->field_name,0);
						if($value > 0){
							return true;
						}else{
							return false;
						}
					break;
				}
			break;
			case "date":
			case "textarea":
				$value = OSPHelper::getStringRequest($field->field_name,'','');
				if($value != ""){
					return true;
				}else{
					return false;
				}
				break;
			case "radio":
			case "singleselect":
				$value = $jinput->getInt($field->field_name,0);
				if($value > 0){
					return true;
				}else{
					return false;
				}
				break;
			case "checkbox":
			case "multipleselect":
				$value = $jinput->get($field->field_name,array(),'ARRAY');
				$value = ArrayHelper::toInteger($value);
				if(count($value) > 0){
					return true;
				}else{
					return false;
				}
				break;
		}
	}

	public static function searchTypeDropdownString($fieldname){
		global $jinput;
		$db = Factory::getDbo();
		$optionArr = array();
		ob_start();
		$optionArr = array('LIKE','NOT LIKE','LIKE %...%','IN (...)','NOT IN (...)');
		$textArr   = array(Text::_('OS_LIKE'),Text::_('OS_NOT_LIKE'),Text::_('OS_LIKE').' %...%',Text::_('OS_IN').' (...)',Text::_('OS_NOT_IN').' (...)')
		?>
		<select name="type_<?php echo $fieldname?>" class="input-small form-select searchTypeDropdownString" id="type_<?php echo $fieldname?>">
			<?php
			$type = $jinput->getString('type_'.$fieldname,'');
			for($i=0;$i<count($optionArr);$i++){
				$op = $optionArr[$i];
				if($op == $type){
					$selected = "selected";
				}else{
					$selected = "";
				}
				?>
				<option value="<?php echo $op?>" <?php echo $selected?>><?php echo $textArr[$i]?></option>
				<?php
			}
			?>
		</select>
		<?php
		$body = ob_get_contents();
		ob_end_clean();
		return $body;
	}

    public static function searchTypeDropdownDate($fieldname){
        $optionArr = array();
        ob_start();
        $optionArr = array('=','>','<');
        $labelArr = array(Text::_('OS_IS'),Text::_('OS_AFTER'),Text::_('OS_BEFORE'));
        ?>
        <select name="type_<?php echo $fieldname?>" class="input-small" id="type_<?php echo $fieldname?>">
            <?php
            $type = $_POST['type_'.$fieldname];
            if($type == ""){
                $type = $_GET['type_'.$fieldname];
            }
            for($i=0;$i<count($optionArr);$i++){
                $op = $optionArr[$i];
                if($op == $type){
                    $selected = "selected";
                }else{
                    $selected = "";
                }
                ?>
                <option value="<?php echo $op?>" <?php echo $selected?>><?php echo $labelArr[$i];?></option>
            <?php
            }
            ?>
        </select>
        <?php
        $body = ob_get_contents();
        ob_end_clean();
        return $body;
    }

	public static function searchTypeDropdownNumber($fieldname){
		$optionArr = array();
		ob_start();
		$optionArr = array('=','>','>=','<','<=','!=');
		?>
		<select name="type_<?php echo $fieldname?>" class="input-small form-select searchTypeDropdownNumber" id="type_<?php echo $fieldname?>">
			<?php
			$type = $_POST['type_'.$fieldname];
			if($type == ""){
				$type = $_GET['type_'.$fieldname];	
			}
			for($i=0;$i<count($optionArr);$i++){
				$op = $optionArr[$i];
				if($op == $type){
					$selected = "selected";
				}else{
					$selected = "";
				}
				?>
				<option value="<?php echo $op?>" <?php echo $selected?>><?php echo $op?></option>
				<?php
			}
			?>
		</select>
		<?php
		$body = ob_get_contents();
		ob_end_clean();
		return $body;
	}
	/**
	 * Show field information for searching
	 *
	 * @param unknown_type $field
	 */
	public static function showFieldinAdvSearch($field,$advancedpage=0){
		global $mainframe;
		switch ($field->field_type){
			case "textarea":
			case "text":
				HelperOspropertyFields::advSearchTextField($field);
				break;
			case "date":
				HelperOspropertyFields::advSearchDateField($field,$advancedpage);
				break;
			case "radio":
				HelperOspropertyFields::advSearchRadioField($field);
				break;
			case "checkbox":
				HelperOspropertyFields::advSearchCheckboxField($field,$advancedpage);
				break;
			case "singleselect":
				HelperOspropertyFields::advSearchSelectField($field);
				break;
			case "multipleselect":
				HelperOspropertyFields::advSearchMselectField($field);
				break;
		}
	}

	/**
	 * Show the text field for searching
	 *
	 * @param unknown_type $field
	 */
	static function advSearchTextField($field){
		global $mainframe,$jinput;
		echo "<div style='float:left;min-width:200px;'>";
		echo "<strong>".OSPHelper::getLanguageFieldValue($field,'field_label')."</strong>: ";
		echo "</div><div style='float:left;padding-bottom:10px;'>";
		if($field->value_type == 0){
			$value = OSPHelper::getStringRequest($field->field_name,'','');
			echo self::searchTypeDropdownString($field->field_name);
			echo "<input type='text' class='input-medium query-search search-query form-control' size='20' value='".htmlentities($value)."' name='$field->field_name' id='$field->field_name'>";
		}elseif($field->value_type == 1){
			$value = $jinput->getInt($field->field_name,'');
			if($value > 0){
				$value = (int)$value;
			}else{
				$value = "";
			}
			echo self::searchTypeDropdownNumber($field->field_name);
			echo "<input type='text' class='input-small query-search search-query form-control' size='20' value='".$value."' name='$field->field_name' id='$field->field_name'>";
		}elseif($field->value_type == 2){
			$value = $jinput->getFloat($field->field_name,0);
			if($value > 0 ){
				$value = (float)$value;
			}else{
				$value = "";
			}
			echo self::searchTypeDropdownNumber($field->field_name);
			echo "<input type='text' class='input-small query-search search-query form-control' size='20' value='".$value."' name='$field->field_name' id='$field->field_name'>";
		}

		echo "</div>";
	}

	/**
	 * Show date field for searching
	 *
	 * @param unknown_type $field
	 */
	static function advSearchDateField($field,$advSearchDateField){
		global $mainframe,$jinput;
		echo "<div style='float:left;min-width:200px;'>";
		echo "<strong>".OSPHelper::getLanguageFieldValue($field,'field_label')."</strong>: ";
		echo "</div><div style='float:left;'>";
		$value = $jinput->getString($field->field_name,'');
		echo self::searchTypeDropdownDate($field->field_name);
		echo HTMLHelper::_('calendar', $value, $field->field_name, $field->field_name.$advSearchDateField, '%Y-%m-%d', array('class'=>'input-small', 'size'=>$field->size));
		echo "</div>";
	}



	static function advSearchRadioField($field){
		global $jinput;
		echo "<div style='float:left;min-width:200px;'>";
		echo "<strong>".OSPHelper::getLanguageFieldValue($field,'field_label')."</strong>: ";
		echo "</div><div style='float:left;'>";
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$optionArr = $db->loadObjectList();
		if(count($optionArr) > 0){
			?>
			
			<select name="<?php echo $field->field_name?>" id="<?php echo $field->field_name?>" style="width:<?php echo $field->size?>px;" class="input-small" >
				<option value=""><?php echo Text::_('OS_ANY')?></option>
				<?php
				$value = $jinput->getInt($field->field_name,0);
				for($i=0;$i<count($optionArr);$i++){
					$opt = $optionArr[$i];
					$i++;
					if($value == $opt->id){
						$selected = "selected";
					}else{
						$selected = "";
					}
					?>
					<option value="<?php echo $opt->id?>" <?php echo $selected?>><?php echo OSPHelper::getLanguageFieldValue($opt,'field_option');?></option>
					<?php
				}
				?>
			</select>
			<?php
		}
		echo "</div>";
	}

	static function advSearchSelectField($field){
		global $jinput;
		if($field->size == 0){
			$field->size = 180;
		}
		echo "<div style='float:left;min-width:200px;'>";
		echo "<strong>".OSPHelper::getLanguageFieldValue($field,'field_label')."</strong>: ";
		echo "</div><div style='float:left;'>";
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$optionArr = $db->loadObjectList();
		if(count($optionArr) > 0){
		?>
		<select name="<?php echo $field->field_name?>" id="<?php echo $field->field_name?>" style="width:<?php echo $field->size?>px;" class="input-small form-select" >
			<option value=""><?php echo Text::_('OS_ANY')?></option>
			<?php
			$value = $jinput->getInt($field->field_name,0);
			for($i=0;$i<count($optionArr);$i++){
				$opt = $optionArr[$i];
				if($value == $opt->id){
					$selected = "selected";
				}else{
					$selected = "";
				}
				?>
				<option value="<?php echo $opt->id?>" <?php echo $selected?>><?php echo OSPHelper::getLanguageFieldValue($opt,'field_option');?></option>
				<?php
			}
			?>
		</select>
		<?php
		}
		echo "</div>";
	}

	static function advSearchMselectField($field){
		global $jinput;
		echo "<div style='float:left;min-width:200px;'>";
		echo "<strong>".OSPHelper::getLanguageFieldValue($field,'field_label')."</strong>: ";
		echo "</div><div style='float:left;'>";
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$optionArr = $db->loadObjectList();
		if(count($optionArr) > 0){
			$value = $jinput->get($field->field_name,array(),'ARRAY');
			$value = ArrayHelper::toInteger($value);
			if($field->size >  0){
				$size = "width:".$field->size."px;";
			}else{
				$size = "";
			}
			?>
			<select name="<?php echo $field->field_name?>[]" id="<?php echo $field->field_name?>" style="<?php  echo $size?>" class="input-large" multiple>
			
			<?php

			for($i=0;$i<count($optionArr);$i++){
				$opt = $optionArr[$i];
				if(in_array($opt->id,$value)){
					$selected = "selected";
				}else{
					$selected = "";
				}
				?>
				<option value="<?php echo $opt->id?>" <?php echo $selected?>><?php echo OSPHelper::getLanguageFieldValue($opt,'field_option');?></option>
				<?php
			}
			echo "</select>";
		}
		echo "</div>";
	}

	static function advSearchCheckboxField($field,$advancedsearchpage){
		global $jinput;
		echo "<div style='float:left;' class='span2'>";
		echo "<strong>".OSPHelper::getLanguageFieldValue($field,'field_label')."</strong>: ";
		if($advancedsearchpage == 1){
			//$width = "min-width:400px;";
		}else{
			//$width = "";
		}
		echo "</div><div style='float:left;".$width."' class='span9'>";
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$optionArr = $db->loadObjectList();
		if(count($optionArr) > 0){
			?>
			<div class="row-fluid">
				
					<?php
					$j = 0;
					$value = $jinput->get($field->field_name,array(),'ARRAY');
					$value = ArrayHelper::toInteger($value);
					for($i=0;$i<count($optionArr);$i++){
						$opt = $optionArr[$i];
						$j++;
						if(in_array($opt->id,$value)){
							$checked = "checked";
						}else{
							$checked = "";
						}
						if($advancedsearchpage == 1){
						?>
							<div class="span3" style="float:left;padding-right:10px;margin-left:0px;">
						<?php
						}else{
						?>
							<div class="row-fluid">
						<?php
						}
						?>
							<input type="checkbox" value="<?php echo $opt->id?>" name="<?php echo $field->field_name?>[]" id="<?php echo $field->field_name.$i?>" <?php echo $checked?>>&nbsp; <?php echo OSPHelper::getLanguageFieldValue($opt,'field_option');?>
						</div>
						<?php
					}
					?>
				
			</div>
			<?php
		}
		echo "</div>";
	}

	/**
	 * Show fields
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();

		switch ($field->field_type){
			case "text":
				HelperOspropertyFields::showField_Text($field,$pid);
				break;
			case "date":
				HelperOspropertyFields::showField_Date($field,$pid);
				break;
			case "textarea":
				HelperOspropertyFields::showField_Textarea($field,$pid);
				break;
			case "radio":
				HelperOspropertyFields::showField_Radio($field,$pid);
				break;
			case "checkbox":
				HelperOspropertyFields::showField_Checkbox($field,$pid);
				break;
			case "singleselect":
				HelperOspropertyFields::showField_Singleselect($field,$pid);
				break;
			case "multipleselect":
				HelperOspropertyFields::showField_Multipleselect($field,$pid);
				break;
		}
	}

	/**
	 * Show TEXT field
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Text($field,$pid){
		global $mainframe,$languages;
		$translatable = Multilanguage::isEnabled() && count($languages);
		$db = Factory::getDBO();
		$value = "";
		if($pid > 0){
			$db->setQuery("Select * from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
			$obj = $db->loadObject();
		}
		if(($field->size == "0") || ($field->size == "")){
			$field->size = 20;
		}
		if(($value == "") && ($field->default_value!="")){
			$value = $field->default_value;
		}
		if($field->readonly == 1){
			$readonly = "readonly";
		}else{
			$readonly = "";
		}
		if(($field->maxlength == "0") || ($field->maxlength == "")){
			$field->maxlength = 255;
		}
		$value = stripslashes($value);

		$default_language = OSPHelper::getDefaultLanguage();
		$default_language = substr($default_language,0,2);
		if(($translatable) && ($field->value_type == 0)){
		?>
		<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $default_language.'.png'; ?>" />
		<?php } 
		if($field->value_type == 0){
			$class = "medium";
		}else{
			$class = "mini";
		}
		if($field->value_type == 0){
			$value = $obj->value;
		}elseif($field->value_type == 1){
			$value = $obj->value_integer;
		}elseif($field->value_type == 2){
			$value = $obj->value_decimal;
		}
		?>
		<input type="text" class="input-<?php echo $class;?> form-control" size="<?php echo $field->size?>" value="<?php echo $value;?>" name="<?php echo $field->field_name?>" id="<?php echo $field->field_name?>" maxlength = "<?php echo $field->maxlength?>" <?php echo $readonly ?> />
		<BR />
		<?php
		if(($translatable) && ($field->value_type == 0)){
			$i = 0;
			//print_r($languages);
			//die();
			foreach ($languages as $language)
			{
				$i++;
				$sef = $language->sef;
				?>
				<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $sef.'.png'; ?>" />
				<input type="text" class="input-<?php echo $class;?> form-control" size="<?php echo $field->size?>" value="<?php echo OSPHelper::getLanguageFieldValueBackend($obj,'value','_'.$sef);?>" name="<?php echo $field->field_name?>_<?php echo $sef?>" id="<?php echo $field->field_name?>_<?php echo $sef?>" maxlength = "<?php echo $field->maxlength?>" <?php echo $readonly ?> />
				<BR />
				<?php
			}
		}
		if($field->required == 1){
			echo "<span class='required'>(*)</span>";
		}
	}

	/**
	 * Show field date
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Date($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();
		$value = "";
		if($pid > 0){
			$db->setQuery("Select `value_date` from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
			$value = $db->loadResult();
		}
		if(($field->size == "0") || ($field->size == "")){
			$field->size = 20;
		}
		if(($value == "") && ($field->default_value!="")){
			$value = $field->default_value;
		}
		if($field->readonly == 1){
			$readonly = "readonly";
		}else{
			$readonly = "";
		}
		if(($field->maxlength == "0") || ($field->maxlength == "")){
			$field->maxlength = 19;
		}
		echo HTMLHelper::_('calendar', $value, $field->field_name, $field->field_name, '%Y-%m-%d', array('class'=>'input-small form-control', 'size'=>$field->size,  'maxlength'=>$field->maxlength));
		if($field->required == 1){
			echo "<span class='required'>(*)</span>";
		}
	}


	/**
	 * Show Textarea
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Textarea($field,$pid)
	{
		global $mainframe,$languages;
		$translatable = Multilanguage::isEnabled() && count($languages);
		$db = Factory::getDBO();
		if($pid > 0){
			$db->setQuery("Select * from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
			//$value = $db->loadResult();
			$obj = $db->loadObject();
		}
		if($field->ncols == "0" || $field->ncols == "")
		{
			$field->ncols = 50;
		}
		if($field->nrows == "0" || $field->nrows == "")
		{
			$field->nrows = 50;
		}
		if($obj->value == "" && $field->default_value!="")
		{
			$obj->value = $field->default_value;
		}
		if($field->readonly == 1)
		{
			$readonly = "readonly";
		}
		else
		{
			$readonly = "";
		}
		//$value = stripslashes($value);

		$default_language = OSPHelper::getDefaultLanguage();
		$default_language = substr($default_language,0,2);
		if($translatable){
		?>
		<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $default_language.'.png'; ?>" />
		<?php } ?>
		<BR />
		<!--  <textarea name="<?php echo $field->field_name?>" id="<?php echo $field->field_name?>" cols="<?php echo $field->ncols?>" rows="<?php echo $field->nrows?>" class="input-large" <?php echo $readonly?>><?php echo $obj->value?></textarea>-->
		<?php 
		$editor	= Editor::getInstance(Factory::getConfig()->get('editor'));
		echo $editor->display( $field->field_name,  OSPHelper::getLanguageFieldValueBackend($obj,'value','') , '95%', '100', '75', '10',false ) ;
		?>
		<BR /><BR />
		<?php
		if($translatable){
			$i = 0;
			foreach ($languages as $language)
			{
				$sef = $language->sef;
				?>
				<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $sef.'.png'; ?>" />
				<BR />
				<!--  <textarea name="<?php echo $field->field_name?>_<?php echo $sef?>" id="<?php echo $field->field_name?>_<?php echo $sef?>" cols="<?php echo $field->ncols?>" rows="<?php echo $field->nrows?>" class="input-large" <?php echo $readonly?>><?php echo OSPHelper::getLanguageFieldValueBackend($obj,'value','_'.$sef);?></textarea>-->
				<?php 
				
				echo $editor->display( $field_name = $field->field_name,  OSPHelper::getLanguageFieldValueBackend($obj,'value','_'.$sef) , '95%', '100', '75', '10' ,false) ;
				?>
				<BR /><BR />
				<?php
			}
		}

		if($field->required == 1){
			echo "<span class='required'>(*)</span>";
		}
	}

	/**
	 * Radio button
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Radio($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();
		if($pid > 0){
			$db->setQuery("Select `oid` from #__osrs_property_field_opt_value where pid = '$pid' and fid = '$field->id'");
			$oid = $db->loadResult();
		}
		else
		{
			$defaultValue = $field->default_value;
			if($defaultValue != "")
			{
				$db->setQuery("Select `id` from #__osrs_extra_field_options where field_id = '$field->id' and field_option like '$defaultValue'");
				$optionId = (int) $db->loadResult();
				if($optionId > 0)
				{
					$oid = $optionId;
				}
				
			}
		}
		//$options = $field->options;
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$options = $db->loadObjectList();
		if(count($options) > 0){
			//$optionArr = explode("\n",$options);
			//remove white space in begin and end of the options of this array
			//$optionArr = HelperOspropertyCommon::stripSpaceArrayOptions($optionArr);
			?>
			<table  width="100%">
				<tr>
					<?php
					$j = 0;
					for($i=0;$i<count($options);$i++){
						$j++;
						$opt = $options[$i];
						if($oid == $opt->id){
							$checked = "checked";
						}else{
							$checked = "";
						}
						?>
						<td width="50%" align="left" style="padding:5px;">
							<input type="radio" value="<?php echo $opt->id?>" name="<?php echo $field->field_name?>" id="<?php echo $field->field_name.$i?>" <?php echo $checked?>>&nbsp; <?php echo OSPHelper::getLanguageFieldValue($opt,'field_option');?>
						</td>
						<?php
						if($j == 2){
							echo "</tr><tr>";
							$j = 0;
						}
					}
					?>
				</tr>
			</table>
<?php
		}
	}


	/**
	 * Checkboxes fields
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Checkbox($field,$pid){
		global $mainframe;
		//print_r($field);
		$db = Factory::getDBO();
		$valueArr = array();
		if($pid > 0){
			$db->setQuery("Select `oid` from #__osrs_property_field_opt_value where pid = '$pid' and fid = '$field->id'");
			$oids = $db->loadObjectList();
			if(count($oids) > 0){
				for($i=0;$i<count($oids);$i++){
					$count = count($valueArr);
					$valueArr[$count] = $oids[$i]->oid;
				}
			}
		}
		else
		{
			$defaultValue = $field->default_value;
			if($defaultValue != "")
			{
				$defaultArr = explode(",", $defaultValue);
				if(count($defaultArr))
				{
					foreach($defaultArr as $dA)
					{
						$db->setQuery("Select `id` from #__osrs_extra_field_options where field_id = '$field->id' and field_option like '$dA'");
						$optionId = (int) $db->loadResult();
						if($optionId > 0)
						{
							$valueArr[] = $optionId;
						}
					}
				}
				
			}
		}

		//$options = $field->options;
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$options = $db->loadObjectList();
		if(count($options) > 0){
			//$optionArr = explode("\n",$options);
			//remove white space in begin and end of the options of this array
			//$optionArr = HelperOspropertyCommon::stripSpaceArrayOptions($optionArr);
			?>
			<table  width="100%">
				<tr>
					<?php
					$j = 0;
					for($i=0;$i<count($options);$i++){
						$j++;
						$opt = $options[$i];
						if(in_array($opt->id,$valueArr)){
							$checked = "checked";
						}else{
							$checked = "";
						}
						?>
						<td width="50%" align="left" style="padding:5px;">
							<input type="checkbox" name="<?php echo $field->field_name?>[]" id="<?php echo $field->field_name.$i?>" <?php echo $checked?> value="<?php echo $opt->id?>">&nbsp; <?php echo OSPHelper::getLanguageFieldValue($opt,'field_option');?>
						</td>
						<?php
						if($j == 2){
							echo "</tr><tr>";
							$j = 0;
						}
					}
					?>
				</tr>
			</table>
<?php
		}
	}

	/**
	 * Single select
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Singleselect($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();
		$value = "";
		$valueArr = array();
		if($pid > 0){
			$db->setQuery("Select `oid` from #__osrs_property_field_opt_value where pid = '$pid' and fid = '$field->id'");
			$value = $db->loadResult();
		}
		if($value == ""){
			//$value = $field->default_value;
			$default_value = $field->default_value;
			$db->setQuery("Select id from #__osrs_extra_field_options where field_id = '$field->id' and field_option like '$default_value'");
			$value = $db->loadResult();
		}
		if(($field->size == "0") || ($field->size == "")){
			$field->size = 180;
		}
		//$options = $field->options;
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$options = $db->loadObjectList();

		if(count($options) > 0){
			//$optionArr = explode("\n",$options);
			//remove white space in begin and end of the options of this array
			//$optionArr = HelperOspropertyCommon::stripSpaceArrayOptions($optionArr);
			//if(count($optionArr)){
			?>
			<select name="<?php echo $field->field_name?>" id="<?php echo $field->field_name?>" style="width:<?php echo $field->size?>px !important;" class="input-medium form-select ilarge" >
			<option value=""><?php echo Text::_('OS_SELECT_VALUE')?></option>
			<?php
			for($i=0;$i<count($options);$i++){
				$opt = $options[$i];
				if($value == $opt->id){
					$selected = "selected";
				}else{
					$selected = "";
				}
				?>
				<option value="<?php echo $opt->id?>" <?php echo $selected?>><?php echo $opt->field_option;?></option>
				<?php
			}
			echo "</select>";
			if($field->required == 1) {
                echo "<span class='required'>(*)</span>";
            }
		}
	}

	/**
	 * Multple select
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Multipleselect($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();
		$valueArr = array();
		if($pid > 0){
			$db->setQuery("Select `oid` from #__osrs_property_field_opt_value where pid = '$pid' and fid = '$field->id'");
			$oids = $db->loadObjectList();
			if(count($oids) > 0){
				for($i=0;$i<count($oids);$i++){
					$count = count($valueArr);
					$valueArr[$count] = $oids[$i]->oid;
				}
			}
		}

		if(($field->size == "0") || ($field->size == "")){
			$field->size = 180;
		}

		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$field->id' order by ordering");
		$options = $db->loadObjectList();
		if(count($options) > 0){
			?>
			<select name="<?php echo $field->field_name?>[]" id="<?php echo $field->field_name?>" style="height:<?php echo $field->size?>px;" class="input-large ilarge form-control" style="min-height:100px !important;" multiple>
			
			<?php
			for($i=0;$i<count($options);$i++){
				$opt = $options[$i];
				if(in_array($opt->id,$valueArr)){
					$selected = "selected";
				}else{
					$selected = "";
				}
				?>
				<option value="<?php echo $opt->id;?>" <?php echo $selected?>><?php echo $opt->field_option?></option>
				<?php
			}
			echo "</select>";
		}
	}



	/**
	 * Save the value of the extra fields
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function saveField($field,$pid){
		global $mainframe;
		switch ($field->field_type){
			case "radio":
			case "singleselect":
				HelperOspropertyFields::saveField_SingleSelect($field,$pid);
				break;
			case "date":
				HelperOspropertyFields::saveField_Date($field,$pid);
				break;
			case "text":
				HelperOspropertyFields::saveField_Text($field,$pid);
				break;
			case "textarea":
				HelperOspropertyFields::saveField_Textarea($field,$pid);
				break;
			case "multipleselect":
			case "checkbox":
				HelperOspropertyFields::saveField_Checkbox($field,$pid);
			break;
		}
	}

	/**
	 * Save field 
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function saveField_Text($field,$pid)
	{
		global $mainframe,$languages,$jinput;
		$translatable = Multilanguage::isEnabled() && count($languages);
		$db = Factory::getDBO();
		$row = Table::getInstance('Fieldvalue','OspropertyTable');
		if($field->value_type == 0)
		{
			$value = $jinput->getString($field->field_name,'');
			if($value != "")
			{
				$db->setQuery("Select count(id) from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
				$count = $db->loadResult();
				if($count == 0){
					$row->id = 0;
					$row->field_id = $field->id;
					$row->pro_id = $pid;
					$row->value = $value;
					if($translatable)
					{
						foreach ($languages as $language)
						{
							$sef = $language->sef;
							$row->{'value_'.$sef} = $jinput->getString($field->field_name.'_'.$sef,'','');
						}
					}
					if (!$row->store()) 
					{
						//JError::raiseError(500, $row->getError() );
						throw new Exception($row->getError(), 500);
					}
				}
				else
				{
					$db->setQuery("Select id from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
					$value_id = $db->loadResult();
					$row->id = $value_id;
					$row->value = $value;
					if($translatable){
						foreach ($languages as $language)
						{
							$sef = $language->sef;
							$row->{'value_'.$sef} = $jinput->getString($field->field_name.'_'.$sef,'');
						}
					}
					if (!$row->store()) 
					{
						//JError::raiseError(500, $row->getError() );
						throw new Exception($row->getError(), 500);
					}
				}
			}

		}
		elseif($field->value_type == 1)
		{
			$value = $jinput->getInt($field->field_name,0);
			$db->setQuery("Select count(id) from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
			$count = $db->loadResult();
			if($count == 0)
			{
				$row->id = 0;
				$row->field_id = $field->id;
				$row->pro_id = $pid;
				$row->value_integer = $value;

				if (!$row->store()) 
				{
					//JError::raiseError(500, $row->getError() );
					throw new Exception($row->getError(), 500);
				}
			}
			else
			{
				$db->setQuery("Select id from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
				$value_id = $db->loadResult();
				$row->id = $value_id;
				$row->value_integer = $value;
				if (!$row->store()) 
				{
					//JError::raiseError(500, $row->getError() );
					throw new Exception($row->getError(), 500);
				}
			}

		}
		elseif($field->value_type == 2)
		{
			$value = $jinput->getFloat($field->field_name,0);
			$db->setQuery("Select count(id) from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
			$count = $db->loadResult();
			if($count == 0){
				$row->id = 0;
				$row->field_id = $field->id;
				$row->pro_id = $pid;
				$row->value_decimal = $value;
				
				if (!$row->store()) 
				{
					//JError::raiseError(500, $row->getError() );
					throw new Exception($row->getError(), 500);
				}
			}
			else
			{
				$db->setQuery("Select id from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
				$value_id = $db->loadResult();
				$row->id = $value_id;
				$row->value_decimal = $value;
				if (!$row->store()) 
				{
					//JError::raiseError(500, $row->getError() );
					throw new Exception($row->getError(), 500);
				}
			}
		}
	}

	/**
	 * Save field 
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function saveField_Date($field,$pid){
		global $mainframe,$languages;
		$translatable = Multilanguage::isEnabled() && count($languages);
		$db = Factory::getDBO();
		$row = Table::getInstance('Fieldvalue','OspropertyTable');
		$value = OSPHelper::getStringRequest($field->field_name,'','');
		if($value != ""){
			$db->setQuery("Select count(id) from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
			$count = $db->loadResult();
			if($count == 0){
				$row->id = 0;
				$row->field_id = $field->id;
				$row->pro_id = $pid;
				$row->value_date = $value;
				if (!$row->store()) 
				{
					//JError::raiseError(500, $row->getError() );
					throw new Exception($row->getError(), 500);
				}
			}else{
				$db->setQuery("Select id from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
				$value_id = $db->loadResult();
				$row->id = $value_id;
				$row->value_date = $value;
				if (!$row->store()) 
				{
					//JError::raiseError(500, $row->getError() );
					throw new Exception($row->getError(), 500);
				}

			}
		}
	}

	/**
	 * Save Single select field
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function saveField_SingleSelect($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();
		$value = $_POST[$field->field_name];
		$db->setQuery("DELETE FROM #__osrs_property_field_opt_value WHERE pid = '$pid' AND fid = '$field->id'");
		$db->execute();
		if($value != ""){
			$db->setQuery("INSERT INTO #__osrs_property_field_opt_value (id, pid,fid,oid) VALUES (NULL,'$pid','$field->id','$value')");
			$db->execute();
		}
	}
	/**
	 * Save value of textarea field
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function saveField_Textarea($field,$pid){
		global $mainframe,$languages;
		$translatable = Multilanguage::isEnabled() && count($languages);
		$db = Factory::getDBO();
		$row = Table::getInstance('Fieldvalue','OspropertyTable');
		$value = $_POST[$field->field_name];
		if($value != ""){
			$db->setQuery("Select count(id) from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
			$count = $db->loadResult();
			if($count == 0){
				$row->id = 0;
				$row->field_id = $field->id;
				$row->pro_id = $pid;
				$row->value = $value;
				if($translatable){
					foreach ($languages as $language)
					{
						$sef = $language->sef;
						$row->{'value_'.$sef} = $_POST[$field->field_name.'_'.$sef];
					}
				}
				if (!$row->store()) 
				{
					//JError::raiseError(500, $row->getError() );
					throw new Exception($row->getError(), 500);
				}
			}else{
				$db->setQuery("Select id from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
				$value_id = $db->loadResult();
				$row->id = $value_id;
				$row->value = $value;
				if($translatable){
					foreach ($languages as $language)
					{
						$sef = $language->sef;
						$row->{'value_'.$sef} = $_POST[$field->field_name.'_'.$sef];
					}
				}
				if (!$row->store()) 
				{
					//JError::raiseError(500, $row->getError() );
					throw new Exception($row->getError(), 500);
				}
			}
		}
	}


	/**
	 * Save checkbox
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function saveField_Checkbox($field,$pid){
		global $mainframe,$jinput;
		$db = Factory::getDBO();
		$valueArr = $jinput->get($field->field_name,array(),'ARRAY');
		$valueArr = ArrayHelper::toInteger($valueArr);
		$db->setQuery("DELETE FROM #__osrs_property_field_opt_value WHERE pid = '$pid' AND fid = '$field->id'");
		$db->execute();
		if(count($valueArr) > 0){
			for($i=0;$i<count($valueArr);$i++){
				$oid = $valueArr[$i];
				$db->setQuery("INSERT INTO #__osrs_property_field_opt_value (id, pid,fid,oid) VALUES (NULL,'$pid','$field->id','$oid')");
				$db->execute();
			}
		}
	}


	/**
	 * Manage field options in the backend
	 *
	 * @param unknown_type $fid
	 */
	static function manageFieldOptions($fid,$div_name,$type){
		global $mainframe,$languages;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_field_options where field_id = '$fid' order by ordering");
		$fields = $db->loadObjectList();
		$translatable = Multilanguage::isEnabled() && count($languages);
		$default_language = OSPHelper::getDefaultLanguage();
		$default_language = substr($default_language,0,2);
		?>
		<table  width="100%" class="admintable" style="border:1px solid #CCC !important;">
			<tr>
				<td width="100%" colspan="2" align="left">
					<b>
						<?php echo Text::_('OS_ADD_NEW_OPT');?>
					</b>
				</td>
			</tr>
			<tr>
				<td class="key" width="30%" valign="top">
					<?php echo Text::_('OS_FIELD_OPTION')?>
				</td>
				<td width="70%">
					<?php
					if($translatable){
					?>
					<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $default_language.'.png'; ?>" />&nbsp;
					<?php
					}
					?>
					<input type="text" class="input-small form-control ishort" name="option_name_<?php echo $default_language.$type?>" id="option_name_<?php echo $default_language.$type?>" size="30"><BR />
					<?php
					$str = $default_language."|";
					if($translatable){
						$i = 0;
						foreach ($languages as $language) {
							$sef = $language->sef;
							$str .= $sef."|";
							?>
							<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $sef.'.png'; ?>" />&nbsp;
							<input type="text" class="input-small form-control ishort" name="option_name_<?php echo $sef?><?php echo $type?>" id="option_name_<?php echo $sef?><?php echo $type?>" size="30" />
							<BR />
							<?php
							$i++;
						}
					}
					$str = substr($str,0,strlen($str)-1);
					?>
					<input type="hidden" name="languages" id="languages" value="<?php echo $str?>" />
					<input type="button" class="btn btn-info" value="<?php echo Text::_('Save')?>" onclick="javascript:saveOption(<?php echo $fid?>,'<?php echo $div_name?>','<?php echo $type;?>');">
				</td>
			</tr>
		</table>
		<table  width="100%" class="adminlist">
			<thead>
				<th width="5%">
					#
				</th>
				<th width="20%">
					<?php echo Text::_('OS_OPTIONS')?> 
					<?php
					if($translatable){
					?>
					<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $default_language.'.png'; ?>" />
					<?php
					}
					?>
				</th>
				<?php
				if($translatable){
					$i = 0;
					foreach ($languages as $language) {
						$sef = $language->sef;
						?>
						<th width="20%">
							<?php echo Text::_('OS_OPTIONS')?> <img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $sef.'.png'; ?>" />
						</th>
						<?php
					}
				}
				?>
				<th width="15%">
					<?php echo Text::_('OS_ORDERING')?>
				</th>
				<th width="15%">
					<?php echo Text::_('OS_SAVE_CHANGE')?>
				</th>
				<th width="10%">
					<?php echo Text::_('OS_REMOVE')?>
				</th>
			</thead>
			<tbody>
				<?php
				$k=0;
				for($i=0;$i<count($fields);$i++){
					$field = $fields[$i];
					?>
					<tr class="rows<?php echo $k?>">
						<td width="5%" align="center">
							<?php
							echo $i + 1;
							?>
						</td>
						<td align="center">
							<input type="text" class="input-small form-control ishort" name="option_<?php echo $default_language?><?php echo $field->id?><?php echo $type?>" id="option_<?php echo $default_language?><?php echo $field->id?><?php echo $type?>" value="<?php echo $field->field_option?>" size="30">
						</td>
						<?php
						if($translatable){
							foreach ($languages as $language) {
								$sef = $language->sef;
								?>
								<td align="center">
									<input type="text" class="input-small form-control ishort" name="option_<?php echo $sef?><?php echo $field->id?><?php echo $type?>" id="option_<?php echo $sef?><?php echo $field->id?><?php echo $type?>" value="<?php echo $field->{'field_option_'.$sef}?>" size="30" />
								</td>
								<?php
							}
						}
						?>
						<td align="center">
							<input type="text" class="input-mini form-control imini" name="ordering_<?php echo $field->id?><?php echo $type?>" id="ordering_<?php echo $field->id?><?php echo $type?>" value="<?php echo $field->ordering?>" size="5">
						</td>
						<td align="center">
							<a href="javascript:saveChange(<?php echo $field->id;?>,<?php echo $fid;?>,'<?php echo $div_name;?>','<?php echo $type?>');">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
								  <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
								  <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
								</svg>
							</a>
						</td>
						<td align="center">
							<a href="javascript:removeOption(<?php echo $field->id;?>,<?php echo $fid;?>,'<?php echo $div_name;?>','<?php echo $type?>');">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
								  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
								</svg>
							</a>
						</td>
					</tr>
					<?php
					$k = 1-$k;
				}
				?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Save new option
	 *
	 * @param unknown_type $options
	 * @param unknown_type $fid
	 */
	static function saveNewOption($options,$fid){
		global $mainframe;
		$db = Factory::getDbo();
		$optionArr = explode("\n",$options);
		if(count($optionArr) > 0){
			for($i=0;$i<count($optionArr);$i++){
				$opt = $optionArr[$i];
				$opt = addslashes($opt);
				$db->setQuery("Select ordering from #__osrs_extra_field_options where field_id = '$fid' order by ordering limit 1");
				$ordering = $db->loadResult();
				$ordering = intval($ordering) + 1;
				$db->setQuery("INSERT INTO #__osrs_extra_field_options (id, field_id,field_option,ordering) VALUES (NULL,'$fid','$opt','$ordering')");
				$db->execute();
			}
		}
	}
}



/**
 * class print by hungvd
 * Show the fields in print page
 *
 */
class HelperOspropertyFieldsPrint{
	/**
	 * Show fields
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField($field,$pid){
		global $mainframe;
		ob_start();
		
		switch ($field->field_type){
			case "text":
				HelperOspropertyFieldsPrint::showField_Text($field,$pid);
				break;
			case "date":
				HelperOspropertyFieldsPrint::showField_Date($field,$pid);
				break;
			case "textarea":
				HelperOspropertyFieldsPrint::showField_Textarea($field,$pid);
				break;
			case "radio":
				HelperOspropertyFieldsPrint::showField_Radio($field,$pid);
				break;
			case "checkbox":
				HelperOspropertyFieldsPrint::showField_Checkbox($field,$pid);
				break;
			case "singleselect":
				HelperOspropertyFieldsPrint::showField_Singleselect($field,$pid);
				break;
			case "multipleselect":
				HelperOspropertyFieldsPrint::showField_Multipleselect($field,$pid);
				break;
		}

		$field_value = ob_get_contents();
		ob_end_clean();
		//echo $field_value;
		return $field_value;
	}

	/**
	 * Show TEXT field
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Text($field,$pid){
		global $mainframe,$languages;
		$translatable = Multilanguage::isEnabled() && count($languages);
		$db = Factory::getDBO();
		if($pid > 0){
			$db->setQuery("Select * from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
			$value = $db->loadObject();
		}
		if($field->clickable == 1)
		{
			if($field->value_type == 0)
			{
				if($value != "")
				{
					$fieldvalue = OSPHelper::getLanguageFieldValue($value,'value');
					if(strpos($fieldvalue,'@') !== false)
					{
						$value = "<a href='mailto:".OSPHelper::getLanguageFieldValue($value,'value')."' target='_blank'>".OSPHelper::getLanguageFieldValue($value,'value')."</a>";
					}
					else
					{
						$value = "<a href='".OSPHelper::getLanguageFieldValue($value,'value')."' target='_blank'>".OSPHelper::getLanguageFieldValue($value,'value')."</a>";
					}
				}
			}
			elseif($field->value_type == 1 && (int) $value > 0)
			{
				$value = "<a href='".$value->value_integer."' target='_blank'>".$value->value_integer."</a>";
			}
			elseif($field->value_type == 2 && (float) $value > 0)
			{
				$value = "<a href='".$value->value_decimal."' target='_blank'>".$value->value_decimal."</a>";
			}
		}
		else
		{
			if($field->value_type == 0){
				$value = OSPHelper::getLanguageFieldValue($value,'value');
			}elseif($field->value_type == 1){
				if($value->value_integer > 0)
				{
					$value = (int)$value->value_integer;
				}
				else
				{
					$value = "";
				}
			}elseif($field->value_type == 2){
				if($value->value_decimal > 0)
				{
					$value = $value->value_decimal;
				}
				else
				{
					$value = "";
				}
			}
		}
		echo $value;
	}

	/**
	 * Show field date
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Date($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();
		if($pid > 0){
			$db->setQuery("Select `value_date` from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
			$value = $db->loadResult();
		}
		if(($value == "") && ($field->default_value!="")){
			$value = $field->default_value;
		}
		echo $value;
	}


	/**
	 * Show Textarea
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Textarea($field,$pid){
		global $mainframe,$languages;
		$translatable = Multilanguage::isEnabled() && count($languages);
		$db = Factory::getDBO();
		if($pid > 0){
			$db->setQuery("Select * from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field->id'");
			$value = $db->loadObject();
		}
		$value = OSPHelper::getLanguageFieldValue($value,'value');
		echo $value;
	}

	/**
	 * Radio button
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Radio($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();
		if($pid > 0){
			$db->setQuery("Select a.* from #__osrs_extra_field_options as a inner join #__osrs_property_field_opt_value as b on b.oid = a.id where b.pid = '$pid' and b.fid = '$field->id'");
			$value = $db->loadObject();
			$value = OSPHelper::getLanguageFieldValue($value,'field_option');
		}
		echo $value;
	}


	/**
	 * Checkboxes fields
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Checkbox($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();
		$valueArr = array();
		if($pid > 0){
			$returnArr = array();
			$db->setQuery("Select a.* from #__osrs_extra_field_options as a inner join #__osrs_property_field_opt_value as b on b.oid = a.id where b.pid = '$pid' and b.fid = '$field->id'");
			$rows = $db->loadObjectList();
			if(count($rows) > 0){
				for($i=0;$i<count($rows);$i++){
					$row = $rows[$i];
					//$returnArr[$i] = $row->field_option;
					$returnArr[$i] = trim(OSPHelper::getLanguageFieldValue($row,'field_option'));
				}
			}
		}
		echo implode(", ",$returnArr);
	}

	/**
	 * Single select
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Singleselect($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();
		if($pid > 0){
			$db->setQuery("Select a.* from #__osrs_extra_field_options as a inner join #__osrs_property_field_opt_value as b on b.oid = a.id where b.pid = '$pid' and b.fid = '$field->id'");
			//echo $db->getQuery();
			$value = $db->loadObject();
			
			$value = OSPHelper::getLanguageFieldValue($value,'field_option');
		}
		echo $value;
	}

	/**
	 * Multple select
	 *
	 * @param unknown_type $field
	 * @param unknown_type $pid
	 */
	static function showField_Multipleselect($field,$pid){
		global $mainframe;
		$db = Factory::getDBO();
		$valueArr = array();
		if($pid > 0){
			$returnArr = array();
			$db->setQuery("Select a.* from #__osrs_extra_field_options as a inner join #__osrs_property_field_opt_value as b on b.oid = a.id where b.pid = '$pid' and b.fid = '$field->id'");
			$rows = $db->loadObjectList();
			if(count($rows) > 0){
				for($i=0;$i<count($rows);$i++){
					$row = $rows[$i];
					$returnArr[$i] = OSPHelper::getLanguageFieldValue($row,'field_option');
				}
			}
		}
		echo implode(",",$returnArr);
	}

	/**
	 * Check field data is existing or not ?
	 *
	 * @param unknown_type $group_id
	 * @param unknown_type $pid
	 */
	static function checkFieldDataExisting($group_id,$pid){
		global $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_extra_fields where group_id = '$group_id'");
		$fields = $db->loadObjectList();
		$hasValue = 0;
		if(count($fields) > 0){
			for($i=0;$i<count($fields);$i++){
				$field = $fields[$i];
				$db->setQuery("Select * from #__osrs_property_field_value where pro_id = '$pid' and `value` <> '' and field_id = '$field->id'");
				$value = $db->loadObject();
				if($field->value_type == 0){
					if($value != ""){
						$hasValue = 1;
					}
				}elseif($field->value_type == 1){
					if($value >= 0){
						$hasValue = 1;
					}
				}elseif($field->value_type == 2){
					if($value > 0){
						$hasValue = 1;
					}
				}
			}
		}
		if($hasValue == 1){
			return true;
		}else{
			return false;
		}
	}
}
?>
