<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

HTMLHelper::stylesheet('com_gsd/styles.css', ['relative' => true, 'version' => 'auto']);

$doc = Factory::getDocument();

$doc->addScriptDeclaration('
    window.GSDReloadForm = () => {
        const isJ3 = typeof Joomla.loadingLayer == "function";

        if (isJ3) {
            Joomla.loadingLayer("show");
        } else {
            document.body.appendChild(document.createElement("joomla-core-loader"));
        }

        document.querySelector("input[name=task]").value = "item.reload";
        Joomla.submitform("item.reload", document.getElementById("adminForm"));
    };
');

if (defined('nrJ4'))
{
    NRFramework\HTML::fixFieldTooltips();

    $doc->getWebAssetManager()->useScript('webcomponent.core-loader');
    $doc->addStyleDeclaration('
        #content select:not([class*="input-"]), #content input:not([class*="input-"]) {
            max-width:270px;
        }
    ');

} else 
{
    HTMLHelper::_('jquery.framework');
    HTMLHelper::script('com_gsd/script.js', ['relative' => true, 'version' => 'auto']);
    HTMLHelper::_('formbehavior.chosen', 'select:not(.select2)');
}

$input = Factory::getApplication()->input;

// In case of modal
$isModal = $input->get('layout') == 'modal' ? true : false;
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

$formData = $this->form->getData();
$selectedApp = $formData->get('plugin');
$selectedAppView = $formData->get('appview');
$selectedContentType = $formData->get('contenttype');


// This style block is required in the Free version where the assignmentselection.css file is missing.
$doc->addStyleDeclaration('
    .assign {
        background-color: #F0F0F0;
        border: solid 1px #DEDEDE;
        color: inherit !important;
        padding: 10px 12px;
        margin-bottom: -1px;
    }
    .assign .control-group {
        margin: 0;
    }
');


?>

<script type="text/javascript">
    Joomla.submitbutton = function(task) {
        var form = document.getElementById('adminForm');

        if (task == 'item.cancel' || document.formvalidator.isValid(form)) {
            Joomla.submitform(task, form);

            <?php if ($isModal) { ?>
            if (task !== 'item.apply') {
                <?php if (!defined('nrJ4')): ?>
                window.parent.jQuery('#gsdModal').modal('hide');
                <?php else: ?>
                if (window.parent.Joomla.Modal.getCurrent())
                {
                    window.parent.Joomla.Modal.getCurrent().close();
                }
                <?php endif; ?>
            }
            <?php } ?>
        }
    }
</script>

<div class="nr-app<?php echo (defined('nrJ4') ? ' j4' : '') . ($isModal ? ' nr-isModal' : ''); ?>">
    <div class="nr-row">
        <div class="nr-main-container">
            <div class="nr-main-content">
                <form action="<?php echo Route::_('index.php?option=com_gsd&view=item&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
                    <div class="form-horizontal">
                        <div class="<?php echo defined('nrJ4') ? 'row' : 'row-fluid' ?>">
                            <div class="span9 col-md-9">
                                <?php 
                                    echo $this->form->renderFieldSet('top'); 
                                ?>

                                <!-- Content Type -->
                                <?php if ($selectedContentType) { ?>
                                    <div class="well nr-well">
                                        <h4><?php echo Text::_('GSD_' . strtoupper($selectedContentType)) ?></h4>
                                        <div class="well-desc"><?php echo Text::_('GSD_MAP_DESC') ?></div>
                                        <?php echo $this->form->renderFieldSet('contenttype');  ?>
                                    </div>
                                <?php } ?>

                                <!-- Conditions -->
                                <?php if ($selectedApp) { ?>
                                    <?php
                                        $conditions = [];
                                        $conditionsForm = new Form(null);

                                        // Default XML checks the assignments.xml
                                        $xmlFile = JPATH_PLUGINS . '/gsd/' . $selectedApp . '/form/assignments.xml';

                                        // However, some apps (J-Business Directory) have a separate XML per app view
                                        if ($selectedAppView && !file_exists($xmlFile))
                                        {
                                            $xmlFile = JPATH_PLUGINS . '/gsd/' . $selectedApp . '/form/' . $selectedAppView . '.xml';
                                        }

                                        // Finally, ensure the XML file exists
                                        if (file_exists($xmlFile))
                                        {
                                            $conditionsForm->loadFile($xmlFile);
                                            $conditions = array_keys($conditionsForm->getFieldSets());
                                        }
                                        
                                        $integration = Text::_('PLG_GSD_' . $selectedApp . '_ALIAS');
                                    ?>
                                    
                                    <div class="well nr-well <?php echo $isModal ? 'hide' : '' ?>">
                                        <h4><?php echo Text::_('GSD_ITEM_RULES') ?></h4>
                                        <div class="well-desc">
                                            <?php 
                                                echo Text::sprintf(
                                                    'GSD_ITEM_PUBLISHING_ASSIGNMENTS_DESC', 
                                                    Text::_('GSD_' . $selectedContentType), 
                                                    $selectedApp
                                                ) 
                                            ?>
                                        </div>

                                        <?php if ($conditions) { ?>
                                            <?php foreach ($conditions as $condition) { ?>
                                                <div class="assign">
                                                    <?php echo $this->form->renderFieldSet($condition); ?>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php echo Text::sprintf('GSD_NO_FILTERS_NOTICE', $integration, $integration); ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="span3 col-md-3 form-vertical form-no-margin">
                                <?php echo $this->form->renderFieldSet('main'); ?>
                            </div>
                        </div>
                    </div>

                    <?php echo HTMLHelper::_('form.token'); ?>
                    <input type="hidden" name="task" value="" />

                    <?php if ($isModal) { ?>
                        <input type="hidden" name="layout" value="modal" />
                        <input type="hidden" name="tmpl" value="component" />
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>
