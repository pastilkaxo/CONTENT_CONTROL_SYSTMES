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

use Alledia\Installer\Extension\Licensed;
use JEventDispatcher;
use JFormFieldCustomFooter;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Extension;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;
use Joomla\Component\Plugins\Administrator\Model\PluginModel;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;
use Joomla\Registry\Registry;
use SimpleXMLElement;
use Throwable;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

require_once 'include.php';

// phpcs:enable PSR1.Files.SideEffects

abstract class AbstractScript
{
    public const VERSION = '2.4.4';

    /**
     * Recognized installation types
     */
    protected const TYPE_INSTALL          = 'install';
    protected const TYPE_DISCOVER_INSTALL = 'discover_install';
    protected const TYPE_UPDATE           = 'update';
    protected const TYPE_UNINSTALL        = 'uninstall';

    /**
     * @var bool
     */
    protected $outputAllowed = true;

    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @var DatabaseDriver
     */
    protected $dbo = null;

    /**
     * @var string
     */
    protected $schemaVersion = null;

    /**
     * @var JEventDispatcher|DispatcherInterface
     */
    protected $dispatcher = null;

    /**
     * @var Installer
     */
    protected $installer = null;

    /**
     * @var SimpleXMLElement
     */
    protected $manifest = null;

    /**
     * @var SimpleXMLElement
     */
    protected $previousManifest = null;

    /**
     * @var string
     */
    protected $mediaFolder = null;

    /**
     * @var string
     */
    protected $element = null;

    /**
     * @var string[]
     */
    protected $systemExtensions = [
        'library..allediaframework',
        'plugin.system.osmylicensesmanager',
    ];

    /**
     * @var bool
     */
    protected $isLicensesManagerInstalled = false;

    /**
     * @var Licensed
     */
    protected $license = null;

    /**
     * @var string
     */
    protected $licenseKey = null;

    /**
     * @var string
     */
    protected $footer = null;

    /**
     * @var string
     */
    protected $mediaURL = null;

    /**
     * @var string[]
     * @deprecated v2.0.0
     */
    protected $messages = [];

    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $group = null;

    /**
     * List of tables and respective columns
     *
     * @var array
     */
    protected $columns = null;

    /**
     * List of tables and respective indexes
     *
     * @var array
     * @deprecated v2.1.0
     */
    protected $indexes = null;

    /**
     * @var object[]
     */
    protected $tableColumns = [];

    /**
     * @var object[]
     */
    protected $tableIndexes = [];

    /**
     * @var object[]
     */
    protected $tableConstraints = [];

    /**
     * @var array
     */
    protected $tables = null;

    /**
     * Flag to cancel the installation
     *
     * @var bool
     */
    protected $cancelInstallation = false;

    /**
     * Feedback of the install by related extension
     *
     * @var array
     */
    protected $relatedExtensionFeedback = [];

    /**
     * @var string
     */
    protected $welcomeMessage = null;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @param InstallerAdapter $parent
     *
     * @return void
     * @throws \Exception
     */
    public function __construct(InstallerAdapter $parent)
    {
        $this->sendDebugMessage('ShackInstaller v' . static::VERSION);
        $this->sendDebugMessage('Base v' . SHACK_INSTALLER_VERSION);
        $this->sendDebugMessage(__METHOD__);

        $this->initProperties($parent);
    }

    /**
     * @param InstallerAdapter $parent
     *
     * @return bool
     * @throws \Exception
     */
    protected function checkInheritance(InstallerAdapter $parent): bool
    {
        $parentClasses   = class_parents($this);
        $scriptClassName = array_pop($parentClasses);
        $scriptClass     = new \ReflectionClass($scriptClassName);

        $sourcePath    = dirname($scriptClass->getFileName());
        $sourceBase    = strpos($sourcePath, JPATH_PLUGINS) === 0 ? 3 : 2;
        $sourceVersion = AbstractScript::VERSION ?? '0.0.0';

        $sourcePath = $this->cleanPath($sourcePath);
        $targetPath = $this->cleanPath(SHACK_INSTALLER_BASE);

        if ($sourcePath != $targetPath && version_compare($sourceVersion, SHACK_INSTALLER_COMPATIBLE, 'lt')) {
            $source = join('/', array_slice(explode('/', $sourcePath), 0, $sourceBase));

            $errorMessage = 'LIB_SHACKINSTALLER_ABORT_'
                . ($parent->getRoute() == 'uninstall' ? 'UNINSTALL' : 'INSTALL');

            Factory::getApplication()->enqueueMessage(Text::sprintf($errorMessage, $source), 'error');

            $this->cancelInstallation = true;

            return false;
        }

        return true;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function cleanPath(string $path): string
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', str_replace(JPATH_ROOT . '/', '', $path));
    }

    /**
     * @param InstallerAdapter $parent
     *
     * @return void
     * @throws \Exception
     */
    protected function initProperties(InstallerAdapter $parent): void
    {
        $this->sendDebugMessage(__METHOD__);

        $this->app           = Factory::getApplication();
        $this->outputAllowed = JPATH_BASE == JPATH_ADMINISTRATOR;

        $language = Factory::getLanguage();
        $language->load('lib_shackinstaller.sys', realpath(__DIR__ . '/../..'));

        if ($this->checkInheritance($parent) == false) {
            return;
        }

        try {
            $this->dbo           = Factory::getDbo();
            $this->installer     = $parent->getParent();
            $this->manifest      = $this->installer->getManifest();
            $this->schemaVersion = $this->getSchemaVersion();

            if ($media = $this->manifest->media) {
                $this->mediaFolder = JPATH_SITE . '/' . $media['folder'] . '/' . $media['destination'];
            }

            $attributes  = $this->manifest->attributes();
            $this->type  = (string)$attributes['type'];
            $this->group = (string)$attributes['group'];

            // Get the previous manifest for use in upgrades
            $targetPath   = $this->installer->getPath('extension_administrator')
                ?: $this->installer->getPath('extension_root');
            $manifestPath = $targetPath . '/' . basename($this->installer->getPath('manifest'));

            if (is_file($manifestPath)) {
                $this->previousManifest = simplexml_load_file($manifestPath);
            }

            // Determine basepath for localized files
            $basePath = $this->installer->getPath('source');
            if (is_dir($basePath)) {
                if ($this->type == 'component' && $basePath != $targetPath) {
                    // For components sourced by manifest, need to find the admin folder
                    if ($files = $this->manifest->administration->files) {
                        if ($files = (string)$files['folder']) {
                            $basePath .= '/' . $files;
                        }
                    }
                }

            } else {
                $basePath = $this->getExtensionPath(
                    $this->type,
                    (string)$this->manifest->alledia->element,
                    $this->group
                );
            }

            // All the files we want to load
            $languageFiles = [
                $this->getFullElement(),
            ];

            // Load from localized or core language folder
            foreach ($languageFiles as $languageFile) {
                $language->load($languageFile, $basePath) || $language->load($languageFile, JPATH_ADMINISTRATOR);
            }

        } catch (Throwable $error) {
            $this->cancelInstallation = true;
            $this->sendErrorMessage($error);
        }
    }

    /**
     * @return JEventDispatcher|DispatcherInterface
     */
    protected function getDispatcher()
    {
        if ($this->dispatcher === null) {
            if (Version::MAJOR_VERSION < 4) {
                $this->dispatcher = JEventDispatcher::getInstance();

            } else {
                $this->dispatcher = $this->app->getDispatcher();
            }
        }

        return $this->dispatcher;
    }

