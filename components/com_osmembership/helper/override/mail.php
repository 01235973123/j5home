<?php

/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2024 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use OSSolution\MembershipPro\Admin\Event\Mail\BeforeSendingEmail;

class OSMembershipHelperOverrideMail extends OSMembershipHelperMail
{
    /**
     * Send email to super administrator and user
     *
     * @param   OSMembershipTableSubscriber  $row
     * @param   MPFConfig                    $config
     */
    public static function sendEmails($row, $config)
    {
        /* @var DatabaseDriver $db */
        $db          = Factory::getContainer()->get('db');
        $query       = $db->getQuery(true);
        $fieldSuffix = OSMembershipHelper::getFieldSuffix($row->language);

        $query->select('*')
            ->from('#__osmembership_plans')
            ->where('id = ' . $row->plan_id);
        $db->setQuery($query);
        $plan = $db->loadObject();

        if ($plan->category_id > 0) {
            $category = OSMembershipHelperDatabase::getCategory($plan->category_id);

            OSMembershipHelper::setPlanMessagesDataFromCategory($plan, $category, [
                'user_email_body',
                'user_email_body_offline',
                'admin_email_body',
                'user_renew_email_body',
                'user_renew_email_body_offline',
                'admin_renew_email_body',
                'user_upgrade_email_body',
                'user_upgrade_email_body_offline',
                'admin_upgrade_email_body',
            ]);
        }

        if ($plan->notification_emails) {
            $config->notification_emails = $plan->notification_emails;
        }

        $mailer = static::getMailer($config);

        $message = OSMembershipHelper::getMessages();

        if ($row->act == 'upgrade') {
            static::sendMembershipUpgradeEmails($mailer, $row, $plan, $config, $message, $fieldSuffix);

            return;
        }

        if ($row->act == 'renew') {
            static::sendMembershipRenewalEmails($mailer, $row, $plan, $config, $message, $fieldSuffix);

            return;
        }

        $logEmails = static::loggingEnabled('new_subscription_emails', $config);

        $rowFields    = OSMembershipHelper::getProfileFields($row->plan_id);
        $emailContent = OSMembershipHelper::getEmailContent($config, $row, false, 'register');
        $replaces     = OSMembershipHelper::callOverridableHelperMethod('Helper', 'buildTags', [$row, $config]);

        $replaces['SUBSCRIPTION_DETAIL'] = $emailContent;

        // New Subscription Email Subject
        if ($fieldSuffix && trim($plan->{'user_email_subject' . $fieldSuffix})) {
            $subject = $plan->{'user_email_subject' . $fieldSuffix};
        } elseif ($fieldSuffix && trim($message->{'user_email_subject' . $fieldSuffix})) {
            $subject = $message->{'user_email_subject' . $fieldSuffix};
        } elseif (trim($plan->user_email_subject)) {
            $subject = $plan->user_email_subject;
        } else {
            $subject = $message->user_email_subject;
        }

        // New Subscription Email Body
        if (str_contains($row->payment_method, 'os_offline') && $row->published == 0) {
            $offlineSuffix = str_replace('os_offline', '', $row->payment_method);
            if ($offlineSuffix && $fieldSuffix && OSMembershipHelper::isValidMessage(
                $message->{'user_email_body_offline' . $offlineSuffix . $fieldSuffix}
            )) {
                $body = $message->{'user_email_body_offline' . $offlineSuffix . $fieldSuffix};
            } elseif ($offlineSuffix && OSMembershipHelper::isValidMessage($message->{'user_email_body_offline' . $offlineSuffix})) {
                $body = $message->{'user_email_body_offline' . $offlineSuffix};
            } elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($plan->{'user_email_body_offline' . $fieldSuffix})) {
                $body = $plan->{'user_email_body_offline' . $fieldSuffix};
            } elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_email_body_offline' . $fieldSuffix})) {
                $body = $message->{'user_email_body_offline' . $fieldSuffix};
            } elseif (OSMembershipHelper::isValidMessage($plan->user_email_body_offline)) {
                $body = $plan->user_email_body_offline;
            } else {
                $body = $message->user_email_body_offline;
            }
        } elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($plan->{'user_email_body' . $fieldSuffix})) {
            $body = $plan->{'user_email_body' . $fieldSuffix};
        } elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_email_body' . $fieldSuffix})) {
            $body = $message->{'user_email_body' . $fieldSuffix};
        } elseif (OSMembershipHelper::isValidMessage($plan->user_email_body)) {
            $body = $plan->user_email_body;
        } else {
            $body = $message->user_email_body;
        }

        foreach ($replaces as $key => $value) {
            $key     = strtoupper($key);
            $value   = (string) $value;
            $subject = str_ireplace("[$key]", $value, $subject);
            $body    = str_ireplace("[$key]", $value, $body);
        }

        $invoicePath = '';

        if (
            $row->invoice_number > 0
            && ($config->send_invoice_to_customer || $config->send_invoice_to_admin)
        ) {
            $invoicePath = OSMembershipHelper::generateAndReturnInvoicePath($row);
        }

        if ($config->send_invoice_to_customer && $invoicePath) {
            $mailer->addAttachment($invoicePath);
        }

        // Generate and send member card to subscriber email
        if (
            $config->send_member_card_via_email
            && $plan->activate_member_card_feature
            && $row->published == 1
        ) {
            $path = OSMembershipHelperSubscription::generatePlanMemberCard($row, $config);
            $mailer->addAttachment($path);
        }

        // Add documents from plan to subscription confirmation email if subscription is active
        if ($row->published == 1) {
            static::addSubscriptionDocuments($mailer, $row);
        }

        if (MailHelper::isEmailAddress($row->email)) {
            static::send(
                $mailer,
                array_merge([$row->email], self::getEmailsFromSubscriptionData($rowFields, $replaces)),
                $subject,
                $body,
                $logEmails,
                2,
                'new_subscription_emails',
                $row
            );

            $mailer->clearAllRecipients();
        }

        $mailer->clearAttachments();

        if ($config->send_invoice_to_admin && $invoicePath) {
            $mailer->addAttachment($invoicePath);
        }

        // 1. Lấy tất cả các field kiểu Checkboxes có emails
        $query = $db->getQuery(true)
            ->clear()
            ->select('id, `values`, emails')
            ->from('#__osmembership_fields')
            ->where("fieldtype = 'Checkboxes'")
            ->where("emails != ''");

        $db->setQuery($query);
        $fields = $db->loadObjectList();

        // 2. Với mỗi field, kiểm tra xem có liên quan đến subscriber hiện tại không
        $result = [];

        foreach ($fields as $field) {
            // Lấy field_value cho subscriber hiện tại
            $query = $db->getQuery(true)
                ->clear()
                ->select('field_value')
                ->from('#__osmembership_field_value')
                ->where('field_id = ' . (int) $field->id)
                ->where('subscriber_id = ' . (int) $row->id);

            $db->setQuery($query);
            $fieldValue = $db->loadResult();
            $fieldValue = json_decode($fieldValue, true);

            if (empty($fieldValue) || !is_array($fieldValue)) {
                continue; // Không có giá trị hoặc không phải mảng => bỏ qua
            }

            // Tách key & value theo dòng mới
            $keyMail = preg_split("/\r\n|\n|\r/", $field->values);  // Chuyển values thành mảng
            $valueMail = preg_split("/\r\n|\n|\r/", $field->emails); // Chuyển emails thành mảng        

            foreach ($fieldValue as $selectedKey) {
                $selectedKey = trim($selectedKey);
                $key = array_search($selectedKey, $keyMail);

                if ($key !== false && isset($valueMail[$key])) {
                    $result[] = $valueMail[$key];
                }
            }
        }

        $emails = explode(',', $config->notification_emails);
        $emails = array_merge($emails, $result);

        if ($fieldSuffix && strlen($message->{'admin_email_subject' . $fieldSuffix})) {
            $subject = $message->{'admin_email_subject' . $fieldSuffix};
        } else {
            $subject = $message->admin_email_subject;
        }

        $subject = str_replace('[PLAN_TITLE]', $plan->title, $subject);

        if ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'admin_email_body' . $fieldSuffix})) {
            $body = $message->{'admin_email_body' . $fieldSuffix};
        } elseif (OSMembershipHelper::isValidMessage($plan->admin_email_body)) {
            $body = $plan->admin_email_body;
        } else {
            $body = $message->admin_email_body;
        }

        $emailContent = OSMembershipHelper::getEmailContent($config, $row, true, 'register');

        $body = str_replace('[SUBSCRIPTION_DETAIL]', $emailContent, $body);

        foreach ($replaces as $key => $value) {
            $key     = strtoupper($key);
            $value   = (string) $value;
            $subject = str_ireplace("[$key]", $value, $subject);
            $body    = str_ireplace("[$key]", $value, $body);
        }

        if ($config->send_attachments_to_admin) {
            self::addAttachments($mailer, $rowFields, $replaces);
        }

        static::send($mailer, $emails, $subject, $body, $logEmails, 1, 'new_subscription_emails', $row);

        //After sending email, we can empty the user_password of subscription was activated
        if ($row->published == 1 && $row->user_password) {
            $query->clear()
                ->update('#__osmembership_subscribers')
                ->set('user_password = ""')
                ->where('id = ' . $row->id);
            $db->setQuery($query);
            $db->execute();
        }
    }
}
