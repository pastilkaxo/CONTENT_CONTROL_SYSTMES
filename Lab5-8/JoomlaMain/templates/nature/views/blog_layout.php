<?php
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$themeOptions = $app->getTemplate(true)->params;
$fileName = $themeOptions->get('blog', '');
if ($fileName) {
    include_once dirname(__FILE__) . '/blog_layout_' . $fileName . '.php';
}