    /**
     * @param InstallerAdapter $parent
     *
     * @return bool
     */
    final public function install(InstallerAdapter $parent): bool
    {
        $this->sendDebugMessage(__METHOD__);

        try {
            return $this->customInstall($parent);

        } catch (Throwable $error) {
            $this->sendErrorMessage($error);
        }

        return false;
    }

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * @param InstallerAdapter $parent
     *
     * @return bool
     */
    final public function discover_install(InstallerAdapter $parent): bool
    {
        $this->sendDebugMessage(__METHOD__);

        try {
            if ($this->install($parent)) {
                return $this->customDiscoverInstall($parent);
            }

        } catch (Throwable $error) {
            $this->sendErrorMessage($error);
        }

        return false;
    }
    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * @param InstallerAdapter $parent
     *
     * @return bool
     */
    final public function update(InstallerAdapter $parent): bool
    {
        $this->sendDebugMessage(__METHOD__);

        try {
            return $this->customUpdate($parent);

        } catch (Throwable $error) {
            $this->sendErrorMessage($error);
        }

        return false;
    }

    /**
     * @param string           $type
     * @param InstallerAdapter $parent
     *
     * @return bool
     * @throws \Exception
     */
    final public function preFlight(string $type, InstallerAdapter $parent): bool
    {
        if ($this->cancelInstallation) {
            $this->sendDebugMessage('CANCEL: ' . __METHOD__);

            return false;
        }

        try {
            $this->sendDebugMessage(__METHOD__);
            $success = true;

            if ($type === 'update') {
                $this->clearUpdateServers();
            }

            if (in_array($type, [static::TYPE_INSTALL, static::TYPE_UPDATE])) {
                // Check minimum target Joomla Platform
                if (isset($this->manifest->alledia->targetplatform)) {
                    $targetPlatform = (string)$this->manifest->alledia->targetplatform;

                    if ($this->validateTargetVersion(JVERSION, $targetPlatform) == false) {
                        // Platform version is invalid. Displays a warning and cancel the install
                        $targetPlatform = str_replace('*', 'x', $targetPlatform);

                        $msg = Text::sprintf('LIB_SHACKINSTALLER_WRONG_PLATFORM', $this->getName(), $targetPlatform);

                        $this->sendMessage($msg, 'warning');
                        $success = false;
                    }
                }

                // Check for minimum mysql version
                if ($targetMySqlVersion = $this->manifest->alledia->mysqlminimum) {
                    $targetMySqlVersion = (string)$targetMySqlVersion;

                    if ($this->dbo->getServerType() == 'mysql') {
                        $dbVersion = $this->dbo->getVersion();
                        if (stripos($dbVersion, 'maria') !== false) {
                            // For MariaDB this is a bit of a punt. We'll assume any version of Maria will do
                            $dbVersion = $targetMySqlVersion;
                        }

                        if ($this->validateTargetVersion($dbVersion, $targetMySqlVersion) == false) {
                            // mySQL version too low
                            $minimumMySql = str_replace('*', 'x', $targetMySqlVersion);

                            $msg = Text::sprintf('LIB_SHACKINSTALLER_WRONG_MYSQL', $this->getName(), $minimumMySql);
                            $this->sendMessage($msg, 'warning');
                            $success = false;
                        }
                    }
                }

                // Check for minimum php version
                if (isset($this->manifest->alledia->phpminimum)) {
                    $targetPhpVersion = (string)$this->manifest->alledia->phpminimum;

                    if ($this->validateTargetVersion(phpversion(), $targetPhpVersion) == false) {
                        // php version is too low
                        $minimumPhp = str_replace('*', 'x', $targetPhpVersion);

                        $msg = Text::sprintf('LIB_SHACKINSTALLER_WRONG_PHP', $this->getName(), $minimumPhp);
                        $this->sendMessage($msg, 'warning');
                        $success = false;
                    }
                }

                // Check for minimum previous version
                $targetVersion = (string)$this->manifest->alledia->previousminimum;
                if ($type == static::TYPE_UPDATE && $targetVersion) {
                    if (!$this->validatePreviousVersion($targetVersion)) {
                        // Previous minimum is not installed
                        $minimumVersion = str_replace('*', 'x', $targetVersion);

                        $msg = Text::sprintf('LIB_SHACKINSTALLER_WRONG_PREVIOUS', $this->getName(), $minimumVersion);
                        $this->sendMessage($msg, 'warning');
                        $success = false;
                    }
                }
            }

            if ($success) {
                $success = $this->customPreFlight($type, $parent);
            }

            if ($success) {
                if (
                    $type !== static::TYPE_UNINSTALL
                    && empty($this->manifest->alledia->obsolete->preflight) == false
                ) {
                    $this->clearObsolete($this->manifest->alledia->obsolete->preflight);
                }
            }

            $this->cancelInstallation = $success == false;

            return $success;

        } catch (Throwable $error) {
            $this->sendErrorMessage($error);
        }

        return false;
    }

    /**
     * @param string           $type
     * @param InstallerAdapter $parent
     *
     * @return void
     * @throws \Exception
     */
    final public function postFlight(string $type, InstallerAdapter $parent): void
    {
        $this->sendDebugMessage(__METHOD__);

        if ($this->cancelInstallation) {
            $this->sendMessage('LIB_SHACKINSTALLER_INSTALL_CANCELLED', 'warning');

            return;
        }

        try {
            /*
             * Joomla 4 now calls postFlight on uninstalls. Which is kinda cool actually.
             * But this code is problematic in that scenario
             */
            if ($type != static::TYPE_UNINSTALL) {
                $this->clearObsolete();
                $this->installRelated();
                $this->addAllediaAuthorshipToExtension();

                $this->element = (string)$this->manifest->alledia->element;

                // Check and publish/reorder the plugin, if required
                if (
                    $this->type === 'plugin'
                    && in_array($type, [static::TYPE_INSTALL, static::TYPE_DISCOVER_INSTALL])
                ) {
                    $this->publishThisPlugin();
                    $this->reorderThisPlugin();
                }

                // If Free, remove any Pro library
                $license = $this->getLicense();
                if (!$license->isPro()) {
                    $proLibraryPath = $license->getProLibraryPath();
                    if (is_dir($proLibraryPath)) {
                        Folder::delete($proLibraryPath);
                    }
                }
            }

            $this->customPostFlight($type, $parent);

            if ($type != static::TYPE_UNINSTALL) {
                $this->displayWelcome($type);
            }

        } catch (Throwable $error) {
            $this->sendErrorMessage($error);
        }
    }

    /**
     * @param InstallerAdapter $parent
     *
     * @return void
     * @throws \Exception
     */
    final public function uninstall(InstallerAdapter $parent): void
    {
        $this->sendDebugMessage(__METHOD__);

        try {
            $this->uninstallRelated();
            $this->customUninstall($parent);

        } catch (Throwable $error) {
            $this->sendErrorMessage($error);
        }
    }

    /**
     * @param int     $number
     * @param string  $error
     * @param ?string $file
     * @param ?int    $line
     *
     * @return void
     */
    public static function errorHandler(int $number, string $error, ?string $file = null, ?int $line = null): void
    {
        try {
            $codes = get_defined_constants(true);
            $codes = $codes['Core'];

            $codes = array_filter(
                $codes,
                function ($key) {
                    return strpos($key, 'E_') === 0;
                },
                ARRAY_FILTER_USE_KEY
            );

            $name = array_search($number, $codes);

            Factory::getApplication()->enqueueMessage(
                sprintf('%s: %s<br>(%s) %s', $name, $error, $line ?: 'NA', $file ?: 'NA'),
                'warning'
            );

        } catch (Throwable $error) {
            // ignore
        }
    }

    /**
     * For use in subclasses
     *
     * @param string           $type
     * @param InstallerAdapter $parent
     *
     * @return bool
     * @throws Throwable
     */
    protected function customPreFlight(string $type, InstallerAdapter $parent): bool
    {
        return true;
    }

    /**
     * For use in subclasses
     *
     * @param InstallerAdapter $parent
     *
     * @return bool
     * @throws Throwable
     */
    protected function customInstall(InstallerAdapter $parent): bool
    {
        return true;
    }

    /**
     * For use in subclasses
     *
     * @param InstallerAdapter $parent
     *
     * @return bool
     * @throws Throwable
     */
    protected function customDiscoverInstall(InstallerAdapter $parent): bool
    {
        return true;
    }

