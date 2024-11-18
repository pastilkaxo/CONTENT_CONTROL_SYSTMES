<?php
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$themeOptions = $app->getTemplate(true)->params;
$fileName = $themeOptions->get('search', '');
if ($fileName) {
    include_once dirname(__FILE__) . '/search_layout_' . $fileName . '.php';
}
