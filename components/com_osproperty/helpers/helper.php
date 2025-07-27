<?php

/*------------------------------------------------------------------------
# helper.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Captcha\Captcha;

class OSPHelper
{
    /**
     * This function is used to load Config and return the Configuration Variable
     *
     */
    public static function loadConfig()
    {
		static $configClass;
		if ($configClass == null)
		{
			$db = Factory::getDbo();
			$db->setQuery("Select * from #__osrs_configuration");
			$configs = $db->loadObjectList();
			$configClass = [];
			foreach ($configs as $config) {
				$configClass[$config->fieldname] = $config->fieldvalue;
			}

			$curr = $configClass['general_currency_default'];
			$arrCode = [];
			$arrSymbol = [];

			$db->setQuery("Select * from #__osrs_currencies where id = '$curr'");
			$currency = $db->loadObject();
			$symbol = $currency->currency_symbol;
			$index = -1;
			if ($symbol == "") {
				$symbol = '$';
			}
			$configClass['curr_symbol'] = $symbol;
		}
        return $configClass;
    }

    /**
     * This function is used to check to see whether we need to update the database to support multilingual or not
     *
     * @return boolean
     */
    public static function isSyncronized()
    {
        $db = Factory::getDbo();
        //#__osrs_tags
        $fields = array_keys($db->getTableColumns('#__osrs_tags'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('keyword_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        //osrs_emails
        $fields = array_keys($db->getTableColumns('#__osrs_emails'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('email_title_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        //osrs_categories
        $fields = array_keys($db->getTableColumns('#__osrs_categories'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('category_name_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        //osrs_amenities
        $fields = array_keys($db->getTableColumns('#__osrs_amenities'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('amenities_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        //osrs_fieldgroups
        $fields = array_keys($db->getTableColumns('#__osrs_fieldgroups'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('group_name_' . $prefix, $fields)) {
                    return false;
                }
            }
        }


        //osrs_osrs_extra_fields
        $fields = array_keys($db->getTableColumns('#__osrs_extra_fields'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('field_label_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        //osrs_extra_field_options
        $fields = array_keys($db->getTableColumns('#__osrs_extra_field_options'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('field_option_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        //osrs_property_field_value
        $fields = array_keys($db->getTableColumns('#__osrs_property_field_value'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('value_' . $prefix, $fields)) {
                    return false;
                }
            }
        }


        //osrs_types
        $fields = array_keys($db->getTableColumns('#__osrs_types'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('type_name_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        //osrs_properties
        $fields = array_keys($db->getTableColumns('#__osrs_properties'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('pro_name_' . $prefix, $fields)) {
                    return false;
                }

				if (!in_array('price_text_' . $prefix, $fields)) {
                    return false;
                }

                if (!in_array('address_' . $prefix, $fields)) {
                    return false;
                }
                if (!in_array('metadesc_' . $prefix, $fields)) {
                    return false;
                }
                if (!in_array('metakey_' . $prefix, $fields)) {
                    return false;
                }
				if (!in_array('pro_browser_title_' . $prefix, $fields)) {
                    return false;
                }
				if (!in_array('region_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        //osrs_agents
        $fields = array_keys($db->getTableColumns('#__osrs_agents'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('bio_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        //osrs_companies
        $fields = array_keys($db->getTableColumns('#__osrs_companies'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('company_description_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        //osrs_states
        $fields = array_keys($db->getTableColumns('#__osrs_states'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('state_name_' . $prefix, $fields)) {
                    return false;
                }
            }
        }


        //osrs_cities
        $fields = array_keys($db->getTableColumns('#__osrs_cities'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('city_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

		//osrs_cities
        $fields = array_keys($db->getTableColumns('#__osrs_countries'));
        $extraLanguages = self::getLanguages();
        if (count($extraLanguages)) {
            foreach ($extraLanguages as $extraLanguage) {
                $prefix = $extraLanguage->sef;
                if (!in_array('country_name_' . $prefix, $fields)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get field suffix used in sql query
     *
     * @return string
     */
    public static function getFieldSuffix($activeLanguage = null)
    {
        static $prefix;
		if($prefix == null)
		{
			if (Multilanguage::isEnabled()) 
			{
				if (!$activeLanguage)
					$activeLanguage = Factory::getLanguage()->getTag();

				if ($activeLanguage != self::getDefaultLanguage()) 
				{
					$prefix = '_' . substr($activeLanguage, 0, 2);
				}
				else
				{
					$prefix = '';
				}
			}
		}
        return $prefix;
    }


    /**
     *
     * Function to get all available languages except the default language
     * @return languages object list
     */
    public static function getAllLanguages()
    {
		static $languages; 
		if($languages == null)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$default = self::getDefaultLanguage();
			$query->select('lang_id, lang_code, title, `sef`')
				->from('#__languages')
				->where('published = 1')
				->order('ordering');
			$db->setQuery($query);
			$languages = $db->loadObjectList();
		}
        return $languages;
    }

    /**
     *
     * Function to get all available languages except the default language
     * @return languages object list
     */
    public static function getLanguages()
    {
		global $languages; 
		static $isLoadAllLanguages;
		if($isLoadAllLanguages == 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$default = self::getDefaultLanguage();
			$query->select('lang_id, lang_code, title, `sef`')
				->from('#__languages')
				->where('published = 1')
				->where('lang_code != "' . $default . '"')
				->order('ordering');
			$db->setQuery($query);
			$languages = $db->loadObjectList();
			$isLoadAllLanguages = 1;
		}
        return $languages;
    }

    /**
     * Get front-end default language
     * @return string
     */
    public static function getDefaultLanguage()
    {
        $params = ComponentHelper::getParams('com_languages');
        return $params->get('site', 'en-GB');
    }

	public static function getDefaultLanguageTag()
	{
		$defaultLanguage = self::getDefaultLanguage();
		$prefix_language = substr($defaultLanguage, 0, 2);
        return $prefix_language;
	}

    /**
     * Get default language of user
     *
     */
    public static function getUserLanguage($user_id)
    {
        $default_language = self::getDefaultLanguage();
        if ($user_id > 0) {
            $user = Factory::getUser($user_id);
            $default_language = $user->getParam('language', $default_language);
        } else {
            return $default_language;
        }
        return $default_language;
    }

    public static function getLanguageFieldValue($obj, $fieldname)
    {
        global $languages,$lang_suffix;
        $lgs = self::getLanguages();
        $translatable = Multilanguage::isEnabled() && count($lgs);
        if ($translatable) {
            $suffix = self::getFieldSuffix();
            $returnValue = $obj->{$fieldname . $suffix};
            if ($returnValue == "") {
                $returnValue = $obj->{$fieldname};
            }
        } else {
            $returnValue = $obj->{$fieldname};
        }
        return $returnValue;
    }

    public static function getLanguageFieldValueBackend($obj, $fieldname, $suffix)
    {
        global $languages;
        $lgs = self::getLanguages();
        $translatable = Multilanguage::isEnabled() && count($lgs);
        if ($translatable) {
            $returnValue = $obj->{$fieldname . $suffix};
            if ($returnValue == "") {
                $returnValue = $obj->{$fieldname};
            }
        } else {
            $returnValue = $obj->{$fieldname};
        }
        return $returnValue;
    }

    /**
     * Syncronize Membership Pro database to support multilingual
     */
    public static function setupMultilingual()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $languages = self::getLanguages();
        if (count($languages)) {
            //states table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_states");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_states table
                $prefix = $language->sef;
                if (!in_array('state_name_' . $prefix, $fieldArr)) {
                    $fieldName = 'state_name_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_states` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

            //cities table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_cities");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_cities table
                $prefix = $language->sef;
                if (!in_array('city_' . $prefix, $fieldArr)) {
                    $fieldName = 'city_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_cities` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }


			//cities table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_countries");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_countries table
                $prefix = $language->sef;
                if (!in_array('country_name_' . $prefix, $fieldArr)) {
                    $fieldName = 'country_name_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_countries` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

            //tags table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_tags");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_emails table
                $prefix = $language->sef;
                //$fields = array_keys($db->getTableColumns('#__osrs_emails'));
                if (!in_array('keyword_' . $prefix, $fieldArr)) {
                    $fieldName = 'keyword_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_tags` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }


            //emails table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_emails");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_emails table
                $prefix = $language->sef;
                //$fields = array_keys($db->getTableColumns('#__osrs_emails'));
                if (!in_array('email_title_' . $prefix, $fieldArr)) {
                    $fieldName = 'email_title_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_emails` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();

                    $fieldName = 'email_content_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_emails` ADD  `$fieldName` TEXT NULL;";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

            //categories table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_categories");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_categories table
                $prefix = $language->sef;
                if (!in_array('category_name_' . $prefix, $fieldArr)) {
                    $fieldName = 'category_name_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_categories` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();

                    $fieldName = 'category_alias_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_categories` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();

                    $fieldName = 'category_description_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_categories` ADD  `$fieldName` TEXT NULL;";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }


            //amenities table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_amenities");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_amenities table
                $prefix = $language->sef;
                if (!in_array('amenities_' . $prefix, $fieldArr)) {
                    $fieldName = 'amenities_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_amenities` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

            //field group table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_fieldgroups");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_amenities table
                $prefix = $language->sef;
                if (!in_array('group_name_' . $prefix, $fieldArr)) {
                    $fieldName = 'group_name_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_fieldgroups` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

            //extra field table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_extra_fields");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_amenities table
                $prefix = $language->sef;
                if (!in_array('field_label_' . $prefix, $fieldArr)) {
                    $fieldName = 'field_label_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_extra_fields` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();

                    $fieldName = 'field_description_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_extra_fields` ADD  `$fieldName` TEXT NULL;";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }


            //field group table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_extra_field_options");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_amenities table
                $prefix = $language->sef;
                if (!in_array('field_option_' . $prefix, $fieldArr)) {
                    $fieldName = 'field_option_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_extra_field_options` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

            //osrs_property_field_value table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_property_field_value");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_amenities table
                $prefix = $language->sef;
                if (!in_array('value_' . $prefix, $fieldArr)) {
                    $fieldName = 'value_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_property_field_value` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

            //types table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_types");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_amenities table
                $prefix = $language->sef;
                if (!in_array('type_name_' . $prefix, $fieldArr)) {
                    $fieldName = 'type_name_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_types` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();

                    $fieldName = 'type_alias_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_types` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }


            //properties table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_properties");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_properties table
                $prefix = $language->sef;
                if (!in_array('pro_name_' . $prefix, $fieldArr)) {
                    $fieldName = 'pro_name_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_properties` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();

                    $fieldName = 'pro_alias_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_properties` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
                if (!in_array('address_' . $prefix, $fieldArr)) {
                    $fieldName = 'address_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_properties` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
				if (!in_array('price_text_' . $prefix, $fieldArr)) {
                    $fieldName = 'price_text_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_properties` ADD  `$fieldName` VARCHAR( 255 );";
                    $db->setQuery($sql);
                    $db->execute();
                }
                if (!in_array('pro_small_desc_' . $prefix, $fieldArr)) {
                    $fieldName = 'pro_small_desc_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_properties` ADD  `$fieldName` TEXT NULL;";
                    $db->setQuery($sql);
                    $db->execute();

                    $fieldName = 'pro_full_desc_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_properties` ADD  `$fieldName` TEXT NULL;";
                    $db->setQuery($sql);
                    $db->execute();
                }
                if (!in_array('metadesc_' . $prefix, $fieldArr)) {
                    $fieldName = 'metadesc_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_properties` ADD  `$fieldName` VARCHAR (255) DEFAULT '' NOT NULL;";
                    $db->setQuery($sql);
                    $db->execute();

                    $fieldName = 'metakey_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_properties` ADD  `$fieldName` VARCHAR (255) DEFAULT '' NOT NULL;";
                    $db->setQuery($sql);
                    $db->execute();
                }
				if (!in_array('pro_browser_title_' . $prefix, $fieldArr)) {
                    $fieldName = 'pro_browser_title_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_properties` ADD  `$fieldName` VARCHAR( 255 ) DEFAULT '' NOT NULL;";
                    $db->setQuery($sql);
                    $db->execute();
                }
				if (!in_array('region_' . $prefix, $fieldArr)) {
                    $fieldName = 'region_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_properties` ADD  `$fieldName` VARCHAR( 255 ) DEFAULT '' NOT NULL;";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

            //types table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_agents");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_amenities table
                $prefix = $language->sef;
                if (!in_array('bio_' . $prefix, $fieldArr)) {
                    $fieldName = 'bio_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_agents` ADD  `$fieldName` TEXT NULL;";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }


            //companies table
            $db->setQuery("SHOW COLUMNS FROM #__osrs_companies");
            $fields = $db->loadObjectList();
            if (count($fields) > 0) {
                $fieldArr = [];
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    $fieldname = $field->Field;
                    $fieldArr[$i] = $fieldname;
                }
            }
            foreach ($languages as $language) {
                #Process for #__osrs_amenities table
                $prefix = $language->sef;
                if (!in_array('company_description_' . $prefix, $fieldArr)) {
                    $fieldName = 'company_description_' . $prefix;
                    $sql = "ALTER TABLE  `#__osrs_companies` ADD  `$fieldName` TEXT NULL;";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

        }
    }

    /**
     * Check the email message
     *
     */
    public static function isEmptyMailContent($subject, $content)
    {
        if (($subject == "") or (strlen(strip_tags($content)) == 0)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Load language from main component
     *
     */
    public static function loadLanguage()
    {
        static $loaded;
        if (!$loaded) {
            $lang = Factory::getLanguage();
            $tag = $lang->getTag();
            if (!$tag)
                $tag = 'en-GB';

            $lang->load('com_osproperty', JPATH_ROOT, $tag);
            $loaded = true;
        }
    }

    /**
     * Load current language
     *
     */
    public static function getCurrentLanguage()
    {
        $lang = Factory::getLanguage();
        $tag = $lang->getTag();
        if (!$tag) {
            $tag = 'en-GB';
        }
        $prefix_language = substr($tag, 0, 2);
        return $prefix_language;
    }

    /**
     * Init data
     *
     */
    public static function initSetup()
    {
        $db = Factory::getDbo();
        $db->setQuery("Select count(id) from #__osrs_init where `name` like 'import_city'");
        $count = $db->loadResult();
        if ($count == 0) {
            $db->setQuery("Select count(id) froM #__osrs_cities where country_id = '194'");
            $count = $db->loadResult();
            if ($count == 0) {
                $configSql = JPATH_ADMINISTRATOR . '/components/com_osproperty/sql/cities.osproperty.sql';
                $sql = File::read($configSql);
                $queries = $db->splitSql($sql);
                if (count($queries)) {
                    foreach ($queries as $query) {
                        $query = trim($query);
                        if ($query != '') {
                            $db->setQuery($query);
                            $db->execute();
                        }
                    }
                }
                $db->setQuery("Insert into #__osrs_init (id,`name`,`value`) values (NULL,'import_city','1')");
                $db->execute();
            } else {
                $db->setQuery("Insert into #__osrs_init (id,`name`,`value`) values (NULL,'import_city','1')");
                $db->execute();
            }
        }
    }

    public static function checkBrowers()
    {
        $browser = new OsBrowser();
        $checkismobile = $browser->returnisMobile();
        if (!$checkismobile) {
            $checkismobile = $browser->isMobile();
        }
        return $checkismobile;
    }


	public static function loadMedia(){
		global $configClass;
		static $loadmedia;
		if(!$loadmedia)
		{
			HTMLHelper::_('jquery.framework');
			$rootUrl = Uri::root(true);
			$app = Factory::getApplication();
			$current_template = $app->getTemplate();
			$document = Factory::getDocument();
			if (file_exists(JPATH_ROOT . $current_template . 'html/com_osproperty/css/frontend_style.css')) {
				$document->addStyleSheet($rootUrl .'/templates/'. $current_template.'/html/com_osproperty/css/frontend_style.css');
			}else {
				$document->addStyleSheet($rootUrl ."/media/com_osproperty/assets/css/frontend_style.css");
			}
			if (file_exists(JPATH_ROOT . '/media/com_osproperty/assets/css/custom.css') && filesize(JPATH_ROOT . '/media/com_osproperty/assets/css/custom.css') > 0)
			{
				$document->addStylesheet($rootUrl . '/media/com_osproperty/assets/css/custom.css');
			}
			if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
			{
				$document->addScript($rootUrl ."/media/com_osproperty/assets/js/ajax.js","text/javascript");
			}
			else
			{
				$document->addScript($rootUrl ."/media/com_osproperty/assets/js/ajax.js","text/javascript",true);
			}
			if(self::isJoomla4())
			{
				$document->addStyleSheet($rootUrl ."/media/com_osproperty/assets/css/style4.css");
			}
			$document->addScript($rootUrl ."/media/com_osproperty/assets/js/noconflict.js","text/javascript");
			$loadmedia = true;
		}
	}


    public static function loadBootstrap($loadJs = true)
    {
        global $configClass;
		static $loadbootstrap;
		if(!$loadbootstrap)
		{
			$app = Factory::getApplication();
			$current_template = $app->getTemplate();
			$language = Factory::getLanguage();
			$configClass = self::loadConfig();
			$rootUrl = Uri::root(true);
			$document = Factory::getDocument();
			if (($configClass['load_bootstrap'] == 1) || (version_compare(JVERSION, '3.0', 'lt')))
			{
				if ($loadJs)
				{
					$document->addScript($rootUrl . '/media/com_osproperty/assets/js/bootstrap/js/bootstrap.min.js','text/javascript');
				}
			}
			if ((int)$configClass['twitter_bootstrap_version'] == 2)
			{
				if($configClass['load_bootstrap'] == 1)
				{
					$document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/js/bootstrap/css/bootstrap.css');
					$document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/css/bs2.css');
					if (file_exists(JPATH_ROOT .'/templates/'. $current_template . '/html/com_osproperty/css/bootstrap_adv.css')) {
						$document->addStyleSheet($rootUrl .'/templates/'. $current_template.'/html/com_osproperty/css/bootstrap_adv.css');
					}else {
						$document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/js/bootstrap/css/bootstrap_adv.css');
					}
				}
			}elseif ((int)$configClass['twitter_bootstrap_version'] == 3)
            {
				$document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/css/bs3.css');
			}
			else
			{
				if (file_exists(JPATH_ROOT .'/templates/'. $current_template . '/html/com_osproperty/css/bootstrap_adv.css'))
				{
					$document->addStyleSheet($rootUrl .'/templates/'. $current_template.'/html/com_osproperty/css/bootstrap_adv.css');
				}
				else
				{
					$document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/js/bootstrap/css/bootstrap_adv.css');
				}
			}
			if ($language->isRTL())
			{
			   $document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/js/bootstrap/css/bootstrap_rtl.css');
			}
			$loadbootstrap = true;
		}
    }

    public static function loadBootstrapStylesheet()
    {
        global $configClass;
        $app = Factory::getApplication();
		$rootUrl = Uri::root(true);
        $language = Factory::getLanguage();
        $current_template = $app->getTemplate();
        $configClass = self::loadConfig();
        $document = Factory::getDocument();
        if ((int)$configClass['twitter_bootstrap_version'] == 2) 
		{
			if($configClass['load_bootstrap'] == 1)
			{
				$document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/js/bootstrap/css/bootstrap.css');
				$document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/css/bs2.css');
				if (file_exists(JPATH_ROOT . $current_template . '/html/com_osproperty/css/bootstrap_adv.css')) 
				{
					$document->addStyleSheet($rootUrl .'/templates/'. $current_template.'/html/com_osproperty/css/bootstrap_adv.css');
				}
				else 
				{
					$document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/js/bootstrap/css/bootstrap_adv.css');
				}
			}
        }
		else
		{
			$document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/css/bs3.css');
		}
    	if ($language->isRTL()) {
		   $document->addStyleSheet($rootUrl . '/media/com_osproperty/assets/js/bootstrap/css/bootstrap_rtl.css');
		}
    }


    /**
     *
     * Function to load jQuery chosen plugin
     */
    public static function chosen()
    {
        $configClass = self::loadConfig();
        if ($configClass['load_chosen'] == 1) {
            $document = Factory::getDocument();
            if (version_compare(JVERSION, '3.0', 'ge')) {
                HTMLHelper::_('formbehavior.chosen', '.chosen');
            } else {
                $document->addStyleSheet(Uri::root() . 'media/com_osproperty/assets/js/chosen/chosen.css');
                ?>
                <script src="<?php echo Uri::root() . "media/com_osproperty/assets/js/chosen/chosen.jquery.js"; ?>" type="text/javascript"></script>
            <?php
            }
            $document->addScriptDeclaration(
                "jQuery(document).ready(function(){
	                    jQuery(\".chosen\").chosen();
	                });");
            $chosenLoaded = true;
        }
    }

    /**
     * Load Google JS API file
     */
    public static function loadGoogleJS($suffix = "")
	{
		global $configClass;
        static $loadGoogleJs;
        if($loadGoogleJs == false){
			$configClass = self::loadConfig();
			if($configClass['goole_aip_key'] != ""){
				$key = "&key=".$configClass['goole_aip_key'];
			}else{
				$key = "";
			}
			if($suffix != ""){
				$suffix = "&".$suffix;
			}
            $document = Factory::getDocument();
            $document->addScript("//maps.googleapis.com/maps/api/js?sensor=false".$key."&v=quarterly");
            $loadGoogleJs = true;
        }
    }

    public static function generateWaterMark($id)
    {
        global $mainframe, $configClass;
        $db = Factory::getDbo();
		if((int)$id == 0){
			return;
		}
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		if(!Folder::exists(JPATH_ROOT.'/images/osproperty/properties/'.$id.'/original')){
			Folder::create(JPATH_ROOT.'/images/osproperty/properties/'.$id.'/original');
		}

        $use_watermark = $configClass['images_use_image_watermarks'];
        $watermark_all = $configClass['watermark_all'];
        if ($use_watermark == 1) {
            //get the first image
            $db->setQuery("Select * from #__osrs_photos where pro_id = '$id' order by ordering");
            $rows = $db->loadObjectList();
            if (count($rows) > 0) {
                if ($watermark_all == 1) 
				{
                    for ($i = 0; $i < count($rows); $i++) 
					{
                        $row = $rows[$i];
						$image = $row->image;
						if(File::exists(JPATH_ROOT.'/images/osproperty/properties/'.$id.'/'.$image)){
							if(!File::exists(JPATH_ROOT.'/images/osproperty/properties/'.$id.'/original/'.$image)){
								File::copy(JPATH_ROOT.'/images/osproperty/properties/'.$id.'/'.$image,JPATH_ROOT.'/images/osproperty/properties/'.$id.'/original/'.$image);
							}
							$db->setQuery("Select count(id) from #__osrs_watermark where pid = '$id' and image like '$row->image'");
							$count = $db->loadResult();
							if ($count == 0) {
								//do watermark
								self::generateWaterMarkForPhoto($id, $row->id);
							}
						}
                    }
                } 
				else 
				{
                    $row = $rows[0];
					$image = $row->image;
					if(File::exists(JPATH_ROOT.'/images/osproperty/properties/'.$id.'/'.$image))
					{
						if(!File::exists(JPATH_ROOT.'/images/osproperty/properties/'.$id.'/original/'.$image))
						{
							File::copy(JPATH_ROOT.'/images/osproperty/properties/'.$id.'/'.$image,JPATH_ROOT.'/images/osproperty/properties/'.$id.'/original/'.$image);
						}
						$db->setQuery("Select count(id) from #__osrs_watermark where pid = '$id' and image like '$row->image'");
						$count = $db->loadResult();
						if ($count == 0) 
						{
							//do watermark
							self::generateWaterMarkForPhoto($id, $row->id);
						}
					}
                }
            }
        }//end checking
    }//end function 

    public static function generateWaterMarkForPhoto($pid, $photoid)
    {
        global $mainframe, $configClass;
        $db = Factory::getDbo();
        $db->setQuery("Select * from #__osrs_properties where id = '$pid'");
        $property = $db->loadObject();
        $wtype = $configClass['watermark_type'];

        switch ($wtype) 
		{
            case "1":
                $watermark_text = $configClass['watermark_text'];
                switch ($watermark_text) {
                    case "1":
                        $db->setQuery("Select category_name from #__osrs_categories where id = '$property->category_id'");
                        $text = $db->loadResult();
                        break;
                    case "2":
                        $db->setQuery("Select type_name from #__osrs_types where id = '$property->pro_type'");
                        $text = $db->loadResult();
                        break;
                    case "3":
                        $text = $configClass['general_bussiness_name'];
                        break;
                    case "4":
                        $text = $configClass['custom_text'];
                        break;
                }
                self::waterMarkText($pid, $photoid, $text);
                break;
            case "2":
                $watermark_photo = $configClass['watermark_photo'];
                $watermark_photo_thumb = $configClass['watermark_photo_thumb'];
                $watermark_photo_original = $configClass['watermark_photo_original'];
                if ($watermark_photo == "" && $watermark_photo_thumb == "" && $watermark_photo_original == "")
				{
                    self::waterMarkText($pid, $photoid, $configClass['general_bussiness_name']);
                } 
				elseif (!file_exists(JPATH_ROOT ."/images/". $watermark_photo)) 
				{
                    self::waterMarkText($pid, $photoid, $configClass['general_bussiness_name']);
                } 
				else 
				{
                    self::waterMarkPhoto($pid, $photoid, $watermark_photo, $watermark_photo_thumb, $watermark_photo_original);
                }
                break;
        }
        //update into watermark table
        $db->setQuery("SELECT image FROM #__osrs_photos WHERE id = '$photoid'");
        $photo = $db->loadResult();
        $db->setQuery("INSERT INTO #__osrs_watermark (id,pid,image) VALUES (NULL,'$pid','$photo')");
        $db->execute();
    }

    public static function waterMarkText($pid, $photoid, $text)
    {
        global $configClass;
        $db = Factory::getDbo();
        $db->setQuery("SELECT image FROM #__osrs_photos WHERE id = '$photoid'");
        $photo = $db->loadResult();
        $font_size = $configClass['watermark_fontsize'];
        $image_path = JPATH_ROOT ."/images/osproperty/properties/". $pid ."/medium/". $photo;
        self::processTextWatermark($image_path, $text, $image_path, $font_size);
        $font_size_thumb = $configClass['watermark_fontsize_thumb'];
        if($font_size_thumb == "")
		{
            $font_size_thumb = $font_size;
        }
        $image_path = JPATH_ROOT ."/images/osproperty/properties/". $pid ."/thumb/". $photo;
        self::processTextWatermark($image_path, $text, $image_path, $font_size_thumb);
        $font_size_original = $configClass['watermark_fontsize_original'];
        if($font_size_original == "")
		{
            $font_size_original = $font_size;
        }
        $image_path = JPATH_ROOT ."/images/osproperty/properties/". $pid ."/". $photo;
        self::processTextWatermark($image_path, $text, $image_path, $font_size_original);
    }


    public static function waterMarkPhoto($pid, $photoid, $watermarkPhoto, $watermark_photo_thumb, $watermark_photo_original)
    {
        $db = Factory::getDbo();
        $db->setQuery("SELECT image FROM #__osrs_photos WHERE id = '$photoid'");
        $photo = $db->loadResult();
        $image_path = JPATH_ROOT ."/images/osproperty/properties/". $pid ."/medium/". $photo;
        if($watermark_photo_original == ""){
            $watermark_photo_original = $watermarkPhoto;
        }
        if($watermark_photo_thumb == ""){
            $watermark_photo_thumb = $watermarkPhoto;
        }
        self::processPhotoWatermark($image_path, $watermarkPhoto,$image_path);
        $image_path = JPATH_ROOT ."/images/osproperty/properties/". $pid ."/". $photo;
        self::processPhotoWatermark($image_path, $watermark_photo_original,$image_path);
        $image_path = JPATH_ROOT ."/images/osproperty/properties/". $pid ."/thumb/". $photo;
        self::processPhotoWatermark($image_path, $watermark_photo_thumb,$image_path);
    }


    static function processPhotoWatermark($SourceFile, $tempPhoto, $DestinationFile)
    {
        global $mainframe, $configClass;
        //check the extension of the photo
        list($sw, $sh) = getimagesize(JPATH_ROOT ."/images/". $tempPhoto);
        $tempPhotoArr = explode(".", $tempPhoto);
		$SourceFileArr = explode(".",$SourceFile);
		$source_ext = strtolower($SourceFileArr[count($SourceFileArr) - 1]);
        $ext = strtolower($tempPhotoArr[count($tempPhotoArr) - 1]);
        switch ($ext) {
            case "jpg":
                $p = imagecreatefromjpeg(JPATH_ROOT ."/images/". $tempPhoto);
                break;
            case "png":
                $p = imagecreatefrompng(JPATH_ROOT ."/images/". $tempPhoto);
                break;
            case "gif":
                $p = imagecreatefromgif(JPATH_ROOT ."/images/". $tempPhoto);
                break;
        }
        
        list($width, $height) = getimagesize($SourceFile);
        $image = imagecreatetruecolor($sw, $sh);
		imagealphablending($image, false);
		
		switch ($source_ext) {
            case "jpg":
                $image = imagecreatefromjpeg($SourceFile);
                break;
            case "png":
                $image = imagecreatefrompng($SourceFile);
                break;
            case "gif":
                $image = imagecreatefromgif($SourceFile);
                break;
        }

		$image_p = imagecreatetruecolor($width, $height);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);
		
        $watermark_position = $configClass['watermark_position'];

        $matrix_width3	= round($width / 3);
        $matrix_height3 = round($height / 3);

        $matrix_width2	= round($width / 2);
        $matrix_height2 = round($height / 2);
        switch ($watermark_position) {
            case "1":
                $w = 20;
                $h = 20;
                break;
            case "2":
                $w = $matrix_width2 - $sw / 2;
                $h = 20;
                break;
            case "3":
                $w = $matrix_width3 * 3 - 20 - $sw;
                $h = 20;
                break;
            case "4":
                $w = $matrix_width3 * 3 - 20 - $sw;
                $h = $matrix_height2 - $sh / 2;
                break;
            case "5":
                $w = $matrix_width2 - $sw / 2;
                $h = $matrix_height2 - $sh / 2;
                break;
            case "6":
                $w = 20;
                $h = $matrix_height2 - $sh / 2;
                break;
            case "7":
                $w = $matrix_width3 * 3 - 20 - $sw;
                $h = $matrix_height3 * 3 - 20 - $sh;
                break;
            case "8":
                $w = $matrix_width2 - $sw / 2;
                $h = $matrix_height3 * 3 - 20 - $sh;
                break;
            case "9":
                $w = 20;
                $h = $matrix_height3 * 3 - 20 - $sh;
                break;
        }

        imagecopy($image_p, $p, $w, $h, 0, 0, $sw, $sh);
		imagesavealpha($image_p, true);
		switch ($source_ext) {
            case "jpg":
                if ($DestinationFile != "") {
					imagejpeg($image_p, $DestinationFile, 100);
				} else {
					header('Content-Type: image/jpeg');
					imagejpeg($image_p, null, 100);
				};
                break;
            case "png":
                if ($DestinationFile != "") {
					imagejpeg($image_p, $DestinationFile, 100);
				} else {
					header('Content-Type: image/jpeg');
					imagejpeg($image_p, null, 100);
				};
                break;
            case "gif":
                if ($DestinationFile != "") {
					imagejpeg($image_p, $DestinationFile);
				} else {
					header('Content-Type: image/gif');
					imagegif($image_p, null, 100);
				};
                break;
        }
	
        imagedestroy($image);
        imagedestroy($image_p);
    }

    /**
     * Watermaking
     *
     * @param unknown_type $SourceFile
     * @param unknown_type $WaterMarkText
     * @param unknown_type $DestinationFile
     */
    public static function processTextWatermark($SourceFile, $WaterMarkText, $DestinationFile, $font_size)
    {
        global $mainframe, $configClass;
		$font_size = (int) $font_size;
        list($width, $height) = getimagesize($SourceFile);
        $image_p = imagecreatetruecolor($width, $height);
		$SourceFileArr = explode(".",$SourceFile);
		$source_ext = strtolower($SourceFileArr[count($SourceFileArr) - 1]);

        //$image = imagecreatefromjpeg($SourceFile);
		switch ($source_ext)
		{
            case "jpg":
                $image = imagecreatefromjpeg($SourceFile);
                break;
            case "png":
                $image = imagecreatefrompng($SourceFile);
                break;
            case "gif":
                $image = imagecreatefromgif($SourceFile);
                break;
			case "webp":
                $image = imagecreatefromwebp($SourceFile);
                break;
        }
		
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);
        $watermark_color = $configClass['watermark_color'];

        $watermarkArr = explode(",", $watermark_color);
        $text_color = imagecolorallocate($image_p, $watermarkArr[0], $watermarkArr[1], $watermarkArr[2]);
        $font_family = $configClass['watermark_font'];
        if ($font_family == "") {
            $font_family = "arial.ttf";
        }
        $font = JPATH_ROOT . '/components/com_osproperty/helpers/tcpdf/fonts/'. $font_family;

        $matrix_width3 = round($width / 3);
        $matrix_height3 = round($height / 3);

        $matrix_width2 = round($width / 2);
        $matrix_height2 = round($height / 2);

        $watermark_position = $configClass['watermark_position'];

        switch ($watermark_position) 
		{
            case "1":
                $w = 20;
                $h = 20 + $font_size;
                break;
            case "2":
                $w = $matrix_width2;
                $h = 20 + $font_size;
                break;
            case "3":
                $w = $matrix_width3 * 2 - 20;
                $h = 20 + $font_size;
                break;
            case "4":
                $w = $matrix_width3 * 2 - 20;
                $h = $matrix_height2;
                break;
            case "5":
                //$lenText = imagefontwidth($font_size)*STRLEN($WaterMarkText);
                $p = imagettfbbox($font_size, 0, $font, $WaterMarkText);

                $txt_width = $p[2] - $p[0];
                $w = $matrix_width2;
                $w = $matrix_width2 - round($txt_width / 2);
                $h = $matrix_height2;
                break;
            case "6":
                $w = 20;
                $h = $matrix_height2;
                break;
            case "7":
                $w = $matrix_width3 * 2 - 20;
                $h = $matrix_height3 * 3 - 10 - $font_size;
                break;
            case "8":
                $w = $matrix_width2;
                $h = $matrix_height3 * 3 - 10 - $font_size;
                break;
            case "9":
                $w = 20;
                $h = $matrix_height3 * 3 - 10 - $font_size;
                break;
        }

        imagettftext($image_p, $font_size, 0, $w, $h, $text_color, $font, $WaterMarkText);
        if ($DestinationFile != "") 
		{
            imagejpeg($image_p, $DestinationFile, $configClass['images_quality']);
        } 
		else 
		{
            header('Content-Type: image/jpeg');
            imagejpeg($image_p, null, $configClass['images_quality']);
        };
        imagedestroy($image);
        imagedestroy($image_p);
    }

    /**
     * Load address in format
     *
     * @param unknown_type $property
     * @return unknown
     */
    public static function generateAddress($property)
    {
        global $mainframe, $configClass;
        $configClass = OSPHelper::loadConfig();
        $address = [];
        $languages = self::getLanguages();
        $translatable = Multilanguage::isEnabled() && count($languages);
        if($translatable)
		{
            $property_address = self::getLanguageFieldValue($property,'address');
			$region			  = self::getLanguageFieldValue($property,'region');
        }
		else
		{
            $property_address = $property->address;
			$region			  = $property->region;
        }

        if (trim($property_address) != "" && $property_address != "&nbsp;") 
		{
            $address[0] = trim($property_address);
        } 
		else 
		{
            $address[0] = "";
        }
		if(property_exists($property, 'city_name') && $property->city_name != '')
		{
			$address[1] = $property->city_name;
		}
		else
		{
			
			$address[1] = self::loadCityName($property->city);
		}
		if(property_exists($property, 'state_name') &&  $property->state_name != '')
		{
			$address[2] = $property->state_name;
		}
		else
		{
			$address[2] = self::loadSateCode($property->state);
		}
        $address[3] = $region;
        $address[4] = $property->postcode;

        $address_format = $configClass['address_format'];
        if ($address_format == "") { //default value
            $address_format = "0,1,2,3,4";
        }
        //echo $address_format;
        //echo $address_format;
        $returnAddress = [];
        $address_formatArr = explode(",", $address_format);
        for ($i = 0; $i < count($address_formatArr); $i++) {
            $item = $address_formatArr[$i];
            if ($address[$item] != "") {
                $returnAddress[] = $address[$item];
            }
        }
		if (HelperOspropertyCommon::checkCountry()) 
		{
			$returnAddress[] = self::loadCountryName($property->country);
		}
        if (count($returnAddress) > 0) {
            return implode(", ", $returnAddress);
        } else {
            return "";
        }
    }

	public static function loadCityName($city_id)
    {
		static $city_id_value, $city_value;
        global $languages;
        $db = Factory::getDBO();
        $lgs = self::getLanguages();
        $translatable = Multilanguage::isEnabled() && count($lgs);
		$app = Factory::getApplication();
		if(!$city_id_value || $city_id_value != $city_id)
		{
			$city_id_value = $city_id;
			if (($translatable) && (!$app->isClient('administrator')))
			{
				$suffix = self::getFieldSuffix();
				$db->setQuery("Select city" . $suffix . " from #__osrs_cities where id = '$city_id_value'");
				$city_value = $db->loadResult();
			} 
			else 
			{
				$db->setQuery("Select city from #__osrs_cities where id = '$city_id_value'");
				$city_value = $db->loadResult();
			}
		}
		return $city_value;
    }

	public static function loadSateCode($state_id)
    {
		global $languages;
		static $state_id_value, $state_code_value;
		if((!$state_id_value) || ($state_id_value != $state_id))
		{
			$state_id_value = $state_id;
			$db = Factory::getDBO();
	        $db->setQuery("Select state_code from #__osrs_states where id = '$state_id'");
			$state_code_value = $db->loadResult();
		}
        return $state_code_value;
    }

    public static function loadSateName($state_id)
    {
        global $languages;
		static $state_id_value, $state_value;
        $db = Factory::getDBO();
        $lgs = self::getLanguages();
        $translatable = Multilanguage::isEnabled() && count($lgs);
		$app = Factory::getApplication();
		if((!$state_id_value) || ($state_id_value != $state_id)){
			$state_id_value = $state_id;
			if (($translatable) && (!$app->isClient('administrator'))){
				$suffix = self::getFieldSuffix();
				$db->setQuery("Select state_name" . $suffix . " from #__osrs_states where id = '$state_id_value'");
				$state_value = $db->loadResult();
			} else {
				$db->setQuery("Select state_name from #__osrs_states where id = '$state_id_value'");
				$state_value = $db->loadResult();
			}
		}
        return $state_value;
    }

    public static function loadCountryName($country_id)
    {
		$languages = self::getLanguages();
		$db = Factory::getDbo();
        $db->setQuery("Select * from #__osrs_countries where id = '$country_id'");
        $country = $db->loadObject();
        $translatable = Multilanguage::isEnabled() && count($languages);
        if($translatable){
            $country_name = self::getLanguageFieldValue($country,'country_name');
        }else{
			$country_name = $country->country_name;
		}
		return $country_name;
    }

    public static function returnDateformat($date)
    {
        return date("D, jS M Y H:i", $date);
    }

    public static function resizePhoto($dest, $width, $height)
    {
        global $configClass;
		
        $custom_thumbnail_photo = $configClass['custom_thumbnail_photo'];
        list($width_orig, $height_orig) = getimagesize($dest);
        if ($width_orig != $width || $height_orig != $height) 
		{
            switch($custom_thumbnail_photo)
			{
                case "0":
                    $thumbimage = new Image($dest);
                    $thumbimage->resize($width, $height);
                    $thumbimage->save($dest, $configClass['images_quality']);
                    break;
                case "2":
                    OsImageHelper::createImage($dest, $dest, $width, $height, true, $configClass['images_quality']);
                    break;
                case "1":
                    $thumbimage = new Image($dest);
                    $thumbimage->resize($width, $height);
                    $thumbimage->save($dest, $configClass['images_quality']);
                    break;
            }
        }
    }

	public static function adjustPhotoOriginal($image_path)
	{

		$info			= getimagesize($image_path);
		$width			= $info[0];
		$height			= $info[1];
		$type			= str_replace('image/', '', $info['mime']);
		$exif			= exif_read_data($image_path);

		$adjust			= 0;
		if ($exif && isset($exif['Orientation'])) 
		{
			$orientation = $exif['Orientation'];

			// Create an image resource from the file
			//$image = imagecreatefromjpeg($imagePath);
			switch ($type) 
			{
				case 'jpeg':
				case 'jpg':
					$image = imagecreatefromjpeg($image_path);
					break;

				case 'png':
					$image = imagecreatefrompng($image_path);
					break;

				case 'bmp':
					$image = imagecreatefrombmp($image_path);
					break;

				case 'gif':
					$image = imagecreatefromgif($image_path);
					break;

				case 'vnd.wap.wbmp':
					$image = imagecreatefromwbmp($image_path);
					break;

				case 'webp':
					$image = imagecreatefromxbm($image_path);
					break;

				default:
					$image = imagecreatefromjpeg($image_path);
				break;
			}


			switch ($orientation) 
			{
				case 3: // 180 degrees
					$adjust = 1;
					$image = imagerotate($image, 180, 0);
					break;
				case 6: // 90 degrees counter-clockwise
					$adjust = 1;
					$image = imagerotate($image, 270, 0);
					break;
				case 8: // 90 degrees clockwise
					$adjust = 1;
					$image = imagerotate($image, 90, 0);
					break;
				default:
					// No rotation needed for orientation 1 (Normal)
					break;
			}

			// Save the rotated image
			//imagejpeg($image, $image_path);
			if($adjust == 1)
			{
				switch ($type) 
				{
					case 'jpeg':
					case 'jpg':
						imagejpeg($image, $image_path);
						break;

					case 'png':
						imagepng($image, $image_path);
						break;

					case 'bmp':
						imagebmp($image, $image_path);
						break;

					case 'gif':
						imagegif($image, $image_path);
						break;

					case 'vnd.wap.wbmp':
						imagebmp($image, $image_path);
						break;

					case 'webp':
						imagexbm($image, $image_path);
						break;

					default:
						imagejpeg($image, $image_path);
						break;
				}
			}

			// Free up memory
			imagedestroy($image);
			
		}
	}

    public static function useBootstrapSlide()
    {
        global $configClass;
        $configClass = self::loadConfig();
        $load_bootstrap = $configClass['load_bootstrap'];
        if ((version_compare(JVERSION, '3.0', 'ge')) and (intval($load_bootstrap) == 0)) {
            return true;
        } else if ((version_compare(JVERSION, '3.0', 'ge')) and (intval($load_bootstrap) == 1)) {
            return false;
        } else if (version_compare(JVERSION, '3.0', 'lt')) {
            return false;
        } else {
            return false;
        }
    }

    public static function generateHeading($type, $title, $hardcode = 0)
    {
        global $jinput;
		$jinput = Factory::getApplication()->input;
		$org_title = $title;
        $document = Factory::getDocument();
        $app = Factory::getApplication();
        $menus = $app->getMenu('site');
        $menu = $menus->getActive();
		if($hardcode == 1)
		{
			if ($type == 1)
			{
				if($title != "")
				{
					$document->setTitle($title);
				}
			}
			else
			{
				if($title != ""){
					?>
					<h1 class="componentheading">
						<?php
						echo $title;
						?>
					</h1>
                    <?php
				}
			}
		}
		elseif (is_object($menu)) 
		{
            $params = new Registry();
            //$params->loadString($menu->getParams());
			$params = $menu->getParams();
            if ($params->get('menu-meta_description')) {
                $document->setDescription($params->get('menu-meta_description'));
            }

            if ($params->get('menu-meta_keywords')) {
                $document->setMetadata('keywords', $params->get('menu-meta_keywords'));
            }

            if ($params->get('robots')) {
                $document->setMetadata('robots', $params->get('robots'));
            }
			
            if ($type == 1) {
                $page_title = $params->get('page_title', '');
                if ($page_title != "") {
					$title = $page_title;
                } elseif ($menu->title != "") {
					$title = $menu->title;
                }

				$task = $jinput->getString('task','');
				if($task == "property_details"){
					$title = $org_title;
				}
			
				if ($app->getCfg('sitename_pagetitles', 0) == 1)
				{
					$title = Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
				}
				elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
				{
					$title = Text::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
				}
				
				$document->setTitle($title);
            } else {
                $show_page_heading = $params->get('show_page_heading', 0);
                $page_heading = $params->get('page_heading', '');
                if ($show_page_heading == 1) {
                    if ($page_heading != "") {
                        ?>
                        <h1 class="componentheading">
                            <?php
                            echo $page_heading;
                            ?>
                        </h1>
                    <?php
                    } elseif ($menu->title != "") {
                        ?>
                        <h1 class="componentheading">
                            <?php
                            echo $menu->title;
                            ?>
                        </h1>
                    <?php
                    } else {
                        ?>
                        <h1 class="componentheading">
                            <?php
                            echo $title;
                            ?>
                        </h1>
                    <?php
                    }
                }
            }
        } else {
            if ($type == 1) {
				if ($app->getCfg('sitename_pagetitles', 0) == 1)
				{
					$title = Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
				}
				elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
				{
					$title = Text::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
				}
				$document->setTitle($title);
            } else {
                ?>
                <h1 class="componentheading">
                    <?php
                    echo $title;
                    ?>
                </h1>
            <?php
            }
        }
    }

    /**
     * This function is used to create the folder to save property's photo
     *
     * @param unknown_type $pid
     */
    public static function createPhotoDirectory($pid)
    {
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        if (!Folder::exists(JPATH_ROOT . '/images/osproperty/properties/' . $pid)) {
            Folder::create(JPATH_ROOT . '/images/osproperty/properties/' . $pid);
            //copy index.html to this folder
            File::copy(JPATH_COMPONENT ."/". 'index.html', JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/index.html');
		}
		if (!Folder::exists(JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/medium')) {
			Folder::create(JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/medium');
			File::copy(JPATH_COMPONENT ."/". 'index.html', JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/medium/index.html');
		}
		if (!Folder::exists(JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/thumb')) {
			Folder::create(JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/thumb');
			File::copy(JPATH_COMPONENT ."/". 'index.html', JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/thumb/index.html');
		}
    }

    /**
     * Moving photo from general directory to sub directory
     *
     * @param unknown_type $pid
     */
    public static function movingPhoto($pid)
    {
        jimport('joomla.filesystem.file');
        $db = Factory::getDbo();
        $db->setQuery("Select image from #__osrs_photos where pro_id = '$pid'");
        $rows = $db->loadObjectList();
        if (count($rows) > 0) {
            for ($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];
                if ((File::exists(JPATH_ROOT . '/images/osproperty/properties/' . $row->image)) and (!File::exists(JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/' . $row->image))) {
                    File::copy(JPATH_ROOT . '/images/osproperty/properties/' . $row->image, JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/' . $row->image);
                }
                if ((File::exists(JPATH_ROOT . '/images/osproperty/properties/medium/' . $row->image)) and (!File::exists(JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/medium/' . $row->image))) {
                    File::copy(JPATH_ROOT . '/images/osproperty/properties/medium/' . $row->image, JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/medium/' . $row->image);
                }
                if ((File::exists(JPATH_ROOT . '/images/osproperty/properties/thumb/' . $row->image)) and (!File::exists(JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/thumb/' . $row->image))) {
                    File::copy(JPATH_ROOT . '/images/osproperty/properties/thumb/' . $row->image, JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/thumb/' . $row->image);
                }
            }
        }
    }

    /**
     * Moving photo from general directory to sub directory in Sample Data installation
     *
     * @param unknown_type $pid
     */
    public static function movingPhotoSampleData($pid)
    {
        jimport('joomla.filesystem.file');
        $db = Factory::getDbo();
        $db->setQuery("Select image from #__osrs_photos where pro_id = '$pid'");
        $rows = $db->loadObjectList();
        if (count($rows) > 0) {
            for ($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (File::exists(JPATH_ROOT . '/images/osproperty/properties/' . $row->image)) {
                    File::copy(JPATH_ROOT . '/images/osproperty/properties/' . $row->image, JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/' . $row->image);
                }
                if (File::exists(JPATH_ROOT . '/images/osproperty/properties/medium/' . $row->image)) {
                    File::copy(JPATH_ROOT . '/images/osproperty/properties/medium/' . $row->image, JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/medium/' . $row->image);
                }
                if (File::exists(JPATH_ROOT . '/images/osproperty/properties/thumb/' . $row->image)) {
                    File::copy(JPATH_ROOT . '/images/osproperty/properties/thumb/' . $row->image, JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/thumb/' . $row->image);
                }
            }
        }
    }

    /**
     * Show Property photo
     *
     * @param unknown_type $image
     * @param unknown_type $image_folder
     * @param unknown_type $pid
     * @param unknown_type $style
     * @param unknown_type $class
     * @param unknown_type $js
     */
    public static function showPropertyPhoto($image, $image_folder, $pid, $style, $class, $js, $loadlazy = 1,$alt = '')
    {
		$configClass = self::loadConfig();
		if ($image_folder != "") {
            $image_folder = $image_folder . '/';
        }
		if(($configClass['load_lazy']) && ($loadlazy == 1)){
			$photourl = Uri::root()."media/com_osproperty/assets/images/loader.gif";
		}else{
			$photourl = Uri::root().'images/osproperty/properties/'.$pid . '/' . $image_folder . $image;	
		}
        if ($image != "") {
            if (file_exists(JPATH_ROOT . '/images/osproperty/properties/' . $pid . '/' . $image_folder . $image)) {
                ?>
                <img
                    src="<?php echo $photourl; ?>" data-original="<?php echo Uri::root() ?>images/osproperty/properties/<?php echo $pid . '/' . $image_folder . $image; ?>"
                    class="<?php echo $class ?> oslazy" style="<?php echo $style ?>" <?php echo $js ?>  alt="<?php echo $alt; ?>" />
            <?php
            } else {
                ?>
                <img src="<?php echo Uri::root() ?>media/com_osproperty/assets/images/nopropertyphoto.png"
                     class="<?php echo $class ?>" style="<?php echo $style ?>"/>
            <?php
            }
        } else {
            ?>
            <img src="<?php echo Uri::root() ?>media/com_osproperty/assets/images/nopropertyphoto.png"
                 class="<?php echo $class ?>" style="<?php echo $style ?>"/>
        <?php
        }
    }

    public static function checkImage($image)
    {
        //checks if the file is a browser compatible image
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        $mimes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png');
        //get mime type
        $mime = getimagesize($image);
        $mime = $mime ['mime'];

        $extensions = array('jpg','jpeg','png','gif','webp');
        $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        if (in_array($extension, $extensions) and in_array($mime, $mimes))
            return TRUE;
        else
            File::delete($image);
        return 'application/octet-stream';
    }


    public static function getImages($folder)
    {
        $files = [];
        $images = [];

        // check if directory exists
        if (is_dir($folder)) {
            if ($handle = opendir($folder)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..' && $file != 'CVS' && $file != 'index.html') {
                        $files [] = $file;
                    }
                }
            }
            closedir($handle);
            $i = 0;
            foreach ($files as $img) {
                if (!is_dir($folder ."/". $img)) {
//					self::checkImage($folder ."/". $img);
					$tmp = new stdClass();
					$tmp->name = $img;
					$tmp->folder = $folder;
					$images [$i] = $tmp;
                    ++$i;
                }
            }
        }
        return $images;
    }

    /**
     * Generate alias
     *
     * @param unknown_type $type
     * @param unknown_type $id
     * @param unknown_type $alias
     */
    static function generateAlias($type, $id, $alias='')
    {
        global $mainframe;
        $db = Factory::getDbo();
        if ($alias != "") {
            //$alias = JString::increment($alias, 'dash');
            $alias   = ApplicationHelper::stringURLSafe($alias);
        }
        switch ($type) {
            case "property":
                if ($alias != "") {
                    $db->setQuery("Select count(id) from #__osrs_properties where pro_alias like '$alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $pro_alias = $alias . " " . $id;
                    } else {
                        $pro_alias = $alias;
                    }
                } else {
                    $db->setQuery("Select pro_name from #__osrs_properties where id = '$id'");
                    $pro_name = $db->loadResult();
                    //$pro_alias   = JApplicationHelper::stringURLSafe($pro_name);
                    $pro_alias = ApplicationHelper::stringURLSafe($pro_name);
                    //$pro_alias = JString::increment($pro_name, 'dash');
                    if($pro_alias == ""){
                        $pro_alias = Text::_('OS_PROPERTY')."-".date("Y-m-d H:i:s",time());
                        $pro_alias = ApplicationHelper::stringURLSafe($pro_alias);
                    }
                    $db->setQuery("Select count(id) from #__osrs_properties where pro_alias like '$pro_alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $pro_alias = $pro_alias . " " . $id;
                    }
                }
                $pro_alias = ApplicationHelper::stringURLSafe($pro_alias);
                return $pro_alias;
                break;
            case "agent":
                if ($alias != "") {
                    $db->setQuery("Select count(id) from #__osrs_agents where alias like '$alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $agent_alias = $alias . " " . $id;
                    } else {
                        $agent_alias = $alias;
                    }
                } else {
                    $db->setQuery("Select name from #__osrs_agents where id = '$id'");
                    $agent_name = $db->loadResult();
                    $agent_alias = ApplicationHelper::stringURLSafe($agent_name);
                    if($agent_alias == ""){
                        $agent_alias = Text::_('OS_AGENT')."-".date("Y-m-d H:i:s",time());
                        $agent_alias = ApplicationHelper::stringURLSafe($agent_alias);
                    }
                    $db->setQuery("Select count(id) from #__osrs_agents where alias like '$agent_alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $agent_alias = $agent_alias . " " . $id;
                    }
                }
                //$agent_alias = mb_strtolower(str_replace(" ", "-", $agent_alias));
                $agent_alias = ApplicationHelper::stringURLSafe($agent_alias);
                return $agent_alias;
                break;
            case "company":
                if ($alias != "") {
                    $db->setQuery("Select count(id) from #__osrs_companies where company_alias like '$alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $company_alias = $alias . " " . $id;
                    } else {
                        $company_alias = $alias;
                    }
                } else {
                    $db->setQuery("Select company_name from #__osrs_companies where id = '$id'");
                    $company_name = $db->loadResult();
                    $company_alias = ApplicationHelper::stringURLSafe($company_name);
                    if($company_alias == ""){
                        $company_alias = Text::_('OS_COMPANY')."-".date("Y-m-d H:i:s",time());
                        $company_alias = ApplicationHelper::stringURLSafe($company_alias);
                    }
                    $db->setQuery("Select count(id) from #__osrs_companies where company_alias like '$company_alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $company_alias = $company_alias . " " . $id;
                    }
                }
               // $company_alias = mb_strtolower(str_replace(" ", "-", $company_alias));
                $company_alias = ApplicationHelper::stringURLSafe($company_alias);
                return $company_alias;
                break;
            case "category":
                if ($alias != "") {
                    $db->setQuery("Select count(id) from #__osrs_categories where category_alias like '$alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $category_alias = $alias . " " . $id;
                    } else {
                        $category_alias = $alias;
                    }
                } else {
                    $db->setQuery("Select category_name from #__osrs_categories where id = '$id'");
                    $category_name = $db->loadResult();
                    $category_alias = ApplicationHelper::stringURLSafe($category_name);
                    if($category_alias == ""){
                        $category_alias = Text::_('OS_CATEGORY')."-".date("Y-m-d H:i:s",time());
                        $category_alias = ApplicationHelper::stringURLSafe($category_alias);
                    }
                    $db->setQuery("Select count(id) from #__osrs_categories where category_alias like '$category_alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $category_alias = $category_alias . " " . $id;
                    }
                }
                //$category_alias = mb_strtolower(str_replace(" ", "-", $category_alias));
                $category_alias = ApplicationHelper::stringURLSafe($category_alias);
                return $category_alias;
                break;
            case "type":
                if ($alias != "") {
                    $db->setQuery("Select count(id) from #__osrs_types where type_alias like '$alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $type_alias = $alias . " " . $id;
                    } else {
                        $type_alias = $alias;
                    }
                } else {
                    $db->setQuery("Select type_name from #__osrs_types where id = '$id'");
                    $type_name = $db->loadResult();
                    $type_alias = ApplicationHelper::stringURLSafe($type_name);
                    if($type_alias == ""){
                        $type_alias = Text::_('OS_TYPE')."-".date("Y-m-d H:i:s",time());
                        $type_alias = ApplicationHelper::stringURLSafe($type_alias);
                    }
                    $db->setQuery("Select count(id) from #__osrs_types where type_alias like '$type_alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $type_alias = $type_alias . " " . $id;
                    }
                }
                $type_alias = ApplicationHelper::stringURLSafe($type_alias);
                return $type_alias;
                break;
        }
    }

    /**
     * Generate alias
     *
     * @param unknown_type $type
     * @param unknown_type $id
     * @param unknown_type $alias
     */
    static function generateAliasMultipleLanguages($type, $id, $alias, $langCode)
    {
        global $mainframe;
        $db = Factory::getDbo();
        if ($alias != "") {
            $alias = ApplicationHelper::stringURLSafe($alias);
        }
        switch ($type) {
            case "property":
                $alias_field_name = "pro_alias_" . $langCode;
                if ($alias != "") {
                    $db->setQuery("Select count(id) from #__osrs_properties where `$alias_field_name` like '$alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $pro_alias = $alias . " " . $id;
                    } else {
                        $pro_alias = $alias;
                    }
                } else {
                    $db->setQuery("Select pro_name_$langCode from #__osrs_properties where id = '$id'");
                    $pro_name = $db->loadResult();
                    $pro_alias = ApplicationHelper::stringURLSafe($pro_name);
                    if($pro_alias == ""){
                        $pro_alias = Text::_('OS_PROPERTY')."-".date("Y-m-d H:i:s",time());
                        $pro_alias = ApplicationHelper::stringURLSafe($pro_alias);
                    }
                    $db->setQuery("Select count(id) from #__osrs_properties where `$alias_field_name` like '$pro_alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $pro_alias = $pro_alias . " " . $id;
                    }
                }
                $pro_alias = ApplicationHelper::stringURLSafe($pro_alias);
                return $pro_alias;
                break;
            case "agent":
                if ($alias != "") {
                    $db->setQuery("Select count(id) from #__osrs_agents where alias like '$alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $agent_alias = $alias . " " . $id;
                    } else {
                        $agent_alias = $alias;
                    }
                } else {
                    $db->setQuery("Select name from #__osrs_agents where id = '$id'");
                    $agent_name = $db->loadResult();
                    $agent_alias = ApplicationHelper::stringURLSafe($agent_name);
                    if($agent_alias == ""){
                        $agent_alias = Text::_('OS_AGENT')."-".date("Y-m-d H:i:s",time());
                        $agent_alias = ApplicationHelper::stringURLSafe($agent_alias);
                    }
                    $db->setQuery("Select count(id) from #__osrs_agents where alias like '$agent_alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $agent_alias = $agent_alias . " " . $id;
                    }
                }
                $agent_alias = ApplicationHelper::stringURLSafe($agent_alias);
                return $agent_alias;
                break;
            case "company":
                if ($alias != "") {
                    $db->setQuery("Select count(id) from #__osrs_companies where company_alias like '$alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $company_alias = $alias . " " . $id;
                    } else {
                        $company_alias = $alias;
                    }
                } else {
                    $db->setQuery("Select company_name from #__osrs_companies where id = '$id'");
                    $company_name = $db->loadResult();
                    $company_alias = ApplicationHelper::stringURLSafe($company_name);
                    if($company_alias == ""){
                        $company_alias = Text::_('OS_COMPANY')."-".date("Y-m-d H:i:s",time());
                        $company_alias = ApplicationHelper::stringURLSafe($company_alias);
                    }
                    $db->setQuery("Select count(id) from #__osrs_companies where company_alias like '$company_alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $company_alias = $company_alias . " " . $id;
                    }
                }
                $company_alias = ApplicationHelper::stringURLSafe($company_alias);
                return $company_alias;
                break;
            case "category":
                $alias_field_name = "category_alias_" . $langCode;
                if ($alias != "") {
                    $db->setQuery("Select count(id) from #__osrs_categories where `$alias_field_name` like '$alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $category_alias = $alias . " " . $id;
                    } else {
                        $category_alias = $alias;
                    }
                } else {
                    $db->setQuery("Select category_name_" . $langCode . " from #__osrs_categories where id = '$id'");
                    $category_name = $db->loadResult();
                    $category_alias = ApplicationHelper::stringURLSafe($category_name);
                    if($category_alias == ""){
                        $category_alias = Text::_('OS_CATEGORY')."-".date("Y-m-d H:i:s",time());
                        $category_alias = ApplicationHelper::stringURLSafe($category_alias);
                    }
                    $db->setQuery("Select count(id) from #__osrs_categories where `$alias_field_name` like '$category_alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $category_alias = $category_alias . " " . $id;
                    }
                }
                $category_alias = ApplicationHelper::stringURLSafe($category_alias);
                return $category_alias;
                break;
            case "type":
                $alias_field_name = "type_alias_" . $langCode;
                if ($alias != "") {
                    $db->setQuery("Select count(id) from #__osrs_types where `$alias_field_name` like '$alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $type_alias = $alias . " " . $id;
                    } else {
                        $type_alias = $alias;
                    }
                } else {
                    $db->setQuery("Select type_name_" . $langCode . " from #__osrs_types where id = '$id'");
                    $type_name = $db->loadResult();
                    $type_alias = ApplicationHelper::stringURLSafe($type_name);
                    if($type_alias == ""){
                        $type_alias = Text::_('OS_TYPE')."-".date("Y-m-d H:i:s",time());
                        $type_alias = ApplicationHelper::stringURLSafe($type_alias);
                    }
                    $db->setQuery("Select count(id) from #__osrs_types where `$alias_field_name` like '$type_alias' and id <> '$id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $type_alias = $type_alias . " " . $id;
                    }
                }
                $type_alias = ApplicationHelper::stringURLSafe($type_alias);
                return $type_alias;
                break;
        }
    }

    /**
     * Get IP address of customers
     *
     * @return unknown
     */
    public static function get_ip_address()
    {
        foreach (array(
                     'HTTP_CLIENT_IP',
                     'HTTP_X_FORWARDED_FOR',
                     'HTTP_X_FORWARDED',
                     'HTTP_X_CLUSTER_CLIENT_IP',
                     'HTTP_FORWARDED_FOR',
                     'HTTP_FORWARDED',
                     'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

    /**
     * Get data by using curl
     *
     * @param unknown_type $path
     * @return unknown
     */
    public static function get_data($path)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $retValue = curl_exec($ch);
        curl_close($ch);
        return $retValue;
    }

    /**
     * Spam deteach
     */
    public static function spamChecking()
    {
        global $jinput;
        $botscoutUrl = 'http://www.stopforumspam.com/api?ip=';
        $accFrequency = 0;
        $access = 'yes';
        $option = $jinput->getString('option');
        // Check we are manipulating a valid form and if we are in admin.
        $ip = self::get_ip_address();
        $url = $botscoutUrl . $ip;
        $xmlDatas = simplexml_load_string(self::get_data($url));
        if ($xmlDatas->appears == $access && $xmlDatas->frequency >= $accFrequency) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Build the select list for parent menu item
     */
    public static function listCategoriesInMultiple($category_ids, $onChangeScript)
    {
        global $mainframe;
        $parentArr = [];
        $parentArr = self::loadCategoryOptions($category_ids, $onChangeScript, 0);
        $output = HTMLHelper::_('select.genericlist', $parentArr, 'category_ids[]', 'multiple class="input-large chosen categorydropdown" ' . $onChangeScript, 'value', 'text', $category_ids);
        return $output;
    }

    /**
     * Build the select list for parent menu item
     */
    public static function listCategories($category_id, $onChangeScript)
    {
        global $mainframe;
        $parentArr = [];
        $parentArr = self::loadCategoryOptions($category_id, $onChangeScript, 1);
        $output = HTMLHelper::_('select.genericlist', $parentArr, 'category_id', 'class="form-select" ' . $onChangeScript, 'value', 'text', $category_id);
        return $output;
    }

    public static function loadCategoryOptions($category_id, $onChangeScript, $hasFirstOption = 0)
    {
        global $mainframe, $lang_suffix;
        $user = Factory::getUser();
		$app = Factory::getApplication();
        if ($app->isClient('administrator')) {
            $lang_suffix = "";
        } else {
            $lang_suffix = OSPHelper::getFieldSuffix();
        }
        $db = Factory::getDBO();

        $query = 'SELECT *,id as value,category_name' . $lang_suffix . ' AS text,category_name' . $lang_suffix . ' AS treename,category_name' . $lang_suffix . ' as category_name,category_name' . $lang_suffix . ' as title,parent_id as parent ' .
            ' FROM #__osrs_categories ' .
            ' WHERE published = 1';
        
        $query .= ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        $query .= ' ORDER BY parent_id, ordering';
        $db->setQuery($query);
        $mitems = $db->loadObjectList();
        // establish the hierarchy of the menu
        $children = [];
        if ($mitems) {
            // first pass - collect children
            foreach ($mitems as $v) {
                $pt = $v->parent_id;
                if ($v->treename == "") {
                    $v->treename = $v->category_name;
                }
                if ($v->title == "") {
                    $v->title = $v->category_name;
                }
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        // second pass - get an indent list of the items
        $list = HTMLHelper::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        // assemble menu items to the array
        $parentArr = [];
        if ($hasFirstOption == 1) {
            $parentArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_CATEGORIES'));
        }

        foreach ($list as $item) {
            //if($item->treename != ""){
            //$item->treename = str_replace("&nbsp;","*",$item->treename);
            //}
            $var = explode("*", $item->treename);

            if (count($var) > 0) {
                $treename = "";
                for ($i = 0; $i < count($var) - 1; $i++) {
                    $treename .= " _ ";
                }
            }
            $text = $item->treename;
            $parentArr[] = HTMLHelper::_('select.option', $item->id, $text);
        }
        return $parentArr;
    }


    /**
     * Build the multiple select list for parent menu item
     */
    public static function listCategoriesCheckboxes($categoryArr)
    {
        global $mainframe;
        $db = Factory::getDbo();
        $db->setQuery("Select count(id) from #__osrs_categories where published = '1'");
        $count_categories = $db->loadResult();
        $parentArr = self::loadCategoryBoxes($categoryArr);
        ob_start();
        ?>
        <input type="checkbox" name="check_all_cats" id="check_all_cats" value="1" checked
               onclick="javascript:checkCats()"/>&nbsp;&nbsp;<strong><?php echo Text::_('OS_CATEGORIES')?></strong>
        <input type="hidden" name="count_categories" id="count_categories" value="<?php echo $count_categories?>"/>
        <BR/>
        <?php
        for ($i = 0; $i < count($parentArr); $i++) {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $parentArr[$i];
            echo "<BR />";
        }
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public static function loadCategoryBoxes($categoryArr, $name = 'categoryArr')
    {
        global $mainframe, $lang_suffix;
        $db = Factory::getDBO();
        $lang_suffix = OSPHelper::getFieldSuffix();
        // get a list of the menu items
        // excluding the current cat item and its child elements
//		$query = 'SELECT *' .
        $query = 'SELECT *, id as value,category_name' . $lang_suffix . ' AS title,category_name' . $lang_suffix . ' AS category_name,parent_id as parent ' .
            ' FROM #__osrs_categories ' .
            ' WHERE published = 1';
        $user = Factory::getUser();
        
        $query .= ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        $query .= ' ORDER BY parent_id, ordering';
        $db->setQuery($query);
        $mitems = $db->loadObjectList();

        // establish the hierarchy of the menu
        $children = [];

        if ($mitems) {
            // first pass - collect children
            foreach ($mitems as $v) {
                $pt = $v->parent_id;
                $list = @$children[$pt] ? $children[$pt] : [];
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        // second pass - get an indent list of the items
        $list = HTMLHelper::_('menu.treerecurse', 0, '',[], $children, 9999, 0, 0);
        // assemble menu items to the array
        $parentArr = [];

        foreach ($list as $item) 
		{
            $checked = "";
            if ($item->treename != "") {
                $item->treename = str_replace("&nbsp;", "", $item->treename);
            }
            $var = explode("-", $item->treename);
            $treename = "";
            for ($i = 0; $i < count($var) - 1; $i++) {
                $treename .= "- -";
            }
            $text = $treename . $item->category_name;
            if (isset($categoryArr)) {
                if (in_array($item->value, $categoryArr)) {
                    $checked = "checked";
                } elseif (count($categoryArr) == 0) {
                    $checked = "checked";
                }
            }
            $parentArr[] = '<input type="checkbox" id="all_categories' . $item->value . '" name="'.$name.'[]" ' . $checked . ' value="' . $item->value . '" data-value="'.$item->category_name.'" />&nbsp;&nbsp;' . $text . '';
        }
        return $parentArr;
    }

    public static function loadAgentType($agent_id)
    {
        global $mainframe;
        $db = Factory::getDbo();
        $db->setQuery("Select agent_type from #__osrs_agents where id = '$agent_id'");
        $agent_type = $db->loadResult();
        switch ($agent_type) {
            case "0":
            default:
                return Text::_('OS_AGENT');
                break;
            case "1":
                return Text::_('OS_OWNER');
                break;
			case "2":
                return Text::_('OS_REALTOR');
                break;
			case "3":
                return Text::_('OS_BROKER');
                break;
			case "4":
                return Text::_('OS_BUILDER');
                break;
			case "5":
                return Text::_('OS_LANDLORD');
                break;
			case "6":
                return Text::_('OS_SELLER');
                break;
			
        }
    }

	public static function loadAgentTypeValue($agent_id)
    {
		$db = Factory::getDbo();
        $db->setQuery("Select agent_type from #__osrs_agents where id = '$agent_id'");
        return (int) $db->loadResult();
	}

    public static function loadAgentTypeDropdown($agent_type,$class_name,$onChange)
    {
        global $mainframe,$bootstrapHelper;
		$configClass = self::loadConfig();
		$user_types = $configClass['user_types'];
		$user_types = explode(",",$user_types);
        $optionArr = [];
		if(in_array(0,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '0', Text::_('OS_AGENT'));
		}
		if(in_array(1,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '1', Text::_('OS_OWNER'));
		}
		if(in_array(2,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '2', Text::_('OS_REALTOR'));
		}
		if(in_array(3,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '3', Text::_('OS_BROKER'));
		}
		if(in_array(4,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '4', Text::_('OS_BUILDER'));
		}
		if(in_array(5,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '5', Text::_('OS_LANDLORD'));
		}
		if(in_array(6,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '6', Text::_('OS_SELLER'));
		}

        echo HTMLHelper::_('select.genericlist', $optionArr, 'agent_type', 'class="'.$class_name.' form-select ilarge" '.$onChange, 'value', 'text', $agent_type);
    }

	public static function loadAgentTypeDropdownFilter($agent_type,$class_name,$onChange)
    {
        global $mainframe;
		$configClass = self::loadConfig();
		$user_types = $configClass['user_types'];
		$user_types = explode(",",$user_types);

        $optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option', '-1', Text::_('OS_SELECT_USER_TYPE'));
        if(in_array(0,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '0', Text::_('OS_AGENT'));
		}
		if(in_array(1,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '1', Text::_('OS_OWNER'));
		}
		if(in_array(2,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '2', Text::_('OS_REALTOR'));
		}
		if(in_array(3,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '3', Text::_('OS_BROKER'));
		}
		if(in_array(4,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '4', Text::_('OS_BUILDER'));
		}
		if(in_array(5,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '5', Text::_('OS_LANDLORD'));
		}
		if(in_array(6,$user_types)){
			$optionArr[] = HTMLHelper::_('select.option', '6', Text::_('OS_SELLER'));
		}
        echo HTMLHelper::_('select.genericlist', $optionArr, 'agent_type', 'class="'.$class_name.'" '.$onChange, 'value', 'text', $agent_type);
    }

    public static function getStringRequest($name, $defaultvalue = '', $method = 'post')
    {
        $db = Factory::getDbo();
        $jinput = Factory::getApplication()->input;
        $temp = $jinput->get($name, $defaultvalue, 'string');
        $badchars = array('#', '>', '<', '\\','%','\'','"');
        $temp = trim(str_replace($badchars, '', $temp));
        $temp = $db->escape($temp);
        $temp = htmlspecialchars($temp);
        return $temp;
    }

    static function showSquareLabels()
    {
        global $mainframe, $configClass;
        $configClass = self::loadConfig();
        if ($configClass['use_square'] == 0) {
            return Text::_('OS_SQUARE_FEET');
        } else {
            return Text::_('OS_SQUARE_METER');
        }
    }

    static function showSquareSymbol()
    {
        global $mainframe, $configClass;
        $configClass = self::loadConfig();
        if ($configClass['use_square'] == 0) {
            return Text::_('OS_SQFT');
        } else {
            return Text::_('OS_SQMT');
        }
    }

    static function showAcresSymbol()
    {
        global $mainframe, $configClass;
        $configClass = self::loadConfig();
        if ($configClass['acreage'] == 0) {
            return Text::_('OS_ACRES');
        } else {
            return Text::_('OS_HECTARES');
        }
    }

    /**
     * Converts a given size with units e.g. read from php.ini to bytes.
     *
     * @param   string $val Value with units (e.g. 8M)
     * @return  int     Value in bytes
     * @since   3.0
     */
    public static function iniToBytes($val)
    {
        $val = trim($val);

        switch (strtolower(substr($val, -1))) {
            case 'm':
                $val = (int)substr($val, 0, -1) * 1048576;
                break;
            case 'k':
                $val = (int)substr($val, 0, -1) * 1024;
                break;
            case 'g':
                $val = (int)substr($val, 0, -1) * 1073741824;
                break;
            case 'b':
                switch (strtolower(substr($val, -2, 1))) {
                    case 'm':
                        $val = (int)substr($val, 0, -2) * 1048576;
                        break;
                    case 'k':
                        $val = (int)substr($val, 0, -2) * 1024;
                        break;
                    case 'g':
                        $val = (int)substr($val, 0, -2) * 1073741824;
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }

        return $val;
    }


    /**
     * Generate price value
     *
     * @param unknown_type $curr
     * @param unknown_type $price
     */
    public static function generatePrice($curr, $price)
    {
        global $configClass;
        $configClass = self::loadConfig();
        if ($configClass['currency_position'] == 0) 
		{
            return HelperOspropertyCommon::loadCurrency($curr) . " " . self::showPrice($price);
        } 
		else 
		{
            return self::showPrice($price) . " " . HelperOspropertyCommon::loadCurrency($curr);
        }
    }

    /**
     * Show Price Filter
     *
     * @param unknown_type $option_id
     * @param unknown_type $max_price
     * @param unknown_type $min_price
     * @param unknown_type $property_type
     * @param unknown_type $style
     */
    public static function showPriceFilter($option_id, $min_price, $max_price, $property_type, $style, $prefix)
    {
        global $configClass,$bootstrapHelper;
        $configClass		= self::loadConfig();
        $document			= Factory::getDocument();
        $db					= Factory::getDbo();
        $min_price_slider	= $configClass['min_price_slider'];
        $max_price_slider	= $configClass['max_price_slider'];
        $price_step_amount	= $configClass['price_step_amount'];
		$min_price			= (float) $min_price;
		$max_price			= (float) $max_price;
        if($price_step_amount == ""){
            $price_step_amount = 1000;
        }

        if($property_type > 0)
		{
            if($configClass['type'.$property_type] != "1"){
                $value = $configClass['type'.$property_type];
                $valueArr = explode("|",$value);
                $min_price_slider  = $valueArr[1];
                $max_price_slider  = $valueArr[2];
                $price_step_amount = $valueArr[3];
            }
        }

        if($max_price_slider != ""){
            $max_price_value = $max_price_slider;
        }else {
            $db->setQuery("Select price from #__osrs_properties order by price desc limit 1");
            $max_price_value = $db->loadResult();
        }
        if($min_price_slider != ""){
            $min_price_value = $min_price_slider;
        }else{
            $db->setQuery("Select price from #__osrs_properties where price_call = 0 order by price limit 1");
            $min_price_value = $db->loadResult();
        }


        if (intval($max_price) == 0) {
            $max_price = $max_price_value;
        }
        if ($min_price_value == $max_price_value) {
            if($min_price_slider != ""){
                $max_price = $min_price_slider;
            }else{
                $max_price = 0;
            }
        }
        if ($configClass['price_filter_type'] == 1) {
            $document->addStyleSheet("//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css");
			//$document->addStyleSheet("//code.jquery.com/ui/1.10.4/themes/smoothness/jquery.ui.slider-rtl.css");
            ?>
            <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js" type="text/javascript"></script>			
            <script src="<?php echo Uri::root() ?>media/com_osproperty/assets/js/jquery.ui.touch-punch.js" type="text/javascript" defer="defer"></script>
			<script src="<?php echo Uri::root() ?>media/com_osproperty/assets/js/autoNumeric.js" type="text/javascript" defer="defer"></script>
            <div id="<?php echo $prefix; ?>sliderange"></div>
            <div class="clearfix"></div>
            <script>
                jQuery.ui.slider.prototype.widgetEventPrefix = 'slider';
                jQuery(function () {
					jQuery("#<?php echo $prefix;?>sliderange")[0].slide = null;
                    jQuery("#<?php echo $prefix;?>sliderange").slider({
						//isRTL: true,
                        range: true,
                        min: <?php echo intval($min_price_value);?>,
                        step: <?php echo $price_step_amount;?>,
                        max: <?php echo intval($max_price_value);?>,
                        values: [<?php echo intval($min_price);?>, <?php echo intval($max_price);?>],
                        slide: function (event, ui) {
                            var price_from = ui.values[0];
                            var price_to = ui.values[1];
                            jQuery("#<?php echo $prefix;?>price_from_input1").val(price_from);
                            jQuery("#<?php echo $prefix;?>price_to_input1").val(price_to);

                            price_from = price_from.formatMoney(0, '<?php echo isset($configClass["thounsands_separator"])? $configClass["thounsands_separator"]:"."; ?>', '<?php echo isset($configClass["decimals_separator"])? $configClass["decimals_separator"]:","; ?>');
                            price_to = price_to.formatMoney(0, '<?php echo isset($configClass["thounsands_separator"])? $configClass["thounsands_separator"]:"."; ?>', '<?php echo isset($configClass["decimals_separator"])? $configClass["decimals_separator"]:","; ?>');

                            jQuery("#<?php echo $prefix;?>price_from_input").text(price_from);
                            jQuery("#<?php echo $prefix;?>price_to_input").text(price_to);
                        }
                    });

					jQuery("#<?php echo $prefix;?>price_from_input1").change(function(){
						  var value1=jQuery("#<?php echo $prefix;?>price_from_input1").val();
						  var value2=jQuery("#<?php echo $prefix;?>price_to_input1").val();
						  if(parseInt(value1) > parseInt(value2)){
							value1 = value2;
							jQuery(".#<?php echo $prefix;?>price_from_input1").val(value1);
						  }
						  jQuery("#<?php echo $prefix;?>sliderange").slider("values",0,value1);
					});
						  
					jQuery("#<?php echo $prefix;?>price_to_input1").change(function(){
						  var value1=jQuery("#<?php echo $prefix;?>price_from_input1").val();
						  var value2=jQuery("#<?php echo $prefix;?>price_to_input1").val();
						  if(parseInt(value1) > parseInt(value2)){
							value2 = value1;
							jQuery("#<?php echo $prefix;?>price_to_input1").val(value2);
						  }
						  jQuery("#<?php echo $prefix;?>sliderange").slider("values",1,value2);
					});
                });
                Number.prototype.formatMoney = function (decPlaces, thouSeparator, decSeparator) {
                    var n = this,
                        decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
                        decSeparator = decSeparator == undefined ? "." : decSeparator,
                        thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
                        sign = n < 0 ? "-" : "",
                        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
                        j = (j = i.length) > 3 ? j % 3 : 0;
                    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
                };
            </script>

            <?php
            if ((strpos($prefix, "adv") === FALSE) and (strpos($prefix, "list") === FALSE)) {
                $span = $bootstrapHelper->getClassMapping('span6');//"span6";
                $style = "margin-top:10px;margin-left:0px;";
                $style1 = "font-size:11px;text-align:left; width: 48.93617021276595%;*width: 48.88297872340425%;float:left;";
                $style2 = "font-size:11px;text-align:right; width: 48.93617021276595%;*width: 48.88297872340425%;float:left;";
                $input_class_name = "input-mini";
            } else {
                $span = $bootstrapHelper->getClassMapping('span6');//"span6";
                $style = "";
                $style1 = "margin-top:10px;margin-left:0px;text-align:left;width: 48.93617021276595%; *width: 48.88297872340425%;float:left;";
                $style2 = "margin-top:10px;margin-left:0px;text-align:right;width: 48.93617021276595%; *width: 48.88297872340425%;float:left;";
                $input_class_name = "input-small";
            }
            ?>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $span ?>" style="<?php echo $style; ?><?php echo $style1 ?>">
                    <?php if ((strpos($prefix, "adv") !== FALSE) or (strpos($prefix, "list") !== FALSE)) { ?>
                        <?php echo Text::_('OS_MIN') ?>
                    <?php } ?>
                    (<?php echo HelperOspropertyCommon::loadCurrency(0); ?>).
                    <span
                        id="<?php echo $prefix; ?>price_from_input"><?php echo number_format($min_price, 0, $configClass['decimals_separator'], $configClass['thounsands_separator']); ?></span>
                    <input type="hidden" name="min_price" id="<?php echo $prefix; ?>price_from_input1"
                           value="<?php echo $min_price; ?>"/>
                </div>
                <div class="<?php echo $span ?>" style="<?php echo $style; ?><?php echo $style2 ?>">
                    <?php if ((strpos($prefix, "adv") !== FALSE) or (strpos($prefix, "list") !== FALSE)) { ?>
                        <?php echo Text::_('OS_MAX') ?>
                    <?php } ?>
                    (<?php echo HelperOspropertyCommon::loadCurrency(0); ?>).
                    <span
                        id="<?php echo $prefix; ?>price_to_input"><?php echo number_format($max_price, 0, $configClass['decimals_separator'], $configClass['thounsands_separator']); ?></span>
                    <input type="hidden" name="max_price" id="<?php echo $prefix; ?>price_to_input1"
                           value="<?php echo $max_price; ?>"/>
                </div>
            </div>
		<?php
        } else {
            echo HelperOspropertyCommon::generatePriceList($property_type, $option_id, $style);
        }
    }

    public static function showPriceTypesConfig(){
		static $property_types;
        $configClass = self::loadConfig();
        $db = Factory::getDbo();
		if($property_types == null){
			$db->setQuery("Select * from #__osrs_types order by ordering");
			$property_types = $db->loadObjectList();
		}
		?>
		<input type="hidden" name="min" id="min" value="<?php echo $configClass['min_price_slider'];?>" />
		<input type="hidden" name="max" id="max" value="<?php echo $configClass['max_price_slider'];?>" />
		<input type="hidden" name="step" id="step" value="<?php echo $configClass['price_step_amount'];?>" />
		<?php
        for($i=0;$i<count($property_types);$i++) {
            $property_type = $property_types[$i];
            $type = $configClass['type'.$property_type->id];
            if($type == "1"){
                ?>
                <input type="hidden" name="min<?php echo $property_type->id;?>" id="min<?php echo $property_type->id;?>" value="<?php echo $configClass['min_price_slider'];?>" />
                <input type="hidden" name="max<?php echo $property_type->id;?>" id="max<?php echo $property_type->id;?>" value="<?php echo $configClass['max_price_slider'];?>" />
                <input type="hidden" name="step<?php echo $property_type->id;?>" id="step<?php echo $property_type->id;?>" value="<?php echo $configClass['price_step_amount'];?>" />
                <?php
            }else{
                $valueArr = [];
                $valueArr = explode("|",$type);
                $min_price_slider  = $valueArr[1];
                $max_price_slider  = $valueArr[2];
                $price_step_amount = $valueArr[3];
                ?>
                    <input type="hidden" name="min<?php echo $property_type->id;?>" id="min<?php echo $property_type->id;?>" value="<?php echo $min_price_slider;?>" />
                    <input type="hidden" name="max<?php echo $property_type->id;?>" id="max<?php echo $property_type->id;?>" value="<?php echo $max_price_slider;?>" />
                    <input type="hidden" name="step<?php echo $property_type->id;?>" id="step<?php echo $property_type->id;?>" value="<?php echo $price_step_amount;?>" />
                <?php
            }
        }
    }
    /**
     * check Owner is existing or not
     *
     */
    public static function checkOwnerExisting()
    {
        global $mainframe;
        $db = Factory::getDbo();
        $db->setQuery("Select count(id) from #__osrs_agents where agent_type <> '0' and published = '1'");
        $count = $db->loadResult();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user use one state in the system
     */
    public static function userOneState()
    {
		static $count_state;
        $configClass = self::getConfig();
        if (!HelperOspropertyCommon::checkCountry()) {
            $defaultcounty = $configClass['show_country_id'];
			if($count_state == null){
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select('count(id)')->from('#__osrs_states')->where('country_id = "' . $defaultcounty . '" and published = "1"');
				$db->setQuery($query);
				$count_state = $db->loadResult();
			}
            if ($count_state == 1) {
                return true;
            }
        }
        return false;
    }

    public static function returnDefaultState()
    {
        $configClass = self::getConfig();
        if (self::userOneState()) {
            $defaultcounty = $configClass['show_country_id'];
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id')->from('#__osrs_states')->where('country_id = "' . $defaultcounty . '" and published = "1"');
            $db->setQuery($query);
            return $db->loadResult();
        }
        return 0;
    }

    public static function returnDefaultStateName()
    {
        $db = Factory::getDbo();
        $lgs = self::getLanguages();

        $translatable = Multilanguage::isEnabled() && count($lgs);
		$suffix = "";
		$app = Factory::getApplication();

        if (($translatable) and (!$app->isClient('administrator'))){
            $suffix = OSPHelper::getFieldSuffix();
        }
        if (self::returnDefaultState() > 0) {
            $query = $db->getQuery(true);
            $query->select('state_name' . $suffix . ' as state_name')->from('#__osrs_states')->where('id="' . self::returnDefaultState() . '"');
            $db->setQuery($query);
            return $db->loadResult();
        }
        return '';
    }

	public static function isJoomlaMultipleLanguages()
	{
		$lgs = self::getLanguages();
        $translatable = Multilanguage::isEnabled() && count($lgs);
		if($translatable)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

    public static function getConfig()
    {
		static $configClass;
		if($configClass == null){
			$db = Factory::getDbo();
			/*
			$db->setQuery("Select * from #__osrs_configuration");
			$configs = $db->loadObjectList();
			$configClass = [];
			foreach ($configs as $config) {
				$configClass[$config->fieldname] = $config->fieldvalue;
			}
			*/
			$configClass = self::loadConfig();

			$curr = $configClass['general_currency_default'];
			$arrCode = [];
			$arrSymbol = [];

			$db->setQuery("Select * from #__osrs_currencies where id = '$curr'");
			$currency = $db->loadObject();
			$symbol = $currency->currency_symbol;
			$index = -1;
			if ($symbol == "") {
				$symbol = '$';
			}

			$configClass['curr_symbol'] = $symbol;
		}
        return $configClass;
    }

    public static function dropdropBath($name, $bath, $class, $jsScript, $firstOption)
    {
        $configClass = self::loadConfig();
        $bathArr = [];
        $bathArr[] = HTMLHelper::_('select.option', '', Text::_($firstOption));
        for ($i = 1; $i <= 10; $i++) {
            $bathArr[] = HTMLHelper::_('select.option', $i.'.00', $i);
            if ($configClass['fractional_bath'] == 1) {
                $bathArr[] = HTMLHelper::_('select.option', $i . '.25', $i . '.25');
                $bathArr[] = HTMLHelper::_('select.option', $i . '.50', $i . '.50');
                $bathArr[] = HTMLHelper::_('select.option', $i . '.75', $i . '.75');
            }
        }
        return HTMLHelper::_('select.genericlist', $bathArr, $name, 'class="' . $class . '" ' . $jsScript, 'value', 'text', $bath);
    }

    public static function dropdropBed($name, $bed, $class, $jsScript, $firstOption)
    {
        $bedArr = [];
        $bedArr[] = HTMLHelper::_('select.option', '', Text::_($firstOption));
        for ($i = 1; $i <= 20; $i++) {
            $bedArr[] = HTMLHelper::_('select.option', $i, $i);
        }
        return HTMLHelper::_('select.genericlist', $bedArr, $name, 'class="' . $class . '" ' . $jsScript, 'value', 'text', $bed);
    }

    public static function dropdropRoom($name, $room, $class, $jsScript, $firstOption)
    {
        $roomArr = [];
        $roomArr[] = HTMLHelper::_('select.option', '', Text::_($firstOption));
        for ($i = 1; $i <= 20; $i++) {
            $roomArr[] = HTMLHelper::_('select.option', $i, $i);
        }
        return HTMLHelper::_('select.genericlist', $roomArr, $name, 'class="' . $class . '" ' . $jsScript, 'value', 'text', $room);
    }

    public static function dropdropFloor($name, $room, $class, $jsScript, $firstOption)
    {
        $roomArr = [];
        $roomArr[] = HTMLHelper::_('select.option', '', Text::_($firstOption));
        for ($i = 1; $i <= 20; $i++) {
            $roomArr[] = HTMLHelper::_('select.option', $i, $i);
        }
        return HTMLHelper::_('select.genericlist', $roomArr, $name, 'class="' . $class . '" ' . $jsScript, 'value', 'text', $room);
    }

    public static function checkboxesCategory($name, $catArr)
    {
		$catArr = (array)$catArr;
        $db = Factory::getDbo();
        $db->setQuery("Select * from #__osrs_categories where published = '1' order by ordering");
        $rows = $db->loadObjectList();
        $tempArr = [];
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                if (in_array($row->id, $catArr)) {
                    $checked = "checked";
                }else{
                    $checked = "";
                }

                $tempArr[] = "<input type='checkbox' name='$name' value='$row->id' $checked> ".self::getLanguageFieldValue($row,'category_name');
            }
        }
        return $tempArr;
    }

    public static function dropdownCategory($name, $catArr, $class)
    {
        $onChangeScript = "";
        $parentArr = self::loadCategoryOptions($catArr, $onChangeScript);
        return HTMLHelper::_('select.genericlist', $parentArr, $name, 'multiple class="' . $class . '" ' . $onChangeScript, 'value', 'text', $catArr);
    }

    //Load Categories Options of Multiple Dropdown Select List: Category
    public static function loadCategoriesOptions($onChangeScript)
    {
        global $mainframe, $lang_suffix;
        $db = Factory::getDBO();
        $app = Factory::getApplication();
        if ($app->isClient('administrator')) {
            $lang_suffix = "";
        } else {
            $lang_suffix = OSPHelper::getFieldSuffix();
        }
        $query = 'SELECT *,id as value,category_name' . $lang_suffix . ' AS text,category_name' . $lang_suffix . ' AS treename,category_name' . $lang_suffix . ' as category_name,parent_id as parent ' .
            ' FROM #__osrs_categories ' .
            ' WHERE published = 1';
        $user = Factory::getUser();
        $query .= ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        $query .= ' ORDER BY parent_id, ordering';
        $db->setQuery($query);
        $mitems = $db->loadObjectList();
        // establish the hierarchy of the menu
        $children = [];
        if ($mitems) {
            // first pass - collect children
            foreach ($mitems as $v) {
                $pt = $v->parent_id;
                if ($v->treename == "") {
                    $v->treename = $v->category_name;
                }
                if ($v->title == "") {
                    $v->title = $v->category_name;
                }
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        // second pass - get an indent list of the items
        $list = HTMLHelper::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        // assemble menu items to the array
        $parentArr = [];
        foreach ($list as $item) {
            //if($item->treename != ""){
            //$item->treename = str_replace("&nbsp;","*",$item->treename);
            //}
            $var = explode("*", $item->treename);

            if (count($var) > 0) {
                $treename = "";
                for ($i = 0; $i < count($var) - 1; $i++) {
                    $treename .= " _ ";
                }
            }
            $text = $item->treename;
            $parentArr[] = HTMLHelper::_('select.option', $item->id, $text);
        }
        return $parentArr;
    }



    public static function getCategoryIdsOfProperty($pid)
    {
		static $propertyId;
		static $categoryIds;
		if((int)$propertyId == 0 || ((int)$propertyId > 0 && (int)$propertyId != $pid))
		{
			$propertyId = $pid;
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('category_id')->from('#__osrs_property_categories')->where('pid="' . $pid . '"');
			$db->setQuery($query);
			$categoryIds = $db->loadColumn(0);
		}
        return $categoryIds;
    }

    public static function getCategoryNamesOfProperty($pid)
    {
        global $lang_suffix, $mainframe;
        $mainframe = Factory::getApplication();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        if ($mainframe->isClient('administrator')) {
            $lang_suffix = "";
        } else {
            $lang_suffix = OSPHelper::getFieldSuffix();
        }
        $user = Factory::getUser();
        $permission = "";
        $permission .= ' 1 = 1 and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        $query = $db->getQuery(true);
        $query->select('category_name' . $lang_suffix)->from('#__osrs_categories')->where($permission . ' and id in (Select category_id from #__osrs_property_categories where pid ="' . $pid . '")');
        $db->setQuery($query);
        $categoryNames = $db->loadColumn(0);
        return implode(", ", $categoryNames);
    }

    public static function getCategoryNamesOfPropertyWithLinks($pid)
    {
        global $lang_suffix, $mainframe;
        $mainframe = Factory::getApplication();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $app = Factory::getApplication();
        if ($app->isClient('administrator')) {
            $lang_suffix = "";
        } else {
            $lang_suffix = OSPHelper::getFieldSuffix();
        }
        $user = Factory::getUser();
        $permission = "";
        $permission .= ' 1 = 1 and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        $query = $db->getQuery(true);
        $query->select('id, category_name' . $lang_suffix . ' as category_name')->from('#__osrs_categories')->where($permission . ' and id in (Select category_id from #__osrs_property_categories where pid ="' . $pid . '")');
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        $categoryArr = [];
        if (count($categories) > 0) {
            $needs = [];
            $needs[] = "category_listing";
            $needs[] = "lcategory";
            $itemid = OSPRoute::getItemid($needs);
            foreach ($categories as $category) {
                $id = $category->id;
                $category_name = $category->category_name;
                $link = Route::_('index.php?option=com_osproperty&task=category_details&id=' . $id . '&Itemid=' . $itemid);
                $categoryArr[] = "<a href='" . $link . "'>" . $category_name . "</a>";
            }
        }
        return implode(", ", $categoryArr);
    }

    public static function array_equal($a, $b)
    {
        return (is_array($a) && is_array($b) && array_diff($a, $b) === array_diff($b, $a));
    }

    public static function showBath($value)
    {
        return rtrim(rtrim($value,'0'),'.');
        //return $value;
    }

    public static function showLotsize($value)
    {
        return rtrim(rtrim($value,'0'),'.');
        //return $value;
    }

    public static function showSquare($value){

        return rtrim(rtrim($value,'0'),'.');
    }

    public static function checkView($taskArr, $menu_id)
    {
        //print_r($taskArr);
        //die();
        //$return = 0;
        //die();
        if ($menu_id > 0) {
            $db = Factory::getDbo();
            $db->setQuery("Select * from #__menu where id = '$menu_id'");
            $menu = $db->loadObject();
            $menu_link = $menu->link;

            if (count($taskArr) > 0) {
                foreach ($taskArr as $task) {
                    if (strpos($menu_link, $task) !== false) {
                        $return = 1;
                    }
                }
            }
        }

        if ($return == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function checkPermissionOfCategories($catArr)
    {
        $returnArr = [];
        $user = Factory::getUser();
        $permission = "";
        $permission .= ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        $db = Factory::getDbo();
        if (count($catArr) > 0) {
            foreach ($catArr as $category_id) {
                if ($category_id > 0) {
                    $db->setQuery("Select count(id) from #__osrs_categories where id = '$category_id' $permission");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $returnArr[] = $category_id;
                    }
                }
            }
        }
        return $returnArr;
    }

    /**
     * Add property to Facebook when it is added/updated
     *
     * @param unknown_type $property
     */
    public static function postPropertyToFacebook($property, $isNew)
    {
        $configClass = self::loadConfig();
        if (($configClass['add_fb'] == 1) and ($configClass['facebook_api'] != "") and ($configClass['application_secret'] != "")) {
            require JPATH_ROOT . '/components/com_osproperty/helpers/fb/facebook.php';
            $facebook = new Facebook(array('appId' => $configClass['facebook_api'], 'secret' => $configClass['application_secret'], 'cookie' => true));

            $url = Route::_("index.php?option=com_osproperty&task=property_details&id=$property->id");
            $url = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')) . $url;

            switch ($isNew) {
                case 1:
                    $message = Text::_('OS_FBLISTING_FB_NEW_TEXT');
                    break;
                default:
                    $message = Text::_('OS_FBLISTING_FB_UPDATE_TEXT');
                    break;
            }
            $message .= '@ ' . $url;

            //find thumb
            $db = Factory::getDbo();
            $db->setQuery("Select * from #__osrs_photos where pro_id = '$property->id'");
            $photos = $db->loadObjectList();
            if (count($photos) > 0) {
                $photo = $photos[0];
                $image = $photo->image;
                if (file_exists(JPATH_ROOT . 'images/osproperty/properties/' . $property->id . '/thumb/' . $image)) {
                    $picture = Uri::root() . 'images/osproperty/properties/' . $property->id . '/thumb/' . $image;
                } else {
                    $picture = Uri::root() . 'media/com_osproperty/assets/images/nopropertyphoto.png';
                }
            } else {
                $picture = Uri::root() . 'media/com_osproperty/assets/images/nopropertyphoto.png';
            }
            $fbpost = array(
                'message' => $message,
                'name' => $property->sef . ", " . self::getLanguageFieldValue($property, 'pro_name'),
                'caption' => Text::_('OS_FBLISTING_LINK_CAPTION'),
                'link' => $url,
                'picture' => $picture
            );

            $result = $facebook->api('/me/feed/', 'post', $fbpost);

            return true;
        }
    }

    public static function addPropertyToQueue($id, $isNew)
    {
		global $configClass;
        $db = Factory::getDbo();
		if($configClass['active_alertemail'] == 1)
		{
			$allowToInsert = 0;
			if($configClass['new_property_alert'] == 1 && $isNew)
			{
				$db->setQuery("Select count(id) from #__osrs_new_properties where pid = '$id'");
				$count = $db->loadResult();
				if($count == 0) 
				{
					$db->setQuery("Insert into #__osrs_new_properties (id,pid,is_new) values (NULL,'$id',1)");
					$db->execute();
				}
			}
			if($configClass['update_property_alert'] == 1 && !$isNew)
			{
				$db->setQuery("Select count(id) from #__osrs_new_properties where pid = '$id'");
				$count = $db->loadResult();
				if($count == 0) 
				{
					$db->setQuery("Insert into #__osrs_new_properties (id,pid,is_new) values (NULL,'$id',0)");
					$db->execute();
				}
			}
		}
    }

	public static function showLocationSideGoogle($address)
	{
		global $bootstrapHelper;
        $language = Factory::getLanguage();
        $activate_language = $language->getTag();
        $activate_language = explode("-",$activate_language);
        $activate_language = $activate_language[0];
        ?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> margintop10">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-bank"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_SCHOOLS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_SCHOOLS');?> </a>		</div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-restaurant"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_RESTAURANTS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_RESTAURANTS');?> </a>		</div>
		</div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-hospital"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_DOCTORS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_DOCTORS');?> </a>		</div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-hospital"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_HOSPITALS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_HOSPITALS');?> </a>		</div>
        </div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-parking"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_RAILWAY');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_RAILWAY');?> </a>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-parking"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_AIRPORTS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_AIRPORTS');?> </a>
            </div>
		</div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-market"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_SUPER_MARKET');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_SUPER_MARKET');?> </a>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-cinema"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_THEATRE');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_THEATRE');?> </a>
            </div>
        </div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-bank"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_UNIVERSITIES');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_UNIVERSITIES');?> </a>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-nursery"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_PARKS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_PARKS');?> </a>
            </div>
		</div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-sport"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_KINDERGARTEN');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_KINDERGARTEN');?> </a>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> marginleft10 paddingleft30 relateditem">
                <i class="icon-detail-maps icon-market"></i> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_SHOPPING_MALL');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_SHOPPING_MALL');?> </a>
            </div>
        </div>
        <?php
	}

    /**
     * This function is used to show the location links above the Google map
     * @param $address
     * @return array|string
     */
    public static function showLocationAboveGoogle($address){
		global $bootstrapHelper;
        $language = Factory::getLanguage();
        $activate_language = $language->getTag();
        $activate_language = explode("-",$activate_language);
        $activate_language = $activate_language[0];
        ?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> margintop10 hidden-phone">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_SCHOOLS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_SCHOOLS');?> </a>		</div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_RESTAURANTS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_RESTAURANTS');?> </a>		</div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_DOCTORS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_DOCTORS');?> </a>		</div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_HOSPITALS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_HOSPITALS');?> </a>		</div>
        </div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> hidden-phone">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_RAILWAY');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_RAILWAY');?> </a>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_AIRPORTS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_AIRPORTS');?> </a>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_SUPER_MARKET');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_SUPER_MARKET');?> </a>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_THEATRE');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_THEATRE');?> </a>
            </div>
        </div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> hidden-phone">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_UNIVERSITIES');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_UNIVERSITIES');?> </a>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_PARKS');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_PARKS');?> </a>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_KINDERGARTEN');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_KINDERGARTEN');?> </a>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> marginleft10">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z"/>
</svg> <a href="https://local.google.com/local?f=l&amp;hl=<?php echo $activate_language;?>&amp;q=category:+<?php echo Text::_('OS_SHOPPING_MALL');?>&amp;om=1&amp;near=<?php echo $address;?>" class="category" rel="nofollow" target="_blank"><?php echo Text::_('OS_SHOPPING_MALL');?> </a>
            </div>
        </div>
        <?php
    }

    public static function isSoldProperty($row,$configClass){
        $sold_property_types = $configClass['sold_property_types'];
        if($sold_property_types != ""){
            $sold_property_types = explode("|",$sold_property_types);
            if((in_array($row->pro_type,$sold_property_types)) and ($row->isSold == 1) and ($configClass['use_sold'] == 1)){
                return true;
            }
        }
        return false;
    }

    /**
     * Check to see if property is in the Compared list
     * @param $pid
     * @return bool
     */
    public static function isInCompareList($pid){
        $session = Factory::getSession();
        $comparelist_ids = $session->get('comparelist');
		if($comparelist_ids != "")
		{
			$comparelist = explode(",",trim($comparelist_ids));
			if(in_array($pid,$comparelist)){
				return true;
			}else{
				return false;
			}
		}
		return false;
    }

    /**
     * Update property to Facebook
     * @param $property
     * @param $isNew
     */
    public static function updateFacebook($property,$isNew){
        $configClass = self::loadConfig();
		if (version_compare(phpversion(), '5.4.0', 'ge')) {
			if(($configClass['facebook_autoposting'] == 1) and ($configClass['fb_app_id'] != "") and ($configClass['fb_app_secret'] != "") and ($configClass['access_token'] != "")){
				$posting_properties = $configClass['posting_properties'];
				if($isNew == 1){
					if(($posting_properties == 0) or ($posting_properties == 1)){
						self::updatePropertyToFacebook($property,$isNew);
					}
				}else{
					if(($posting_properties == 0) or ($posting_properties == 2)){
						self::updatePropertyToFacebook($property,$isNew);
					}
				}
			}
		}
    }

    /**
     * Update Property to Facebook
     * @param $property
     * @param $isNew
     * @return bool|mixed
     */
    public static function updatePropertyToFacebook($property,$isNew){
        require JPATH_ROOT.'/components/com_osproperty/helpers/inc/facebook.php';
        require_once JPATH_ROOT.'/components/com_osproperty/helpers/route.php';
        $configClass = self::loadConfig();
        $needs = [];
        $needs[] = "property_details";
        $needs[] = $property->id;
        $itemid  = OSPRoute::getItemid($needs);
        $url = Uri::root()."index.php?option=com_osproperty&task=property_details&id=$property->id&Itemid=".$itemid;
        $url = self::get_tiny_url($url);
        switch ($isNew){
            case "1":
                $message = Text::_('OS_NEW_PROPERTY_POSTED');
                break;
            default:
                $message = Text::_('OS_PROPERTY_UPDATED');
                break;
        }
        $appid      = $configClass['fb_app_id'];
        $appkey     = $configClass['fb_app_secret'];

        if(!$facebook = new Facebook(array( 'appId' => $appid, 'secret' => $appkey, 'cookie' => true ))) return false;

        $access_token = $configClass['access_token'];
        if($access_token == "") {return false;}
        $facebook->setAccessToken($access_token);

        $message .= '@ '.$url;

        $db = Factory::getDbo();
        $db->setQuery("Select image from #__osrs_photos where pro_id = '$property->id'");
        $image = $db->loadResult();
        $thumb = "";
        if(($image != "") and (file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/medium/'.$image))){
            $thumb = Uri::root().'images/osproperty/properties/'.$property->id.'/medium/'.$image;
        }
		if(OSPHelper::getLanguageFieldValue($property,'price_text') != "")
		{
			$message .= " - ".OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($property,'price_text'));
		}
		elseif(($property->price_call == 0) && ($property->price > 0))
		{
            $message .= " - ".self::showPrice($property->price)." ".self::loadCurrencyCode($property->curr);
            if($property->rent_time != "")
			{
                $message .= " ".Text::_($property->rent_time);
            }
        }
		if((int)$configClass['facebook_version'] == 0){
			$fbpost = array(
				'message' => $message,
				'link' => $url
			);
		}else{
			$fbpost = array(
				'message' => $message,
				'name' => $property->pro_name,
				'caption' => $property->pro_name,
				'link' => $url,
				'picture' => $thumb
			);
		}
        $fb_target = $configClass['fb_target'];
        if($fb_target == ""){
            $post_url = "/me/feed/";
        }else{
            $post_url = "/".$fb_target."/feed/";
        }
        $result = $facebook->api($post_url, 'post', $fbpost);
        return $result;
    }

    /**
     * Update property to Twitter
     * @param $property
     * @param $isNew
     */
    public static function updateTweet($property,$isNew){
        $configClass = self::loadConfig();
        if(($configClass['tweet_autoposting'] == 1) and ($configClass['consumer_key'] != "") and ($configClass['consumer_secret'] != "") and ($configClass['tw_access_token'] != "") and ($configClass['tw_access_token_secret'] != "")){
            $posting_properties = $configClass['tw_posting_properties'];
            if($isNew == 1){
                if(($posting_properties == 0) or ($posting_properties == 1)){
                    self::updatePropertyToTwitter($property,$isNew);
                }
            }else{
                if(($posting_properties == 0) or ($posting_properties == 2)){
                    self::updatePropertyToTwitter($property,$isNew);
                }
            }
        }
    }

    /**
     * Update Property to Twitter
     * @param $property
     * @param $isNew
     * @return bool|mixed
     */
    public static function updatePropertyToTwitter($property,$isNew){
        require JPATH_ROOT.'/components/com_osproperty/helpers/tw/TwitterAPIExchange.php';
        require_once JPATH_ROOT.'/components/com_osproperty/helpers/route.php';
        $configClass = self::loadConfig();
        $needs = [];
        $needs[] = "property_details";
        $needs[] = $property->id;
        $itemid  = OSPRoute::getItemid($needs);
        $url = Uri::root()."index.php?option=com_osproperty&task=property_details&id=$property->id&Itemid=".$itemid;
        $url = self::get_tiny_url($url);
        switch ($isNew){
            case "1":
                $message = Text::_('OS_NEW_PROPERTY_POSTED');
                break;
            default:
                $message = Text::_('OS_PROPERTY_UPDATED');
                break;
        }
        $consumer_key                = $configClass['consumer_key'];
        $consumer_secret             = $configClass['consumer_secret'];
        $tw_access_token             = $configClass['tw_access_token'];
        $tw_access_token_secret      = $configClass['tw_access_token_secret'];

        /* Create a TwitterOauth object with consumer/user tokens. */
        $settings = array(
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret,
            'oauth_access_token' => $tw_access_token,
            'oauth_access_token_secret' => $tw_access_token_secret
        );
        $twitter = new TwitterAPIExchange($settings);

        if(OSPHelper::getLanguageFieldValue($property,'price_text') != ""){
			$message .= " - ".OSPHelper::getLanguageFieldValue($property,'price_text');
		}elseif(($property->price_call == 0) and ($property->price > 0)){
            $message .= " - ".self::showPrice($property->price)." ".self::loadCurrencyCode($property->curr);
            if($property->rent_time != ""){
                $message .= " ".Text::_($property->rent_time);
            }
        }

        $postFields = array(
            'status' => $message.' '.$url
        );
        $rs = json_decode($twitter->buildOauth('https://api.twitter.com/1.1/statuses/update.json', 'POST')->setPostfields($postFields)->performRequest());

        return $rs;
    }

    /**
     * Get tiny url
     * @param $url
     * @return mixed
     */
    public static function get_tiny_url($url)  {
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.urlencode($url));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }


	/**
	 * Create a user account
	 *
	 * @param   array  $data
	 *
	 * @return int Id of created user
	 */
	public static function registration($data)
	{
	
		//Need to load com_users language file
		$lang = Factory::getLanguage();
		$tag  = $lang->getTag();

		if (!$tag)
		{
			$tag = 'en-GB';
		}

		$lang->load('com_users', JPATH_ROOT, $tag);

		$data['password1'] = $data['password'];
		$data['email1']   = $data['email2'] = $data['email'];

		if (OSPHelper::isJoomla4())
		{
			Form::addFormPath(JPATH_ROOT . '/components/com_users/forms');

			/* @var \Joomla\Component\Users\Site\Model\RegistrationModel $model */
			$model = Factory::getApplication()->bootComponent('com_users')
				->getMVCFactory()->createModel('Registration', 'Site', ['ignore_request' => true]);
		}
		else
		{
			// Add path to load xml form definition
			if (Multilanguage::isEnabled())
			{
				Form::addFormPath(JPATH_ROOT . '/components/com_users/models/forms');
				Form::addFieldPath(JPATH_ROOT . '/components/com_users/models/fields');
			}

			JLoader::register('UsersModelRegistration', JPATH_ROOT . '/components/com_users/models/registration.php');

			$model = new UsersModelRegistration;
		}

		$model->register($data);


		$params = ComponentHelper::getParams('com_users');
        // Initialise the table with JUser.

        $useractivation = $params->get('useractivation');
		// Redirect to the login screen.
		if ($useractivation == 2)
		{
			$msg[] = Text::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY');
		}
		elseif ($useractivation == 1)
		{
			$msg[] = Text::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE');
		}
		else
		{
			$msg[] = Text::_('COM_USERS_REGISTRATION_SAVE_SUCCESS');
		}
		$return			= [];
		$tmp			= new stdClass();
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__users')
			->where('username = ' . $db->quote($data['username']));
		$db->setQuery($query);
		$user_id		= $db->loadResult();
		$user			= Factory::getUser($user_id);
		$tmp->user		= $user;
		$tmp->message	= $msg;
		$return[0]		= $tmp;
		return $return;
	}

    /**
     * Register Joomla User
     * @param $data
     */
    public static function registrationOldVersion($data,$usertype = 0)
    {
        $mainframe = Factory::getApplication();
        $msg = [];
        $language = Factory::getLanguage();
        $current_language = $language->getTag();
        $extension = 'com_users';
        $base_dir = JPATH_SITE;
        $language->load($extension, $base_dir, $current_language);
        $params = ComponentHelper::getParams('com_users');
        // Initialise the table with JUser.
        $user = new User;
        $new_usertype = $params->get('new_usertype', '2');
        $groups = [];
        $groups[0] = $new_usertype;
        $data['groups'] = $groups;
        $useractivation = $params->get('useractivation');
        $sendpassword = $params->get('sendpassword', 1);
        // Check if the user needs to activate their account.
        if (($useractivation == 1) || ($useractivation == 2)) 
		{
            jimport('joomla.user.helper');
            if (version_compare(JVERSION, '3.0', 'lt')) 
			{
                $data['activation'] = ApplicationHelper::getHash(UserHelper::genRandomPassword());
            } 
			else 
			{
                $data['activation'] = ApplicationHelper::getHash(UserHelper::genRandomPassword());
            }
            $data['block'] = 1;
        }
        // Bind the data.
        if (!$user->bind($data)) 
		{
			if($usertype	== 0)
			{
				$needs		= [];
				$needs[]	= "agent_register";
				$needs[]	= "aagentregistration";
				$itemid		= OSPRoute::getItemid($needs);
				OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=agent_register&Itemid=' . $itemid), Text::sprintf('OS_COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			}
			else
			{
				$needs		= [];
				$needs[]	= "company_register";
				$needs[]	= "ccompanyregistration";
				$itemid		= OSPRoute::getItemid($needs);
				OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_register&Itemid=' . $itemid), Text::sprintf('OS_COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			}
            return false;
        }
        // Load the users plugin group.
        PluginHelper::importPlugin('user');
        // Store the data.
        if (!$user->save()) 
		{
            if($usertype	== 0)
			{
				$needs		= [];
				$needs[]	= "agent_register";
				$needs[]	= "aagentregistration";
				$itemid		= OSPRoute::getItemid($needs);
				OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=agent_register&Itemid=' . $itemid), Text::sprintf('OS_COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			}
			else
			{
				$needs		= [];
				$needs[]	= "company_register";
				$needs[]	= "ccompanyregistration";
				$itemid		= OSPRoute::getItemid($needs);
				OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=company_register&Itemid=' . $itemid), Text::sprintf('OS_COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			}
            return false;
        }

        $config = Factory::getConfig();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        // Compile the notification mail values.
        $data = $user->getProperties();
        $data['fromname'] = $config->get('fromname');
        $data['mailfrom'] = $config->get('mailfrom');
        $data['sitename'] = $config->get('sitename');
        $data['siteurl'] = Uri::base();

        // Handle account activation/confirmation emails.
        if ($useractivation == 2) 
		{
            // Set the link to confirm the user email.
            $uri = Uri::getInstance();
            $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
            $data['activate'] = $base . Route::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);
			$data['activate'] = str_replace("/administrator","",$data['activate']);

            $emailSubject = Text::sprintf(
                'OS_COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            if ($sendpassword) {
                $emailBody = Text::sprintf(
                    'OS_COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['activate'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            } else {
                $emailBody = Text::sprintf(
                    'OS_COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
                    $data['name'],
                    $data['sitename'],
                    $data['activate'],
                    $data['siteurl'],
                    $data['username']
                );
            }
        } 
		elseif ($useractivation == 1) 
		{
            // Set the link to activate the user account.
            $uri = Uri::getInstance();
            $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
            $data['activate'] = $base . Route::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);
			$data['activate'] = str_replace("/administrator","",$data['activate']);

            $emailSubject = Text::sprintf(
                'OS_COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            if ($sendpassword) {
                $emailBody = Text::sprintf(
                    'OS_COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['activate'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            } else {
                $emailBody = Text::sprintf(
                    'OS_COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
                    $data['name'],
                    $data['sitename'],
                    $data['activate'],
                    $data['siteurl'],
                    $data['username']
                );
            }
        } else {

            $emailSubject = Text::sprintf(
                'OS_COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            if ($sendpassword) {
                $emailBody = Text::sprintf(
                    'OS_COM_USERS_EMAIL_REGISTERED_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            } else {
                $emailBody = Text::sprintf(
                    'OS_COM_USERS_EMAIL_REGISTERED_BODY_NOPW',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl']
                );
            }
        }
		
        // Send the registration email.
		try
		{
			$return = Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}
        // Send Notification mail to administrators
        if (($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1)) {
            $emailSubject = Text::sprintf(
                'OS_COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            $emailBodyAdmin = Text::sprintf(
                'OS_COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
                $data['name'],
                $data['username'],
                $data['siteurl']
            );

            // Get all admin users
            $query->clear()
                ->select($db->quoteName(array('name', 'email', 'sendEmail')))
                ->from($db->quoteName('#__users'))
                ->where($db->quoteName('sendEmail') . ' = ' . 1);

            $db->setQuery($query);

            try {
                $rows = $db->loadObjectList();
            } 
			catch (RuntimeException $e) 
			{
				throw new Exception(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);
                return false;
            }
            // Send mail to all superadministrators id
            foreach ($rows as $row) 
			{
				try
				{
					$return = Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
                // Check for an error.
                if ($return !== true) {
                    $msg[] = Text::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED');
                }
            }
            // Check for an error.
            if ($return !== true) {
                $msg[] = Text::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED');

                // Send a system message to administrators receiving system mails
                $db = Factory::getDbo();
                $query->clear()
                    ->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
                    ->from($db->quoteName('#__users'))
                    ->where($db->quoteName('block') . ' = ' . (int)0)
                    ->where($db->quoteName('sendEmail') . ' = ' . (int)1);
                $db->setQuery($query);

                try {
                    $sendEmail = $db->loadColumn();
                } 
				catch (RuntimeException $e) 
				{

					throw new Exception(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()) , 500);
                    //$this->setError(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);
                    return false;
                }

                if (count($sendEmail) > 0) {
                    $jdate = new Date;
                    // Build the query to add the messages
                    foreach ($sendEmail as $userid) {
                        $values = array($db->quote($userid), $db->quote($userid), $db->quote($jdate->toSql()), $db->quote(Text::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')), $db->quote(Text::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username'])));
                        $query->clear()
                            ->insert($db->quoteName('#__messages'))
                            ->columns($db->quoteName(array('user_id_from', 'user_id_to', 'date_time', 'subject', 'message')))
                            ->values(implode(',', $values));
                        $db->setQuery($query);

                        try {
                            $db->execute();
                        } 
						catch (RuntimeException $e) 
						{
                            throw new Exception(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()) , 500);
                            //$this->setError(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);
                            return false;
                        }
                    }
                }
            }
		}
		// Redirect to the login screen.
		if ($useractivation == 2)
		{
			$msg[] = Text::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY');
		}
		elseif ($useractivation == 1)
		{
			$msg[] = Text::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE');
		}
		else
		{
			$msg[] = Text::_('COM_USERS_REGISTRATION_SAVE_SUCCESS');
		}
		$return = [];
		$tmp = new stdClass();
		$tmp->user = $user;
		$tmp->message = $msg;
		$return[0] = $tmp;
		return $return;
    }


	public static function validateUsername($username)
	{
		$filterInput = InputFilter::getInstance();
		$db          = Factory::getDbo();
		$query       = $db->getQuery(true);
		$errors      = [];

		if (empty($username))
		{
			$errors[] = Text::sprintf('OS_FORM_FIELD_IS_REQURED', Text::_('OS_USERNAME'));
		}

		if ($filterInput->clean($username, 'TRIM') == '')
		{
			$errors[] = Text::_('JLIB_DATABASE_ERROR_PLEASE_ENTER_A_USER_NAME');
		}

		if (preg_match('#[<>"\'%;()&\\\\]|\\.\\./#', $username) || strlen(utf8_decode($username)) < 2
			|| $filterInput->clean($username, 'TRIM') !== $username
		)
		{
			$errors[] = Text::sprintf('JLIB_DATABASE_ERROR_VALID_AZ09', 2);
		}

		$query->select('COUNT(*)')
			->from('#__users')
			->where('username = ' . $db->quote($username));
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($total)
		{
			$errors[] = Text::_('OS_VALIDATION_INVALID_USERNAME');
		}

		return $errors;
	}

	/**
	 * Method to validate password
	 *
	 * @param   string  $password
	 *
	 * @return array
	 */
	public static function validatePassword($password)
	{
		if (self::isJoomla4())
		{
			$prefix = 'JFIELD_PASSWORD_';
		}
		else
		{
			//Load language from user component
			$lang = Factory::getLanguage();
			$tag  = $lang->getTag();

			if (!$tag)
			{
				$tag = 'en-GB';
			}

			$lang->load('com_users', JPATH_ROOT, $tag);

			$prefix = 'COM_USERS_MSG_';
		}

		$errors = [];

		$params           = ComponentHelper::getParams('com_users');
		$minimumIntegers  = $params->get('minimum_integers');
		$minimumSymbols   = $params->get('minimum_symbols');
		$minimumUppercase = $params->get('minimum_uppercase');
		$minimumLowercase = $params->get('minimum_lowercase');
		$minimumLength    = $params->get('minimum_length');


		// We don't allow white space inside passwords
		$valueTrim   = trim($password);
		$valueLength = strlen($password);

		if (strlen($valueTrim) !== $valueLength)
		{
			$errors[] = Text::_($prefix . 'SPACES_IN_PASSWORD');
		}

		if (!empty($minimumIntegers))
		{
			$nInts = preg_match_all('/[0-9]/', $password, $imatch);

			if ($nInts < $minimumIntegers)
			{
				$errors[] = Text::plural($prefix . 'NOT_ENOUGH_INTEGERS_N', $minimumIntegers);
			}
		}

		if (!empty($minimumSymbols))
		{
			$nsymbols = preg_match_all('[\W]', $password, $smatch);

			if ($nsymbols < $minimumSymbols)
			{
				$errors[] = Text::plural($prefix . 'NOT_ENOUGH_SYMBOLS_N', $minimumSymbols);
			}
		}

		if (!empty($minimumUppercase))
		{
			$nUppercase = preg_match_all("/[A-Z]/", $password, $umatch);

			if ($nUppercase < $minimumUppercase)
			{
				$errors[] = Text::plural($prefix . 'NOT_ENOUGH_UPPERCASE_LETTERS_N', $minimumUppercase);
			}
		}

		if (!empty($minimumLowercase))
		{
			$nLowercase = preg_match_all('/[a-z]/', $password, $lmatch);

			if ($nLowercase < $minimumLowercase)
			{
				$errors[] = Text::plural($prefix . 'NOT_ENOUGH_LOWERCASE_LETTERS_N', $minimumLowercase);
			}
		}

		if (!empty($minimumLength))
		{
			if (strlen((string) $password) < $minimumLength)
			{
				$errors[] = Text::plural($prefix . 'TOO_SHORT_N', $minimumLength);
			}
		}
		
		return $errors;
	}

	static function newJoomlaUser($data)
	{
		$language = Factory::getLanguage();
        $current_language = $language->getTag();
        $extension = 'com_users';
        $base_dir = JPATH_SITE;
        $language->load($extension, $base_dir, $current_language);
        $params = ComponentHelper::getParams('com_users');
        // Initialise the table with JUser.
        $user = new User;
        $new_usertype = $params->get('new_usertype', '2');
        $groups = [];
        $groups[0] = $new_usertype;
        $data['groups'] = $groups;
        $useractivation = $params->get('useractivation');
        $sendpassword = $params->get('sendpassword', 1);
        // Check if the user needs to activate their account.
        if (($useractivation == 1) || ($useractivation == 2)) 
		{
            jimport('joomla.user.helper');
            if (version_compare(JVERSION, '3.0', 'lt')) 
			{
                $data['activation'] = ApplicationHelper::getHash(UserHelper::genRandomPassword());
            } 
			else 
			{
                $data['activation'] = ApplicationHelper::getHash(UserHelper::genRandomPassword());
            }
            $data['block'] = 1;
        }
		if (!$user->bind($data)) {
            return false;
        }
        // Load the users plugin group.
        PluginHelper::importPlugin('user');
        // Store the data.
        if (!$user->save()) {
            return false;
        }
        $config = Factory::getConfig();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        // Compile the notification mail values.
        $data = $user->getProperties();
        $data['fromname'] = $config->get('fromname');
        $data['mailfrom'] = $config->get('mailfrom');
        $data['sitename'] = $config->get('sitename');
        $data['siteurl'] = Uri::base();

        // Handle account activation/confirmation emails.
        if ($useractivation == 2) {
            // Set the link to confirm the user email.
            $uri = Uri::getInstance();
            $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
            $data['activate'] = $base . Route::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

            $emailSubject = Text::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            if ($sendpassword) {
                $emailBody = Text::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['activate'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            } else {
                $emailBody = Text::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
                    $data['name'],
                    $data['sitename'],
                    $data['activate'],
                    $data['siteurl'],
                    $data['username']
                );
            }
        } elseif ($useractivation == 1) {
            // Set the link to activate the user account.
            $uri = Uri::getInstance();
            $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
            $data['activate'] = $base . Route::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

            $emailSubject = Text::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            if ($sendpassword) {
                $emailBody = Text::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['activate'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            } else {
                $emailBody = Text::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
                    $data['name'],
                    $data['sitename'],
                    $data['activate'],
                    $data['siteurl'],
                    $data['username']
                );
            }
        } else {

            $emailSubject = Text::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            if ($sendpassword) {
                $emailBody = Text::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            } else {
                $emailBody = Text::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_BODY_NOPW',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl']
                );
            }
        }

        // Send the registration email.
        $return = Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

        // Send Notification mail to administrators
        if (($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1)) {
            $emailSubject = Text::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            $emailBodyAdmin = Text::sprintf(
                'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
                $data['name'],
                $data['username'],
                $data['siteurl']
            );

            // Get all admin users
            $query->clear()
                ->select($db->quoteName(array('name', 'email', 'sendEmail')))
                ->from($db->quoteName('#__users'))
                ->where($db->quoteName('sendEmail') . ' = ' . 1);

            $db->setQuery($query);

            try {
                $rows = $db->loadObjectList();
            } catch (RuntimeException $e) {

				throw new Exception(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()) , 500);
                return false;
            }

            // Send mail to all superadministrators id
            foreach ($rows as $row) 
			{
				try
				{
					$return = Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}

                // Check for an error.
                if ($return !== true) {
                    $msg[] = Text::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED');
                    return false;
                }
            }

            // Check for an error.
            if ($return !== true) {
                $msg[] = Text::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED');

                // Send a system message to administrators receiving system mails
                $db = Factory::getDbo();
                $query->clear()
                    ->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
                    ->from($db->quoteName('#__users'))
                    ->where($db->quoteName('block') . ' = ' . (int)0)
                    ->where($db->quoteName('sendEmail') . ' = ' . (int)1);
                $db->setQuery($query);

                try {
                    $sendEmail = $db->loadColumn();
                } catch (RuntimeException $e) {
					throw new Exception(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()) , 500);
                    //$this->setError(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);
                    return false;
                }

                if (count($sendEmail) > 0) {
                    $jdate = new Date;
                    // Build the query to add the messages
                    foreach ($sendEmail as $userid) {
                        $values = array($db->quote($userid), $db->quote($userid), $db->quote($jdate->toSql()), $db->quote(Text::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')), $db->quote(Text::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username'])));
                        $query->clear()
                            ->insert($db->quoteName('#__messages'))
                            ->columns($db->quoteName(array('user_id_from', 'user_id_to', 'date_time', 'subject', 'message')))
                            ->values(implode(',', $values));
                        $db->setQuery($query);

                        try {
                            $db->execute();
                        } catch (RuntimeException $e) {
                            throw new Exception(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()) , 500);

                            //$this->setError(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);
                            return false;
                        }
                    }
                }
                return false;
            }
		}
		return true;
	}



    /**
     * Login
     */
    public static function login($data){
        $mainframe = Factory::getApplication();
        $params = ComponentHelper::getParams('com_users');
        // Initialise the table with JUser.
        $useractivation     = $params->get('useractivation');
        if($useractivation == 0){
            $data['return'] = base64_decode($data['return_url']);
            // Get the log in options.
            $options = [];
            $options['remember']      = 0;
            $options['return']        = $data['return'];
            // Get the log in credentials.
            $credentials = [];
            $credentials['username']  = $data['username'];
            $credentials['password']  = $data['password'];
            // Perform the log in.
            $error = $mainframe->login($credentials, $options);
        }
    }

    /**
     * Upload picture and resize pictures
     */
    public static function uploadAndResizePicture($file,$type,$oldpicture)
	{
        global $configClass;
        $configClass = self::loadConfig();
        jimport('joomla.filesystem.file');
		$picture_name = $file['name'];
		$picture_name = self::processImageName($picture_name);
        switch($type)
		{
            case "agent":
                    define('OSPATH_UPLOAD_PHOTO',JPATH_ROOT.'/images/osproperty/agent');
                    //$picture_name = "agent".uniqid().".jpg";
                break;
            case "company":
                    define('OSPATH_UPLOAD_PHOTO',JPATH_ROOT.'/images/osproperty/company');
                    //$picture_name = "company".uniqid().".jpg";
                break;
        }
        if (move_uploaded_file($file['tmp_name'],OSPATH_UPLOAD_PHOTO."/".$picture_name))
		{
            // copy image before resize
            File::copy(OSPATH_UPLOAD_PHOTO."/".$picture_name,OSPATH_UPLOAD_PHOTO."/thumbnail/".$picture_name);
            // resize image just copy and replace it self
            $thumb_width = $configClass['images_thumbnail_width'];
            $thumb_height = $configClass['images_thumbnail_height'];
            OSPHelper::resizePhoto(OSPATH_UPLOAD_PHOTO."/thumbnail/".$picture_name,$thumb_width,$thumb_height);
            // remove old image
            if($oldpicture != "") 
			{
                if (is_file(OSPATH_UPLOAD_PHOTO . "/" . $oldpicture)) unlink(OSPATH_UPLOAD_PHOTO . "/" . $oldpicture);
                if (is_file(OSPATH_UPLOAD_PHOTO . "/thumbnail/" . $oldpicture)) unlink(OSPATH_UPLOAD_PHOTO . "/thumbnail/" . $oldpicture);
            }
            // keep file name
            return $picture_name;
        }
    }

    /**
     * Return the correct image name
     *
     * @param unknown_type $image_name
     * @return unknown
     */
    public static function processImageName($image_name)
    {
        $image_name = str_replace(" ", "", $image_name);
        $image_name = str_replace("'", "", $image_name);
        $image_name = str_replace("\n", "", $image_name);
        $image_name = str_replace("\r", "", $image_name);
        $image_name = str_replace("\x00", "", $image_name);
        $image_name = str_replace("\x1a", "", $image_name);
        return $image_name;
    }

    /**
     * Load Currency code
     * @param $currency_code
     */
    public static function loadCurrencyCode($currency_id=''){
        $configClass = self::loadConfig();
        $db = Factory::getDbo();
        if($currency_id == ""){
            $currency_id = $configClass['general_currency_default'];
        }
        $db->setQuery("Select currency_code from #__osrs_currencies where id = '$currency_id'");
        $currency_code = $db->loadResult();
        return $currency_code;
    }

    /**
     * Access Dropdown
     * @param $access
     */
    public static function accessDropdown($name, $selected, $attribs = 'class="form-select input-medium ilarge"', $params = true, $id = false){
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.id AS value, a.title AS text')
            ->from('#__viewlevels AS a')
            ->group('a.id, a.title, a.ordering')
            ->order('a.ordering ASC')
            ->order($db->quoteName('title') . ' ASC');

        // Get the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();

        // If params is an array, push these options to the array
        if (is_array($params))
        {
            $options = array_merge($params, $options);
        }
        // If all levels is allowed, push it into the array.
        elseif ($params)
        {
            array_unshift($options, HTMLHelper::_('select.option', '', Text::_('JOPTION_ACCESS_SHOW_ALL_LEVELS')));
        }

        return HTMLHelper::_(
            'select.genericlist',
            $options,
            $name,
            array(
                'list.attr' => $attribs,
                'list.select' => $selected,
                'id' => $id
            )
        );
    }


    public static function returnAccessLevel($access)
	{
		$accesslevel = '';
		if($access > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('title')
				->from('#__viewlevels')
				->where('id = '.$access);
			// Get the options.
			$db->setQuery($query);
			$accesslevel = $db->loadResult();
		}
        return $accesslevel;
    }

	static function getLimitStart(){
		$request = $_REQUEST;
		if (!isset($request['limitstart']))
		{
			$limitstart = 0;
		}
		if (isset($request['start']) && !isset($request['limitstart']))
		{
			$limitstart = $request['start'];
		}
		if (isset($request['limitstart']))
		{
			$limitstart = $request['limitstart'];
		}
		return $limitstart;
	}

	static function getLimitStartPost(){
		global $mainframe;
		$limitstart	= $_POST['limitstart'];
		if(!isset($limitstart)){
			$limitstart	= $mainframe->getUserStateFromRequest('list.filter.limitstart','limit_start',0);
		}
		$mainframe->setUserState('list.filter.limitstart',$limitstart);
		return $limitstart;
	}

	
	public static function registerNewAgent($user,$user_type)
	{
		global $mainframe,$jinput;
		if($user->id == 0){
			$user = Factory::getUser();
		}
		if($user->id == 0){
			throw new Exception(Text::_( 'OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
		}
		if(HelperOspropertyCommon::isCompanyAdmin($user->id)){
			throw new Exception(Text::_( 'OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
		}
		$db				= Factory::getDbo();
		if(HelperOspropertyCommon::isAgent($user->id)){
			$db->setQuery("Select id from #__osrs_agents where user_id = '$user->id'");
			$agent_id = $db->loadResult();
			return $agent_id;
		}
		$configClass	= self::loadConfig();
		$languages		= OSPHelper::getLanguages();
		$agent = Table::getInstance('Agent','OspropertyTable');
		$post = $jinput->post->getArray();
		$agent->bind($post);
		if($configClass['show_agent_image'] == 1){
			if(is_uploaded_file($_FILES['photo']['tmp_name'])){
				if(!HelperOspropertyCommon::checkIsPhotoFileUploaded('photo')){
					//do nothing
				}else{
                    $agent->photo = OSPHelper::uploadAndResizePicture($_FILES['photo'],"agent","");
				}
			}
		}
		if($user_type == -1){
			$user_type = $configClass['default_user_type'];
		}
		$agent->agent_type = $user_type;
		$agent->user_id = $user->id;
		$agent->name = $user->name;
		$agent->alias = strtolower(str_replace(" ","",$agent->name));
		$agent->email = $user->email;
		$agent->bio	= (string) $agent->bio;
		if($configClass['auto_approval_agent_registration'] == 1){
			$agent->request_to_approval = 0;
			$agent->published = 1;
		}else{
			$agent->request_to_approval = 1;
			$agent->published = 0;
		}
		$db->setQuery("Select ordering from #__osrs_agents order by ordering desc");
		$ordering = $db->loadResult();
		$ordering++;
		$agent->ordering = $ordering;
		if (!$agent->store()) 
		{
			//JError::raiseError(500, $row->getError() );
			throw new Exception($agent->getError(), 500);
		}
		$agent_id = $db->insertid();
		
		
		//update for other languages
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language) {	
				$sef = $language->sef;
				$bio_language = $agent->bio;
				if($bio_language != ""){
					$newagent = Table::getInstance('Agent','OspropertyTable');
					$newagent->id = $agent_id;
					$newagent->{'bio_'.$sef} = $bio_language;
					$newagent->store();
				}
			}
		}
		
		$alias = OSPHelper::getStringRequest('alias','','post');
		$agent_alias = OSPHelper::generateAlias('agent',$agent_id,$alias);
		$db->setQuery("Update #__osrs_agents set alias = '$agent_alias' where id = '$agent_id'");
		$db->execute();

		if(intval($configClass['agent_joomla_group_id']) > 0){
			$user_id = $user->id;
			$db->setQuery("Select count(user_id) from #__user_usergroup_map where user_id = '$user_id' and group_id = '".$configClass['agent_joomla_group_id']."'");
			$count = $db->loadResult();
			if($count == 0){
				$db->setQuery("Insert into #__user_usergroup_map (user_id,group_id) values ('$user_id','".$configClass['agent_joomla_group_id']."')");
				$db->execute();
			}
		}

		return $agent_id;
	}

    /**
     * Load Theme Style
     */
    public static function loadThemeStyle($task){
        global $jinput;
        $document = Factory::getDocument();
        $db = Factory::getDbo();
        if($task != ""){
            $taskArr = explode("_",$task);
            $maintask = $taskArr[0];
        }else{
            //cpanel
            $maintask = "";
        }
        $itemid = $jinput->getInt('Itemid',0);
        if($task != "property_new" && $task != "property_edit" && $maintask != "ajax"){

            $theme_id = 0;
            if($itemid > 0){
                $menus = Factory::getApplication()->getMenu();
                $menu = $menus->getActive();
                if (is_object($menu)) {
                    if ($itemid == $menu->id) {
                        $menuParams = new Registry() ;
                        $menuParams->loadString($menu->getParams()) ;
                        $theme_id = $menuParams->get('theme_id','0');
                    }
                }
            }

			$themename		   = self::getThemeName();
            $default_themename = ($themename != "") ? $themename : "default";

            if($theme_id > 0)
			{
                $db->setQuery("Select * from #__osrs_themes where id = '$theme_id'");
                $theme = $db->loadObject();
                $themename = ($theme->name != "") ? $theme->name : $default_themename;
            }
			else
			{
                $themename = $default_themename;
            }

            if(file_exists(JPATH_ROOT."/components/com_osproperty/templates/" . $themename . "/style/style.css")) {
                $document->addStyleSheet(Uri::root() . "components/com_osproperty/templates/" . $themename . "/style/style.css");
            }
            if(file_exists(JPATH_ROOT.'/media/com_osproperty/style/custom.css')){
                if(filesize(JPATH_ROOT.'/media/com_osproperty/style/custom.css') > 0){
                    $document->addStyleSheet(Uri::root()."media/com_osproperty/style/custom.css");
                }
            }
        }
    }

    /**
     * Get Using Theme
     */
    public static function getThemeName()
	{
		static $default_theme, $configClass;
        $jinput		= Factory::getApplication()->input;
        $db			= Factory::getDbo();
        $itemid		= $jinput->getInt('Itemid',0);
        $theme_id	= 0;
		$configClass = self::loadConfig();
		//echo $configClass['property_details_theme'];
		$task		= $jinput->getString('task','');
		if($task == "property_details" && $configClass['property_details_theme'] != "")
		{
			return $configClass['property_details_theme'];
		}
        if($itemid > 0)
		{
            $menus = Factory::getApplication()->getMenu();
            $menu = $menus->getActive();
            if (is_object($menu)) {
                if ($itemid == $menu->id) {
                    $menuParams = new Registry() ;
                    $menuParams->loadString($menu->getParams()) ;
                    $theme_id = $menuParams->get('theme_id','0');
                }
            }
        }
		if($default_theme == "")
		{
			$db->setQuery("Select * from #__osrs_themes where published = '1'");
			$default_theme = $db->loadObject();
		}
		

        $default_themename = ($default_theme->name != "") ? $default_theme->name : "default";

        if($theme_id > 0){
            $db->setQuery("Select * from #__osrs_themes where id = '$theme_id'");
            $theme = $db->loadObject();
            $themename = ($theme->name != "") ? $theme->name : $default_themename;
        }else{
            $themename = $default_themename;
        }

        return $themename;
    }

    /**
     * Show base fields in Property details page
     * @param $row
     */
	public static function showBaseFields($row){
		global $bootstrapHelper;
        $configClass = self::loadConfig();
        if(($configClass['use_rooms'] == 1 && $row->rooms > 0) || ($configClass['use_bedrooms'] == 1 && $row->bed_room > 0) || ($configClass['use_bathrooms'] == 1 && $row->bath_room > 0) || ((float)$row->square_feet > 0)){
            ob_start();
            ?>
            <div class="clearfix"></div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="baseFieldHeading">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <h4>
                        <?php echo Text::_('OS_BASE_INFORMATION');?>
                    </h4>
                </div>
            </div>
            <?php
            if(($configClass['use_rooms'] == 1) and ($row->rooms > 0)){ ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_ROOMS').": ".$row->rooms;?>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if(($configClass['use_bedrooms'] == 1) and ($row->bed_room > 0)){?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_BED').": ".$row->bed_room;?>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if(($configClass['use_bathrooms'] == 1) and ($row->bath_room > 0)){ ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_BATH').": ".self::showBath($row->bath_room);?>
                    </div>
                </div>
            <?php
            }
			if($configClass['more_bath_infor']== 1 && $configClass['use_bathrooms']== 1 && self::isBathroomInformation($row->id))
			{
			?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_BATHROOM_INFORMATION')?>:
						 <?php
						 self::showBathroomInformation($row->id);
						 ?>
                    </div>
                </div>
            <?php
			}
            ?>
            <?php
            if((float)$row->square_feet > 0){ ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo OSPHelper::showSquareLabels().": ".$row->square_feet;?>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            $body = ob_get_contents();
            ob_end_clean();
        }
        return $body;
	}

    /**
     * @param $row
     * @return string
     */
    public static function showGarage($row){
		global $bootstrapHelper;
        $configClass = self::loadConfig();
        if(($configClass['use_parking'] == 1) and (($row->parking > 0) or ($row->garage_description != ""))){
            ob_start();
            ?>
            <div class="clearfix"></div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="parkingHeading">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <h4>
                        <?php echo Text::_('OS_PARKING_INFORMATION');?>
                    </h4>
                </div>
            </div>
            <?php
            if($row->parking > 0){ ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_PARKING').": ".$row->parking;?>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if($row->garage_description != ""){?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_GARAGE_DESCRIPTION').": ".$row->garage_description;?>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            $body = ob_get_contents();
            ob_end_clean();
        }
        return $body;
    }

    /**
     * @param $row
     * @return string
     */
    public static function buildingInfo($row){
		global $bootstrapHelper;
		$body = "";
        $configClass = self::loadConfig();
        if($configClass['use_nfloors'] == 1){
            $textFieldsArr = array('house_style','house_construction','exterior_finish','roof','flooring');
            $numberFieldArr = array('floor_area_lower','floor_area_main_level','floor_area_upper','floor_area_total');
			$intFieldArr = array('number_of_floors','built_on','remodeled_on');
            $show = 0;
            foreach($textFieldsArr as $textfield){
                if($row->{$textfield} != ""){
                    $show = 1;
                }
            }
            foreach($numberFieldArr as $numfield){
                if($row->{$numfield}  > 0){
                    $show = 1;
                }
            }
			foreach($intFieldArr as $numfield){
                if($row->{$numfield}  > 0){
                    $show = 1;
                }
            }
            if($show == 1) {
                ob_start();
                ?>
                <div class="clearfix"></div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="buildingInformationHeading">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <h4>
                            <?php echo Text::_('OS_BUILDING_INFORMATION');?>
                        </h4>
                    </div>
                </div>
                <?php
                foreach($textFieldsArr as $textfield){
                    if($row->{$textfield} != ""){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_'.strtoupper($textfield)) . ": " . $row->{$textfield};?>
                            </div>
                        </div>
                    <?php
                    }
                }
				foreach($intFieldArr as $numfield){
                    if($row->{$numfield}  > 0){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_'.strtoupper($numfield)) . ": " . $row->{$numfield};?>
                            </div>
                        </div>
                        <?php
                    }
                }
                foreach($numberFieldArr as $numfield){
                    if($row->{$numfield}  > 0){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_'.strtoupper($numfield)) . ": " . self::showBath($row->{$numfield});?>
                            </div>
							<?php
							if($numfield != "number_of_floors")
							{
								echo " ".self::showSquareSymbol();
							}
							?>
                        </div>
                        <?php
                    }
                }
				$body = ob_get_contents();
				ob_end_clean();
            }
        }
        return $body;
    }


    /**
     * @param $row
     * @return string
     */
    public static function basementFoundation($row){
		global $bootstrapHelper;
		$body = "";
        $configClass = self::loadConfig();
        if(($configClass['basement_foundation'] == 1) and (($row->basement_size > 0) or ($row->basement_foundation != "") or ($row->percent_finished != ""))){
            ob_start();
            ?>
            <div class="clearfix"></div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="basementFoundationHeading">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <h4>
                        <?php echo Text::_('OS_BASEMENT_FOUNDATION');?>
                    </h4>
                </div>
            </div>
            <?php
            if($row->basement_foundation != ""){ ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_BASEMENT_FOUNDATION').": ".$row->basement_foundation;?>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if($row->basement_size > 0){?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_BASEMENT_SIZE').": ".self::showBath($row->basement_size);?>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if($row->percent_finished != ""){ ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_PERCENT_FINISH').": ".$row->percent_finished;?>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            $body = ob_get_contents();
            ob_end_clean();
        }
        return $body;
    }

    /**
     * @param $row
     * @return string
     */
    public static function landInformation($row){
		global $bootstrapHelper;
        $configClass = self::loadConfig();
		$body = "";
        if($configClass['use_squarefeet'] == 1){
            $textFieldsArr = array('subdivision','land_holding_type','lot_dimensions','frontpage','depth');
            $numberFieldArr = array('total_acres','lot_size','living_areas');
            $show = 0;
            foreach($textFieldsArr as $textfield){
                if($row->{$textfield} != ""){
                    $show = 1;
                }
            }
            foreach($numberFieldArr as $numfield){
                if($row->{$numfield}  > 0){
                    $show = 1;
                }
            }
            if($show == 1) {
                ob_start();
                ?>
                <div class="clearfix"></div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="landgInformationHeading">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <h4>
                            <?php echo Text::_('OS_LAND_INFORMATION');?>
                        </h4>
                    </div>
                </div>
                <?php
                foreach($textFieldsArr as $textfield){
                    if($row->{$textfield} != ""){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_'.strtoupper($textfield)) . ": " . $row->{$textfield};?>

                            </div>
                        </div>
                    <?php
                    }
                }
                foreach($numberFieldArr as $numfield){
                    if($row->{$numfield}  > 0){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_'.strtoupper($numfield)) . ": " . self::showBath($row->{$numfield});?>
                                <?php
                                switch($numfield){
                                    case "square_feet":
                                    case "lot_size":
                                        echo " ".self::showSquareSymbol();
                                        break;
                                    default:
                                        echo " ".self::showAcresSymbol();
                                        break;
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    }
                }
				$body = ob_get_contents();
				ob_end_clean();
            }
        }
        return $body;
    }

    /**
     * @param $row
     * @return string
     */
    public static function businessInformation($row){
		global $bootstrapHelper;
		$body = "";
        $configClass = self::loadConfig();
        if($configClass['use_business'] == 1){
            $textFieldsArr = array('takings','returns','net_profit','business_type','stock','fixtures','fittings','percent_office','percent_warehouse','loading_facilities');
            $show = 0;
            foreach($textFieldsArr as $textfield){
                if($row->{$textfield} != ""){
                    $show = 1;
                }
            }

            if($show == 1) {
                ob_start();
                ?>
                <div class="clearfix"></div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="businessInformationHeading">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <h4>
                            <?php echo Text::_('OS_BUSINESS_INFORMATION');?>
                        </h4>
                    </div>
                </div>
                <?php
                foreach($textFieldsArr as $textfield){
                    if($row->{$textfield} != ""){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_'.strtoupper($textfield)) . ": " . $row->{$textfield};?>

                            </div>
                        </div>
                    <?php
                    }
                }
				$body = ob_get_contents();
				ob_end_clean();
            }
        }
        return $body;
    }

    /**
     * @param $row
     * @return string
     */
    public static function ruralInformation($row){
		global $bootstrapHelper;
        $configClass = self::loadConfig();
		$body = "";
        if($configClass['use_rural'] == 1){
            $textFieldsArr = array('fencing','rainfall','soil_type','grazing','cropping','irrigation','water_resources','carrying_capacity','storage');
            $show = 0;
            foreach($textFieldsArr as $textfield){
                if($row->{$textfield} != ""){
                    $show = 1;
                }
            }

            if($show == 1) {
                ob_start();
                ?>
                <div class="clearfix"></div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="ruralInformationHeading">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <h4>
                            <?php echo Text::_('OS_RURAL_INFORMATION');?>
                        </h4>
                    </div>
                </div>
                <?php
                foreach($textFieldsArr as $textfield){
                    if($row->{$textfield} != ""){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg> <?php echo Text::_('OS_'.strtoupper($textfield)) . ": " . $row->{$textfield};?>

                            </div>
                        </div>
                    <?php
                    }
                }
				$body = ob_get_contents();
				ob_end_clean();
            }
        }
        return $body;
    }

    public static function showCoreFields($property){
		global $bootstrapHelper;
        $tmpArray                   = [];
        $tmpArray[]			        = OSPHelper::showBaseFields($property);
        $tmpArray[]				    = OSPHelper::showGarage($property);
        $tmpArray[]                 = OSPHelper::buildingInfo($property);
        $tmpArray[]				    = OSPHelper::basementFoundation($property);
        $tmpArray[]		            = OSPHelper::landInformation($property);
        $tmpArray[]                 = OSPHelper::businessInformation($property);
        $tmpArray[]	                = OSPHelper::ruralInformation($property);
        ob_start();
        ?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <?php
            $i = 0;
            foreach($tmpArray as $tmp){
                if($tmp != ""){
                    $i++;
                    ?>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>">
                        <?php echo $tmp; ?>
                    </div>
                    <?php
                    if($i == 3){
                        $i = 0;
                        echo "</div><div class='amenitygroup ".$bootstrapHelper->getClassMapping('row-fluid')."'></div><div class='".$bootstrapHelper->getClassMapping('row-fluid')."'>";
                    }
                }
            }
            ?>
        </div>
        <?php
        $body = ob_get_contents();
        ob_end_clean();
        return $body;
    }


	/**
     * Show base fields in Property details page
     * @param $row
     */
	public static function showBaseFields1($row){
		global $bootstrapHelper;
        $configClass = self::loadConfig();
        if((($configClass['use_rooms'] == 1) && ($row->rooms > 0)) || (($configClass['use_bedrooms'] == 1) && ($row->bed_room > 0)) && (($configClass['use_bathrooms'] == 1) && ($row->bath_room > 0)) || ((float)$row->square_feet > 0))
        {
            ob_start();
            ?>
            <div class="clearfix"></div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="baseFieldHeading">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <h4>
                        <?php echo Text::_('OS_BASE_INFORMATION');?>
                    </h4>
                </div>
            </div>
            <?php
            if(($configClass['use_rooms'] == 1) and ($row->rooms > 0))
            {
                ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> ">
						<div class="fieldlabel">
							<?php echo Text::_('OS_ROOMS')?>
						</div>
						<div class="fieldvalue">
							<?php echo $row->rooms;?>
						</div>
                    </div>
                </div>
            <?php
            }
            if(($configClass['use_bedrooms'] == 1) and ($row->bed_room > 0))
            {
                ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="fieldlabel">
							<?php echo Text::_('OS_BED')?>
						</div>
						<div class="fieldvalue">
							<?php echo $row->bed_room;?>
						</div>
                    </div>
                </div>
            <?php
            }
            if(($configClass['use_bathrooms'] == 1) and ($row->bath_room > 0))
            {
            ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="fieldlabel">
							<?php echo Text::_('OS_BATH')?>
						</div>
						<div class="fieldvalue">
							<?php echo self::showBath($row->bath_room);?>
						</div>
                    </div>
                </div>
            <?php
            }
			if($configClass['more_bath_infor']== 1 && $configClass['use_bathrooms']== 1 && self::isBathroomInformation($row->id))
			{
			?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="fieldlabel">
							<?php echo Text::_('OS_BATHROOM_INFORMATION')?>
						</div>
						<div class="fieldvalue">
							<?php
							self::showBathroomInformation($row->id);
							?>
						</div>
                    </div>
                </div>
            <?php
			}
			if((float)$row->square_feet > 0)
            {
            ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="fieldlabel">
							<?php echo OSPHelper::showSquareLabels()?>
						</div>
						<div class="fieldvalue">
							<?php echo $row->square_feet;?> <?php echo self::showSquareSymbol();?>
						</div>
					</div>
                </div>
            <?php
            }
            ?>
            <?php
            $body = ob_get_contents();
            ob_end_clean();
        }
        return $body;
	}

    /**
     * @param $row
     * @return string
     */
    public static function showGarage1($row){
		global $bootstrapHelper;
        $configClass = self::loadConfig();
        if(($configClass['use_parking'] == 1) and (($row->parking > 0) or ($row->garage_description != ""))){
            ob_start();
            ?>
            <div class="clearfix"></div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="parkingHeading">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <h4>
                        <?php echo Text::_('OS_PARKING_INFORMATION');?>
                    </h4>
                </div>
            </div>
            <?php
            if($row->parking > 0){ ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="fieldlabel">
							<?php echo Text::_('OS_PARKING')?>
						</div>
						<div class="fieldvalue">
							<?php echo $row->parking;?>
						</div>
					</div>
                </div>
            <?php
            }
            ?>
            <?php
            if($row->garage_description != ""){?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="fieldlabel">
							<?php echo Text::_('OS_GARAGE_DESCRIPTION')?>
						</div>
						<div class="fieldvalue">
							<?php echo $row->garage_description;?>
						</div>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            $body = ob_get_contents();
            ob_end_clean();
        }
        return $body;
    }

    /**
     * @param $row
     * @return string
     */
    public static function buildingInfo1($row){
		global $bootstrapHelper;
		$body = "";
        $configClass = self::loadConfig();
        if($configClass['use_nfloors'] == 1){
            $textFieldsArr = array('house_style','house_construction','exterior_finish','roof','flooring');
            $numberFieldArr = array('floor_area_lower','floor_area_main_level','floor_area_upper','floor_area_total');
			$intFieldArr = array('number_of_floors','built_on','remodeled_on');
            $show = 0;
            foreach($textFieldsArr as $textfield){
                if($row->{$textfield} != ""){
                    $show = 1;
                }
            }
            foreach($numberFieldArr as $numfield){
                if($row->{$numfield}  > 0){
                    $show = 1;
                }
            }
			foreach($intFieldArr as $numfield){
                if($row->{$numfield}  > 0){
                    $show = 1;
                }
            }
            if($show == 1) {
                ob_start();
                ?>
                <div class="clearfix"></div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="buildingInfoHeading">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <h4>
                            <?php echo Text::_('OS_BUILDING_INFORMATION');?>
                        </h4>
                    </div>
                </div>
                <?php
                foreach($textFieldsArr as $textfield){
                    if($row->{$textfield} != ""){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
								<div class="fieldlabel">
									<?php echo Text::_('OS_'.strtoupper($textfield)); ?>
								</div>
								<div class="fieldvalue">
									<?php echo $row->{$textfield};?>
								</div>
                            </div>
                        </div>
                    <?php
                    }
                }
				foreach($intFieldArr as $numfield){
                    if($row->{$numfield}  > 0){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
								<div class="fieldlabel">
									<?php echo Text::_('OS_'.strtoupper($numfield)); ?>
								</div>
								<div class="fieldvalue">
									<?php echo $row->{$numfield};?>
								</div>
                            </div>
                        </div>
                        <?php
                    }
                }
                foreach($numberFieldArr as $numfield){
                    if($row->{$numfield}  > 0){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
								<div class="fieldlabel">
									<?php echo Text::_('OS_'.strtoupper($numfield)); ?>
								</div>
								<div class="fieldvalue">
									<?php 
									echo self::showBath($row->{$numfield});
									if($numfield != "number_of_floors")
									{
										echo " ".self::showSquareSymbol();
									}
									?>
								</div>
                            </div>
                        </div>
                        <?php
                    }
                }
				$body = ob_get_contents();
				ob_end_clean();
            }
        }
        return $body;
    }


    /**
     * @param $row
     * @return string
     */
    public static function basementFoundation1($row){
		global $bootstrapHelper;
		$body = "";
        $configClass = self::loadConfig();
        if(($configClass['basement_foundation'] == 1) and (($row->basement_size > 0) or ($row->basement_foundation != "") or ($row->percent_finished != ""))){
            ob_start();
            ?>
            <div class="clearfix"></div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="basementFoundationHeading">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <h4>
                        <?php echo Text::_('OS_BASEMENT_FOUNDATION');?>
                    </h4>
                </div>
            </div>
            <?php
            if($row->basement_foundation != ""){ ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="fieldlabel">
							<?php echo Text::_('OS_BASEMENT_FOUNDATION'); ?>
						</div>
						<div class="fieldvalue">
							<?php echo $row->basement_foundation;?>
						</div>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if($row->basement_size > 0){?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="fieldlabel">
							<?php echo Text::_('OS_BASEMENT_SIZE'); ?>
						</div>
						<div class="fieldvalue">
							<?php echo self::showBath($row->basement_size);?>
						</div>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if($row->percent_finished != ""){ ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="fieldlabel">
							<?php echo Text::_('OS_PERCENT_FINISH'); ?>
						</div>
						<div class="fieldvalue">
							<?php echo $row->percent_finished;?>
						</div>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            $body = ob_get_contents();
            ob_end_clean();
        }
        return $body;
    }

    /**
     * @param $row
     * @return string
     */
    public static function landInformation1($row){
		global $bootstrapHelper;
        $configClass = self::loadConfig();
		$body = "";
        if($configClass['use_squarefeet'] == 1){
            $textFieldsArr = array('subdivision','land_holding_type','lot_dimensions','frontpage','depth');
            $numberFieldArr = array('total_acres','square_feet','lot_size','living_areas');
            $show = 0;
            foreach($textFieldsArr as $textfield){
                if($row->{$textfield} != ""){
                    $show = 1;
                }
            }
            foreach($numberFieldArr as $numfield){
                if($row->{$numfield}  > 0){
                    $show = 1;
                }
            }
            if($show == 1) {
                ob_start();
                ?>
                <div class="clearfix"></div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="landInformationHeading">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <h4>
                            <?php echo Text::_('OS_LAND_INFORMATION');?>
                        </h4>
                    </div>
                </div>
                <?php
                foreach($textFieldsArr as $textfield){
                    if($row->{$textfield} != ""){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <div class="fieldlabel">
									<?php echo Text::_('OS_'.strtoupper($textfield)); ?>
								</div>
								<div class="fieldvalue">
									<?php echo $row->{$textfield};?>
								</div>
                            </div>
                        </div>
                    <?php
                    }
                }
                foreach($numberFieldArr as $numfield){
                    if($row->{$numfield}  > 0){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
								<div class="fieldlabel">
									<?php echo Text::_('OS_'.strtoupper($numfield)); ?>
								</div>
								<div class="fieldvalue">
									<?php echo self::showBath($row->{$numfield});?>
									<?php
									switch($numfield)
									{
										case "square_feet":
										case "lot_size":
											echo " ".self::showSquareSymbol();
											break;
										default:
											echo " ".self::showAcresSymbol();
											break;
									}
                                ?>
								</div>
                            </div>
                        </div>
                    <?php
                    }
                }
				$body = ob_get_contents();
				ob_end_clean();
            }
        }
        return $body;
    }

    /**
     * @param $row
     * @return string
     */
    public static function businessInformation1($row){
		global $bootstrapHelper;
		$body = "";
        $configClass = self::loadConfig();
        if($configClass['use_business'] == 1){
            $textFieldsArr = array('takings','returns','net_profit','business_type','stock','fixtures','fittings','percent_office','percent_warehouse','loading_facilities');
            $show = 0;
            foreach($textFieldsArr as $textfield){
                if($row->{$textfield} != ""){
                    $show = 1;
                }
            }

            if($show == 1) {
                ob_start();
                ?>
                <div class="clearfix"></div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="businessInformationHeading">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <h4>
                            <?php echo Text::_('OS_BUSINESS_INFORMATION');?>
                        </h4>
                    </div>
                </div>
                <?php
                foreach($textFieldsArr as $textfield){
                    if($row->{$textfield} != ""){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <div class="fieldlabel">
									<?php echo Text::_('OS_'.strtoupper($textfield)); ?>
								</div>
								<div class="fieldvalue">
									<?php echo $row->{$textfield};?>
								</div>
                            </div>
                        </div>
                    <?php
                    }
                }
				$body = ob_get_contents();
				ob_end_clean();
            }
        }
        return $body;
    }

    /**
     * @param $row
     * @return string
     */
    public static function ruralInformation1($row){
		global $bootstrapHelper;
        $configClass = self::loadConfig();
		$body = "";
        if($configClass['use_rural'] == 1){
            $textFieldsArr = array('fencing','rainfall','soil_type','grazing','cropping','irrigation','water_resources','carrying_capacity','storage');
            $show = 0;
            foreach($textFieldsArr as $textfield){
                if($row->{$textfield} != ""){
                    $show = 1;
                }
            }

            if($show == 1) {
                ob_start();
                ?>
                <div class="clearfix"></div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="ruralInformationHeading">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <h4>
                            <?php echo Text::_('OS_RURAL_INFORMATION');?>
                        </h4>
                    </div>
                </div>
                <?php
                foreach($textFieldsArr as $textfield){
                    if($row->{$textfield} != ""){
                        ?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <div class="fieldlabel">
									<?php echo Text::_('OS_'.strtoupper($textfield)); ?>
								</div>
								<div class="fieldvalue">
									<?php echo $row->{$textfield};?>
								</div>
                            </div>
                        </div>
                    <?php
                    }
                }
				$body = ob_get_contents();
				ob_end_clean();
            }
        }
        return $body;
    }

	public static function showCoreFields1($property){
		global $bootstrapHelper;
        $tmpArray                   = [];
        $tmpArray[]			        = OSPHelper::showBaseFields1($property);
        $tmpArray[]				    = OSPHelper::showGarage1($property);
        $tmpArray[]                 = OSPHelper::buildingInfo1($property);
        $tmpArray[]				    = OSPHelper::basementFoundation1($property);
        $tmpArray[]		            = OSPHelper::landInformation1($property);
        $tmpArray[]                 = OSPHelper::businessInformation1($property);
        $tmpArray[]	                = OSPHelper::ruralInformation1($property);
        ob_start();
        ?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <?php
            $i = 0;
            foreach($tmpArray as $tmp){
                if($tmp != ""){
                    $i++;
                    ?>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
                        <?php echo $tmp; ?>
                    </div>
                    <?php
                    if($i == 2){
                        $i = 0;
                        echo "</div><div class='".$bootstrapHelper->getClassMapping('row-fluid')."'></div><div class='".$bootstrapHelper->getClassMapping('row-fluid')."'>";
                    }
                }
            }
            ?>
        </div>
        <?php
        $body = ob_get_contents();
        ob_end_clean();
        return $body;
    }

	public static function checkFieldWithPropertType($fid,$pid){
		$db = Factory::getDbo();
		$db->setQuery("Select pro_type from #__osrs_properties where id = '$pid'");
		$pro_type = $db->loadResult();
	
		$db->setQuery("Select count(id) from #__osrs_extra_field_types where fid = '$fid' and type_id = '$pro_type'");
		$count = $db->loadResult();
		if($count == 0){
			return false;
		}else{
			return true;
		}
	}

    /**
     * This function is used to retrieve type icon of specific property type
     * @param $type_id
     * @return mixed|string
     */
	public static function getTypeIcon($type_id)
    {
        $db = Factory::getDbo();
        $db->setQuery("Select type_icon from #__osrs_types where id = '$type_id'");
        $type_icon = $db->loadResult();
        if($type_icon == ""){
            $type_icon = "1.png";
        }
        return $type_icon;
    }

	public static function getCountryName($country_id){
		static $country_name;
		$db = Factory::getDbo();
		if(!HelperOspropertyCommon::checkCountry()){
			$default_country_id = HelperOspropertyCommon::getDefaultCountry();
			if($country_id == $default_country_id){
				if($country_name == null){
					$country_name_value = self::loadCountryName($country_id);
					$country_name = $country_name_value;
				}else{
					$country_name_value = $country_name;
				}
			}else{
				$country_name_value = self::loadCountryName($country_id);
			}
		}else{
			$country_name_value = self::loadCountryName($country_id);
		}
		return $country_name_value;
	}

	public static function showRatingOverPicture($rate,$color)
	{
		$rate = (float) $rate;
		$rate = round($rate);
		for($i=0;$i<$rate;$i++){
			?>
			<i class="osicon-star" style="color:<?php echo $color;?>;"></i>
			<?php
		}
		$i = $rate;
		if($i < 5){
			for($j=$i;$j<5;$j++){
				?>
				<i class="osicon-star stars"></i>
				<?php
			}
		}
	}

	//get Association article for Term and Condition
	public static function getAssocArticleId($articleId)
	{
		JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

		if (Multilanguage::isEnabled())
		{
			$associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
			$langCode     = Factory::getLanguage()->getTag();

			if (isset($associations[$langCode]))
			{
				$article = $associations[$langCode];
			}
		}

		if (!isset($article))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, catid')
				->from('#__content')
				->where('id = ' . (int) $articleId);
			$db->setQuery($query);
			$article = $db->loadObject();
		}

		if (!$article)
		{
			return '';
		}

		return ContentHelperRoute::getArticleRoute($article->id, $article->catid) . '&tmpl=component&format=html';
	}

	static function in_array_field($needle, $needle_field, $haystack, $strict = false) {
		if ($strict) {
			foreach ($haystack as $item)
				if (isset($item->$needle_field) && $item->$needle_field === $needle)
					return true;
		}
		else {
			foreach ($haystack as $item)
				if (isset($item->$needle_field) && $item->$needle_field == $needle)
					return true;
		}
		return false;
	}

	static function in_array_sub($needle, $haystack){
		$haystack = (array) $haystack;
		foreach($haystack as $item){
			$value = (array)$item->value;
			if(in_array($needle,$value)){
				return true;
			}
		}
		return false;
	}

	static function find_key($needle, $haystack){
		$find = 0;
		$key  = '';
		foreach($haystack as $dup){
			if($dup->id == $needle){
				$key = $find;
			}
			$find++;
		}
		return $key;
	}

	static function findGoogleDuplication($rows){
		//process data
		$tempArr = [];
		$i = 0;
		foreach($rows as $row){
			if($row->show_address == 1 && $row->lat_add != "" && $row->long_add != "")
			{
				$tempArr[$i] = new stdClass();
				$tempArr[$i]->id = $row->id;
				$tempArr[$i]->lat_add = $row->lat_add;
				$tempArr[$i]->long_add = $row->long_add;
				$i++;
			}
		}

		$duplicate = [];
		for($i=0;$i<count($tempArr)-1;$i++){
			for($j=1;$j<count($tempArr);$j++){
				if(($tempArr[$i]->id != $tempArr[$j]->id) and ($tempArr[$i]->lat_add == $tempArr[$j]->lat_add) and ($tempArr[$i]->long_add == $tempArr[$j]->long_add))
				{
					$count = count($duplicate);
					if((! self::in_array_field($tempArr[$i]->id,'id',$duplicate)) and (! self::in_array_sub($tempArr[$i]->id, $duplicate)))
					{
						$duplicate[$count] = new stdClass();
						$duplicate[$count]->id = $tempArr[$i]->id;
						$duplicate[$count]->value[0] = $tempArr[$j]->id;
					}
					elseif(self::in_array_field($tempArr[$i]->id,'id',$duplicate))
					{
						$key = self::find_key($tempArr[$i]->id,$duplicate);
						$duplicate[$key]->value[count($duplicate[$key]->value)] = $tempArr[$j]->id;
					}
				}
			}
		}
		for($i=0;$i<count($tempArr);$i++){
			$count = count($duplicate);
			if((! self::in_array_field($tempArr[$i]->id,'id',$duplicate)) and (! self::in_array_sub($tempArr[$i]->id, $duplicate)))
			{
				$duplicate[$count] = new stdClass();
				$duplicate[$count]->id = $tempArr[$i]->id;
			}
		}

		return $duplicate;
	}


    /**
     * Get bootstrapped style boolean input
     *
     * @param $name
     * @param $value
     *
     * @return string
     */
    public static function getBooleanInput($name, $value , $option1 = "", $option2 = "")
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
        $element->addAttribute('name', $name);

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

		return $field->input;
    }

	static function socialsharing($pid)
	{
		$configClass = self::loadConfig();
		$needs = [];
		$needs[] = "property_details";
		$needs[] = $pid;
		$itemid = OSPRoute::getItemid($needs);
		$property_link = Route::_("index.php?option=com_osproperty&task=property_details&id=".$pid."&Itemid=".$itemid);
		$property_link = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).$property_link;

		if($configClass['social_sharing'] == 1)
		{
			if($configClass['social_sharing_type'] == 1)
			{
				$add_this_share='
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_default_style">
					<a class="addthis_button_facebook_like" fb:like:layout="button_count" class="addthis_button" addthis:url="'.$property_link.'"></a>
					<a class="addthis_button_google_plusone" g:plusone:size="medium" class="addthis_button" addthis:url="'.$property_link.'"></a>
					<a class="addthis_button_tweet" class="addthis_button" addthis:url="'.$property_link.'"></a>
					<a class="addthis_button_pinterest_pinit" class="addthis_button" addthis:url="'.$property_link.'"></a>
					<a class="addthis_counter addthis_pill_style" class="addthis_button" addthis:url="'.$property_link.'"></a>
				</div>
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid="'.$configClass['publisher_id'].'"></script>
				<!-- AddThis Button END -->' ;
				$add_this_js='https://s7.addthis.com/js/300/addthis_widget.js';
				self::loadScriptOnce($add_this_js);
				//output all social sharing buttons
				return ' <div id="rr" >
					<div class="social_share_container">
					<div class="social_share_container_inner">'.
						$add_this_share.
					'</div>
				</div>
				</div>
				';
			}
			else
			{
				$social_sharing = '<script type="text/javascript" src="'.Uri::root(true).'/media/com_osproperty/assets/js/fblike.js"></script>';
				$social_sharing .= '<div class="jd_horizontal_social_buttons">';
					$social_sharing .= '<div class="jd_float_left">
							<div class="fb-like" data-href="'.$property_link.'" data-send="true" data-layout="button_count" data-width="450" data-show-faces="true">
							</div>
						</div>';
					$social_sharing .= '
					<div class="jd_float_left">
							&nbsp; <div class="g-plus" data-action="share" data-annotation="bubble" data-href="'.$property_link.'">
								</div>
					</div>';
				$social_sharing .= '<div class="jd_float_left">
						&nbsp; <a href="https://twitter.com/share" class="twitter-share-button"  data-url="'.$property_link.'" data-counturl="'.$property_link.'">Tweet</a>
					</div>';
				$social_sharing .= '</div>
					<div class="clearfix"></div>';

				return $social_sharing;
			}
		}
	}

	/** Function for  load Script
	 *
	 * @param   File  $script  Script
	 *
	 * @return  void
	 */
	public static function loadScriptOnce($script)
	{
		$doc = Factory::getDocument();
		$flg = 0;

		foreach ($doc->_scripts as $name => $ar)
		{
			if ($name == $script)
			{
				$flg = 1;
			}
		}

		if ($flg == 0)
		{
			$doc->addScript($script);
		}
	}

	static function generateMetaTags($itemid){
		if($itemid > 0){
			$app		= Factory::getApplication();
			$document	= Factory::getDocument();
			$menus		= $app->getMenu('site');
			$menu		= $menus->getItem($itemid);
			$params = new Registry();
			//$params->loadString($menu->getParams());
			$params    = $menu->getParams();
			if ($params->get('menu-meta_description')) {
				$document->setDescription($params->get('menu-meta_description'));
			}
			if ($params->get('menu-meta_keywords')) {
				$document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			}
			if ($params->get('robots')) {
				$document->setMetadata('robots', $params->get('robots'));
			}
		}
		return 1;
	}

    /**
     * This function is used to init BootstrapHelper variable
     */
	public static function generateBoostrapVariables(){
        global $configClass,$bootstrapHelper;
        $configClass = self::loadConfig();
		if((int)$configClass['twitter_bootstrap_version'] == 0)
		{
			$configClass['twitter_bootstrap_version'] = 2;
		}
		if(!self::isJoomla4() && Factory::getApplication()->isClient('administrator'))
		{
			$configClass['twitter_bootstrap_version'] = 2;
		}
        $bootstrapHelper = new OspropertyHelperBootstrap((int)$configClass['twitter_bootstrap_version']);
    }

	/**
     * Get Image from Url
     *
     * @param unknown_type $link
     * @return unknown
     */
    public static function getImageFromUrl($link){
        $ch = curl_init ($link);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

	static function getDpeClassDropdownlist($type,$value){
		global $configClass;
		$configClass = self::loadConfig();
		$optionArr = [];
		if($type == 0){
			$optionArr[] = HTMLHelper::_('select.option','',Text::_('OS_ENERGY_CLASS'));
			$energy_class = $configClass['energy_class'];
			if($energy_class  != ""){
				$energy_class_array = explode(",",$energy_class);
				if(count($energy_class_array))
				{
					foreach($energy_class_array as $e)
					{
						$optionArr[] = HTMLHelper::_('select.option',$e,$e);
					}
					return HTMLHelper::_('select.genericlist',$optionArr,'e_class','class="inlinedisplaye input-medium form-select imedium "','value','text',$value);
				}
			}
		}
		else
		{
			$optionArr[] = HTMLHelper::_('select.option','',Text::_('OS_CO2_CLASS'));
			$energy_class = $configClass['energy_class'];
			if($energy_class  != "")
			{
				$energy_class_array = explode(",",$energy_class);
				if(count($energy_class_array))
				{
					foreach($energy_class_array as $e)
					{
						$optionArr[] = HTMLHelper::_('select.option',$e,$e);
					}
					return HTMLHelper::_('select.genericlist',$optionArr,'c_class','class="inlinedisplaye input-medium form-select imedium "','value','text',$value);
				}
			}
		}
	}

	static function buildDropdownMarketStatus($mstatus){
        $configClass = self::loadConfig();
        $market_status 		= $configClass['active_market_status'];
        if($market_status == 1){
            $marketArr			= [];
            $marketArr[]		= HTMLHelper::_('select.option',0,Text::_('OS_MARKET_STATUS'));
            $market_status 		= $configClass['market_status'];
            if($market_status != ""){
                $market_status_array = explode(",",$market_status);
                if(in_array('1',$market_status_array)){
                    $marketArr[] = HTMLHelper::_('select.option',1,Text::_('OS_SOLD'));
                }
                if(in_array('2',$market_status_array)){
                    $marketArr[] = HTMLHelper::_('select.option',2,Text::_('OS_CURRENT'));
                }
                if(in_array('3',$market_status_array)){
                    $marketArr[] = HTMLHelper::_('select.option',3,Text::_('OS_RENTED'));
                }
				if(in_array('4',$market_status_array)){
                    $marketArr[] = HTMLHelper::_('select.option',4,Text::_('OS_OFF_MARKET'));
                }
				if(in_array('5',$market_status_array)){
                    $marketArr[] = HTMLHelper::_('select.option',5,Text::_('OS_FOR_SALE'));
                }
				if(in_array('6',$market_status_array)){
                    $marketArr[] = HTMLHelper::_('select.option',6,Text::_('OS_FOR_RENT'));
                }
				if(in_array('7',$market_status_array)){
                    $marketArr[] = HTMLHelper::_('select.option',7,Text::_('OS_SALE_PENDING'));
                }
            }
            return HTMLHelper::_('select.genericlist',$marketArr,'isSold','class="imedium input-medium form-select"','value','text',$mstatus);
        }
    }

    static function returnMarketStatus($marketStatus){
        switch ($marketStatus){
            case "1":
                return Text::_('OS_SOLD');
            break;
            case "2":
                return Text::_('OS_CURRENT');
            break;
            case "3":
                return Text::_('OS_RENTED');
            break;
			case "4":
                return Text::_('OS_OFF_MARKET');
            break;
			case "5":
                return Text::_('OS_FOR_SALE');
            break;
			case "6":
                return Text::_('OS_FOR_RENT');
            break;
			case "7":
                return Text::_('OS_SALE_PENDING');
            break;
        }
    }

	static function loadCategoryName($id){
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_categories where id = '$id'");
		$category = $db->loadObject();
		return self::getLanguageFieldValue($category,'category_name');
	}

	static function loadTypeName($id){
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_types where id = '$id'");
		$type = $db->loadObject();
		return self::getLanguageFieldValue($type,'type_name');
	}

	public static function redirect($url,$msg = ''){
		Factory::getApplication()->enqueueMessage($msg);
		Factory::getApplication()->redirect($url);
	}

    /**
     * Generate article selection box
     *
     * @param int    $fieldValue
     * @param string $fieldName
     *
     * @return string
     */
    public static function getArticleInput($fieldValue, $fieldName = 'article_id')
	{
		HTMLHelper::_('jquery.framework');
		FormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_content/models/fields');

		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			FormHelper::addFieldPrefix('Joomla\Component\Content\Administrator\Field');

		}

		$field = FormHelper::loadFieldType('Modal_Article');

		if (version_compare(JVERSION, '4.2.0-dev', 'ge'))
		{
			$field->setDatabase(Factory::getDbo());
		}

		$element = new SimpleXMLElement('<field />');
		$element->addAttribute('name', $fieldName);
		$element->addAttribute('select', 'true');
		$element->addAttribute('clear', 'true');

		$field->setup($element, $fieldValue);

		return $field->input;
	}

    /**
     * This function is used to check if Agent profile can be shown as public
     * @param $agentoptin
     * @return bool
     */
    static function allowShowingProfile($agentoptin)
	{
        global $configClass;
        if($configClass['show_agent_details'] == 1)
		{
            if($configClass['use_privacy_policy'] == 1) 
			{
                if ($configClass['allow_user_profile_optin'] && $agentoptin == 0) 
				{
                    return true;
                }
				else
				{
					if ($configClass['allow_user_profile_optin'] == 0)
					{
						return true;
					}
                    return false;
                }
            }
			else
			{
                return true;
            }
        }
        return false;
    }

    /**
     * This function is used to check if Agent profile can be shown as public in properties listing page
     * @param $agentoptin
     * @return bool
     */
    static function allowShowingProfileInListing($agentoptin){
        global $configClass;
        if($configClass['listing_show_agent'] == 1){
            if($configClass['use_privacy_policy']) {
                if ($configClass['allow_user_profile_optin'] && $agentoptin == 0) {
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }
        return false;
    }

    static function removeProperties($cid){
        global $jinput, $mainframe;
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        $db = Factory::getDBO();
        if($cid){
            foreach($cid as $pid){
                $db->setQuery("Select pro_pdf_file from #__osrs_properties where id = '$pid'");
                $pro_pdf_file = $db->loadResult();
                if($pro_pdf_file != ""){
                    if(File::exists(JPATH_ROOT.'/components/com_osproperty/document/'.$pro_pdf_file)){
                        File::delete(JPATH_ROOT.'/components/com_osproperty/document/'.$pro_pdf_file);
                    }
                }
            }
            $cids = implode(",",$cid);
            //remove properties in property category relation
            $db->setQuery("Delete from #__osrs_property_categories where pid in ($cids)");
            $db->execute();
            //remove from properties table
            $db->setQuery("Delete from #__osrs_properties where id in ($cids)");
            $db->execute();
            //remove from amenities table
            $db->setQuery("Delete from #__osrs_property_amenities where pro_id in ($cids)");
            $db->execute();
            //remove from extra field table
            $db->setQuery("Delete from #__osrs_property_field_value where pro_id in ($cids)");
            $db->execute();
            //remove from expired table
            $db->setQuery("Delete from #__osrs_expired where pid in ($cids)");
            $db->execute();
            //remove from queue table
            $db->setQuery("Delete from #__osrs_queue where pid in ($cids)");
            $db->execute();
            //remove from neighborhood table
            $db->setQuery("Delete from #__osrs_neighborhood where pid in ($cids)");
            $db->execute();
            //remove from #__osrs_property_field_opt_value
            $db->setQuery("Delete from #__osrs_property_field_opt_value where pid in ($cids)");
            $db->execute();
            //remove from tag xref
            $db->setQuery("Delete from #__osrs_tag_xref where pid in ($cids)");
            $db->execute();

            //remove images
            $db->setQuery("Select * from #__osrs_photos where pro_id in ($cids)");
            $photos = $db->loadObjectList();
            if(count($photos) > 0){
                for($i=0;$i<count($photos);$i++){
                    $photo = $photos[$i];
                    $image = $photo->image;
                    $image_link = JPATH_ROOT."/images/osproperty/properties/".$photo->pro_id;
                    File::delete($image_link."/".$image);
                    File::delete($image_link."/thumb/".$image);
                    File::delete($image_link."/medium/".$image);
                    File::delete($image_link."/original/".$image);
                }
            }
            $db->setQuery("Delete from #__osrs_photos where pro_id in ($cids)");
            $db->execute();
            foreach ($cid as $id){
                Folder::delete(JPATH_ROOT."/images/osproperty/properties/".$id);
            }
        }
    }


    /**
     * This function is used to generate the toolbar button in Property modification form
     * @param $pid
     * return Array: 0,1,2,3
     * 0: Save & Activate property
     * 1: Save
     * 2: Apply
     * 3: Close
     */
    static function returnToolbarButtons($pid)
	{
        global $mainframe,$configClass;
        $payment = self::activePayment();
        if($pid > 0)
		{ //edit property
            if (Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) 
			{
                $returnArr = array(1,2,3);
            }
			elseif(HelperOspropertyCommon::isCompanyAdmin() || HelperOspropertyCommon::isAgent())
			{
                if($payment && !self::isApprovedProperty($pid)) 
				{
                    $returnArr = array(0, 1, 2, 3);
                }
				else
				{
                    $returnArr = array(1, 2, 3);
                }
            }
        }
		else
		{
            if (Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) 
			{
                $returnArr = array(1, 2, 3);
            }
			elseif(HelperOspropertyCommon::isCompanyAdmin() || HelperOspropertyCommon::isAgent())
			{
                if($payment) 
				{
                    $returnArr = array(0, 1, 2, 3);
                }
				else
				{
                    $returnArr = array(1, 2, 3);
                }
            }
        }
        return $returnArr;
    }

    /**
     * Check to see if you already active Payment in OS Property system
     * @return int
     */
    static function activePayment()
	{
        global $configClass;
        if($configClass['active_payment'] == 1 || $configClass['integrate_membership'] == 1)
		{
            return true;
        }
		else
		{
            return false;
        }
    }

    /**
     * Is Approved property
     * @param $pid
     * @return mixed
     */
    static function isApprovedProperty($pid)
	{
        $db         = Factory::getDbo();
        $query      = $db->getQuery(true);
        if($pid > 0) 
		{
            $query->select(' 	approved')->from('#__osrs_properties')->where('id = "' . $pid . '"');
            $db->setQuery($query);
            return $db->loadResult();
        }
		else
		{
            return 0;
        }
    }

    /**
     * Approval property
     * @param $pid
     */
    static function approvalPropertyNonPayment($id){
        global $configClass;
        $db = Factory::getDbo();
        $db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
        $count = $db->loadResult();
        if($count == 0 && $configClass['general_approval'] == 1 && !self::activePayment())
		{
            HelperOspropertyCommon::setApproval("n",$id);
            OspropertyListing::setexpired('com_osproperty',$id);
			//send Email to admin
			OspropertyEmail::sendEmail($id,'new_property_inform',1);
			//send Email to agent
			OspropertyEmail::sendEmail($id,'new_property_confirmation',0);
        }
		//in case the property is unapproval, just sending notification to administrator
		if($count == 0 && $configClass['general_approval'] == 0 && !self::activePayment())
		{
			OspropertyEmail::sendEmail($id,'new_property_inform',1);
		}
    }

	static function approvalPropertyPaymentZero($id){
        global $configClass;
        $db = Factory::getDbo();
        $db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
        $count = $db->loadResult();
        //if($count == 0)
		//{
            HelperOspropertyCommon::setApproval("n",$id);
            OspropertyListing::setexpired('com_osproperty',$id);
			//send Email to admin
			OspropertyEmail::sendEmail($id,'new_property_inform',1);
			//send Email to agent
			OspropertyEmail::sendEmail($id,'new_property_confirmation',0);
        //}        
    }

    /**
     * This function is used to check Company or Agent permissions with specific pid
     * @param $pid
     */
    static function checkPermissionWithSpecificProperty($pid)
	{
        if( !HelperOspropertyCommon::isAgent() && !HelperOspropertyCommon::isCompanyAdmin())
		{
            return false;
        }
		elseif(HelperOspropertyCommon::isAgent())
		{ 
			//is Agent
            if(HelperOspropertyCommon::isOwner($pid))
			{
                return true;
            }
			else
			{
                return false;
            }
        }
		elseif(HelperOspropertyCommon::isCompanyAdmin())
		{
            if(HelperOspropertyCommon::isCompanyOwner($pid))
			{
                return true;
            }
			else
			{
                return false;
            }
        }
    }

    /**
     * This function is used to retieve Agent ID of specfic property
     * @param $pid
     */
    static function retrieveOwnerID($pid)
	{
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('agent_id')->from('#__osrs_properties')->where('id = "'.$pid.'"');
        $db->setQuery($query);
        return $db->loadResult();
    }

    static function retrieveCompanyId($pid){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('company_id')->from('#__osrs_properties')->where('id = "'.$pid.'"');
        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Generate related properties by categories and price
     * @param $id
     */
    static function generateRelatedProperties($id){
        $config     = self::loadConfig();
        $categories = self::getCategoryIdsOfProperty($id);
        $return     = [];
        if(count($categories) > 0){
            foreach($categories as $categoryId){
                if($config['price_from_'.$categoryId] != '' && $config['price_to_'.$categoryId] != ''){
                    $return[] = " ( pr.price >= '".$config['price_from_'.$categoryId]."' and pr.price <= '".$config['price_to_'.$categoryId]."' )";
                }
            }
            if(count($return) > 0) {
                return " AND (" . implode(" or ", $return) ." ) ";
            }
        }
        return '';
    }

    public static function getCompanyName($agent_id)
    {
        $db = Factory::getDbo();
        $db->setQuery("Select a.company_name from #__osrs_companies as a inner join #__osrs_agents as b on a.id = b.company_id where b.id = '$agent_id'");
        return $db->loadResult();
    }

	/**
	 * Get URL of the site, using for Ajax request
	 *
	 * @return string
	 *
	 * @throws Exception
	 */
	public static function getSiteUrl()
	{
		$uri  = Uri::getInstance();
		$base = $uri->toString(array('scheme', 'host', 'port'));

		if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
		{
			$script_name = $_SERVER['PHP_SELF'];
		}
		else
		{
			$script_name = $_SERVER['SCRIPT_NAME'];
		}

		$path = rtrim(dirname($script_name), '/\\');

		if ($path)
		{
			$siteUrl = $base . $path . '/';
		}
		else
		{
			$siteUrl = $base . '/';
		}

		if (Factory::getApplication()->isClient('administrator'))
		{
			$adminPos = strrpos($siteUrl, 'administrator/');
			$siteUrl  = substr_replace($siteUrl, '', $adminPos, 14);
		}

		return $siteUrl;
	}

	/**
	 * Internal method to get a JavaScript object notation string from an array
	 *
	 * @param   array  $array  The array to convert to JavaScript object notation
	 *
	 * @return  string  JavaScript object notation representation of the array
	 *
	 * @since   3.0
	 * @deprecated  4.0 Use `json_encode()` or `Joomla\Registry\Registry::toString('json')` instead
	 */
	public static function getJSObject(array $array = array())
	{

		$elements = [];

		foreach ($array as $k => $v)
		{
			// Don't encode either of these types
			if ($v === null || is_resource($v))
			{
				continue;
			}

			// Safely encode as a Javascript string
			$key = json_encode((string) $k);

			if (is_bool($v))
			{
				$elements[] = $key . ': ' . ($v ? 'true' : 'false');
			}
			elseif (is_numeric($v))
			{
				$elements[] = $key . ': ' . ($v + 0);
			}
			elseif (is_string($v))
			{
				if (strpos($v, '\\') === 0)
				{
					// Items such as functions and JSON objects are prefixed with \, strip the prefix and don't encode them
					$elements[] = $key . ': ' . substr($v, 1);
				}
				else
				{
					// The safest way to insert a string
					$elements[] = $key . ': ' . json_encode((string) $v);
				}
			}
			else
			{
				$elements[] = $key . ': ' . static::getJSObject(is_object($v) ? get_object_vars($v) : $v);
			}
		}

		return '{' . implode(',', $elements) . '}';
	}

	public static function isBathroomInformation($pid)
	{
		$db = Factory::getDbo();
		$db->setQuery("Select count(id) from #__osrs_property_bath_values where pid = '$pid'");
		$count = $db->loadResult();
		if($count > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function showBathroomInformation($pid)
	{
		$db = Factory::getDbo();
		$db->setQuery("Select count(id) from #__osrs_property_bath_values where pid = '$pid'");
		$count = $db->loadResult();
		if($count > 0)
		{
			$bathLabelArray = array('OS_FULL','OS_THREE_QUARTER','OS_HALF','OS_QUARTER','OS_ENSUITE');
			?>
				<table width="100%" class="bathinforTable">
					<?php
					foreach($bathLabelArray as $label)
					{
						$db->setQuery("Select `bath_value` from #__osrs_property_bath_values where pid = '$pid' and bath_label like '$label'");
						$bath_value = (int) $db->loadResult();
						if($bath_value > 0)
						{
							?>
							<tr>
								<td width="30%">
									<?php 
									echo Text::_($label);
									?>
								</td>
								<td>
									<?php
									echo $bath_value;	
									?>
								</td>
							</tr>
							<?php
						}
					}
					?>
				</table>
			<?php
		}
	}

	public static function verifyCaptcha($redirectUrl, $redirectMsg)
	{
		global $jinput;
		$captchaPlugin = Factory::getApplication()->getParams()->get('captcha', Factory::getConfig()->get('captcha'));
		if (!$captchaPlugin)
		{
			// Hardcode to recaptcha, reduce support request
			$captchaPlugin = 'recaptcha';
		}
		$plugin		   = PluginHelper::getPlugin('captcha', $captchaPlugin);
		if ($plugin)
		{
			try
			{
				$res   = Captcha::getInstance($captchaPlugin)->checkAnswer($jinput->post->get('recaptcha_response_field', '', 'string'));
				if (!$res)
				{
					self::redirect(Route::_($redirectUrl),$redirectMsg);
				}
			}
			catch (Exception $e)
			{
				//do the same with case !$res
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}	
	}


	public static function loadTooltip()
	{
		
		
		HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
		$document = Factory::getDocument();
		$document->addScriptDeclaration("
		document.addEventListener('DOMContentLoaded', function () {
			var tooltipOptions = {'html' : true, 'sanitize': false};      
				if (bootstrap.Tooltip) {
					var tooltipTriggerList = [].slice.call(document.querySelectorAll('.hastooltip'));
					var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
					  return new bootstrap.Tooltip(tooltipTriggerEl, tooltipOptions);
					});                                     
				}     
		});
		");
		
		
	}

	public static function showPriceText($price_text)
	{
		$price_text = str_replace('"','\'',$price_text);
		$price_text = str_replace('[','<',$price_text);
		$price_text = str_replace(']','>',$price_text);
		return $price_text;
	}

	public static function publishDateSql($prefix)
	{
		$db = Factory::getDbo();
		$sql = "";
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());
		$sql .= " AND ((".$prefix.".publish_up is null or  ".$prefix.".publish_up = ".$nullDate." or ".$prefix.".publish_up <= ".$nowDate."  or ".$prefix.".publish_up = '0000-00-00')";
		$sql .= " AND (".$prefix.".publish_down is null  or ".$prefix.".publish_down = ".$nullDate." or ".$prefix.".publish_down >= ".$nowDate."  or ".$prefix.".publish_down = '0000-00-00'))";
		return $sql;
	}

    public static function getDefaultAgent()
    {
        $db = Factory::getDbo();
        $db->setQuery("Select id from #__osrs_agents where default_agent = '1' limit 1");
        $id = $db->loadResult();
        if($id > 0)
        {
            return $id;
        }
        else
        {
            $db->setQuery("Select count(id) from #__osrs_agents");
            if ((int)$db->loadResult() == 1)
            {
                $db->setQuery("Select id from #__osrs_agents limit 1");
                return $db->loadResult();
            }
        }
        return 0;
    }

	
	public static function getChoicesJsSelect($html, $hint = '')
	{
		static $isJoomla4;

		if ($isJoomla4 === null)
		{
			$isJoomla4 = self::isJoomla4();
		}

		if ($isJoomla4)
		{
			Text::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
			Text::script('JGLOBAL_SELECT_PRESS_TO_SELECT');

			Factory::getApplication()->getDocument()->getWebAssetManager()
				->usePreset('choicesjs')
				->useScript('webcomponent.field-fancy-select');

			$attributes = [];

			$hint = $hint ?: Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_OPTIONS');

			$attributes[] = 'placeholder="' . $hint . '""';
			$attributes[] = 'search-placeholder="' . $hint . '""';


			return '<joomla-field-fancy-select ' . implode(' ', $attributes) . '>' . $html . '</joomla-field-fancy-select>';
		}

		return $html;
	}

	/**
	 * Helper method to write data to a log file, for debuging purpose
	 *
	 * @param   string  $logFile
	 * @param   array   $data
	 * @param   string  $message
	 */
	public static function logData($logFile, $data = [], $message = null)
	{
		$text = '[' . gmdate('m/d/Y g:i A') . '] - ';

		foreach ($data as $key => $value)
		{
			$text .= "$key=$value, ";
		}

		$text .= $message;

		$fp = fopen($logFile, 'a');
		fwrite($fp, $text . "\n\n");
		fclose($fp);
	}

	public static function timeago($date) 
	{
	   $timestamp = strtotime($date);	
	   
	   $strTime = array(Text::_('OS_SECOND'), Text::_('OS_MINUTE'), Text::_('OS_HOUR'), Text::_('OS_DAY'), Text::_('OS_MONTH'), Text::_('OS_YEAR'));
	   $length = array("60","60","24","30","12","10");

	   $currentTime = time();
	   if($currentTime >= $timestamp) 
	   {
			$diff     = time()- $timestamp;
			for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) 
			{
				$diff = $diff / $length[$i];
			}

			$diff = round($diff);
			return $diff . " " . $strTime[$i] . Text::_('OS_AGO');
	   }
	}

	/**
	 *
	 *
	 * @return string
	 */
	public static function validateEngine()
	{
		$dateNow    = HTMLHelper::_('date', Factory::getDate(), 'Y/m/d');
		$validClass = array(
			"",
			"validate[custom[integer]]",
			"validate[custom[number]]",
			"validate[custom[email]]",
			"validate[custom[url]]",
			"validate[custom[phone]]",
			"validate[custom[date],past[$dateNow]]",
			"validate[custom[ipv4]]",
			"validate[minSize[6]]",
			"validate[maxSize[12]]",
			"validate[custom[integer],min[-5]]",
			"validate[custom[integer],max[50]]");

		return json_encode($validClass);
	}

	/**
	 * Get hased field name to store the time which form started to be rendered
	 *
	 * @return string
	 */
	public static function getHashedFieldName()
	{
		$app = Factory::getApplication();

		$siteName = $app->get('sitename');
		$secret   = $app->get('secret');

		return md5('OSP' . $siteName . $secret);
	}

	/**
	 * Method to add some checks to prevent spams
	 *
	 */
	public static function antiSpam()
	{
		global $jinput;

		$honeypotFieldName = 'osp_my_own_website_name';

		if ($jinput->getString($honeypotFieldName))
		{
			throw new \Exception(Text::_('OS_HONEYPOT_SPAM_DETECTED'), 403);
		}

		
		$startTime = $jinput->getInt(OSPHelper::getHashedFieldName(), 0);

		if ((time() - $startTime) < 30)
		{
			throw new \Exception(Text::_('OS_FORM_SUBMIT_TOO_FAST'), 403);
		}
		
		$session = Factory::getSession();

		$numberSubmissions = (int) $session->get('osp_number_submissions', 0) + 1;

		if ($numberSubmissions > 20)
		{
			throw new \Exception(Text::_('OS_EXCEEDED_NUMBER_FORM_SUBMISSIONS'), 403);
		}
		else
		{
			$session->set('osp_number_submissions', $numberSubmissions);
		}
		
	}

	/**
	 * Show price 
	 * get value of record 21 from #__osrs_configuration
	 *
	 * @param unknown_type $price
	 * @return unknown
	 */
	public static function showPrice($price)
	{
		global $bootstrapHelper, $jinput, $configClass;
		static $money_format;
		$db = Factory::getDBO();
		if($money_format == null)
		{
			$db->setQuery("Select fieldvalue from #__osrs_configuration where fieldname like 'general_currency_money_format'");
			$money_format = $db->loadResult();
		}
		switch ($money_format){
			case "1":
				return number_format($price,2,',','.');
				break;
			case "2":
				return number_format($price,2,',',' ');
				break;
			case "3":
				return number_format($price,2,'.',',');
				break;
			case "4":
				return number_format($price,0,',','.');
				break;
			case "5":
				return number_format($price,0,',',' ');
				break;
			case "6":
				return number_format($price,0,'.',',');
				break;
		}
	}

	public static function createZipFile( $files = [], $destination = '', $overwrite = true )
    {
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );

        // if the zip file already exists and overwrite is false, return false
        if(File::exists($destination) && !$overwrite) {
            return false;
        }
        // vars
        $valid_files = [];

        // if files were passed in...
        if(is_array($files)) 
		{
            // cycle through each file
            foreach($files as $file) 
			{
                // make sure the file exists
                if(File::exists($file)) 
				{
                    $valid_files[] = $file;
                }
            }
        }

        // if we have valid files
        if(count($valid_files)) 
		{
            // create the archive
            $zip = new ZipArchive();
            $limit = 250;

            $result = $zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            if (!$result === TRUE)
			{
                return false;
            }

            // add the files
            foreach($valid_files as $file) 
			{

                // special function for trouble when we have to many files
                $only_filename = basename($file);
                //$zip->addFile($file, $only_filename);
                $zip->addFile($file, iconv('UTF-8', 'CP866', $only_filename)); // bugfix by 'Makulia'
            }
            // close the zip -- done!
            $zip->close();

            // check to make sure the file exists
            return file_exists($destination);
        } 
		else 
		{
            return false;
        }
    }

	public static function generateCaptcha()
	{
		$captchaPlugin = Factory::getApplication()->getParams()->get('captcha', Factory::getConfig()->get('captcha'));
		if ($captchaPlugin)
		{
			$googlecaptcha = 1;
			echo Captcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
		}
		else
		{
			Factory::getApplication()->enqueueMessage(Text::_('OS_CAPTCHA_NOT_ACTIVATED_IN_YOUR_SITE'), 'error');
		}
	}

	public static function generateJDCaptcha()
	{
		$captchaPlugin = 'jdcaptcha';
		if ($captchaPlugin)
		{
			echo Captcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
		}
	}

	public static function checkJDCaptcha()
	{
		global $jinput;
		$captchaPlugin = 'jdcaptcha';
		return Captcha::getInstance($captchaPlugin)->checkAnswer($jinput->post->get('recaptcha_response_field', '', 'string'));
	}

	public static function convertToEmbedLink($watch_url) 
	{
		// Extract video ID using regex
		if (preg_match('/v=([a-zA-Z0-9_-]+)/', $watch_url, $matches)) {
			// Return embed link
			return "https://www.youtube.com/embed/" . $matches[1];
		}
		return false; // Return false if the URL format is invalid
	}

	public static function returnSupportedCountries()
	{
		$countryIDArray = [12,28,35,169,66,193,86,92,130,147,187,152,162,175,71,9,13,18,20,15,51,65,73,90,120,136,167,87,39,47,55,114,138,144,176,181,195,91,149,151,110,41,145,3,5,77,45,82,93,25,135,146,50,206,163,170,192,125,132,198,84,54,185,97,56,191,207,208,209,116,4,30,49,133,158,178,123,83];
		$countryFileArray = ['au_australia','br_brazil','ca_canada','es_spain','fr_france','gb_united','in_india','it_italy','nl_netherlands','pt_portugal','tr_turkey','ru_russia','sg_singapore','se_sweden','de_germany','ar_argentina','at_austria','bb_barbados','be_belgium','bs_bahamas','dk_denmark','fi_finland','gr_greece','ie_ireland','mx_mexico','no_norway','za_southafrica','id_indonesia','cl_chile','hr_croatia','ec_ecuador','my_malaysia','pk_pakistan','pe_peru','ch_switzerland','th_thailand','uy_uruguay','il_israel','qa_qatar','ro_romania','lu_luxembourg','co_colombia','ph_philippines','al_albania','ad_andorra','gt_guatemala','cr_costarica','hn_honduras','jm_jamaica','bo_bolivia','ng_nigeria','pl_poland','cz_czech','mv_maldives','sk_slovakia','sk_srilanka','ae_uae','mo_morocco','nz_newzealand','ve_venezuela','hu_hungary','do_dominican','tt_trinidad','ke_kenya','eg_egypt','uk_ukraine','sl_scotland','nr_northern_ireland','wa_wales','mt_malta','dz_algeria','bg_bulgaria','cy_cyprus','ni_nicaragua','sa_saudiarabia','tw_taiwan','mo_montenegro','hk_hongkong'];

		return array_map(function ($id, $file) {
			return (object)[
				'country_id' => $id,
				'file_name' => "$file.txt"
			];
		}, $countryIDArray, $countryFileArray);

	}

	public static function getInstalledVersion()
	{
		return '4.0.0';
	}

	public static function currentUrl()
	{
		return base64_encode(Uri::getInstance()->toString());
	}
	/**
	 * Helper method to determine if we are in Joomla 4
	 *
	 * @return bool
	 */
	public static function isJoomla4()
	{
		return version_compare(JVERSION, '4.0.0-dev', 'ge');
	}

	/**
	 * Helper method to determine if we are in Joomla 4
	 *
	 * @return bool
	 */
	public static function isJoomla5()
	{
		return version_compare(JVERSION, '4.4.99', '>');
	}
}
?>
