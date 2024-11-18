<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
define('_JEXEC', 1);

use Joomla\CMS\Factory;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session as CmsSession;
use Joomla\Session\Session;
use Joomla\Session\SessionInterface;
use Joomla\CMS\User\User;

define('JPATH_BASE', dirname(dirname(dirname(dirname(dirname(__FILE__))))));

require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'defines.php';
require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'framework.php';


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

$uid = (int) Factory::getApplication('site')->input->get('uid', 0);
if (0 < $uid) {
    $session = Factory::getSession();
    $user = new User($uid);
    $session->set('user', $user);
}
?>
