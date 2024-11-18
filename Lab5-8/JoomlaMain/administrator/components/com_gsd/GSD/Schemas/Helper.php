<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD\Schemas;

// No direct access
defined('_JEXEC') or die;

use Joomla\Filesystem\Folder;

class Helper
{
    /**
     * Bootup a schema type class instance
     *
     * @param   string      $type   The name of the schema, eg: article, product
     * @param   Registry   $data   The schema properties
     * 
     * @return  object
     */
    public static function getInstance($type, $data = null)
    {
        $classPath = '\\GSD\\Schemas\\Schemas\\';
        $type = strtolower($type);

        // Try to find the class using the given name
        $className = $classPath . ucfirst($type);

        if (!class_exists($className))
        {
            // Try to find the class by searching all files in the file system
            $files = Folder::files(__DIR__ . '/Schemas');
    
            foreach ($files as $file)
            {
                $fileStripExt = str_replace('.php', '', $file);
    
                if (strtolower($fileStripExt) == $type)
                {
                    $className = $classPath . $fileStripExt;
                    break;
                }
            }
        }

        return new $className($data);
    }
}