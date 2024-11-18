<?php
defined('_JEXEC') or die;

use Joomla\CMS\Filter\OutputFilter;

$linkClassName = isset($linkClassName) ? $linkClassName : '';
$linkInlineStyles = isset($linkInlineStyles) ? $linkInlineStyles : '';
$linkActiveClass = isset($itemIsCurrent) && $itemIsCurrent ? 'active' : '';
$attributes = array(
    'class' => array($linkClassName, $item->anchor_css, $linkActiveClass),
    'title' => $item->anchor_title,
    'href' => OutputFilter::ampReplace(htmlspecialchars($item->flink)),
    'style' => $linkInlineStyles);

switch ($item->browserNav) {
    case 1:
        // _blank
        $attributes['target'] = '_blank';
        break;
    case 2:
        // window.open
        $attributes['onclick'] = 'window.open(this.href,\'targetWindow\','
            . '\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes\');return false;';
        break;
}

$title = '<span>' . $item->title . '</span>';

$linktype = $item->menu_image
    ? ('<img src="' . $item->menu_image . '" alt="' . $item->title . '" />'
        . ($itemParams->get('menu_text', 1) ? $title : ''))
    : $title;

echo funcTagBuilder('a', $attributes, $linktype);