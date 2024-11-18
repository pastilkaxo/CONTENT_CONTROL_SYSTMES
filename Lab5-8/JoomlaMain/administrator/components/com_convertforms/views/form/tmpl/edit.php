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
use NRFramework\HTML;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.dropdown');

$doc = Factory::getDocument();

// Needed by Tasks tab and Conditional Fields
HTMLHelper::script('plg_system_nrframework/vendor/react.min.js', ['relative' => true, 'version' => 'auto']);
HTMLHelper::script('plg_system_nrframework/vendor/react-dom.min.js', ['relative' => true, 'version' => 'auto']);

HTMLHelper::stylesheet('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');

HTMLHelper::script('com_convertforms/admin.js', ['relative' => true, 'version' => 'auto']);
HTMLHelper::stylesheet('com_convertforms/editor.css', ['relative' => true, 'version' => 'auto']);

if (defined('nrJ4'))
{
    $doc->addScript(Uri::root(true) . '/media/vendor/tinymce/tinymce.js');
    HTML::fixFieldTooltips();

} else 
{
    $doc->addScript(Uri::root(true) . '/media/editors/tinymce/tinymce.min.js');
    HTMLHelper::script('com_convertforms/cookie.js', ['relative' => true, 'version' => 'auto']);
}

// Fix Joomla 4's very high CodeMirror max-height.
if (defined('t_isJ4'))
{
    $doc->addStyleDeclaration('
        .CodeMirror:not(.CodeMirror-fullscreen) {
            min-height: 200px;
            max-height: 400px;
        }
    ');
}

$doc->addStyleDeclaration('
    .CodeMirror {
        margin: 0;
    }

    .CodeMirror-gutter.CodeMirror-foldgutter,
    .CodeMirror-gutter.CodeMirror-markergutter,
    joomla-editor-codemirror > p.small.float-end {
        display: none;
    }
');

$fonts = new NRFonts();
$doc->addScriptDeclaration('var ConvertFormsGoogleFonts = '. json_encode($fonts->getFontGroup('google')));

$tabState      = Factory::getApplication()->input->cookie->get("ConvertFormsState" . $this->item->id, 'fields');
$tabStateParts = explode("-", $tabState);
$tabActive     = $tabStateParts[0];

// Smart Tags Box
echo NRFramework\HTML::smartTagsBox();


NRFramework\HTML::renderProOnlyModal();


if (!$this->isnew) { 
    // Render Embed popup
    echo HTMLHelper::_('bootstrap.renderModal', 'embedForm', [
        'title'  => 'Embed Form',
        'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" aria-hidden="true">'. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
    ], '
    <p>You are almost done! To embed this form on your site, please paste the following shortcode inside an article or a module.</p>
    <input class="shortcode" readonly value="{convertforms ' . $this->item->id . '}"/>
    <p>or you can follow the instructions from this <a target="_blank" href="https://www.tassos.gr/joomla-extensions/convert-forms/docs/how-to-display-a-form-on-the-frontend">page</a>.</p>
    ');
}


function tabSetStart($active)
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.startTabSet', 'sections', ['active' => $active, 'recall' => true, 'orientation' => 'vertical']) : HTMLHelper::_('bootstrap.startTabSet', 'sections', ['active' => $active]);;
}

function tabSetEnd()
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.endTabSet') : HTMLHelper::_('bootstrap.endTabSet');;
}

function tabStart($name, $title)
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.addTab', 'sections', $name, Text::_($title)) : HTMLHelper::_('bootstrap.addTab', 'sections', $name, Text::_($title));
}

function tabEnd()
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.endTab') : HTMLHelper::_('bootstrap.endTab');
}

if (defined('nrJ4'))
{
    NRFramework\HTML::fixFieldTooltips();
}

$theme = [
    'cf-font-size'         => '16px',
    'cf-line-height'       => '1.5',
    'cf-color-default'     => '#444',
    'cf-color-primary'     => '#F1A208',
    'cf-color-success'     => '#46a546',
    'cf-color-danger'      => '#bd362f',
    'cf-color-secondary'   => '#1a3867',
    'cf-color-grey'        => '#dcdbdb',
    'cf-color-grey-light'  => '#F5F5F5',
    'cf-color-grey-medium' => '#e3e3e3',
    'cf-base-font-size'    => '16px',
    'cf-border-radius'     => '4px',
    'cf-border-color'      => '#ddd',
];

