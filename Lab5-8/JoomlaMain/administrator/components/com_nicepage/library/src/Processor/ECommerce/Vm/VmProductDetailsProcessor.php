<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor\ECommerce\Vm;

defined('_JEXEC') or die;

use ShopFunctions;
use CurrencyDisplay;
class VmProductDetailsProcessor extends VmProductItem
{
    private $_html = '';
    private $_products;

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
     * @return void
     */
    public function build()
    {
        $params = $this->getControlParameters($this->_html, 'product');
        $this->_options = array_merge($this->_options, $params);

        if ($this->_options['productSource']) {
            $options = array('productId' => $this->_options['productSource']);
        } else {
            $options = array('categoryName' => 'Recent products');
        }
        $options['pageId'] = $this->_options['pageId'];
        $products = array_slice($this->_getProducts($options), 0, 1);

        $this->processProductItem($products);
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
        $this->_products = $products;
        $this->_html = $this->_setProductItem($this->_html, $this->_products);
    }

    /**
     * Get build result
     *
     * @return string
     */
    public function getResult() {
        $this->_html = '<div class="product-container">' . $this->_html . '</div>';
        $this->_html .= $this->_appendJsonLd($this->_products);
        return $this->_html;
    }

    /**
     * Append product json ld
     *
     * @param array $products Product collection
     *
     * @return mixed
     */
    private function _appendJsonLd($products) {
        $jsonLd = '';
        if (count($products) < 1) {
            return $jsonLd;
        }
        $product = $products[0];
        $availability = ($product['product-item']->product_in_stock - $product['product-item']->product_ordered) < 1 ? 'OutOfStock' : 'InStock';
        $priceCurrency = ShopFunctions::getCurrencyByID(CurrencyDisplay::getInstance()->getCurrencyForDisplay(), 'currency_code_3');
        $price = '';
        if ($product['product-price']) {
            $parts = explode(' ', $product['product-price']);
            $price = $parts[0];
        }
        ob_start();
        ?>
        <script type="application/ld+json">
            {
                "@context": "http://schema.org",
                "@type": "Product",
                "name": <?php echo json_encode(strip_tags($product['product-title'])); ?>,
    "image": "<?php echo $product['product-image']; ?>",
    "description": <?php echo json_encode(strip_tags($product['product-desc'])); ?>,
    "offers": {
        "@type": "Offer",
        "availability": "https://schema.org/<?php echo $availability; ?>",
        "url": "<?php echo $product['product-title-link']; ?>",
        "itemCondition": "NewCondition",
        "priceCurrency": "<?php echo $priceCurrency; ?>",
        "price": "<?php echo $price; ?>"
    }
}
        </script>
        <?php
        $jsonLd = ob_get_clean();
        return $jsonLd;
    }
}
