<?php
/**
 * @package   ShackExtensionSupport
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2023 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of ShackExtensionSupport.
 *
 * ShackExtensionSupport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ShackExtensionSupport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ShackExtensionSupport.  If not, see <http://www.gnu.org/licenses/>.
 */

use Alledia\Framework\AutoLoader;
use Joomla\CMS\Factory;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

if (defined('ALLEDIA_FRAMEWORK_LOADED') == false) {
    $frameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';

    if (
        (is_file($frameworkPath) && include $frameworkPath) == false
            && Factory::getApplication()->isClient('administrator')
    ) {
        Factory::getApplication()
            ->enqueueMessage('[Joomlashack Extension Support] Joomlashack Framework not found', 'error');
    }
}

if (
    defined('ALLEDIA_FRAMEWORK_LOADED')
    && defined('SHACKEXTENSIONSUPPORT_LOADED') == false
) {
    AutoLoader::register('\\Alledia\\OSMyLicensesManager', JPATH_PLUGINS . '/system/osmylicensesmanager/library');

    define('SHACKEXTENSIONSUPPORT_LOADED', 1);
}

return defined('ALLEDIA_FRAMEWORK_LOADED') && defined('SHACKEXTENSIONSUPPORT_LOADED');
