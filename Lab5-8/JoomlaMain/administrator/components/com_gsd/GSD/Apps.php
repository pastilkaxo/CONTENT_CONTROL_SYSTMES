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

namespace GSD;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

class Apps
{
	public static function getApp($name, $data = null)
	{
		if (!$plugin = PluginHelper::getPlugin('gsd', $name))
		{
			throw new \RuntimeException(Text::sprintf('GSD_PLUGIN_NOT_FOUND', $name));
		}

		// On Joomla 4, use bootPlugin()
		if (defined('nrJ4'))
		{
			$app = Factory::getApplication()->bootPlugin($plugin->name, $plugin->type);

		} else 
		{
			// On Joomla 3, use the old classic way to boot up a plugin
			// TODO: Remove when J3 support is dropped
			$name = 'plg' . $plugin->type . $plugin->name;
	
			require_once JPATH_PLUGINS . '/gsd/' . $plugin->name . '/' . $plugin->name . '.php';
	
			$dispatcher = \JEventDispatcher::getInstance();

			$app = new $name($dispatcher, (array) $plugin);
		}

		return $app;
	}
}