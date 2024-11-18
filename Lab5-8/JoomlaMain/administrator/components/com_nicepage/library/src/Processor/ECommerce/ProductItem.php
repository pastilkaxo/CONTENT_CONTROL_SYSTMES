<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor\ECommerce;

use Joomla\CMS\Factory;
use vmJsApi;

defined('_JEXEC') or die;

abstract class ProductItem
{
    protected $_options;
    protected $_addZeroCents;

    /**
     * @param array $options Global parameters
     */
    protected function __construct($options)
    {
        // Initialization:
        $this->_options = $options;
    }

    /**
     * Set title
     *
     * @param string $titleMatch Title match
     *
     * @return mixed|string|string[]|null
     */
    protected function _setTitleData($titleMatch) {
        $titleHtml = $titleMatch[1];
        $titleHtml = preg_replace_callback(
            '/<\!--product_title_content-->([\s\S]+?)<\!--\/product_title_content-->/',
            function ($titleContentMatch) {
                return isset($this->_options['product']['product-title']) ? $this->_options['product']['product-title'] : $titleContentMatch[1];
            },
            $titleHtml
        );
        $titleLink = isset($this->_options['product']['product-title-link']) ? $this->_options['product']['product-title-link'] : '#';
        $titleHtml = preg_replace('/(href=[\'"])([\s\S]+?)([\'"])/', '$1' . $titleLink . '$3', $titleHtml);
        return $titleHtml;
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

        $isBackgroundImage = strpos($imageHtml, '<div') !== false ? true : false;

        $link = isset($this->_options['product']['product-title-link']) ? $this->_options['product']['product-title-link'] : '';
        $src = isset($this->_options['product']['product-image']) ? $this->_options['product']['product-image'] : '';

        if (!$src) {
            return $isBackgroundImage ? $imageHtml : '<div class="none-post-image" style="display: none;"></div>';
        }

        if ($isBackgroundImage) {
            $imageHtml = str_replace('<div', '<div data-product-control="' . $link . '"', $imageHtml);
            if (strpos($imageHtml, 'data-bg') !== false) {
                $imageHtml = preg_replace('/(data-bg=[\'"])([\s\S]+?)([\'"])/', '$1url(' . $this->_options['product']['product-image'] . ')$3', $imageHtml);
            } else {
                $imageHtml = str_replace('<div', '<div' . ' style="background-image:url(' . $this->_options['product']['product-image'] . ')"', $imageHtml);
            }
        } else {
            $imageHtml = preg_replace('/(src=[\'"])([\s\S]+?)([\'"])/', '$1' . $this->_options['product']['product-image'] . '$3 style="cursor:pointer;" data-product-control="' . $link . '"', $imageHtml);
        }

        if (isset($this->_options['showSecondImage']) && $this->_options['showSecondImage']
            && count($this->_options['product']['product-gallery']) > 1
        ) {
            $imageHtml .= '<img src="' . $this->_options['product']['product-gallery'][1] . '" class="u-product-second-image">';
        }
        return $imageHtml;
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

        return preg_replace_callback(
            '/<\!--product_category_link-->([\s\S]+?)<\!--\/product_category_link-->/',
            function ($linkMatch) {
                if (count($this->_options['product']['product-categories']) < 1) {
                    return '';
                }
                $linkHtml = $linkMatch[1];
                $categories = $this->_options['product']['product-categories'];
                $result = '';
                foreach ($categories as $i => $category) {
                    $newCategoryHtml = preg_replace('/(href=[\'"])([\s\S]+?)([\'"])/', '$1' . $category->id . '$3', $linkHtml);
                    $title = ($i > 0 ? ', ' : '') . $category->title;
                    $newCategoryHtml = preg_replace('/<\!--product_category_link_content-->([\s\S]+?)<\!--\/product_category_link_content-->/', $title, $newCategoryHtml);
                    $result .= $newCategoryHtml;
                }
                $this->_options['product']['product-categories'] = array();
                return $result;
            },
            $categoryHtml
        );
    }

