<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$themePath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $themePath . '/functions.php';

Core::load("Core_Content");

$component = new CoreContent($this, $this->params);
$allItems = $this->items;
$all = count($allItems);

$themeOptions = Factory::getApplication()->getTemplate(true)->params;
$fileName = $themeOptions->get('blog', '');
if ($fileName) {
    include_once dirname(__FILE__) . '/' . $fileName . '.php';
}