    /**
     * For use in subclasses
     *
     * @param InstallerAdapter $parent
     *
     * @return bool
     * @throws Throwable
     */
    protected function customUpdate(InstallerAdapter $parent): bool
    {
        return true;
    }

    /**
     * For use in subclassses
     *
     * @param string           $type
     * @param InstallerAdapter $parent
     *
     * @return void
     * @throws Throwable
     */
    protected function customPostFlight(string $type, InstallerAdapter $parent): void
    {
    }

    /**
     * For use in subclasses
     *
     * @param InstallerAdapter $parent
     *
     * @return void
     * @throws Throwable
     */
    protected function customUninstall(InstallerAdapter $parent): void
    {
    }

    /**
     * @return void
     * @throws \Exception
     */
    final protected function installRelated(): void
    {
        $this->sendDebugMessage(__METHOD__);

        if ($this->manifest->alledia->relatedExtensions) {
            $source         = $this->installer->getPath('source');
            $extensionsPath = $source . '/extensions';

            $defaultAttributes = $this->manifest->alledia->relatedExtensions->attributes();
            $defaultDowngrade  = $this->getXmlValue($defaultAttributes['downgrade'], 'bool');
            $defaultPublish    = $this->getXmlValue($defaultAttributes['publish'], 'bool');

            foreach ($this->manifest->alledia->relatedExtensions->extension as $extension) {
                $path = $extensionsPath . '/' . $this->getXmlValue($extension);

                if (is_dir($path)) {
                    $type    = $this->getXmlValue($extension['type']);
                    $element = $this->getXmlValue($extension['element']);
                    $group   = $this->getXmlValue($extension['group']);
                    $key     = md5(join(':', [$type, $element, $group]));

                    $this->sendDebugMessage(
                        sprintf('Related: %s%s/%s', $type, $group ? ($group . '/') : '', $element)
                    );

                    try {
                        if ($type == 'plugin' && in_array($group, ['search', 'finder'])) {
                            if (is_dir(JPATH_ADMINISTRATOR . '/components/com_' . $group) == false) {
                                // skip search/finder plugins based on installed components
                                $this->sendDebugMessage(
                                    sprintf(
                                        'Skipped/Uninstalled plugin %s',
                                        ucwords($group . ' ' . $element)
                                    )
                                );

                                $this->uninstallExtension($type, $element, $group);
                                continue;
                            }
                        }

                        $current = $this->findExtension($type, $element, $group);
                        $isNew   = empty($current);

                        $typeName = ucwords(trim($group . ' ' . $type));

                        // Get data from the manifest
                        $tmpInstaller = new Installer();
                        $tmpInstaller->setPath('source', $path);
                        $tmpInstaller->setPath('parent', $this->installer->getPath('source'));

                        $newManifest = $tmpInstaller->getManifest();
                        $newVersion  = (string)$newManifest->version;

                        $this->storeFeedbackForRelatedExtension($key, 'name', (string)$newManifest->name);

                        $downgrade = $this->getXmlValue($extension['downgrade'], 'bool', $defaultDowngrade);

                        if (($isNew || $downgrade) == false) {
                            $currentManifestPath = $this->getManifestPath($type, $element, $group);
                            $currentManifest     = $this->getInfoFromManifest($currentManifestPath);

                            // Avoid to update for an outdated version
                            $currentVersion = $currentManifest->get('version');

                            if (version_compare($currentVersion, $newVersion, '>')) {
                                // Store the state of the install/update
                                $this->storeFeedbackForRelatedExtension(
                                    $key,
                                    'message',
                                    Text::sprintf(
                                        'LIB_SHACKINSTALLER_RELATED_UPDATE_STATE_SKIPED',
                                        $newVersion,
                                        $currentVersion
                                    )
                                );

                                // Skip the installation for this extension
                                continue;
                            }
                        }

                        $text = 'LIB_SHACKINSTALLER_RELATED_' . ($isNew ? 'INSTALL' : 'UPDATE');

                        if ($tmpInstaller->install($path)) {
                            $this->sendMessage(Text::sprintf($text, $typeName, $element));
                            if ($isNew) {
                                $current = $this->findExtension($type, $element, $group);

                                if (is_object($current)) {
                                    if ($type === 'plugin') {
                                        if ($this->getXmlValue($extension['publish'], 'bool', $defaultPublish)) {
                                            $current->publish();

                                            $this->storeFeedbackForRelatedExtension($key, 'publish', true);
                                        }

                                        if ($ordering = $this->getXmlValue($extension['ordering'])) {
                                            $this->setPluginOrder($current, $ordering);

                                            $this->storeFeedbackForRelatedExtension($key, 'ordering', $ordering);
                                        }
                                    }
                                }
                            }

                            $this->storeFeedbackForRelatedExtension(
                                $key,
                                'message',
                                Text::sprintf('LIB_SHACKINSTALLER_RELATED_UPDATE_STATE_INSTALLED', $newVersion)
                            );

                        } else {
                            $this->sendMessage(Text::sprintf($text . '_FAIL', $typeName, $element), 'error');

                            $this->storeFeedbackForRelatedExtension(
                                $key,
                                'message',
                                Text::sprintf(
                                    'LIB_SHACKINSTALLER_RELATED_UPDATE_STATE_FAILED',
                                    $newVersion
                                )
                            );
                        }
                        unset($tmpInstaller);

                    } catch (Throwable $error) {
                        $this->sendErrorMessage($error, false);
                    }
                }
            }
        }
    }

    /**
     * Uninstall the related extensions that are useless without the component
     *
     * @return void
     * @throws \Exception
     */
    final protected function uninstallRelated(): void
    {
        if ($this->manifest->alledia->relatedExtensions) {
            $defaultAttributes = $this->manifest->alledia->relatedExtensions->attributes();
            $defaultUninstall  = $this->getXmlValue($defaultAttributes['uninstall'], 'bool');

            foreach ($this->manifest->alledia->relatedExtensions->extension as $extension) {
                $type    = $this->getXmlValue($extension['type']);
                $element = $this->getXmlValue($extension['element']);
                $group   = $this->getXmlValue($extension['group']);

                $uninstall       = $this->getXmlValue($extension['uninstall'], 'bool', $defaultUninstall);
                $systemExtension = in_array(join('.', [$type, $group, $element]), $this->systemExtensions);
                if ($uninstall && $systemExtension == false) {
                    $this->uninstallExtension($type, $element, $group);

                } else {
                    $message = 'LIB_SHACKINSTALLER_RELATED_NOT_UNINSTALLED'
                        . ($systemExtension ? '_SYSTEM' : '');

                    if ($type == 'plugin') {
                        $type = $group . ' ' . $type;
                    }

                    $this->sendDebugMessage(Text::sprintf($message, ucwords($type), $element));
                }
            }
        }
    }

    /**
     * @param string  $type
     * @param string  $element
     * @param ?string $group
     *
     * @return void
     * @throws \Exception
     */
    final protected function uninstallExtension(string $type, string $element, ?string $group = null): void
    {
        if ($extension = $this->findExtension($type, $element, $group)) {
            $installer = new Installer();

            $success = $installer->uninstall($extension->get('type'), $extension->get('extension_id'));
            $msg     = 'LIB_SHACKINSTALLER_RELATED_UNINSTALL' . ($success ? '' : '_FAIL');
            if ($type == 'plugin') {
                $type = $group . ' ' . $type;
            }

            $this->sendMessage(
                Text::sprintf($msg, ucwords($type), $element),
                $success ? 'message' : 'error'
            );
        }
    }

