<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('JPATH_BASE') or die();

use Joomla\CMS\Factory;
/**
 * Class NicepageInstallerEvents3
 */
class NicepageInstallerEvents3 extends NicepageInstallerEvents
{
    /**
     * On extension after install
     *
     * @param object $installer Installer object
     * @param int    $eid       Id
     */
    public function onExtensionAfterInstall($installer, $eid)
    {
        $lang = Factory::getLanguage();
        $lang->load('install_override', dirname(__FILE__), $lang->getTag(), true);
        $this->toplevel_installer->set('extension_message', $this->getMessages());
    }

    /**
     * On extension after update
     *
     * @param object $installer Installer object
     * @param int    $eid       Id
     */
    public function onExtensionAfterUpdate($installer, $eid)
    {
        $lang = Factory::getLanguage();
        $lang->load('install_override', dirname(__FILE__), $lang->getTag(), true);
        $this->toplevel_installer->set('extension_message', $this->getMessages());
    }

    /**
     * Get messages html content
     *
     * @return string
     */
    protected function getMessages()
    {
        $buffer = '<div id="nicepageinstall"><ul id="nicepageinstall-status">' . implode('', self::$messages) . '</ul></div>';

        if (file_exists(JPATH_ROOT . '/tmp/install.html')) {
            return file_get_contents(JPATH_ROOT . '/tmp/install.html') . $buffer;
        }

        return $buffer;
    }
}
