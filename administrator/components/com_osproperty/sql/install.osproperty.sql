CREATE TABLE IF NOT EXISTS `#__osrs_agent_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sub_id` int(11) NOT NULL DEFAULT '0',
  `agent_id` int(11) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `nproperties` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_agents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_type` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `company_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `fax` varchar(40) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` int(11) DEFAULT '0',
  `state` int(11) DEFAULT '0',
  `country` int(11) DEFAULT '0',
  `photo` varchar(255) DEFAULT NULL,
  `yahoo` varchar(50) DEFAULT NULL,
  `skype` varchar(50) DEFAULT NULL,
  `aim` varchar(50) DEFAULT NULL,
  `msn` varchar(50) DEFAULT NULL,
  `gtalk` varchar(50) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `license` varchar(255) DEFAULT NULL,
  `ordering` int(11) DEFAULT '0',
  `published` tinyint(1) unsigned DEFAULT '1',
  `featured` tinyint(1) unsigned DEFAULT '0',
  `request_to_approval` tinyint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `#__osrs_amenities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` tinyint(2) NOT NULL DEFAULT '0',
  `amenities` varchar(255) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `#__osrs_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `category_alias` varchar(255) NOT NULL DEFAULT '',
  `category_meta` TEXT DEFAULT NULL,
  `category_description` text,
  `access` tinyint(3) unsigned DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `category_image` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT '1',
  `category_name_es` varchar(255) DEFAULT NULL,
  `category_alias_es` varchar(255) DEFAULT NULL,
  `category_description_es` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(255) NOT NULL DEFAULT '',
  `country_id` int(11) NOT NULL DEFAULT '0',
  `state_id` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `rate1` tinyint(1) NOT NULL DEFAULT '0',
  `rate2` tinyint(1) NOT NULL DEFAULT '0',
  `rate3` tinyint(1) NOT NULL DEFAULT '0',
  `rate4` smallint(1) NOT NULL DEFAULT '0',
  `rate` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `ip_address` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `created_on` datetime DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT '1',
  `alreadyPublished` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL,
  `company_alias` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(11) DEFAULT '0',
  `address` varchar(255) DEFAULT NULL,
  `state` int(11) DEFAULT '0',
  `city` int(11) DEFAULT '0',
  `country` int(11) DEFAULT '0',
  `postcode` varchar(50) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `fax` varchar(40) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(250) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `company_description` text,
  `request_to_approval` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_company_agents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `agent_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldname` varchar(255) DEFAULT NULL,
  `fieldvalue` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `#__osrs_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(100) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
 

