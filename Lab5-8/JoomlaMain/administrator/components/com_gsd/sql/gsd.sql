CREATE TABLE IF NOT EXISTS `#__gsd` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `contenttype` varchar(100) NOT NULL,
  `params` text,
  `plugin` varchar(50) NOT NULL DEFAULT '0' COMMENT 'The plugin name of the referenced item',
  `appview` varchar(50) NOT NULL DEFAULT '*',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `language` varchar(7) NOT NULL DEFAULT '*',
  `note` text NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `state` (`state`),
  KEY `plugin` (`plugin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gsd_config` (
  `name` varchar(255) NOT NULL,
  `params` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Store any configuration in key => params maps';