    /**
     * Set text
     *
     * @param string $textMatch Text match
     *
     * @return mixed|string|string[]|null
     */
    protected function _setTextData($textMatch) {
        $textHtml = $textMatch[1];
        $textHtml = preg_replace_callback(
            '/<\!--product_content_content-->([\s\S]+?)<\!--\/product_content_content-->/',
            function ($contentMatch) {
                return isset($this->_options['product']['product-desc']) ? $this->_options['product']['product-desc'] : $contentMatch[1];
            },
            $textHtml
        );
        return $textHtml;
    }

    /**
     * Set desc
     *
     * @param array $descMatch Desc match
     *
     * @return array|string|string[]|null
     */
    protected function _setDescriptionData($descMatch) {
        $descHtml = $descMatch[1];
        $descHtml = preg_replace_callback(
            '/<\!--product_description_content-->([\s\S]+?)<\!--\/product_description_content-->/',
            function ($contentMatch) {
                return isset($this->_options['product']['product-full-desc']) ? $this->_options['product']['product-full-desc'] : $contentMatch[1];
            },
            $descHtml
        );
        return $descHtml;
    }

    /**
     * Set tabs data
     *
     * @param array $quantityMatch Quantity match
     *
     * @return mixed|string|string[]|null
     */
    protected function _setQuantityData($quantityMatch) {
        $quantityHtml = $quantityMatch[1];

        if ($this->_options['product']['product-quantity-notify']) {
            return $this->_options['product']['product-quantity-notify'];
        }

        if (!$this->_options['product']['product-quantity-html']) {
            return '';
        }

        $quantityHtml = preg_replace_callback(
            '/<\!--product_quantity_label_content-->([\s\S]+?)<\!--\/product_quantity_label_content-->/',
            function ($quantityLabelContentMatch) {
                return isset($this->_options['product']['product-quantity-label']) ? $this->_options['product']['product-quantity-label'] : $quantityLabelContentMatch[1];
            },
            $quantityHtml
        );

        $quantityHtml = preg_replace_callback(
            '/<\!--product_quantity_input-->([\s\S]+?)<\!--\/product_quantity_input-->/',
            function ($quantityInputMatch) {
                $quantityInputHtml = $quantityInputMatch[1];
                preg_match('/class=[\'"](.*?)[\'"]/', $quantityInputHtml, $inputClassMatch);
                $newQuantityInputHtml = str_replace('js-recalculate', 'js-recalculate ' . $inputClassMatch[1], $this->_options['product']['product-quantity-html']);
                $newQuantityInputHtml = str_replace('quantity-input', '', $newQuantityInputHtml);
                return $newQuantityInputHtml;
            },
            $quantityHtml
        );

        $quantityHtml = str_replace('minus', 'quantity-minus', $quantityHtml);
        $quantityHtml = str_replace('plus', 'quantity-plus', $quantityHtml);
        $quantityHtml = str_replace('disabled', '', $quantityHtml);

        $this->_options['quantityExists'] = true;

        return $quantityHtml;
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

        if ($this->_options['product']['product-button-text'] === 'product-template') {
            return $buttonHtml;
        }

        $isOnlyCatalog = !$this->_options['product']['product-button-text'] ? true : false;
        if ($isOnlyCatalog) {
            return '';
        }
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
        if ($this->_options['product']['product-button-html'] && isset($controlOptions['content']) && $controlOptions['content']) {
            $this->_options['product']['product-button-text'] = $controlOptions['content'];
        }
        $buttonHtml = preg_replace_callback(
            '/<\!--product_button_content-->([\s\S]+?)<\!--\/product_button_content-->/',
            function ($buttonContentMatch) {
                return isset($this->_options['product']['product-button-text']) ? $this->_options['product']['product-button-text'] : $buttonContentMatch[1];
            },
            $buttonHtml
        );
        if ($this->_options['product']['product-button-html'] && !$goToProduct) {
            $buttonHtml = str_replace('[[button]]', $buttonHtml, $this->_options['product']['product-button-html']);
            $defaultQuantityHtml = '<input type="hidden" class="quantity-input js-recalculate" name="quantity[]" value="1">';
            $buttonHtml = str_replace('[[quantity]]', !$this->_options['quantityExists'] ? $defaultQuantityHtml : '', $buttonHtml);
            $buttonHtml = str_replace('<a', '<a name="addtocart"', $buttonHtml);
            $buttonLink = '#';
        } else {
            $buttonLink = $this->_options['product']['product-button-link'];
        }
        $buttonHtml = preg_replace('/(href=[\'"])([\s\S]+?)([\'"])/', '$1' . $buttonLink . '$3', $buttonHtml);

        vmJsApi::jPrice();
        vmJsApi::cssSite();
        vmJsApi::jDynUpdate();

        $buttonHtml .= vmJsApi::writeJS();
        return $buttonHtml;
    }

