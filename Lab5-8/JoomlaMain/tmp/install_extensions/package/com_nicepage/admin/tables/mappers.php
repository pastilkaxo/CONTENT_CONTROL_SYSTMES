<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;

/**
 * Class Nicepage_Data_CategoryMapper
 */
class Nicepage_Data_CategoryMapper extends Nicepage_Data_Mapper
{
    /**
     * Nicepage_Data_CategoryMapper constructor.
     */
    public function __construct()
    {
        parent::__construct('Category', 'categories', 'id');
    }

    /**
     * Find category row by filter
     *
     * @param array $filter Filter parameters
     *
     * @return array|void
     */
    public function find($filter = array())
    {
        $where = array();
        if (isset($filter['id'])) {
            $where[] = 'id = ' . intval($filter['id']);
        }
        if (isset($filter['extension'])) {
            $where[] = 'extension = ' . $this->_db->Quote($filter['extension']);
        }
        if (isset($filter['title'])) {
            $where[] = 'title = ' . $this->_db->Quote($filter['title']);
        }

        $result = $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0, true);
        return $result;
    }

    /**
     * Create raw category object
     *
     * @return bool|Table
     */
    public function create()
    {
        $row = $this->_create();
        $row->setLocation(1, 'last-child');
        $row->published = 1;
        $row->params = '{"category_layout":"","image":""}';
        $row->metadata = '{"author":"","robots":""}';
        $row->language = '*';
        $row->alias = '';
        return $row;
    }

    /**
     * Delete category object by id
     *
     * @param int $id Category id
     *
     * @return null|void
     */
    public function delete($id)
    {
        $status = $this->_cascadeDelete('content', array('category' => $id));
        if (is_string($status)) {
            return $this->_error($status, 1);
        }
        return parent::delete($id);
    }

    /**
     * Method to save category object
     *
     * @param object $category Category object
     *
     * @return null|void
     */
    public function save($category)
    {
        $status = parent::save($category);
        if (is_string($status)) {
            return $this->_error($status, 1);
        }
        if (!$category->rebuildPath($category->id)) {
            return $this->_error($category->getError(), 1);
        }
        if (!$category->rebuild($category->id, $category->lft, $category->level, $category->path)) {
            return $this->_error($category->getError(), 1);
        }
        return null;
    }
}

/**
 * Class Nicepage_Data_ContentMapper
 */
class Nicepage_Data_ContentMapper extends Nicepage_Data_Mapper
{
    /**
     * Nicepage_Data_ContentMapper constructor.
     */
    function __construct()
    {
        parent::__construct('content', 'content', 'id');
    }

    /**
     * Method to find content rows by filter
     *
     * @param array $filter Filter parameters
     *
     * @return array|void
     */
    function find($filter = array())
    {
        $where = array();
        if (isset($filter['id'])) {
            $where[] = 'id = ' . intval($filter['id']);
        }
        if (isset($filter['section'])) {
            $where[] = 'sectionid = ' . intval($filter['section']);
        }
        if (isset($filter['category'])) {
            $where[] = 'catid = ' . intval($filter['category']);
        }
        if (isset($filter['title'])) {
            $where[] = 'title = ' . $this->_db->Quote($this->_db->escape($filter['title'], true), false);
        }
        if (isset($filter['extra']) && is_array($filter['extra'])) {
            foreach ($filter['extra'] as $value) {
                $where[] = $value;
            }
        }
        if (isset($filter['alias'])) {
            $where[] = 'alias = ' . $this->_db->Quote($this->_db->escape($filter['alias'], true), false);
        }
        $result = $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0);
        return $result;
    }

    /**
     * Method to create raw content row
     *
     * @return bool|Table
     */
    function create()
    {
        $row = $this->_create();
        $row->state = '1';
        $row->version = '1';
        $row->language = '*';
        $row->fulltext = '';
        $row->created = Factory::getDate()->toSql();
        $row->publish_up = $row->created;
        return $row;
    }

    /**
     * Method to save row object
     *
     * @param object $row Row object
     *
     * @return null|void
     */
    function save($row)
    {
        $isNew = !$row->id ? true : false;
        if (!$row->check()) {
            return $this->_error($row->getError(), 1);
        }
        if (!$row->store()) {
            return $this->_error($row->getError(), 1);
        }
        $row->checkin();
        $row->reorder('catid = ' . (int)$row->catid . ' AND state >= 0');
        $cache = Factory::getCache('com_content');
        $cache->clean();
        if ($isNew) {
            $this->createAssociation($row->id);
        }
        return null;
    }

    /**
     * @param int $pk
     * @param int $state
     */
    function createAssociation($pk, $state = 1) {
        $extension = 'com_content.article';
        $query = $this->_db->getQuery(true);

        $query->insert($this->_db->quoteName('#__workflow_associations'))
            ->columns(
                [
                    $this->_db->quoteName('item_id'),
                    $this->_db->quoteName('stage_id'),
                    $this->_db->quoteName('extension'),
                ]
            )
            ->values(':pk, :state, :extension')
            ->bind(':pk', $pk, ParameterType::INTEGER)
            ->bind(':state', $state, ParameterType::INTEGER)
            ->bind(':extension', $extension);

        $this->_db->setQuery($query)->execute();
    }
}

