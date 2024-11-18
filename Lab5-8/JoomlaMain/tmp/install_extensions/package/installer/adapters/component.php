<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('JPATH_BASE') or die();

use Joomla\CMS\Table\Table;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;

/**
 * Class NicepageInstallerComponent
 */
class NicepageInstallerComponent extends ComponentAdapter
{
    /**
     * @var string
     */
    protected $installtype = 'install';

    /**
     * Update component method
     *
     * @return mixed
     */
    public function update()
    {
        $this->installtype = 'update';

        return parent::update();
    }

    /**
     * Get install type
     *
     * @return string
     */
    public function getInstallType()
    {
        if (version_compare(JVERSION, '3.4', '<')) {
            return $this->installtype;
        } else {
            return $this->route;
        }
    }

    /**
     * Local post template
     *
     * @param object $extension Extension
     * @param object $iteminfo  Item info
     */
    protected function localPostInstall($extension, $iteminfo)
    {

    }

    /**
     * Component access option
     *
     * @var
     */
    protected $access;

    /**
     * Component enabled option
     *
     * @var
     */
    protected $enabled;

    /**
     * Component client option
     *
     * @var
     */
    protected $client;

    /**
     * Component ordering option
     *
     * @var
     */
    protected $ordering;

    /**
     * Component protected option
     *
     * @var
     */
    protected $protected;

    /**
     * Component params option
     *
     * @var
     */
    protected $params;

    /**
     * Default access value
     */
    const DEFAULT_ACCESS = 1;

    /**
     * Default enabled value
     */
    const DEFAULT_ENABLED = 'true';

    /**
     * Default protected value
     */
    const DEFAULT_PROTECTED = 'false';

    /**
     * Default client value
     */
    const DEFAULT_CLIENT = 'site';

    /**
     * Default ordering value
     */
    const DEFAULT_ORDERING = 0;

    /**
     * Default params value
     */
    const DEFAULT_PARAMS = null;

    /**
     * Set access method
     *
     * @param string $access Access value
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * Get access method
     *
     * @return mixed
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Set client method
     *
     * @param string $client Client value
     */
    public function setClient($client)
    {
        switch (strtolower($client)) {
        case 'site':
            $client = 0;
            break;
        case 'administrator':
            $client = 1;
            break;
        default:
            $client = (int) $client;
            break;
        }
        $this->client = $client;
    }

    /**
     * Get access method
     *
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set enabled method
     *
     * @param string $enabled Enabled value
     */
    public function setEnabled($enabled)
    {
        switch (strtolower($enabled)) {
        case 'true':
            $enabled = 1;
            break;
        case 'false':
            $enabled = 0;
            break;
        default:
            $enabled = (int) $enabled;
            break;
        }
        $this->enabled = $enabled;
    }

    /**
     * Get enabled method
     *
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set ordering method
     *
     * @param string $ordering Ordering value
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }

    /**
     * Get ordering method
     *
     * @return mixed
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Set protected method
     *
     * @param string $protected Protected value
     */
    public function setProtected($protected)
    {
        switch (strtolower($protected)) {
        case 'true':
            $protected = 1;
            break;
        case 'false':
            $protected = 0;
            break;
        default:
            $protected = (int) $protected;
            break;
        }
        $this->protected = $protected;
    }

    /**
     * Get protected method
     *
     * @return mixed
     */
    public function getProtected()
    {
        return $this->protected;
    }

    /**
     * Set params method
     *
     * @param array $params Parameters list
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Get params method
     *
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Method to update extension
     *
     * @param object $extension Extension object
     */
    protected function updateExtension($extension)
    {
        if ($extension) {
            $extension->access = $this->access;
            $extension->enabled = $this->enabled;
            $extension->protected = $this->protected;
            $extension->client_id = $this->client;
            $extension->ordering = $this->ordering;
            $extension->params = $this->params;
            $extension->state = 0;
            $extension->store();
        }
    }

    /**
     * Install component method
     *
     * @return mixed
     */
    public function install()
    {
        $result = parent::install();

        if ($result !== false) {
            $this->postInstall($result);
        }

        return $result;
    }

    /**
     * Post install actions
     *
     * @param int $extensionId Extension id
     */
    public function postInstall($extensionId)
    {
        if ($this->route) {
            $this->parent->setInstallType($this->route);
        }
        $iteminfo = $this->parent->getiteminfo();

        $this->setAccess(($iteminfo['access']) ? (int)$iteminfo['access'] : self::DEFAULT_ACCESS);
        $this->setEnabled(($iteminfo['enabled']) ? (string)$iteminfo['enabled'] : self::DEFAULT_ENABLED);
        $this->setProtected(($iteminfo['protected']) ? (string)$iteminfo['protected'] : self::DEFAULT_PROTECTED);
        $this->setClient(($iteminfo['client']) ? (string)$iteminfo['client'] : self::DEFAULT_CLIENT);
        $this->setParams(($iteminfo->params) ? (string)$iteminfo->params : self::DEFAULT_PARAMS);
        $this->setOrdering(($iteminfo['ordering']) ? (int)$iteminfo['ordering'] : self::DEFAULT_ORDERING);

        $extension = $this->loadExtension($extensionId);

        // update the extension info
        $this->updateExtension($extension);

        $this->localPostInstall($extension, $iteminfo);
    }

    /**
     * Load extension
     *
     * @param int $extensionId Extension id
     *
     * @return mixed
     */
    protected function loadExtension($extensionId)
    {
        $row = Table::getInstance('extension');
        $row->load($extensionId);

        if (!$row->extension_id) {
            throw new RuntimeException("Internal error in Joomla installer: extension {$extensionId} not found!");
        }

        return $row;
    }
}
