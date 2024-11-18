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

class ModelTasksHistory
{
    /**
     * Get the Tasks History table
     *
     * @return Table
     */
    public static function getTable()
    {
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/tables');
        $table = Table::getInstance('TaskHistory', 'ConvertFormsTable');

        return $table;
    }

    /**
     * Add a new record in the tabler
     *
     * @param [type] $data
     * @return void
     */
    public static function add($data)
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