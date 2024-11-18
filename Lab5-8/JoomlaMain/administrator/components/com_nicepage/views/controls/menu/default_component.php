<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use NP\Utility\Theme;

$linkClassName = isset($linkClassName) ? $linkClassName : '';
$linkInlineStyles = isset($linkInlineStyles) ? $linkInlineStyles : '';
$linkActiveClass = isset($itemIsCurrent) && $itemIsCurrent ? 'active' : '';

$attrTitle = $item->anchor_title ? $item->anchor_title : '';
$attrClass = $item->anchor_css ? $item->anchor_css : '';

$attributes = array(
    'class' => array($linkClassName, $attrClass, $linkActiveClass),
    'title' => $attrTitle,
    'style' => $linkInlineStyles);

switch ($item->browserNav) {
default:
case 0:
    $attributes['href'] = $item->flink;
    break;
case 1:
    // _blank
    $attributes['href'] = $item->flink;
    $attributes['target'] = '_blank';
    break;
case 2:
    // window.open
    $attributes['href'] = $item->flink;
    $attributes['onclick'] = 'window.open(this.href,\'targetWindow\','
        . '\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes\');return false;';
    break;
}

$originalLink = $item->link;
$linkParts = explode('#', $originalLink);
if (count($linkParts) > 1) {
    $anchor = $linkParts[1];
    if (strpos($originalLink, 'com_content') !== false && preg_match('/#' . $anchor . '$/', $originalLink)) {
        $attributes['data-link-anchor'] = '#' . $anchor;
        $attributes['href'] = str_replace($attributes['data-link-anchor'], '', $attributes['href']);
    }
}

$title = '<span>' . $item->title . '</span>';

$linktype = $item->menu_image
    ? ('<img src="' . $item->menu_image . '" alt="' . $item->title . '" />'
        . ($itemParams->get('menu_text', 1) ? $title : ''))
    : $title;

echo Theme::funcTagBuilder('a', $attributes, $linktype);