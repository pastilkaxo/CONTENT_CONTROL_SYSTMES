<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use NP\Utility\Theme;

$linkClassName = isset($linkClassName) ? $linkClassName : '';
$linkInlineStyles = isset($linkInlineStyles) ? $linkInlineStyles : '';

$title = $item->anchor_title ? $item->anchor_title : '';
$anchor_css = $item->anchor_css ? $item->anchor_css : '';

$attributes = array(
    'class' => array($linkClassName, $anchor_css, 'separator'),
    'title' => $title,
    'style' => $linkInlineStyles);

$linktype   = $item->title;

if ($item->menu_image) {
    $linktype = HTMLHelper::_('image', $item->menu_image, $item->title);

    if ($itemParams->get('menu_text', 1)) {
        $linktype .= '<span class="image-title">' . $item->title . '</span>';
    }
}

echo Theme::funcTagBuilder('span', $attributes, $linktype);
