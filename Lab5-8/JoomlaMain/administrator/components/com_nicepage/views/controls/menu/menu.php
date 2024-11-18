<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Layout\LayoutHelper;

if (isset($controlProps)) {
    $doc = Factory::getDocument();
    if (strpos($controlProps['position'], 'hmenu-') !== false && !$doc->countModules($controlProps['position'])) {
        $controlProps['position'] = 'hmenu';
    }
    $menuContent = '';
    foreach (ModuleHelper::getModules($controlProps['position']) as $mod) {
        if ($mod->module !== 'mod_menu') {
            continue;
        }
        $displayData = array(
            'module' => $mod,
            'params' => new Registry($mod->params),
            'attribs' => $controlProps['attr'],
        );
        $menuContent .= LayoutHelper::render('chromes.menufromplugin', $displayData, JPATH_ADMINISTRATOR . '/components/com_nicepage/layouts');
    }
    echo $menuContent;
}