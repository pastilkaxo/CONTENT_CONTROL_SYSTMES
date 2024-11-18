<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('JPATH_BASE') or die();

use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Installer\Adapter\ModuleAdapter;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class NicepageInstallerModule
 */
class NicepageInstallerModule extends ModuleAdapter
{
    /**
     * @return string
     */
    public function getInstallType()
    {
        return strtolower($this->route);
    }

    /**
     * @param object $extension Extension object
     * @param object $iteminfo  Item info
     */
    public function localPostInstall($extension, $iteminfo)
    {
        if ($this->getInstallType() != 'update') {
            if (strtolower($this->route) == 'install') {
                // remove the auto installed module instance
                $this->removeInstances($extension->element);
            }

            foreach ($iteminfo->module as $moduleinfo) {
                $this->addInstance($extension->element, $moduleinfo);
            }
        }
    }

    /**
     * @param string $module_name Module name
     */
    protected function removeInstances($module_name)
    {
        $db = $this->parent->getDbo();

        // Lets delete all the module copies for the type we are uninstalling
        $query = 'SELECT id' .
            ' FROM #__modules' .
            ' WHERE module = ' . $db->quote($module_name);
        $db->setQuery($query);

        try {
            $modules = $db->loadColumn();
        } catch (Exception $e) {
            $modules = array();
        }

        // Do we have any module copies?
        if (count($modules)) {
            // Ensure the list is sane
            ArrayHelper::toInteger($modules);
            $modID = implode(',', $modules);

            // Wipe out any items assigned to menus
            $query = 'DELETE' .
                ' FROM #__modules_menu' .
                ' WHERE moduleid IN (' . $modID . ')';
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (Exception $e) {
                Factory::getApplication()->enqueueMessage(Text::sprintf('JLIB_INSTALLER_ERROR_MOD_UNINSTALL_EXCEPTION', $db->stderr(true)), 'warning');
            }

            // Wipe out any instances in the modules table
            $query = 'DELETE' .
                ' FROM #__modules' .
                ' WHERE id IN (' . $modID . ')';
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (Exception $e) {
                Factory::getApplication()->enqueueMessage(Text::sprintf('JLIB_INSTALLER_ERROR_MOD_UNINSTALL_EXCEPTION', $db->stderr(true)), 'warning');
            }
        }
    }

    /**
     * @param string $module_name Module name
     * @param array  $moduleInfo  Module info array
     */
    protected function addInstance($module_name, &$moduleInfo)
    {
        $db = $this->parent->getDbo();

        $module = Table::getInstance('module');

        $module->set('module', $module_name);

        if ($moduleInfo['title']) {
            $module->set('title', (string)$moduleInfo['title']);
        }

        if ($moduleInfo['position']) {
            $module->set('position', (string)$moduleInfo['position']);
        }

        if ($moduleInfo['access']) {
            $module->set('access', (int)$moduleInfo['access']);
        }

        if ($moduleInfo['ordering']) {
            $module->set('ordering', (int)$moduleInfo['ordering']);
        }

        $module->set('language', ($moduleInfo['language']) ? (string)$moduleInfo['language'] : '*');

        if ($moduleInfo['published']) {
            $published = (string)$moduleInfo['published'];

            switch (strtolower($published)) {
            case 'true':
                $published = 1;
                break;
            case 'false':
                $published = 0;
                break;
            default:
                $published = (int)$published;
                break;
            }

            $module->set('published', $published);
        }

        if ($moduleInfo['showtitle']) {
            $showtitle = (string)$moduleInfo['showtitle'];

            switch (strtolower($showtitle)) {
            case 'true':
                $showtitle = 1;
                break;
            case 'false':
                $showtitle = 0;
                break;
            default:
                $showtitle = (int)$showtitle;
                break;
            }

            $module->set('showtitle', $showtitle);
        }

        if ($moduleInfo['client']) {
            $client = (string)$moduleInfo['client'];

            switch ($client) {
            case 'site':
                $client_id = 0;
                break;
            case 'administrator':
                $client_id = 1;
                break;
            default:
                $client_id = (int)$client;
                break;
            }

            $module->set('client_id', $client_id);
        }

        $module->set('params', $moduleInfo->params ? (string)$moduleInfo->params : self::DEFAULT_PARAMS);

        if ($moduleInfo->content) {
            $module->set('content', (string)$moduleInfo->content);
        }

        if ($moduleInfo->note) {
            $module->set('note', (string)$moduleInfo->note);
        }

        $module->store();

        $module_id = $module->id;

        $query = $db->getQuery(true);

        if ($moduleInfo['assigned'] && strtolower((string)$moduleInfo['assigned']) == 'all') {
            $query->clear();
            $query->insert('#__modules_menu');
            $query->columns(array('moduleid', 'menuid'));
            $query->values((int)$module_id . ', 0');
            $db->setQuery((string)$query);
            $db->execute();
        }
    }

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
    const DEFAULT_PARAMS = '{}';

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
            $client = (int)$client;
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
     * @param string $enabled enabled value
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
            $enabled = (int)$enabled;
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
            $protected = (int)$protected;
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
     * @param int $extensionId Extension id
     */
    public function postInstall($extensionId)
    {
        if ($this->route) {
            $this->parent->setInstallType($this->route);
        }
        $iteminfo = $this->parent->getItemInfo();

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
