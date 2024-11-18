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

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/script.install.helper.php';

class PlgConvertFormsEmailsInstallerScript extends PlgConvertFormsEmailsInstallerScriptHelper
{
	public $name = 'PLG_CONVERTFORMS_EMAILS';
	public $alias = 'emails';
	public $extension_type = 'plugin';
	public $plugin_folder = "convertforms";
	public $show_message = false;
}
