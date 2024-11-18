<?php

/*
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2012 E-MAILiT                                     |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 3 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program.  If not, see <http://www.gnu.org/licenses/>.    |
 * +--------------------------------------------------------------------------+
 */

/**
 *
 * Creates E-MAILiT sharing button and appends it to the user selected pages.
 * 
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$code = ModEmailitHelper::appendEmailitScript($params);
require JModuleHelper::getLayoutPath('mod_emailit');