/**
 * Class Nicepage_Data_ExtensionMapper
 */
class Nicepage_Data_ExtensionMapper extends Nicepage_Data_Mapper
{
    /**
     * Nicepage_Data_ExtensionMapper constructor.
     */
    function __construct()
    {
        parent::__construct('Extension', 'extensions', 'extension_id');
    }

    /**
     * Method to find extension rows by filter
     *
     * @param array $filter Filter parameters
     *
     * @return array|void
     */
    function find($filter = array())
    {
        $where = array();
        if (isset($filter['element'])) {
            $where[] = 'element = ' . $this->_db->Quote($this->_db->escape($filter['element'], true), false);
        }
        $result = $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0);
        return $result;
    }

    /**
     * Method to create raw extension row
     *
     * @return bool|Table
     */
    function create()
    {
        $row = $this->_create();
        return $row;
    }
}

/**
 * Class Nicepage_Data_Mapper
 */
class Nicepage_Data_Mapper
{
    /**
     * @var DatabaseDriver
     */
    protected $_db;

    /**
     * @var
     */
    protected $_entity;

    /**
     * @var Table name from db
     */
    protected $_table;

    /**
     * @var Table primary key
     */
    protected $_pk;

    /**
     * Nicepage_Data_Mapper constructor.
     *
     * @param string $entity Entity table
     * @param string $table  Table name
     * @param string $pk     Primary key value
     */
    public function __construct($entity, $table, $pk)
    {
        $this->_entity = $entity;
        $this->_table = $table;
        $this->_pk = $pk;
        $this->_db = Factory::getDBO();
    }

    /**
     * Check rows exists by filter
     *
     * @param array $filter Filter parameters
     *
     * @return bool|void
     */
    public function exists($filter = array())
    {
        $row = $this->findOne($filter);
        if (is_string($row)) {
            return $this->_error($row, 1);
        }
        return !is_null($row);
    }

    /**
     * Method to get one row by filter
     *
     * @param array $filter Filter parameters
     *
     * @return mixed|null|void
     */
    public function findOne($filter = array())
    {
        $filter['limit'] = 1;
        $list = $this->find($filter);
        if (is_string($list)) {
            return $this->_error($list, 1);
        }
        if (0 == count($list)) {
            $null = null;
            return $null;
        }
        return $list[0];
    }

    /**
     * Method to find results by filter
     *
     * @param array $filter Filter parameters
     *
     * @return array|void
     */
    public function find($filter = array())
    {
        $result = $this->_loadObjects();
        return $result;
    }

    /**
     * Method to fetch row by id
     *
     * @param int $id Row id
     *
     * @return bool|Table
     */
    public function fetch($id)
    {
        $row = Table::getInstance($this->_entity);
        $row->load($id);
        return $row;
    }

    /**
     * Method to delete row by id
     *
     * @param int $id Row id
     *
     * @return null|void
     */
    public function delete($id)
    {
        $row = $this->fetch($id);
        if (!$row->delete($id)) {
            return $this->_error($row->getError(), 1);
        }
        return null;
    }

