<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Virtuemart;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use \VmConfig;
use \vmLanguage;
use \vRequest;

class VirtuemartComponent
{
    /**
     * @return bool
     */
    public static function exists()
    {
        if (!file_exists(dirname(JPATH_ADMINISTRATOR) . '/components/com_virtuemart/')) {
            return false;
        }

        if (!ComponentHelper::getComponent('com_virtuemart', true)->enabled) {
            return false;
        }
        return true;
    }

    /**
     * Init virtuemart
     */
    public static function init() {
        if (!class_exists('VmConfig')) {
            include_once JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php';
        }
        VmConfig::loadConfig();

        if (!class_exists('vmLanguage')) {
            include_once VMPATH_ADMIN . '/helpers/vmlanguage.php';
        }
        vmLanguage::loadJLang('com_virtuemart');

        if (!class_exists('VmModel')) {
            include_once VMPATH_ADMIN . '/helpers/vmmodel.php';
        }

        if (!class_exists('VmMediaHandler')) {
            include_once VMPATH_ADMIN . '/helpers/mediahandler.php';
        }

        $token = vRequest::getFormToken();
        vRequest::setVar('token', $token);
    }
}

