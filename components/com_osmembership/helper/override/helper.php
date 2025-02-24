<?php

/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2024 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use CB\Database\Table\UserTable;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\Component\Users\Site\Model\RegistrationModel;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use OSSolution\MembershipPro\Admin\Event\Subscribe\CheckCanSubscribeToPlan;

class OSMembershipHelperOverrideHelper extends OSMembershipHelper
{
    /**
     * Create an user account based on the entered data
     *
     * @param $data
     *
     * @return int
     * @throws Exception
     */
    public static function saveRegistrationSimple($data)
    {
        $config = OSMembershipHelper::getConfig();

        if (!empty($config->use_cb_api)) {
            return static::userRegistrationCB($data['first_name'], $data['last_name'], $data['email'], $data['username'], $data['password1']);
        }

        //Need to load com_users language file
        $lang = Factory::getApplication()->getLanguage();
        $tag  = $lang->getTag();

        if (!$tag) {
            $tag = 'en-GB';
        }

        $lang->load('com_users', JPATH_ROOT, $tag);
        $userData             = [];
        $userData['username'] = $data['username'];
        $userData['name']     = trim($data['first_name'] . ' ' . $data['last_name']);
        $userData['password'] = $userData['password1'] = $userData['password2'] = $data['password1'];
        $userData['email']    = $userData['email1'] = $userData['email2'] = $data['email'];

        $params         = ComponentHelper::getParams('com_users');

        $userData['activation'] = 0;
        $userData['groups']   = [];
        $userData['groups'][] = $params->get('new_usertype', 2);
        $user                 = new User();

        if (!$user->bind($userData)) {
            self::logCreateUserError($userData, $user->getError());

            throw new Exception(Text::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
        }

        // Store the data.
        if (!$user->save()) {
            self::logCreateUserError($userData, $user->getError());

            throw new Exception(Text::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
        }

        return $user->id;
    }

    public static function buildTags($row, $config)
    {
        $replaces = OSMembershipHelper::buildTags($row, $config);
        $link = Route::link(
            'site',
            'index.php?option=com_users&view=reset',
            false,
            0,
            true
        );

        $replaces['reset_password'] = '<a href="' . $link . '">' . Text::_('OSM_RESET_PASSWORD_TEXT') . '</a>';
        return  $replaces;
    }
}
