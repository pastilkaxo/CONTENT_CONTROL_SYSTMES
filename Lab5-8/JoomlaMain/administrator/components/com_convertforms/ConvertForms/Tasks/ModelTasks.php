<?php

/**
 * @package         Convert Forms
 * @version         4.4.6 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms\Tasks;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

class ModelTasks
{
    public static function getTable()
    {
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/tables');
        $table = Table::getInstance('Task', 'ConvertFormsTable');

        return $table;
    }

    public static function getItems($form_id, $assocByID = false, $mergeOptionsWithConditions = false)
    {
        $db = Factory::getDbo();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__convertforms_tasks'))
            ->where($db->quoteName('form_id') . ' = ' . $form_id)
            ->order($db->quoteName('ordering'));

        $db->setQuery($query);

        $items = $db->loadAssocList($assocByID ? 'id' : null);

        foreach ($items as &$item)
        {
            $item['options']    = json_decode($item['options'], true);
            $item['conditions'] = json_decode($item['conditions'], true);

            if ($mergeOptionsWithConditions)
            {
                $item['options']['conditions'] = $item['conditions'];
                unset($item['conditions']);
            }

            // Joomla 3 returns it as string which breaks the Switch component.
            $item['state'] = (bool) $item['state'];
            $item['silentfail'] = (bool) $item['silentfail'];

            unset($item['created']);
            unset($item['created_by']);
            unset($item['modified']);
        }

        return $items;
    }

    public static function save($data)
    {
        $table = self::getTable();

        if (!$table->bind($data))
        {
            throw new \Exception($table->getError());
        }

        if (!$table->check())
        {
            throw new \Exception($table->getError());
        }

        if (!$table->store())
        {
            throw new \Exception($table->getError());
        }
    }
}