    /**
     * Set product price
     *
     * @param array $priceMatch Price match
     *
     * @return mixed|string|string[]|null
     */
    private function _setPriceData($priceMatch) {
        $priceHtml = $priceMatch[1];
        $this->_addZeroCents = strpos($priceHtml, 'data-add-zero-cents="true"') !== false ? true : false;

        $priceHtml = preg_replace_callback(
            '/<\!--product_regular_price-->([\s\S]+?)<\!--\/product_regular_price-->/',
            function ($regularPriceMatch) {
                if ($this->_options['product']['product-price']) {
                    $price = preg_quote($this->_options['product']['product-price']);
                    $price = $this->addZeroCentsProcess($price, $this->_addZeroCents);
                    return preg_replace('/<\!--product_regular_price_content-->([\s\S]+?)<\!--\/product_regular_price_content-->/', $price, $regularPriceMatch[1]);
                } else {
                    return '';
                }
            },
            $priceHtml
        );

        $priceHtml = preg_replace_callback(
            '/<\!--product_old_price-->([\s\S]+?)<\!--\/product_old_price-->/',
            function ($oldPriceMatch) {
                $oldPrice = preg_quote($this->_options['product']['product-old-price']);
                $oldPrice = $this->addZeroCentsProcess($oldPrice, $this->_addZeroCents);
                if ($this->_options['product']['product-old-price'] && $this->_options['product']['product-old-price'] !== $this->_options['product']['product-price']) {
                    return preg_replace('/<\!--product_old_price_content-->([\s\S]+?)<\!--\/product_old_price_content-->/', $oldPrice, $oldPriceMatch[1]);
                } else {
                    return '';
                }
            },
            $priceHtml
        );

        return $priceHtml;
    }

    /**
     * Set gallery data
     *
     * @param array $galleryMatch Gallery match
     *
     * @return string
     */
    protected function _setGalleryData($galleryMatch) {
        $galleryHtml = $galleryMatch[1];
        $galleryData = $this->_options['product']['product-gallery'];

        if (count($galleryData) < 1) {
            return '';
        }

        $controlOptions = array();
        if (preg_match('/<\!--options_json--><\!--([\s\S]+?)--><\!--\/options_json-->/', $galleryHtml, $matches)) {
            $controlOptions = json_decode($matches[1], true);
            $galleryHtml = str_replace($matches[0], '', $galleryHtml);
        }

        $maxItems = -1;
        if (isset($controlOptions['maxItems']) && $controlOptions['maxItems']) {
            $maxItems = (int) $controlOptions['maxItems'];
        }

        if ($maxItems !== -1 && count($galleryData) > $maxItems) {
            $galleryData = array_slice($galleryData, 0, $maxItems);
        }

        $galleryItemRe = '/<\!--product_gallery_item-->([\s\S]+?)<\!--\/product_gallery_item-->/';
        preg_match($galleryItemRe, $galleryHtml, $galleryItemMatch);
        $galleryItemHtml = str_replace('u-active', '', $galleryItemMatch[1]);

        $galleryThumbnailRe = '/<\!--product_gallery_thumbnail-->([\s\S]+?)<\!--\/product_gallery_thumbnail-->/';
        $galleryThumbnailHtml = '';
        if (preg_match($galleryThumbnailRe, $galleryHtml, $galleryThumbnailMatch)) {
            $galleryThumbnailHtml = $galleryThumbnailMatch[1];
        }

        $newGalleryItemListHtml = '';
        $newThumbnailListHtml = '';
        foreach ($galleryData as $key => $img) {
            $newGalleryItemHtml = $key == 0 ? str_replace('u-gallery-item', 'u-gallery-item u-active', $galleryItemHtml) : $galleryItemHtml;
            $newGalleryItemListHtml .= preg_replace('/(src=[\'"])([\s\S]+?)([\'"])/', '$1' . $img . '$3', $newGalleryItemHtml);
            if ($galleryThumbnailHtml) {
                $newThumbnailHtml = preg_replace('/data-u-slide-to=([\'"])([\s\S]+?)([\'"])/', 'data-u-slide-to="' . $key . '"', $galleryThumbnailHtml);
                $newThumbnailListHtml .= preg_replace('/(src=[\'"])([\s\S]+?)([\'"])/', '$1' . $img . '$3', $newThumbnailHtml);
            }
        }

        $galleryParts = preg_split($galleryItemRe, $galleryHtml, -1, PREG_SPLIT_NO_EMPTY);
        $newGalleryHtml = $galleryParts[0] . $newGalleryItemListHtml . $galleryParts[1];

        $newGalleryParts = preg_split($galleryThumbnailRe, $newGalleryHtml, -1, PREG_SPLIT_NO_EMPTY);
        return $newGalleryParts[0] . $newThumbnailListHtml . $newGalleryParts[1];
    }

