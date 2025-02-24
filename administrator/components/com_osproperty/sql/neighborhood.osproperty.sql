CREATE TABLE `#__osrs_neighborhoodname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `neighborhood` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `#__osrs_neighborhoodname` VALUES (1, 'OS_SHOPPING_CENTER'),
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

CREATE TABLE `#__osrs_neighborhood` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `neighbor_id` int(11) NOT NULL DEFAULT '0',
  `mins` tinyint(3) NOT NULL DEFAULT '0',
  `traffic_type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;