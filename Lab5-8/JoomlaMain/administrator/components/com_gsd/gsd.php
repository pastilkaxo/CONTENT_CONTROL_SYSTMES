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
if (!Factory::getUser()->authorise('core.manage', 'com_gsd'))
{
	$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
}

use NRFramework\Functions;
use NRFramework\Extension;

// Load framework's and component's language files
Functions::loadLanguage();
Functions::loadLanguage('plg_system_gsd');	

// Initialize component's library
require_once JPATH_ADMINISTRATOR . '/components/com_gsd/autoload.php';

// Check required extensions
if (!Extension::pluginIsEnabled('nrframework'))
{
	$app->enqueueMessage(Text::sprintf('NR_EXTENSION_REQUIRED', Text::_('GSD'), Text::_('PLG_SYSTEM_NRFRAMEWORK')), 'error');
}

if (!Extension::pluginIsEnabled('gsd'))
{
	$app->enqueueMessage(Text::sprintf('NR_EXTENSION_REQUIRED', Text::_('GSD'), Text::_('PLG_SYSTEM_GSD')), 'error');
}

if (defined('nrJ4'))
{
	HTMLHelper::stylesheet('plg_system_nrframework/joomla4.css', ['relative' => true, 'version' => 'auto']);
} else 
{
	HTMLHelper::stylesheet('plg_system_nrframework/joomla3.css', ['relative' => true, 'version' => 'auto']);
	HTMLHelper::stylesheet('com_gsd/joomla3.css', ['relative' => true, 'version' => 'auto']);
}

GSD\Helper::event('onGSDGetNames');

// Perform the Request task
$controller = BaseController::getInstance('GSD');
$controller->execute($app->input->get('task'));
$controller->redirect();