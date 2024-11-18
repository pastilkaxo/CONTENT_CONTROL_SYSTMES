CREATE TABLE IF NOT EXISTS `#__nicepage_sections` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `page_id` int(10) unsigned NOT NULL DEFAULT '0',
    `props` mediumtext NOT NULL,
    `preview_props` mediumtext NOT NULL,
    `autosave_props` mediumtext NOT NULL,
    `templateKey` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__nicepage_params` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `params` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
);