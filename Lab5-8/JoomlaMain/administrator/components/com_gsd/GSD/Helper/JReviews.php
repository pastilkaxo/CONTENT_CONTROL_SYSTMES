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

namespace GSD\Helper;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

class JReviews
{
    public static function getListing($id)
    {
        try {
            $db = Factory::getDbo();

            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__jreviews_content')
                ->where('contentid = ' . $db->q($id));
    
            $db->setQuery($query);
    
            return $db->loadAssoc();
        } catch (\Exception $exception) {}

        return null;
    }
}