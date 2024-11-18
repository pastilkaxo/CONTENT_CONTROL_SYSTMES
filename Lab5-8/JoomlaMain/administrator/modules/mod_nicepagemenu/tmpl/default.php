<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// Load translations
$lang = Factory::getLanguage()->load('com_nicepage.sys', JPATH_ADMINISTRATOR);

if ($nicepageComponentItems && !empty($nicepageComponentItems->submenu)) {
    $dashboardHtml =<<<HTML
<span class="menu-dashboard">
    <a href="#">
        <span class="icon-th-large" title="Nicepage Dashboard" aria-hidden="true"></span>
        <span class="visually-hidden">Nicepage Dashboard</span>
    </a>
</span>
HTML;

    $nicepageMenu = '<div id="np-menu-container" style="display:none"><ul id="np-menu" class="nav flex-column main-nav" >';
    $nicepageMenu .= '<li class="item parent item-level-1" ><a class="has-arrow" data-toggle="dropdown" href="#">';
    $nicepageMenu .= '<span class="np-icon">&nbsp;</span><span class="sidebar-item-title">' . Text::_($nicepageComponentItems->title) . '</strong>';
    $nicepageMenu .= '</a>' . $dashboardHtml;
    $nicepageMenu .= '<ul class="collapse-level-1 mm-collapse">';
    $ulIterator = 2;
    $liIterator = 2;
    foreach ($nicepageComponentItems->submenu as $sub) {
        $liClass = 'item item-level-' . $liIterator;
        $title = '<span class="sidebar-item-title">' . Text::_($sub->title) . '</span>';
        if (strpos($sub->link, 'view=theme') !== false) {
            if (strpos($sub->link, 'element=') !== false) {
                if (strpos($sub->link, 'element=Header') !== false) {
                    $nicepageMenu .= '<li class="divider"><span></span></li>';
                }
                $nicepageMenu .= '<li class="' . $liClass . '"><a class="' . $sub->class . '" href="' . $sub->link . '">' . $title . '</a></li>';
            } else {
                $nicepageMenu .= '<li class="parent ' . $liClass . '"><a class="has-arrow ' . $sub->class . '" href="' . $sub->link . '">' . $title . '</a>' . $dashboardHtml;
                $nicepageMenu .= '<ul class="collapse-level-' . $ulIterator . ' mm-collapse">';
                $ulIterator++;
                $liIterator++;
            }
        } else {
            $nicepageMenu .= '<li class="' . $liClass . '"><a class="' . $sub->class . '" href="' . $sub->link . '">' . $title . '</a></li>';
        }
    }
    $nicepageMenu .= '</ul></li>'; // close Theme menu
    $nicepageMenu .= '</ul>';
    $nicepageMenu .= '</li></ul></div>';
    echo $nicepageMenu;
    $nicepageIcon = Uri::getInstance(Uri::root())->toString() . '/components/com_nicepage/assets/images/button-icon.png?r=' . md5(mt_rand(1, 100000));
    ?>
    <style>
        .np-icon {
            background: url('<?php echo $nicepageIcon; ?>') no-repeat;
            float: left;
            width: 18px;
            height: 18px;
            background-size: 18px;
            margin: 0 0.85rem;
        }
    </style>
    <script>
        var npMenuItem = document.querySelector('#np-menu>li'),
            mainMenuItems = document.querySelectorAll('.main-nav-container>ul>li'),
            contentMenuItem = mainMenuItems && mainMenuItems[1];
        if (contentMenuItem && npMenuItem) {
            contentMenuItem.parentNode.insertBefore(npMenuItem, contentMenuItem.nextSibling);
            var npMenuContainer = document.getElementById("np-menu-container");
            npMenuContainer.parentNode.removeChild(npMenuContainer);
        }
    </script>
    <?php
}