    /**
     * Set variations data
     *
     * @param array $variationsMatch Variations match
     *
     * @return mixed|string|string[]|null
     */
    protected function _setVariationsData($variationsMatch) {
        $variationsHtml = $variationsMatch[1];
        $variationsData = $this->_options['product']['product-variations'];

        if (count($variationsData) < 1) {
            return '';
        }

        $variationRe = '/<\!--product_variation-->([\s\S]+?)<\!--\/product_variation-->/';
        preg_match($variationRe, $variationsHtml, $variationMatch);

        $newVariationListHtml = '';
        foreach ($variationsData as $i => $variationData) {
            $newVariationHtml = str_replace('<select', '<select ' . $variationData['s_attributes'], $variationMatch[1]);
            $newVariationHtml = str_replace('u-input ', 'u-input ' . $variationData['s_classes'] . ' ', $newVariationHtml);
            $newVariationHtml = preg_replace('/<\!--product_variation_label_content-->([\s\S]+?)<\!--\/product_variation_label_content-->/', $variationData['title'], $newVariationHtml);
            preg_match('/<\!--product_variation_option-->([\s\S]+?)<\!--\/product_variation_option-->/', $newVariationHtml, $optionMatch);
            $optionHtml = $optionMatch[1];

            $options = $variationData['options'];
            $newOptionsHtml = '';
            foreach ($options as $option) {
                $newOptionHtml = preg_replace('/<\!--product_variation_option_content-->([\s\S]+?)<\!--\/product_variation_option_content-->/', $option['text'], $optionHtml);
                if ($option['selected']) {
                    $newOptionHtml = str_replace('<option', '<option selected="selected"', $newOptionHtml);
                }
                $newOptionHtml = preg_replace('/(value=[\'"])([\s\S]+?)([\'"])/', '$1[[value]]$3', $newOptionHtml);
                $newOptionHtml = str_replace('[[value]]', $option['value'], $newOptionHtml);
                $newOptionsHtml .= $newOptionHtml;
            }
            if ($i !== 0) {
                $newVariationHtml = '<div style="margin-top: 10px;">' . $newVariationHtml . '</div>';
            }
            $newVariationParts = preg_split('/<\!--product_variation_option-->([\s\S]+?)<\!--\/product_variation_option-->/', $newVariationHtml, -1, PREG_SPLIT_NO_EMPTY);
            $newVariationListHtml .= $newVariationParts[0] . $newOptionsHtml . $newVariationParts[1];
        }

        $variationsParts = preg_split($variationRe, $variationsHtml, -1, PREG_SPLIT_NO_EMPTY);
        $newVariationsHtml = $variationsParts[0] . $newVariationListHtml . $variationsParts[1];
        $newVariationsHtml = str_replace('u-product-variations ', 'u-product-variations product-field-display ', $newVariationsHtml);
        return $newVariationsHtml;
    }

