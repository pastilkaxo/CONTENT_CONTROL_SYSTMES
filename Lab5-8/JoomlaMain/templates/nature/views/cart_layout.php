<?php
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$themeOptions = $app->getTemplate(true)->params;
$fileName = $themeOptions->get('cart', '');
if ($fileName) {
    include_once dirname(__FILE__) . '/cart_layout_' . $fileName . '.php';
}
