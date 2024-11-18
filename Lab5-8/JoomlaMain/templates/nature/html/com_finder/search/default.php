<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

$themePath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $themePath . '/functions.php';

$themeOptions = Factory::getApplication()->getTemplate(true)->params;
$fileName = $themeOptions->get('search', '');
if ($fileName) {
    include_once dirname(__FILE__) . '/' . $fileName . '.php';
}
