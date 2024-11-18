<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

require_once JPATH_ADMINISTRATOR . '/components/com_nicepage/library/loader.php';

JLoader::registerPrefix('Nicepage', JPATH_COMPONENT_ADMINISTRATOR);
$controller = BaseController::getInstance('Nicepage');
$controller->execute(Factory::getApplication()->input->get('task'));