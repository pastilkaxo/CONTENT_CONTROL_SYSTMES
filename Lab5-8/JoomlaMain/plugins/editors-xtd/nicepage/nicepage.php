<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

JLoader::register('NicepageHelpersNicepage', JPATH_ADMINISTRATOR . '/components/com_nicepage/helpers/nicepage.php');

use NP\Editor\Editor;
use NP\Factory as NpFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

class PlgButtonNicepage extends JPlugin
{
    /**
     * PlgButtonNicepage constructor.
     *
     * @param object $subject Subject object
     * @param object $config  Config object
     */
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
        HTMLHelper::_('jquery.framework');
    }

    /**
     * @param string $name Name
     */
    public function onDisplay($name)
    {
        if (!$this->isDisplayed()) {
            return;
        }
        $this->updateDuplicatedPageId();

        $editor = new Editor();
        $editor->addCommonScript();
        $editor->addLinkDialogScript();
        $editor->addDataBridgeScript();
        $editor->addMainScript();
        $editor->includeScripts();


        $this->displayEditButton();
        $this->displayScreenshots();
    }

    /**
     * Check editor button displaying
     *
     * @return bool
     */
    public function isDisplayed()
    {
        $app = Factory::getApplication();
        $option = $app->input->get('option');
        $aid = $app->input->get('id', '');

        if (!in_array($option, array('com_content')) || '' == $aid || !$app->isClient('administrator')) {
            return false;
        }

        if (!$this->_npInstalled()) {
            return false;
        }

        return true;
    }

    /**
     * Update nicepage id after duplicating
     */
    public function updateDuplicatedPageId()
    {
        $id = Factory::getApplication()->input->get('id', '');
        NicepageHelpersNicepage::getSectionsTable()->updateDuplicatedPageId($id);
    }

    /**
     * Display nicepage editor button
     */
    public function displayEditButton()
    {
        $input = Factory::getApplication()->input;
        $start = $input->get('start', '0');
        $autostart = $input->get('autostart', '0');
        $favicon = dirname(dirname((Uri::current()))) . '/components/com_nicepage/assets/images/button-icon.png?r=' . md5(mt_rand(1, 100000));;
        $cssDisplay = ($start == '1' || $autostart == '1') ? 'none' : 'block';
        $selectors = array(
            '.joomla-modal',
            'script',
            '#shortcutOverviewModal',
            '._access-main-css',
        );
        $notSelectors = '*';
        foreach ($selectors as $selector) {
            $notSelectors .= ':not(' . $selector . ')';
        }
        $css = <<<EOF
body.editor {
    overflow: hidden;
    position: relative;
}

body.editor>$notSelectors {
    display:none;
}

body>:$notSelectors {
    display:$cssDisplay;
}

body.editor div#wrapper {
    display: none !important;
}

body div#wrapper {
    display:$cssDisplay !important;
}
EOF;
        $css .= <<<EOF
#np-loader {
	display: block !important;
}

#editor-frame{
    display: block !important;
}

#sbox-window iframe {
    max-height: 100%;
    max-width: 100%;
}

#sbox-overlay, #sbox-window {
    display: block !important;
}

#sbox-overlay[aria-hidden=true],
#sbox-window[aria-hidden=true] {
    display: none !important;
}

.btn.nicepage-button, .btn.nicepage-preview-button{
    color: #4184F4;
    font-weight: bold;
    font-family: Arial;
    margin: 10px 10px 20px 0px;
}

.btn.nicepage-button {
    padding-right: 5px;
    padding-left: 25px;
    background: url('$favicon') no-repeat 4px 10px;
    background-size: 16px;
    border: 1px solid #b3b3b3;
}

.btn.nicepage-preview-button {
    background: url('$favicon') no-repeat -9999px;
    border: 1px solid #b3b3b3;
}

.nicepage-select-template {
    margin-left: 10px
}

[data-color-scheme="dark"] .nicepage-select-template-area {
    background: none;
}

.nicepage-select-template-area {
    border: 1px solid #001b4c;
    border-radius: 0.25rem;
    background: white;
    margin: 10px 0 20px 0;
    padding: 15px;
}

.nicepage-line {
    /* outline: inset; */
    width: 600px;
    height: 1px;
    background: #ddd;
    margin: 0px 0 15px 0;
}

.nicepage-password-input {
    margin-left: 10px
}
EOF;
        Factory::getDocument()->addStyleDeclaration($css);
    }

    /**
     * Display screenshot of article, created from sections
     */
    public function displayScreenshots()
    {
        $aid = Factory::getApplication()->input->get('id', '');

        if (!$aid) {
            return;
        }

        $page = NpFactory::getPage($aid);

        if (!$page) {
            return;
        }

        $page->setPageView('thumbnail');
        $page->buildPageElements();
        $content = $page->get();

        $content = str_replace('[[site_path_live]]', dirname(dirname((Uri::current()))) . '/', $content);
        $content = call_user_func('base' . '64_encode', $content);
        $css = <<<EOF
fieldset.adminform {
    display:none;
}
.nicepage-button {
    margin: 20px;
}
#preview-container {
    overflow: hidden;
}
#preview-frame {
    transform: scale(0.3);
    transform-origin: 0 0;
    height: 333.333%;
    max-width: 100%;
}
EOF;
        $js = <<<EOF
function renderScreenshots()
{
    (function($){
        var previewContainer = $('<div>', {
            id: 'preview-container'
        }),
        previewFrame = $('<iframe>', {
            id: 'preview-frame',
            frameborder: 0,
            scrolling: 'no',
            width: '9999px'
        });

        $('.adminform').before(previewContainer);
        previewContainer.append(previewFrame);
        
        var doc = previewFrame[0].contentDocument;
        doc.open();
        doc.write(decodeURIComponent(Array.prototype.map.call(atob('$content'), function(c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
        }).join('')));
        doc.close();
        
        previewFrame.on("load", function() {
            var containerHeight = 0;
            $('section', previewFrame.contents()).each(function(i, el) {
                containerHeight += $(el).height();
            });
            
            previewContainer.height(containerHeight * 0.3);
        });
        
        $('body', previewFrame.contents()).click(function(e) {
            runNicepage();
        });
    })(jQuery);
}

jQuery(function($) {
    renderScreenshots();
});
EOF;
        $doc = Factory::getDocument();
        $doc->addScriptDeclaration($js);
        $doc->addStyleDeclaration($css);
    }

    /**
     * Check on exist nicepage plugin
     *
     * @return bool
     */
    public function _npInstalled()
    {
        if (!file_exists(dirname(JPATH_ADMINISTRATOR) . '/components/com_nicepage')) {
            return false;
        }

        if (!ComponentHelper::getComponent('com_nicepage', true)->enabled) {
            return false;
        }
        return true;
    }
}
