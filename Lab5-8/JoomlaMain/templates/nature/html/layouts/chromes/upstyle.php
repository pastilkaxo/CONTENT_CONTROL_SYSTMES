<?php
defined('_JEXEC') or die;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

if ('' === $attribs['upstyle']) {
    echo $module->content;
    return;
}

$module->content = stylingDefaultControls($module->content);

$parts = explode('%', $attribs['upstyle']);
$style = $parts[0];

if (!isset($attribs['funcStyling']))
    $attribs['funcStyling'] = count($parts) > 1 ? $parts[1] : '';

$styles = array(
    'block' => 'block.php',
    'nostyle' => 'nostyle.php'
);

$fileName = $styles[$style];
$displayData['module'] = $module;
$displayData['params'] = $params;
$displayData['attribs'] = $attribs;
include dirname(__FILE__) . '/' . $fileName;