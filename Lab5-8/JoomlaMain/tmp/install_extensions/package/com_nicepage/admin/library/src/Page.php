<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use \NicepageHelpersNicepage;
use NP\Utility\ColorHelper;

/**
 * Class Page
 */
class Page
{
    private static $_instance;

    private $_originalName = 'nicepage';
    private $_isNicepageTheme = '0';
    private $_pageTable = null;

    private $_pageView = 'landing';
    private $_config = null;
    private $_siteSettings = null;
    private $_props = null;

    private $_scripts = '';
    private $_styles = '';
    private $_backlink = '';
    private $_sectionsHtml = '';
    private $_cookiesConsent = '';
    private $_cookiesConfirmCode = '';
    private $_backToTop = '';
    private $_canonicalUrl = '';

    private $_context;
    private $_row;
    private $_params;

    private $_header = '';
    private $_footer = '';

    private $_buildedPageElements = false;
    private $_pagePasswordProtected = false;

    private $_publishDialogs = array();

    private $_productName = '';

    /**
     * Page constructor.
     *
     * @param null   $pageTable Page table
     * @param string $context   Component context
     * @param null   $row       Component row
     * @param null   $params    Component params
     */
    public function __construct($pageTable, $context, &$row, &$params) {
        $this->_pageTable = $pageTable;
        $this->_context = $context;
        $this->_row = $row;
        $this->_params = $params;

        $props = $this->_pageTable->getProps();
        $this->_config = NicepageHelpersNicepage::getConfig($props['isPreview']);
        if (isset($this->_config['siteSettings'])) {
            $this->_siteSettings = json_decode($this->_config['siteSettings'], true);
        }
        $this->_props = $this->prepareProps($props);

        if (isset($props['pageView'])) {
            $this->_pageView = $props['pageView'];
        }

        $originalName = $this->_originalName;
        if ($this->_row) {
            $this->_row->{$originalName} = true;
        }

        $this->_isNicepageTheme = Factory::getApplication()->getTemplate(true)->params->get($originalName . 'theme', '0');
    }

    /**
     * Get page id
     *
     * @return mixed
     */
    public function getPageId() {
        return $this->_props['pageId'];
    }

