<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\AdministratorApplication;

if (!class_exists('PlgextensioninstallerInstallerScript')) {
    /**
     * Class PlgextensioninstallerInstallerScript
     */
    class PlgextensioninstallerInstallerScript
    {
        /**
         * @var array
         */
        protected $versions = array(
            'PHP' => array (
                '7.2' => '7.2.5',
                '0' => '7.2.5' // Preferred version
            ),
            'Joomla!' => array (
                '4.0' => '4.0.0',
                '0' => '4.0.2' // Preferred version
            )
        );

        /**
         * @var array
         */
        protected $packages = array();

        /**
         * @var
         */
        protected $sourcedir;

        /**
         * @var
         */
        protected $installerdir;

        /**
         * @var
         */
        protected $manifest;

        /**
         * @var
         */
        protected $parent;

        /**
         * @param object $parent Prent object
         *
         * @return bool
         */
        public function install($parent)
        {
            $this->cleanErrors();

            jimport('joomla.filesystem.file');
            jimport('joomla.filesystem.folder');
            jimport('joomla.installer.helper');

            if (!class_exists('NicepageInstaller')) {
                include_once $this->installerdir . '/NicepageInstaller.php';
            }

            $retval = true;
            ob_get_clean();

            // Cycle through items and install each
            if (count($this->manifest->items->children())) {
                foreach ($this->manifest->items->children() as $item) {
                    $folder = $this->sourcedir . '/' . $item->dirname;

                    if (is_dir($folder)) {
                        // if its actually a directory then fill it up
                        $type = (string) $item['type'];
                        $package                = Array();
                        $package['dir']         = $folder;
                        $package['type']        = InstallerHelper::detectType($folder);
                        $package['installer']   = new NicepageInstaller();
                        $package['name']        = (string) $item->name;
                        $package['state']       = 'Success';
                        $package['description'] = (string) $item->description;
                        $package['msg']         = '';
                        $package['type']        = ucfirst($type);

                        $package['installer']->setItemInfo($item);

                        // add installer to static for possible rollback.
                        $this->packages[] = $package;

                        $package['installer']->setAdapter($type);
                        if (!@$package['installer']->install($package['dir'])) {
                            $messages = Factory::getApplication()->getMessageQueue(true);
                            if ($messages && is_array($messages) && count($messages) > 0) {
                                $package['msg'] .= $messages[0]['message'];
                            }

                            NicepageInstallerEvents::addMessage($package, NicepageInstallerEvents::STATUS_ERROR, $package['msg']);
                            break;
                        }

                        if ($package['installer']->getInstallType() == 'install') {
                            NicepageInstallerEvents::addMessage($package, NicepageInstallerEvents::STATUS_INSTALLED);
                        } else {
                            NicepageInstallerEvents::addMessage($package, NicepageInstallerEvents::STATUS_UPDATED);
                        }
                    } else {
                        $package                = Array();
                        $package['dir']         = $folder;
                        $package['name']        = (string) $item->name;
                        $package['state']       = 'Failed';
                        $package['description'] = (string) $item->description;
                        $package['msg']         = '';
                        $package['type']        = ucfirst((string) $item['type']);

                        NicepageInstallerEvents::addMessage(
                            $package,
                            NicepageInstallerEvents::STATUS_ERROR, Text::_('JLIB_INSTALLER_ABORT_NOINSTALLPATH')
                        );
                        break;
                    }
                }
            } else {
                $parent->getParent()->abort(
                    Text::sprintf(
                        'JLIB_INSTALLER_ABORT_PACK_INSTALL_NO_FILES',
                        Text::_('JLIB_INSTALLER_' . strtoupper($this->route))
                    )
                );
            }
            return $retval;
        }

        /**
         * @param object $parent Parent object
         *
         * @return bool
         */
        public function update($parent)
        {
            return $this->install($parent);
        }

        /**
         * @param string $type   Type extension
         * @param object $parent Parent object
         *
         * @return bool
         */
        public function preflight($type, $parent)
        {
            $this->setup($parent);

            //Load Event Handler.
            if (!class_exists('NicepageInstallerEvents')) {
                include_once $this->installerdir . '/NicepageInstallerEvents.php';
                if (version_compare(JVERSION, '4.0.0', '<')) {
                    include_once $this->installerdir . '/NicepageInstallerEvents3.php';
                    $dispatcher = JDispatcher::getInstance();
                    $plugin = new NicepageInstallerEvents3($dispatcher);
                } else {
                    $dispatcher = Factory::getApplication()->getDispatcher();
                    $plugin = new NicepageInstallerEvents($dispatcher);
                }
                $plugin->setTopInstaller($this->parent->getParent());
            }

            // Check installer requirements.
            if (($requirements = $this->checkRequirements()) !== true) {
                NicepageInstallerEvents::addMessage(
                    array('name' => ''),
                    NicepageInstallerEvents::STATUS_ERROR,
                    implode('<br />', $requirements)
                );
                return false;
            }
            if (($result = $this->checkEditorExtensions()) !== false) {
                NicepageInstallerEvents::addMessage(
                    array('name' => ''),
                    NicepageInstallerEvents::STATUS_ERROR,
                    implode('<br />', $result)
                );
                return false;
            }
        }

        /**
         * @return array|bool
         */
        protected function checkEditorExtensions()
        {
            $errors = array();
            $currentPath = dirname(__FILE__);
            $rootPath = dirname(JPATH_PLUGINS);

            $compFolders = Folder::folders($rootPath . '/administrator/components', 'com_');

            $findSameEditorFolder = '';
            $findSameEditorName = '';
            foreach ($compFolders as $folder) {
                if (file_exists($rootPath . '/administrator/components/' . $folder . '/assets/app/app.js')) {
                    $findSameEditorFolder = $folder;
                    $folderParts = explode('_', $folder);
                    $findSameEditorName = $folderParts[1];
                }
            }

            if (!$findSameEditorFolder) {
                return false;
            }

            if (!file_exists($currentPath . '/installer.xml')
                || ($content = file_get_contents($currentPath . '/installer.xml')) === false
                || !preg_match('/\<dirname\>(com\_([\s\S]+?))\<\/dirname\>/', $content, $matches)
            ) {
                return false;
            }

            $findCurrentEditorFolder = $matches[1];
            $findCurrentEditorName = $matches[2];

            $extensions = array(
                '1' =>
                    array(
                        'type' => 'component',
                        'installed_path' => $rootPath . '/administrator/components/' . $findSameEditorFolder .'/' . $findSameEditorName .'.php',
                        'installed_lang' => $rootPath . '/administrator/components/' . $findSameEditorFolder . '/languages/en-GB/en-GB.' . $findSameEditorFolder . '.sys.ini',
                        'search_installed_name' => 'COM_' . strtoupper($findSameEditorName),
                        'current_lang' => $currentPath . '/' . $findCurrentEditorFolder .'/admin/languages/en-GB/en-GB.' . $findCurrentEditorFolder . '.sys.ini',
                        'search_current_name' => 'COM_' . strtoupper($findCurrentEditorName)
                    ),
                '2' =>
                    array(
                        'type' => 'plugin',
                        'installed_path' => $rootPath . '/plugins/content/' . $findSameEditorName. '/' . $findSameEditorName . '.php',
                        'installed_lang' => $rootPath . '/administrator/language/en-GB/en-GB.plg_content_' . $findSameEditorName .'.sys.ini',
                        'search_installed_name' => 'PLG_CONTENT_' . strtoupper($findSameEditorName),
                        'current_lang' => $currentPath . '/plg_content_' . $findCurrentEditorName . '/language/en-GB/en-GB.plg_content_' . $findCurrentEditorName . '.sys.ini',
                        'search_current_name' => 'PLG_CONTENT_' . strtoupper($findCurrentEditorName)
                    ),
                '3' =>
                    array(
                        'type' => 'plugin',
                        'installed_path' => $rootPath . '/plugins/system/' . $findSameEditorName. '/' . $findSameEditorName . '.php',
                        'installed_lang' => $rootPath . '/administrator/language/en-GB/en-GB.plg_system_' . $findSameEditorName .'.sys.ini',
                        'search_installed_name' => 'PLG_SYSTEM_' . strtoupper($findSameEditorName),
                        'current_lang' => $currentPath . '/plg_system_' . $findCurrentEditorName . '/language/en-GB/en-GB.plg_system_' . $findCurrentEditorName . '.sys.ini',
                        'search_current_name' => 'PLG_SYSTEM_' . strtoupper($findCurrentEditorName)
                    ),
                '4' =>
                    array(
                        'type' => 'plugin',
                        'installed_path' => $rootPath . '/plugins/editors-xtd/' . $findSameEditorName . '/' . $findSameEditorName . '.php',
                        'installed_lang' => $rootPath . '/administrator/language/en-GB/en-GB.plg_editors-xtd_' . $findSameEditorName .'.sys.ini',
                        'search_installed_name' => 'PLG_EDITORS-XTD_' . strtoupper($findSameEditorName),
                        'current_lang' => $currentPath . '/plg_editors-xtd_' . $findCurrentEditorName . '/language/en-GB/en-GB.plg_editors-xtd_' . $findCurrentEditorName . '.sys.ini',
                        'search_current_name' => 'PLG_EDITORS-XTD_' . strtoupper($findCurrentEditorName)
                    )
            );

            foreach ($extensions as $ext) {
                if (!file_exists($ext['installed_path'])
                    || !file_exists($ext['installed_lang'])
                    || ($lgContent1 = file_get_contents($ext['installed_lang'])) === false
                    || ($lgContent2 = file_get_contents($ext['current_lang'])) === false
                ) {
                    continue;
                }
                $installedName = preg_match('/' . $ext['search_installed_name'] .'="([^"]+)"/', $lgContent1, $matches1)  ? $matches1[1] : '';
                $currentName = preg_match('/' . $ext['search_current_name'] .'="([^"]+)"/', $lgContent2, $matches2) ? $matches2[1] : '';
                if ($installedName !== $currentName) {
                    $errors[] = sprintf(
                        '<span class="extension-error">Please uninstall <span class="ext-name">%s</span> %s before installing <span class="ext-name">%s</span> %s</span>',
                        $installedName,
                        $ext['type'],
                        $currentName,
                        $ext['type']
                    );
                }
            }
            if (count($errors) > 0) {
                $manageUrl = dirname(Uri::current()) . '/index.php?option=com_installer&view=manage';
                $errors[] = sprintf('<a class="manage-link" href="' . $manageUrl . '" target="_blank">Go to manage board</a>');
            }
            return count($errors) > 0 ? $errors : false;
        }

        /**
         * @param string $type   Type extension
         * @param object $parent Parent object
         */
        public function postflight($type, $parent)
        {
            $conf = Factory::getConfig();
            $conf->set('debug', false);
            $parent->getParent()->abort();
        }

        /**
         * @param null $msg  Text message
         * @param null $type Type extension
         */
        public function abort($msg = null, $type = null)
        {
            if ($msg) {
                Factory::getApplication()->enqueueMessage($msg, 'error');
            }
            foreach ($this->packages as $package) {
                $package['installer']->abort(null, $type);
            }
        }

        /**
         * @param object $parent Parent object
         */
        protected function setup($parent)
        {
            $this->parent       = $parent;
            $this->sourcedir    = $parent->getParent()->getPath('source');
            $this->manifest     = $parent->getParent()->getManifest();
            $this->installerdir = $this->sourcedir . '/installer';
        }

        /**
         * @return array|bool
         */
        protected function checkRequirements()
        {
            $errors = array();

            if (($error = $this->checkVersion('PHP', phpversion())) !== true) {
                $errors[] = $error;
            }

            if (($error = $this->checkVersion('Joomla!', JVERSION)) !== true) {
                $errors[] = $error;
            }

            return $errors ? $errors : true;
        }

        /**
         * @param string $name    Extension name
         * @param string $version Extension version
         *
         * @return bool|string
         */
        protected function checkVersion($name, $version)
        {
            $major = $minor = 0;
            foreach ($this->versions[$name] as $major => $minor) {
                if (!$major || version_compare($version, $major, '<')) {
                    continue;
                }

                if (version_compare($version, $minor, '>=')) {
                    return true;
                }
                break;
            }

            if (!$major) {
                $minor = reset($this->versions[$name]);
            }

            $recommended = end($this->versions[$name]);

            if (version_compare($recommended, $minor, '>')) {
                return sprintf(
                    '%s %s is not supported. Minimum required version is %s %s, but it is highly recommended to use %s %s or later version.',
                    $name,
                    $version,
                    $name,
                    $minor,
                    $name,
                    $recommended
                );
            } else {
                return sprintf(
                    '%s %s is not supported. Please update to %s %s or later version.',
                    $name,
                    $version,
                    $name,
                    $minor
                );
            }
        }

        /**
         * Clean errors
         */
        protected function cleanErrors()
        {
            $app               = new NicepageInstallerJAdministratorWrapper(Factory::getApplication());
            $enqueued_messages = $app->getMessageQueue();
            $other_messages    = array();

            if (!empty($enqueued_messages) && is_array($enqueued_messages)) {
                foreach ($enqueued_messages as $enqueued_message) {
                    if (!($enqueued_message['message'] == Text::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE') && $enqueued_message['type']) == 'error') {
                        $other_messages[] = $enqueued_message;
                    }
                }
            }
            $app->setMessageQueue($other_messages);
        }
    }

    if (!class_exists('NicepageInstallerJAdministratorWrapper')) {
        /**
         * Class NicepageInstallerJAdministratorWrapper
         */
        class NicepageInstallerJAdministratorWrapper extends AdministratorApplication
        {
            /**
             * @var CMSApplication
             */
            protected $app;

            /**
             * NicepageInstallerJAdministratorWrapper constructor.
             *
             * @param CMSApplication $app Application object
             */
            public function __construct(CMSApplication $app)
            {
                $this->app = $app;
            }

            /**
             * @param bool $clear Clear variable
             *
             * @return mixed
             */
            public function getMessageQueue($clear = false)
            {
                return $this->app->getMessageQueue();
            }

            /**
             * @param array $messages Messages list
             */
            public function setMessageQueue($messages)
            {
                $this->app->_messageQueue = $messages;
            }
        }
    }
}
