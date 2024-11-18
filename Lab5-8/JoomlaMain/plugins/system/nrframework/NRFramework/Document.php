<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class Document
{
	/**
	 * A cross Joomla compatible method to inject inline script as module
	 *
	 * @param  string $script	The inline script to load as module (defer)
	 * 
	 * @return void
	 */
	static public function addInlineScriptDefer($script)
	{
		$doc = Factory::getApplication()->getDocument();

		if (defined('nrJ4'))
		{
			// Joomla => 4
			$doc->getWebAssetManager()->addInlineScript($script, [], ['type' => 'module']);
		} else 
		{
			// Joomla <= 3
			$doc->addCustomTag('<script type="module">' . $script  . '</script>');
		}
	}
}