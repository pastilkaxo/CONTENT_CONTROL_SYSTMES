<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use NP\Utility\Theme;

if (isset($controlProps) && isset($controlTemplate)) {
    $logoInfo = Theme::getLogoInfo(
        array(
            'src' => isset($controlProps['src']) ? $controlProps['src'] : '#',
            'href' => isset($controlProps['href']) ? $controlProps['href'] : '#',
        ),
        true
    );
    $controlTemplate = str_replace('[[url]]', $logoInfo['href'], $controlTemplate);
    $controlTemplate = str_replace('[[src]]', $logoInfo['src'], $controlTemplate);
    echo $controlTemplate;
}
