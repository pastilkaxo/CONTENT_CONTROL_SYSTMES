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

namespace Alledia\Installer\Extension;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

/**
 * Generic extension class
 */
class Generic
{
    /**
     * The extension namespace
     *
     * @var string
     */
    protected $namespace = null;

    /**
     * The extension type
     *
     * @var string
     */
    protected $type = null;

    /**
     * The extension id
     *
     * @var int
     */
    protected $id = null;

    /**
     * The extension name
     *
     * @var string
     */
    protected $name = null;

    /**
     * The extension params
     *
     * @var Registry
     */
    public $params = null;

    /**
     * The extension enable state
     *
     * @var bool
     */
    protected $enabled = null;

    /**
     * The element of the extension
     *
     * @var string
     */
    protected $element = null;

    /**
     * @var string
     */
    protected $folder = null;

    /**
     * Base path
     *
     * @var string
     */
    protected $basePath = null;

    /**
     * The manifest information
     *
     * @var \SimpleXMLElement
     */
    public $manifest = null;

    /**
     * The config.xml information
     *
     * @var \SimpleXMLElement
     */
    public $config = null;

    /**
     * Class constructor, set the extension type.
     *
     * @param string  $namespace The element of the extension
     * @param string  $type      The type of extension
     * @param ?string $folder    The folder for plugins (only)
     * @param string  $basePath
     *
     * @return void
     */
    public function __construct(string $namespace, string $type, ?string $folder = '', string $basePath = JPATH_SITE)
    {
        $this->type      = $type;
        $this->element   = strtolower($namespace);
        $this->folder    = $folder;
        $this->basePath  = $basePath;
        $this->namespace = $namespace;

        $this->getManifest();

        $this->getDataFromDatabase();
    }

