<?php
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$themeOptions = $app->getTemplate(true)->params;
$fileName = $themeOptions->get('checkout', '');
if ($fileName) {
    include_once dirname(__FILE__) . '/checkout_layout_' . $fileName . '.php';
}
