<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if (isset($controlProps) && isset($controlTemplate)) {
    $user = Factory::getUser();
    $cookieLogin = $user->get('cookieLogin');
    $loginText = !empty($cookieLogin) || $user->get('guest') ? Text::_('JLOGIN') : Text::_('JLOGOUT');
    $userUrl = $controlProps['href'];
    if ($userUrl === '' || $userUrl === '#') {
        $userUrl = Route::_('index.php?option=com_users&view=login');
    }
    $controlTemplate = str_replace('[[content]]', $loginText, $controlTemplate);
    $controlTemplate = str_replace('[[href]]', $userUrl, $controlTemplate);
    echo $controlTemplate;
}