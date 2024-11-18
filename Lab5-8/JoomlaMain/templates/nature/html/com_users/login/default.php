<?php
defined('_JEXEC') or die;

$cookieLogin = $this->user->get('cookieLogin');

if (!empty($cookieLogin) || $this->user->get('guest'))
{
    // The user is not logged in or needs to provide a password.
    echo $this->loadTemplate('login');
}
else
{
    // The user is already logged in.
    echo $this->loadTemplate('logout');
}
