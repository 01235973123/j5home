CREATE TABLE IF NOT EXISTS `#__eb_taxes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int UNSIGNED DEFAULT 0,
  `event_id` int UNSIGNED DEFAULT 0,
  `country` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `rate` decimal(10,2) DEFAULT '0.00',
  `vies` tinyint UNSIGNED DEFAULT 0,
  `published` tinyint UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_agendas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int UNSIGNED DEFAULT 0,
  `time` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `ordering` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `#__eb_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent` int DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `layout` varchar(50) NOT NULL DEFAULT '',
  `description` text,
  `ordering` int DEFAULT 0,
  `access` int NOT NULL DEFAULT 1,
  `published` tinyint UNSIGNED DEFAULT 0,
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `page_heading` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `meta_description` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `image_alt` varchar(255) NOT NULL DEFAULT '',
  `submit_event_access` int NOT NULL DEFAULT 1,
  `color_code` varchar(20) NOT NULL DEFAULT '',
  `text_color` varchar(20) NOT NULL DEFAULT '',
  `language` varchar(50) DEFAULT '*',
  `level` tinyint NOT NULL DEFAULT 1,
  `tax_rate` decimal(10,2) DEFAULT 0.00,
  `payment_methods` varchar(255) NOT NULL DEFAULT '',
  `paypal_email` varchar(255) NOT NULL DEFAULT '',
  `notification_emails` varchar(255) NOT NULL DEFAULT '',
  `category_detail_url` varchar(255) NOT NULL DEFAULT '',
  `admin_email_body` text,
  `user_email_subject` varchar(255) NOT NULL DEFAULT '',
  `user_email_body` text,
  `user_email_body_offline` text,
  `group_member_email_body` text,
  `thanks_message` text,
  `thanks_message_offline` text,
  `registration_approved_email_body` text,
  `reminder_email_subject` varchar(255) NOT NULL DEFAULT '',
  `reminder_email_body` text,
  `second_reminder_email_subject` varchar(255) NOT NULL DEFAULT '',
  `second_reminder_email_body` text,
  `third_reminder_email_subject` varchar(255) NOT NULL DEFAULT '',
  `third_reminder_email_body` text,
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent`),
  KEY `idx_access` (`access`),
  KEY `idx_published` (`published`),
  KEY `idx_alias` (`alias`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `#__eb_configs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL DEFAULT '',
  `config_value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `#__eb_countries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `zone_id` int NOT NULL DEFAULT 1,
  `name` varchar(64) NOT NULL DEFAULT '',
  `country_3_code` char(3) NOT NULL DEFAULT '',
  `country_2_code` char(2) NOT NULL DEFAULT '',
  `ordering` int DEFAULT 0,
  `published` tinyint NOT NULL DEFAULT 0,
  `country_id` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_country_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_coupons` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL DEFAULT '',
  `coupon_type` tinyint UNSIGNED DEFAULT 0,
  `discount` decimal(10,2) DEFAULT 0.00,
  `event_id` int NOT NULL DEFAULT 0,
  `times` int NOT NULL DEFAULT 0,
  `used` int DEFAULT 0,
  `valid_from` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `valid_to` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint UNSIGNED DEFAULT 0,
  `max_usage_per_user` int NOT NULL DEFAULT 0,
  `category_id` int NOT NULL DEFAULT 0,
  `user_id` int NOT NULL DEFAULT 0,
  `apply_to` int NOT NULL DEFAULT 0,
  `max_number_registrants` int NOT NULL DEFAULT 0,
  `min_number_registrants` int NOT NULL DEFAULT 0,
  `note` varchar(255) NOT NULL DEFAULT '',
  `enable_for` int NOT NULL DEFAULT 0,
  `access` int NOT NULL DEFAULT 1,
  `used_amount` decimal(10,2) DEFAULT 0.00,
  `min_payment_amount` decimal(10,2) DEFAULT 0.00,
  `max_payment_amount` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_coupon_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int DEFAULT 0,
  `category_id` int DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_coupon_id` (`coupon_id`),
  KEY `idx_category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_coupon_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int DEFAULT 0,
  `event_id` int DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_coupon_id` (`coupon_id`),
  KEY `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_discounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `number_events` int NOT NULL DEFAULT 0,
  `event_ids` tinytext,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_type` tinyint NOT NULL DEFAULT 1,
  `from_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `to_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `times` int NOT NULL DEFAULT 0,
  `used` int NOT NULL DEFAULT 0,
  `published` tinyint NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_discount_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `discount_id` int NOT NULL DEFAULT 0,
  `event_id` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_discount_id` (`discount_id`),
  KEY `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_emails` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email_type` varchar(50) NOT NULL DEFAULT '',
  `sent_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sent_to` tinyint NOT NULL DEFAULT 0,
  `email` varchar(100) DEFAULT 0,
  `subject` varchar(255) NOT NULL DEFAULT '',
  `body` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL DEFAULT 0,
  `main_category_id` int NOT NULL DEFAULT 0,
  `location_id` int NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `event_type` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `event_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `event_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `short_description` mediumtext,
  `description` text,
  `access` int NOT NULL DEFAULT 1,
  `registration_access` int NOT NULL DEFAULT 1,
  `individual_price` decimal(10,2) DEFAULT 0.00,
  `tax_rate` decimal(10,2) DEFAULT 0.00,
  `event_capacity` int DEFAULT 0,
  `private_booking_count` int DEFAULT 0,
  `waiting_list_capacity` int DEFAULT 0,
  `created_by` int DEFAULT 0,
  `cut_off_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `registration_type` tinyint UNSIGNED DEFAULT 0,
  `registrants_emailed` tinyint UNSIGNED DEFAULT 0,
  `max_group_number` int DEFAULT 0,
  `discount_type` int DEFAULT 0,
  `discount` decimal(10,2) DEFAULT 0.00,
  `early_bird_discount_type` tinyint UNSIGNED DEFAULT 0,
  `early_bird_discount_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `early_bird_discount_amount` decimal(10,2) DEFAULT 0.00,
  `enable_cancel_registration` tinyint UNSIGNED DEFAULT 0,
  `cancel_before_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `registrant_edit_close_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enable_sms_reminder` tinyint UNSIGNED DEFAULT 0,
  `recurring_type` tinyint UNSIGNED DEFAULT 0,
  `recurring_frequency` int DEFAULT 0,
  `weekdays` varchar(50) NOT NULL DEFAULT '',
  `monthdays` varchar(50) NOT NULL DEFAULT '',
  `recurring_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recurring_occurrencies` int DEFAULT 0,
  `paypal_email` varchar(255) NOT NULL DEFAULT '',
  `notification_emails` varchar(255) NOT NULL DEFAULT '',
  `admin_email_body` text,
  `user_email_body` text,
  `user_email_body_offline` text,
  `group_member_email_body` text,
  `thanks_message` text,
  `thanks_message_offline` text,
  `params` text,
  `ordering` int DEFAULT 0,
  `published` int DEFAULT 0,
  `custom_fields` text,
  `from_name` varchar(100) NOT NULL DEFAULT '',
  `from_email` varchar(150) NOT NULL DEFAULT '',
  `reply_to_email` varchar(150) NOT NULL DEFAULT '',
  `send_first_reminder` int NOT NULL DEFAULT 0,
  `send_second_reminder` int NOT NULL DEFAULT 0,
  `send_third_reminder` int NOT NULL DEFAULT 0,
  `first_reminder_frequency` CHAR(1) DEFAULT 'd',
  `second_reminder_frequency` CHAR(1) DEFAULT 'd',
  `third_reminder_frequency` CHAR(1) DEFAULT 'd',
  `second_reminder_email_body` text,
  `third_reminder_email_body` text,
  `free_event_registration_status` tinyint NOT NULL DEFAULT 1,
  `members_discount_apply_for` tinyint NOT NULL DEFAULT 0,
  `send_emails` tinyint NOT NULL DEFAULT '-1',
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `page_heading` varchar(255) NOT NULL DEFAULT '',
  `collect_member_information` char(1) NOT NULL DEFAULT '',
  `prevent_duplicate_registration` char(1) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `image_alt` varchar(255) NOT NULL DEFAULT '',
  `featured` tinyint NOT NULL DEFAULT 0,
  `hidden` tinyint NOT NULL DEFAULT 0,
  `has_multiple_ticket_types` tinyint NOT NULL DEFAULT 0,
  `discount_groups` varchar(255) NOT NULL DEFAULT '',
  `discount_amounts` varchar(255) NOT NULL DEFAULT '',
  `registration_start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `max_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activate_tickets_pdf` tinyint NOT NULL DEFAULT 0,
  `ticket_start_number` int NOT NULL DEFAULT 1,
  `ticket_prefix` varchar(10) NOT NULL DEFAULT '',
  `ticket_bg_image` varchar(255) NOT NULL DEFAULT '',
  `ticket_layout` text,
  `invoice_format` text,
  `min_group_number` tinyint NOT NULL DEFAULT 0,
  `registration_handle_url` varchar(255) NOT NULL DEFAULT '',
  `event_detail_url` varchar(255) NOT NULL DEFAULT '',
  `fixed_group_price` decimal(10,2) DEFAULT 0.00,
  `attachment` varchar(400) NOT NULL DEFAULT '',
  `registration_form_message` text,
  `registration_form_message_group` text,
  `hits` int NOT NULL DEFAULT 0,
  `late_fee_type` tinyint NOT NULL DEFAULT 0,
  `late_fee_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `late_fee_amount` decimal(10,2) DEFAULT 0.00,
  `is_additional_date` tinyint NOT NULL DEFAULT 0,
  `article_id` int NOT NULL DEFAULT 0,
  `deposit_type` tinyint NOT NULL DEFAULT 0,
  `deposit_amount` decimal(10,2) DEFAULT 0.00,
  `deposit_until_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `custom_field_ids` varchar(255) NOT NULL DEFAULT '',
  `event_password` varchar(255) NOT NULL DEFAULT '',
  `payment_methods` varchar(255) NOT NULL DEFAULT '',
  `currency_code` varchar(10) NOT NULL DEFAULT '',
  `currency_symbol` varchar(20) NOT NULL DEFAULT '',
  `thumb` varchar(255) NOT NULL DEFAULT '',
  `registration_approved_email_body` text,
  `language` varchar(50) DEFAULT '*',
  `created_language` varchar(50) DEFAULT '*',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `meta_description` varchar(255) NOT NULL DEFAULT '',
  `reminder_email_body` text,
  `enable_coupon` tinyint NOT NULL DEFAULT 0,
  `activate_waiting_list` tinyint NOT NULL DEFAULT '2',
  `price_text` varchar(255) NOT NULL DEFAULT '',
  `registration_complete_url` text,
  `offline_payment_registration_complete_url` text,
  `activate_certificate_feature` tinyint NOT NULL DEFAULT 0,
  `certificate_layout` text,
  `certificate_bg_image` varchar(255) NOT NULL DEFAULT '',
  `user_email_subject` varchar(255) NOT NULL DEFAULT '',
  `enable_terms_and_conditions` tinyint NOT NULL DEFAULT '2',
  `reminder_email_subject` varchar(255) NOT NULL DEFAULT '',
  `second_reminder_email_subject` varchar(255) NOT NULL DEFAULT '',
  `third_reminder_email_subject` varchar(255) NOT NULL DEFAULT '',  
  PRIMARY KEY (`id`),
  KEY `idx_main_category_id` (`main_category_id`),
  KEY `idx_location_id` (`location_id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_access` (`access`),
  KEY `idx_published` (`published`),
  KEY `idx_event_date` (`event_date`),
  KEY `idx_event_end_date` (`event_end_date`),
  KEY `idx_alias` (`alias`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `#__eb_event_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT 0,
  `category_id` int DEFAULT 0,
  `main_category` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_event_group_prices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT 0,
  `registrant_number` int DEFAULT 0,
  `price` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_event_speakers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT 0,
  `speaker_id` int DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_speaker_id` (`speaker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_event_sponsors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT 0,
  `sponsor_id` int DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_sponsor_id` (`sponsor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_fields` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `field_type` int DEFAULT 0,
  `required` tinyint UNSIGNED DEFAULT 0,
  `readonly` tinyint UNSIGNED DEFAULT 0,
  `values` text,
  `default_values` text,
  `fee_field` tinyint UNSIGNED DEFAULT 0,
  `fee_values` text,
  `fee_formula` varchar(255) NOT NULL DEFAULT '',
  `display_in` tinyint UNSIGNED DEFAULT 0,
  `rows` tinyint UNSIGNED DEFAULT NULL,
  `cols` tinyint UNSIGNED DEFAULT NULL,
  `size` int DEFAULT 0,
  `css_class` varchar(50) NOT NULL DEFAULT '',
  `input_mask` varchar(255) NOT NULL DEFAULT '',
  `container_size` varchar(50) NOT NULL DEFAULT '',
  `input_size` varchar(50) NOT NULL DEFAULT '',
  `prompt_text` varchar(255) NOT NULL DEFAULT '',
  `field_mapping` varchar(100) NOT NULL DEFAULT '',
  `depend_on_ticket_type_ids` varchar(400) NOT NULL DEFAULT '',
  `access` int NOT NULL DEFAULT 1,
  `ordering` int DEFAULT 0,
  `show_on_public_registrants_list` tinyint UNSIGNED DEFAULT 0,
  `show_on_registration_type` tinyint UNSIGNED DEFAULT 0,
  `taxable` tinyint UNSIGNED DEFAULT 1,
  `position` tinyint UNSIGNED DEFAULT 0,
  `published` tinyint UNSIGNED DEFAULT 0,
  `filterable` tinyint NOT NULL DEFAULT 0,
  `hide_for_first_group_member` tinyint NOT NULL DEFAULT 0,
  `not_required_for_first_group_member` tinyint NOT NULL DEFAULT 0,
  `newsletter_field_mapping` varchar(100) DEFAULT '',
  `server_validation_rules` varchar(255) DEFAULT '',
  `language` varchar(50) DEFAULT '*',
  `datatype_validation` tinyint NOT NULL DEFAULT 0,
  `discountable` tinyint NOT NULL DEFAULT 1,
  `extra_attributes` varchar(255) NOT NULL DEFAULT '',
  `show_in_list_view` tinyint NOT NULL DEFAULT 0,
  `depend_on_field_id` int NOT NULL DEFAULT 0,
  `depend_on_options` text,
  `max_length` int NOT NULL DEFAULT 0,
  `place_holder` varchar(255) NOT NULL DEFAULT '',
  `multiple` tinyint NOT NULL DEFAULT 0,
  `validation_rules` varchar(255) NOT NULL DEFAULT '',
  `validation_error_message` varchar(255) NOT NULL DEFAULT '',
  `quantity_field` tinyint NOT NULL DEFAULT 0,
  `quantity_values` text,
  `only_show_for_first_member` tinyint NOT NULL DEFAULT 0,
  `only_require_for_first_member` tinyint NOT NULL DEFAULT 0,
  `hide_on_email` tinyint NOT NULL DEFAULT 0,
  `hide_on_export` tinyint NOT NULL DEFAULT 0,
  `show_on_registrants` tinyint NOT NULL DEFAULT 0,
  `receive_confirmation_email` tinyint NOT NULL DEFAULT 0,
  `populate_from_previous_registration` tinyint NOT NULL DEFAULT 1,
  `pattern` varchar(255) NOT NULL DEFAULT '',
  `filter` varchar(100) NOT NULL DEFAULT '',
  `min` int NOT NULL DEFAULT 0,
  `max` int NOT NULL DEFAULT 0,
  `step` int NOT NULL DEFAULT 0,
  `is_searchable` tinyint NOT NULL DEFAULT 0,
  `is_core` tinyint NOT NULL DEFAULT 0,
  `fieldtype` varchar(50) NOT NULL DEFAULT '',
  `payment_method` varchar(255) NOT NULL DEFAULT '',
  `category_id` int NOT NULL DEFAULT 0,
  `encrypt_data` tinyint UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_access_id` (`access`),
  KEY `idx_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `#__eb_field_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int DEFAULT 0,
  `category_id` int DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_field_id` (`field_id`),
  KEY `idx_category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_field_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int DEFAULT 0,
  `event_id` int DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_field_id` (`field_id`),
  KEY `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_field_values` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `registrant_id` int DEFAULT 0,
  `field_id` int DEFAULT 0,
  `field_value` text,
  PRIMARY KEY (`id`),
  KEY `idx_registrant_id` (`registrant_id`),
  KEY `idx_field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_galleries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int UNSIGNED DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `ordering` int UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_locations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `gsd_venue_mapping` varchar(400) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `state` varchar(50) NOT NULL DEFAULT '',
  `zip` varchar(10) NOT NULL DEFAULT '',
  `country` varchar(100) NOT NULL DEFAULT '',
  `lat` decimal(10,6) DEFAULT '0.000000',
  `long` decimal(10,6) DEFAULT '0.000000',
  `published` tinyint UNSIGNED DEFAULT 0,
  `user_id` int NOT NULL DEFAULT 0,
  `language` varchar(50) DEFAULT '*',
  `layout` varchar(50) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `#__eb_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(255) DEFAULT NULL,
  `menu_parent_id` int(11) DEFAULT NULL,
  `menu_link` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `menu_class` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message_key` varchar(255) NOT NULL DEFAULT '',
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `#__eb_payment_plugins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `author` varchar(255) NOT NULL DEFAULT '',
  `creation_date` varchar(50) NOT NULL DEFAULT '',
  `copyright` varchar(255) NOT NULL DEFAULT '',
  `license` varchar(255) NOT NULL DEFAULT '',
  `author_email` varchar(50) NOT NULL DEFAULT '',
  `author_url` varchar(50) NOT NULL DEFAULT '',
  `version` varchar(20) NOT NULL DEFAULT '',
  `description` text,
  `params` text,
  `ordering` int DEFAULT 0,
  `published` tinyint UNSIGNED DEFAULT 0,
  `access` int NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_registrants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT 0,
  `user_id` int DEFAULT 0,
  `group_id` int DEFAULT 0,
  `first_name` varchar(50) NOT NULL DEFAULT '',
  `last_name` varchar(50) NOT NULL DEFAULT '',
  `organization` varchar(100) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `address2` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(50) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `zip` varchar(15) NOT NULL DEFAULT '',
  `phone` varchar(30) NOT NULL DEFAULT '',
  `fax` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `number_registrants` int DEFAULT 0,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `amount` decimal(10,2) DEFAULT 0.00,
  `register_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `payment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `registration_cancel_date` datetime,
  `payment_method` varchar(50) NOT NULL DEFAULT '',
  `transaction_id` varchar(50) NOT NULL DEFAULT '',
  `comment` text,
  `published` tinyint UNSIGNED DEFAULT 0,
  `cart_id` int NOT NULL DEFAULT 0,
  `invoice_year` int NOT NULL DEFAULT 0,
  `coupon_usage_restored` tinyint NOT NULL DEFAULT 0,
  `checked_in_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subscribe_newsletter` tinyint NOT NULL DEFAULT 1,
  `agree_privacy_policy` tinyint NOT NULL DEFAULT 1,
  `coupon_usage_times` int NOT NULL DEFAULT 1,
  `auto_coupon_coupon_id` int NOT NULL DEFAULT 0,
  `deposit_payment_processing_fee` decimal(10,6) DEFAULT '0.000000',
  `payment_amount` decimal(10,6) DEFAULT '0.000000',
  `payment_currency` varchar(15) NOT NULL DEFAULT '',
  `payment_processing_fee` decimal(10,6) DEFAULT '0.000000',
  `coupon_discount_amount` decimal(10,6) DEFAULT '0.000000',
  `late_fee` decimal(10,6) DEFAULT '0.000000',
  `notified` tinyint NOT NULL DEFAULT 0,
  `checked_in` tinyint NOT NULL DEFAULT 0,
  `coupon_usage_calculated` tinyint NOT NULL DEFAULT 0,
  `checked_in_count` tinyint NOT NULL DEFAULT 0,
  `deposit_amount` decimal(10,2) DEFAULT 0.00,
  `payment_status` tinyint NOT NULL DEFAULT 1,
  `coupon_id` int NOT NULL DEFAULT 0,
  `check_coupon` tinyint NOT NULL DEFAULT 0,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `tax_rate` decimal(10,2) DEFAULT 0.00,
  `registration_code` varchar(32) NOT NULL DEFAULT '',
  `params` text,
  `is_reminder_sent` tinyint NOT NULL DEFAULT 0,
  `is_second_reminder_sent` tinyint NOT NULL DEFAULT 0,
  `is_third_reminder_sent` tinyint NOT NULL DEFAULT 0,
  `first_sms_reminder_sent` tinyint NOT NULL DEFAULT 0,
  `second_sms_reminder_sent` tinyint NOT NULL DEFAULT 0,
  `is_deposit_payment_reminder_sent` tinyint NOT NULL DEFAULT 0,
  `is_offline_payment_reminder_sent` tinyint NOT NULL DEFAULT 0,
  `icpr_notified` tinyint NOT NULL DEFAULT 0,
  `certificate_sent` tinyint NOT NULL DEFAULT 0,
  `process_deposit_payment` tinyint NOT NULL DEFAULT 0,
  `deposit_payment_transaction_id` varchar(100) NOT NULL DEFAULT '',
  `refunded` tinyint NOT NULL DEFAULT 0,
  `user_ip` varchar(100) NOT NULL DEFAULT '',
  `deposit_payment_method` varchar(100) NOT NULL DEFAULT '',
  `is_group_billing` tinyint NOT NULL DEFAULT 0,
  `language` varchar(50) DEFAULT '*',
  `ticket_number` int NOT NULL DEFAULT 0,
  `ticket_code` varchar(40) NOT NULL DEFAULT '',
  `ticket_qrcode` varchar(40) NOT NULL DEFAULT '',
  `invoice_number` int NOT NULL DEFAULT 0,
  `formatted_invoice_number` varchar(255) NOT NULL DEFAULT '',
  `created_by` int DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_published` (`published`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_cart_id` (`cart_id`),
  KEY `idx_first_name` (`first_name`),
  KEY `idx_last_name` (`last_name`),
  KEY `idx_email` (`email`),
  KEY `idx_payment_method` (`payment_method`),
  KEY `idx_transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `#__eb_registrant_tickets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `registrant_id` int DEFAULT 0,
  `ticket_type_id` int DEFAULT 0,
  `quantity` int DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_registrant_id` (`registrant_id`),
  KEY `idx_ticket_type_id` (`ticket_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_speakers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int UNSIGNED DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `url` varchar(255) NOT NULL DEFAULT '',
  `ordering` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `#__eb_sponsors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int UNSIGNED DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `website` varchar(255) NOT NULL DEFAULT '',
  `ordering` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_states` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int NOT NULL DEFAULT 1,
  `state_name` varchar(64) NOT NULL DEFAULT '',
  `state_3_code` char(10) NOT NULL DEFAULT '',
  `state_2_code` char(10) NOT NULL DEFAULT '',
  `state_id` int NOT NULL DEFAULT 0,
  `published` tinyint NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_state_name` (`state_name`),
  UNIQUE KEY `state_3_code` (`country_id`,`state_3_code`),
  UNIQUE KEY `state_2_code` (`country_id`,`state_2_code`),
  KEY `idx_country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_themes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `author` varchar(255) NOT NULL DEFAULT '',
  `creation_date` varchar(50) NOT NULL DEFAULT '',
  `copyright` varchar(255) NOT NULL DEFAULT '',
  `license` varchar(255) NOT NULL DEFAULT '',
  `author_email` varchar(50) NOT NULL DEFAULT '',
  `author_url` varchar(50) NOT NULL DEFAULT '',
  `version` varchar(20) NOT NULL DEFAULT '',
  `description` text,
  `params` text,
  `ordering` int DEFAULT 0,
  `published` tinyint UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_ticket_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `discount_rules` text,
  `price` decimal(10,2) DEFAULT 0.00,
  `capacity` int DEFAULT 0,
  `weight` int NOT NULL DEFAULT 1,
  `max_tickets_per_booking` int NOT NULL DEFAULT 0,
  `min_tickets_per_booking` int NOT NULL DEFAULT 0,
  `parent_ticket_type_id` int NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int NOT NULL DEFAULT 1,
  `discountable` int NOT NULL DEFAULT 1,
  `ordering` int DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `#__eb_urls` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `query` text,
  `route` varchar(400) NOT NULL DEFAULT '',
  `view` varchar(15) NOT NULL DEFAULT '',
  `record_id` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_md5_key` (`md5_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_mitems` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NULL,
  `title` varchar(255) NULL,
  `title_en` varchar(400) NULL,
  `description` varchar(255) NULL,
  `type` varchar(255) NULL,
  `group` varchar(255) NULL,
  `translatable` tinyint UNSIGNED DEFAULT 1,
  `featured` tinyint UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_exporttmpls` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `fields` text,
  `ordering` int DEFAULT 0,
  `published` tinyint NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_mmtemplates` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`message` text,
	`ordering` int NOT NULL DEFAULT 0,
	`published` tinyint NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__eb_accesstokens` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`token` varchar(400) NOT NULL DEFAULT '',
	`expire_at` bigint UNSIGNED NOT NULL DEFAULT 0,
	`vendor` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
