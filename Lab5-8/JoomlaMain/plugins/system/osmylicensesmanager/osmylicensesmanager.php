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

use Alledia\Framework\Joomla\Extension\AbstractPlugin;
use Alledia\Framework\Joomla\Extension\Helper;
use Alledia\OSMyLicensesManager\Free\PluginHelper;
use Alledia\OSMyLicensesManager\PluginBase;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

if ((include 'include.php') == false) {
    class_alias(CMSPlugin::class, PluginBase::class);
} else {
    class_alias(AbstractPlugin::class, PluginBase::class);
}

// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class PlgSystemOSMyLicensesManager extends PluginBase
{
    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @inheritdoc
     */
    protected $namespace = 'OSMyLicensesManager';

    /**
     * @var bool
     */
    protected $enabled = null;

    /**
     * @return void
     */
    public function onAfterInitialise()
    {

        $plugin = $this->app->input->getCmd('plugin');
        $task   = $this->app->input->getCmd('task');
        $user   = Factory::getUser();

        if (
            $this->isEnabled()
            && $this->app->isClient('administrator')
            && $plugin == 'system_osmylicensesmanager'
            && $task == 'license.save'
            && $user->guest == false
        ) {
            // The user is saving a license key from the installer screen
            $this->init();

            $licenseKeys = $this->app->input->post->getString('license-keys', '');

            $result = (object)[
                'success' => PluginHelper::updateLicenseKeys($licenseKeys),
            ];

            echo json_encode($result);

            jexit();
        }
    }

    /**
     * @return void
     */
    public function onAfterRender()
    {
        $option    = $this->app->input->getCmd('option');
        $extension = $this->app->input->getCmd('extension');

        if (
            $this->isEnabled()
            && $this->app->isClient('administrator')
            && $option === 'com_categories'
            && $extension
        ) {
            $this->addCustomFooterToCategories($extension);
        }
    }

    /**
     * @param string $url
     *
     * @return void
     */
    public function onInstallerBeforePackageDownload(string &$url)
    {
        if (
            $this->isEnabled()
            && PluginHelper::isOurDownloadURL($url)
            && PluginHelper::getLicenseTypeFromURL($url) !== 'free'
        ) {
            $this->init();

            // Append the license keys to the URL
            $licenseKeys = $this->params->get('license-keys', '');
            $url         = PluginHelper::appendLicenseKeyToURL($url, $licenseKeys);
        }
    }

    /**
     * @param ?string $element
     *
     * @return void
     */
    protected function addCustomFooterToCategories(?string $element)
    {
        if ($this->isEnabled() && $element) {
            // Check if the specified extension is from Alledia
            if ($extension = Helper::getExtensionForElement($element)) {
                if ($footer = $extension->getFooterMarkup()) {
                    $this->app->setBody(
                        str_replace('</section>', '</section>' . $footer, $this->app->getBody())
                    );
                }
            }
        }
    }

    /**
     * @return bool
     */
    protected function isEnabled(): bool
    {
        if ($this->enabled === null) {
            $this->enabled = $this instanceof AbstractPlugin;
        }

        return $this->enabled;
    }
}
