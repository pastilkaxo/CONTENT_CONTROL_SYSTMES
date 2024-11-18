<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor\ECommerce\Site;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use NP\Utility\CategoriesFilter;
use NP\Utility\Pagination;

class SiteProductListProcessor extends SiteProductItem
{
    protected $_html = '';
    protected $_options;

    /**
     * @param string $html    Control html
     * @param array  $options Global parameters
     */
    public function __construct($html, $options)
    {
        $this->_html = $html;
        $this->_options = $options;
        parent::__construct($options);
    }

    /**
     * Build control
     *
     * @return void
     */
    public function build() {
        //backward if code
        if (!$this->_options['productName'] && strpos($this->_html, 'product_title_content') === false) {
            $this->_oldVariantBuild();
            return;
        }

        $params = $this->getControlParameters($this->_html, 'products');
        $this->_options = array_merge($this->_options, $params);

        $options = array(
            'name' => $this->_options['productName'] ?: 'product-list',
            'pageId' => $this->_options['pageId'],
            'categoryId' => $this->_options['categoryId'],
        );

        $products = $this->_getProducts($options);

        if (count($products) < 1) {
            $this->_html = '';
            return;
        }

        $this->_options['paginationProps'] = $this->getPaginationParameters($products, $this->_options);
        $this->processProductItem($products);

        $categoriesFilterProps = $this->getCategoriesFilterParameters($this->_options);
        $this->processCategoriesFilter($this->_getCategories(), $categoriesFilterProps);
    }

    /**
     * Old variant build
     *
     * @return void
     */
    private function _oldVariantBuild() {
        $this->_html = preg_replace_callback('/<\!--product_button-->([\s\S]+?)<\!--\/product_button-->/', array(&$this, '_setButtonData'), $this->_html);
        $this->_html = preg_replace_callback('/<\!--product_image-->([\s\S]+?)<\!--\/product_image-->/', array(&$this, '_setImageData'), $this->_html);
        $this->_html = preg_replace_callback('/<\!--product_category-->([\s\S]+?)<\!--\/product_category-->/', array(&$this, '_setProductCategory'), $this->_html);
    }

    /**
     * @param array $products Product List
     *
     * @return void
     */
    public function processProductItem($products) {
        $this->_html = $this->_setProductItem($this->_html, $products);
    }

    /**
     * Process categories filter in product list
     *
     * @param array $categoriesModel Page html
     * @param array $options         Page id
     *
     * @return void
     */
    public function processCategoriesFilter($categoriesModel, $options) {
        $filter = new CategoriesFilter($categoriesModel, $options);
        $this->_html = $filter->process($this->_html);
    }

    /**
     * Get build result
     *
     * @return array|string|string[]|null
     */
    public function getResult() {
        if (strpos($this->_html, 'u-cms') === false) {
            $this->_html = preg_replace('/ u-products /', ' u-products u-cms ', trim($this->_html));
        }
        if (strpos($this->_html, 'data-products-datasource="site"') === false) {
            $this->_html = preg_replace('/^<div/', '<div data-products-datasource="site"', trim($this->_html));
        }
        if (strpos($this->_html, 'data-products-id') === false) {
            $this->_html = preg_replace('/^<div/', '<div data-products-id="' . $this->_options['productsPosition'] . '"', trim($this->_html));
        }
        return $this->_html;
    }
}
