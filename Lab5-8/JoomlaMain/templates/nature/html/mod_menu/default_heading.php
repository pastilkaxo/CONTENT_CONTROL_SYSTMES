<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

$linkClassName = isset($linkClassName) ? $linkClassName : '';
$linkInlineStyles = isset($linkInlineStyles) ? $linkInlineStyles : '';

$title = $item->anchor_title ? $item->anchor_title : '';
$anchor_css = $item->anchor_css ?: '';

$attributes = array(
    'class' => array($linkClassName, $anchor_css, 'nav-header'),
    'title' => $title,
    'style' => $linkInlineStyles);

$linktype   = $item->title;

if ($item->menu_image) {
    $image_attributes = $item->menu_image_css ? array('class' => $item->menu_image_css) : array();
    $linktype = HTMLHelper::_('image', $item->menu_image, $item->title, $image_attributes);
    if ($itemParams->get('menu_text', 1)) {
        $linktype .= '<span class="image-title">' . $item->title . '</span>';
    }
}

echo funcTagBuilder('span', $attributes, $linktype);