    /**
     * Method to save row object
     *
     * @param object $row Row object
     *
     * @return null|void
     */
    public function save($row)
    {
        if (!$row->check()) {
            return $this->_error($row->getError(), 1);
        }
        if (!$row->store()) {
            return $this->_error($row->getError(), 1);
        }
        if (!$row->checkin()) {
            return $this->_error($row->getError(), 1);
        }
        return null;
    }

    /**
     * Method to create raw object
     *
     * @return bool|Table
     */
    protected function _create()
    {
        $result = Table::getInstance($this->_entity);
        return $result;
    }

    /**
     * Method to load objects by parameters
     *
     * @param array $where Custom parameters
     * @param int   $limit Count rows
     *
     * @return array|void
     */
    protected function _loadObjects($where = array(), $limit = 0)
    {
        $query = 'SELECT * FROM #__' . $this->_table
            . (count($where) ? ' WHERE ' . implode(' AND ', $where) : '')
            . ' ORDER BY ' . $this->_pk;
        $this->_db->setQuery($query, 0, $limit);

        try {
            $rows = $this->_db->loadAssocList();
        } catch (Exception $e) {
            return $this->_error($e->getMessage(), 1);
        }

        $result = array();
        for ($i = 0; $i < count($rows); $i++) {
            $result[$i] = Table::getInstance($this->_entity);
            $result[$i]->bind($rows[$i]);
        }
        return $result;
    }

    /**
     * Cascading delete rows by filter
     *
     * @param string $mapper Mapper name
     * @param array  $filter Filter parameters
     *
     * @return null|void
     */
    protected function _cascadeDelete($mapper, $filter)
    {
        $menuItems = Nicepage_Data_Mappers::get($mapper);
        $itemsList = $menuItems->find($filter);
        if (is_string($itemsList)) {
            return $this->_error($itemsList, 1);
        }
        foreach ($itemsList as $item) {
            $status = $menuItems->delete($item->id);
            if (is_string($status)) {
                return $this->_error($status, 1);
            }
        }
        return null;
    }

    /**
     * Create Nicepage_Data_Mappers error
     *
     * @param string $error Error text
     * @param int    $code  Number code
     */
    protected function _error($error, $code)
    {
        Nicepage_Data_Mappers::error($error, $code);
    }
}

/**
 * Class Nicepage_Data_Mappers
 */
class Nicepage_Data_Mappers
{
    /**
     *  Callback error function
     *
     * @param callable $callback Callback function
     * @param bool     $get      Flag parameter
     *
     * @return mixed
     */
    public static function errorCallback($callback, $get = false)
    {
        static $errorCallback;
        if (!$get) {
            $errorCallback = $callback;
        }
        return $errorCallback;
    }

    /**
     * Method to get mapper object by name
     *
     * @param string $name Mapper name
     *
     * @return mixed
     */
    public static function get($name)
    {
        $className = 'Nicepage_Data_' . ucfirst($name) . 'Mapper';
        $mapper = new $className();
        return $mapper;
    }

    /**
     * Method to create error
     *
     * @param string $error Error text
     * @param int    $code  Number code
     *
     * @return mixed
     */
    public static function error($error, $code)
    {
        $null = null;
        $callback = Nicepage_Data_Mappers::errorCallback($null, true);
        if (isset($callback)) {
            call_user_func($callback, $error, $code);
        }
        return $error;
    }
}

/**
 * Class Nicepage_Data_ModuleMapper
 */
class Nicepage_Data_ModuleMapper extends Nicepage_Data_Mapper
{
    /**
     * Nicepage_Data_ModuleMapper constructor.
     */
    function __construct()
    {
        parent::__construct('module', 'modules', 'id');
    }

