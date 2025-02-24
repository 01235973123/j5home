<?php
/*------------------------------------------------------------------------
# configuration.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\ToolbarHelper;

class TextOs
{
	static function _($string)
	{
		if ($string != '')
		{
			$string = str_replace(",","",$string);
			$string = str_replace(".","",$string);
			$string = str_replace("'","",$string);
			
			$string = str_replace(" - ","_",$string);
			$string = str_replace("-","_",$string);
			$string = str_replace(" ","_",$string);
			
			$string = str_replace("?","",$string);
			$string = str_replace("/","",$string);
			$string = str_replace("(","",$string);
			$string = str_replace(")","",$string);
			$string = strtoupper('OS_'.$string);
			
		}
		return Text::_($string);
	}
}


class OspropertyConfiguration
{
	/**
	 * default function 
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task){
		global $mainframe;
		switch ($task){
			case "configuration_list":
				OspropertyConfiguration::configuration_list($option);
			break;
			case 'configuration_cancel':
				$mainframe->redirect("index.php?option=$option");
			break;	
			case "configuration_save":
				OspropertyConfiguration::configuration_save($option,$task);
			break;
			case "configuration_apply":
				OspropertyConfiguration::configuration_save($option,$task);
			break;
            case "configuration_help":
                OspropertyConfiguration::helpLayout($option,$task);
            break;
            case "configuration_connectfb":
				if (version_compare(phpversion(), '5.4.0', 'ge')) {
					OspropertyConfiguration::connectFb($option);
				}
            break;
			case "configuration_changecurrencystatus":
				OspropertyConfiguration::changecurrencystatus($option);
			break;
		}
	}
	
	/**
	 * configuration list
	 *
	 * @param unknown_type $option
	 */
	static function configuration_list($option){
		global $mainframe;
		HTMLHelper::_('jquery.framework');
		OSPHelper::loadTooltip();
		if (version_compare(JVERSION, '4.0.0-dev', 'lt'))
		{
			HTMLHelper::_('behavior.tabstate');
		}
		HTMLHelper::_('script', 'jui/cms.js', false, true);
		$db = Factory::getDBO();
		$db->setQuery('SELECT * FROM #__osrs_configuration ');
		$configs = array();
		foreach ($db->loadObjectList() as $config) {
			$configs[$config->fieldname] = $config->fieldvalue;
		}

		$db->setQuery("Select * from #__osrs_currencies order by currency_name");
		$used_currencies = $db->loadObjectList();

		HTML_OspropertyConfiguration::configurationHTML($option,$configs,$used_currencies);
	}
	
	/**
	 * save configuation
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function configuration_save($option,$task){
		global $mainframe,$languages,$jinput;
		$db = Factory::getDbo();
		
		$agentArr = array();
		$db->setQuery("Select user_id from #__osrs_agents");
		$agents = $db->loadOBjectList();
		if(count($agents) > 0){
			for($i=0;$i<count($agents);$i++){
				$agentArr[] = $agents[$i]->user_id;
			}
		}
		
		$configuration = $jinput->get('configuration',array(),'array');
		if($configuration['comment_active_comment'] == 0)
		{
			$configuration['show_rating'] = 0;
			$configuration['registered_user_write_comment'] = 0;
			$configuration['allow_edit_comment'] = 0;
		}
		
		$agent_joomla_group_id = $configuration['agent_joomla_group_id'];
		$db->setQuery("Select fieldvalue from #__osrs_configuration where fieldname like 'agent_joomla_group_id'");
		$old_agent_joomla_group_id = $db->loadResult();
		if($old_agent_joomla_group_id != ""){
			if($old_agent_joomla_group_id != $agent_joomla_group_id){
				if(count($agentArr) > 0){
					$db->setQuery("Delete from #__user_usergroup_map where user_id in (".implode(",",$agentArr).") and group_id = '$old_agent_joomla_group_id'");
					$db->execute();
				}
			}
		}
		if($agent_joomla_group_id != ""){
			for($i=0;$i<count($agentArr);$i++){
				$agent_id = $agentArr[$i];
				$db->setQuery("Select count(user_id) from #__user_usergroup_map where user_id = '$agent_id' and group_id = '$agent_joomla_group_id'");
				$count = $db->loadResult();
				if($count == 0){
					$db->setQuery("Insert into #__user_usergroup_map (user_id,group_id) values ('$agent_id','$agent_joomla_group_id')");
					$db->execute();
				}
			}
		}
		
		$companyArr = array();
		$db->setQuery("Select user_id from #__osrs_companies");
		$companies = $db->loadOBjectList();
		if(count($companies) > 0){
			for($i=0;$i<count($companies);$i++){
				$companyArr[] = $companies[$i]->user_id;
			}
		}
		
		
		$company_joomla_group_id = $configuration['company_joomla_group_id'];
		$db->setQuery("Select fieldvalue from #__osrs_configuration where fieldname like 'company_joomla_group_id'");
		$old_company_joomla_group_id = $db->loadResult();
		if($old_company_joomla_group_id != ""){
			if($old_company_joomla_group_id != $company_joomla_group_id){
				if(count($companyArr) > 0){
					$db->setQuery("Delete from #__user_usergroup_map where user_id in (".implode(",",$companyArr).") and group_id = '$old_company_joomla_group_id'");
					$db->execute();
				}
			}
		}
		if($company_joomla_group_id != ""){
			for($i=0;$i<count($companyArr);$i++){
				$company_id = $companyArr[$i];
				$db->setQuery("Select count(user_id) from #__user_usergroup_map where user_id = '$company_id' and group_id = '$company_joomla_group_id'");
				$count = $db->loadResult();
				if($count == 0){
					$db->setQuery("Insert into #__user_usergroup_map (user_id,group_id) values ('$company_id','$company_joomla_group_id')");
					$db->execute();
				}
			}
		}

        $db->setQuery("Select * from #__osrs_types order by ordering");
        $property_types = $db->loadObjectList();
        for($i=0;$i<count($property_types);$i++) {
            $property_type = $property_types[$i];
            $type = $jinput->getInt('type'.$property_type->id,0);
            if($type == 1){
                $configuration['type'.$property_type->id] = 1;
            }else{
                $valueTemp = array();
                $valueTemp[] = 0;
                $min = $jinput->getInt('min'.$property_type->id,0);
                $max = $jinput->getInt('max'.$property_type->id,0);
                $step = $jinput->getInt('step'.$property_type->id,0);
                $valueTemp[] = $min;
                $valueTemp[] = $max;
                $valueTemp[] = $step;
                $value = implode("|",$valueTemp);
                $configuration['type'.$property_type->id] = $value;
            }
        }

		if($configuration['limit_upload_pdfs'] > 10)
		{
			$configuration['limit_upload_pdfs'] = 10;
		}
		
		foreach ($configuration as $fieldname => $fieldvalue) {
			if (is_array($fieldvalue)) $fieldvalue = implode(',',$fieldvalue);
			$fieldvalue = addslashes($fieldvalue);
			$db->setQuery("SELECT count(id) FROM #__osrs_configuration WHERE `fieldname` = '$fieldname'");
			if ($db->loadResult()){
				$db->setQuery("UPDATE #__osrs_configuration SET `fieldvalue` = '$fieldvalue' WHERE `fieldname` = '$fieldname'");
				$db->execute();
			}else{
				$db->setQuery("INSERT INTO #__osrs_configuration (id, fieldname, fieldvalue) VALUES ('0','$fieldname','$fieldvalue')");
				$db->execute();
			}
		}

		
		$show_top_menus_in = $jinput->get('show_top_menus_in',array(),'array');
		//	if(count($show_top_menus_in) > 0){
		$show_top_menus_in = implode("|",$show_top_menus_in);
		$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$show_top_menus_in' WHERE fieldname like 'show_top_menus_in'");
		$db->execute();
		//}
		
		$db->setQuery("Select fieldvalue from #__osrs_configuration where fieldname like 'general_unpublished_days'");
		$default_currency = $db->loadResult();
		if(intval($default_currency) > 0){
			$db->setQuery("Update #__osrs_properties set curr = '$default_currency' where curr = '0'");
			$db->execute();
		}
		
		//Upload watermark
		$remove_watermark_photo = $jinput->getInt('remove_watermark_photo',0);
		if(is_uploaded_file($_FILES['watermark_photo']['tmp_name'])){
			$filename    = $_FILES['watermark_photo']['name'];
			$filenameArr = explode(".",$filename);
			$ext         = $filenameArr[count($filenameArr)-1];
			$filename    = "ospwatermark.".$ext;
			move_uploaded_file($_FILES['watermark_photo']['tmp_name'],JPATH_ROOT.DS."images".DS.$filename);
			$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$filename' WHERE fieldname like 'watermark_photo'");
			$db->execute();
		}elseif($remove_watermark_photo == 1){
			$filename 	 =  "";
			$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$filename' WHERE fieldname like 'watermark_photo'");
			$db->execute();
		}

		$db->setQuery("Select count(id) from #__osrs_configuration where fieldname like 'watermark_photo_thumb'");
		$count = $db->loadResult();
		if($count == 0){
			$db->setQuery("Insert into #__osrs_configuration (id,fieldname,fieldvalue) values (NULL,'watermark_photo_thumb','')");
			$db->execute();
		}
		$remove_watermark_photo = $jinput->getInt('remove_watermark_photo_thumb',0);
		if(is_uploaded_file($_FILES['watermark_photo_thumb']['tmp_name'])){
			$filename    = $_FILES['watermark_photo_thumb']['name'];
			$filenameArr = explode(".",$filename);
			$ext         = $filenameArr[count($filenameArr)-1];
			$filename    = "ospwatermark_thumb_.".$ext;
			move_uploaded_file($_FILES['watermark_photo_thumb']['tmp_name'],JPATH_ROOT.DS."images".DS.$filename);
			$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$filename' WHERE fieldname like 'watermark_photo_thumb'");
			$db->execute();
		}elseif($remove_watermark_photo == 1){
			$filename 	 =  "";
			$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$filename' WHERE fieldname like 'watermark_photo_thumb'");
			$db->execute();
		}

		$db->setQuery("Select count(id) from #__osrs_configuration where fieldname like 'watermark_photo_original'");
		$count = $db->loadResult();
		if($count == 0){
			$db->setQuery("Insert into #__osrs_configuration (id,fieldname,fieldvalue) values (NULL,'watermark_photo_original','')");
			$db->execute();
		}
		$remove_watermark_photo = $jinput->getInt('remove_watermark_photo_original',0);
		if(is_uploaded_file($_FILES['watermark_photo_original']['tmp_name'])){
			$filename    = $_FILES['watermark_photo_original']['name'];
			$filenameArr = explode(".",$filename);
			$ext         = $filenameArr[count($filenameArr)-1];
			$filename    = "ospwatermark_original_.".$ext;
			move_uploaded_file($_FILES['watermark_photo_original']['tmp_name'],JPATH_ROOT.DS."images".DS.$filename);
			$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$filename' WHERE fieldname like 'watermark_photo_original'");
			$db->execute();
		}elseif($remove_watermark_photo == 1){
			$filename 	 =  "";
			$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$filename' WHERE fieldname like 'watermark_photo_original'");
			$db->execute();
		}
		
		$adv_type_ids = $jinput->get('adv_type_ids',array(),'ARRAY');
		if(count($adv_type_ids) > 0){
			if(in_array(0,$adv_type_ids)){
				$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '0' WHERE fieldname LIKE 'adv_type_ids'");
				$db->execute();
			}else{
				$adv_type_ids1 = array();
				for($i=0;$i<count($adv_type_ids);$i++){
					if($adv_type_ids[$i] != 0){
						$adv_type_ids1[count($adv_type_ids1)] = $adv_type_ids[$i];
					}
				}
				$adv_type_ids = implode("|",$adv_type_ids1);
				$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$adv_type_ids' WHERE fieldname LIKE 'adv_type_ids'");
				$db->execute();
			}
		}
		
		$locator_type_ids = $jinput->get('locator_type_ids',array(),'ARRAY');
        //print_r($locator_type_ids);die();
		if(count($locator_type_ids) > 0){
			if(in_array(0,$locator_type_ids)){
				$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '0' WHERE fieldname LIKE 'locator_type_ids'");
				$db->execute();
			}else{
				$locator_type_ids1 = array();
				for($i=0;$i<count($locator_type_ids);$i++){
					if($locator_type_ids[$i] != 0){
						$locator_type_ids1[count($locator_type_ids1)] = $locator_type_ids[$i];
					}
				}
				$locator_type_ids = implode("|",$locator_type_ids1);
                //echo $locator_type_ids;die();
				$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$locator_type_ids' WHERE fieldname LIKE 'locator_type_ids'");
				$db->execute();
			}
		}

		$market_statuses = $jinput->get('market_status',array(),'ARRAY');
		if(count($market_statuses) > 0){
			$market_status = implode(",",$market_statuses);
			$db->setQuery("Select count(id) from #__osrs_configuration where fieldname like 'market_status'");
			$count = $db->loadResult();
			if($count > 0){
				$db->setQuery("Update #__osrs_configuration set fieldvalue = '$market_status' where fieldname like 'market_status'");
				$db->execute();
			}else{
				$db->setQuery("Insert into #__osrs_configuration (id,fieldname,fieldvalue) values (NULL,'market_status','$market_status')");
				$db->execute();
			}
		}
		
		
		$show_date_search_in = $jinput->get('show_date_search_in',array(),'ARRAY');
		if(count($show_date_search_in) > 0){
			if(in_array(0,$show_date_search_in)){
				$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '0' WHERE fieldname LIKE 'show_date_search_in'");
				$db->execute();
			}else{
				$show_date_search_in1 = array();
				for($i=0;$i<count($show_date_search_in);$i++){
					if($show_date_search_in[$i] != 0){
						$show_date_search_in1[count($show_date_search_in1)] = $show_date_search_in[$i];
					}
				}
				$show_date_search_in = implode("|",$show_date_search_in1);
				$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$show_date_search_in' WHERE fieldname LIKE 'show_date_search_in'");
				$db->execute();
			}
		}
		
		$db->setQuery("Select count(id) from #__osrs_configuration where fieldname like 'sold_property_types'");
		$count_sold = $db->loadResult();
		if($count_sold == 0){
			$db->setQuery("Insert into #__osrs_configuration (id,fieldname) values (NULL,'sold_property_types');");
			$db->execute();
		}
		$adv_type_ids = $jinput->get('sold_property_types',array(),'ARRAY');
		if(count($adv_type_ids) > 0){
			if(in_array(0,$adv_type_ids)){
				$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '0' WHERE fieldname LIKE 'sold_property_types'");
				$db->execute();
			}else{
				$adv_type_ids1 = array();
				for($i=0;$i<count($adv_type_ids);$i++){
					if($adv_type_ids[$i] != 0){
						$adv_type_ids1[count($adv_type_ids1)] = $adv_type_ids[$i];
					}
				}
				$adv_type_ids = implode("|",$adv_type_ids1);
				$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$adv_type_ids' WHERE fieldname LIKE 'sold_property_types'");
				$db->execute();
			}
		}else{
			$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '' WHERE fieldname LIKE 'sold_property_types'");
			$db->execute();
		}
		
		$image_code = $jinput->getString('image_code','');
		if($image_code != ""){
			$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '$image_code' WHERE fieldname LIKE 'image_background_color'");
			$db->execute();
		}

		$enable_cardtypes = $jinput->get('enable_cardtypes',array(),'ARRAY');
		$enable_cardtypes  = implode(",",$enable_cardtypes);
		$db->setQuery("Select count(id) from #__osrs_configuration where fieldname like 'enable_cardtypes'");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("Update #__osrs_configuration set fieldvalue = '$enable_cardtypes' where fieldname like 'enable_cardtypes'");
			$db->execute();
		}else{
			$db->setQuery("Insert into #__osrs_configuration (id,fieldname,fieldvalue) values (NULL,'enable_cardtypes','$enable_cardtypes')");
			$db->execute();
		}

		$allowed_subjects = $jinput->get('allowed_subjects',array(),'ARRAY');
		$allowed_subjects  = implode(",",$allowed_subjects);
		$db->setQuery("Select count(id) from #__osrs_configuration where fieldname like 'allowed_subjects'");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("Update #__osrs_configuration set fieldvalue = '$allowed_subjects' where fieldname like 'allowed_subjects'");
			$db->execute();
		}else{
			$db->setQuery("Insert into #__osrs_configuration (id,fieldname,fieldvalue) values (NULL,'allowed_subjects','$allowed_subjects')");
			$db->execute();
		}

	
		//some relate configure option
		//comment

		if (isset($configuration['custom_css']))
		{
			File::write(JPATH_ROOT . '/media/com_osproperty/assets/css/custom.css', trim($configuration['custom_css']));
		}

		$msg = Text::_("OS_CONFIGURE_OPTION_HAVE_BEEN_SAVED");
		$mainframe->enqueueMessage($msg);
		if ($task == 'configuration_save')
		{
			$mainframe->redirect("index.php?option=$option");
		}
		else
		{
			$mainframe->redirect("index.php?option=$option&task=configuration_list");
		}
	}

    /**
     * Return the configuration field checkboxes
     * @param $fieldname
     * @param $fieldvalue
     */
    public static function showCheckboxfield($fieldname,$value,$option1='',$option2='')
	{
		
        if($option1 == "")
		{
            $option1 = Text::_('JNO');
        }
        if($option2 == "")
		{
            $option2 = Text::_('JYES');
        }

        HTMLHelper::_('jquery.framework');
        $field = FormHelper::loadFieldType('Radio');

        $element = new SimpleXMLElement('<field />');
        $element->addAttribute('name', 'configuration['.$fieldname.']');

        if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
        {
            $element->addAttribute('layout', 'joomla.form.field.radio.switcher');
        }
        else
        {
            $element->addAttribute('class', 'radio btn-group btn-group-yesno');
        }

        $element->addAttribute('default', '0');

        $node = $element->addChild('option', $option1);
        $node->addAttribute('value', '0');

        $node = $element->addChild('option', $option2);
        $node->addAttribute('value', '1');

        $field->setup($element, $value);

		echo $field->input;
    }

    static function helpLayout($option,$task){
        ToolBarHelper::title(Text::_('JTOOLBAR_HELP'),"help");
        ?>
        <div class="row-fluid">
            <div class="span12" style="border:1px solid #DDD;padding:15px;border-radius:10px;-webkit-box-shadow: 6px 15px 26px -5px rgba(0,0,0,0.61);-moz-box-shadow: 6px 15px 26px -5px rgba(0,0,0,0.61);box-shadow: 6px 15px 26px -5px rgba(0,0,0,0.61);">
                OS Property official page: <a title="OS Property official page" href="http://joomdonation.com/joomla-extensions/os-property-joomla-real-estate.html" target="_blank">http://joomdonation.com/joomla-extensions/os-property-joomla-real-estate.html</a>
                <BR /><BR />
                Demo site: <a title="Demo site" href="https://demo.joomdonation.com/osproperty/" target="_blank">https://demo.joomdonation.com/osproperty/</a>
                <BR /><BR />
                Documentation: <a title="Documentation" href="http://docs.joomdonation.com/osproperty/" target="_blank">http://docs.joomdonation.com/osproperty/</a>
                <BR /><BR />
                If you have any questions regarding the OS Property, you can choose one of following ways to get the best supports:
                <BR />
                <ol>
                    <li>
                        Login to your account and
                        <a target="new" href="https://joomdonation.com/support-tickets.html">Submit a Support Ticket</a>
                    </li>
                    <li>
                        Leave questions on
                        <a target="new" href="https://joomdonation.com/forum/os-property.html">Forum</a>
                    </li>
                    <li>
                        Drop me an email to
                        <a href="mailto:contact@joomdonation.com">contact@joomdonation.com</a>
                    </li>
                </ol>
            </div>
        </div>
        <BR /><BR />
    <?php
    }

	public function connectFb($option){
		$input = Factory::getApplication()->input;
        $app_id = $input->get('app_id','','string');
        $app_secret = $input->get('app_secret','','string');
		include_once(JPATH_ROOT.'/components/com_osproperty/helpers/inc/osfacebook.php');
		OSPFacebook::fbConnect($app_id,$app_secret);
	}

	static function changecurrencystatus($option){
        global $jinput;
		$db = Factory::getDbo();
		$id = $jinput->getInt('id',0);
		$status = $jinput->getInt('status',0);
		$db->setQuery("Update #__osrs_currencies set published = '$status' where id = '$id'");
		$db->execute();
		if($status == 1){
			?>
			<a href="javascript:changePublishedStatus(0,<?php echo $id?>,'<?php echo Uri::base();?>')" title="<?php echo Text::_('OS_CLICK_HERE_TO_UNPUBLISH_CURRENCY');?>">
				<i class="icon-star" style="color:green !important;"></i>
			</a>
			
			<?php
		}else{
			?>
			
			<a href="javascript:changePublishedStatus(1,<?php echo $id?>,'<?php echo Uri::base();?>')" title="<?php echo Text::_('OS_CLICK_HERE_TO_PUBLISH_CURRENCY');?>">
				<i class="icon-star"  style="color:red !important;"></i>
			</a>
			
			<?php
		}
		exit();
	}

	/**
	 * Get bootstrapped style boolean input
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return string
	 */
	public static function getBooleanInput($name, $value)
	{
		$html = array();

		// Start the radio field output.
		$html[] = '<fieldset id="' . $name . '" class="radio btn-group btn-group-yesno">';

		// Yes Option
		$checked = ($value == 1) ? ' checked="checked"' : '';
		$html[]  = '<input type="radio" id="' . $name . '0" name="configuration[' . $name . ']" value="1"' . $checked . ' />';
		$html[]  = '<label for="' . $name . '0">' . Text::_('JYES') . '</label>';

		// No Option
		$checked = ($value == 0) ? ' checked="checked"' : '';
		$html[]  = '<input type="radio" id="' . $name . '1" name="configuration[' . $name . ']" value="0"' . $checked . ' />';
		$html[]  = '<label for="' . $name . '1">' . Text::_('JNO') . '</label>';

		// End the radio field output.
		$html[] = '</fieldset>';

		return implode($html);
	}
}
?>
