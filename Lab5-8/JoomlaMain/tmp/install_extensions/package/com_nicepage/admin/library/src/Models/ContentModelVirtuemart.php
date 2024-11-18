<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Models;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use \VmConfig, \vmDefines, \vmLanguage;

class ContentModelVirtuemart {
    /**
     * Check vm
     *
     * @return bool
     */
    protected function _vmInit()
    {
        if (!file_exists(dirname(JPATH_ADMINISTRATOR) . '/components/com_virtuemart/')) {
            return false;
        }

        if (!ComponentHelper::getComponent('com_virtuemart', true)->enabled) {
            return false;
        }

        $vmdefinesPath = JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/vmdefines.php';
        if (!class_exists('vmDefines') && !file_exists($vmdefinesPath)) {
            return false;
        }

        $configPath = JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';
        if (!class_exists('VmConfig') && !file_exists($configPath)) {
            return false;
        }

        include_once $vmdefinesPath;
        include_once $configPath;

        if (!method_exists('VmConfig', 'loadConfig')) {
            return false;
        }

        if (!method_exists('vmDefines', 'core')) {
            return false;
        }

        VmConfig::loadConfig();
        vmDefines::core(JPATH_ROOT);
        vmLanguage::loadJLang('com_virtuemart', true);

        $document = Factory::getApplication()->getDocument();

        $scripts = <<<SCRIPT
            <script type="text/javascript">
                if (typeof Virtuemart === "undefined") {
                    var Virtuemart = {};
                }
                jQuery(function ($) {
                    Virtuemart.customUpdateVirtueMartNpCart = function(el, options) {
                        var base 	= this;
                        base.npEl 	= $(".u-shopping-cart");
                        base.options 	= $.extend({}, Virtuemart.customUpdateVirtueMartNpCart.defaults, options);
                        
                        base.init = function() {
                            $.ajaxSetup({cache: false});
                            $.getJSON(Virtuemart.vmSiteurl + "index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json" + Virtuemart.vmLang,
                                function (datas, textStatus) {
                                    base.npEl.each(function(index, control) {
                                        $(control).find(".u-shopping-cart-count").html(datas.totalProduct);
                                    });
                                }
                            );
                        };
                        base.init();
                    };
                });
                
                jQuery(document).ready(function( $ ) {
                    $(document).off("updateVirtueMartCartModule", "body", Virtuemart.customUpdateVirtueMartNpCart);
                    $(document).on("updateVirtueMartCartModule", "body", Virtuemart.customUpdateVirtueMartNpCart);
                });
            </script>
SCRIPT;
        $document->addCustomTag($scripts);

        return true;
    }
}
