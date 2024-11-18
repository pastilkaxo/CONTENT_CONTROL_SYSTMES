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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;

require_once __DIR__ . '/script.install.helper.php';

class Com_ConvertFormsInstallerScript extends Com_ConvertFormsInstallerScriptHelper
{
	public $name = 'CONVERTFORMS';
	public $alias = 'convertforms';
	public $extension_type = 'component';

	public function onAfterInstall()
	{
		$this->moveFrontEndImages();

		if ($this->install_type == 'update') 
		{
			require_once __DIR__ . '/autoload.php';

			try {
				(new ConvertForms\Migrator($this->installedVersion))->start();
			} catch (\Throwable $th)
			{
			}

			$this->dropIndex('convertforms_conversions', 'email_campaign_id');

			// Drop auto-translation feature with language strings in field options in favor of the new {language.KEY} Smart Tag.
			if (version_compare($this->installedVersion, '2.8.0', '<=')) 
			{
				Factory::getApplication()->enqueueMessage('
					<b>Backwards Compatibility Break:</b> Language strings can no longer be used directly in field options. If you\'re using language strings to produce multilingual forms in one of the following options, you should update your forms to be using the <b>{language.KEY}</b> Smart Tag instead:<br><br>
	
					<ul>
						<li>Field Value</li>
						<li>Field Label</li>
						<li>Field Placeholder</li>
						<li>Field Description</li>
						<li>Dropdown Choice Label</li>
						<li>Radio Button Choice Label</li>
						<li>Checkbox Choice Label</li>
					</ul>', 'warning');
			}
        }
    }

	/**
	 *  Moves front-end based images from /media/ folder to /images/
	 *
	 *  @return  void
	 */
	private function moveFrontEndImages()
	{
		$source      = JPATH_SITE . '/media/com_convertforms/img/convertforms';
		$destination = JPATH_SITE . '/images/convertforms';

		if (!is_dir($source))
		{
			return;
		}

		if (!Folder::copy($source, $destination, null, true))
		{
			return;
		}

		Folder::delete($source);
	}
}