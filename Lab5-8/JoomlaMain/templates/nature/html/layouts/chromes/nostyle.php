<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$module  = $displayData['module'];

$app = Factory::getApplication();
$document = $app->getDocument();

if (!empty($module->content)) {
    $result = $module->content;
    if (isset($attribs['positionNumber'])) {
        $content = $document->getBuffer('component');
        $content = str_replace('[[position_' . $attribs['positionNumber'] . ']]', $result, $content);
        $document->setBuffer($content, 'component');
        $module->content = '';
        $module->contentRendered = false;
        $result = '';
    }
    echo $result;
}