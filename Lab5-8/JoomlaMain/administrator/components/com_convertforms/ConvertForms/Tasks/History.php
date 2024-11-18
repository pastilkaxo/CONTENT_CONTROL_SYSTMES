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

class History
{
    public static function log($app, $app_trigger, $event, $success, $error, $ref_type, $ref_id)
    {
        $db = Factory::getDbo();

        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__convertforms_tasks_history'))
            ->columns($db->quoteName(['app', 'app_trigger', 'event', 'success', 'error', 'ref_type', 'ref_id']))
            ->values(implode(',', [$app, $app_trigger, $event, $success, $error, $ref_type, $ref_id]));

        $db->setQuery($query);
        $db->execute();
    }
}