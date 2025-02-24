CREATE TABLE IF NOT EXISTS `#__osrs_agent_account` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`agent_id` int( 11 ) DEFAULT NULL ,
`deadline_time` datetime DEFAULT NULL ,
`number_listings` int( 11 ) DEFAULT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT =1;


CREATE TABLE IF NOT EXISTS `#__osrs_agents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `company_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `fax` varchar(40) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
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
  `request_to_approval` tinyint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_amenities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amenities` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `category_description` text,
  `access` tinyint(3) unsigned DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `category_image` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `rate` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `created_on` datetime DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT '1',
  `alreadyPublished` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `address` varchar(255) DEFAULT NULL,
  `state` int(11) DEFAULT '0',
  `city` varchar(255) DEFAULT NULL,
  `country` int(11) DEFAULT '0',
  `postcode` varchar(50) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `fax` varchar(40) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(250) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `company_description` text,
  `published` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__osrs_company_agents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `agent_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__osrs_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldname` varchar(255) DEFAULT NULL,
  `fieldvalue` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(100) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


REPLACE INTO `#__osrs_countries` VALUES (1, 'Afghanistan', 'AF'),
(2, 'Aland islands', 'AX'),
(3, 'Albania', 'AL'),
(4, 'Algeria', 'DZ'),
(5, 'Andorra', 'AD'),
(6, 'Angola', 'AO'),
(7, 'Anguilla', 'AI'),
(8, 'Antigua and Barbuda', 'AG'),
(9, 'Argentina', 'AR'),
(10, 'Armenia', 'AM'),
(11, 'Aruba', 'AW'),
(12, 'Australia', 'AU'),
(13, 'Austria', 'AT'),
(14, 'Azerbaijan', 'AZ'),
(15, 'Bahamas', 'BS'),
(16, 'Bahrain', 'BH'),
(17, 'Bangladesh', 'BD'),
(18, 'Barbados', 'BB'),
(19, 'Belarus', 'BY'),
(20, 'Belgium', 'BE'),
(21, 'Belize', 'BZ'),
(22, 'Benin', 'BJ'),
(23, 'Bermuda', 'BM'),
(24, 'Bhutan', 'BT'),
(25, 'Bolivia', 'BO'),
(26, 'Bosnia and Herzegovina', 'BA'),
(27, 'Botswana', 'BW'),
(28, 'Brazil', 'BR'),
(29, 'Brunei Darussalam', 'BN'),
(30, 'Bulgaria', 'BG'),
(31, 'Burkina Faso', 'BF'),
(32, 'Burundi', 'BI'),
(33, 'Cambodia', 'KH'),
(34, 'Cameroon', 'CM'),
(35, 'Canada', 'CA'),
(36, 'Cape Verde', 'CV'),
(37, 'Central african republic', 'CF'),
(38, 'Chad', 'TD'),
(39, 'Chile', 'CL'),
(40, 'China', 'CN'),
(41, 'Colombia', 'CO'),
(42, 'Comoros', 'KM'),
(43, 'Republic of Congo', 'CG'),
(44, 'The Democratic Republic of the Congo', 'CD'),
(45, 'Costa Rica', 'CR'),
(46, 'Cote d''Ivoire', 'CI'),
(47, 'Croatia', 'HR'),
(48, 'Cuba', 'CU'),
(49, 'Cyprus', 'CY'),
(50, 'Czech Republic', 'CZ'),
(51, 'Denmark', 'DK'),
(52, 'Djibouti', 'DJ'),
(53, 'Dominica', 'DM'),
(54, 'Dominican Republic', 'DO'),
(55, 'Ecuador', 'EC'),
(56, 'Egypt', 'EG'),
(57, 'El salvador', 'SV'),
(58, 'Equatorial Guinea', 'GQ'),
(59, 'Eritrea', 'ER'),
(60, 'Estonia', 'EE'),
(61, 'Ethiopia', 'ET'),
(62, 'Faeroe Islands', 'FO'),
(63, 'Falkland Islands', 'FK'),
(64, 'Fiji', 'FJ'),
(65, 'Finland', 'FI'),
(66, 'France', 'FR'),
(67, 'French Guiana', 'GF'),
(68, 'Gabon', 'GA'),
(69, 'Gambia, the', 'GM'),
(70, 'Georgia', 'GE'),
(71, 'Germany', 'DE'),
(72, 'Ghana', 'GH'),
(73, 'Greece', 'GR'),
(74, 'Greenland', 'GL'),
(75, 'Grenada', 'GD'),
(76, 'Guadeloupe', 'GP'),
(77, 'Guatemala', 'GT'),
(78, 'Guinea', 'GN'),
(79, 'Guinea-Bissau', 'GW'),
(80, 'Guyana', 'GY'),
(81, 'Haiti', 'HT'),
(82, 'Honduras', 'HN'),
(83, 'Hong Kong', 'HK'),
(84, 'Hungary', 'HU'),
(85, 'Iceland', 'IS'),
(86, 'India', 'IN'),
(87, 'Indonesia', 'ID'),
(88, 'Iran', 'IR'),
(89, 'Iraq', 'IQ'),
(90, 'Ireland', 'IE'),
(91, 'Israel', 'IL'),
(92, 'Italy', 'IT'),
(93, 'Jamaica', 'JM'),
(94, 'Japan', 'JP'),
(95, 'Jordan', 'JO'),
(96, 'Kazakhstan', 'KZ'),
(97, 'Kenya', 'KE'),
(98, 'North Korea', 'KP'),
(99, 'South Korea', 'KR'),
(100, 'Kuwait', 'KW'),
(101, 'Kyrgyzstan', 'KG'),
(102, 'Lao People''s Democratic Republic', 'LA'),
(103, 'Latvia', 'LV'),
(104, 'Lebanon', 'LB'),
(105, 'Lesotho', 'LS'),
(106, 'Liberia', 'LR'),
(107, 'Libya', 'LY'),
(108, 'Liechtenstein', 'LI'),
(109, 'Lithuania', 'LT'),
(110, 'Luxembourg', 'LU'),
(111, 'Macedonia', 'MK'),
(112, 'Madagascar', 'MG'),
(113, 'Malawi', 'MW'),
(114, 'Malaysia', 'MY'),
(115, 'Mali', 'ML'),
(116, 'Malta', 'MT'),
(117, 'Martinique', 'MQ'),
(118, 'Mauritania', 'MR'),
(119, 'Mauritius', 'MU'),
(120, 'Mexico', 'MX'),
(121, 'Moldova', 'MD'),
(122, 'Mongolia', 'MN'),
(123, 'Montenegro', 'ME'),
(124, 'Montserrat', 'MS'),
(125, 'Morocco', 'MA'),
(126, 'Mozambique', 'MZ'),
(127, 'Myanmar', 'MM'),
(128, 'Namibia', 'NA'),
(129, 'Nepal', 'NP'),
(130, 'Netherlands', 'NL'),
(131, 'New Caledonia', 'NC'),
(132, 'New Zealand', 'NZ'),
(133, 'Nicaragua', 'NI'),
(134, 'Niger', 'NE'),
(135, 'Nigeria', 'NG'),
(136, 'Norway', 'NO'),
(137, 'Oman', 'OM'),
(138, 'Pakistan', 'PK'),
(139, 'Palau', 'PW'),
(140, 'Palestinian Territories', 'PS'),
(141, 'Panama', 'PA'),
(142, 'Papua New Guinea', 'PG'),
(143, 'Paraguay', 'PY'),
(144, 'Peru', 'PE'),
(145, 'Philippines', 'PH'),
(146, 'Poland', 'PL'),
(147, 'Portugal', 'PT'),
(148, 'Puerto rico', 'PR'),
(149, 'Qatar', 'QA'),
(150, 'Reunion', 'RE'),
(151, 'Romania', 'RO'),
(152, 'Russian Federation', 'RU'),
(153, 'Rwanda', 'RW'),
(154, 'Saint Kitts and Nevis', 'KN'),
(155, 'Saint Lucia', 'LC'),
(156, 'Samoa', 'WS'),
(157, 'Sao Tome and Principe', 'ST'),
(158, 'Saudi Arabia', 'SA'),
(159, 'Senegal', 'SN'),
(160, 'Serbia', 'RS'),
(161, 'Sierra Leone', 'SL'),
(162, 'Singapore', 'SG'),
(163, 'Slovakia', 'SK'),
(164, 'Slovenia', 'SI'),
(165, 'Solomon Islands', 'SB'),
(166, 'Somalia', 'SO'),
(167, 'South Africa', 'ZA'),
(168, 'South Georgia and the South Sandwich Islands', 'GS'),
(169, 'Spain', 'ES'),
(170, 'Sri Lanka', 'LK'),
(171, 'Sudan', 'SD'),
(172, 'Suriname', 'SR'),
(173, 'Svalbard and Jan Mayen', 'SJ'),
(174, 'Swaziland', 'SZ'),
(175, 'Sweden', 'SE'),
(176, 'Switzerland', 'CH'),
(177, 'Syrian Arab Republic', 'SY'),
(178, 'Taiwan', 'TW'),
(179, 'Tajikistan', 'TJ'),
(180, 'Tanzania', 'TZ'),
(181, 'Thailand', 'TH'),
(182, 'Timor-Leste', 'TL'),
(183, 'Togo', 'TG'),
(184, 'Tonga', 'TO'),
(185, 'Trinidad and Tobago', 'TT'),
(186, 'Tunisia', 'TN'),
(187, 'Turkey', 'TR'),
(188, 'Turkmenistan', 'TM'),
(189, 'Turks and Caicos Islands', 'TC'),
(190, 'Uganda', 'UG'),
(191, 'Ukraine', 'UA'),
(192, 'United Arab Emirates', 'AE'),
(193, 'United Kingdom', 'GB'),
(194, 'United States', 'US'),
(195, 'Uruguay', 'UY'),
(196, 'Uzbekistan', 'UZ'),
(197, 'Vanuatu', 'VU'),
(198, 'Venezuela', 'VE'),
(199, 'Viet nam', 'VN'),
(200, 'Virgin Islands, British', 'VG'),
(201, 'Western Sahara', 'EH'),
(202, 'Yemen', 'YE'),
(203, 'Zambia', 'ZM'),
(204, 'Zimbabwe', 'ZW'),
(205, 'KKTC', 'KKTC');


 

