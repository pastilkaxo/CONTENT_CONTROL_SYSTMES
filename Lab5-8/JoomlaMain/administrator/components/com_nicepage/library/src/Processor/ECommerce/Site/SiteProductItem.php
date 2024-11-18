<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor\ECommerce\Site;

defined('_JEXEC') or die;

use NP\Processor\ECommerce\ProductItem;
use NP\Models\ContentModelCustomSiteProducts;
use Joomla\CMS\Uri\Uri;

class SiteProductItem extends ProductItem
{
    protected $_options;

    /**
     * @param array $options Options
     */
    protected function __construct($options)
    {
        parent::__construct($options);
        $this->_options = $options;
    }

    /**
     * Set product button
     *
     * @param array $buttonMatch Image match
     *
     * @return mixed
     */
    protected function _setButtonData($buttonMatch) {
        $buttonHtml = $buttonMatch[1];

        $controlOptions = array();
        if (preg_match('/<\!--options_json--><\!--([\s\S]+?)--><\!--\/options_json-->/', $buttonHtml, $matches)) {
            $controlOptions = json_decode($matches[1], true);
            $buttonHtml = str_replace($matches[0], '', $buttonHtml);
        }

        $goToProduct = false;
        if (isset($controlOptions['clickType']) && $controlOptions['clickType'] === 'go-to-page') {
            $goToProduct = true;
        }

        //backward code
        if ($this->_options['isShop'] && !$this->_options['product']) {
            return preg_replace_callback(
                '/href=[\"\']{1}(product-?\d+)[\"\']{1}/',
                function ($hrefMatch) {
                    $productViewPath = Uri::root() . 'index.php?option=com_nicepage&view=product';
                    return 'href="' . $productViewPath . '&page_id=' . $this->_options['pageId'] . '&product_name=' . $hrefMatch[1] . '"';
                },
                $buttonHtml
            );
        }

        if ($goToProduct) {
            $buttonLink = $this->_options['product']['product-button-link'];
            $buttonHtml = preg_replace('/(href=[\'"])([\s\S]+?)([\'"])/', '$1' . $buttonLink . '$3', $buttonHtml);
        }

        $buttonHtml = str_replace('data-product-id=""', 'data-product-id="' . $this->_options['product']['product-id']  . '"', $buttonHtml);
        $buttonHtml = str_replace('<a', '<a data-product="' . $this->_options['product']['product-json']  . '"', $buttonHtml);
        return $buttonHtml;
    }

    /**
     * Set product image
     *
     * @param string $imageMatch Image match
     *
     * @return mixed
     */
    protected function _setImageData($imageMatch) {
        $imageHtml = $imageMatch[1];

        //backward code
        if ($this->_options['isShop'] && !$this->_options['product']) {
            return preg_replace_callback(
                '/href=[\"\']{1}(product-?\d+)[\"\']{1}/',
                function ($hrefMatch) {
                    $productViewPath = Uri::root() . 'index.php?option=com_nicepage&view=product';
                    return 'href="' . $productViewPath . '&page_id=' . $this->_options['pageId'] . '&product_name=' . $hrefMatch[1] . '"';
                },
                $imageHtml
            );
        }

        return parent::_setImageData($imageMatch);
    }

    /**
     * Set category
     *
     * @param array $categoryMatch Category match
     *
     * @return array|string|string[]|null
     */
    protected function _setProductCategory($categoryMatch) {
        $categoryHtml = $categoryMatch[1];

        //backward code
        if ($this->_options['isShop'] && !$this->_options['product']) {
            return preg_replace_callback(
                '/href=[\"\']{1}product-?\d+#category-(\d+)[\"\']{1}/',
                function ($hrefMatch) {
                    $categoryViewPath = Uri::root() . 'index.php?option=com_nicepage&view=product';
                    return 'href="' . $categoryViewPath . '&page_id=' . $this->_options['pageId'] . '&product_name=product-list&catid=' . $hrefMatch[1] . '"';
                },
                $categoryHtml
            );
        }
        return parent::_setProductCategory($categoryMatch);
    }

    /**
     * Get products by source
     *
     * @param array $options Source options
     *
     * @return array
     */
    protected function _getProducts($options)
    {
        $model = new ContentModelCustomSiteProducts($options);
        return $model->getProducts();
    }

    /**
     * Get categories from model
     *
     * @param array $options Source options
     *
     * @return array
     */
    protected function _getCategories($options = array())
    {
        $model = new ContentModelCustomSiteProducts($options);
        return $model->getCategories();
    }
}
