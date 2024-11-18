ALTER TABLE `#__gsd` 
    ADD `title` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `id`, 
    ADD `contenttype` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `title`, 
    ADD `note` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `ordering`,
    ADD INDEX `plugin` (`plugin`);