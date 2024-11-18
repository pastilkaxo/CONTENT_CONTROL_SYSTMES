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

class PagesTableSections extends Table
{
    /**
     * Constructor
     *
     * @param DatabaseDriver $db Database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__nicepage_sections', 'id', $db);
    }

    /**
     * Load page object
     *
     * @param null $keys  Filter keys
     * @param bool $reset Reset flag
     *
     * @return bool
     */
    public function load($keys = null, $reset = true) {
        $result = parent::load($keys, $reset);
        if ($result) {
            $this->_decodeProps();
        }
        return $result;
    }

    /**
     * Get page props
     */
    public function getProps() {
        $props = $this->props;
        $app = Factory::getApplication();
        $isPreview = $app->input->getBool('isPreview', false);
        if ($isPreview && $this->preview_props !== '') {
            $props = $this->preview_props;
        }
        $props['isPreview'] = $isPreview;
        $props['pageId'] = $this->page_id;
        return $props;
    }

    /**
     * Decode page props
     */
    private function _decodeProps() {
        $this->props = unserialize(call_user_func('base' . '64_decode', $this->props));

        if ($this->preview_props) {
            $data = @unserialize($this->preview_props);
            $this->preview_props = $data !== false ? $data : '';
        } else {
            if (isset($this->props['preview'])) {
                $this->preview_props = $this->props['preview'];
                unset($this->props['preview']); // remove depracted property
            }
        }

        if ($this->autosave_props) {
            $data = @unserialize($this->autosave_props);
            $this->autosave_props = $data !== false ? $data : '';
        } else {
            if (isset($this->props['autosave'])) {
                $this->autosave_props = $this->props['autosave'];
                unset($this->props['autosave']); // remove depracted property
            }
        }
    }

    /**
     * Save page object
     *
     * @param array|object $src            Page options
     * @param string       $orderingFilter Filter
     * @param string       $ignore         Ignore
     *
     * @return mixed
     */
    public function save($src, $orderingFilter = '', $ignore = '') {
        return parent::save($this->_encodeProps($src), $orderingFilter, $ignore);
    }

    /**
     * Encode page props
     *
     * @param array $src Page options
     *
     * @return array
     */
    private function _encodeProps($src) {
        if (is_array($src)) {
            if (array_key_exists('props', $src)) {
                $src['props'] = call_user_func('base' . '64_encode', serialize($src['props']));
            }
            if (array_key_exists('preview_props', $src) && $src['preview_props']) {
                $src['preview_props'] = serialize($src['preview_props']);
            }
            if (array_key_exists('autosave_props', $src) && $src['autosave_props']) {
                $src['autosave_props'] = serialize($src['autosave_props']);
            }
        }
        return $src;
    }

    /**
     * Get all articles ids
     *
     * @return mixed
     */
    public function getAllPageIds()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('page_id')
            ->from('#__nicepage_sections');
        $db->setQuery($query);
        return $db->loadAssocList(null, 'page_id');
    }

    /**
     * Update id after page duplicating
     *
     * @param int $id Page id
     */
    public function updateDuplicatedPageId($id)
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->update('#__nicepage_sections');
        $query->set($db->quoteName('page_id') . '=' . $id);
        $query->where('page_id= 1000000');
        $db->setQuery($query);
        $db->execute();
    }
}