    /**
     * @param ?string $type
     * @param ?string $element
     * @param ?string $group
     *
     * @return ?Extension
     * @throws \Exception
     */
    final protected function findExtension(?string $type, ?string $element, ?string $group = null): ?Extension
    {
        // @TODO: Why do we need to use JTable?
        /** @var Extension $row */
        $row = Table::getInstance('extension');

        $prefixes = [
            'component' => 'com_',
            'module'    => 'mod_',
        ];

        // Fix the element, if the prefix is not found
        if (array_key_exists($type, $prefixes)) {
            if (substr_count($element, $prefixes[$type]) === 0) {
                $element = $prefixes[$type] . $element;
            }
        }

        $terms = [
            'type'    => $type,
            'element' => $element,
        ];

        if ($type === 'plugin') {
            $terms['folder'] = $group;
        }

        $eid = $row->find($terms);

        if ($eid) {
            if ($row->load($eid) == false) {
                throw new \Exception($row->getError());
            }

            return $row;
        }

        return null;
    }

    /**
     * Set requested ordering for selected plugin extension
     * Accepted ordering arguments:
     * (n<=1 | first) First within folder
     * (* | last) Last within folder
     * (before:element) Before the named plugin
     * (after:element) After the named plugin
     *
     * @param Extension $extension
     * @param string    $order
     *
     * @return void
     */
    final protected function setPluginOrder(Extension $extension, string $order): void
    {
        if ($extension->get('type') == 'plugin' && empty($order) == false) {
            $db    = $this->dbo;
            $query = $db->getQuery(true);

            $query->select('extension_id, element');
            $query->from('#__extensions');
            $query->where([
                $db->quoteName('folder') . ' = ' . $db->quote($extension->get('folder')),
                $db->quoteName('type') . ' = ' . $db->quote($extension->get('type')),
            ]);
            $query->order($db->quoteName('ordering'));

            $plugins = $db->setQuery($query)->loadObjectList('element');

            // Set the order only if plugin already successfully installed
            if (array_key_exists($extension->get('element'), $plugins)) {
                $target = [
                    $extension->get('element') => $plugins[$extension->get('element')],
                ];
                $others = array_diff_key($plugins, $target);

                if ((is_numeric($order) && $order <= 1) || $order == 'first') {
                    // First in order
                    $neworder = array_merge($target, $others);

                } elseif (($order == '*') || ($order == 'last')) {
                    // Last in order
                    $neworder = array_merge($others, $target);

                } elseif (preg_match('/^(before|after):(\S+)$/', $order, $match)) {
                    // place before or after named plugin
                    $place    = $match[1];
                    $element  = $match[2];
                    $neworder = [];
                    $previous = '';

                    foreach ($others as $plugin) {
                        if (
                            (($place == 'before') && ($plugin->element == $element))
                            || (($place == 'after') && ($previous == $element))
                        ) {
                            $neworder = array_merge($neworder, $target);
                        }
                        $neworder[$plugin->element] = $plugin;
                        $previous                   = $plugin->element;
                    }

                    if (count($neworder) < count($plugins)) {
                        // Make it last if the requested plugin isn't installed
                        $neworder = array_merge($neworder, $target);
                    }

                } else {
                    $neworder = [];
                }

                if (count($neworder) == count($plugins)) {
                    // Only reorder if have a validated new order
                    BaseDatabaseModel::addIncludePath(
                        JPATH_ADMINISTRATOR . '/components/com_plugins/models',
                        'PluginsModels'
                    );

                    // @TODO: Model class is (\PluginsModelPlugin) in J3 but this works either way
                    /** @var PluginModel $model */
                    $model = BaseDatabaseModel::getInstance('Plugin', 'PluginsModel');

                    $ids = [];
                    foreach ($neworder as $plugin) {
                        $ids[] = $plugin->extension_id;
                    }
                    $order = range(1, count($ids));
                    $model->saveorder($ids, $order);
                }
            }
        }
    }

    /**
     * Add a message to the message list
     *
     * @param string  $message
     * @param ?string $type
     *
     * @return void
     * @deprecated v2.0.0: use $this->sendMessage()
     */
    final protected function setMessage(string $message, ?string $type = 'message'): void
    {
        $this->sendMessage($message, $type);
    }

    /**
     * Delete obsolete files, folders and extensions.
     * Files and folders are identified from the site
     * root path.
     *
     * @param ?SimpleXMLElement $obsolete
     *
     * @return void
     * @throws \Exception
     */
    final protected function clearObsolete(?SimpleXMLElement $obsolete = null): void
    {
        $obsolete = $obsolete ?: $this->manifest->alledia->obsolete;

        $this->sendDebugMessage(__METHOD__ . '<pre>' . print_r($obsolete, 1) . '</pre>');

        $this->clearOldSystemPlugin();
        if ($obsolete) {
            if ($obsolete->extension) {
                foreach ($obsolete->extension as $extension) {
                    $type    = $this->getXmlValue($extension['type']);
                    $element = $this->getXmlValue($extension['element']);
                    $group   = $this->getXmlValue($extension['group']);

                    $current = $this->findExtension($type, $element, $group);
                    if (empty($current) == false) {
                        // Try to uninstall
                        $tmpInstaller = new Installer();
                        $uninstalled  = $tmpInstaller->uninstall($type, $current->get('extension_id'));

                        $typeName = ucfirst(trim(($group ?: '') . ' ' . $type));

                        if ($uninstalled) {
                            $this->sendMessage(
                                Text::sprintf(
                                    'LIB_SHACKINSTALLER_OBSOLETE_UNINSTALLED_SUCCESS',
                                    strtolower($typeName),
                                    $element
                                )
                            );
                        } else {
                            $this->sendMessage(
                                Text::sprintf(
                                    'LIB_SHACKINSTALLER_OBSOLETE_UNINSTALLED_FAIL',
                                    strtolower($typeName),
                                    $element
                                ),
                                'error'
                            );
                        }
                    }
                }
            }

            if ($obsolete->file) {
                foreach ($obsolete->file as $file) {
                    $path = JPATH_ROOT . '/' . trim((string)$file, '/');
                    if (is_file($path)) {
                        File::delete($path);
                    }
                }
            }

            if ($obsolete->folder) {
                foreach ($obsolete->folder as $folder) {
                    $path = JPATH_ROOT . '/' . trim((string)$folder, '/');
                    if (is_dir($path)) {
                        Folder::delete($path);
                    }
                }
            }
        }

        $oldLanguageFiles = Folder::files(JPATH_ADMINISTRATOR . '/language', '\.lib_allediainstaller\.', true, true);
        foreach ($oldLanguageFiles as $oldLanguageFile) {
            File::delete($oldLanguageFile);
        }
    }

    /**
     * Finds the extension row for the main extension
     *
     * @return ?Extension
     * @throws \Exception
     */
    final protected function findThisExtension(): ?Extension
    {
        return $this->findExtension(
            $this->getXmlValue($this->manifest['type']),
            $this->getXmlValue($this->manifest->alledia->element),
            $this->getXmlValue($this->manifest['group'])
        );
    }

    /**
     * Use this in preflight to clear out obsolete update servers when the url has changed.
     *
     * @return void
     * @throws \Exception
     */
    final protected function clearUpdateServers(): void
    {
        if ($extension = $this->findThisExtension()) {
            $db = $this->dbo;

            $query = $db->getQuery(true)
                ->select($db->quoteName('update_site_id'))
                ->from($db->quoteName('#__update_sites_extensions'))
                ->where($db->quoteName('extension_id') . '=' . (int)$extension->get('extension_id'));

            if ($list = $db->setQuery($query)->loadColumn()) {
                $query = $db->getQuery(true)
                    ->delete($db->quoteName('#__update_sites_extensions'))
                    ->where($db->quoteName('extension_id') . '=' . (int)$extension->get('extension_id'));
                $db->setQuery($query)->execute();

                array_walk($list, 'intval');
                $query = $db->getQuery(true)
                    ->delete($db->quoteName('#__update_sites'))
                    ->where($db->quoteName('update_site_id') . ' IN (' . join(',', $list) . ')');
                $db->setQuery($query)->execute();
            }
        }
    }

