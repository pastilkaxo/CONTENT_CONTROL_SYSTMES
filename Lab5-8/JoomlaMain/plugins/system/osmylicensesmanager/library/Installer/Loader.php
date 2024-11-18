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

namespace Alledia\Installer;

use Alledia\Framework\Factory;
use Joomla\CMS\Log\Log;

defined('_JEXEC') or die();

abstract class Loader
{
    protected static $logRegistered = false;

    /**
     * Safely include a PHP file, making sure it exists before import.
     *
     * This method will register a log message and display a warning for admins
     * in case the file is missed.
     *
     * @param string $path The file path you want to include
     *
     * @return  bool
     * @throws \Exception
     */
    public static function includeFile(string $path): bool
    {
        if (!static::$logRegistered) {
            Log::addLogger(
                ['text_file' => 'shackframework.loader.errors.php'],
                Log::ALL,
                ['allediaframework']
            );

            static::$logRegistered = true;
        }

        // Check if the file doesn't exist
        if (!is_file($path)) {
            $logMsg = 'Required file is missed: ' . $path;

            // Generate a backtrace to know from where the request cames
            if (version_compare(phpversion(), '5.4', '<')) {
                $backtrace = debug_backtrace();
            } else {
                $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            }

            if (!empty($backtrace)) {
                $logMsg .= sprintf(
                    ' (%s:%s)',
                    $backtrace[0]['file'],
                    $backtrace[0]['line']
                );
            }

            // Register the log
            Log::add($logMsg, Log::ERROR, 'allediaframework');

            // Warn admin users
            $app = Factory::getApplication();
            if ($app->getName() == 'administrator') {
                $app->enqueueMessage(
                    'ShackInstaller Loader detected that a required file was not found! Please, check the logs.',
                    'error'
                );
            }

            // Stand up a flag to warn a required file is missed
            if (!defined('SHACK_INSTALLER_MISSED_FILE')) {
                define('SHACK_INSTALLER_MISSED_FILE', true);
            }

            return false;
        }

        include_once $path;

        return true;
    }
}
