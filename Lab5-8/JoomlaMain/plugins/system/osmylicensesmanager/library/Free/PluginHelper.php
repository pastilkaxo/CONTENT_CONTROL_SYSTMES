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

namespace Alledia\OSMyLicensesManager\Free;

use Alledia\Framework\Joomla\Extension\Licensed;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects

/**
 * PluginHelper class
 */
abstract class PluginHelper
{
    /**
     * @var string
     */
    protected static $downloadBaseURL = 'https://deploy.ostraining.com/client/download/';

    /**
     * Update the license key on the plugin params
     *
     * @param ?string $licenseKeys
     *
     * @return bool
     */
    public static function updateLicenseKeys(?string $licenseKeys = ''): bool
    {
        $licenseKeys = PluginHelper::sanitizeKey($licenseKeys);

        // Update the extension params
        $extension = new Licensed('osmylicensesmanager', 'plugin', 'system');
        $extension->params->set('license-keys', $licenseKeys);
        $extension->storeParams();

        return true;
    }

    /**
     * Detects if the passed URL is our download URL, returning a boolean value.
     *
     * @param string $url
     *
     * @return bool
     */
    public static function isOurDownloadURL(string $url): bool
    {
        return preg_match('#^' . static::$downloadBaseURL . '#', $url) === 1;
    }

    /**
     * Removes the license key from the URL and returns it.
     *
     * @param string $url
     *
     * @return string
     */
    public static function getURLWithoutLicenseKey(string $url): string
    {
        if (static::isOurDownloadURL($url)) {
            $url = preg_replace('#^(' . static::$downloadBaseURL . '(free|pro|paid)/[^/]+/[^/]+).*$#i', '$1', $url);
            $url .= '/';
        }

        return $url;
    }

    /**
     * Sanitizes the license key, making sure we have only valid chars.
     *
     * @param string $key
     *
     * @return string
     */
    public static function sanitizeKey(string $key): string
    {
        return preg_replace('/[^a-z0-9,]/i', '', $key);
    }

    /**
     * Appends the license key to the URL and returns it. We recognize the url
     * for designated generic extensions using a default license key to allow
     * legacy customers to download updates.
     *
     * @param string $url
     * @param string $keys
     *
     * @return string
     */
    public static function appendLicenseKeyToURL(string $url, string $keys): string
    {
        if (static::isOurDownloadURL($url)) {
            $url = PluginHelper::getURLWithoutLicenseKey($url);

            $sanitizedKeys = static::sanitizeKey($keys);

            if ($keys) {
                $encodedKeys = base64_encode($sanitizedKeys);
                $url         .= $encodedKeys;
            }
        }

        return $url;
    }

    /**
     * Detects the license type based on the URL. If no license is detected,
     * returns false
     *
     * @param string $url
     *
     * @return ?string
     */
    public static function getLicenseTypeFromURL(string $url): ?string
    {
        if (preg_match('#^' . static::$downloadBaseURL . '(free|pro)/#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