CREATE TABLE IF NOT EXISTS `#__osrs_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_name` varchar(255) DEFAULT NULL,
  `coupon_code` varchar(50) NOT NULL DEFAULT '',
  `start_time` date DEFAULT NULL,
  `end_time` date DEFAULT NULL,
  `discount` int(3) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `#__osrs_email_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `email_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_key` varchar(255) DEFAULT NULL,
  `email_title` varchar(255) DEFAULT NULL,
  `email_content` text,
  `published` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC ;


CREATE TABLE IF NOT EXISTS `#__osrs_expired` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `inform_time` datetime NOT NULL,
  `send_inform` tinyint(1) NOT NULL DEFAULT '0',
  `send_expired` tinyint(1) NOT NULL DEFAULT '0',
  `expired_time` datetime DEFAULT NULL,
  `expired_feature_time` datetime DEFAULT NULL,
  `send_featured` tinyint(1) NOT NULL DEFAULT '0',
  `remove_from_database` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__osrs_extra_field_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL DEFAULT '0',
  `field_option` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_property_field_opt_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `fid` int(11) NOT NULL DEFAULT '0',
  `oid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_extra_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `field_type` varchar(50) DEFAULT NULL,
  `field_name` varchar(50) DEFAULT NULL,
  `field_label` varchar(50) DEFAULT NULL,
  `field_description` text,
  `ordering` int(11) DEFAULT NULL,
  `required` tinyint(1) unsigned DEFAULT NULL,
  `show_description` tinyint(1) unsigned DEFAULT NULL,
  `options` text,
  `default_value` varchar(255) NOT NULL DEFAULT '',
  `size` int(3) DEFAULT NULL,
  `clickable` tinyint(1)  NOT NULL DEFAULT '0',
  `maxlength` int(4) NOT NULL,
  `value_type` tinyint(1) NOT NULL DEFAULT '0',
  `ncols` int(3) DEFAULT NULL,
  `nrows` int(3) DEFAULT NULL,
  `readonly` tinyint(1) unsigned DEFAULT NULL,
  `searchable` tinyint(1) unsigned DEFAULT NULL,
  `displaytitle` tinyint(1) unsigned DEFAULT NULL,
  `show_on_list` tinyint(1) NOT NULL DEFAULT '0',
  `access` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `#__osrs_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `pro_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `#__osrs_fieldgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) DEFAULT NULL,
  `access` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `#__osrs_lastcron` (
  `runtime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



REPLACE INTO `#__osrs_lastcron` VALUES (1321009488);


CREATE TABLE IF NOT EXISTS `#__osrs_order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `#__osrs_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL,
  `created_by` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT '',
  `x_card_num` varchar(20) NOT NULL DEFAULT '',
  `x_card_code` int(3) NOT NULL DEFAULT 0,
  `card_holder_name` varchar(100) NOT NULL DEFAULT '',
  `exp_year` int(4) NOT NULL DEFAULT 0,
  `exp_month` tinyint(2) NOT NULL DEFAULT 0,
  `card_type` varchar(50) NOT NULL DEFAULT '',
  `order_status` char(1) DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `coupon_id` int(11) NOT NULL DEFAULT '0',
  `total` decimal(16,2) DEFAULT NULL,
  `curr` int(3) NOT NULL DEFAULT 0,
  `direction` tinyint(1) NOT NULL DEFAULT '0',
  `payment_made` tinyint(1) NOT NULL DEFAULT '0',
  `message` text,
  `stripe_token` VARCHAR (255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_desc` text,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_pricegroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL DEFAULT '0',
  `price_from` decimal(16,2) DEFAULT NULL,
  `price_to` decimal(16,2) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(50) NOT NULL DEFAULT '',
  `pro_name` varchar(255) DEFAULT NULL,
  `pro_alias` varchar(255) NOT NULL DEFAULT '',
  `pro_browser_title` varchar(255) NOT NULL DEFAULT '',
  `agent_id` int(11) DEFAULT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(16,2) DEFAULT NULL,
  `price_original` decimal(16,2) DEFAULT NULL,
  `curr` int(11) NOT NULL DEFAULT '0',
  `pro_small_desc` text,
  `pro_full_desc` text,
  `pro_type` int(11) DEFAULT NULL,
  `isFeatured` tinyint(1) unsigned DEFAULT NULL,
  `isSold` tinyint(1) NOT NULL DEFAULT '0',
  `soldOn` date NOT NULL,
  `note` text,
  `lat_add` varchar(50) DEFAULT NULL,
  `long_add` varchar(50) DEFAULT NULL,
  `gbase_address` varchar(255) DEFAULT NULL,
  `price_call` tinyint(1) unsigned DEFAULT NULL,
  `gbase_url` varchar(150) DEFAULT NULL,
  `pro_video` text,
  `address` varchar(255) DEFAULT NULL,
  `city` int(11) DEFAULT '0',
  `state` int(11) DEFAULT '0',
  `region` varchar(255) DEFAULT NULL,
  `country` int(11) DEFAULT '0',
  `province` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `show_address` tinyint(1) unsigned DEFAULT NULL,
  `hits` int(11) DEFAULT NULL,
  `metadesc` varchar(255) DEFAULT NULL,
  `metakey` varchar(255) DEFAULT NULL,
  `created` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` date DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `access` tinyint(1) unsigned DEFAULT NULL,
  `publish_up` date DEFAULT NULL,
  `publish_down` date DEFAULT NULL,
  `remove_date` date NOT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  `approved` tinyint(1) unsigned DEFAULT NULL,
  `pro_pdf` varchar(255) DEFAULT NULL,
  `pro_pdf_file` varchar(255) NOT NULL,
  `panorama` text DEFAULT NULL,
  `bed_room` int(11) DEFAULT NULL,
  `bath_room` decimal(4,2) DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `parking` varchar(255) DEFAULT NULL,
  `energy` decimal(6,2) DEFAULT NULL,
  `climate` decimal(6,2) DEFAULT NULL,
  `rent_time` varchar(255) DEFAULT NULL,
  `square_feet` decimal(10,2) NOT NULL DEFAULT '0.00',
  `lot_size` decimal(7,2) NOT NULL DEFAULT '0.00',
  `number_of_floors` int(3) NOT NULL DEFAULT '0',
  `number_votes` int(11) NOT NULL DEFAULT '0',
  `total_points` int(11) NOT NULL DEFAULT '0',
  `total_request_info` int(11) NOT NULL DEFAULT '0',
  `request_to_approval` tinyint(1) NOT NULL DEFAULT '0',
  `request_featured` tinyint(1) NOT NULL DEFAULT '0',
  `posted_by` tinyint(1) NOT NULL DEFAULT '0',
  `living_areas` varchar(50) NOT NULL,
  `garage_description` varchar(255) NOT NULL,
  `built_on` int(4) NOT NULL,
  `remodeled_on` int(4) NOT NULL,
  `house_style` varchar(255) NOT NULL,
  `house_construction` varchar(255) NOT NULL,
  `exterior_finish` varchar(255) NOT NULL,
  `roof` varchar(100) NOT NULL,
  `flooring` varchar(100) NOT NULL,
  `floor_area_lower` decimal(10,2) NOT NULL,
  `floor_area_main_level` decimal(10,2) NOT NULL,
  `floor_area_upper` decimal(10,2) NOT NULL,
  `floor_area_total` decimal(10,2) NOT NULL,
  `basement_foundation` varchar(255) NOT NULL,
  `basement_size` decimal(12,2) NOT NULL,
  `percent_finished` varchar(100) NOT NULL,
  `subdivision` varchar(100) NOT NULL,
  `land_holding_type` varchar(100) NOT NULL,
  `land_area` decimal(10,2) NOT NULL,
  `total_acres` decimal(10,2) NOT NULL,
  `lot_dimensions` varchar(100) NOT NULL,
  `frontpage` varchar(100) NOT NULL,
  `depth` varchar(100) NOT NULL,
  `takings` varchar(255) NOT NULL,
  `returns` varchar(255) NOT NULL,
  `net_profit` varchar(255) NOT NULL,
  `business_type` varchar(255) NOT NULL,
  `stock` varchar(255) NOT NULL,
  `fixtures` varchar(255) NOT NULL,
  `fittings` varchar(255) NOT NULL,
  `percent_office` varchar(255) NOT NULL,
  `percent_warehouse` varchar(255) NOT NULL,
  `loading_facilities` varchar(255) NOT NULL,
  `fencing` varchar(255) NOT NULL,
  `rainfall` varchar(255) NOT NULL,
  `soil_type` varchar(255) NOT NULL,
  `grazing` varchar(255) NOT NULL,
  `cropping` varchar(255) NOT NULL,
  `irrigation` varchar(255) NOT NULL,
  `water_resources` varchar(255) NOT NULL,
  `carrying_capacity` varchar(100) NOT NULL,
  `storage` varchar(100) NOT NULL,
  `price_text` TEXT,
  `e_class` varchar(2) NOT NULL,
  `c_class` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `#__osrs_property_amenities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) DEFAULT NULL,
  `amen_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_property_field_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) DEFAULT NULL,
  `field_id` int(11) DEFAULT NULL,
  `value` text,
  `value_integer` int(11) NOT NULL,
  `value_decimal` decimal(12,2) NOT NULL,
  `value_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `emailtype` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `#__osrs_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL DEFAULT '0',
  `state_name` varchar(255) DEFAULT NULL,
  `state_code` varchar(50) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__osrs_tag_cloud` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word_search` varchar(255) DEFAULT NULL,
  `number_search` int(11) DEFAULT NULL,
  `field_search` varchar(255) DEFAULT NULL,
  `value_search` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) DEFAULT NULL,
  `type_alias` varchar(255) NOT NULL,
  `type_description` text,
  `price_type` tinyint(1) NOT NULL DEFAULT '0',
  `type_icon` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_user_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_neighborhoodname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `neighborhood` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