    /**
     * Set tabs data
     *
     * @param array $tabsMatch Tabs match
     *
     * @return mixed|string|string[]|null
     */
    protected function _setTabsData($tabsMatch) {
        $tabsHtml = $tabsMatch[1];
        $tabsData = $this->_options['product']['product-tabs'];

        if (count($tabsData) < 1) {
            return '';
        }

        $tabItemRe = '/<\!--product_tabitem-->([\s\S]+?)<\!--\/product_tabitem-->/';
        preg_match($tabItemRe, $tabsHtml, $tabItemMatch);
        $tabItemLinkClassRe = '/(class=[\'"])(.*?u-tab-link.*?)([\'"])/';
        preg_match($tabItemLinkClassRe, $tabItemMatch[1], $tabItemLinkClassMatch);
        $classesParts = explode(' ', $tabItemLinkClassMatch[2]);
        $key = array_search('active', $classesParts);
        if ($key !== false) {
            array_splice($classesParts, $key, 1);
        }
        $tabItemHtml = preg_replace($tabItemLinkClassRe, '$1' . implode(' ', $classesParts) . '$3', $tabItemMatch[1]);

        $tabPaneRe = '/<\!--product_tabpane-->([\s\S]+?)<\!--\/product_tabpane-->/';
        preg_match($tabPaneRe, $tabsHtml, $tabPaneMatch);
        $tabPaneHtml = str_replace('u-tab-active', '', $tabPaneMatch[1]);

        $newTabItemListHtml = '';
        $newTabPaneListHtml = '';
        foreach ($tabsData as $key => $tab) {
            $newTabItemHtml = preg_replace('/<\!--product_tabitem_title-->([\s\S]+?)<\!--\/product_tabitem_title-->/', $tab['title'], $tabItemHtml);
            $newTabItemHtml = $key == 0 ? str_replace('u-tab-link', 'u-tab-link active', $newTabItemHtml) : $newTabItemHtml;
            $newTabItemHtml = preg_replace('/(id=[\'"])([\s\S]+?)([\'"])/', '$1tab-' . $tab['guid'] . '$3', $newTabItemHtml);
            $newTabItemHtml = preg_replace('/(href=[\'"])([\s\S]+?)([\'"])/', '$1#link-tab-' . $tab['guid'] . '$3', $newTabItemHtml);
            $newTabItemHtml = preg_replace('/(aria-controls=[\'"])([\s\S]+?)([\'"])/', '$1link-tab-' . $tab['guid'] . '$3', $newTabItemHtml);
            $newTabItemListHtml .= $newTabItemHtml;

            $newTabPaneHtml = $key == 0 ? str_replace('u-tab-pane', 'u-tab-pane u-tab-active', $tabPaneHtml) : $tabPaneHtml;
            $newTabPaneHtml = preg_replace('/(id=[\'"])([\s\S]+?)([\'"])/', '$1link-tab-' . $tab['guid'] . '$3', $newTabPaneHtml);
            $newTabPaneHtml = preg_replace('/(aria-labelledby=[\'"])([\s\S]+?)([\'"])/', '$1tab-' . $tab['guid'] . '$3', $newTabPaneHtml);
            $newTabPaneHtml = preg_replace('/<\!--product_tabpane_content-->([\s\S]+?)<\!--\/product_tabpane_content-->/', $tab['content'], $newTabPaneHtml);
            $newTabPaneListHtml .= $newTabPaneHtml;
        }

        $tabsParts = preg_split($tabItemRe, $tabsHtml, -1, PREG_SPLIT_NO_EMPTY);
        $newTabsHtml = $tabsParts[0] . $newTabItemListHtml . $tabsParts[1];

        $tabsParts = preg_split($tabPaneRe, $newTabsHtml, -1, PREG_SPLIT_NO_EMPTY);
        return $tabsParts[0] . $newTabPaneListHtml . $tabsParts[1];
    }

