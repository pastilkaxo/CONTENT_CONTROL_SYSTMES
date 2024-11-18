<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\EditorField;
use Joomla\CMS\Form\Field\TextareaField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

class JFormFieldTFEditor extends TextareaField
{
    public function getInput()
    {
        $this->class .= ' tf-editor';
        
        $this->loadAssets();

        return '<div class="tf-editor-wrapper"' . $this->getWrapperAttributes() . '>' . parent::getInput() . '</div>';
    }
    
    private function loadAssets()
    {
		$doc = Factory::getDocument();

        // Get the global editor
        $globalEditor = Factory::getConfig()->get('editor');
        
        // Get the user editor
        $userEditor = Factory::getUser()->getParam('editor');

        $editor = $userEditor ? $userEditor : $globalEditor;
        $option = Factory::getApplication()->input->get('option', '');
        $layout = Factory::getApplication()->input->get('layout', '');

        if (!in_array($option, ['com_content', 'com_contact']) || $layout !== 'edit' || $editor !== 'jce')
        {
            if (JVERSION < 4)
            {
                $doc->addScript(Uri::root(true) . '/media/editors/tinymce/tinymce.min.js');
            }
            else
            {
                $wa = $doc->getWebAssetManager();
    
                if (!$wa->assetExists('script', 'tinymce'))
                {
                    $wa->registerScript('tinymce', 'media/vendor/tinymce/tinymce.min.js', [], ['defer' => true]);
                }
    
                if (!$wa->assetExists('script', 'plg_editors_tinymce'))
                {
                    $wa->registerScript('plg_editors_tinymce', 'plg_editors_tinymce/tinymce.min.js', [], ['defer' => true], ['core', 'tinymce']);
                }
    
                $wa->useScript('tinymce')->useScript('plg_editors_tinymce');
            }
        }

        HTMLHelper::stylesheet('plg_system_nrframework/controls/editor.css', ['relative' => true, 'versioning' => 'auto']);
        HTMLHelper::script('plg_system_nrframework/controls/editor.js', ['relative' => true, 'versioning' => 'auto'], ['defer' => true]);
    }

    private function getWrapperAttributes()
    {
        $plugins = isset($this->element['plugins']) ? array_filter(array_map('trim', explode(',', (string) $this->element['plugins']))) : false;
        $toolbar = isset($this->element['toolbar']) ? array_filter(array_map('trim', explode(',', (string) $this->element['toolbar']))) : false;

        $atts = [];
        
        if ($plugins)
        {
            $atts[] = 'data-plugins="' . htmlspecialchars(json_encode($plugins), ENT_COMPAT, 'UTF-8') . '"';
        }

        if ($toolbar)
        {
            $atts[] = 'data-toolbar="' . htmlspecialchars(json_encode($toolbar), ENT_COMPAT, 'UTF-8') . '"';
        }

        return implode(' ', $atts);
    }
}