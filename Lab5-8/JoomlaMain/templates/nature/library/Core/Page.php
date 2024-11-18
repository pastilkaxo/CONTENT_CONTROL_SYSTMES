<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Contains page rendering helpers.
 */
class CorePage
{

    public $page;

    public function __construct($page)
    {
        $this->page = $page;
    }

    public function isFrontEditing()
    {
        $input = Factory::getApplication()->input;
        $option = $input->getCmd('option');
        $controller = $input->getCmd('controller');
        if ($option === 'com_config' && $controller === 'config.display.modules') {
            return true;
        }

        $view = $input->getCmd('view');
        $layout = $input->getCmd('layout');
        $aid = $input->getCmd('a_id');
        if ($aid && $view === 'form' && $layout === 'edit') {
            return true;
        }

        return false;
    }

    public function renderHeader($indexDir, $params = null) {
        $lang = checkAndGetLanguage();
        if ($lang) {
            $indexDir = $indexDir . '/headerTranslations/' . $lang;
        }
        include_once  "$indexDir/header.php";
        $themeHelper = ThemeHelper::getInstance();
        $header = $themeHelper->headerHtml . $themeHelper->headerExtraHtml;
        if ($params) {
            $dataIds = json_decode($params->get('dataIds', ''));
            if ($dataIds) {
                foreach ($dataIds as $key => $value) {
                    $header = str_replace('[page_' . $key . ']', 'index.php?option=com_content&amp;view=article&amp;id=' . $value, $header);
                    $header = str_replace('[' . $key . ']', 'index.php?option=com_content&amp;view=article&amp;id=' . $value, $header);
                }
            }
        }
        echo $header;
    }

    public function renderFooter($indexDir, $params = null) {
        $lang = checkAndGetLanguage();
        if ($lang) {
            $indexDir = $indexDir . '/footerTranslations/' . $lang;
        }
        include_once  "$indexDir/footer.php";
        $themeHelper = ThemeHelper::getInstance();
        $footer = $themeHelper->footerHtml . $themeHelper->footerExtraHtml;
        if ($params) {
            $dataIds = json_decode($params->get('dataIds', ''), true);
            if ($dataIds) {
                foreach ($dataIds as $key => $value) {
                    $footer = str_replace('[page_' . $key . ']', 'index.php?option=com_content&amp;view=article&amp;id=' . $value, $footer);
                    $footer = str_replace('[' . $key . ']', 'index.php?option=com_content&amp;view=article&amp;id=' . $value, $footer);
                }
            }
        }
        echo $footer;
    }

    public function fixVmStyles() {
        $document = Factory::getApplication()->getDocument();
        $styleSheets = $document->_styleSheets;
        $newStyleSheets = array();
        foreach ($styleSheets as $styleFilePath => $styleSheet) {
            if (preg_match('/vm\-(l|r)tr/', $styleFilePath)) {
                continue;
            }
            $newStyleSheets[$styleFilePath] = $styleSheet;
        }
        $document->_styleSheets = $newStyleSheets;
    }

    public function fixVmScripts($content)
    {
        $document = Factory::getApplication()->getDocument();
        $scripts = $document->_scripts;
        $index = 0;
        foreach ($scripts as $filePath => $script) {
            $index++;
            if (preg_match('/com\_virtuemart.+vmprices\.js/', $filePath)) {
                $before = array_slice($scripts, 0, $index - 1);
                $after = array_slice($scripts, $index);
                $newFilePath = $document->baseurl . '/templates/' . $document->template . '/scripts/vmprices.js';
                $new = array($newFilePath => $script);
                $scripts = array_merge($before, $new, $after);
            }
        }
        $document->_scripts = $scripts;

        $content = str_replace('Virtuemart.product($("form.product"));', 'Virtuemart.product($(".product"));', $content);
        return $content;
    }

    public function renderLayout()
    {
        if ($this->page->getType() != 'html') {
            return;
        }
        $input = Factory::getApplication()->input;
        $option = $input->getCmd('option');
        $view = $input->getCmd('view');
        $task = $input->getCmd('task');

        $content = $this->page->getBuffer('component');

        if (!$this->isFrontEditing()) {
            $content = str_replace('hasTooltip', '', $content);
        }

        

        $this->page->setBuffer($content, 'component');

        if ($option == 'com_content' && $view == 'category') {
            $currentLayout = $input->getCmd('layout', '');
            $compParams = ComponentHelper::getParams('com_content');
            $commonLayout = $compParams->get('category_layout');
            if ($currentLayout || strpos($commonLayout, 'blog') !== false) {
                $view = 'blog';
            }
        }

        $method = $input->getCmd('method');
        switch ($option) {
            case "com_ajax":
                switch ($method) {
                    case "product":
                        $this->renderLayoutByType('custom_product', false, true);
                        return;
                    case "products":
                        $this->renderLayoutByType('custom_products', false, true);
                        return;
                }
                break;
            case "com_users":
                switch ($view) {
                    case "login":
                        $this->renderLayoutByType('login');
                        return;
                }
                break;
            case "com_content":
                switch ($view) {
                    case "article":
                        $this->renderLayoutByType('post');
                        return;
                    case "blog":
                    case "featured":
                    case "archive":
                        $this->renderLayoutByType('blog');
                        return;
                }
                break;
            
                            case "com_finder":
                                switch ($view) {
                                    case "search":
                                        $this->renderLayoutByType('search');
                                        return;
                                }
                                break;
                        
            
        }
        $this->renderDefaultLayout();
    }

    public function renderComponent()
    {
        echo CoreStatements::message();
        echo CoreStatements::component();
    }

    public static  $positionPlaceholders = array();
    public static $foundPositionPlaceholders = array();

    public static function parsePositionPlaceholders($matches)
    {
        $placeholder = array_search($matches[0], CorePage::$foundPositionPlaceholders);
        if (!$placeholder) {
            CorePage::$positionPlaceholders[] = str_replace('/>', ' positionNumber="' . (count(CorePage::$positionPlaceholders) + 1) . '" />', $matches[0]);
            $count = preg_match('/count=[\'"](\d+)[\'"]/', $matches[0], $countMatches) ? (int) $countMatches[1] : 1;
            $placeholder = '[[position_' . count(CorePage::$positionPlaceholders) . ($count > 1 ? "_$count" : '') . ']]';
            CorePage::$foundPositionPlaceholders[$placeholder] = $matches[0];
        }
        return $placeholder;
    }

    public function renderLayoutByType($type, $isVm = false, $isEcommerce = false) {
        $this->beforeRenderLayout();
        if ($isVm) {
            $this->fixVmStyles();
        }
        $viewsFolder = $isEcommerce ? 'ecommerce' : 'views';
        include_once dirname(dirname(dirname(__FILE__))) . '/' . $viewsFolder . '/' . $type . '_layout.php';
        $this->afterRenderLayout();
    }

    public function beforeRenderLayout()
    {
        $content = $this->page->getBuffer('component');
        $content = preg_replace_callback(
            '/<jdoc[\s\S]+?\/>/',
            array('CorePage', 'parsePositionPlaceholders'),
            $content
        );
        $this->page->setBuffer($content, 'component');
    }

    public function afterRenderLayout()
    {
        echo implode('', CorePage::$positionPlaceholders);
    }

    public function renderDefaultLayout()
    {
        ob_start();
        include_once dirname(dirname(dirname(__FILE__))) . '/html/com_content/article/default_styles.php';
        Factory::getApplication()->getDocument()->addCustomTag(ob_get_clean());
        include_once dirname(dirname(dirname(__FILE__))) . '/views/default_layout.php';
    }
}