    /**
     * Set product badge
     *
     * @param array $badgeMatch Badge match
     *
     * @return mixed
     */
    protected function _setProductBadge($badgeMatch) {
        $badgeHtml = $badgeMatch[1];
        if (preg_match('/data-badge-source="sale"/', $badgeHtml)) {
            if ($this->_options['product']['product-sale']) {
                return preg_replace_callback(
                    '/<\!--product_badge_content-->([\s\S]+?)<\!--\/product_badge_content-->/',
                    function () {
                        return $this->_options['product']['product-sale'];
                    },
                    $badgeHtml
                );
            }
        } else {
            if ($this->_options['product']['product-is-new']) {
                return $badgeHtml;
            }
        }
        return str_replace('class="', 'class="u-hidden-block ', $badgeHtml);
    }

    /**
     * Set product outofstock
     *
     * @param array $outOfStockMatch OutOfStock match
     *
     * @return mixed
     */
    protected function _setProductOutOfStock($outOfStockMatch) {
        $outOfStockHtml = $outOfStockMatch[1];
        if ($this->_options['product']['product-out-of-stock']) {
            return $outOfStockHtml;
        }
        return str_replace('class="', 'class="u-hidden-block ', $outOfStockHtml);
    }

    /**
     * Set product sku
     *
     * @param array $skuMatch Sku match
     *
     * @return array|string|string[]|null
     */
    protected function _setProductSku($skuMatch) {
        $skuHtml = $skuMatch[1];
        return preg_replace_callback(
            '/<\!--product_sku_content-->([\s\S]+?)<\!--\/product_sku_content-->/',
            function () {
                return $this->_options['product']['product-sku'];
            },
            $skuHtml
        );
    }

