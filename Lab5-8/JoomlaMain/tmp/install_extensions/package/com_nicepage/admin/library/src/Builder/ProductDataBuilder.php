<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Builder;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use \vmText, \shopFunctionsF, \VmConfig, \vmJsApi, \CurrencyDisplay, \vRequest, \VmModel;
/**
 * Class ProductDataBuilder
 */
class ProductDataBuilder extends DataBuilder
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
        $this->_item = $item;
        $base = array(
            'product-title' => $this->title(),
            'product-title-link' => $this->titleLink(),
            'product-desc' => $this->content(),
            'product-full-desc' => $this->desc(),
            'product-image' => $this->image(),
            'product-gallery' => $this->gallery(),
            'product-variations' => $this->variations(),
            'product-tabs' => $this->tabs(),
            'product-is-new' => $this->isNew(),
            'product-sale' => $this->sale(),
            'product-categories' => $this->categories(),
            'product-out-of-stock' => $this->outOfStock(),
            'product-sku' => $this->sku(),
        );
        $this->_data = array_merge($base, $this->quantity(), $this->button(), $this->price());
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
        return $this->_item->product_name;
    }

    /**
     * Get product content
     *
     * @return false|string|string[]|null
     */
    public function content()
    {
        $desc = $this->_item->product_s_desc ? $this->_item->product_s_desc : $this->_item->product_desc;
        return $this->excerpt($desc, 150, '...', true);
    }

    /**
     * Get product desc
     *
     * @return false|string|string[]|null
     */
    public function desc()
    {
        return $this->_item->product_desc;
    }

    /**
     * Get product title link
     *
     * @return string
     */
    public function titleLink()
    {
        $productId = $this->_item->virtuemart_product_id;
        $categoryId = $this->_item->virtuemart_category_id;
        $baseUrl = 'index.php?option=com_virtuemart&view=productdetails';
        return Route::_($baseUrl . '&virtuemart_product_id=' . $productId);
    }

    /**
     * Get product image
     *
     * @return string
     */
    public function image()
    {
        $imageSource = '';
        if (!empty($this->_item->images)) {
            $imageSource = Uri::root(true) . '/' . $this->_item->images[0]->file_url;
        }
        return $imageSource;
    }

    /**
     * Get product gallery
     *
     * @return string
     */
    public function gallery()
    {
        $images = array();
        if (!empty($this->_item->images)) {
            foreach ($this->_item->images as $image) {
                array_push($images, Uri::root(true) . '/' . $image->file_url);
            }
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
        $quantity = array('product-quantity-notify' => '', 'product-quantity-label' => '', 'product-quantity-html' => '');

        if ($this->_item->show_notify) {
            $notifyUrl = Route::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $this->_item->virtuemart_product_id);
            $quantity['product-quantity-notify'] = '<a class="notify u-btn" href="' . $notifyUrl . '" >' .  vmText::_('COM_VIRTUEMART_CART_NOTIFY') . '</a>';
        }

        $tmpPrice = (float) $this->_item->prices['costPrice'];
        $wrongAmountText = vmText::_('COM_VIRTUEMART_WRONG_AMOUNT_ADDED');
        if (!(VmConfig::get('askprice', true) && empty($tmpPrice)) && $this->_item->orderable) {
            $init = 1;
            if (!empty($this->_item->min_order_level) && $init < $this->_item->min_order_level) {
                $init = $this->_item->min_order_level;
            }

            $step = 1;
            if (!empty($this->_item->step_order_level)) {
                $step = $this->_item->step_order_level;
                if (!empty($init)) {
                    if ($init < $step) {
                        $init = $step;
                    } else {
                        $init = ceil($init / $step) * $step;
                    }
                }
                if (empty($this->_item->min_order_level)) {
                    $init = $step;
                }
            }

            $maxOrder = '';
            if (!empty($this->_item->max_order_level)) {
                $maxOrder = ' max="' . $this->_item->max_order_level . '" ';
            }

            $quantity['product-quantity-html'] = <<<HTML
            <input type="text" class="quantity-input js-recalculate" name="quantity[]" data-errStr="$wrongAmountText"
                value="$init" data-init="$init" data-step="$step" $maxOrder />
HTML;
            $quantity['product-quantity-label'] = vmText::_('COM_VIRTUEMART_CART_QUANTITY');
        }
        return $quantity;
    }

    /**
     * Get product button
     *
     * @return array
     */
    public function button()
    {
        $button = array('product-button-text' => '', 'product-button-link' => $this->titleLink(), 'product-button-html' => '');

        if (VmConfig::get('use_as_catalog', 0)) {
            return $button;
        }

        $buttonHtml = shopFunctionsF::renderVmSubLayout('addtocart', array('product'=> $this->_item));
        if (strpos($buttonHtml, 'addtocart-button-disabled') !== false) {
            $button['product-button-text'] = vmText::_('COM_VIRTUEMART_ADDTOCART_CHOOSE_VARIANT');
        } else {
            $button['product-button-text'] = vmText::_('COM_VIRTUEMART_CART_ADD_TO');
            $productId = $this->_item->virtuemart_product_id;
            $productName = $this->_item->product_name;
            $formAction = Route::_('index.php?option=com_virtuemart', false);
            $button['product-button-html'] = <<<HTML
            <form method="post" class="form-product js-recalculate" action="$formAction" autocomplete="off" >
			[[button]]
			<input type="hidden" name="option" value="com_virtuemart"/>
			<input type="hidden" name="view" value="cart"/>
			<input type="hidden" name="virtuemart_product_id[]" value="$productId"/>
			<input type="hidden" name="pname" value="$productName"/>
			<input type="hidden" name="pid" value="$productId"/>
			[[quantity]]
            <noscript><input type="hidden" name="task" value="add"/></noscript>
HTML;
            $itemId = vRequest::getInt('Itemid', false);
            if ($itemId) {
                $button['product-button-html'] .= '<input type="hidden" name="Itemid" value="'.$itemId.'"/>';
            }

            $button['product-button-html'] .= '</form>';
        }
        return $button;
    }

    /**
     * Get product price
     *
     * @return array
     */
    public function price()
    {
        $currency = CurrencyDisplay::getInstance();

        $regularPrice = $currency->createPriceDiv('salesPrice', '', $this->_item->prices, true, false, 1.0, true);
        $oldPrice = $currency->createPriceDiv('basePrice', '', $this->_item->prices, true, false, 1.0, true);

        if (!$regularPrice) {
            $regularPrice = $oldPrice;
        }

        $prices = array('product-price' => '', 'product-old-price' => '');
        if ($regularPrice) {
            $prices['product-price'] = $regularPrice;
        }
        if ($oldPrice) {
            $prices['product-old-price'] = $oldPrice;
        }

        return $prices;
    }

    /**
     * Product is new
     */
    public function isNew() {
        $currentDate = (int) (microtime(true) * 1000);
        if (property_exists($this->_item, 'created_on')) {
            $createdDate = Factory::getDate($this->_item->created_on)->format('U') * 1000;
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
        $symbol = CurrencyDisplay::getInstance()->getSymbol();
        $prices = $this->price();
        $price = $prices['product-price'] ?: 0;
        if ($price) {
            $price = str_replace(',', '.', $price);
            $price = str_replace($symbol, '', $price);
            $price = (float) trim($price);
        }
        $oldPrice = $prices['product-old-price'] ?: 0;
        if ($oldPrice) {
            $oldPrice = str_replace(',', '.', $oldPrice);
            $oldPrice = str_replace($symbol, '', $oldPrice);
            $oldPrice = (float) trim($oldPrice);
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
        $categoryModel = VmModel::getModel('category');
        $categoryIds = $this->_item->categories;
        if (!$categoryIds) {
            $categoryIds = array();
        }
        $result = array();
        foreach ($categoryIds as $categoryId) {
            $category = $categoryModel->getCategory($categoryId);
            $r = new Registry(
                array(
                    'id' => Route::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->getId()),
                    'title' => $category->category_name,
                )
            );
            array_push($result, $r->toObject());
        }
        if (count($result) < 1) {
            $r = new Registry(
                array(
                    'id' => '#',
                    'title' => 'Uncategorized',
                )
            );
            array_push($result, $r->toObject());
        }
        return $result;
    }

    /**
     * OutOfStock product
     */
    public function outOfStock() {
        if ($this->_item->product_in_stock - $this->_item->product_ordered < 1) {
            return true;
        }
        return false;
    }

    /**
     * Product sku
     *
     * @return mixed
     */
    public function sku() {
        return $this->_item->product_sku;
    }

    /**
     * Get product variations
     *
     * @return array
     */
    public function variations()
    {
        $variations = array();

        if (empty($this->_item->customfields)) {
            return $variations;
        }

        foreach ($this->_item->customfields as $customfield) {
            if ($customfield->layout_pos !== 'addtocart') {
                continue;
            }
            if (property_exists($customfield, 'display') && strpos($customfield->display, '<select ') !== false) {
                preg_match_all('/<select([\s\S]+?)>([\s\S]+?)<\/select>/', $customfield->display, $selectMatches, PREG_SET_ORDER);
                foreach ($selectMatches as $index => $selectMatch) {
                    $selectHtml = $selectMatch[1];

                    $s_classes = '';
                    preg_match('/class="([\s\S]+?)"/', $selectHtml, $classMatch);
                    if (count($classMatch) > 0) {
                        $selectHtml = preg_replace('/class="[\s\S]+?"/', '', $selectHtml);
                        $s_classes = str_replace('vm-chzn-select', '', $classMatch[1]);
                        $s_classes = str_replace('no-vm-bind', '', $s_classes);
                    }

                    $attributesMatch = explode(' ', $selectHtml);
                    $attributes = array();
                    foreach ($attributesMatch as $attr) {
                        if (trim($attr) && !preg_match('/^(id|class|style)/', $attr) && strpos($attr, '=') !== false) {
                            array_push($attributes, $attr);
                        }
                    }

                    preg_match_all('/<option[\s\S]+?value=[\'"]([\s\S]*?)[\'"][\s\S]*?>([\s\S]+?)<\/option>/', $selectMatch[2], $matches);
                    $optionTags = $matches[0];
                    $values = $matches[1];
                    $text = $matches[2];
                    $options = array();
                    foreach ($values as $key => $value) {
                        $option = array(
                            'text' => $text[$key],
                            'value' => $value,
                        );
                        $option['selected'] = strpos($optionTags[$key], 'selected') !== false ? true : false;
                        array_push($options, $option);
                    }

                    $variation = array(
                        'title' => $index == 0 ? $customfield->custom_title : '',
                        'options' => $options,
                        's_attributes' => implode(' ', $attributes),
                        's_classes' => $s_classes,
                    );
                    array_push($variations, $variation);
                }

            }
        }

        return $variations;
    }

    /**
     * Get product tabs
     *
     * @return array
     */
    public function tabs() {
        $tabs = array();

        if (!isset($this->_item->allowReview)) {
            return $tabs;
        }

        $descTabTitle = vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE');
        $descTabContent = $this->_item->product_desc;
        $descTabContent .= shopFunctionsF::renderVmSubLayout('customfields', array('product' => $this->_item, 'position' => 'normal'));
        $descTab = array('title' => $descTabTitle, 'content' => $descTabContent, 'guid' => strtolower(substr($this->_createGuid(), 0, 4)));
        array_push($tabs, $descTab);

        $revTabContent = $this->_getReviewTab();
        if ($revTabContent) {
            $revTabTitle = vmText::_('COM_VIRTUEMART_REVIEWS');
            $revTab = array('title' => $revTabTitle, 'content' => $revTabContent, 'guid' => strtolower(substr($this->_createGuid(), 0, 4)));
            array_push($tabs, $revTab);
        }
        return $tabs;
    }

    /**
     * Get reviews tab content
     *
     * @return false|string
     */
    private function _getReviewTab() {
        ob_start();
        // Customer Reviews
        $review_editable = true;
        if ($this->_item->allowRating || $this->_item->allowReview || $this->_item->showRating || $this->_item->showReview) {
            $maxrating = VmConfig::get('vm_maximum_rating_scale', 5);
            $ratingsShow = VmConfig::get('vm_num_ratings_show', 3); // TODO add  vm_num_ratings_show in vmConfig
            $stars = array();
            $ratingWidth = $maxrating * 24;
            for ($num = 0; $num <= $maxrating; $num++) {
                $stars[] = '
                    <span title="' . (vmText::_("COM_VIRTUEMART_RATING_TITLE") . $num . '/' . $maxrating) . '" class="vmicon ratingbox" style="display:inline-block;width:' . 24 * $maxrating . 'px;">
                        <span class="stars-orange" style="width:' . (24 * $num) . 'px">
                        </span>
                    </span>';
            }

            echo '<div class="customer-reviews">';

            if ($this->_item->rating_reviews) {
                foreach ($this->_item->rating_reviews as $review) {
                    /* Check if user already commented */
                    // if ($review->virtuemart_userid == $this->user->id ) {
                    if ($review->created_by == $this->_item->user->id && !$review->review_editable) {
                        $review_editable = false;
                    }
                }
            }
        }
        ?>
        <?php if ($this->_item->allowRating or $this->_item->allowReview) : ?>
            <?php if ($review_editable) : ?>
                <form method="post"
                      action="<?php echo Route::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->_item->virtuemart_product_id . '&virtuemart_category_id=' . $this->_item->virtuemart_category_id, false); ?>"
                      name="reviewForm" id="reviewform">
                    <?php if ($this->_item->allowRating and $review_editable) : ?>
                        <h4><?php echo vmText::_('COM_VIRTUEMART_WRITE_REVIEW');
                        if (count($this->_item->rating_reviews) == 0) {
                            ?><span><?php echo vmText::_('COM_VIRTUEMART_WRITE_FIRST_REVIEW'); ?></span><?php
                        } ?>
                        </h4>
                        <span class="step"><?php echo vmText::_('COM_VIRTUEMART_RATING_FIRST_RATE'); ?></span>
                        <div class="rating">
                            <label for="vote"><?php echo $stars[$maxrating]; ?></label>
                            <input type="hidden" id="vote" value="<?php echo $maxrating; ?>" name="vote">
                        </div>
                        <?php
                        $reviewJavascript = "
                            jQuery(function($) {
                                var ratingBox = $('.rating .ratingbox');
                                var tabPane = ratingBox.parents('.u-tab-pane');
                                var steps = " . $maxrating . ";
                                var parentPos= ratingBox.position();
                                var boxWidth = ratingBox.width();// nbr of total pixels
                                var starSize = (boxWidth/steps);
                                var changedTabPaneStyles = false;
                                if (tabPane.css('display') === 'none') {
                                    tabPane.css({'display' : 'block','opacity' : '0'});
                                    changedTabPaneStyles = true;
                                }
                                var ratingboxPos= ratingBox.offset();
                                if (changedTabPaneStyles) {
                                    tabPane.css({'display' : '','opacity' : ''});
                                }
                                jQuery('.rating .ratingbox').mousemove(function(e) {
                                    var span = jQuery(this).children();
                                    var dif = e.pageX-ratingboxPos.left; // nbr of pixels
                                    difRatio = Math.floor(dif/boxWidth* steps )+1; //step
                                    span.width(difRatio*starSize);
                                    $('#vote').val(difRatio);
                                    //console.log('note = ',parentPos, boxWidth, ratingboxPos);
                                });
                            });
                        ";
                        vmJsApi::addJScript('rating_stars', $reviewJavascript);
                        ?>
                    <?php endif; ?>
                    <?php if ($this->_item->allowReview and $review_editable) : ?>
                        <div class="write-reviews">
                            <?php // Show Review Length While Your Are Writing
                            $reviewJavascript = "
                                function check_reviewform() {
                                
                                var form = document.getElementById('reviewform');
                                var ausgewaehlt = false;
                                
                                    if (form.comment.value.length < " . VmConfig::get('reviews_minimum_comment_length', 100) . ") {
                                        alert('" . addslashes(vmText::sprintf('COM_VIRTUEMART_REVIEW_ERR_COMMENT1_JS', VmConfig::get('reviews_minimum_comment_length', 100))) . "');
                                        return false;
                                    }
                                    else if (form.comment.value.length > ".VmConfig::get('reviews_maximum_comment_length', 2000) . ") {
                                        alert('" . addslashes(vmText::sprintf('COM_VIRTUEMART_REVIEW_ERR_COMMENT2_JS', VmConfig::get('reviews_maximum_comment_length', 2000))) . "');
                                        return false;
                                    }
                                    else {
                                        return true;
                                    }
                                }
                                
                                function refresh_counter() {
                                    var form = document.getElementById('reviewform');
                                    form.counter.value= form.comment.value.length;
                                }
                            ";
                            vmJsApi::addJScript('check_reviewform', $reviewJavascript); ?>
                            <span class="step">
                                <?php echo vmText::sprintf('COM_VIRTUEMART_REVIEW_COMMENT', VmConfig::get('reviews_minimum_comment_length', 100), VmConfig::get('reviews_maximum_comment_length', 2000)); ?>
                            </span>
                            <br/>
                            <textarea style="width:100%" class="virtuemart" title="<?php echo vmText::_('COM_VIRTUEMART_WRITE_REVIEW'); ?>"
                                      class="inputbox" id="comment" onblur="refresh_counter();" onfocus="refresh_counter();"
                                      onkeyup="refresh_counter();" name="comment" rows="5"
                                      cols="60">
                                <?php if (!empty($this->_item->review->comment)) : ?>
                                    <?php echo $this->_item->review->comment; ?>
                                <?php endif; ?>
                            </textarea>
                            <br/>
                            <span>
                                <?php echo vmText::_('COM_VIRTUEMART_REVIEW_COUNT'); ?>
                                <input type="text" value="0" size="4" name="counter" maxlength="4" readonly="readonly"/>
                            </span>
                            <br/><br/>
                            <input class="u-btn" style="display: inline-block;" type="submit" onclick="return(check_reviewform());"
                                   name="submit_review" title="<?php echo vmText::_('COM_VIRTUEMART_REVIEW_SUBMIT') ?>"
                                   value="<?php echo vmText::_('COM_VIRTUEMART_REVIEW_SUBMIT'); ?>"/>
                        </div>
                    <?php elseif ($review_editable and $this->_item->allowRating) : ?>
                        <input class="u-btn" style="display: inline-block;" type="submit" name="submit_review"
                               title="<?php echo vmText::_('COM_VIRTUEMART_REVIEW_SUBMIT'); ?>"
                               value="<?php echo vmText::_('COM_VIRTUEMART_REVIEW_SUBMIT'); ?>"/>
                    <?php endif; ?>
                    <input type="hidden" name="virtuemart_product_id"
                           value="<?php echo $this->_item->virtuemart_product_id; ?>"/>
                    <input type="hidden" name="option" value="com_virtuemart"/>
                    <input type="hidden" name="virtuemart_category_id"
                           value="<?php echo vRequest::getInt('virtuemart_category_id'); ?>"/>
                    <input type="hidden" name="virtuemart_rating_review_id" value="0"/>
                    <input type="hidden" name="task" value="review"/>
                </form>
            <?php elseif (!$review_editable) : ?>
                <?php
                echo '<strong>' . vmText::_('COM_VIRTUEMART_DEAR') . $this->_item->user->name . ',</strong><br>';
                echo vmText::_('COM_VIRTUEMART_REVIEW_ALREADYDONE');
                ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($this->_item->showReview) : ?>
            <h4><?php echo vmText::_('COM_VIRTUEMART_REVIEWS') ?></h4>
            <div class="list-reviews">
                <?php
                $i = 0;
                //$review_editable = TRUE;
                $reviews_published = 0;
                if ($this->_item->rating_reviews) {
                    foreach ($this->_item->rating_reviews as $review) {
                        if ($i % 2 == 0) {
                            $color = 'normal';
                        } else {
                            $color = 'highlight';
                        }
                        ?>
                        <?php // Loop through all reviews
                        if (!empty($this->_item->rating_reviews) && $review->published) {
                            $reviews_published++;
                            ?>
                            <div class="<?php echo $color ?>">
                                <span class="date"><?php echo HTMLHelper::date($review->created_on, vmText::_('DATE_FORMAT_LC')); ?></span>
                                <span class="vote"><?php echo $stars[(int)$review->review_rating] ?></span>
                                <blockquote><?php echo $review->comment; ?></blockquote>
                                <span class="bold"><?php echo $review->customer ?></span>
                            </div>
                            <?php
                        }
                        $i++;
                        if ($i == $ratingsShow && !$this->_item->showall) {
                            /* Show all reviews ? */
                            if ($reviews_published >= $ratingsShow) {
                                $attribute = array('class'=> 'details', 'title'=> vmText::_('COM_VIRTUEMART_MORE_REVIEWS'));
                                echo HTMLHelper::link($this->_item->more_reviews, vmText::_('COM_VIRTUEMART_MORE_REVIEWS'), $attribute);
                            }
                            break;
                        }
                    }
                } else {
                    // "There are no reviews for this product"
                    ?>
                    <span class="step"><?php echo vmText::_('COM_VIRTUEMART_NO_REVIEWS') ?></span>
                    <?php
                }  ?>
                <div class="clear"></div>
            </div>
        <?php endif; ?>
        <?php if ($this->_item->allowRating || $this->_item->allowReview || $this->_item->showRating || $this->_item->showReview) : ?>
            </div>
        <?php endif; ?>
        <?php
        $result = ob_get_clean();
        return preg_replace('/\s+/', '', $result) !== '<divclass="customer-reviews"></div>' ? $result : '';
    }

    /**
     * Create guid key
     *
     * @return string
     */
    private function _createGuid()
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

}
