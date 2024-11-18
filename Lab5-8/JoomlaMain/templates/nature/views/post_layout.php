<?php
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$themeOptions = $app->getTemplate(true)->params;
$fileName = $themeOptions->get('post', '');
if ($fileName) {
    include_once dirname(__FILE__) . '/post_layout_' . $fileName . '.php';
}
