<?php

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'administrator');

require_once dirname(dirname(__FILE__)) . '/library/Core.php';

require_once JPATH_BASE . DS . 'includes' . DS . 'defines.php';
require_once JPATH_BASE . DS . 'includes' . DS . 'framework.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session as CmsSession;
use Joomla\Session\Session;
use Joomla\Session\SessionInterface;

$container = Factory::getContainer();
$container->alias('session.web', 'session.web.site')
    ->alias('session', 'session.web.site')
    ->alias('JSession', 'session.web.site')
    ->alias(CmsSession::class, 'session.web.site')
    ->alias(Session::class, 'session.web.site')
    ->alias(SessionInterface::class, 'session.web.site');

// Instantiate the application.
$app = $container->get(AdministratorApplication::class);

// Set the application as global app
\Joomla\CMS\Factory::$application = $app;

$app->createExtensionNamespaceMap();
$app->loadLanguage();
$app->loadDocument();

// checking user privileges
$user = Factory::getUser();
if (!$user->authorise('core.edit', 'com_menus') || !$user->authorise('core.edit', 'com_modules') || !$user->authorise('core.edit', 'com_content')) {
    exit('error:2:You do not have sufficient permissions to import/install content.');
}

$pluginName = 'nicepage';

$pathDataLoader = JPATH_BASE . '/components/com_' . $pluginName . '/helpers/import.php';
if (!file_exists($pathDataLoader))
    exit('error:2:Loader not found');

require_once JPATH_BASE . '/components/com_' . $pluginName . '/helpers/' . $pluginName . '.php';
require_once JPATH_BASE . '/components/com_' . $pluginName . '/helpers/import.php';

$className = ucfirst($pluginName) . '_Data_Loader';
$loader = new $className();
$loader->load('content.json', true);
echo $loader->execute($_GET);