    /**
     * Get the full element, like com_myextension, lib_extension
     *
     * @param ?string $type
     * @param ?string $element
     * @param ?string $group
     *
     * @return string
     */
    final protected function getFullElement(
        ?string $type = null,
        ?string $element = null,
        ?string $group = null
    ): string {
        $prefixes = [
            'component' => 'com',
            'plugin'    => 'plg',
            'template'  => 'tpl',
            'library'   => 'lib',
            'cli'       => 'cli',
            'module'    => 'mod',
            'file'      => 'file',
        ];

        $type    = $type ?: $this->type;
        $element = $element ?: (string)$this->manifest->alledia->element;
        $group   = $group ?: $this->group;

        $fullElement = $prefixes[$type] . '_';

        if ($type === 'plugin') {
            $fullElement .= $group . '_';
        }

        return $fullElement . $element;
    }

    /**
     * @return Licensed
     */
    final protected function getLicense(): Licensed
    {
        if ($this->license === null) {
            $this->license = new Licensed(
                (string)$this->manifest->alledia->namespace,
                $this->type,
                $this->group
            );
        }

        return $this->license;
    }

    /**
     * @param string $manifestPath
     *
     * @return Registry
     */
    final protected function getInfoFromManifest(string $manifestPath): Registry
    {
        $info = new Registry();

        if (is_file($manifestPath)) {
            $xml = simplexml_load_file($manifestPath);

            $attributes = (array)$xml->attributes();
            $attributes = $attributes['@attributes'];
            foreach ($attributes as $attribute => $value) {
                $info->set($attribute, $value);
            }

            foreach ($xml->children() as $e) {
                if (!$e->children()) {
                    $info->set($e->getName(), (string)$e);
                }
            }

        } else {
            $relativePath = str_replace(JPATH_SITE . '/', '', $manifestPath);
            $this->sendMessage(
                Text::sprintf('LIB_SHACKINSTALLER_MANIFEST_NOT_FOUND', $relativePath),
                'error'
            );
        }

        return $info;
    }

    /**
     * @param string  $type
     * @param string  $element
     * @param ?string $group
     *
     * @return string
     */
    final protected function getExtensionPath(string $type, string $element, ?string $group = ''): string
    {
        $folders = [
            'component' => 'administrator/components/',
            'plugin'    => 'plugins/',
            'template'  => 'templates/',
            'library'   => 'libraries/',
            'cli'       => 'cli/',
            'module'    => 'modules/',
            'file'      => 'administrator/manifests/files/',
        ];

        $basePath = JPATH_SITE . '/' . $folders[$type];

        switch ($type) {
            case 'plugin':
                $basePath .= $group . '/';
                break;

            case 'module':
                if (!preg_match('/^mod_/', $element)) {
                    $basePath .= 'mod_';
                }
                break;

            case 'component':
                if (!preg_match('/^com_/', $element)) {
                    $basePath .= 'com_';
                }
                break;

            case 'template':
                if (preg_match('/^tpl_/', $element)) {
                    $element = str_replace('tpl_', '', $element);
                }
                break;
        }

        if ($type !== 'file') {
            $basePath .= $element;
        }

        return $basePath;
    }

    /**
     * @param string  $type
     * @param string  $element
     * @param ?string $group
     *
     * @return int
     */
    final protected function getExtensionId(string $type, string $element, ?string $group = ''): int
    {
        $db    = $this->dbo;
        $query = $db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where([
                $db->quoteName('element') . ' = ' . $db->quote($element),
                $db->quoteName('folder') . ' = ' . $db->quote($group),
                $db->quoteName('type') . ' = ' . $db->quote($type),
            ]);
        $db->setQuery($query);

        return (int)$db->loadResult();
    }

    /**
     * Get the path for the manifest file
     *
     * @return string The path
     */
    final protected function getManifestPath($type, $element, $group = ''): string
    {
        $installer = new Installer();

        switch ($type) {
            case 'library':
            case 'file':
                $folders = [
                    'library' => 'libraries',
                    'file'    => 'files',
                ];

                $manifestPath = JPATH_SITE . '/administrator/manifests/' . $folders[$type] . '/' . $element . '.xml';

                if (!file_exists($manifestPath) || !$installer->isManifest($manifestPath)) {
                    $manifestPath = false;
                }
                break;

            default:
                $basePath = $this->getExtensionPath($type, $element, $group);

                $installer->setPath('source', $basePath);
                $installer->getManifest();

                $manifestPath = $installer->getPath('manifest');
                break;
        }

        return $manifestPath;
    }

    /**
     * Check if it needs to publish the extension
     *
     * @return void
     * @throws \Exception
     */
    final protected function publishThisPlugin(): void
    {
        $attributes = $this->manifest->alledia->element->attributes();
        $publish    = (string)$attributes['publish'];

        if ($publish === 'true' || $publish === '1') {
            $extension = $this->findThisExtension();
            $extension->publish();
        }
    }

    /**
     * Check if it needs to reorder the extension
     *
     * @return void
     * @throws \Exception
     */
    final protected function reorderThisPlugin(): void
    {
        $attributes = $this->manifest->alledia->element->attributes();
        $ordering   = (string)$attributes['ordering'];

        if ($ordering !== '') {
            $extension = $this->findThisExtension();
            $this->setPluginOrder($extension, $ordering);
        }
    }

    /**
     * Stores feedback data for related extensions to display after install
     *
     * @param string $key
     * @param string $property
     * @param string $value
     *
     * @return void
     */
    final protected function storeFeedbackForRelatedExtension(string $key, string $property, string $value): void
    {
        $this->sendDebugMessage(sprintf(
            '%s<br>**** %s-%s-%s<br><br>',
            __METHOD__,
            $key,
            $property,
            $value
        ));

        if (empty($this->relatedExtensionFeedback[$key])) {
            $this->relatedExtensionFeedback[$key] = [];
        }

        $this->relatedExtensionFeedback[$key][$property] = $value;
    }

    /**
     * This method add a mark to the extensions, allowing to detect our extensions
     * on the extensions table.
     *
     * @return void
     * @throws \Exception
     */
    final protected function addAllediaAuthorshipToExtension(): void
    {
        if ($extension = $this->findThisExtension()) {
            $db = $this->dbo;

            // Update the extension
            $customData         = json_decode($extension->get('custom_data')) ?: (object)[];
            $customData->author = 'Joomlashack';

            $query = $db->getQuery(true)
                ->update($db->quoteName('#__extensions'))
                ->set($db->quoteName('custom_data') . '=' . $db->quote(json_encode($customData)))
                ->where($db->quoteName('extension_id') . '=' . (int)$extension->get('extension_id'));
            $db->setQuery($query)->execute();

            // Update the Alledia framework
            // @TODO: remove this after libraries be able to have a custom install script
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__extensions'))
                ->set($db->quoteName('custom_data') . '=' . $db->quote('{"author":"Joomlashack"}'))
                ->where([
                    $db->quoteName('type') . '=' . $db->quote('library'),
                    $db->quoteName('element') . '=' . $db->quote('allediaframework'),
                ]);
            $db->setQuery($query)->execute();
        }
    }

    /**
     * Add styles to the output. Used because when the postFlight
     * method is called, we can't add stylesheets to the head.
     *
     * @param mixed $stylesheets
     *
     * @return void
     */
    final protected function addStyle($stylesheets): void
    {
        if (is_string($stylesheets)) {
            $stylesheets = [$stylesheets];
        }

        foreach ($stylesheets as $path) {
            if (file_exists($path)) {
                $style = file_get_contents($path);

                echo '<style>' . $style . '</style>';
            }
        }
    }

    /**
     * On new component install, this will check and fix any menus
     * that may have been created in a previous installation.
     *
     * @return void
     * @throws \Exception
     */
    final protected function fixMenus(): void
    {
        if ($this->type == 'component') {
            $db = $this->dbo;

            if ($extension = $this->findThisExtension()) {
                $id     = $extension->get('extension_id');
                $option = $extension->get('name');

                $query = $db->getQuery(true)
                    ->update('#__menu')
                    ->set('component_id = ' . $db->quote($id))
                    ->where([
                        'type = ' . $db->quote('component'),
                        'link LIKE ' . $db->quote("%option={$option}%"),
                    ]);
                $db->setQuery($query)->execute();

                // Check hidden admin menu option
                // @TODO:  Remove after Joomla! incorporates this natively
                $menuElement = $this->manifest->administration->menu;
                if (in_array((string)$menuElement['hidden'], ['true', 'hidden'])) {
                    $menu = Table::getInstance('Menu');
                    $menu->load(['component_id' => $id, 'client_id' => 1]);
                    if ($menu->id) {
                        $menu->delete();
                    }
                }
            }
        }
    }

