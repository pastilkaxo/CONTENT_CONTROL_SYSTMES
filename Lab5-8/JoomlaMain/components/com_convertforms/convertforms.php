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
use Joomla\CMS\MVC\Controller\BaseController;

// Load Framework
if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
{
	throw new RuntimeException('Tassos Framework is not installed', 500);
}

// Initialize Convert Forms Library
if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_convertforms/autoload.php'))
{
	throw new RuntimeException('Convert Forms component is not properly installed', 500);
}

// Load component's language files
NRFramework\Functions::loadLanguage('com_convertforms');

// Set default controller
$input = Factory::getApplication()->input;
$task  = $input->get('task', '');

if (strpos($task, '.') === false)
{
	$input->set('task', $task . '.' . $task);
}

// Load controller
$controller = BaseController::getInstance('ConvertForms');
$controller->execute($input->get('task'));
$controller->redirect();