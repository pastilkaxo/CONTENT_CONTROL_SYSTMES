<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('JPATH_BASE') or die();

use Joomla\CMS\Table\Table;
use Joomla\CMS\Installer\Adapter\PluginAdapter;

class NicepageInstallerPlugin extends PluginAdapter
{
    /**
     * @var
     */
    protected $access;

    /**
     * @var
     */
    protected $enabled;

    /**
     * @var
     */
    protected $client;

    /**
     * @var
     */
    protected $ordering;

    /**
     * @var
     */
    protected $protected;

    /**
     * @var
     */
    protected $params;

    /*
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
     * Defauilt params value
     */
    const DEFAULT_PARAMS = null;

    /**
     * @param string $access Access value
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * @return mixed
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param string $client Client id
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
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
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
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param string $ordering Ordering value
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }

    /**
     * @return mixed
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
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
     * @return mixed
     */
    public function getProtected()
    {
        return $this->protected;
    }

    /**
     * @param array $params Parameters list
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
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
     * @return mixed
     */
    public function getInstallType()
    {
        return $this->route;
    }

    /**
     * @param int $extensionId Extension id
     */
    public function postInstall($extensionId)
    {
        if ($this->route) {
            $this->parent->setInstallType($this->route);
        }

        $iteminfo = $this->parent->getIteminfo();

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
     * @param object $extension Extension
     * @param array  $iteminfo  Item info
     */
    protected function localPostInstall($extension, $iteminfo)
    {

    }

    /**
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
