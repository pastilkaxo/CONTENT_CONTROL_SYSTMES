<?php

/**
 * @author          Tassos.gr
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class RSEventsProSingle extends RSEventsProBase
{
    /**
     * Shortcode aliases for this Condition
     */
    public static $shortcode_aliases = ['rseventspro.single'];

    /**
	 *  Pass check
	 *
	 *  @return  bool
	 */
	public function pass()
	{
        return $this->passSinglePage();
    }
    
    /**
     *  Returns the assignment's value
     * 
     *  @return int
     */
    public function value()
    {
        return $this->request->id;
    }
}