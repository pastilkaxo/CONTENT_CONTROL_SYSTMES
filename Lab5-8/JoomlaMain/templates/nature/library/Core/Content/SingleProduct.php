<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

Core::load("Core_Content_ProductBase");
class CoreContentSingleProduct extends CoreContentProductBase
{
    protected $_component;
    protected $_componentParams;
    protected $_product;

    public $title;
    public $titleLink;
    public $shortDesc;
    public $desc;
    public $regularPrice = '';
    public $oldPrice = '';

    protected function __construct($component, $componentParams, $product)
    {
        $this->_component = $component;
        $this->_componentParams = $componentParams;
        $this->_product = $product;

        $this->title = $this->_product->product_name;

        $link = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->_product->virtuemart_product_id;
        $this->titleLink = Route::_($link, FALSE);

        $this->shortDesc = $this->_product->product_s_desc;
        $this->desc = $this->_product->product_desc;
        $this->buildPrices();
    }

    public function buildPrices() {
        $currency = CurrencyDisplay::getInstance();
        $regularPrice = $currency->createPriceDiv('salesPrice', '', $this->_product->prices, true, false, 1.0, true, true);
        $oldPrice = $currency->createPriceDiv('basePrice', '', $this->_product->prices, true, false, 1.0, true, true);
        if (!$regularPrice) {
            $regularPrice = $oldPrice;
        }
        $this->regularPrice = array(
            'price' => $this->addZeroCentsProcess($regularPrice),
            'priceWithZeroCents' => $this->addZeroCentsProcess($regularPrice, true),
            'callForPrice' => '',
        );

        $oldPrice = $oldPrice !== $regularPrice ? $oldPrice : '';
        $this->oldPrice = array(
            'price' => $this->addZeroCentsProcess($oldPrice),
            'priceWithZeroCents' => $this->addZeroCentsProcess($oldPrice, true),
            'callForPrice' => '',
        );

        if ($this->_product->prices['salesPrice'] <= 0 && VmConfig::get('askprice', 1) &&
            isset($this->_product->images[0]) && !$this->_product->images[0]->file_is_downloadable
        ) {
            $askquestion_url = Uri::root(true) . ('/index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->_product->virtuemart_product_id . '&virtuemart_category_id=' . $this->_product->virtuemart_category_id . '&tmpl=component');
            ob_start();
            ?>
            <a class="ask-a-question bold" href="<?php echo $askquestion_url ?>" rel="nofollow" ><?php echo vmText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE') ?></a>
            <?php
            $this->regularPrice['callForPrice'] = ob_get_clean();
        }
    }

    public function outOfStock() {
        if ($this->_product->product_in_stock - $this->_product->product_ordered < 1) {
            return true;
        }
        return false;
    }

    public function sku() {
        return $this->_product->product_sku;
    }

    public function getCategories() {
        $categoryModel = VmModel::getModel('category');
        $categoryIds = $this->_product->categories;
        if (!$categoryIds) {
            $categoryIds = array();
        }
        $result = array();
        foreach ($categoryIds as $categoryId) {
            $category = $categoryModel->getCategory($categoryId);
            array_push($result, array(
                'id' => Route::_ ('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->getId()),
                'title' => $category->category_name,
            ));
        }
        if (count($result) < 1) {
            array_push($result, array(
                'id' => '#',
                'title' => 'Uncategorized',
            ));
        }
        return $result;
    }

    public function getImage() {
        $imageUrl = '';
        if (!empty($this->_product->images)) {
            $image = $this->_product->images[0];
            $imageHtml = $image->displayMediaFull("",true,"rel='vm-additional-images'");
            preg_match('/src=[\'"]([\s\S]+?)[\'"]/', $imageHtml, $matches);
            if (count($matches) > 1) {
                $imageUrl = $matches[1];
            }
        }
        return $imageUrl;
    }

    public function getQuantityProps() {
        $props = array('notify' => '', 'label' => '', 'html' => '');
        if ($this->_product->show_notify) {
            $notifyUrl = Route::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $this->_product->virtuemart_product_id);
            $props['notify'] = '<a class="notify u-btn" href="' . $notifyUrl . '" >' .  vmText::_('COM_VIRTUEMART_CART_NOTIFY') . '</a>';
        }

        $tmpPrice = (float) $this->_product->prices['costPrice'];
        $wrongAmountText = vmText::_('COM_VIRTUEMART_WRONG_AMOUNT_ADDED');
        if (!(VmConfig::get('askprice', true) && empty($tmpPrice)) && $this->_product->orderable) {
            $init = 1;
            if (!empty($this->_product->min_order_level) && $init < $this->_product->min_order_level) {
                $init = $this->_product->min_order_level;
            }

            $step = 1;
            if (!empty($this->_product->step_order_level)) {
                $step = $this->_product->step_order_level;
                if (!empty($init)) {
                    if ($init < $step) {
                        $init = $step;
                    } else {
                        $init = ceil($init / $step) * $step;
                    }
                }
                if (empty($this->_product->min_order_level)) {
                    $init = $step;
                }
            }

            $maxOrder = '';
            if (!empty($this->_product->max_order_level)) {
                $maxOrder = ' max="' . $this->_product->max_order_level . '" ';
            }

            $props['html'] = <<<HTML
            <input type="text" class="quantity-input js-recalculate" name="quantity[]" data-errStr="$wrongAmountText"
                value="$init" data-init="$init" data-step="$step" $maxOrder />
HTML;
            $props['label'] = vmText::_('COM_VIRTUEMART_CART_QUANTITY');
        }
        return $props;
    }

    public function getButtonProps($setDynamicQuantity = false) {
        $props = array('text' => '', 'link' => $this->titleLink, 'html' => '');
        $btnPlaceholder = !$this->regularPrice['callForPrice'] ? '[[button]]': '';
        if (!VmConfig::get('use_as_catalog', 0)) {
            $buttonHtml = shopFunctionsF::renderVmSubLayout('addtocart', array('product'=> $this->_product));
            if (strpos($buttonHtml, 'addtocart-button-disabled') !== false) {
                $props['text'] = vmText::_('COM_VIRTUEMART_ADDTOCART_CHOOSE_VARIANT');
            } else {
                $props['text'] = vmText::_('COM_VIRTUEMART_CART_ADD_TO');
                $productId = $this->_product->virtuemart_product_id;
                $productName = $this->_product->product_name;
                $formAction = Route::_('index.php?option=com_virtuemart', false);
                $quantityHtml = '<input type="hidden" class="quantity-input js-recalculate" name="quantity[]" value="1">';
                if ($setDynamicQuantity) {
                    $quantityHtml = '[[dynamic_quantity]]';
                }
                $props['html'] = <<<HTML
<form method="post" class="form-product js-recalculate" action="$formAction" autocomplete="off" >
			$btnPlaceholder
			<input type="hidden" name="option" value="com_virtuemart"/>
			<input type="hidden" name="view" value="cart"/>
			<input type="hidden" name="virtuemart_product_id[]" value="$productId"/>
			<input type="hidden" name="pname" value="$productName"/>
			<input type="hidden" name="pid" value="$productId"/>
			$quantityHtml
            <noscript><input type="hidden" name="task" value="add"/></noscript>
HTML;
                $itemId = vRequest::getInt('Itemid', false);
                if ($itemId) {
                    $props['html'] .= '<input type="hidden" name="Itemid" value="'.$itemId.'"/>';
                }

                $props['html'] .= '</form>';
            }
        }
        return $props;
    }

    public function isNew() {
        $currentDate = (int) (microtime(true) * 1000);
        if (property_exists($this->_product, 'created_on')) {
            $createdDate = Factory::getDate($this->_product->created_on)->format('U') * 1000;
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
        $symbol = CurrencyDisplay::getInstance()->getSymbol();
        $price = $this->regularPrice['price'] ?: 0;
        if ($price) {
            $price = str_replace(',', '.', $price);
            $price = str_replace($symbol, '', $price);
            $price = (float) trim($price);
        }
        $oldPrice = $this->oldPrice['price'] ?: 0;
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

    public function getGallery() {
        $galleryImages = array();

        $productModel = VmModel::getModel('Product');
        $productModel->addImages($this->_product);

        if (empty($this->_product->images)) {
            return $galleryImages;
        }

        if (count($this->_product->images) === 1) {
            return array($this->getImage());
        }

        for ($i = 0; $i < count($this->_product->images); $i++) {
            $image = $this->_product->images[$i];
            $imageHtml = $image->displayMediaFull('', true, "rel='vm-additional-images'");
            preg_match('/src=[\'"]([\s\S]+?)[\'"]/', $imageHtml, $matches);
            if (count($matches) > 1) {
                array_push($galleryImages, $matches[1]);
            }
        }
        return $galleryImages;
    }

    public function getTabs() {
        $tabs = array();

        $descTabTitle = vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE');
        $descTabContent = $this->_product->product_desc;
        $descTabContent .= shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->_product,'position'=>'normal'));
        $descTab = array('title' => $descTabTitle, 'content' => $descTabContent, 'guid' => strtolower(substr(createGuid(), 0, 4)));
        array_push($tabs, $descTab);

        $revTabContent = $this->_component->loadTemplate('reviews');
        if ($revTabContent) {
            $revTabTitle = vmText::_('COM_VIRTUEMART_REVIEWS');
            $revTab = array('title' => $revTabTitle, 'content' => $revTabContent, 'guid' => strtolower(substr(createGuid(), 0, 4)));
            array_push($tabs, $revTab);
        }

        return $tabs;
    }

    public function getVariations($position = 'addtocart') {
        $variations = array();
        if (!empty($this->_product->customfieldsSorted[$position])) {
            $customfields = $this->_product->customfieldsSorted[$position];
            foreach ($customfields as $customfield) {
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
        }
        return $variations;
    }

    public function includeScripts($templateType = '') {
        vmJsApi::jPrice();
        vmJsApi::cssSite();
        vmJsApi::jDynUpdate();

        if($templateType === 'productdetails' && VmConfig::get('jdynupdate', true)) {
            $j = <<<SCRIPT
Virtuemart.container = jQuery('.productdetails-view');
Virtuemart.containerSelector = '.productdetails-view';
SCRIPT;
            vmJsApi::addJScript('ajaxContent', $j);

            $j = <<<SCRIPT
jQuery(document).ready(function($) {
	Virtuemart.stopVmLoading();
	var msg = '';
	$('a[data-dynamic-update=\"1\"]').off('click', Virtuemart.startVmLoading).on('click', {msg:msg}, Virtuemart.startVmLoading);
	$('[data-dynamic-update=\"1\"]').off('change', Virtuemart.startVmLoading).on('change', {msg:msg}, Virtuemart.startVmLoading);
});
SCRIPT;
            vmJsApi::addJScript('vmPreloader', $j);
        }
        if($templateType === 'products') {
            $j = <<<SCRIPT
jQuery('select').on('change', function() {
    window.location.href = this.value;
});
SCRIPT;
            vmJsApi::addJScript('vmOrderByList', $j);
        }

        echo vmJsApi::writeJS();
    }

    public function addZeroCentsProcess($price, $addZeroCents = false) {
        if (!$price) {
            return '';
        }
        $separator = strpos($price, ',') > -1 ? ',' : '.';
        $currentPrice = '0';
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
}
