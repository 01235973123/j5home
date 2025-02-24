b<?php

    /**
     * @version        1.7.0
     * @package        Joomla
     * @subpackage     EDocman
     * @author         Tuan Pham Ngoc
     * @copyright      Copyright (C) 2011 - 2023 Ossolution Team
     * @license        GNU/GPL, see LICENSE.php
     */

    defined('_JEXEC') or die('Restricted access');

    use Joomla\CMS\Plugin\CMSPlugin;
    use Joomla\CMS\Uri\Uri;
    use Joomla\CMS\Factory;

    class plgEDocmanNotificationByCategory extends CMSPlugin
    {
        public function __construct(&$subject, $config)
        {
            parent::__construct($subject, $config);
        }

        /**
         * Send notification according to plugin settings
         *
         * @param $context
         * @param $row the document object, you can access to any information of this document via this object
         * @param $isNew
         */

        public function onDocumentAfterSave($context, $row, $isNew)
        {
            require_once JPATH_ROOT . '/components/com_edocman/helper/route.php';
            require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';
            EDocmanHelper::loadLanguage();

            if ((int)$row->published == 0) {
                return;
            }

            $disable_sending = $this->params->get('disable_sending', 0);
            $item_id     = $this->params->get('item_id', 0);

            //disabling to send email to assigned documents
            if ($disable_sending == 1 && (($row->user_ids != "" && $row->user_ids > 0) || $row->owner_group_ids != "")) {
                return;
            }

            //Try to find notify group ids from category settings, if not found, we will fall back to plugin settings
            if ($context == "com_edocman.upload") {
                $categoryId = $row->getMainCategoryId();
            } else {
                $data   = Factory::getApplication()->input->get('jform', array(), 'array');
                $categoryId = (int) $data['category_id'];
            }

            $category       = self::getMainCategory($categoryId);

            if (!$category->send_email_document) {
                return;
            }

            if ($category->accesspicker) {
                $groupIds = self::getGroupsByCategory($category->level);
            } else {
                $groupIds = self::getUserGroupsByAccessLevel($category->access);
            }

            //If no groups selected, don't process
            if (!count($groupIds)) {
                return;
            }
            $mailer    = Factory::getMailer();
            $config    = Factory::getConfig();
            $fromEmail = $config->get('mailfrom');
            $fromName  = $config->get('fromname');

            $db    = Factory::getDbo();
            $query = $db->getQuery(true);

            $query->select('email')->from($db->quoteName('#__edocman_unsubscribe_emails'));
            $db->setQuery($query);
            $unsubscribeEmails = $db->loadColumn(0);
            $query->clear();

            $allow_sending = 1;
            if ($this->params->get('disable_sending') && ($row->user_ids != "" && $row->user_ids > 0)) {
                $allow_sending = 0;
            }

            if ($isNew && $this->params->get('new_document_notification') && ($allow_sending == 1)) {
                // Send new document upload notification here
                $subject = $category->new_document_email_subject;
                $body    = $category->new_document_email_body;
                // Get uploaded user
                $query->select('username, name, email')
                    ->from('#__users')
                    ->where('id = ' . (int) $row->created_user_id);
                $db->setQuery($query);
                $createdUser = $db->loadObject();

                $replaces                          = [];
                $replaces['DOCUMENT_TITLE']        = $row->title;
                $replaces['DOCUMENT_LINK']         = self::getSiteUrl() . EDocmanHelperRoute::getDocumentRoute($row->id, 0, $item_id);
                $replaces['CATEGORY_TITLE']        = $category->title;
                $replaces['CREATED_USER_USERNAME'] = $createdUser->username;
                $replaces['CREATED_USER_NAME']     = $createdUser->name;
                $replaces['CREATED_USER_EMAIL']    = $createdUser->email;

                foreach ($replaces as $key => $value) {
                    $subject = str_replace("[$key]", $value, $subject);
                    $body    = str_replace("[$key]", $value, $body);
                }

                $body             = EdocmanHelper::convertImgTags($body);
                $registeredUsers = self::getUsersByGroup($groupIds);

                foreach ($registeredUsers as $user) {
                    $unsubscribe_link = Uri::root() . "index.php?option=com_edocman&task=unsubscribe&email=" . $user->email;
                    $emailBody = $body;
                    $emailBody = str_replace('[USERNAME]', $user->username, $emailBody);
                    $emailBody = str_replace('[NAME]', $user->name, $emailBody);
                    $emailBody = str_replace('[UNSUBSCRIBE_LINK]', $unsubscribe_link, $emailBody);
                    $emailBody = EdocmanHelper::convertImgTags($emailBody);
                    if (!in_array($user->email, $unsubscribeEmails)) {
                        try {
                            $mailer->sendMail($fromEmail, $fromName, $user->email, $subject, $emailBody, 1);
                        } catch (Exception $e) {
                            Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
                        }
                        $mailer->clearAllRecipients();
                    }
                }
            }

            if (!$isNew && $this->params->get('update_document_notification') && ($allow_sending == 1)) {
                // Send update document notification here
                $subject = $category->update_document_email_subject;
                $body    = $category->update_document_email_body;

                // Get modified user
                $query->select('username, name, email')
                    ->from('#__users')
                    ->where('id = ' . (int) $row->modified_user_id);
                $db->setQuery($query);
                $modifiedUser = $db->loadObject();

                $replaces                           = [];
                $replaces['DOCUMENT_TITLE']         = $row->title;
                $replaces['DOCUMENT_LINK']          = self::getSiteUrl() . EDocmanHelperRoute::getDocumentRoute($row->id, 0, $item_id);
                $replaces['CATEGORY_TITLE']         = $category->title;
                $replaces['MODIFIED_USER_USERNAME'] = $modifiedUser->username;
                $replaces['MODIFIED_USER_NAME']     = $modifiedUser->name;
                $replaces['MODIFIED_USER_EMAIL']    = $modifiedUser->email;

                foreach ($replaces as $key => $value) {
                    $subject = str_replace("[$key]", $value, $subject);
                    $body    = str_replace("[$key]", $value, $body);
                }
                $body             = EdocmanHelper::convertImgTags($body);
                $registeredUsers = self::getUsersByGroup($groupIds);

                foreach ($registeredUsers as $user) {
                    $unsubscribe_link = Uri::root() . "index.php?option=com_edocman&task=unsubscribe&email=" . $user->email;
                    $emailBody = $body;
                    $emailBody = str_replace('[USERNAME]', $user->username, $emailBody);
                    $emailBody = str_replace('[NAME]', $user->name, $emailBody);
                    $emailBody = str_replace('[UNSUBSCRIBE_LINK]', $unsubscribe_link, $emailBody);
                    if (!in_array($user->email, $unsubscribeEmails)) {
                        try {
                            $mailer->sendMail($fromEmail, $fromName, $user->email, $subject, $emailBody, 1);
                        } catch (Exception $e) {
                            Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
                        }
                        $mailer->clearAllRecipients();
                    }
                }
            }
        }

        /**
         * Method to return a list of users from given groups
         *
         * @param   array $groupIds IDs of the groups
         *
         * @return  array
         *
         */
        public static function getUsersByGroup($groupIds)
        {
            // Get a database object.
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id, name, username, email')
                ->from('#__users')
                ->where('id IN (SELECT DISTINCT user_id FROM #__user_usergroup_map WHERE group_id IN (' . implode(',', $groupIds) . ')) AND `block` = "0" ');
            $db->setQuery($query);

            return $db->loadObjectList();
        }

        function getUserGroupsByAccessLevel($accessLevelId)
        {
            // Lấy đối tượng cơ sở dữ liệu
            $db = Factory::getDbo();

            // Tạo truy vấn để lấy các nhóm người dùng
            $query = $db->getQuery(true)
                ->select($db->quoteName('rules'))
                ->from($db->quoteName('#__viewlevels'))
                ->where($db->quoteName('id') . ' = ' . (int) $accessLevelId);

            // Thực thi truy vấn
            $db->setQuery($query);
            $rules = $db->loadResult();

            // Nếu không có quy tắc nào, trả về mảng rỗng
            if (!$rules) {
                return [];
            }

            // Giải mã JSON để lấy danh sách ID nhóm người dùng
            $groupIds = json_decode($rules, true);

            return $groupIds ?: [];
        }

        function getGroupsByCategory($levelId)
        {
            // Lấy đối tượng cơ sở dữ liệu
            $db = Factory::getDbo();

            // Tạo truy vấn để lấy các nhóm người dùng
            $query = $db->getQuery(true)
                ->select($db->quoteName('groups'))
                ->from($db->quoteName('#__edocman_levels'))
                ->where($db->quoteName('id') . ' = ' . (int) $levelId);

            // Thực thi truy vấn
            $db->setQuery($query);
            return explode(',', $db->loadResult());
        }

        /**
         * Get main category of the given document
         *
         * @param $id
         *
         * @return mixed
         */
        public static function getMainCategory($id)
        {
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('a.*')
                ->from('#__edocman_categories AS a')
                ->where('a.id=' . (int) $id);
            $db->setQuery($query);

            return $db->loadObject();
        }

        /**
         * Get URL of the site, using for Ajax request
         */
        public static function getSiteUrl()
        {
            $uri  = Uri::getInstance();
            $base = $uri->toString(array('scheme', 'host', 'port'));
            if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI'])) {
                $script_name = $_SERVER['PHP_SELF'];
            } else {
                $script_name = $_SERVER['SCRIPT_NAME'];
            }
            $path = rtrim(dirname($script_name), '/\\');
            if ($path) {
                $path = $base . $path . '/';
            } else {
                $path = $base . '/';
            }

            $path = str_replace("administrator/", "", $path);
            return $path;
        }
    }
