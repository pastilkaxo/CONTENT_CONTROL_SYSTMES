<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor\ECommerce\Vm;

defined('_JEXEC') or die;

use NP\Utility\CategoriesFilter;
use NP\Utility\Pagination;

class VmProductListProcessor extends VmProductItem
{
    private $_html = '';

    /**
     * @param string $html    Control html
     * @param array  $options Global options
     */
    public function __construct($html, $options)
    {
        $this->_html = $html;
        parent::__construct($options);
    }

    /**
     * Build control
     *
     * @return string|void
     */
    public function build() {

        $params = $this->getControlParameters($this->_html, 'products');
        $this->_options = array_merge($this->_options, $params);

        $options = array(
            'categoryName' => $this->_options['productsSource'],
            'categoryId' => $this->_options['categoryId'],
        );

        $products = $this->_getProducts($options);

        if (count($products) < 1) {
            $this->_html = '';
            return '';
        }

        $this->_options['paginationProps'] = $this->getPaginationParameters($products, $this->_options);
        $this->processProductItem($products);

        $categoriesFilterProps = $this->getCategoriesFilterParameters($this->_options);
        $this->processCategoriesFilter($this->_getCategories(), $categoriesFilterProps);

        $this->processPagination();
    }

    /**
     * Process control
     *
     * @param array $products Product list
     *
     * @return void
     */
    public function processProductItem($products)
    {
        $this->_html = $this->_setProductItem($this->_html, $products);
    }

    /**
     * Process categories filter in product list
     *
     * @param array $categoriesModel Page html
     * @param array $options         Options
     *
     * @return array|string|string[]|null
     */
    public function processCategoriesFilter($categoriesModel, $options) {
        $filter = new CategoriesFilter($categoriesModel, $options);
        $this->_html = $filter->process($this->_html);
    }

    /**
     * Process pagination
     *
     * @return array|string|string[]|null
     */
    public function processPagination() {
        $this->_html = preg_replace_callback('/<\!--products_pagination-->([\s\S]+?)<\!--\/products_pagination-->/', array(&$this, '_processProductsPagination'), $this->_html);
    }

    /**
     * Process pagination
     *
     * @param array $paginationMatch Matches
     *
     * @return false|mixed|string
     */
    private function _processProductsPagination($paginationMatch) {
        if (!$this->_options['paginationProps']) {
            return '';
        }
        $paginationHtml = $paginationMatch[1];
        $paginationStyleOptions = array();
        if (preg_match('/<\!--products_pagination_options_json--><\!--([\s\S]+?)--><\!--\/products_pagination_options_json-->/', $paginationHtml, $matches)) {
            $paginationStyleOptions = json_decode($matches[1], true);
        }
        $pagination = new Pagination($this->_options['paginationProps'], $paginationStyleOptions);
        return $pagination->getPagination();
    }

    /**
     * Get build result
     *
     * @return string
     */
    public function getResult() {
        return $this->_html;
    }
}