REPLACE INTO `#__osrs_neighborhoodname` VALUES (1, 'OS_SHOPPING_CENTER'),
(2, 'OS_TOWN_CENTER'),
(3, 'OS_HOSPITAL'),
(4, 'OS_POLICE_STATION'),
(5, 'OS_TRAIN_STATION'),
(6, 'OS_BUS_STATION'),
(7, 'OS_AIRPORT'),
(8, 'OS_COFFEE_SHOP'),
(9, 'OS_BEACH'),
(10, 'OS_CINEMA'),
(11, 'OS_PARK'),
(12, 'OS_SCHOOL'),
(13, 'OS_UNIVERSITY'),
(14, 'OS_EXHIBITION'),
(15, 'OS_SUPERMARKET');

CREATE TABLE IF NOT EXISTS `#__osrs_neighborhood` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `neighbor_id` int(11) NOT NULL DEFAULT '0',
  `mins` tinyint(3) NOT NULL DEFAULT '0',
  `traffic_type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_user_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `list_name` varchar(255) NOT NULL,
  `receive_email` tinyint(1) NOT NULL DEFAULT '0',
  `lang` varchar(20) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_user_list_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL DEFAULT '0',
  `field_id` varchar(100) NOT NULL,
  `field_type` tinyint(1) NOT NULL DEFAULT '0',
  `search_type` varchar(50) NOT NULL,
  `search_param` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_csv_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(255) DEFAULT NULL,
  `max_file_size` decimal(3,1) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_import` datetime DEFAULT NULL,
  `yes_value` varchar(50) NOT NULL,
  `no_value` varchar(50) NOT NULL,
  `ftype` tinyint(1) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `fcategory` tinyint(1) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `agent_id` int(11) NOT NULL DEFAULT '0',
  `country` int(11) NOT NULL DEFAULT '0',
  `fstate` tinyint(1) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `fcity` tinyint(1) NOT NULL DEFAULT '0',
  `city` int(11) NOT NULL DEFAULT '0',
  `image_type` tinyint(1) NOT NULL DEFAULT '0',
  `update_type` tinyint(1) NOT NULL DEFAULT '0',
  `active_cron_import` tinyint(1) NOT NULL DEFAULT '0',
  `csv_file` varchar(255) NOT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_form_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) DEFAULT '0',
  `column_number` tinyint(3) unsigned DEFAULT NULL,
  `field` varchar(255) DEFAULT NULL,
  `header_text` varchar(255) DEFAULT '',
  `field_type` varchar(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_importlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL DEFAULT '0',
  `log1` text NOT NULL,
  `log2` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_importlog_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(100) NOT NULL,
  `currency_code` varchar(3) NOT NULL,
  `currency_symbol` varchar(50) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_watermark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `author` varchar(60) DEFAULT NULL,
  `creation_date` varchar(50) DEFAULT NULL,
  `copyright` varchar(100) DEFAULT NULL,
  `license` varchar(255) DEFAULT NULL,
  `author_email` varchar(50) DEFAULT NULL,
  `author_url` varchar(50) DEFAULT NULL,
  `version` varchar(40) DEFAULT NULL,
  `description` text,
  `params` text,
  `support_mobile_device` tinyint(1) unsigned DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  `default_duplicate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `md5_key` text,
  `query` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(255) NOT NULL,
  `menu_icon` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `menu_task` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_init` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_ip` varchar(50) DEFAULT NULL,
  `item_type` tinyint(1) unsigned DEFAULT NULL,
  `report_reason` varchar(255) DEFAULT NULL,
  `report_details` text,
  `report_email` varchar(100) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `frontend_url` varchar(255) DEFAULT NULL,
  `backend_url` varchar(255) DEFAULT NULL,
  `report_on` int(11) DEFAULT '0',
  `is_checked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_tag_xref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) DEFAULT NULL,
  `pid` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_property_listing_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `featured` tinyint(1) unsigned DEFAULT NULL,
  `sold` tinyint(1) NOT NULL DEFAULT '0',
  `state_id` int(11) DEFAULT NULL,
  `agenttype` tinyint(2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_extra_field_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_property_history_tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `tax_year` int(4) DEFAULT NULL,
  `property_tax` decimal(10,2) DEFAULT NULL,
  `tax_change` decimal(10,2) DEFAULT NULL,
  `tax_assessment` decimal(10,2) DEFAULT NULL,
  `tax_assessment_change` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_property_open` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `start_from` datetime DEFAULT NULL,
  `end_to` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_property_price_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_property_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_list_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `list_id` int(11) DEFAULT NULL,
  `sent_notify` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_new_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `processed` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_xml` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) DEFAULT NULL,
  `publish_properties` tinyint(1) NOT NULL DEFAULT '0',
  `imported` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__osrs_xml_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xml_id` int(11) DEFAULT NULL,
  `obj_content` text,
  `imported` tinyint(1) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `#__osrs_plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `author` varchar(250) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `license` varchar(255) DEFAULT NULL,
  `author_email` varchar(50) DEFAULT NULL,
  `author_url` varchar(50) DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `params` text,
  `ordering` int(11) DEFAULT NULL,
  `published` tinyint(3) unsigned DEFAULT NULL,
  `support_recurring_subscription` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;