<?php
/**
 * @package   ShackInstaller
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2023 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of ShackInstaller.
 *
 * ShackInstaller is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ShackInstaller is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ShackInstaller.  If not, see <https://www.gnu.org/licenses/>.
 */

use Alledia\Installer\AutoLoader;
use Joomla\CMS\Version;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

if (defined('SHACK_INSTALLER_BASE') == false) {
    define('SHACK_INSTALLER_BASE', __DIR__);

    require_once SHACK_INSTALLER_BASE . '/AutoLoader.php';
}

AutoLoader::register('Alledia\\Installer', __DIR__, true);

if (defined('SHACK_INSTALLER_VERSION') == false) {
    define('SHACK_INSTALLER_VERSION', '2.4.4');
    define('SHACK_INSTALLER_COMPATIBLE', '2.4.0');

    if (isset($reportErrors) == false) {
        $reportErrors = E_ALL ^ E_DEPRECATED ^ E_USER_DEPRECATED;
        if (Version::MAJOR_VERSION == 4) {
            // There is a bad line of code in Joomla 4 that runs during extension install/update
            $reportErrors = $reportErrors ^ E_NOTICE;
        }
    }

    if ($reportErrors) {
        set_error_handler('\\Alledia\\Installer\\AbstractScript::errorHandler', $reportErrors);
    }
}
