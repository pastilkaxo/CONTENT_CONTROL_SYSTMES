<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor\ECommerce\Site;

defined('_JEXEC') or die;

class SiteProductDetailsProcessor extends SiteProductItem
{
    protected $_html = '';
    protected $_options;

    /**
     * @param string $html    Control html
     * @param array  $options Global options
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
    public function build()
    {
        //backward if code
        if (!$this->_options['productName'] && strpos($this->_html, 'product_title_content') === false) {
            $this->_oldVariantBuild();
            return;
        }

        $params = $this->getControlParameters($this->_html, 'product');
        $this->_options = array_merge($this->_options, $params);
        $options = array(
            'name' => $this->_options['productName'] ?: ($this->_options['productSource'] ? 'product-' . $this->_options['productSource'] :  'product-list'),
            'pageId' => $this->_options['pageId'],
        );

        $products = array_slice($this->_getProducts($options), 0, 1);
        $this->processProductItem($products);
    }

    /**
     * Old variant build
     *
     * @return void
     */
    public function _oldVariantBuild() {
        $this->_html = preg_replace_callback('/<\!--product_category-->([\s\S]+?)<\!--\/product_category-->/', array(&$this, '_setProductCategory'), $this->_html);
    }

    /**
     * Process items
     *
     * @param array $products Product list
     *
     * @return void
     */
    public function processProductItem($products) {
        $this->_html = $this->_setProductItem($this->_html, $products);
    }

    /**
     * Get build result
     *
     * @return mixed|string
     */
    public function getResult() {
        return $this->_html;
    }
}
