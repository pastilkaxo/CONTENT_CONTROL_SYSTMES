<?php

/**
 * @author          Tassos.gr
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class JBusinessDirectoryEventCategory extends JBusinessDirectoryEventBase
{
/**
     * Shortcode aliases for this Condition
     */
    public static $shortcode_aliases = ['jbuisnessdirectory.event_category'];

    /**
     *  Pass check
     *
     *  @return bool
     */
    public function pass()
    {
        return $this->passCategories('jbusinessdirectory_categories', 'parent_id');
	}
}