    /**
     * Method to find module rows by filter
     *
     * @param array $filter Filtering parameters
     *
     * @return array|void
     */
    function find($filter = array())
    {
        $where = array();
        if (isset($filter['published'])) {
            $where[] = 'published = ' . $this->_db->Quote($filter['published'], false);
        }
        if (isset($filter['module'])) {
            $where[] = 'module = ' . $this->_db->Quote($filter['module'], false);
        }
        if (isset($filter['position'])) {
            $where[] = 'position = ' . $this->_db->Quote($filter['position'], false);
        }
        if (isset($filter['title'])) {
            $where[] = 'title = ' . $this->_db->Quote($this->_db->escape($filter['title'], true), false);
        }
        if (isset($filter['scope']) && ('site' == $filter['scope'] || 'administrator' == $filter['scope'])) {
            $where[] = 'client_id = ' . ('site' == $filter['scope'] ? '0' : '1');
        }
        $result = $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0);
        return $result;
    }

    /**
     * Method to fetch row by id
     *
     * @param int $id Row id
     *
     * @return bool|Table
     */
    function fetch($id)
    {
        $result = parent::fetch($id);
        return $result;
    }

    /**
     * Delete module object by id
     *
     * @param int $id Module id
     *
     * @return null|void
     */
    function delete($id)
    {
        $status = $this->enableOn($id, array());
        if (is_string($status)) {
            return $status;
        }
        return parent::delete($id);
    }

    /**
     * Method to create raw module raw
     *
     * @return bool|Table
     */
    function create()
    {
        $row = $this->_create();
        $row->published = 1;
        $row->language = '*';
        $row->showtitle = 1;
        return $row;
    }

    /**
     * Method to enable module for custom menut items
     *
     * @param int   $id    module id
     * @param array $items Array of menu items
     *
     * @return null|void
     */
    function enableOn($id, $items)
    {
        $query = 'DELETE FROM #__modules_menu WHERE moduleid = ' . $this->_db->Quote($id);
        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (Exception $e) {
            return $this->_error($e->getMessage(), 1);
        }

        foreach ($items as $i) {
            $query = 'INSERT INTO #__modules_menu (moduleid, menuid) VALUES ('
                . $this->_db->Quote($id) . ',' . $this->_db->Quote($i) . ')';
            $this->_db->setQuery($query);
            try {
                $this->_db->execute();
            } catch (Exception $e) {
                return $this->_error($e->getMessage(), 1);
            }
        }
        return null;
    }

    /**
     * Method to disable module for custom menut items
     *
     * @param int   $id    module id
     * @param array $items Array of menu items
     *
     * @return null|void
     */
    function disableOn($id, $items)
    {
        $query = 'DELETE FROM #__modules_menu WHERE moduleid = ' . $this->_db->Quote($id);
        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (Exception $e) {
            return $this->_error($e->getMessage(), 1);
        }

        foreach ($items as $i) {
            $query = 'INSERT INTO #__modules_menu (moduleid, menuid) VALUES ('
                . $this->_db->Quote($id) . ',' . $this->_db->Quote('-' . $i) . ')';
            $this->_db->setQuery($query);
            try {
                $this->_db->execute();
            } catch (Exception $e) {
                return $this->_error($e->getMessage(), 1);
            }
        }
        return null;
    }

    /**
     * Method to get assigment menut items by module id
     *
     * @param int $id
     */
    function getAssignment($id)
    {
        $query = 'SELECT menuid FROM #__modules_menu WHERE moduleid = ' . $this->_db->Quote($id);
        $this->_db->setQuery($query);
        try {
            $this->_db->execute();
            $rows = $this->_db->loadColumn(0);
        } catch (Exception $e) {
            return $this->_error($e->getMessage(), 1);
        }
        return $rows;
    }
}

/**
 * Class Nicepage_Data_MenuMapper
 */
class Nicepage_Data_MenuMapper extends Nicepage_Data_Mapper
{
    /**
     * Nicepage_Data_MenuMapper constructor.
     */
    function __construct()
    {
        parent::__construct('MenuType', 'menu_types', 'id');
    }

