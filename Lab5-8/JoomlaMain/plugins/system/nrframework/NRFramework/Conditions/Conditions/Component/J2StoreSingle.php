<?php

/**
 * @author          Tassos.gr
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class J2StoreSingle extends J2StoreBase
{
    /**
     * Shortcode aliases for this Condition
     */
    public static $shortcode_aliases = ['j2storesingle'];

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