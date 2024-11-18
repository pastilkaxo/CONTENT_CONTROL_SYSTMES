<?php
defined('_JEXEC') or die;

use NP\Processor\PositionsProcessor;
use NP\Utility\Theme;

$module  = $displayData['module'];
$attribs = $displayData['attribs'];

$number = $attribs['iterator'];
$result = PositionsProcessor::$blockLayouts[$number];
if (!empty($module->content) && $result) {
if ($module->showtitle != 0) {
$result = preg_replace('/<\!--block_header_content-->[\s\S]*?<\!--\/block_header_content-->/', $module->title, $result);
} else {
$result = preg_replace('/<\!--block_header-->[\s\S]+?<\!--\/block_header-->/', '', $result);
}
$moduleContent = str_replace('$', '\$', $module->content);
$moduleContent = Theme::stylingDefaultControls($moduleContent);
$result = preg_replace('/<\!--block_content_content-->[\s\S]*?<\!--\/block_content_content-->/', $moduleContent, $result);
$result = preg_replace('/<\!--\/?block\_?(header|content)?-->/', '', $result);
echo $result;
}