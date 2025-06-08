<?php
/**
 * @version               1.22.0
 * @package               Joomla
 * @subpackage            OS Property
 * @author                Tuan Pham Ngoc
 * @copyright             Copyright (C) 2012 - 2023 Ossolution Team
 * @license               GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Cache\Cache;
use Joomla\Registry\Registry;

class plgSystemOspcrontask extends CMSPlugin
{
	/**
     * Application object.
     *
     * @var    JApplicationCms
     */
    protected $app;

    /**
     * Database object.
     *
     * @var    JDatabaseDriver
     */
    protected $db;

    /**
     * Constructor.
     *
     * @param   object  &$subject  The object to observe.
     * @param   array    $config   An optional associative array of configuration settings.
     */
    public function __construct(&$subject, $config = [])
    {
        if (!file_exists(JPATH_ROOT . '/components/com_osproperty/osproperty.php')) 
		{
            return;
        }
        parent::__construct($subject, $config);
    }

    /**
     * Send reminder to registrants
     *
     * @return void
     * @throws Exception
     */
    public function onAfterRespond()
    {
		
        if (!$this->app) {
            return;
        }


        if (!$this->canRun()) {
            return;
        }

		include (JPATH_ROOT.'/components/com_osproperty/classes/template.class.php');
		include (JPATH_ROOT.'/components/com_osproperty/helpers/common.php');
		include (JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
		include (JPATH_ROOT.'/components/com_osproperty/helpers/cronhelper.php');
		include (JPATH_ROOT.'/administrator/components/com_osproperty/helpers/extrafields.php');
		
		
        $cacheTime = (int)$this->params->get('cache_time', 20) * 60; // 60 minutes
		
        // We only need to check and store last runtime if cron job is not configured
        if (!trim($this->params->get('trigger_reminder_code', ''))
            && ! OspropertyHelperPlugin::checkAndStoreLastRuntime($this->params, $cacheTime, $this->_name)) 
		{
            return;
        }
		global $configClass;
		$db = Factory::getDbo();
        $configClass = OSPHelper::loadConfig();

		if($configClass['active_alertemail'] == 1) 
		{
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
			//$db = Factory::getDbo();
			
			$db->setQuery("Select b.* from #__osrs_new_properties as a inner join #__osrs_properties as b on a.pid = b.id where a.processed = '0' and b.published = '1' and b.approved = '1' limit $max_properties_per_time");
			$rows = $db->loadObjectList();
			if (count($rows) > 0) 
			{
				foreach ($rows as $row) 
				{
					//width each product, check all saved list in database
					$db->setQuery("Select id from #__osrs_user_list where receive_email = '1' and id not in (Select list_id from #__osrs_list_properties where pid = '$row->id') limit $max_lists_per_time");
					$lists = $db->loadObjectList();
					if (count($lists) == 0) 
					{
						//update new_properties table
						$db->setQuery("Update #__osrs_new_properties set processed = '1' where pid = '$row->id'");
						$db->execute();
					} 
					else 
					{
						foreach ($lists as $list) 
						{
							OSPHelperCron::checkProperty($row, $list);
						}
					}
				}
			}
			//send alert email
			$query = "Select distinct(list_id) from #__osrs_list_properties where sent_notify = '1' limit $max_email_per_time";
			$db->setQuery($query);
			$lists = $db->loadObjectList();
			$mailer = Factory::getMailer();
			if (count($lists) > 0) 
			{
				foreach ($lists as $list) 
				{
					$db->setQuery("Select * from #__osrs_user_list where id = '$list->list_id'");
					$saved_list = $db->loadObject();
					$user = $saved_list->user_id;
					$user = Factory::getUser($user);
					$lang = $saved_list->lang;
					$default_lang = OSPHelper::getDefaultLanguage();
					if ($lang == "") 
					{
						$lang = $default_lang;
					}
					$suffix = "";
					if ($lang != $default_lang) {
						$langArr = explode("-", $lang);
						$lang = $langArr[0];
						$suffix = "_" . $lang;
					}
					$language = Factory::getLanguage();
					$language->load('com_osproperty', JPATH_ROOT, $lang);

					$query = "Select a.* from #__osrs_properties as a"
						. " inner join #__osrs_states as b on b.id = a.state"
						. " inner join #__osrs_cities as c on c.id = a.city"
						. " inner join #__osrs_list_properties as d on d.pid = a.id"
						. " where a.published = '1' and a.approved = '1' and d.list_id = '$list->list_id'";
					$db->setQuery($query);
					$properties = $db->loadObjectList();
					if (count($properties) > 0) 
					{
						foreach ($properties as $property) 
						{
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
						if (File::exists(JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_osproperty/layouts/alertcontent.php')) {
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
							Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
						}
					}
				}
			}
		}
	}

	    

    /**
     * Method to check whether this plugin should be run
     *
     * @return bool
     */
    private function canRun()
    {
        if (!$this->app) 
		{
            return false;
        }

        // If trigger reminder code is set, we will only process sending reminder from cron job
        if (trim($this->params->get('trigger_reminder_code', ''))
            && trim($this->params->get('trigger_reminder_code', '')) != $this->app->input->getString('trigger_reminder_code')) {
            return false;
        }

        return true;
    }
}