    /**
     * Method to find menu rows by filter
     *
     * @param array $filter Filtering parameters
     *
     * @return array|void
     */
    function find($filter = array())
    {
        $where = array();
        if (isset($filter['title'])) {
            $where[] = 'title = ' . $this->_db->Quote($this->_db->escape($filter['title'], true), false);
        }
        $result = $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0);
        return $result;
    }

    /**
     * Method to create raw menu row
     *
     * @return bool|Table
     */
    function create()
    {
        $row = $this->_create();
        return $row;
    }

    /**
     * Delete menu object by id
     *
     * @param int $id Menu id
     *
     * @return null|void
     */
    function delete($id)
    {
        // Delete related records in the modules_menu table.

        // Start with checking whether this menu exists:
        $menu = $this->fetch($id);
        if (is_string($menu)) {
            return $this->_error($menu, 1);
        }

        // Get the menu:
        $this->_db->setQuery('SELECT menutype FROM #__menu_types WHERE id=' . $this->_db->Quote($id));
        try {
            $menutype = $this->_db->loadResult();
        } catch (Exception $e) {
            return $this->_error($e->getMessage(), 1);
        }
        if (is_string($menutype)) {
            // Select items for the specified menu:
            $this->_db->setQuery('SELECT id FROM #__menu WHERE menutype=' . $this->_db->Quote($menutype) . ' ORDER BY id');
            try {
                $items = $this->_db->loadColumn(0);
            } catch (Exception $e) {
                return $this->_error($e->getMessage(), 1);
            }

            $items = array_map('intval', $items);

            if (0 < count($items)) {
                // Delete "Only on the pages selected" assignments:
                $this->_db->setQuery('DELETE FROM #__modules_menu WHERE menuid in (' . implode(',', $items) . ')');
                try {
                    $this->_db->execute();
                } catch (Exception $e) {
                    return $this->_error($e->getMessage(), 1);
                }

                // Invert items:
                for ($i = 0, $limit = count($items); $i < $limit; $i++) {
                    $items[$i] = -$items[$i];
                }

                // Get the modules that are not shown on the menu items that are about to be deleted:
                $this->_db->setQuery('SELECT moduleid FROM #__modules_menu WHERE menuid in (' . implode(',', $items) . ')');
                try {
                    $modules = $this->_db->loadColumn(0);
                } catch (Exception $e) {
                    return $this->_error($e->getMessage(), 1);
                }

                $modules = array_unique($modules);

                // delete "On all pages except those selected" assignment:
                $this->_db->setQuery('DELETE FROM #__modules_menu WHERE menuid in (' . implode(',', $items) . ')');
                try {
                    $this->_db->execute();
                } catch (Exception $e) {
                    return $this->_error($e->getMessage(), 1);
                }

                // restore modules "On all pages" state:
                foreach ($modules as $module) {
                    $this->_db->setQuery('SELECT COUNT(*) FROM #__modules_menu WHERE moduleid=' . $this->_db->Quote($module));
                    try {
                        $count = (int)$this->_db->loadResult();
                    } catch (Exception $e) {
                        return $this->_error($e->getMessage(), 1);
                    }

                    if (0 == $count) {
                        $this->_db->setQuery('INSERT INTO #__modules_menu (moduleid, menuid) VALUES (' . $this->_db->Quote($module) . ', 0)');
                        try {
                            $this->_db->execute();
                        } catch (Exception $e) {
                            return $this->_error($e->getMessage(), 1);
                        }
                    }
                }
            }
        }
        return parent::delete($id);
    }
}

/**
 * Class Nicepage_Data_MenuItemMapper
 */
class Nicepage_Data_MenuItemMapper extends Nicepage_Data_Mapper
{
    /**
     * Nicepage_Data_MenuItemMapper constructor.
     */
    function __construct()
    {
        parent::__construct('Menu', 'menu', 'id');
    }

    /**
     * Method to find menu item rows by filter
     *
     * @param array $filter Filtering parameters
     *
     * @return array|void
     */
    function find($filter = array())
    {
        $where = array();
        if (isset($filter['menu'])) {
            $where[] = 'menutype = ' . $this->_db->Quote($filter['menu']);
        }
        if (isset($filter['title'])) {
            $where[] = 'title = ' . $this->_db->Quote($filter['title']);
        }
        if (isset($filter['home'])) {
            $where[] = 'home = ' . $this->_db->Quote($filter['home']);
        }
        if (isset($filter['language'])) {
            $where[] = 'language = ' . $this->_db->Quote($filter['language']);
        }
        if (isset($filter['scope']) && ('site' == $filter['scope'] || 'administrator' == $filter['scope'])) {
            $where[] = 'client_id = ' . ('site' == $filter['scope'] ? '0' : '1');
        }
        $result = $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0);
        return $result;
    }

    /**
     * Method to create raw menu item row
     *
     * @return bool|Table
     */
    function create()
    {
        $row = $this->_create();
        $row->published = '1';
        $row->access = 1;
        $row->language = '*';
        $row->setLocation(1, 'last-child');
        return $row;
    }
}