    /**
     * Get information about this extension from the database
     * NOTE: This is duplicated code from the corresponding class in
     * \Alledia\Framework\Joomla\Extension\Generic
     */
    protected function getDataFromDatabase()
    {
        $element = $this->getElementToDb();

        // Load the extension info from database
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('extension_id'),
                $db->quoteName('name'),
                $db->quoteName('enabled'),
                $db->quoteName('params')
            ])
            ->from('#__extensions')
            ->where($db->quoteName('type') . ' = ' . $db->quote($this->type))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element));

        if ($this->type === 'plugin') {
            $query->where($db->quoteName('folder') . ' = ' . $db->quote($this->folder));
        }

        $db->setQuery($query);
        $row = $db->loadObject();

        if (is_object($row)) {
            $this->id      = $row->extension_id;
            $this->name    = $row->name;
            $this->enabled = (bool)$row->enabled;
            $this->params  = new Registry($row->params);

        } else {
            $this->id      = null;
            $this->name    = null;
            $this->enabled = false;
            $this->params  = new Registry();

        }
    }

    /**
     * Check if the extension is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->enabled;
    }

    /**
     * Get the path for the extension
     *
     * @return string The path
     */
    public function getExtensionPath(): string
    {
        $folders = [
            'component' => 'administrator/components/',
            'plugin'    => 'plugins/',
            'template'  => 'templates/',
            'library'   => 'libraries/',
            'cli'       => 'cli/',
            'module'    => 'modules/'
        ];

        $basePath = $this->basePath . '/' . $folders[$this->type];

        switch ($this->type) {
            case 'plugin':
                $basePath .= $this->folder . '/';
                break;

            case 'module':
                if (!preg_match('/^mod_/', $this->element)) {
                    $basePath .= 'mod_';
                }
                break;

            case 'component':
                if (!preg_match('/^com_/', $this->element)) {
                    $basePath .= 'com_';
                }
                break;
        }

        $basePath .= $this->element;

        return $basePath;
    }

    /**
     * Get the full element
     *
     * @return string
     */
    public function getFullElement(): string
    {
        $prefixes = [
            'component' => 'com',
            'plugin'    => 'plg',
            'template'  => 'tpl',
            'library'   => 'lib',
            'cli'       => 'cli',
            'module'    => 'mod'
        ];

        $fullElement = $prefixes[$this->type];

        if ($this->type === 'plugin') {
            $fullElement .= '_' . $this->folder;
        }

        $fullElement .= '_' . $this->element;

        return $fullElement;
    }

    /**
     * Get the element to match the database records.
     * Only components and modules have the prefix.
     *
     * @return string The element
     */
    public function getElementToDb(): string
    {
        $prefixes = [
            'component' => 'com_',
            'module'    => 'mod_'
        ];

        $fullElement = '';
        if (array_key_exists($this->type, $prefixes)) {
            if (!preg_match('/^' . $prefixes[$this->type] . '/', $this->element)) {
                $fullElement = $prefixes[$this->type];
            }
        }

        $fullElement .= $this->element;

        return $fullElement;
    }

    /**
     * Get manifest path for this extension
     *
     * @return string
     */
    public function getManifestPath(): string
    {
        $extensionPath = $this->getExtensionPath();

        switch ($this->type) {
            case 'template':
                $fileName = 'templateDetails.xml';
                break;

            case 'library':
                $fileName = $this->element . '.xml';
                if (!is_file($extensionPath . '/' . $fileName)) {
                    $extensionPath = JPATH_MANIFESTS . '/libraries';
                }
                break;

            case 'module':
                $fileName = 'mod_' . $this->element . '.xml';
                break;

            case 'pkg':
                $extensionPath = JPATH_MANIFESTS . '/packages';
                $fileName      = 'pkg_' . $this->element . '.xml';
                break;

            case 'file':
                $extensionPath = JPATH_MANIFESTS . '/files';
                $fileName = 'file_' . $this->element . '.xml';
                break;

            default:
                $fileName = $this->element . '.xml';
                break;
        }

        return $extensionPath . '/' . $fileName;
    }

    /**
     * Get extension information
     *
     * @param bool $force If true, force to load the manifest, ignoring the cached one
     *
     * @return ?\SimpleXMLElement
     */
    public function getManifest(bool $force = false): ?\SimpleXMLElement
    {
        if ($this->manifest === null || $force) {
            $this->manifest = false;

            $path = $this->getManifestPath();
            if (is_file($path)) {
                $this->manifest = simplexml_load_file($path);
            }
        }

        return $this->manifest ?: null;
    }

    /**
     * Get extension config file
     *
     * @param bool $force Force to reload the config file
     *
     * @return \SimpleXMLElement
     */
    public function getConfig(bool $force = false)
    {
        if ($this->config === null || $force) {
            $path = $this->getExtensionPath() . '/config.xml';

            $this->config = is_file($path) ? simplexml_load_file($path) : false;
        }

        return $this->config;
    }

    /**
     * Returns the update URL from database
     *
     * @return string
     */
    public function getUpdateURL(): string
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('sites.location')
            ->from('#__update_sites AS sites')
            ->leftJoin('#__update_sites_extensions AS extensions ON (sites.update_site_id = extensions.update_site_id)')
            ->where('extensions.extension_id = ' . $this->id);

        return $db->setQuery($query)->loadResult();
    }

    /**
     * Set the update URL
     *
     * @param string $url
     */
    public function setUpdateURL(string $url)
    {
        $db = Factory::getDbo();

        // Get the update site id
        $join  = $db->quoteName('#__update_sites_extensions') . ' AS extensions '
            . 'ON (sites.update_site_id = extensions.update_site_id)';
        $query = $db->getQuery(true)
            ->select('sites.update_site_id')
            ->from($db->quoteName('#__update_sites') . ' AS sites')
            ->leftJoin($join)
            ->where('extensions.extension_id = ' . $this->id);
        $db->setQuery($query);
        $siteId = (int)$db->loadResult();

        if (!empty($siteId)) {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__update_sites'))
                ->set($db->quoteName('location') . ' = ' . $db->quote($url))
                ->where($db->quoteName('update_site_id') . ' = ' . $siteId);
            $db->setQuery($query);
            $db->execute();
        }
    }

    /**
     * Store the params on the database
     *
     * @return void
     */
    public function storeParams()
    {
        $db     = Factory::getDbo();
        $params = $db->quote($this->params->toString());
        $id     = $db->quote($this->id);

        $query = "UPDATE `#__extensions` SET params = {$params} WHERE extension_id = {$id}";
        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Get extension name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