    /**
     * Set product item
     *
     * @param string $html     Control html
     * @param array  $products Product list
     *
     * @return array|mixed|string|string[]|null
     */
    protected function _setProductItem($html, $products) {
        $reProductItem = '/<\!--product_item-->([\s\S]+?)<\!--\/product_item-->/';
        preg_match_all($reProductItem, $html, $matches, PREG_SET_ORDER);
        $allTemplates = count($matches);

        if ($this->_options['isShop'] && count($products) > $allTemplates) {
            $products = array_slice($products, 0, $allTemplates);
        }

        if ($allTemplates > 0) {
            $productsHtml = '';
            $i = 0;
            while (count($products) > 0) {
                $tmplIndex = $i % $allTemplates;
                $productItemHtml = $matches[$tmplIndex][1];
                $productItemHtml = str_replace('u-products-item ', 'u-products-item product ', $productItemHtml);
                $productItemHtml = str_replace('u-product ', 'u-product product ', $productItemHtml);

                $this->_options['product'] = array_shift($products);
                $productItemHtml = preg_replace_callback('/<\!--product_title-->([\s\S]+?)<\!--\/product_title-->/', array(&$this, '_setTitleData'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_content-->([\s\S]+?)<\!--\/product_content-->/', array(&$this, '_setTextData'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_description-->([\s\S]+?)<\!--\/product_description-->/', array(&$this, '_setDescriptionData'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_image-->([\s\S]+?)<\!--\/product_image-->/', array(&$this, '_setImageData'), $productItemHtml);

                $this->_options['quantityExists'] = false;
                $productItemHtml = preg_replace_callback('/<\!--product_quantity-->([\s\S]+?)<\!--\/product_quantity-->/', array(&$this, '_setQuantityData'), $productItemHtml);

                $productItemHtml = preg_replace_callback('/<\!--product_button-->([\s\S]+?)<\!--\/product_button-->/', array(&$this, '_setButtonData'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_price-->([\s\S]+?)<\!--\/product_price-->/', array(&$this, '_setPriceData'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_gallery-->([\s\S]+?)<\!--\/product_gallery-->/', array(&$this, '_setGalleryData'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_variations-->([\s\S]+?)<\!--\/product_variations-->/', array(&$this, '_setVariationsData'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_tabs-->([\s\S]+?)<\!--\/product_tabs-->/', array(&$this, '_setTabsData'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_badge-->([\s\S]+?)<\!--\/product_badge-->/', array(&$this, '_setProductBadge'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_category-->([\s\S]+?)<\!--\/product_category-->/', array(&$this, '_setProductCategory'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_outofstock-->([\s\S]+?)<\!--\/product_outofstock-->/', array(&$this, '_setProductOutOfStock'), $productItemHtml);
                $productItemHtml = preg_replace_callback('/<\!--product_sku-->([\s\S]+?)<\!--\/product_sku-->/', array(&$this, '_setProductSku'), $productItemHtml);
                $productsHtml .= $productItemHtml;
                $i++;
            }
            $html = preg_replace($reProductItem, $productsHtml, $html, 1);
            $html = preg_replace($reProductItem, '', $html);
        }
        return $html;
    }

    /**
     * Get price with/without cents
     *
     * @param string $price
     * @param bool   $addZeroCents
     *
     * @return string $price
     */
    public function addZeroCentsProcess($price, $addZeroCents = false) {
        $separator = strpos($price, ',') > -1 ? ',' : '.';
        $currentPrice = '0';
        $price = str_replace('\\', '', $price);
        if (preg_match('/\d+(' . $separator . '\d+)?/', $price, $matches)) {
            $currentPrice = $matches[0];
            $price = str_replace($matches[0], '[[currentPrice]]', $price);
        }
        $priceParams = explode($separator, $currentPrice);
        $cents = isset($priceParams[1]) ? $priceParams[1] : '00';
        if ($cents === '00') {
            $currentPrice = $priceParams[0];
        }
        if ($addZeroCents) {
            $currentPrice = $priceParams[0] . $separator . $cents;
        }
        return str_replace('[[currentPrice]]', $currentPrice, $price);
    }

    /**
     * Get control parameters
     *
     * @param string $html        Control html
     * @param string $controlName Control name
     *
     * @return array
     */
    public function getControlParameters(&$html, $controlName) {
        $controlOptions = array();
        if (preg_match('/<\!--' . $controlName . '_options_json--><\!--([\s\S]+?)--><\!--\/' . $controlName . '_options_json-->/', $html, $matches)) {
            $controlOptions = json_decode($matches[1], true);
            $html = str_replace($matches[0], '', $html);
        }

        $productsCount = isset($controlOptions['count']) ? (int)$controlOptions['count'] : '';

        $productsSourceType = isset($controlOptions['type']) ? $controlOptions['type'] : '';
        if ($productsSourceType === 'products-featured') {
            $productsSource = 'Featured products';
        } else if ($productsSourceType === 'products-recent') {
            $productsSource = 'Recent products';
        } else {
            $productsSource = isset($controlOptions['source']) && $controlOptions['source'] ? $controlOptions['source'] : '';
        }

        $source = '';
        if (isset($controlOptions['source']) && $controlOptions['source']) {
            $source = $controlOptions['source'];
        }

        $categoryId = Factory::getApplication()->input->get('category_id', '');


        return array(
            'productsCount' => $productsCount,
            'productsSource' => $productsSource,
            'categoryId' => $categoryId,
            'productSource' => $source,
        );
    }

    /**
     * Get pagination parameters
     *
     * @param array $products Product list
     * @param array $options  Options
     *
     * @return array|null
     */
    public function getPaginationParameters(&$products, $options) {
        $paginationProps = null;
        $productsCount = $options['productsCount'];
        if ($productsCount && count($products) > $productsCount) {
            $app = Factory::getApplication();
            $limitstart = $app->input->get('offset', 0);
            $pageId = $app->input->get('pageId', $options['pageId']);
            $positionOnPage = $app->input->get('position', $options['productsPosition']);
            $paginationProps = array(
                'allPosts' => count($products),
                'offset' => (int) $limitstart,
                'postsPerPage' => $productsCount,
                'pageId' => (int) $pageId,
                'positionOnPage' => $positionOnPage,
                'task' => 'productlist',
            );
            $products = array_slice($products, $limitstart, $productsCount);
        }
        return $paginationProps;
    }

    /**
     * Get categories parameters
     * 
     * @param array $options Global options
     *
     * @return array
     */
    public function getCategoriesFilterParameters($options) {
        $app = Factory::getApplication();
        return array(
            'pageId' => $app->input->get('pageId', $options['pageId']),
            'positionOnPage' => $app->input->get('position', $options['productsPosition']),
            'categoryId' => $options['categoryId'],
            'productName' => $options['productName'],
            'isShop' => $options['isShop'],
        );
    }
}
