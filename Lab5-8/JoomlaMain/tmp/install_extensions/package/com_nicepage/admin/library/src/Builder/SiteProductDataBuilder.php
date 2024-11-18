<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Builder;

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

/**
 * Class ProductDataBuilder
 */
class SiteProductDataBuilder extends DataBuilder
{
    private $_item;
    private $_data;

    /**
     * ProductDataBuilder constructor.
     *
     * @param object $item Article object
     */
    public function __construct($item)
    {
        $this->_item = json_decode(json_encode($item));
        $this->processImages();

        $base = array(
            'product-title' => $this->title(),
            'product-title-link' => $this->titleLink(),
            'product-desc' => $this->content(),
            'product-full-desc' => $this->desc(),
            'product-image' => $this->image(),
            'product-gallery' => $this->gallery(),
            'product-variations' => $this->variations(),
            'product-tabs' => $this->tabs(),
            'product-json' => $this->getJson(),
            'product-id' => $this->getId(),
            'product-is-new' => $this->isNew(),
            'product-sale' => $this->sale(),
            'product-categories' => $this->categories(),
            'product-out-of-stock' => $this->outOfStock(),
            'product-sku' => $this->sku(),
        );
        $this->_data = array_merge($base, $this->quantity(), $this->button(), $this->price());
    }

    /**
     * Process images
     *
     * @return void
     */
    public function processImages() {
        if (property_exists($this->_item, 'images')) {
            foreach ($this->_item->images as &$image) {
                $image->url = str_replace('[[site_path_editor]]', Uri::root(true), $image->url);
            }
        } else {
            $this->_item->images = array();
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_item->id;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        return htmlspecialchars(json_encode($this->_item));
    }

    /**
     * Get product data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Get product title
     *
     * @return mixed
     */
    public function title()
    {
        return $this->_item->title;
    }

    /**
     * Get product content
     *
     * @return false|string|string[]|null
     */
    public function content()
    {
        $desc = $this->_item->description;
        return $this->excerpt($desc, 150, '...', true);
    }

    /**
     * Get product desc
     *
     * @return false|string|string[]|null
     */
    public function desc()
    {
        return property_exists($this->_item, 'fullDescription') ? $this->_item->fullDescription : '';
    }

    /**
     * Get product title link
     *
     * @return string
     */
    public function titleLink()
    {
        return Uri::root() . 'index.php?option=com_nicepage&view=product&page_id=[[pageId]]&product_name=product-' . $this->_item->id . '"';
    }

    /**
     * Get product image
     *
     * @return string
     */
    public function image()
    {
        return count($this->_item->images) > 0 ? $this->_item->images[0]->url : '';
    }

    /**
     * Get product gallery
     *
     * @return array
     */
    public function gallery()
    {
        $images = array();
        foreach ($this->_item->images as $image) {
            array_push($images, $image->url);
        }
        return $images;
    }

    /**
     * Get product quantity
     *
     * @return array
     */
    public function quantity()
    {
        return array('product-quantity-notify' => '', 'product-quantity-label' => '', 'product-quantity-html' => '');
    }

    /**
     * Get product button
     *
     * @return array
     */
    public function button()
    {
        return array('product-button-text' => 'product-template', 'product-button-link' => $this->titleLink(), 'product-button-html' => '');
    }

    /**
     * Get price
     *
     * @param string $type Price type
     *
     * @return string
     */
    public function getPrice($type) {
        if (!property_exists($this->_item, $type)) {
            return $type == 'fullPrice' ? $this->_item->price : '';
        }

        $price = $this->_item->{$type};

        $lang = Factory::getApplication()->input->get('lang', '');
        if (!$lang) {
            return $price;
        }

        if (!property_exists($this->_item, 'translations')) {
            return $price;
        }

        if (!property_exists($this->_item->translations, $lang)) {
            return $price;
        }

        if (!property_exists($this->_item->translations->{$lang}, $type)) {
            return $price;
        }

        return $this->_item->translations->{$lang}->{$type};
    }

    /**
     * Get product price
     *
     * @return array
     */
    public function price()
    {
        $price = str_replace('$', '_dollar_symbol_', $this->getPrice('fullPrice'));
        $oldPrice = str_replace('$', '_dollar_symbol_', $this->getPrice('fullPriceOld'));
        return array(
            'product-price' => $price,
            'product-old-price' => $oldPrice,
        );
    }

    /**
     * Product is new
     */
    public function isNew() {
        $currentDate = (int) (microtime(true) * 1000);
        if (property_exists($this->_item, 'created')) {
            $createdDate = $this->_item->created;
        } else {
            $createdDate = $currentDate;
        }
        $milliseconds30Days = 30 * (60 * 60 * 24 * 1000); // 30 days in milliseconds
        if (($currentDate - $createdDate) <= $milliseconds30Days) {
            return true;
        }
        return false;
    }

    /**
     * Sale for product
     */
    public function sale() {
        $price = 0;
        if (property_exists($this->_item, 'price')) {
            $price = (float) $this->_item->price;
        }
        $oldPrice = 0;
        if (property_exists($this->_item, 'oldPrice')) {
            $oldPrice = (float) $this->_item->oldPrice;
        }
        $sale = '';
        if ($price && $oldPrice && $price < $oldPrice) {
            $sale = '-' . (int)(100 - ($price * 100 / $oldPrice)) . '%';
        }
        return $sale;
    }

    /**
     * Categories for product
     */
    public function categories() {
        return $this->_item->categoriesData;
    }

    /**
     * OutOfStock product
     */
    public function outOfStock() {
        return $this->_item->outOfStock;
    }

    /**
     * Sku product
     */
    public function sku() {
        return $this->_item->sku;
    }

    /**
     * Get product variations
     *
     * @return array
     */
    public function variations()
    {
        $variations = array();
        return $variations;
    }

    /**
     * Get product tabs
     *
     * @return array
     */
    public function tabs() {
        $tabs = array();
        return $tabs;
    }
}
