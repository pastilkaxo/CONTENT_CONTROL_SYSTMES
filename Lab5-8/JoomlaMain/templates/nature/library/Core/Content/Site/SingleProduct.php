<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

Core::load("Core_Content_ProductBase");
class CoreContentSiteSingleProduct extends CoreContentProductBase
{
    protected $_product;
    protected $_themeName;
    public $id;
    public $title;
    public $titleLink;
    public $description;
    public $fullDescription;
    public $categoriesData;

    protected function __construct($product)
    {
        $this->_themeName = Factory::getApplication()->getTemplate();
        $this->_product = $product;
        $this->processImages();


        $this->id = $this->_product['id'];
        $this->title = $this->_product['title'];
        $this->titleLink = 'index.php?option=com_ajax&format=html&template=' . $this->_themeName .
            '&method=product&product_name=product-' . $this->id;
        $this->description = $this->_product['description'];
        $this->fullDescription = isset($this->_product['fullDescription']) ? $this->_product['fullDescription'] : '';
        $this->categoriesData = $this->_product['categoriesData'];
    }

    public function processImages() {
        $imagePath = URI::root(true) . '/templates/' . $this->_themeName . '/';
        if (isset($this->_product['images'])) {
            foreach ($this->_product['images'] as &$image) {
                $image['url'] = $imagePath . $image['url'];
            }
        } else {
            $this->_product['images'] = array();
        }
    }
    public function getImage() {
        return count($this->_product['images']) > 0 ? $this->_product['images'][0]['url'] : '';
    }

    public function getGalleryImages() {
        $galleryImages = array();
        foreach ($this->_product['images'] as $image) {
            array_push($galleryImages, $image['url']);
        }
        return $galleryImages;
    }

    public function variations() {
        return array();
    }

    public function getPrice($product, $type) {
        $price = $product[$type];

        $lang = Factory::getApplication()->input->get('lang', '');
        if (!$lang) {
            return $price;
        }
        if (!isset($product['translations'])) {
            return $price;
        }
        if (!isset($product['translations'][$lang])) {
            return $price;
        }
        if (!isset($product['translations'][$lang][$type])) {
            return $price;
        }
        return $product['translations'][$lang][$type];
    }

    public function getRegularPrice() {
        $price = $this->getPrice($this->_product, 'fullPrice');
        return array(
            'price' => $this->addZeroCentsProcess($price),
            'priceWithZeroCents' => $this->addZeroCentsProcess($price, true),
            'callForPrice' => '',
        );
    }

    public function getOldPrice() {
        $price = $this->getPrice($this->_product, 'fullPriceOld');
        return array(
            'price' => $this->addZeroCentsProcess($price),
            'priceWithZeroCents' => $this->addZeroCentsProcess($price, true),
            'callForPrice' => '',
        );
    }

    public function isNew() {
        $currentDate = (int) (microtime(true) * 1000);
        if (isset($this->_product['created'])) {
            $createdDate = $this->_product['created'];
        } else {
            $createdDate = $currentDate;
        }
        $milliseconds30Days = 30 * (60 * 60 * 24 * 1000); // 30 days in milliseconds
        $isNew = false;
        if (($currentDate - $createdDate) <= $milliseconds30Days) {
            $isNew = true;
        }
        return $isNew;
    }

    public function sale() {
        $price = 0;
        if (isset($this->_product['price'])) {
            $price = (float) $this->_product['price'];
        }
        $oldPrice = 0;
        if (isset($this->_product['oldPrice'])) {
            $oldPrice = (float) $this->_product['oldPrice'];
        }
        $sale = '';
        if ($price && $oldPrice && $price < $oldPrice) {
            $sale = '-' . (int)(100 - ($price * 100 / $oldPrice)) . '%';
        }
        return $sale;
    }

    public function outOfStock() {
        return $this->_product['outOfStock'];
    }

    public function sku() {
        return $this->_product['sku'];
    }

    public function toJson()
    {
        return htmlspecialchars(json_encode($this->_product));
    }
}
