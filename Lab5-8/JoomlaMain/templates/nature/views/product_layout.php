<?php
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$themeOptions = $app->getTemplate(true)->params;
$fileName = $themeOptions->get('product', '');
if ($fileName) {
    include_once dirname(__FILE__) . '/product_layout_' . $fileName . '.php';
}