    /**
     * Get and store a cache of columns of a table
     *
     * @param string $table The table name
     *
     * @return string[]
     * @deprecated v2.1.0: Use $this->findColumn()
     */
    final protected function getColumnsFromTable(string $table): array
    {
        if (!isset($this->columns[$table])) {
            $db = $this->dbo;
            $db->setQuery('SHOW COLUMNS FROM ' . $db->quoteName($table));
            $rows = $db->loadObjectList();

            $columns = [];
            foreach ($rows as $row) {
                $columns[] = $row->Field;
            }

            $this->columns[$table] = $columns;
        }

        return $this->columns[$table];
    }

    /**
     * Get and store a cache of indexes of a table
     *
     * @param string $table The table name
     *
     * @return string[]
     */
    final protected function getIndexesFromTable(string $table): array
    {
        if (!isset($this->indexes[$table])) {
            $db = $this->dbo;
            $db->setQuery('SHOW INDEX FROM ' . $db->quoteName($table));
            $rows = $db->loadObjectList();

            $indexes = [];
            foreach ($rows as $row) {
                $indexes[] = $row->Key_name;
            }

            $this->indexes[$table] = $indexes;
        }

        return $this->indexes[$table];
    }

    /**
     * Add columns to a table if they doesn't exists
     *
     * @param string   $table   The table name
     * @param string[] $columns Assoc array of columnNames => definition
     *
     * @return void
     * @deprecated v2.1.0: Use $this->addColumns()
     */
    final protected function addColumnsIfNotExists(string $table, array $columns): void
    {
        $columnSpecs = [];
        foreach ($columns as $columnName => $columnData) {
            $columnId               = $table . '.' . $columnName;
            $columnSpecs[$columnId] = $columnData;
        }

        $this->addColumns($columnSpecs);
    }

    /**
     * Add indexes to a table if they doesn't exists
     *
     * @param string $table   The table name
     * @param array  $indexes Assoc array of indexName => definition
     *
     * @return void
     * @deprecated v2.1.0: use $this->addIndexes()
     */
    final protected function addIndexesIfNotExists(string $table, array $indexes): void
    {
        $db = $this->dbo;

        $existentIndexes = $this->getIndexesFromTable($table);

        foreach ($indexes as $index => $specification) {
            if (!in_array($index, $existentIndexes)) {
                $db->setQuery(
                    "ALTER TABLE {$db->quoteName($table)} CREATE INDEX {$specification} ON {$index}"
                )
                    ->execute();
            }
        }
    }

    /**
     * Drop columns from a table if they exists
     *
     * @param string   $table   The table name
     * @param string[] $columns The column names that needed to be checked and added
     *
     * @return void
     * @deprecated v2.1.0: Use $this->dropColumns()
     */
    final protected function dropColumnsIfExists(string $table, array $columns): void
    {
        $columnIds = [];
        foreach ($columns as $column) {
            $columnIds[] = $table . '.' . $column;
        }

        $this->dropColumns($columnIds);
    }

    /**
     * Check if a table exists
     *
     * @param string $name
     *
     * @return bool
     * @deprecated v2.1.0: Use $this->findTable()
     */
    final protected function tableExists(string $name): bool
    {
        return $this->findTable($name);
    }

