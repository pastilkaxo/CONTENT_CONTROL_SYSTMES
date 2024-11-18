<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Models;

defined('_JEXEC') or die;

use NP\Builder\SiteProductDataBuilder;
use NP\Utility\ProductsHelper;
use Joomla\CMS\Factory;
use \NicepageHelpersNicepage;

class ContentModelCustomSiteProducts
{
    private $_options;
    private $_helper = null;

    /**
     * ContentModelCustomProducts constructor.
     *
     * @param array $options options
     */
    public function __construct($options = array())
    {
        $this->_options = $options;
        $config = NicepageHelpersNicepage::getConfig();
        if (isset($config['productsJson'])) {
            $this->_helper = new ProductsHelper($config['productsJson']);
        }
    }

    /**
     * Get products
     *
     * @return array
     */
    public function getProducts() {
        $products = array();

        if (!$this->_helper) {
            return $products;
        }

        $items = null;
        $inputs = Factory::getApplication()->input;
        $view = $inputs->get('view', 'product');
        $catId = $view === 'product' ? $inputs->get('catid', '') : $this->_options['categoryId'];
        $siteProducts = $this->_helper->getProducts($catId);
        if ($this->_options['name'] == 'product-list') {
            $items = $siteProducts;
        } else {
            foreach ($siteProducts as $product) {
                if ($this->_options['name'] === ('product-' . $product['id'])) {
                    $items = array($product);
                    break;
                }
            }
        }


        if (empty($items)) {
            return $products;
        }

        foreach ($items as $item) {
            $builder = new SiteProductDataBuilder($item);
            $product = $builder->getData();
            array_push($products, $product);
        }

        return $products;
    }

    /**
     * Get categories
     *
     * @return array
     */
    public function getCategories() {
        if (!$this->_helper) {
            return array();
        }
        return $this->_helper->getCategories();
    }
}
