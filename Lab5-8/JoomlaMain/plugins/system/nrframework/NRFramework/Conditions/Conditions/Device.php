<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions;

defined('_JEXEC') or die;

use NRFramework\Conditions\Condition;
use Joomla\CMS\Language\Text;

class Device extends Condition
{
    /**
     *  Returns the assignment's value
     * 
     *  @return string Device type
     */
	public function value()
	{
		return $this->factory->getDevice();
	}

    /**
	 * A one-line text that describes the current value detected by the rule. Eg: The current time is %s.
    *
	 * @return string
	 */
	public function getValueHint()
	{
        return parent::getValueHint() . ' ' . Text::_('NR_ASSIGN_DEVICES_NOTE');
	}
}