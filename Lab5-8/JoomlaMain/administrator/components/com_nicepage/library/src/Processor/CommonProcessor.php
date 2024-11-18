<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor;

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use NP\Utility\Utility;
use \NicepageHelpersNicepage;

class CommonProcessor
{
    /**
     * Processing of default image path
     *
     * @param string $content Page content
     *
     * @return mixed
     */
    public function processDefaultImage($content)
    {
        // replace default image placeholder
        $url = Uri::root() . 'components/com_nicepage/assets/images/nicepage-images/default-image.jpg';
        return str_replace('[image_default]', $url, $content);
    }

    /**
     * Processing of forms
     *
     * @param string $content Page content
     * @param string $pageId  Type id
     *
     * @return mixed
     */
    public function processForm($content, $pageId)
    {
        // process source is joomla
        $componentUrl = Uri::root() . 'index.php?option=com_nicepage';
        $content = preg_replace('/(<form[^>]*action=[\'\"]+).*?([\'\"][^>]*source=[\'\"]joomla)/', '$1' . $componentUrl . '&task=sendmail$2', $content);
        $content = preg_replace_callback(
            '/<form[^>]+redirect-address=[\'\"](.*?)[\'\"][^>]*>/',
            function ($matches) {
                if (preg_match('/<form[^>]+redirect=[\'\"]true[\'\"]/', $matches[0])) {
                    return $matches[0] . '<input type="hidden" name="redirect" value="' . $matches[1] . '">';
                }
                return $matches[0];
            },
            $content
        );
        // process source is customphp
        if ($pageId) {
            $isPreview = Factory::getApplication()->input->getBool('isPreview', false) ? '&isPreview=true' : '';
            $content = preg_replace(
                '/(<form[^>]*action=[\'\"]+)\[\[form\-(.*?)\]\]([\'\"][^>]*source=[\'\"]customphp)/',
                '$1' . $componentUrl . '&task=form&id=' . $pageId . $isPreview . '&formId=$2$3',
                $content
            );
        }
        return $content;
    }

    /**
     * Process all custom php on the page
     *
     * @param string $content Page content
     *
     * @return mixed
     */
    public function processCustomPhp($content) {
        $content = preg_replace('/data-custom-php=["\']+(<\!--custom_php-->[\s\S]+?<\!--\/custom_php-->)["\']+([^>]*?>)/', '$2$1', $content);
        $content = preg_replace_callback(
            '/<\!--custom_php-->([\s\S]+?)<\!--\/custom_php-->/',
            function ($matches) {
                $code = trim($matches[1]);
                $code = str_replace("<?php", "", $code);
                $code = str_replace("?>", "", $code);
                $code = str_replace("&quot;", "\"", $code);
                ob_start();
                @eval($code);
                return ob_get_clean();
            },
            $content
        );
        return $content;
    }

    /**
     * Backward - add https protocol to google maps url when site with http
     *
     * @param string $content
     *
     * @return string
     */
    public function processGoogleMaps($content) {
        if (!Utility::isSSL() && strpos($content, 'src="//maps.google.com/maps') !== false) {
            $content = str_replace('src="//maps.google.com/maps', 'src="https://maps.google.com/maps', $content);
        }
        return $content;
    }

    /**
     * @param string  $content
     * @param integer $pageId
     * @param integer $propsId
     *
     * @return array|mixed|string|string[]
     */
    public function processLinks($content, $pageId, $propsId) {
        if ($pageId === 'header' || $pageId === 'footer') {
            $pageId = $propsId;
        }

        $config = NicepageHelpersNicepage::getConfig();
        $products = array();
        if (isset($config['productsJson'])) {
            if (is_array($config['productsJson'])) {
                $products = $config['productsJson'];
            } else {
                $jsonData = json_decode($config['productsJson'], true);
                $products = $jsonData['products'];
            }
        }

        if (count($products) < 1) {
            return $content;
        }
        $content = preg_replace_callback(
            '/href=[\"\']{1}(product-?\d+)[\"\']{1}/',
            function ($hrefMatch) {
                return 'href="page_id=[[pageId]]&product_name=' . $hrefMatch[1] . '"';
            },
            $content
        );
        $content = preg_replace_callback(
            '/href=[\"\']{1}(product-list)[\"\']{1}/',
            function () {
                return 'href="page_id=[[pageId]]&product_name=product-list"';
            },
            $content
        );
        $productViewPath = Uri::root() . 'index.php?option=com_nicepage&view=product';
        $content = str_replace('page_id=[[pageId]]', $productViewPath . '&page_id=' . $pageId, $content);
        return $content;
    }
}
