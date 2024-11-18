<?php
defined('_JEXEC') or die;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

$number = $attribs['iterator'];
$result = $GLOBALS['themeBlocks'][$number];
if (!empty($module->content) && $result) {
    if ($module->showtitle != 0) {
        $result = preg_replace('/<\!--block_header_content-->[\s\S]+?<\!--\/block_header_content-->/', $module->title, $result);
    } else {
        $result = preg_replace('/<\!--block_header-->[\s\S]+?<\!--\/block_header-->/', '', $result);
    }
    $result = preg_replace('/<\!--block_content_content-->[\s\S]+?<\!--\/block_content_content-->/', $module->content, $result);
    $result = preg_replace('/<\!--\/?block\_?(header|content)?-->/', '', $result);
    echo $result;
}