    /**
     * Parses a conditional string, returning a Boolean value (default: false).
     * For now it only supports an extension name and * as version.
     *
     * @param string $expression
     *
     * @return bool
     * @throws \Exception
     */
    final protected function parseConditionalExpression(string $expression): bool
    {
        $expression = strtolower($expression);
        $terms      = explode('=', $expression);
        $firstTerm  = array_shift($terms);

        if (count($terms) == 0) {
            return $firstTerm == 'true' || $firstTerm == '1';

        } elseif (preg_match('/^(com_|plg_|mod_|lib_|tpl_|cli_)/', $firstTerm)) {
            // The first term is the name of an extension

            $info = $this->getExtensionInfoFromElement($firstTerm);

            $extension = $this->findExtension($info['type'], $firstTerm, $info['group']);

            // @TODO: compare the version, if specified, or different than *
            // @TODO: Check if the extension is enabled, not just installed

            if (empty($extension) == false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get extension's info from element string, or extension name
     *
     * @param string $element The extension name, as element
     *
     * @return string[] An associative array with information about the extension
     */
    final protected function getExtensionInfoFromElement(string $element): array
    {
        $result = array_fill_keys(
            ['type', 'name', 'group', 'prefix', 'namespace'],
            null
        );

        $types = [
            'com' => 'component',
            'plg' => 'plugin',
            'mod' => 'module',
            'lib' => 'library',
            'tpl' => 'template',
            'cli' => 'cli',
        ];

        $element = explode('_', $element, 3);

        $prefix = $result['prefix'] = array_shift($element);
        $name   = array_pop($element);
        $group  = array_pop($element);

        if (array_key_exists($prefix, $types)) {
            $result = array_merge(
                $result,
                [
                    'type'  => $types[$prefix],
                    'group' => $group,
                    'name'  => $name,
                ]
            );
        }

        $result['namespace'] = preg_replace_callback(
            '/^(os[a-z])(.*)/i',
            function ($matches) {
                return strtoupper($matches[1]) . $matches[2];
            },
            $name ?? ''
        );

        return $result;
    }

    /**
     * Check if the actual version is at least the minimum target version.
     *
     * @param string  $actualVersion
     * @param string  $targetVersion
     * @param ?string $compare
     *
     * @return bool True, if the target version is greater than or equal to actual version
     */
    final protected function validateTargetVersion(
        string $actualVersion,
        string $targetVersion,
        ?string $compare = null
    ): bool {
        if ($targetVersion === '.*') {
            // Any version is valid
            return true;
        }

        $targetVersion = str_replace('*', '0', $targetVersion);

        return version_compare($actualVersion, $targetVersion, $compare ?: 'ge');
    }

    /**
     * @param string  $targetVersion
     * @param ?string $compare
     *
     * @return bool
     */
    final protected function validatePreviousVersion(string $targetVersion, ?string $compare = null): bool
    {
        if ($this->previousManifest) {
            $lastVersion = (string)$this->previousManifest->version;

            return $this->validateTargetVersion($lastVersion, $targetVersion, $compare);
        }

        return true;
    }

    /**
     * Get the extension name. If no custom name is set, uses the namespace
     *
     * @return string
     */
    final protected function getName(): string
    {
        return (string)($this->manifest->alledia->name ?? $this->manifest->alledia->namespace);
    }

    /**
     * @param ?bool $force Force to get a fresh list of tables
     *
     * @return string[] List of tables
     */
    final protected function getTables(?bool $force = false): array
    {
        if ($force || $this->tables === null) {
            $this->tables = $this->dbo->setQuery('SHOW TABLES')->loadColumn();
        }

        return $this->tables;
    }

    /**
     * @param string $table
     *
     * @return bool
     */
    final protected function findTable(string $table): bool
    {
        return in_array($this->dbo->replacePrefix($table), $this->getTables());
    }

    /**
     * @param string[] $columnSpecs
     *
     * @return void
     * @TODO: allow use of specification array
     */
    final protected function addColumns(array $columnSpecs): void
    {
        $db = $this->dbo;

        foreach ($columnSpecs as $columnId => $specification) {
            if (strpos($columnId, '.') !== false && empty($this->findColumn($columnId))) {
                [$table, $columnName] = explode('.', $columnId);

                $db->setQuery(
                    sprintf(
                        'ALTER TABLE %s ADD COLUMN %s %s',
                        $db->quoteName($table),
                        $db->quoteName($columnName),
                        $specification
                    )
                )
                    ->execute();
            }
        }
    }

    /**
     * @param string[] $columnIds
     *
     * @return void
     */
    final protected function dropColumns(array $columnIds): void
    {
        $db = $this->dbo;
        foreach ($columnIds as $columnId) {
            if (strpos($columnId, '.') !== false) {
                [$table, $column] = explode('.', $columnId);

                $db->setQuery(
                    sprintf(
                        'ALTER TABLE %s DROP COLUMN %s',
                        $db->quoteName($table),
                        $column
                    )
                )
                    ->execute();
            }
        }
    }

    /**
     * @param string $columnId
     *
     * @return ?object
     */
    final protected function findColumn(string $columnId): ?object
    {
        if (strpos($columnId, '.') !== false) {
            $db = $this->dbo;

            [$table, $field] = explode('.', $columnId, 2);

            if (isset($this->tableColumns[$table]) == false) {
                $this->tableColumns[$table] = $db->setQuery('SHOW COLUMNS FROM ' . $db->quoteName($table))
                    ->loadObjectList();
            }

            foreach ($this->tableColumns[$table] as $column) {
                if ($column->Field == $field) {
                    return $column;
                }
            }
        }

        return null;
    }

    /**
     * @param array $indexes
     *
     * @return void
     */
    final protected function addIndexes(array $indexes): void
    {
        $db = $this->dbo;

        foreach ($indexes as $indexId => $ordering) {
            if (strpos($indexId, '.') !== false) {
                $index      = explode('.', $indexId);
                $indexTable = array_shift($index) ?: '';
                $indexName  = array_shift($index) ?: '';
                $indexType  = array_shift($index) ?: '';

                if ($this->findIndex($indexTable . '.' . $indexName) == false) {
                    $db->setQuery(
                        sprintf(
                            'ALTER TABLE %s ADD %s INDEX %s(%s)',
                            $db->quoteName($indexTable),
                            $indexType,
                            $db->quoteName($indexName),
                            join(',', $ordering)
                        )
                    )
                        ->execute();
                }
            }
        }
    }

    /**
     * @param string[] $indexIds
     *
     * @return void
     */
    final protected function dropIndexes(array $indexIds): void
    {
        $db = $this->dbo;

        foreach ($indexIds as $indexId) {
            if (strpos($indexId, '.') !== false) {
                if ($this->findIndex($indexId)) {
                    [$table, $indexName] = explode('.', $indexId);

                    $db->setQuery(
                        sprintf(
                            'ALTER TABLE %s DROP INDEX %s',
                            $db->quoteName($table),
                            $db->quoteName($indexName)
                        )
                    )
                        ->execute();
                }
            }
        }
    }

    /**
     * @param string $indexId
     *
     * @return object[]
     */
    final protected function findIndex(string $indexId): array
    {
        if (strpos($indexId, '.') !== false) {
            $db = $this->dbo;

            [$table, $indexName] = explode('.', $indexId);

            if (isset($this->tableIndexes[$table]) == false) {
                $this->tableIndexes[$table] = $db->setQuery('SHOW INDEX FROM ' . $db->quoteName($table))
                    ->loadObjectList();
            }

            $indexes = [];
            foreach ($this->tableIndexes[$table] as $index) {
                if ($index->Key_name == $indexName) {
                    $indexes[] = $index;
                }
            }

            return $indexes;
        }

        return [];
    }

    /**
     * @param string[] $constraintIds
     *
     * @return void
     */
    final protected function dropConstraints(array $constraintIds): void
    {
        $db = $this->dbo;

        foreach ($constraintIds as $constraintId) {
            if (strpos($constraintId, '.') !== false && $this->findConstraint($constraintId)) {
                [$table, $constraintName] = explode('.', $constraintId);

                $db->setQuery(
                    sprintf(
                        'ALTER TABLE %s DROP FOREIGN KEY %s',
                        $db->quoteName($table),
                        $db->quoteName($constraintName)
                    )
                )
                    ->execute();
            }
        }
    }

    /**
     * @param string $constraintId
     *
     * @return object[]
     */
    final protected function findConstraint(string $constraintId): array
    {
        if (strpos($constraintId, '.') !== false) {
            $db = $this->dbo;

            [$table, $constraint] = explode('.', $constraintId);

            if (isset($this->tableConstraints[$table]) == false) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('information_schema.KEY_COLUMN_USAGE')
                    ->where('TABLE_NAME = ' . $db->quote($db->replacePrefix($table)));

                $this->tableConstraints[$table] = $db->setQuery($query)->loadObjectList();
            }

            $items = [];
            foreach ($this->tableConstraints[$table] as $item) {
                if ($item->CONSTRAINT_NAME == $constraint) {
                    $items[] = $item;
                }
            }

            return $items ?: [];
        }

        return [];
    }

    /**
     * @return ?string
     * @throws \Exception
     */
    final protected function getSchemaVersion(): ?string
    {
        if ($extension = $this->findThisExtension()) {
            $query = $this->dbo->getQuery(true)
                ->select('version_id')
                ->from('#__schemas')
                ->where('extension_id = ' . $extension->get('extension_id'));

            return $this->dbo->setQuery($query)->loadResult();
        }

        return null;
    }

    /**
     * @param string|string[] $queries
     *
     * @return bool|Throwable
     */
    final protected function executeQuery($schemaVersion, $queries)
    {
        $this->sendDebugMessage(__METHOD__);
        $this->sendDebugMessage($this->schemaVersion . ' / ' . $schemaVersion);

        if ($this->schemaVersion && version_compare($this->schemaVersion, $schemaVersion, 'lt')) {
            $this->sendDebugMessage(sprintf('Running v%s Schema Updates', $schemaVersion));

            $db = $this->dbo;
            try {
                foreach ((array)$queries as $query) {
                    $this->sendDebugMessage($query);
                    if ($db->setQuery($query)->execute() == false) {
                        return new \Exception('Query Error: ' . $query);
                    }
                }

            } catch (Throwable $error) {
                return $error;
            }
        }

        return true;
    }

    /**
     * Joomla 4 does a database check that has lots of problems with standard sql syntax
     * causing it to declare the database tables as not up to date and in some cases
     * generates various sql errors. This can optionally be called during Post Install to
     * clear out all update files and still maintain the latest schema version correctly.
     *
     * @param string $basePath
     *
     * @return void
     */
    final protected function clearDBUpdateFiles(string $basePath): void
    {
        $this->sendDebugMessage(__METHOD__);

        $updatePath = $basePath . '/sql/updates';
        if (is_dir($updatePath) && $files = Folder::files($updatePath, '\.sql$', true, true)) {
            $this->sendDebugMessage('Removing:<pre>' . print_r($files, 1) . '</pre>');
            $final = reset($files);
            foreach ($files as $file) {
                $version     = basename($file, '.sql');
                $lastVersion = basename($final, '.sql');

                if (version_compare($version, $lastVersion, 'gt')) {
                    $final = $file;
                }
                File::delete($file);
            }

            if ($final) {
                File::write($final, '');
                $this->sendDebugMessage('Wrote blank: ' . $final);
            }
        }
    }

    /**
     * @param SimpleXMLElement|string $element
     * @param ?string                 $type
     * @param mixed                   $default
     *
     * @return bool|string
     */
    final protected function getXmlValue($element, ?string $type = 'string', $default = null)
    {
        $value = $element ? (string)$element : $default;

        switch ($type) {
            case 'bool':
            case 'boolean':
                $value = $element
                    ? $value == 'true' || $value == '1'
                    : (bool)$default;
                break;

            case 'string':
            default:
                if ($value) {
                    $value = trim($value);
                }
                break;
        }

        return $value;
    }

    /**
     * @param string $text
     * @param string $type
     *
     * @return void
     */
    final protected function sendMessage(string $text, string $type = 'message'): void
    {
        if ($this->outputAllowed) {
            try {
                $this->app = $this->app ?: Factory::getApplication();
                $this->app->enqueueMessage($text, $type);

            } catch (Throwable $error) {
                // Give up trying to send a message normally
            }
        }
    }

    /**
     * @param Throwable $error
     * @param bool      $cancel
     *
     * @return void
     */
    final protected function sendErrorMessage(Throwable $error, bool $cancel = true): void
    {
        if ($cancel) {
            $this->cancelInstallation = true;
        }

        if ($this->outputAllowed) {
            $trace = $error->getTrace();
            $trace = array_shift($trace);

            if (empty($trace['class'])) {
                $caller = basename($trace['file']);

            } else {
                $className = explode('\\', $trace['class']);
                $caller    = array_pop($className);
            }
            $line     = $trace['line'];
            $function = $trace['function'] ?? null;
            $file     = $trace['file'];

            if ($function) {
                $message = sprintf('%s: %s<br>%s::%s() - %s', $line, $file, $caller, $function, $error->getMessage());
            } else {
                $message = sprintf('%s:%s (%s) - %s', $line, $caller, $file, $error->getMessage());
            }

            $this->sendMessage($message, 'error');
        }
    }

    /**
     * @param string $text
     *
     * @return void
     */
    final protected function sendDebugMessage(string $text): void
    {
        if ($this->debug) {
            $type = Version::MAJOR_VERSION == 3 ? 'Debug-' . get_class($this) : CMSApplicationInterface::MSG_DEBUG;
            $this->sendMessage($text, $type);
        }
    }

    /**
     * @param string $type
     *
     * @return void
     */
    final protected function displayWelcome(string $type): void
    {
        if ($this->outputAllowed == false) {
            return;
        }

        $this->sendDebugMessage(
            sprintf(
                '%s<br>Parent: %s<br>Current: %s',
                __METHOD__,
                $this->installer->getPath('parent'),
                $this->installer->getPath('source')
            )
        );

        $license = $this->getLicense();
        $name    = $this->getName() . ($license->isPro() ? ' Pro' : '');

        // Get the footer content
        $this->footer = '';

        // Check if we have a dedicated config.xml file
        $configPath = $license->getExtensionPath() . '/config.xml';
        if (is_file($configPath)) {
            $config = $license->getConfig();

            if (empty($config) == false) {
                $footerElement = $config->xpath('//field[@type="customfooter"]');
            }
        } else {
            $footerElement = $this->manifest->xpath('//field[@type="customfooter"]');
        }

        if (empty($footerElement) == false) {
            if (class_exists('\\JFormFieldCustomFooter') == false) {
                // Custom footer field is not (and should not be) automatically loaded
                $customFooterPath = $license->getExtensionPath() . '/form/fields/customfooter.php';

                if (is_file($customFooterPath)) {
                    include_once $customFooterPath;
                }
            }

            if (class_exists('\\JFormFieldCustomFooter')) {
                $field                = new JFormFieldCustomFooter();
                $field->fromInstaller = true;
                $this->footer         = $field->getInputUsingCustomElement($footerElement[0]);

                unset($field, $footerElement);
            }
        } else {
            $this->sendDebugMessage('No Footer element was found');
        }

        // Show additional installation messages
        $extensionPath = $this->getExtensionPath(
            $this->type,
            (string)$this->manifest->alledia->element,
            $this->group
        );

        // If Pro extension, includes the license form view
        if ($license->isPro()) {
            // Get the OSMyLicensesManager extension to handle the license key
            if ($licensesManagerExtension = new Licensed('osmylicensesmanager', 'plugin', 'system')) {
                if (isset($licensesManagerExtension->params)) {
                    $this->licenseKey = $licensesManagerExtension->params->get('license-keys', '');
                } else {
                    $this->licenseKey = '';
                }

                $this->isLicensesManagerInstalled = true;
            }

            $this->sendDebugMessage('License Manager plugin: ' . (int)$this->isLicensesManagerInstalled);
        }

        // Welcome message
        if (in_array($type, [static::TYPE_INSTALL, static::TYPE_DISCOVER_INSTALL])) {
            $string = 'LIB_SHACKINSTALLER_THANKS_INSTALL';
        } else {
            $string = 'LIB_SHACKINSTALLER_THANKS_UPDATE';
        }

        // Variables for the included template
        $this->welcomeMessage = Text::sprintf($string, $name);
        $this->mediaURL       = Uri::root() . 'media/' . $license->getFullElement();

        $this->addStyle($this->mediaFolder . '/css/installer.css');

        /*
         * Include the template
         * Try to find the template in an alternative folder, since some extensions
         * which uses FOF will display the "Installers" view on admin, errouniously.
         * FOF look for views automatically reading the views folder. So on that
         * case we move the installer view to another folder.
        */
        $path = $extensionPath . '/views/installer/tmpl/default.php';

        if (is_file($path) == false) {
            $path = $extensionPath . '/alledia_views/installer/tmpl/default.php';
        }

        $this->sendDebugMessage(sprintf('Welcome View (%s): %s', (int)is_file($path), $path));
        if (is_file($path)) {
            include $path;
        }
    }

    /**
     * WARNIMG! This is duplicated from the Joomlashack Framework
     *
     * @param string  $name
     * @param string  $prefix
     * @param string  $component
     * @param ?string $appName
     * @param ?array  $options
     *
     * @return mixed
     * @throws \Exception
     */
    protected function getJoomlaModel(
        string $name,
        string $prefix,
        string $component,
        ?string $appName = null,
        ?array $options = []
    ) {
        $defaultApp = 'Site';
        $appNames   = [$defaultApp, 'Administrator'];

        $appName = ucfirst($appName ?: $defaultApp);
        $appName = in_array($appName, $appNames) ? $appName : $defaultApp;

        if (Version::MAJOR_VERSION < 4) {
            $basePath = $appName == 'Administrator' ? JPATH_ADMINISTRATOR : JPATH_SITE;

            $path = $basePath . '/components/' . $component;
            BaseDatabaseModel::addIncludePath($path . '/models');
            Table::addIncludePath($path . '/tables');

            $model = BaseDatabaseModel::getInstance($name, $prefix, $options);

        } else {
            $model = Factory::getApplication()->bootComponent($component)
                ->getMVCFactory()->createModel($name, $appName, $options);
        }

        return $model;
    }

    /**
     * Utility function to setting extension states
     * @param array $extensions
     * @param int   $state
     * @return array
     * @throws \Exception
     */
    final protected function setExtensionState(array $extensions, int $state = 0): array
    {
        $states = [];
        if (in_array($state, [0, 1])) {
            foreach ($extensions as $extension) {
                $parts = explode('.', $extension);

                $element = array_pop($parts);
                $folder  = null;

                switch (count($parts)) {
                    case 1:
                        $type = array_pop($parts);
                        break;

                    case 2:
                        $folder = array_pop($parts);
                        $type   = array_pop($parts);
                        break;

                    default:
                        // Badly structured extension identifier
                        break 2;
                }

                if ($object = $this->findExtension($type, $element, $folder)) {
                    $states[$extension] = (int)$object->get('enabled');
                    if ($states[$extension] != $state) {
                        $this->sendDebugMessage(
                            sprintf(
                                '%s: %s',
                                $extension,
                                $state ? 'Enabled' : 'Disabled'
                            )
                        );

                        $object->set('enabled', $state);
                        $object->store();
                    }
                }
            }
        }

        return $states;
    }

    /**
     * If the old system plugin is installed, it requires special handling to avoid
     * fatal conflicts with its install script
     *
     * @return void
     * @throws \Exception
     */
    final protected function clearOldSystemPlugin()
    {
        if ($this->findExtension('plugin', 'ossystem', 'system')) {
            if (class_exists('PlgSystemOSSystemInstallerScript') == false) {
                class_alias(static::class, 'PlgSystemOSSystemInstallerScript');
            }
        }
    }
}
