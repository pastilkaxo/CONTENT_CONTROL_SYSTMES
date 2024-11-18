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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Controller\BaseController;

// Load Framework
if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
{
	throw new RuntimeException('Tassos Framework is not installed', 500);
}

$app = Factory::getApplication();

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_convertforms'))
{
	$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
}

use NRFramework\Functions;
use NRFramework\Extension;

// Load framework's and component's language files
Functions::loadLanguage();
Functions::loadLanguage('com_convertforms');
Functions::loadLanguage('plg_system_convertforms');

// Initialize Convert Forms Library
require_once JPATH_ADMINISTRATOR . '/components/com_convertforms/autoload.php';

// Check required extensions
if (!Extension::pluginIsEnabled('nrframework'))
{
	$app->enqueueMessage(Text::sprintf('NR_EXTENSION_REQUIRED', Text::_('COM_CONVERTFORMS'), Text::_('PLG_SYSTEM_NRFRAMEWORK')), 'error');
}

if (!Extension::pluginIsEnabled('convertforms'))
{
	$app->enqueueMessage(Text::sprintf('NR_EXTENSION_REQUIRED', Text::_('COM_CONVERTFORMS'), Text::_('PLG_SYSTEM_CONVERTFORMS')), 'error');
}

if (!Extension::componentIsEnabled('ajax'))
{
	$app->enqueueMessage(Text::sprintf('NR_EXTENSION_REQUIRED', Text::_('COM_CONVERTFORMS'), 'Ajax Interface'), 'error');
}

// Load component's CSS/JS files
ConvertForms\Helper::loadassets();

if (defined('nrJ4'))
{
	HTMLHelper::stylesheet('plg_system_nrframework/joomla4.css', ['relative' => true, 'version' => 'auto']);
} else 
{
	HTMLHelper::stylesheet('com_convertforms/joomla3.css', ['relative' => true, 'version' => 'auto']);
}

// Perform the Request task
$controller = BaseController::getInstance('ConvertForms');
$controller->execute($app->input->get('task'));
$controller->redirect();