<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Helpers;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

class Settings
{
	/**
     * Get the value of a specific setting from the Tassos Framework plugin.
	 * 
	 * @param   string  $key      The key of the setting to retrieve.
	 * @param   string  $default  The default value to return if the setting is not found.
     * 
	 * @return  string  The value of the setting, or the default value if the setting is not found.
	 */
	public static function getValue($key = '', $default = '')
	{
		if (!$framework = PluginHelper::getPlugin('system', 'nrframework'))
		{
			return $this->defaultAPIKey;
		}
		
		// Get plugin params
		$params = new Registry($framework->params);
		return $params->get($key, $default);
	}
}