<?php
defined('_JEXEC') or die;
use Joomla\Module\Menu\Site\Helper\MenuHelper;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

$list       = MenuHelper::getList($params);
$base       = MenuHelper::getBase($params);
$active     = MenuHelper::getActive($params);
$default    = MenuHelper::getDefault();
$active_id  = $active->id;
$default_id = $default->id;
$path       = $base->tree;
$showAll    = $params->get('showAllChildren', 1);
$class_sfx  = htmlspecialchars($params->get('class_sfx', ''), ENT_COMPAT, 'UTF-8');
include dirname(JPATH_PLUGINS) . '/administrator/components/com_nicepage/views/controls/menu/default.php';