CREATE TABLE IF NOT EXISTS `#__osrs_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_name` varchar(255) DEFAULT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `start_time` date DEFAULT NULL,
  `end_time` date DEFAULT NULL,
  `discount` int(3) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_email_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `email_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__osrs_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_key` varchar(255) DEFAULT NULL,
  `email_title` varchar(255) DEFAULT NULL,
  `email_content` text,
  `published` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `#__osrs_emails`
-- 

REPLACE INTO `#__osrs_emails` VALUES (1, 'payment_accept', 'Payment has been successfully accepted.', 'Dear {username},\r\n\r\nYour payment has been successfully accepted.\r\nPayment details:\r\nGateway: {gateway}\r\nTransaction ID: {txn}\r\nItem: {item}\r\nItem Price: {price}\r\nDate: {date}\r\n\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team\r\n', 1),
(2, 'free_approval_listing_created', 'Your Listing was successfully added to {site_name}', 'Dear Sir/Madam,\r\n\r\nNew Listing was successfully added to {site_name}, but it hadn''t approved yet, please wait for administrator activation.\r\nNow you can manage new listing details, add photos or video to your listing.\r\n\r\nUse following link to manage your listing:\r\n{link}\r\n\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team', 1),
(3, 'not_paid_listing_created', 'Your Listing was successfully added to {site_name}', 'Dear Sir/Madam,\r\n\r\nNew Listing was successfully added to {site_name}, this listing has unpaid status, you should make a payment, for approving your listing.\r\n\r\nPlease, use following link to make a payment:\r\n{link}\r\n\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team', 1),
(4, 'listing_expired_email', 'Your listing has been expired!', 'Dear {username},\r\n\r\nYour listing "{listing}" has now expired.\r\nPlease login to {site_name} in order to renew your listing.\r\nClick the following link to go to directly to the update page:\r\n{update_link}\r\nListing Details:\r\n{details_link}\r\n\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team', 1),
(5, 'comment_send_after_ad', 'Added new comment', 'Dear {username},\r\n\r\nIn your listing was added a new comment.\r\n<b>Author:</b> {author}\r\n<b>Title:</b> {title}\r\n<b>Message:</b> {message}\r\n<b>Rate:</b> {rate}\r\n____________________________________________________________\r\n\r\nIf you want look this message please click this link: {link}\r\n\r\nJust ignore or delete this message if you are not.\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team', 1),
(6, 'listing_activated', 'Listing has been activated in {site_name}', 'Dear {username},\r\n\r\nYour listing "{listing}" in {site_name} was activated.\r\nPlease click the following link to view listing details page:\r\n{link}\r\n\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team', 1),
(7, 'listing_deactivated', 'Listing has been deactivated in {site_name}', 'Dear {username},\r\n\r\nYour listing "{listing}" in {site_name} was deactivated by administrator.\r\nPlease contact site administrators to get details about this issue.\r\n{site_name}\r\n\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team', 1),
(8, 'tell_friend', '-Tell a Friend- form notification', 'Dear {friend_name},\r\n\r\nYour friend {name} wish to inform you about following listing, \r\nclick following link to check it:\r\n{link}\r\n\r\n{message}\r\n\r\nThank You!\r\n{site_name}\r\nAdministration Team', 1),
(9, 'featured_expire_listing', 'Your listing featured has been expired!', 'Dear {username},\r\nYour listing featured "{listing}" has been expired!\r\nYou may login to {site_name} and update (renew) your featured listing.\r\nClick the following link to go directly to the update page:\r\n{update_link}\r\nListing Details:\r\n{details_link}\r\n\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team', 1),
(10, 'new_property_inform', 'New property has been submitted', 'Dear Administrator,\r\nUser {customer} has just submitted new property. \r\nProperty details here :\r\n{property_details}\r\nPlease go this link to see the property details {link}\r\nThanks', 1),
(11, 'new_property_confirmation', 'You have submitted new property', 'Dear {customer},\r\nYou have just submitted new property\r\nHere is the details:\r\n{property_details}\r\n{information}\r\nThanks', 1),
(12, 'new_message_received', 'New message from {visitor_name} at {site_name}', 'Dear {received_name},\r\n\r\nYou have new message from {visitor_name} at {site_name}.\r\nSubject: {subject}\r\n{contact_email}\r\n\r\n{message}\r\n\r\nThank You!\r\n{site_name}\r\nAdministration Team\r\n{site_url}', 1),
(13, 'approximates_email', 'Your listing plan approximates to expiration date.', 'Dear {username},\r\n\r\nYour listing will be expire after {days} days ({expire_date}).\r\nYou may login to {site_name} and update your listing plan.\r\nClick the following link to go update page:\r\n{update_link}\r\nListing details\r\n{details_link}\r\n\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team', 1),
(14, 'request_approval_agent', 'New agent approval request', 'Dear Administrator,\r\nUser {customer} has just submitted agent approval request.\r\nPlease go this link to see the agent details {link}\r\nThanks', 1),
(15, 'request_approval_property', 'New property approval request', 'Dear Administrator,\r\nUser {customer} has just submitted property approval request.\r\nProperty : {property}\r\nPlease go this link to see the property details {link}\r\nThanks', 1),
(16, 'approval_agent_request', 'Your agent request has been approval', 'Dear {agent},\r\n\r\nYour agent registration request has been approved bty administrator. \r\nNow, you can add your property. \r\nYour sincerely,\r\n{site_name}', 1),
(17, 'featured_listing_activated', 'Feature listing has been activated in {site_name}', 'Dear {username},\r\n\r\nYour listing "{listing}" in {site_name} was activated.\r\nPlease click the following link to view listing details page:\r\n{link}\r\n\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team', 1),
(18, 'featured_listing_deactivated', 'Feature listing has been deactivated in {site_name}', 'Dear {username},\r\n\r\nYour feature listing "{listing}" in {site_name} was deactivated by administrator.\r\nPlease contact site administrators to get details about this issue.\r\n{site_name}\r\n\r\n______________________________\r\nThank you,\r\n{site_name}\r\nAdministration Team', 1),
(19, 'comment_add_send_to_admin', 'New submitted comment need approval', 'Dear Administrator,\r\n\r\nIn listing "{listing}" was added a new comment.\r\n<b>Author:</b> {author}\r\n<b>Title:</b> {title}\r\n<b>Message:</b> {message}\r\n<b>Rate:</b> {rate}\r\n____________________________________________________________\r\n\r\nIf you want look this message please click this link: {link}\r\n\r\nJust ignore or delete this message if you are not.', 1);



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
  `field_option` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__osrs_property_field_opt_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `fid` int(11) NOT NULL DEFAULT '0',
  `oid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `default_value` varchar(255) NOT NULL,
  `size` int(3) DEFAULT NULL,
  `maxlength` int(4) NOT NULL,
  `ncols` int(3) DEFAULT NULL,
  `nrows` int(3) DEFAULT NULL,
  `readonly` tinyint(1) unsigned DEFAULT NULL,
  `searchable` tinyint(1) unsigned DEFAULT NULL,
  `displaytitle` tinyint(1) unsigned DEFAULT NULL,
  `access` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `pro_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_fieldgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) DEFAULT NULL,
  `access` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_lastcron` (
  `runtime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



