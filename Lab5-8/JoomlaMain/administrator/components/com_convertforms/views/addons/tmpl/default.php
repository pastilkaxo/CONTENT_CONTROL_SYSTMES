<?php

/**
 * @package         Convert Forms
 * @version         4.4.6 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.modal');
HTMLHelper::_('bootstrap.popover');
?>
<div class="row-fluid">
    <?php if (!defined('nrJ4')) { ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <?php } ?>

    <div id="j-main-container">
        
        <div class="cf-addons-container">
            <h2>
                <?php echo Text::_("COM_CONVERTFORMS") ?>
                <?php echo Text::_("COM_CONVERTFORMS_ADDONS") ?>
            </h2>
            <p><?php echo Text::_("COM_CONVERTFORMS_ADDONS_DESC") ?></p>
            <div class="cf-addons">
                <?php foreach ($this->availableAddons as $key => $item) { ?>
                    <div class="cf-addon">
                        <div class="cf-addon-wrap">
                            <div class="cf-addon-img">
                                <img alt="<?php echo $item["label"]; ?>" src="<?php echo $item["image"]; ?>"/>
                            </div>
                            <div class="cf-addon-text">
                                <h3><?php echo $item["label"]; ?></h3>
                                <?php echo $item["description"]; ?>
                            </div>
                            <div class="cf-addon-action text-center">

                               
                               <?php 
                                    if (!$item['comingsoon'] && $item['proonly'] === true)
                                    {
                                        NRFramework\HTML::renderProButton(Text::_($item['label']));
                                    }
                                ?>
                                

                                <?php if ($item['comingsoon']) { ?>
                                    <?php echo Text::_('NR_ROADMAP'); ?>
                                <?php } ?>

                                <?php if (!$item['comingsoon'] && $item['extensionid']) { ?>

                                    <?php 
                                        $optionsURL = Uri::base(true) . '/index.php?option=com_plugins&view=plugin&tmpl=component&layout=modal&extension_id=' . $item['extensionid'];
                                        $modalName = 'cfPluginModal-' . $item['extensionid'];
                                    ?>

                                    <a class="btn btn-sm btn-secondary"
                                        data-toggle="modal"
                                        data-bs-toggle="modal"
                                        href="#<?php echo $modalName ?>"
                                        role="button"
                                        title="<?php echo Text::_("JOPTIONS") ?>">
                                        <span class="icon-options"></span>
                                    </a>

                                    <?php
                                        $options = [
                                            'title'       => Text::_('GSD_INTEGRATION_EDIT'),
                                            'url'         => $optionsURL,
                                            'height'      => '400px',
                                            'backdrop'    => 'static',
                                            'bodyHeight'  => '70',
                                            'modalWidth'  => '70',
                                            'footer'      => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" aria-hidden="true">'
                                                    . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>                                      
                                                    <button type="button" class="btn btn-primary"
                                                        onclick="document.querySelector(\'#' . $modalName . ' iframe\').contentDocument.querySelector(\'' . (version_compare(JVERSION, '5.0', 'ge') ? 'button.button-apply' : '#applyBtn') . '\').click();' . (defined('nrJ4') ? ' window.parent.Joomla.Modal.getCurrent().close();' : '') . '">'
                                                    . Text::_('JSAVE') . '</button>
                                                    <button type="button" class="btn btn-success"
                                                    onclick="document.querySelector(\'#' . $modalName . ' iframe\').contentDocument.querySelector(\'' . (version_compare(JVERSION, '5.0', 'ge') ? 'button.button-apply' : '#applyBtn') . '\').click();">'
                                                    . Text::_('JAPPLY') . '</button>'
                                        ];

                                        echo HTMLHelper::_('bootstrap.renderModal', $modalName, $options);
                                    ?>
                                <?php } ?>

                                <?php 
                                    $docsURL = 'https://www.tassos.gr/joomla-extensions/convert-forms//docs/' . $item['docalias'];
                                ?>

                                <a class="btn btn-sm btn-secondary" href="<?php echo $docsURL; ?>" target="_blank" title="<?php echo Text::_("NR_DOCUMENTATION") ?>">
                                    <span class="icon-info"></span>
                                </a>

                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="cf-addon">
                    <div class="cf-addon-wrap">
                        <div class="cf-addon-img">
                            <a target="_blank" target="_blank" href="https://www.tassos.gr/contact">
                                <img alt="<?php echo $item["description"]; ?>" src="https://static.tassos.gr/images/integrations/addon.png"/>
                            </a>
                        </div>
                        <div class="cf-addon-text">
                            <h3><?php echo Text::_("COM_CONVERTFORMS_ADDONS_MISSING_ADDON") ?></h3>
                            <?php echo Text::_("COM_CONVERTFORMS_ADDONS_MISSING_ADDON_DESC") ?>
                        </div>
                        <div class="cf-addon-action text-center">
                            <a class="btn btn-sm btn-primary" target="_blank" href="https://www.tassos.gr/contact"><?php echo Text::_("NR_CONTACT_US")?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once(JPATH_COMPONENT_ADMINISTRATOR."/layouts/footer.php"); ?>
