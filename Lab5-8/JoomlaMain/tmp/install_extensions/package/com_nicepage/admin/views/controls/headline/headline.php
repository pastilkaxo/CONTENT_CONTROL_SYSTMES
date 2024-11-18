<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use NP\Utility\Theme;

if (isset($controlProps) && isset($controlTemplate)) {
    $siteTitle = Theme::getThemeParams('siteTitle');
    $content = '';
    if ($siteTitle) {
        $content = $siteTitle;
    } else {
        $content = $controlProps['content'];
    }
    $controlTemplate = str_replace('[[content]]', $content, $controlTemplate);
    $controlTemplate = str_replace('[[url]]', Uri::root() . 'index.php', $controlTemplate);
    echo $controlTemplate;
}
