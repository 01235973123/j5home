<?php
/*------------------------------------------------------------------------
# cron.php - OS Property 
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2016 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// No direct access.
// defined('_JEXEC') or die;
//define('_JEXEC', true);
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
if (file_exists(dirname(__FILE__) . '/defines.php')) {
    include_once dirname(__FILE__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(dirname(__DIR__)));
    require_once JPATH_BASE.'/includes/defines.php';
}

define('REPLACE_PATH', str_replace(JPATH_BASE,'',dirname(__FILE__)));

require_once JPATH_BASE.'/includes/framework.php';

jimport('joomla.database.database');
jimport('joomla.application.input');
jimport('joomla.event.dispatcher');
jimport('joomla.application.input');
jimport('joomla.event.dispatcher');
jimport('joomla.environment.response');
jimport('joomla.environment.uri');
jimport('joomla.log.log');
jimport('joomla.application.component.helper');
jimport('joomla.methods');
jimport('joomla.factory');

$app = JFactory::getApplication('site');
$app->initialise();
include (JPATH_ROOT.'/components/com_osproperty/classes/template.class.php');
include (JPATH_ROOT.'/components/com_osproperty/helpers/common.php');
include (JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
include (JPATH_ROOT.'/components/com_osproperty/helpers/cronhelper.php');
include (JPATH_ROOT.'/administrator/components/com_osproperty/helpers/extrafields.php');

$configClass = OSPHelper::loadConfig();

if($configClass['active_alertemail'] == 1) {
    $max_properties_per_time = $configClass['max_properties_per_time'];
    if($max_properties_per_time == ''){
        $max_properties_per_time = 100;
    }
    $max_lists_per_time = $configClass['max_lists_per_time'];
    if($max_lists_per_time == ''){
        $max_lists_per_time = 50;
    }
    $max_email_per_time = $configClass['max_email_per_time'];
    if($max_email_per_time == ''){
        $max_email_per_time = 50;
    }
	$configClass = OSPHelper::loadConfig();
    $root_link = $configClass['live_site'];
    $db = JFactory::getDbo();
    
    $db->setQuery("Select b.* from #__osrs_new_properties as a inner join #__osrs_properties as b on a.pid = b.id where a.processed = '0' and b.published = '1' and b.approved = '1' limit $max_properties_per_time");
    $rows = $db->loadObjectList();
    if (count($rows) > 0) {
        foreach ($rows as $row) {
            //width each product, check all saved list in database
            $db->setQuery("Select id from #__osrs_user_list where receive_email = '1' and id not in (Select list_id from #__osrs_list_properties where pid = '$row->id') limit $max_lists_per_time");
            $lists = $db->loadObjectList();
            if (count($lists) == 0) {
                //update new_properties table
                $db->setQuery("Update #__osrs_new_properties set processed = '1' where pid = '$row->id'");
                $db->execute();
            } else {
                foreach ($lists as $list) {
                    OSPHelperCron::checkProperty($row, $list);
                }
            }
        }
    }
    //send alert email
    $query = "Select distinct(list_id) from #__osrs_list_properties where sent_notify < 2 limit $max_email_per_time";
    $db->setQuery($query);
    $lists = $db->loadObjectList();
    $mailer = JFactory::getMailer();
    if (count($lists) > 0) {
        foreach ($lists as $list) {
            $db->setQuery("Select * from #__osrs_user_list where id = '$list->list_id'");
            $saved_list = $db->loadObject();
            $user = $saved_list->user_id;
            $user = JFactory::getUser($user);
            $lang = $saved_list->lang;
            $default_lang = OSPHelper::getDefaultLanguage();
            if ($lang == "") {
                $lang = $default_lang;
            }
            $suffix = "";
            if ($lang != $default_lang) {
                $langArr = explode("-", $lang);
                $lang = $langArr[0];
                $suffix = "_" . $lang;
            }
            $language = JFactory::getLanguage();
            $language->load('com_osproperty', JPATH_ROOT, $lang);

            $query = "Select a.* from #__osrs_properties as a"
                . " inner join #__osrs_states as b on b.id = a.state"
                . " inner join #__osrs_cities as c on c.id = a.city"
                . " inner join #__osrs_list_properties as d on d.pid = a.id"
                . " where a.published = '1' and a.approved = '1' and d.list_id = '$list->list_id'";
            $db->setQuery($query);
            $properties = $db->loadObjectList();
            if (count($properties) > 0) {
                foreach ($properties as $property) {
                    $db->setQuery("Select * from #__osrs_photos where pro_id = '$property->id'");
                    $photo = $db->loadObject();
                    $image = $photo->image;
                    if (($image != "") and (file_exists(JPATH_ROOT . '/images/osproperty/properties/' . $property->id . '/thumb/' . $image))) {
                        $property->image = $root_link . 'images/osproperty/properties/' . $property->id . '/thumb/' . $image;
                    } else {
                        $property->image = $root_link . 'media/com_osproperty/assets/images/nopropertyphoto.png';
                    }

                    $db->setQuery("Select * from #__osrs_types where id = '$property->pro_type'");
                    $property->property_type = $db->loadObject();

                    $property->detailsurl = $root_link . 'index.php?option=com_osproperty&task=property_details&id=' . $property->id;
                }

                jimport('joomla.filesystem.file');
                if (JFile::exists(JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_osproperty/layouts/alertcontent.php')) {
                    $tpl = new OspropertyTemplate(JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_osproperty/layouts/');
                } else {
                    $tpl = new OspropertyTemplate(JPATH_ROOT . '/components/com_osproperty/helpers/layouts/');
                }
                $tpl->set('properties', $properties);
                $tpl->set('saved_list', $saved_list);
                $tpl->set('configClass', $configClass);
                $body = $tpl->fetch("alertcontent.php");

                $db->setQuery("Select * from #__osrs_emails where email_key like 'email_alert'");
                $email = $db->loadObject();

                $title = OSPHelper::getLanguageFieldValueBackend($email, 'email_title', $suffix);
                $content = OSPHelper::getLanguageFieldValueBackend($email, 'email_content', $suffix);

                $content = str_replace("{listname}", $saved_list->list_name, $content);
                $content = str_replace("{new_properties}", $body, $content);
                $cancel_link = $root_link . "index.php?option=com_osproperty&task=property_cancelalertemail&list_id=" . md5($list->list_id) . "|" . $list->list_id;
                $cancel_link = "<a href='$cancel_link' target='_blank'>" . $cancel_link . "</a>";
                $content = str_replace("{cancel_alert_email_link}", $cancel_link, $content);

                $config = new JConfig();
                $mailfrom = $config->mailfrom;
                $fromname = $config->fromname;

                //print_r($config);
				try
				
				{
					if ($mailer->sendMail($mailfrom, $fromname, $user->email, $title, $content, 1)) {
						//update the sent status for each properties of this list
						foreach ($properties as $property) {
							$db->setQuery("Update #__osrs_list_properties set sent_notify = '2' where pid = '$property->id' and list_id = '$list->list_id'");
							$db->execute();
						}
					}
				}
				catch (Exception $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
            }
        }
    }
}
?>