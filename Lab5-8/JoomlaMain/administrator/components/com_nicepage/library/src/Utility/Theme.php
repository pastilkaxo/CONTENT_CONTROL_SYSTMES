<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Utility;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

/**
 * Class Nicepage_Theme_Nicepage
 */
class Theme
{
    /**
     * Get logo info from plugin
     *
     * @param array   $defaults      Default values
     * @param boolean $setThemeSizes Set themes sizes
     *
     * @return array
     */
    public static function getLogoInfo($defaults = array(), $setThemeSizes = false)
    {
        $app = Factory::getApplication();
        $rootPath = str_replace(DIRECTORY_SEPARATOR, '/', JPATH_ROOT);

        $info = array();
        $info['src'] = isset($defaults['src']) ? $defaults['src'] : '';
        $info['src_path'] = '';
        if ($info['src']) {
            $info['src'] = preg_match('#^(http:|https:|//)#', $info['src']) ? $info['src'] :
                Uri::root() . 'templates/' . $app->getTemplate() . $info['src'];
            $info['src_path'] = $rootPath . '/templates/' . $app->getTemplate() . $defaults['src'];
        }
        $info['href'] = isset($defaults['href']) ? $defaults['href'] : Uri::base(true);

        $themeParams = $app->getTemplate(true)->params;
        if ($themeParams->get('logoFile')) {
            $info['src'] = Uri::root() . $themeParams->get('logoFile');
            $info['src_path'] = $rootPath . '/' . $themeParams->get('logoFile');
        }
        if ($themeParams->get('logoLink')) {
            $info['href'] = $themeParams->get('logoLink');
        }

        $parts = explode(".", $info['src_path']);
        $extension = end($parts);
        $isSvgFile = strtolower($extension) == 'svg' ? true : false;

        if ($setThemeSizes) {
            $style = '';
            $themeLogoWidth = $themeParams->get('logoWidth', '');
            $themeLogoHeight = $themeParams->get('logoHeight', '');
            if ($themeLogoWidth) {
                $style .= "max-width: " . $themeLogoWidth . "px !important;\n";
            }
            if ($themeLogoHeight) {
                $style .= "max-height: " . $themeLogoHeight . "px !important;\n";
            }

            if ($isSvgFile) {
                if ($themeLogoWidth > $themeLogoHeight && $themeLogoWidth) {
                    $style .= "width: " . $themeLogoWidth . "px  !important\n";
                }
                if ($themeLogoWidth <= $themeLogoHeight && $themeLogoHeight) {
                    $style .= "height: " . $themeLogoHeight . "px  !important\n";
                }
            }

            if ($style) {
                $document = Factory::getDocument();
                $document->addStyleDeclaration('.u-logo img {' . $style . '}');
            }
        }

        return $info;
    }

    /**
     * Get theme params by name
     *
     * @param string $name Name of option
     *
     * @return string
     */
    public static function getThemeParams($name) {
        $template = Factory::getApplication()->getTemplate();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, params')
            ->from('#__template_styles')
            ->where('template = ' . $db->quote($template))
            ->where('client_id = 0');
        $db->setQuery($query);
        $templates = $db->loadObjectList('id');

        if (count($templates) < 1) {
            return '';
        }

        $site = Factory::getApplication('site');
        $menu = $site->getMenu('site');
        $item = $menu->getActive();

        $id         = is_object($item) ? $item->template_style_id : 0;
        $template   = isset($templates[$id]) ? $templates[$id] : array_shift($templates);

        $registry = new Registry();
        $registry->loadString($template->params);
        return $registry->get($name, '');
    }

    /**
     * Build tag
     *
     * @param string $tag        Tag name
     * @param array  $attributes Array attrs
     * @param string $content    Content
     *
     * @return string
     */
    public static function funcTagBuilder($tag, $attributes = array(), $content = '') {
        $result = '<' . $tag;
        foreach ($attributes as $name => $value) {
            if (is_string($value)) {
                if (!empty($value)) {
                    $result .= ' ' . $name . '="' . $value . '"';
                }
            } else if (is_array($value)) {
                $values = array_filter($value);
                if (count($values)) {
                    $result .= ' ' . $name . '="' . implode(' ', $value) . '"';
                }
            }
        }
        $result .= '>' . $content . '</' . $tag . '>';
        return $result;
    }

    /**
     * Styling default controls (input, button and еtс.)
     *
     * @param string $content Content
     *
     * @return string|string[]|null
     */
    public static function stylingDefaultControls($content) {
        $content = preg_replace('/<form([\s\S]+?)class="form/', '<form$1class="u-form form', $content);
        $content = preg_replace('/<input([\s\S]+?)class="input/', '<input$1class="u-input input', $content);
        $content = preg_replace('/<button([\s\S]+?)class="btn/', '<button$1class="u-btn u-button-style btn', $content);
        return $content;
    }
}