REPLACE INTO `#__osrs_lastcron` VALUES (1321009488);


CREATE TABLE IF NOT EXISTS `#__osrs_order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `order_status` char(1) DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `coupon_id` int(11) NOT NULL DEFAULT '0',
  `total` decimal(12,2) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_desc` text,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__osrs_pricegroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price` decimal(12,2) DEFAULT NULL,
  `display_price` varchar(50) DEFAULT '',
  `ordering` int(11) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

 

REPLACE INTO `#__osrs_pricegroups` VALUES (1, 100.00, '100,00', 1, 1),
(2, 1000000.00, '1.000.000,00', 2, 1),
(3, 5000000.00, '5.000.000,00', 3, 1),
(4, 15000000.00, '15.000.000,00', 4, 1);



CREATE TABLE IF NOT EXISTS `#__osrs_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_name` varchar(255) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `price_original` decimal(12,2) DEFAULT NULL,
  `curr` int(11) NOT NULL DEFAULT '0',
  `pro_small_desc` text,
  `pro_full_desc` text,
  `pro_type` int(11) DEFAULT NULL,
  `isFeatured` tinyint(1) unsigned DEFAULT NULL,
  `note` text,
  `lat_add` varchar(50) DEFAULT NULL,
  `long_add` varchar(50) DEFAULT NULL,
  `gbase_address` varchar(255) DEFAULT NULL,
  `price_call` tinyint(1) unsigned DEFAULT NULL,
  `gbase_url` varchar(150) DEFAULT NULL,
  `pro_video` text,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
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
  `bed_room` int(11) DEFAULT NULL,
  `bath_room` int(11) DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `parking` varchar(255) DEFAULT NULL,
  `energy` decimal(6,2) NOT NULL,
  `climate` decimal(6,2) NOT NULL,
  `rent_time` varchar(255) NOT NULL,
  `square_feet` varchar(50) NOT NULL,
  `number_of_floors` int(3) NOT NULL DEFAULT '0',
  `number_votes` int(11) NOT NULL DEFAULT '0',
  `total_points` int(11) NOT NULL DEFAULT '0',
  `request_to_approval` tinyint(1) NOT NULL DEFAULT '0',
  `request_featured` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__osrs_property_amenities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) DEFAULT NULL,
  `amen_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__osrs_property_field_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) DEFAULT NULL,
  `field_id` int(11) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__osrs_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `emailtype` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__osrs_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) DEFAULT NULL,
  `type_description` text,
  `published` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



REPLACE INTO `#__osrs_types` VALUES (1, 'for sale', '', 1),
(2, 'for lease', '', 1),
(3, 'for rent', '', 1),
(4, 'pending', '', 1),
(5, 'sold', '', 1),
(6, 'for sale or lease', '', 1);


CREATE TABLE IF NOT EXISTS `#__osrs_user_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__osrs_neighborhoodname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `neighborhood` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__osrs_user_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `list_name` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__osrs_user_list_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL DEFAULT '0',
  `field_id` varchar(100) NOT NULL,
  `field_type` tinyint(1) NOT NULL DEFAULT '0',
  `search_param` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__osrs_csv_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(255) DEFAULT NULL,
  `max_file_size` decimal(3,1) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_import` datetime DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__osrs_form_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) DEFAULT '0',
  `column_number` tinyint(3) unsigned DEFAULT NULL,
  `field` varchar(255) DEFAULT NULL,
  `header_text` varchar(255) DEFAULT '',
  `field_type` varchar(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__osrs_importlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL DEFAULT '0',
  `log1` text NOT NULL,
  `log2` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__osrs_importlog_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__osrs_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(100) NOT NULL,
  `currency_code` varchar(3) NOT NULL,
  `currency_symbol` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;