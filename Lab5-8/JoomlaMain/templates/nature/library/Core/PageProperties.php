<?php
defined('_JEXEC') or die;

class CorePageProperties
{
    private $_properties = array();

    public function __construct($page, $type = 'default')
    {
        $themeHelper = ThemeHelper::getInstance();
        if ($type === '404') {
            $content = isset($themeHelper->pageContent) && $themeHelper->pageContent ? $themeHelper->pageContent : '';
        } else {
            $content = $page->getBuffer('component');
        }

        if (preg_match('/<\!--component_settings-->([\s\S]+?)<\!--\/component_settings-->/', $content, $matches)) {
            $this->_properties = json_decode($matches[1], true);
            $content = str_replace($matches[0], '', $content);
        }

        if ($type === '404') {
            $themeHelper->pageContent = $content;
        } else {
            $page->setBuffer($content, 'component');
        }
    }

    public function getStyles()
    {
        return isset($this->_properties['styles']) ? $this->_properties['styles'] : '';
    }

    public function getBodyClass($defValue)
    {
        return 'class="' . (isset($this->_properties['bodyClass']) ? $this->_properties['bodyClass'] : $defValue) . '"';
    }

    public function getBodyStyle()
    {
        return isset($this->_properties['bodyStyle']) && $this->_properties['bodyStyle'] ? ' style="' . $this->_properties['bodyStyle'] . '"' : '';
    }

    public function getBackToTop()
    {
        return isset($this->_properties['backToTop']) && $this->_properties['backToTop'] ? $this->_properties['backToTop'] : '';
    }

    public function getPopupDialogs()
    {
        return isset($this->_properties['popupDialogs']) && $this->_properties['popupDialogs'] ? $this->_properties['popupDialogs'] : '';
    }

    public function showHeader()
    {
        return isset($this->_properties['hideHeader']) && $this->_properties['hideHeader'] ? false : true;
    }

    public function showFooter()
    {
        return isset($this->_properties['hideFooter']) && $this->_properties['hideFooter'] ? false : true;
    }

    public function getLocalFontsFile()
    {
        return isset($this->_properties['localFontsFile']) && $this->_properties['localFontsFile'] ? $this->_properties['localFontsFile'] : '';
    }
}
