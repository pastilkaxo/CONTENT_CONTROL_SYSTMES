<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;

/**
 * Item Model Class
 */
class GSDModelItem extends AdminModel
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      Table  A database object
     * @since       2.5
     */
    public function getTable($type = 'Item', $prefix = 'GSDTable', $config = array()) 
    {
        return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Method to allow derived classes to preprocess the form.
     *
     * @param   Form   $form   A Form object.
     * @param   mixed   $data   The data expected for the form.
     * @param   string  $group  The name of the plugin group to import (defaults to "content").
     *
     * @return  void
     *
     * @see     FormField
     * @since   1.6
     * @throws  Exception if there is an error in the form event.
     */
    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        // Add some useful field paths
        $form->addFieldPath(__DIR__ . '/forms/fields');
        $form->addFieldPath(JPATH_PLUGINS . '/system/nrframework/fields');
        $form->addFieldPath(JPATH_PLUGINS . '/system/nrframework/NRFramework/Fields');

        // When item is not saved yet, the $data variable is type of Array.
        $tempData = (object) $data;

        if (isset($tempData->contenttype))
        {
            $form->loadFile(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/contenttypes/' . $tempData->contenttype . '.xml');
        }

        parent::preprocessForm($form, $data, $group);
    }

    /**
     * Method to get the record form.
     *
     * @param       array   $data           Data for the form.
     * @param       boolean $loadData       True if the form is to load its own data (default case), false if not.
     * @return      mixed   A Form object on success, false on failure
     * @since       2.5
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_gsd.item', 'item', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) 
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();

        // Check the session for previously entered form data.
        $data = $app->getUserState('com_gsd.edit.item.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to validate form data.
     */
    public function validate($form, $data, $group = null)
    {
        // Keep first 100 characters, based on db schema.
        if (strlen($data['title']) > 100)
        {
            $data['title'] = mb_substr($data['title'], 0, 100);
        }

        $newdata = array();
        $params  = array();

        $this->_db->setQuery('SHOW COLUMNS FROM #__gsd');

        $dbkeys = $this->_db->loadObjectList('Field');
        $dbkeys = array_keys($dbkeys);

        foreach ($data as $key => $val)
        {
            if (in_array($key, $dbkeys))
            {
                $newdata[$key] = $val;
            }
            else
            {
                $params[$key] = $val;
            }
        }

        if (!isset($newdata['params']))
        {
            $newdata['params'] = json_encode($params);
        }

        return $newdata;
    }

    /**
     * Remove timezone offset from all Date properties and store them in UTC in the database. 
     * 
     * @todo Consider this as a temporary workaround. We should create classes per content type and let each class manipulate their data independently.
     *
     * @param   array  The form data.
     *
     * @return  boolean  True on success.
     */
    public function save($data)
    {
        if (!isset($data['contenttype']) && empty($data['contenttype']))
        {
            return parent::save($data);
        }

        // Let schema validate its properties
        $params = json_decode($data['params'], true);

        \GSD\Schemas\Helper::getInstance($data['contenttype'])->onSave($params[$data['contenttype']]);
        
        $data['params'] = json_encode($params);

        return parent::save($data);
    }

    /**
     *  [getItem description]
     *
     *  @param   [type]  $pk  [description]
     *
     *  @return  [type]       [description]
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk))
        {
            $params = $item->params;

            if (is_array($params) && count($params))
            {
                foreach ($params as $key => $value)
                {
                    if (!isset($item->$key) && !is_object($value))
                    {
                        $item->$key = $value;
                    }
                }

                unset($item->params);
            }
        }

        $input = Factory::getApplication()->input;

        if ($input->get('override_item'))
        {
            $item->title       = $input->getString('title');
            $item->contenttype = $input->get('contenttype');
            $item->plugin      = $input->get('plugin');
            $item->assignments = $input->get('assignments', '', 'Array');
        }

        return $item;
    }

    /**
     * Method to copy an item
     *
     * @access    public
     * @return    boolean    True on success
     */
    public function copy($id)
    {
        $item = $this->getItem($id);

        $item->id = 0;
        $item->state = 0;
        
        // Keep first 100 characters of title on export, based on db schema
        $item->title = mb_substr($item->title, 0, 50);

        $item = $this->validate(null, get_object_vars($item));

        return ($this->save($item));
    }
}