    /**
     * Check page is protected
     *
     * @return bool
     */
    public function pagePasswordProtected() {
        if (isset($this->_props['passwordProtection']) && $this->_props['passwordProtection']) {
            $originalPassword = $this->_props['passwordProtection'];
            $uri = Uri::getInstance()->toString();
            $cookieHash = ApplicationHelper::getHash($uri);
            $cookieName = 'joomla-postpass_' . $cookieHash;
            $app = Factory::getApplication();

            $userPassword = $app->input->get('password', '');
            $userPasswordHash = $app->input->get('password_hash', '');
            if ($userPassword && ($userPassword === $originalPassword || $userPasswordHash === $originalPassword)) {
                // Create ten days cookies.
                $cookieLifeTime = time() + 10 * 24 * 60 * 60;
                $cookieDomain   = $app->get('cookie_domain', '');
                $cookiePath     = $app->get('cookie_path', '/');
                $isHttpsForced  = $app->isHttpsForced();

                $app->input->cookie->set(
                    $cookieName,
                    $userPasswordHash === $originalPassword ? $userPasswordHash : md5($userPassword),
                    $cookieLifeTime,
                    $cookiePath,
                    $cookieDomain,
                    $isHttpsForced,
                    true
                );
            }

            $cookie = $app->input->cookie;
            $cookiePass = $cookie ? $cookie->get($cookieName, '') : '';
            if (!$cookiePass || ($cookiePass !== $originalPassword && $cookiePass !== md5($originalPassword))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Build page elements
     */
    public function buildPageElements() {
        if ($this->_buildedPageElements) {
            return;
        }

        if ($this->pagePasswordProtected()) {
            $this->_pagePasswordProtected = true;
        }

        $this->setPageProperties();
        $this->setScripts();
        $this->setStyles();
        $this->setBacklink();
        $this->setSectionsHtml();
        $this->setCookiesConsent();
        $this->setBackToTop();
        $this->setCanonicalUrl();

        if ($this->_pageView == 'landing') {
            $this->setHeader();
            $this->setFooter();
        }

        $this->_buildedPageElements = true;
    }

    /**
     * Build page header
     */
    public function setHeader() {
        $content = $this->fixImagePaths($this->_config['header']);
        $hideHeader = $this->_props['hideHeader'];
        if ($content && !$hideHeader) {
            $headerItem = json_decode($content, true);
            if ($headerItem) {
                $translations = json_decode($this->fixImagePaths($this->_config['headerTranslations']), true);
                $lang = $this->getCurrentLanguage($translations);
                if ($lang && $translations && isset($translations[$lang])) {
                    $headerItem['php'] = $translations[$lang]['php'];
                }
                ob_start();
                echo $headerItem['styles'];
                echo $headerItem['php'];
                $publishHeader = ob_get_clean();
                $this->setPublishDialogs($publishHeader, 'header');
                $options = array(
                    'isPublic' => true,
                    'pageId' => 'header',
                    'propsId' => $this->_props['pageId'],
                    'settings' => $this->_siteSettings,
                );
                $this->_header = NicepageHelpersNicepage::processSectionsHtml($publishHeader, $options);
            }
        }
    }

    /**
     * Build page footer
     */
    public function setFooter() {
        $content = $this->fixImagePaths($this->_config['footer']);
        $hideFooter = $this->_props['hideFooter'];
        if ($content && !$hideFooter) {
            $footerItem = json_decode($content, true);
            if ($footerItem) {
                $translations = json_decode($this->fixImagePaths($this->_config['footerTranslations']), true);
                $lang = $this->getCurrentLanguage($translations);
                if ($lang && $translations && isset($translations[$lang])) {
                    $footerItem['php'] = $translations[$lang]['php'];
                }
                ob_start();
                echo $footerItem['styles'];
                echo $footerItem['php'];
                $publishFooter = ob_get_clean();
                $this->setPublishDialogs($publishFooter, 'footer');
                $options = array(
                    'isPublic' => true,
                    'propsId' => $this->_props['pageId'],
                    'pageId' => 'footer',
                );
                $this->_footer = NicepageHelpersNicepage::processSectionsHtml($publishFooter, $options);
            }
        }
    }

    /**
     * Get page header
     *
     * @return string
     */
    public function getHeader() {
        return $this->_header;
    }

    /**
     * Get page footer
     *
     * @return string
     */
    public function getFooter() {
        return $this->_footer;
    }

    /**
     * Set publish dialogs
     *
     * @param string $html Content
     * @param string $type Type
     */
    public function setPublishDialogs($html, $type = '') {
        $dialogs = array();
        if ($type == 'header' || $type == 'footer') {
            if (isset($this->_config[$type]) && $this->_config[$type]) {
                $item = json_decode($this->_config[$type], true);
                $dialogs = isset($item['dialogs']) ? json_decode($item['dialogs'], true) : array();
            }
        } else {
            $dialogs = json_decode($this->_props['dialogs'], true);
        }

        foreach ($dialogs as $dialog) {
            $dialog['publishHtmlTranslations'] = isset($dialog['publishHtmlTranslations']) ? $dialog['publishHtmlTranslations'] : array();
            $lang = $this->getCurrentLanguage($dialog['publishHtmlTranslations']);
            if ($lang && isset($dialog['publishHtmlTranslations'][$lang])) {
                $dialog['publishHtml'] = $dialog['publishHtmlTranslations'][$lang];
            }
            $this->_publishDialogs[$dialog['sectionAnchorId']] = $this->processPublishHtml($dialog['publishHtml'] . '<style>' . $dialog['publishCss'] . '</style>');
        }
        // All dialogs
        if (isset($this->_config['publishDialogs']) && $this->_config['publishDialogs']) {
            $publishDialogs = json_decode($this->_config['publishDialogs'], true);
            foreach ($publishDialogs as $dialog) {
                $anchorId = $dialog['sectionAnchorId'];
                $showOnList = isset($dialog['showOnList']) ? $dialog['showOnList'] : array();
                $shownOn = isset($dialog['showOn']) ? $dialog['showOn'] : '';
                $foundDialogUsage = false;

                if (($shownOn == 'timer' || $shownOn == 'page_exit') && in_array($this->getPageId(), $showOnList)) {
                    $foundDialogUsage = true;
                }

                if (strpos($html, $anchorId) !== false) {
                    $foundDialogUsage = true;
                }

                if ($foundDialogUsage && !array_key_exists($anchorId, $this->_publishDialogs)) {
                    $this->_publishDialogs[$anchorId] = $this->processPublishHtml($dialog['publishHtml']) . '<style>' . $dialog['publishCss'] . '</style>';
                }
            }
        }
    }

    /**
     * @param string $html Page html
     *
     * @return mixed
     */
    public function processPublishHtml($html) {
        $html = $this->fixImagePaths($html);
        $html = HTMLHelper::_('content.prepare', $html, new Registry, 'com_content.article');
        $options = array(
            'isPublic' => true,
            'pageId' => $this->_props['pageId'],
        );
        $html = NicepageHelpersNicepage::processSectionsHtml($html, $options);
        return $html;
    }

    /**
     * Apply dialogs to content
     *
     * @param string $html Content
     *
     * @return mixed|string|string[]|null
     */
    public function applyPublishDialogs($html) {
        $publishDialogsHtml = '';
        foreach ($this->_publishDialogs as $anchor => $dialog) {
            $publishDialogsHtml .= $dialog;
        }
        if ($publishDialogsHtml && $this->getPageView() !== 'landing' && $this->_isNicepageTheme != '1') {
            $publishDialogsHtml =  '<div class="nicepage-container"><div class="'. $this->_props['bodyClass'] .'">' . $publishDialogsHtml . '</div></div>';
        }
        $html = str_replace('</body>', $publishDialogsHtml . '</body>', $html);
        return $html;
    }

    /**
     * Build page
     */
    public function prepare() {
        $isBlog = $this->_context === 'com_content.featured' || $this->_context === 'com_content.category';
        if ($isBlog) {
            $introImgStruct = isset($this->_props['introImgStruct']) ? $this->_props['introImgStruct'] : '';
            if ($introImgStruct) {
                $this->_row->pageIntroImgStruct = json_decode($this->fixImagePaths($introImgStruct), true);
            }
        } else {
            $this->buildPageElements();
            $this->_row->introtext = $this->_row->text = $this->getHtml();
        }
    }

    /**
     * @return string
     */
    public function getHtml() {
        $type = $this->_pageView === 'landing' ? 'landing' : 'content';
        $content = "<!--np_" . $type . "-->" . $this->getSectionsHtml() . "<!--/np_" . $type . "-->";
        $content .= "<!--np_page_id-->" . $this->getPageId() . "<!--/np_page_id-->";
        return $content;
    }

    /**
     * Get page content
     *
     * @param string $pageContent Page content
     *
     * @return mixed|string|string[]|null
     */
    public function get($pageContent = '') {
        if ($this->_pageView === 'thumbnail') {
            return $this->buildThumbnail();
        } else if ($this->_pageView === 'landing') {
            $pageContent = $this->buildNpHeaderFooter($pageContent);
        } else if ($this->_pageView === 'landing_with_header_footer') {
            $pageContent = $this->buildThemeHeaderFooter($pageContent);
        } else {
            $pageContent = preg_replace('/<!--\/?np\_content-->/', '', $pageContent);
            $pageContent = preg_replace('/<\!--np\_page_id-->([\s\S]+?)<\!--\/np\_page_id-->/', '', $pageContent);
        }
        $pageContent = $this->appendOpenGraphTags($pageContent);
        if (strpos($pageContent, '<meta name="viewport"') === false) {
            $pageContent = str_replace('<head>', '<head><meta name="viewport" content="width=device-width, initial-scale=1.0">', $pageContent);
        }
        $pageContent = str_replace('</head>', $this->getStyles() . $this->getScripts() . $this->getCookiesConfirmCode() . '</head>', $pageContent);
        $pageContent = str_replace('</body>', $this->getBacklink() . $this->getCookiesConsent() . $this->getBackToTop() . '</body>', $pageContent);
        $pageCanonical = $this->getCanonicalUrl();
        if ($pageCanonical) {
            if (preg_match('/<link[^>]+?rel="canonical"[^>]*?>/', $pageContent, $canonicalMatches)) {
                $pageContent = str_replace($canonicalMatches[0], $pageCanonical, $pageContent);
            } else {
                $pageContent = str_replace('<head>', '<head>' . $pageCanonical, $pageContent);
            }
        }
        $pageContent = $this->applyPublishDialogs($pageContent);
        $pageContent = $this->applyMetaData($pageContent);
        return $pageContent;
    }

    /**
     * Apply seo otpions
     *
     * @param string $pageContent Page content
     *
     * @return array|mixed|string|string[]
     */
    public function applyMetaData($pageContent) {
        $title = isset($this->_props['titleInBrowser']) ? $this->_props['titleInBrowser'] : '';
        if ($title && preg_match('/<title>([\S\s]+?)<\/title>/', $pageContent, $titleMatches)) {
            $app = Factory::getApplication();
            $siteName = $app->get('sitename');
            $siteNamePageTitles = $app->get('sitename_pagetitles', 0);
            if ($siteNamePageTitles == 1) {
                $title = Text::sprintf('JPAGETITLE', $siteName, $title);
            } elseif ($siteNamePageTitles == 2) {
                $title = Text::sprintf('JPAGETITLE', $title, $siteName);
            }
            $pageContent = str_replace($titleMatches[0], '<title>' . $title . '</title>', $pageContent);
        }

        $keywords = isset($this->_props['keywords']) ? $this->_props['keywords'] : '';
        if ($keywords) {
            if (preg_match('/<meta\s+?name="keywords"\s+?content="([^"]+?)"\s*?\/?>/', $pageContent, $keywordsMatches)
                && strpos($keywordsMatches[1], $keywords) === false
            ) {
                $pageContent = str_replace($keywordsMatches[0], '<meta name="keywords" content="' . $keywords . ', ' . $keywordsMatches[1] . '" />', $pageContent);
            } else {
                $pageContent = str_replace('<title>', '<meta name="keywords" content="' . $keywords . '" />' . '<title>', $pageContent);
            }
        }

        $desc = isset($this->_props['description']) ? $this->_props['description'] : '';
        if ($desc && !$this->_props['isDefaultLanguage']) {
            if (preg_match('/<meta\s+?name="description"\s+?content="([^"]+?)"\s*?\/?>/', $pageContent, $descMatches)
                && strpos($descMatches[1], $desc) === false
            ) {
                $pageContent = str_replace($descMatches[0], '<meta name="description" content="' . $desc . '" />', $pageContent);
            } else {
                $pageContent = str_replace('<title>', '<meta name="description" content="' . $desc . '" />' . '<title>', $pageContent);
            }
        }

        return $pageContent;
    }


    /**
     * Generate og tags
     *
     * @param string $pageContent Page content
     *
     * @return string|void
     */
    public function appendOpenGraphTags($pageContent) {
        $seoTags = $this->getSeoTags();
        $buildedTags = array();
        foreach ($seoTags as $key => $values) {
            $tag = '<';
            foreach ($values as $property => $value) {
                if ($property == 'tag') {
                    $tag .= $value;
                    continue;
                }
                $tag .= ' ' . $property . '="' . $value . '"';
            }
            $tag .= '>';
            $buildedTags[$key] = $tag;
        }

        $ogTypes = array('og:title', 'og:description', 'og:url', 'og:image', 'og:site_name', 'og:type');
        foreach ($ogTypes as $type) {
            if (preg_match('/<meta[^>]+?property="' . $type . '"[^>]+?>/', $pageContent, $typeMatches)
                && isset($buildedTags[$type])
            ) {
                $pageContent = str_replace($typeMatches[0], $buildedTags[$type], $pageContent);
                unset($buildedTags[$type]);
            }
        }

        $result = implode('', $buildedTags);
        if (isset($settings['colorScheme']) && isset($settings['colorScheme']['colors']) && count($settings['colorScheme']['colors']) > 0) {
            $result .= '<meta name="theme-color" content="' . $settings['colorScheme']['colors'][0] . '">';
        }
        $pageContent = str_replace('</head>', $result . '</head>', $pageContent);

        return $pageContent;
    }

    /**
     * @return array
     */
    public function getSeoTags() {
        $settings = isset($this->_config['siteSettings']) ? json_decode($this->_config['siteSettings'], true) : array();
        $siteDisableOpenGraph = isset($settings['disableOpenGraph']) && $settings['disableOpenGraph'] == 'true' ? true : false;
        if ($siteDisableOpenGraph) {
            return array();
        }

        $config = Factory::getConfig();
        $document = Factory::getDocument();

        $pageTitle = $document->getTitle();
        if ($this->_props['ogTags'] && $this->_props['ogTags']['title']) {
            $pageTitle = $this->_props['ogTags']['title'];
        }
        $pageDesc = $document->getDescription() ? $document->getDescription() : $config->get('sitename');
        if ($this->_props['ogTags'] && $this->_props['ogTags']['description']) {
            $pageDesc = $this->_props['ogTags']['description'];
        }
        $pageUrl = Uri::getInstance()->toString();
        if ($this->_props['ogTags'] && $this->_props['ogTags']['url']) {
            $pageUrl = $this->_props['ogTags']['url'];
        }
        $pageImage = '';
        if ($this->_props['ogTags'] && $this->_props['ogTags']['image']) {
            $pageImage = $this->_props['ogTags']['image'];
        }

        $seoTags = array();
        $seoTags['og:site_name'] = array('tag' => 'meta', 'property' => 'og:site_name', 'content' => $config->get('sitename'));
        $seoTags['og:type'] = array('tag' => 'meta', 'property' => 'og:type', 'content' => 'article');
        $seoTags['og:title'] = array('tag' => 'meta', 'property' => 'og:title', 'content' => $pageTitle);
        $seoTags['og:description'] = array('tag' => 'meta', 'property' => 'og:description', 'content' =>  $pageDesc);
        if ($pageImage) {
            $seoTags['og:image'] = array('tag' => 'meta', 'property' => 'og:image', 'content' => $pageImage);
        }
        $seoTags['og:url'] = array('tag' => 'meta', 'property' => 'og:url', 'content' => $pageUrl);
        return $seoTags;
    }

    /**
     * Build thumbnail page
     *
     * @return mixed
     */
    public function buildThumbnail()
    {
        $ret = <<<EOF
<!DOCTYPE html>
<html>        
    <head>
    <style>
        body {
            cursor: pointer;
        }
    </style>
    {$this->getStyles()}
    </head>
    <body class="{$this->getBodyClass()}" style="{$this->getBodyStyle()}" {$this->getBodyDataBg(true)}>
        {$this->getSectionsHtml()}
    </body>
</html>
EOF;
        return $ret;
    }

    /**
     * Build page with np header&footer option
     *
     * @param string $pageContent Page content
     *
     * @return mixed
     */
    public function buildNpHeaderFooter($pageContent)
    {
        $systemMsgPlaceholderRe = '/<\!--np\_message-->([\s\S]+?)<\!--\/np\_message-->/';
        $systemMsg = '';
        if (preg_match($systemMsgPlaceholderRe, $pageContent, $systemMsgPlaceHolderMatches)) {
            $systemMsg = $systemMsgPlaceHolderMatches[1];
        }

        $placeholderRe = '/<\!--np\_landing-->([\s\S]+?)<\!--\/np\_landing-->/';
        if (!preg_match($placeholderRe, $pageContent, $placeHolderMatches)) {
            return $pageContent;
        }
        $sectionsHtml = $systemMsg . $placeHolderMatches[1];

        $bodyRe = '/(<body[^>]+>)([\s\S]*)(<\/body>)/';
        if (!preg_match($bodyRe, $pageContent, $bodyMatches)) {
            return $pageContent;
        }

        list($bodyStartTag, $bodyContent, $bodyEndTag) = array($bodyMatches[1], $bodyMatches[2], $bodyMatches[3]);

        $bodyStartTagUpdated = str_replace('{bodyClass}', $this->getBodyClass(), $bodyStartTag);
        $bodyStartTagUpdated = str_replace('{bodyStyle}', $this->getBodyStyle(), $bodyStartTagUpdated);
        $bodyStartTagUpdated = str_replace('{bodyDataBg}', $this->getBodyDataBg(true), $bodyStartTagUpdated);

        return str_replace(
            array(
                $bodyStartTag,
                $bodyContent,
                $bodyEndTag,
            ),
            array(
                $bodyStartTagUpdated . $this->getHeader(),
                $sectionsHtml,
                $this->getFooter() . $bodyEndTag,
            ),
            $pageContent
        );
    }

    /**
     * Build page with theme header&footer option
     *
     * @param string $pageContent Page content
     *
     * @return mixed
     */
    public function buildThemeHeaderFooter($pageContent)
    {
        $placeholderRe = '/<\!--np\_content-->([\s\S]+?)<\!--\/np\_content-->/';
        if (!preg_match($placeholderRe, $pageContent, $placeHolderMatches)) {
            return $pageContent;
        }
        $sectionsHtml = $placeHolderMatches[1];

        $bodyRe = '/(<body[^>]+>)([\s\S]*)(<\/body>)/';
        if (!preg_match($bodyRe, $pageContent, $bodyMatches)) {
            return $pageContent;
        }

        list($bodyStartTag, $bodyContent, $bodyEndTag) = array($bodyMatches[1], trim($bodyMatches[2]), $bodyMatches[3]);

        if ($bodyContent == '') {
            $newPageContent = $bodyStartTag . $sectionsHtml . $bodyEndTag;
        } else {
            $newPageContent = $bodyStartTag;
            if (preg_match('/<header[^>]+>[\s\S]*<\/header>/', $bodyContent, $headerMatches)) {
                $newPageContent .= $headerMatches[0];
                if ($this->_isNicepageTheme && class_exists('ThemeHelper') && isset(\ThemeHelper::getInstance()->headerExtraHtml)) {
                    $newPageContent .= \ThemeHelper::getInstance()->headerExtraHtml;
                }
            }
            $newPageContent .= $sectionsHtml;
            if (preg_match('/<footer[^>]+>[\s\S]*<\/footer>/', $bodyContent, $footerMatches)) {
                $newPageContent .= $footerMatches[0];
                if ($this->_isNicepageTheme && class_exists('ThemeHelper') && isset(\ThemeHelper::getInstance()->footerExtraHtml)) {
                    $newPageContent .= \ThemeHelper::getInstance()->footerExtraHtml;
                }
            }
            if (preg_match('/<\/footer>([\s\S]*)/', $bodyContent, $afterFooterContentMatches)) {
                $newPageContent .= $afterFooterContentMatches[1];
            }
            $newPageContent .= $bodyEndTag;
        }
        $pageContent = preg_replace('/(<body[^>]+>)([\s\S]*)(<\/body>)/', '[[body]]', $pageContent);
        $pageContent = str_replace('[[body]]', $newPageContent, $pageContent);
        return $pageContent;
    }

    /**
     * Add custom page properties
     */
    public function setPageProperties()
    {
        $document = Factory::getDocument();
        if ($this->_props['metaTags']) {
            $document->addCustomTag($this->_props['metaTags']);
        }
        if ($this->_props['customHeadHtml']) {
            $document->addCustomTag($this->_props['customHeadHtml']);
        }
        if ($this->_props['metaGeneratorContent'] && $this->_pageView === 'landing') {
            $document->setMetaData('generator', $this->_props['metaGeneratorContent']);
        }
        if ($this->_props['metaReferrer'] && $this->_pageView === 'landing') {
            $document->setMetaData('referrer', 'origin');
        }
    }

    /**
     * Set plugin scripts
     */
    public function setScripts()
    {
        if ($this->_isNicepageTheme !== '1' || $this->_pageView == 'landing') {
            $assets = Uri::root(true) . '/components/com_nicepage/assets';
            HTMLHelper::_('jquery.framework');
            if ((isset($this->_config['jquery']) && $this->_config['jquery'] === '1')) {
                $this->_scripts .= '<script src="' . $assets . '/js/jquery.js"></script>';
            }
            $this->_scripts .= '<script src="' . $assets . '/js/nicepage.js"></script>';
            $getProductsUrl = Uri::root(true) . '/index.php?option=com_nicepage&task=siteproducts&page_id=' . $this->getPageId();
            $this->_scripts .= '<script type="application/javascript"> window._npProductsJsonUrl = "' . $getProductsUrl . '";</script>';
        }
    }

    /**
     * Get plugin scripts
     *
     * @return string
     */
    public function getScripts()
    {
        return $this->_scripts;
    }

    /**
     * Set plugin styles
     */
    public function setStyles()
    {
        $assets = Uri::root(true) . '/components/com_nicepage/assets';

        $siteStyleCss = ColorHelper::buildSiteStyleCss(
            $this->_config,
            $this->_props['pageCssUsedIds'],
            $this->_pagePasswordProtected
        );

        if (!$this->_pagePasswordProtected) {
            $sectionsHead = $this->_props['head'];
        }

        if ($this->_pageView == 'landing' || $this->_pageView == 'thumbnail') {
            $this->_styles = '<link rel="stylesheet" type="text/css" media="all" href="' . $assets . '/css/nicepage.css" rel="stylesheet" id="nicepage-style-css">';
            $this->_styles .= '<link rel="stylesheet" type="text/css" media="all" href="' . $assets . '/css/media.css" rel="stylesheet" id="theme-media-css">';
            $this->_styles .= $this->_props['fonts'];
            $this->_styles .= '<style>' . $siteStyleCss . $sectionsHead . '</style>';
        } else {

            $autoResponsive = isset($this->_config['autoResponsive']) ? !!$this->_config['autoResponsive'] : true;

            if ($autoResponsive && $this->_isNicepageTheme == '0') {
                $sectionsHead = preg_replace('#\/\*RESPONSIVE_MEDIA\*\/([\s\S]*?)\/\*\/RESPONSIVE_MEDIA\*\/#', '', $sectionsHead);
                $this->_styles .= '<link href="' . $assets . '/css/responsive.css" rel="stylesheet">';
            } else {
                $sectionsHead = preg_replace('#\/\*RESPONSIVE_CLASS\*\/([\s\S]*?)\/\*\/RESPONSIVE_CLASS\*\/#', '', $sectionsHead);
                if ($this->_isNicepageTheme == '0') {
                    $this->_styles .= '<link href="' . $assets . '/css/media.css" rel="stylesheet">';
                }
            }
            $dynamicCss = $siteStyleCss . $sectionsHead;
            if ($this->_isNicepageTheme !== '1') {
                $this->_styles .= '<link href="' . $assets . '/css/page-styles.css" rel="stylesheet">';
                $dynamicCss = $this->wrapStyles($dynamicCss);
            }
            $this->_styles .= $this->_props['fonts'];
            $this->_styles .= '<style id="nicepage-style-css">' . $dynamicCss . '</style>';
        }
        $customFontsFilePath = JPATH_BASE . '/images/nicepage-fonts/fonts_' . $this->_props['pageId'] . '.css';
        if (file_exists($customFontsFilePath)) {
            $customFontsFileHref = Uri::root(true) . '/images/nicepage-fonts/fonts_' .  $this->_props['pageId'] . '.css';
            $this->_styles .= '<link href="' . $customFontsFileHref . '" rel="stylesheet">';
        }
    }

    /**
     * Wrap styles by container
     *
     * @param string $dynamicCss Additional styles
     *
     * @return null|string|string[]
     */
    public function wrapStyles($dynamicCss)
    {
        return preg_replace_callback(
            '/([^{}]+)\{[^{}]+?\}/',
            function ($match) {
                $selectors = $match[1];
                $parts = explode(',', $selectors);
                $newSelectors = implode(
                    ',',
                    array_map(
                        function ($part) {
                            if (!preg_match('/html|body|sheet|keyframes/', $part)) {
                                return ' .nicepage-container ' . $part;
                            } else {
                                return $part;
                            }
                        },
                        $parts
                    )
                );
                return str_replace($selectors, $newSelectors, $match[0]);
            },
            $dynamicCss
        );
    }

    /**
     * Get plugin styles
     *
     * @return string
     */
    public function getStyles()
    {
        return $this->_styles;
    }

    /**
     * Set page backlink
     */
    public function setBacklink()
    {
        $backlink = $this->_props['backlink'];
        if ($backlink && ($this->_pageView == 'default' || $this->_pageView === 'landing_with_header_footer')) {
            if ($this->_isNicepageTheme !== '1') {
                $backlink = '<div class="nicepage-container"><div class="'. $this->_props['bodyClass'] .'">' . $backlink . '</div></div>';
            } else {
                $backlink = '';
            }
        }
        $this->_backlink = $backlink;
    }

    /**
     * Get page backlink
     *
     * @return string
     */
    public function getBacklink()
    {
        return $this->_backlink;
    }

    /**
     * @param string $productName Product name
     *
     * @return null
     */
    public function setProductName($productName)
    {
        $this->_productName = $productName;

        $templateName = $this->_productName === 'product-list' ? 'products' : 'product';
        $props = isset($this->_config[$templateName]) ? $this->_config[$templateName] : '';

        if (!$props) {
            return null;
        }
        $props = unserialize(call_user_func('base' . '64_decode', $props));
        $props['pageId'] = $this->getPageId();
        $props['isPreview'] = false;
        $this->_props = $this->prepareProps($props);
        $this->_pageView = 'landing';
    }

    /**
     * Set sections html
     */
    public function setSectionsHtml()
    {
        $isPublic = $this->_pageView == 'thumbnail' ? false : true;
        $options = array(
            'isPublic' => $isPublic,
            'pageId' => $this->_props['pageId'],
        );

        if ($this->_productName) {
            $options['productName'] = $this->_productName;
        }

        $this->_sectionsHtml = NicepageHelpersNicepage::processSectionsHtml($this->_props['publishHtml'], $options);

        if ($this->_pageView == 'thumbnail') {
            preg_match_all('/<section[\s\S]+?<\/section>/', $this->_sectionsHtml, $matches, PREG_SET_ORDER);
            $count = count($matches);
            if ($count > 4) {
                for ($i = 4; $i < $count; $i++) {
                    $this->_sectionsHtml = str_replace($matches[$i], '', $this->_sectionsHtml);
                }
            }
            return;
        }

        $this->setPublishDialogs($this->_sectionsHtml);

        if ($this->_pageView !== 'landing') {
            $autoResponsive = isset($this->_config['autoResponsive']) ? !!$this->_config['autoResponsive'] : true;
            if ($autoResponsive && $this->_isNicepageTheme == '0') {
                $responsiveScript = <<<SCRIPT
<script>
    (function ($) {
        var ResponsiveCms = window.ResponsiveCms;
        if (!ResponsiveCms) {
            return;
        }
        ResponsiveCms.contentDom = $('script:last').parent();
        
        if (typeof ResponsiveCms.recalcClasses === 'function') {
            ResponsiveCms.recalcClasses();
        }
    })(jQuery);
</script>
SCRIPT;
                $this->_sectionsHtml = $responsiveScript . $this->_sectionsHtml;
            }

            if ($this->_isNicepageTheme === '0') {
                $this->_sectionsHtml = '<div class="nicepage-container"><div ' . $this->getBodyDataBg(true) . 'style="' . $this->_props['bodyStyle'] . '" class="' . $this->_props['bodyClass'] . '">' . $this->_sectionsHtml . '</div></div>';
            } else {
                $bodyScript = <<<SCRIPT
<script>
var body = document.body;
    
    body.className += " {$this->_props['bodyClass']}";
    body.style.cssText += " {$this->_props['bodyStyle']}";
    var dataBg = '{$this->getBodyDataBg()}';
    if (dataBg) {
        body.setAttribute('data-bg', dataBg);
    }
</script>
SCRIPT;
                $this->_sectionsHtml = $bodyScript . $this->_sectionsHtml;
            }
        }

        if ($this->_pagePasswordProtected) {
            $this->_sectionsHtml = $this->buildPasswordProtectionTemplate();
        }
    }

    /**
     * Build password protection template
     *
     * @return array|string|string[]
     */
    public function buildPasswordProtectionTemplate() {
        $publishPassword = '';
        $content = $this->fixImagePaths($this->_config['password']);
        if ($content) {
            $passwordItem = json_decode($content, true);
            if ($passwordItem) {
                ob_start();
                echo $passwordItem['styles'];
                echo $passwordItem['php'];
                $publishPassword = ob_get_clean();
                $options = array(
                    'isPublic' => true,
                    'pageId' => 'password',
                );
                $publishPassword = NicepageHelpersNicepage::processSectionsHtml($publishPassword, $options);
            }
        }
        if (!$publishPassword) {
            $publishPassword = $this->defaultPasswordProtectionTemplate();
        }
        $uri = Uri::getInstance()->toString();
        $publishPassword = str_replace('[[action]]', $uri, $publishPassword);
        $publishPassword = str_replace('[[method]]', 'post', $publishPassword);
        $passwordHash = Factory::getApplication()->input->get('password_hash', '');
        if ($passwordHash) {
            $publishPassword .=<<<SCRIPT
<script>
jQuery(function($) {    
    var form = $('.u-password-control form');
    var errorContainer = form.find('.u-form-send-error');
    errorContainer.show();
    setTimeout(function () {
        errorContainer.hide();
    }, 2000);
});
</script>
SCRIPT;
        }
        return $publishPassword;
    }

    /**
     * Get default password protection template
     */
    public function defaultPasswordProtectionTemplate() {
        return <<<FORM
<section>
    <div class="u-clearfix u-sheet">
        <div class="u-form u-password-control" style="text-align: center;">
            <form action="[[action]]" method="[[method]]">
                <p>PROTECTED CONTENT</p>
                <p>
                    <label for="pwbox"> 
                        <input placeholder="Enter your password" name="password" id="pwbox" type="password" size="20">
                        <input name="password_hash" id="hashbox" type="hidden" size="20">
                    </label>                    
                    <div class="u-form-submit">
                        <a href="#" class="u-btn u-btn-submit u-button-style">Submit</a>
                        <input type="submit" name="Submit" value="Enter" class="u-form-control-hidden">
                        <div class="u-form-send-error">Password is incorrect</div>
                    </div>
                </p>
            </form>
        </div>
    </div>
</section>
FORM;
    }

    /**
     * Get sections html
     *
     * @return string
     */
    public function getSectionsHtml()
    {
        $html = $this->_sectionsHtml;
        if ($this->_pageView !== 'thumbnail' && $this->_productName === '') {
            $html .= $this->getEditLinkHtml();
        }
        return $html;
    }

    /**
     * Set page cookies consent
     */
    public function setCookiesConsent()
    {
        if ($this->_isNicepageTheme === '1' && $this->_pageView !== 'landing') {
            return;
        }

        if (isset($this->_config['cookiesConsent'])) {
            $cookiesConsent = json_decode($this->_config['cookiesConsent'], true);
            if ($cookiesConsent && (!$cookiesConsent['hideCookies'] || $cookiesConsent['hideCookies'] === 'false')) {
                $content = $this->fixImagePaths($cookiesConsent['publishCookiesSection']);
                if ($this->_pageView == 'landing') {
                    $this->_cookiesConsent = $content;
                } else {
                    $this->_cookiesConsent = '<div class="nicepage-container"><div class="' . $this->_props['bodyClass'] . '">' . $content . '</div></div>';
                }
                $this->_cookiesConfirmCode = $cookiesConsent['cookieConfirmCode'];
            }
        }
    }

    /**
     * Get page cookies consent
     *
     * @return string
     */
    public function getCookiesConsent()
    {
        return $this->_cookiesConsent;
    }

    /**
     * Get page cookies confirm code
     *
     * @return string
     */
    public function getCookiesConfirmCode()
    {
        return $this->_cookiesConfirmCode;
    }

    /**
     * Set backtotop in content
     */
    public function setBackToTop() {
        $hideBackToTop = $this->_props['hideBackToTop'];
        if (isset($this->_config['backToTop']) && !$hideBackToTop) {
            $backToTop = $this->fixImagePaths($this->_config['backToTop']);
            $backToTop = str_replace('[[site_path_editor]]', Uri::root(), $backToTop);
            if ($this->_pageView == 'landing') {
                $this->_backToTop = $backToTop;
            } else {
                $this->_backToTop = '<div class="nicepage-container"><div class="' . $this->_props['bodyClass'] . '">' . $backToTop . '</div></div>';
            }
        }
    }

    /**
     * Get page backlink
     *
     * @return string
     */
    public function getBackToTop()
    {
        return $this->_backToTop;
    }

    /**
     * Set canonical url
     */
    public function setCanonicalUrl() {
        $this->_canonicalUrl = $this->_props['canonical'];
    }

    /**
     * @return string
     */
    public function getCanonicalUrl() {
        $canonical = $this->_canonicalUrl;
        if (!$canonical && $this->_pageView == 'landing') {
            $canonical = Uri::getInstance()->toString();
        }
        return $canonical ? '<link rel="canonical" href="' . $canonical . '">' : '';
    }

    /**
     * Get page view
     *
     * @return mixed|string
     */
    public function getPageView() {
        return $this->_pageView;
    }

    /**
     * Set page view
     *
     * @param string $view Page view
     */
    public function setPageView($view) {
        $this->_pageView = $view;
    }

    /**
     * Get body style
     *
     * @return mixed
     */
    public function getBodyStyle() {
        return $this->_props['bodyStyle'];
    }

    /**
     * Get body class
     *
     * @return mixed
     */
    public function getBodyClass() {
        return $this->_props['bodyClass'];
    }

    /**
     * Get body data bg attr value
     *
     * @param bool $withAttr Build with attr
     *
     * @return mixed|string
     */
    public function getBodyDataBg($withAttr = false) {
        $bodyDataBg = $this->_props['bodyDataBg'];
        if ($bodyDataBg && $withAttr) {
            $bodyDataBg = 'data-bg="' . $bodyDataBg . '"';
        }
        return $bodyDataBg;
    }

    /**
     * Get edit link html
     *
     * @return string
     */
    public function getEditLinkHtml() {
        $html = '';
        $adminUrl = Uri::root() . '/administrator';
        $icon = dirname($adminUrl) . '/components/com_nicepage/assets/images/button-icon.png?r=' . md5(mt_rand(1, 100000));
        $link = $adminUrl . '/index.php?option=com_nicepage&task=nicepage.autostart&postid=' . $this->_row->id;
        if ($this->_params->get('access-edit')) {
            $html= <<<HTML
        <div><a href="$link" target="_blank" class="edit-nicepage-button">Edit Page</a></div>
        <style>
            a.edit-nicepage-button {
                position: fixed;
                top: 0;
                right: 0;
                background: url($icon) no-repeat 5px 6px;
                background-size: 16px;
                color: #4184F4;
                font-family: Georgia;
                margin: 10px;
                display: inline-block;
                padding: 5px 5px 5px 25px;
                font-size: 14px;
                line-height: 18px;
                background-color: #fff;
                border-radius: 3px;
                border: 1px solid #eee;
                z-index: 9999;
                text-decoration: none;
            }
            a.edit-nicepage-button:hover {
                color: #BC5A5B;
            }
        </style>
HTML;
        }
        return $html;
    }

    /**
     * Prepare page props
     *
     * @param array $props Page props
     *
     * @return mixed
     */
    public function prepareProps($props)
    {
        $props['bodyClass']   = isset($props['bodyClass']) ? $props['bodyClass'] : '';
        if (strpos($props['bodyClass'], '-mode') === false) {
            $props['bodyClass'] = $props['bodyClass'] . ' u-xl-mode';
        }
        $props['bodyStyle']   = isset($props['bodyStyle']) ? $props['bodyStyle'] : '';
        $props['bodyDataBg']  = isset($props['bodyDataBg']) ? $props['bodyDataBg'] : '';
        if ($props['bodyDataBg']) {
            $props['bodyDataBg'] = str_replace('"', '\'', $props['bodyDataBg']);
        }
        $props['head']        = isset($props['head']) ? $props['head'] : '';
        $props['fonts']       = isset($props['fonts']) ? $props['fonts'] : '';
        $props['publishHtml'] = isset($props['publishHtml']) ? $props['publishHtml'] : '';
        $props['publishHtmlTranslations'] = isset($props['publishHtmlTranslations']) ? $props['publishHtmlTranslations'] : array();

        $onContentPrepare = true;
        $lang = $this->getCurrentLanguage($props['publishHtmlTranslations']);
        $props['isDefaultLanguage'] = true;
        if ($lang && isset($props['publishHtmlTranslations'][$lang])) {
            $props['publishHtml'] = $props['publishHtmlTranslations'][$lang];
            if (isset($props['seoTranslations']) && isset($props['seoTranslations'][$lang])) {
                $seoLang = $props['seoTranslations'][$lang];
                $props['titleInBrowser'] = $seoLang['title'];
                $props['keywords'] = $seoLang['keywords'];
                $props['description'] = $seoLang['description'];
                $props['canonical'] = isset($seoLang['canonical']) ? $seoLang['canonical'] : '';
            }
            $props['isDefaultLanguage'] = false;
        }
        $publishHtml = $props['publishHtml'];
        if (!$props['isPreview'] && $this->_row && property_exists($this->_row, 'text') && $props['isDefaultLanguage']) {
            $text = $this->_row->text;
            if (preg_match('/<\!--np\_fulltext-->([\s\S]+?)<\!--\/np\_fulltext-->/', $text, $fullTextMatches)) {
                $publishHtml = $fullTextMatches[1];
                $onContentPrepare = false;
            }
        }

        // Process image paths
        $publishHtml          = $this->fixBackgroundImageQuots($publishHtml);
        $props['publishHtml'] = $this->fixImagePaths($publishHtml);
        $props['head']        = $this->fixImagePaths($props['head']);
        $props['bodyStyle']   = $this->fixImagePaths($props['bodyStyle']);
        $props['bodyDataBg']  = $this->fixImagePaths($props['bodyDataBg']);
        $props['fonts']       = $this->fixImagePaths($props['fonts']);

        // Process backlink
        if ($this->_config) {
            $hideBacklink = isset($this->_config['hideBacklink']) ? (bool)$this->_config['hideBacklink'] : false;
            $backlink = $props['backlink'];
            $props['backlink'] = $hideBacklink ? str_replace('u-backlink', 'u-backlink u-hidden', $backlink) : $backlink;
        }

        // Process content
        if ($onContentPrepare && $this->_row) {
            $this->_row->doubleСall = true;
            $currentText = $this->_row->text;
            $currentPostId = $this->_row->id;
            $this->_row->text = $props['publishHtml'];
            $this->_row->id = '-1';
            PluginHelper::importPlugin('content');
            Factory::getApplication()->triggerEvent('onContentPrepare', array($this->_context, &$this->_row, &$this->_params, 0));
            $props['publishHtml'] = $this->_row->text;
            $this->_row->text = $currentText;
            $this->_row->id = $currentPostId;
        }

        $props['backlink']       = isset($props['backlink']) ? $props['backlink'] : '';
        $props['pageCssUsedIds'] = isset($props['pageCssUsedIds']) ? $props['pageCssUsedIds'] : '';
        $props['hideHeader']     = isset($props['hideHeader']) ? $props['hideHeader'] : false;
        $props['hideFooter']     = isset($props['hideFooter']) ? $props['hideFooter'] : false;
        $props['hideBackToTop']  = isset($props['hideBackToTop']) ? $props['hideBackToTop'] : false;
        $props['ogTags']         = isset($props['ogTags']) && $props['ogTags'] ? $props['ogTags'] : '';
        if ($props['ogTags']) {
            $props['ogTags']['image'] = $this->fixImagePaths($props['ogTags']['image']);
        }

        $props['metaTags']       = isset($props['metaTags']) ? $props['metaTags'] : '';
        $props['customHeadHtml'] = isset($props['customHeadHtml']) ? $props['customHeadHtml'] : '';
        $props['metaGeneratorContent'] = isset($props['metaGeneratorContent']) ? $props['metaGeneratorContent'] : '';
        $props['metaReferrer'] = isset($props['metaReferrer']) ? $props['metaReferrer'] : '';
        $props['canonical'] = isset($props['canonical']) ? $props['canonical'] : '';
        $props['dialogs'] = isset($props['dialogs']) ? $props['dialogs'] : json_encode(array());
        $props['passwordProtection'] = isset($props['passwordProtection']) ? $props['passwordProtection'] : '';

        return $props;
    }

    /**
     * Get current language
     *
     * @param array $htmlTranslations Translation html list
     *
     * @return mixed
     */
    public function getCurrentLanguage($htmlTranslations) {
        $lang = Factory::getApplication()->input->get('lang', '');

        if (!$htmlTranslations) {
            return $lang;
        }

        if (!$lang) {
            $locales = Factory::getLanguage()->getLocale() ?: array();
            foreach ($locales as $locale) {
                if (isset($htmlTranslations[$locale])) {
                    $lang = $locale;
                    break;
                }
            }
        }
        return $lang;
    }

    /**
     * Fixing background image
     *
     * @param string $content Page content
     *
     * @return string
     */
    public function fixBackgroundImageQuots($content) {
        $content = str_replace('url(&quot;', 'url(\'', $content);
        $content = str_replace('&quot;)', '\')', $content);
        return $content;
    }

    /**
     * Fix image paths
     *
     * @param string $content Content
     *
     * @return mixed
     */
    public function fixImagePaths($content) {
        return str_replace('[[site_path_live]]', Uri::root(), $content);
    }

    /**
     * Get page instance
     *
     * @param null   $pageId  Page id
     * @param string $context Component context
     * @param null   $row     Component row
     * @param null   $params  Component params
     *
     * @return Page
     */
    public static function getInstance($pageId, $context, &$row, &$params)
    {
        $pageTable = NicepageHelpersNicepage::getSectionsTable();
        if (!$pageTable->load(array('page_id' => $pageId))) {
            return null;
        }

        if (!is_object(self::$_instance)) {
            self::$_instance = new self($pageTable, $context, $row, $params);
        }

        return self::$_instance;
    }
}

