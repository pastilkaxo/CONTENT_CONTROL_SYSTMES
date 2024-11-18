<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use NP\Processor\ECommerce\Site\SiteProductListProcessor;
use NP\Processor\ECommerce\Site\SiteProductDetailsProcessor;
use NP\Processor\ECommerce\Vm\VmProductListProcessor;
use NP\Processor\ECommerce\Vm\VmProductDetailsProcessor;

class ProductsProcessor
{
    private $_pageId;
    private $_productName = null;
    private $_productsList = array();
    private $_productsPosition = 0;
    private $_productsOptions = array();

    /**
     * ProductsProcessor constructor.
     *
     * @param string $pageId      Page id
     * @param int    $productName Product Name
     */
    public function __construct($pageId = '', $productName = null)
    {
        $this->_pageId = $pageId;
        $this->_productName = $productName;
    }

    /**
     * Process products
     *
     * @param string $content Content
     *
     * @return string|string[]|null
     */
    public function process($content) {
        $position = Factory::getApplication()->input->get('position', '');
        if ($position && $this->_productName === 'product-list') {
            header('Content-Type: text/html');
            exit($this->processProductsByAjaxLoad($content));
        } else {
            $content = preg_replace_callback('/<\!--products-->([\s\S]+?)<\!--\/products-->/', array(&$this, '_processProducts'), $content);
            $content = preg_replace_callback('/<\!--product-->([\s\S]+?)<\!--\/product-->/', array(&$this, '_processProduct'), $content);
        }

        if (strpos($content, 'none-post-image') !== false) {
            $content = str_replace('u-products-item', 'u-products-item u-invisible', $content);
        }

        return $this->fixers($content);
    }

    /**
     * Process one products
     *
     * @param string $content Page content
     *
     * @return int
     */
    public function processProductsByAjaxLoad($content) {
        preg_replace_callback('/<\!--products-->([\s\S]+?)<\!--\/products-->/', array(&$this, '_processProducts'), $content);
        $position = Factory::getApplication()->input->get('position', 1);
        $result = array_slice($this->_productsList, $position - 1, 1);
        return $this->fixers(count($result) > 0 ? $result[0] : '');
    }

    /**
     * Process products
     *
     * @param array $productsMatch Matches
     *
     * @return string|string[]|null
     */
    private function _processProducts($productsMatch) {
        $html = $productsMatch[1];
        $this->_productsPosition += 1;
        $this->_productsOptions = array(
            'product' => null,
            'paginationProps' => null,
            'quantityExists' => false,
            'showSecondImage' => strpos($html, 'u-show-second-image') !== false ? true : false,
            'isShop' => strpos($html, 'data-products-datasource="site"') !== false ? true : false,
            'pageId' => $this->_pageId,
            'productsPosition' => $this->_productsPosition,
            'productName' => $this->_productName,
        );

        if ($this->_productsOptions['isShop'] || $this->_productName) {
            $processor = new SiteProductListProcessor($html, $this->_productsOptions);
        } else {
            $processor = new VmProductListProcessor($html, $this->_productsOptions);
        }
        $processor->build();
        $result = $processor->getResult();

        array_push($this->_productsList, $result);

        return $result;
    }

    /**
     * Process product
     *
     * @param array $productMatch Matches
     *
     * @return string|string[]|null
     */
    private function _processProduct($productMatch) {
        $html = $productMatch[1];
        $this->_productsOptions = array(
            'isShop' => strpos($html, 'data-products-datasource="site"') !== false ? true: false,
            'pageId' => $this->_pageId,
            'productName' => $this->_productName,
        );

        if ($this->_productsOptions['isShop'] || $this->_productName) {
            $processor = new SiteProductDetailsProcessor($html, $this->_productsOptions);
        } else {
            $processor = new VmProductDetailsProcessor($html, $this->_productsOptions);
        }
        $processor->build();

        return $processor->getResult();
    }

    /**
     * @param string $content Content
     *
     * @return array|string|string[]
     */
    public function fixers($content) {
        $content = $this->_fixVmScripts($content);
        $content = $this->_fixDollarSymbol($content);
        $content = $this->_fixPageId($content);
        return $content;
    }

    /**
     * @param string $content Content
     *
     * @return array|string|string[]
     */
    private function _fixDollarSymbol($content)
    {
        return str_replace('_dollar_symbol_', '$', $content);
    }

    /**
     * @param string $content Content
     *
     * @return array|string|string[]
     */
    private function _fixPageId($content) {
        return str_replace('[[pageId]]', $this->_pageId, $content);
    }

    /**
     * Fix virtuemart scripts
     *
     * @param string $content Content
     *
     * @return mixed
     */
    private function _fixVmScripts($content)
    {
        $document = Factory::getDocument();
        $scripts = $document->_scripts;
        $index = 0;
        foreach ($scripts as $filePath => $script) {
            $index++;
            if (preg_match('/com\_virtuemart.+vmprices\.js/', $filePath)) {
                $before = array_slice($scripts, 0, $index - 1);
                $after = array_slice($scripts, $index);
                $newFilePath = str_replace('com_virtuemart', 'com_nicepage', $filePath);
                $new = array($newFilePath => $script);
                $scripts = array_merge($before, $new, $after);
            }
        }
        $document->_scripts = $scripts;

        $content = str_replace('Virtuemart.product($("form.product"));', 'Virtuemart.product($(".product"));', $content);
        return $content;
    }
}