$themeCSSVars = \ConvertForms\FieldsHelper::cssVarsToString($theme, ':root');
$doc->addStyleDeclaration($themeCSSVars);
?>
<div class="cfEditor">

    <?php
        PluginHelper::importPlugin('convertformstools');
        Factory::getApplication()->triggerEvent('onConvertFormsEditorView');
    ?>

    <div class="nrEditor" data-root="<?php echo Uri::root(); ?>">
        <div class="cfe-header">
            <div class="cfe-logo">
                <a target="_blank" href="https://www.tassos.gr/joomla-extensions/convert-forms">
                    <img width="145px" alt="Convert Forms" src="<?php echo Uri::root() . 'media/com_convertforms/img/logoWhiteText.svg'; ?>"/>
                </a>
            </div>
            <div class="cfe-title">
                <label for="formname"><?php echo $this->isnew ? Text::_('COM_CONVERTFORMS_NEW_FORM') : Text::_('COM_CONVERTFORMS_EDIT_FORM') ?>:</label>
                <input type="text" data-fallback="<?php echo Text::_("COM_CONVERTFORMS_UNTITLED_BOX") ?>" id="formname" value="<?php echo $this->name ?>"/>
            </div>
            <div class="cfe-toolbar">
                <ul class="cf-menu">
                    <li>
                        <a href="#" class="btn btn-success save cf-menu-item saveForm" title="Save form" data-cfaction="save">
                            <i class="cf-icon-ok up-state"><?php echo Text::_('JAPPLY') ?></i>
                            <i class="cf-icon-spin hover-state"><?php echo Text::_('COM_CONVERTFORMS_SAVING'); ?></i>
                        </a>
                    </li>
                    <li class="cf-menu-parent">
                        <a href="#" class="cf-icon-dots cf-menu-item " role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" data-toggle="dropdown" title="View more"></a>
                        <ul class="dropdown <?php echo defined('nrJ4') ? 'dropdown-menu' : '' ?>">
                            <li>
                                <a class="<?php echo $this->isnew ? 'disabled' : '' ?>" data-bs-toggle="modal" data-bs-target="#embedForm" data-toggle="modal" data-target="#embedForm" href="#">
                                    <span class="cf-icon-link"></span>
                                    <?php echo Text::_('NR_EMBED') ?>
                                </a>
                            </li>
                            <li>
                                <a class="<?php echo $this->isnew ? 'disabled' : '' ?>" target="_blank" href="<?php echo Uri::base() ?>index.php?option=com_convertforms&view=conversions&filter.form_id=<?php echo $this->item->id ?>">
                                    <span class="cf-icon-users"></span>
                                    <?php echo Text::_('COM_CONVERTFORMS_SUBMISSIONS')?>
                                </a>
                            </li>
                            <li class="separator"></li>
                            
                            <li>
                                <a href="#" data-pro-only>
                                    <span class="cf-icon-heart"></span>
                                    <?php echo Text::_('NR_UPGRADE_TO_PRO') ?>
                                </a>
                            </li>
                            <li class="separator"></li>
                            
                            <li>
                                <a href="https://extensions.joomla.org/extension/convert-forms/" target="_blank">
                                    <span class="cf-icon-thumbs-up"></span>
                                    <?php echo Text::sprintf('NR_RATE', Text::_('CONVERTFORMS')) ?>
                                </a>
                            </li>
                            <li>
                                <a href="http://www.tassos.gr/contact?topic=Bug Report&extension=Convert Forms" target="_blank">
                                    <span class="cf-icon-megaphone"></span>
                                    <?php echo Text::_('NR_REPORT_ISSUE') ?>
                                </a>
                            </li>
                            <li>
                                <a href="http://www.tassos.gr/joomla-extensions/convert-forms/docs" target="_blank">
                                    <span class="cf-icon-help"></span>
                                    <?php echo Text::_('JHELP') ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo Route::_('index.php?option=com_convertforms&view=forms') ?>" class="cf-icon-cancel cf-menu-item" title="Close and return to forms list"></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="cfe-main">
            <div class="nrEditorOptions inputSettings cf-editor-options">
                <form action="<?php echo Route::_('index.php?option=com_convertforms&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-vertical" pk="<?php echo (int) $this->item->id ?>">
                    <div class="tabs-left">
                        <?php 
                            tabSetStart($tabActive);

                            foreach ($this->tabs as $key => $tab)
                            {
                                $tabName  = $key;
                                $tabLabel = Text::_($tab["label"]);

                                tabStart($tabName, '<span data-label="' . $tabLabel . '" class="' . $tab["icon"] . '"></span>');

                                $panelActive = $tabActive == $key ? $tabState : "";

                                echo HTMLHelper::_('bootstrap.startAccordion', $tabName, array('active' => $panelActive));
                                echo "<h2>" . $tabLabel . "</h2>";

                                $single = count($tab["fields"]) == 1 ? true : false;

                                foreach ($tab["fields"] as $key => $field)
                                {
                                    if ($single)
                                    {
                                        echo '<div class="accordion-inner"> ' . $this->form->renderFieldset($field["name"]) . '</div>';
                                        continue;
                                    }

                                    echo HTMLHelper::_('bootstrap.addSlide', $tabName, Text::_($field["label"]), $tabName.'-' . $field["name"], $field["name"]);

                                    $fieldset = $this->form->renderFieldset($field["name"]);
                                    Factory::getApplication()->triggerEvent('onConvertFormsBackendFormPrepareFieldset', [$field["name"], &$fieldset]);
                                    echo $fieldset;

                                    echo HTMLHelper::_('bootstrap.endSlide');
                                }

                                echo HTMLHelper::_('bootstrap.endAccordion');

                                tabEnd();
                            }

                            tabSetEnd();
                        ?>
                        <input type="hidden" name="task" value="form.edit" />
                        <?php echo HTMLHelper::_('form.token'); ?>
                    </div>
                </form>
            </div>
            <div class="nrEditorPreview hidden-phone">
                <div class="nrEditorTools inputSettings">
                    <div class="l nrEditorTabs">
                        <ul class="nrNav">
                            <li class="nrCheckbox">
                                <input value="1" type="checkbox" id="preview-successmsg">
                                <label for="preview-successmsg"><?php echo Text::_("COM_CONVERTFORMS_PREVIEW_SUCCESS") ?></label>
                            </li>
                        </ul>
                    </div>
                    <div class="r">

                    </div>
                </div>
                <div class="nrEditorPreviewContainer"></div>
                <div class="loader"></div>
            </div>
        </div>
    </div>

</div>