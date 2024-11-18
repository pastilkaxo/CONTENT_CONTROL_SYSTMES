<?php
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(dirname(dirname(__FILE__))) . DS . 'administrator');

require_once JPATH_BASE . DS . 'includes' . DS . 'defines.php';
require_once JPATH_BASE . DS . 'includes' . DS . 'framework.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session as CmsSession;
use Joomla\Session\Session;
use Joomla\Session\SessionInterface;
use Joomla\CMS\Component\ComponentHelper;

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

$action = $app->input->get('action', '');
$name = $app->input->get('plugin', '');
$themeName = $app->input->get('template', '');
$task = $app->input->get('task', '');
if (!$action) {
    echo 'fail';
    return;
}

$response = 'ok';
switch($action) {
    case 'activate_theme':
        if ($themeName) {
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->update('#__template_styles')
                ->set('home=0')
                ->where('client_id=0')
                ->where('home=1');

            try {
                $db->setQuery($query)->execute();
            } catch (RuntimeException $e) {
                $response = 'fail';
            }

            if ($response == 'ok') {
                $query->clear();
                $query->update('#__template_styles')
                    ->set('home=1')
                    ->where('client_id=0')
                    ->where('template=' . $db->quote($themeName));
                try {
                    $db->setQuery($query)->execute();
                } catch (RuntimeException $e) {
                    $response = 'fail';
                }
            }
        } else {
            $response = 'fail';
        }
        break;
    case 'install_plg':
        // Create token
        $session = Factory::getSession();
        $token = $session::getFormToken();
        define('JPATH_COMPONENT', JPATH_BASE . '/components/com_installer');


        $app->input->set('installtype', 'folder');
        $app->input->set('install_directory', dirname(__FILE__) . '/package');
        $app->input->post->set($token, 1);

        $app->scope = 'com_installer';
        $dispatcher = $app->bootComponent('com_installer')->getDispatcher($app);
        $controller = $dispatcher->getController('install', 'Administrator', array('option' => 'com_installer', 'name' => 'install'));

        $controller->execute('install');
        $extMsg = $app->getUserState('com_installer.extension_message');
        $app->setUserState('com_installer.extension_message', '');
        if (preg_match('/install\-(update|success|failure)/', $extMsg)) {
            $extMsg = preg_replace('/<style>[\s\S]+?<\/style>/', '', $extMsg);
            $extMsg = str_replace($name . 'install', 'wizardinstall', $extMsg);
            $response = $extMsg;
        } else {
            $response = 'fail';
        }
        break;
    case 'check_plg':
        if ($name) {
            if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_' . $name)) {
                $response = 'fail';
            }
            if (!ComponentHelper::getComponent('com_' . $name, true)->enabled) {
                $response = 'fail';
            }
        } else {
            $response = 'fail';
        }
        break;
    case 'import':
        $pathDataLoader = JPATH_BASE . '/components/com_' . $name . '/helpers/import.php';
        if ($name && $themeName && file_exists($pathDataLoader)) {
            require_once JPATH_BASE . '/components/com_' . $name . '/helpers/' . $name . '.php';
            require_once JPATH_BASE . '/components/com_' . $name . '/helpers/import.php';

            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->select('id')->from('#__template_styles')->where('template=' . $db->quote($themeName));
            $db->setQuery($query);
            $_GET['id'] = $db->loadResult();
            $_GET['action'] = 'run';
            $className = ucfirst($name) . '_Data_Loader';
            $loader = new $className();
            $loader->load('content/content.json', true);
            ob_start();
            $result = $loader->execute($_GET);
            $result = ob_get_clean();
        } else {
            $response = 'fail';
        }
        break;
    default:
        $response = 'fail';
}
exit($response);
