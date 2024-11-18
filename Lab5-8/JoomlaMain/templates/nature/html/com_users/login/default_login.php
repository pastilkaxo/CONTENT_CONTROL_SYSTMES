<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$themePath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $themePath . '/functions.php';

$themeOptions = Factory::getApplication()->getTemplate(true)->params;
$fileName = $themeOptions->get('login', '');
if ($fileName) {
    include_once dirname(__FILE__) . '/' . $fileName . '.php';
}