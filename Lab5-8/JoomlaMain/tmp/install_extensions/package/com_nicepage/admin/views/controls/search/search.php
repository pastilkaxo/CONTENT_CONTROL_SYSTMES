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
use Joomla\CMS\Router\Route;

if (isset($controlProps) && isset($controlTemplate)) {
    $controlTemplate = str_replace('[[action]]', Route::_('index.php?option=com_finder&view=search'), $controlTemplate);

    Factory::getApplication()->getLanguage()->load('mod_finder', JPATH_SITE);
    $placeholder = htmlspecialchars(Text::_('MOD_FINDER_SEARCHBUTTON_TEXT'), ENT_COMPAT, 'UTF-8');
    $controlTemplate = str_replace('[[placeholder]]', $placeholder, $controlTemplate);

    echo $controlTemplate;
}