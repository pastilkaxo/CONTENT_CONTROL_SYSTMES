<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

$result = '';
if (!empty ($module->content)) {
    $args = array(
        'caption' => $module->showtitle != 0 ? $module->title : '',
        'content' => $module->content
    );
    if (isset($attribs['funcStyling']) && '' !== $attribs['funcStyling']) {
        $result = call_user_func_array($attribs['funcStyling'], $args);
    } else {
        $result = $module->content;
    }
}

$app = Factory::getApplication();
$document = $app->getDocument();
if (isset($attribs['positionNumber'])) {
    $content = $document->getBuffer('component');
    $placeholder = '[[position_' . $attribs['positionNumber'] . ']]';
    $newPlaceholder = '';
    if (preg_match('/\[\[position_' . $attribs['positionNumber'] . '_(\d+)\]\]/', $content, $matches)) { // more than one module in position
        $placeholder = $matches[0];
        $count = (int) $matches[1];
        if ($count > 1) {
            $newPlaceholder = '[[position_' . $attribs['positionNumber'] . '_' . --$count . ']]';
        }
    }
    $content = str_replace($placeholder, $result . $newPlaceholder, $content);
    $document->setBuffer($content, 'component');
    $module->content = '';
    $module->contentRendered = false;
    $result = '';
}

echo $result;
