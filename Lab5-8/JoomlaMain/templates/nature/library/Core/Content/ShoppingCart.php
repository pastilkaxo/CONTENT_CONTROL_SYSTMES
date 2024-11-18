
<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

class CoreContentShoppingCart
{
    private $_component;
    private $_componentParams;
    private $_cart;

    private $_cartBlocks = array();

    public function __construct($component, $componentParams, $cart)
    {
        $this->_component = $component;
        $this->_componentParams = $componentParams;
        $this->_cart = $cart;
    }

    public function continueShopping()
    {
        return array('link' => $this->_component->continue_link, 'content' => vmText::_ ('COM_VIRTUEMART_CONTINUE_SHOPPING'));
    }

    public function checkOut()
    {
        return str_replace('<button', '<button style="display:none"', $this->_component->checkout_link_html);
    }

    public function getCartBlocks($controlOptions)
    {
        $uri = vmUri::getCurrentUrlBy('get');
        $uri = str_replace(array('?tmpl=component','&tmpl=component'), '', $uri);
        $loginBlockContent = shopFunctionsF::getLoginForm($this->_cart, FALSE, $uri);
        if ($loginBlockContent) {
            $loginBlockContent = str_replace('class="inputbox', 'class="' . $controlOptions['inputClass'] . '"', $loginBlockContent);
            $loginBlockContent = str_replace('class="form-login-button', 'class="form-login-button ' . $controlOptions['buttonClass'] . '"', $loginBlockContent);
            $loginBlockContent = str_replace('name="', 'data-name="', $loginBlockContent);
            $loginBlockContent = str_replace('action="', 'data-action="', $loginBlockContent);
            $loginBlockContent = str_replace('method="', 'data-method="', $loginBlockContent);
            $loginBlockContent = str_replace('<form', '<div', $loginBlockContent);
            $loginBlockContent = str_replace('</form>', '</div>', $loginBlockContent);
            $this->_cartBlocks['login'] = array(
                'header' => 'Login Form',
                'content' => $loginBlockContent
            );
        }

        if ($this->_component->allowChangeShopper and !$this->_component->isPdf) {
            $shopperBlockContent = $this->_component->loadTemplate('shopperform');
            $shopperBlockContent = str_replace('class="button', 'class="' . $controlOptions['buttonClass'] . '"', $shopperBlockContent);
            $shopperBlockContent = str_replace('class="shopperform-button', 'class="shopperform-button ' . $controlOptions['buttonClass'] . '"', $shopperBlockContent);
            $shopperBlockContent = str_replace('name="', 'data-name="', $shopperBlockContent);
            $shopperBlockContent = str_replace('action="', 'data-action="', $shopperBlockContent);
            $shopperBlockContent = str_replace('method="', 'data-method="', $shopperBlockContent);
            $shopperBlockContent = str_replace('<form', '<div', $shopperBlockContent);
            $shopperBlockContent = str_replace('</form>', '</div>', $shopperBlockContent);
            $this->_cartBlocks['changeshopper'] = array(
                'header' => vmText::_('COM_VIRTUEMART_CART_CHANGE_SHOPPER'),
                'content' => $shopperBlockContent
            );
        }

        if (VmConfig::get('coupons_enable')) {
            if (!empty($this->_component->layoutName) && $this->_component->layoutName == $this->_cart->layout) {
                $couponBlockContent = $this->_component->loadTemplate('coupon');
            }
            if (!empty($this->_cart->cartData['couponCode'])) {
                $couponBlockContent .= '<br />' . $this->_cart->cartData['couponCode'];
                $couponBlockContent .= '<br />' . ($this->_cart->cartData['couponDescr'] ? (' (' . $this->_cart->cartData['couponDescr'] . ')') : '');
                $couponBlockContent .= $this->_component->currencyDisplay->createPriceDiv('salesPriceCoupon', '', $this->_cart->cartPrices['salesPriceCoupon'], false);
            }
            if ($couponBlockContent) {
                $couponBlockContent = str_replace('class="coupon"', 'class="' . $controlOptions['inputClass'] . '"', $couponBlockContent);
                $couponBlockContent = str_replace('class="details-button"', 'class="' . $controlOptions['buttonClass'] . '"', $couponBlockContent);
                $this->_cartBlocks['coupon'] = array('header' => 'Coupon', 'content' => $couponBlockContent);
            }
        }

        $billToContent = $this->_component->loadTemplate('billto');
        $billToContent = str_replace('class="details', 'class="' . $controlOptions['buttonClass'] . '"', $billToContent);
        $this->_cartBlocks['billto'] = array('header' => vmText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'),
            'content' => $billToContent);

        $shipToContent = $this->_component->loadTemplate('shipto');
        $shipToContent = str_replace('class="details', 'class="' . $controlOptions['buttonClass'] . '"', $shipToContent);
        $this->_cartBlocks['shipto'] = array('header' => vmText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'),
            'content' => $shipToContent);



        if (VmConfig::get('oncheckout_opc', true) || !VmConfig::get('oncheckout_show_steps', false) ||
            (!VmConfig::get('oncheckout_opc', true) && VmConfig::get('oncheckout_show_steps', false) &&
                !empty($this->_cart->virtuemart_shipmentmethod_id))) {
            $shipmentHeader = vmText::_ ('COM_VIRTUEMART_CART_SELECTED_SHIPMENT');
            $shipmentContent = '';
            if (!$this->_cart->automaticSelectedShipment) {
                $shipmentContent = $this->_cart->cartData['shipmentName'] . '<br/>';
                if (!empty($this->_component->layoutName) && $this->_component->layoutName == $this->_cart->layout) {
                    if (VmConfig::get('oncheckout_opc', 0)) {
                        $previouslayout = $this->_component->setLayout('select');
                        $shipmentContent .= $this->_component->loadTemplate('shipment');
                        $this->_component->setLayout($previouslayout);
                    } else {
                        $shipmentContent .= HTMLHelper::_('link', Route::_('index.php?option=com_virtuemart&view=cart&task=edit_shipment', $this->_component->useXHTML, $this->_component->useSSL), $this->_component->select_shipment_text, 'class=""');
                    }
                } else {
                    $shipmentContent .= vmText::_ ('COM_VIRTUEMART_CART_SHIPPING');
                }
            } else {
                $shipmentContent .= $this->_cart->cartData['shipmentName'] . '<br />';
                $shipmentContent .=  $this->_component->currencyDisplay->createPriceDiv('shipmentValue', '', $this->_cart->cartPrices['shipmentValue'], true);
            }

            if ($this->_cart->cartPrices['salesPriceShipment'] < 0) {
                $shipmentContent .= $this->_component->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->_cart->cartPrices['salesPriceShipment'], false) . '<br />';
            }
            $shipmentContent .= $this->_component->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->_cart->cartPrices['salesPriceShipment'], false) . '<br />';
            $this->_cartBlocks['shipment'] = array('header' => $shipmentHeader, 'content' => $shipmentContent);
        }

        if ($this->_cart->pricesUnformatted['salesPrice']>0.0 && (VmConfig::get('oncheckout_opc', true) || !VmConfig::get('oncheckout_show_steps', false) ||
                ((!VmConfig::get('oncheckout_opc', true) and VmConfig::get('oncheckout_show_steps', false) ) and !empty($this->_cart->virtuemart_paymentmethod_id)))) {
            $paymentHeader = vmText::_('COM_VIRTUEMART_CART_SELECTED_PAYMENT');
            $paymentContent = '';
            if (!$this->_cart->automaticSelectedPayment) {
                $paymentContent = $this->_cart->cartData['paymentName'] . '<br/>';
                if (!empty($this->_component->layoutName) && $this->_component->layoutName == $this->_cart->layout) {
                    if (VmConfig::get('oncheckout_opc', 0)) {
                        $previouslayout = $this->_component->setLayout('select');
                        $paymentContent .= $this->_component->loadTemplate('payment');
                        $this->_component->setLayout($previouslayout);
                    } else {
                        $paymentContent .= HTMLHelper::_('link', Route::_('index.php?option=com_virtuemart&view=cart&task=editpayment', $this->_component->useXHTML, $this->_component->useSSL), $this->_component->select_payment_text, 'class=""');
                    }
                } else {
                    $paymentContent .= vmText::_('COM_VIRTUEMART_CART_PAYMENT');
                }
            } else {
                $paymentContent .= $this->_cart->cartData['paymentName'];
            }
            if ($this->_cart->cartPrices['salesPricePayment'] < 0) {
                $paymentContent .= $this->_component->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->_cart->cartPrices['salesPricePayment'], false);
            }
            $paymentContent .= $this->_component->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->_cart->cartPrices['salesPricePayment'], false);
            $this->_cartBlocks['payment'] = array('header' => $paymentHeader, 'content' => $paymentContent);
        }

        $this->_cartBlocks['cartfields'] = array('header' => '', 'content' => $this->_component->loadTemplate ('cartfields'));
        $cartTotalsBlock = array('cartTotals' => array('header' => '', 'content' => $this->getCartTotals()));
        return array_merge(array_slice($this->_cartBlocks, 0, 1), $cartTotalsBlock, array_slice($this->_cartBlocks, 1));
    }

    public function getCartTotals()
    {
        $subTotal = $this->_component->currencyDisplay->createPriceDiv('salesPrice', '', $this->_cart->cartPrices, true);
        $total = $this->_component->currencyDisplay->createPriceDiv('billTotal', '', $this->_cart->cartPrices['billTotal'], true);
        return array(
            'header' => vmText::_('COM_VIRTUEMART_CART_TOTAL'),
            'subtotalText' => vmText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'),
            'subtotal' => $subTotal,
            'totalText' => vmText::_('COM_VIRTUEMART_CART_TOTAL'),
            'total' => $total,
            'checkoutBtn' => $this->checkOut(),
            'checkoutBtnText' => $this->_component->checkout_task === 'checkout' ? vmText::_('COM_VIRTUEMART_CHECKOUT_TITLE') : vmText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'),
        );
    }

    public function beginCheckoutForm() {
        $action = Route::_ ('index.php?option=com_virtuemart&view=cart' . '', $this->_component->useXHTML, $this->_component->useSSL);
        echo <<<STARTFORM
        <div id="cart-view" class="cart-view">
            <form method="post" id="checkoutForm" name="checkoutForm" action="$action">
STARTFORM;
    }

    public function endCheckoutForm() {
        $orderLanguage = $this->_component->order_language;
        echo <<<ENDFORM
        <input type='hidden' name='order_language' value='$orderLanguage'/>
		<input type='hidden' name='task' value='updatecart'/>
		<input type='hidden' name='option' value='com_virtuemart'/>
		<input type='hidden' name='view' value='cart'/>
    </form>
</div>
<div style="display: none" class="form-hidden-container"></div>
ENDFORM;
    }

    public function getProductListHeaders()
    {
        $headers = array(
            vmText::_ ('COM_VIRTUEMART_CART_NAME'),
            vmText::_ ('COM_VIRTUEMART_CART_PRICE'),
            vmText::_ ('COM_VIRTUEMART_CART_QUANTITY'),
            vmText::_ ('COM_VIRTUEMART_CART_TOTAL'),
        );
        if (VmConfig::get('show_tax')) {
            $tax = vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT');
            if(!empty($this->_cart->cartData['VatTax'])){
                if(count($this->_cart->cartData['VatTax']) < 2) {
                    reset($this->_cart->cartData['VatTax']);
                    $taxd = current($this->_cart->cartData['VatTax']);
                    $tax = shopFunctionsF::getTaxNameWithValue(vmText::_($taxd['calc_name']),$taxd['calc_value']);
                }
            }
            array_push($headers, $tax);
        }
        return $headers;
    }

    public function getProductItems()
    {
        $productItems = array();
        foreach ($this->_cart->products as $pkey => $prow) {
            $productItem = array();
            $prow->prices = array_merge($prow->prices, $this->_cart->cartPrices[$pkey]);
            $productItem['position'] = '<input type="hidden" name="cartpos[]" value="' . $pkey . '">';
            $imageUrl = '';
            if ($prow->virtuemart_media_id && !empty($prow->images[0])) {
                $imageHtml = $prow->images[0]->displayMediaThumb('', false);
                preg_match('/src=[\'"]([\s\S]+?)[\'"]/', $imageHtml, $matches);
                if (count($matches) > 1) {
                    $imageUrl = $matches[1];
                }
            }
            $productItem['image'] = $imageUrl;
            //$productItem['name'] = HTMLHelper::link ($prow->url, $prow->product_name);
            $productItem['name'] = $prow->product_name;
            $productItem['name-url'] = $prow->url;
            $productItem['customfields'] = $this->_component->customfieldsModel->CustomsFieldCartDisplay($prow);

            $prices = '';
            /*if (VmConfig::get('checkout_show_origprice', 1) && $prow->prices['discountedPriceWithoutTax'] != $prow->prices['priceWithoutTax']) {
                $prices = $this->_component->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, true) . '<br />';
            }*/
            if ($prow->prices['discountedPriceWithoutTax']) {
                $prices .= $this->_component->currencyDisplay->createPriceDiv('discountedPriceWithoutTax', '', $prow->prices, true);
            } else {
                $prices .= $this->_component->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, true);
            }

            $productItem['prices'] = $prices;
            $productItem['tax'] = $this->_component->currencyDisplay->createPriceDiv('taxAmount', '', $prow->prices, true, false, $prow->quantity, true, true);

            if ($prow->step_order_level) {
                $step = $prow->step_order_level;
            } else {
                $step = 1;
            }
            if ($step == 0) {
                $step = 1;
            }
            $wrongAmountAdded =  vmText::_('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', true);
            $cartUpdateText = vmText::_('COM_VIRTUEMART_CART_UPDATE');
            $quantityValue = $prow->quantity;
            $quantity = <<<QUANTITY
<input type="text"
       onblur="Virtuemart.checkQuantity(this, $step,'$wrongAmountAdded');"
       onclick="Virtuemart.checkQuantity(this, $step,'$wrongAmountAdded');"
       onchange="Virtuemart.checkQuantity(this, $step,'$wrongAmountAdded');"
       onsubmit="Virtuemart.checkQuantity(this, $step,'$wrongAmountAdded');"
       title="$cartUpdateText" class="quantity-input js-recalculate" size="3" maxlength="4" name="quantity[$pkey]" value="$quantityValue" />
QUANTITY;
            $productItem['quantity'] = $quantity;
            $productItem['update-button'] = '<button type="submit" style="display:none" class="vmicon vm2-add_quantity_cart" name="updatecart.' . $pkey .'" title="' . vmText::_('COM_VIRTUEMART_CART_UPDATE') . '" data-dynamic-update="1" ></button>';
            $productItem['delete-button'] = '<button type="submit" style="display:none" class="vmicon vm2-remove_from_cart" name="delete.' . $pkey .'" title="' . vmText::_('COM_VIRTUEMART_CART_DELETE') .'" ></button>';

            $totalPrices = '';
            /*if (VmConfig::get('checkout_show_origprice', 1) && !empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceWithTax'] != $prow->prices['salesPrice']) {
                $totalPrices = $this->_component->currencyDisplay->createPriceDiv('basePriceWithTax', '', $prow->prices, true, false, $prow->quantity) . '<br />';
            } elseif (VmConfig::get('checkout_show_origprice', 1) && empty($prow->prices['basePriceWithTax']) && !empty($prow->prices['basePriceVariant']) && $prow->prices['basePriceVariant'] != $prow->prices['salesPrice']) {
                $totalPrices = $this->_component->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, true, false, $prow->quantity) . '<br />';
            }*/
            $totalPrices .= $this->_component->currencyDisplay->createPriceDiv('salesPrice', '', $prow->prices, true, false, $prow->quantity);
            $productItem['total-prices'] = $totalPrices;
            array_push($productItems, $productItem);
        }
        return $productItems;
    }

    public function includeScripts()
    {
        vmJsApi::vmValidator();

        if(VmConfig::get('oncheckout_ajax', false)) {
            vmJsApi::addJScript('updDynamicListeners',"
        if (typeof Virtuemart.containerSelector === 'undefined') { Virtuemart.containerSelector = '#cart-view'; }
        if (typeof Virtuemart.container === 'undefined') { Virtuemart.container = jQuery(Virtuemart.containerSelector); }
        
        jQuery(document).ready(function() {
            if (Virtuemart.container)
                Virtuemart.updDynFormListeners();
        }); ");
        }

        $orderDoneLink = Route::_('index.php?option=com_virtuemart&view=cart&task=orderdone');

        vmJsApi::addJScript('vm-checkoutFormSubmit',"
        Virtuemart.bCheckoutButton = function(e) {
            e.preventDefault();
            jQuery(this).vm2front('startVmLoading');
            jQuery(this).attr('disabled', 'true');
            jQuery(this).removeClass( 'vm-button-correct' );
            jQuery(this).addClass( 'vm-button' );
            jQuery(this).fadeIn( 400 );
            var name = jQuery(this).attr('name');
            var div = '<input name=\"'+name+'\" value=\"1\" type=\"hidden\">';
            if(name=='confirm'){
                jQuery('#checkoutForm').attr('action','".$orderDoneLink."');
            }
            jQuery('#checkoutForm').append(div);
            //Virtuemart.updForm();
        
            jQuery('#checkoutForm').submit();
        }
        jQuery(document).ready(function($) {
            jQuery(this).vm2front('stopVmLoading');
            var el = jQuery('#checkoutFormSubmit');
            el.unbind('click dblclick');
            el.on('click dblclick', Virtuemart.bCheckoutButton);
            
            jQuery('.u-cart-remove-item').on('click', function () {
                var deleteSubmitBtn = jQuery(this).next();
                if (deleteSubmitBtn.length) {
                    deleteSubmitBtn.click();
                }                
            });
            
            jQuery('.u-cart-update').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var updateSubmitBtn = $('.vm2-add_quantity_cart');
                if (updateSubmitBtn.length) {
                    updateSubmitBtn[0].click();
                }                
            });
            
            jQuery('.u-cart-checkout-btn').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var checkoutSubmitBtn = $('#checkoutFormSubmit');
                if (checkoutSubmitBtn.length) {
                    checkoutSubmitBtn.click();
                }
            });            
            
            jQuery('.form-login-button, .shopperform-button').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var formContainer = $(this).parents('.com-form-container, .com-shopperform-container');
                if (formContainer.length) {
                    var formHtml = formContainer.html().trim();
                    formHtml = formHtml.replace(/^<div/, '<form');
                    formHtml = formHtml.replace(/div>$/, 'form>');
                    formHtml = formHtml.replaceAll('data-name=', 'name=');
                    formHtml = formHtml.replaceAll('data-action=', 'action=');
                    formHtml = formHtml.replaceAll('data-method=', 'method=');
                    var hiddenForm = $(formHtml);
                    formContainer.find('*[data-name]').each(function () {
                        var userInput = $(this);
                        var userInputType = userInput.attr('type');
                        var dataName = userInput.attr('data-name');
                        var tagName = userInput.prop('tagName');
                        if (tagName === 'SELECT') {
                            hiddenForm.find('select[name=\"' + dataName + '\"]').val(userInput.val());
                        } else if (userInputType == 'checkbox') {
                            hiddenForm.find('input[name=\"' + dataName + '\"]')[0].setAttribute('checked', userInput.checked);
                        } else if (userInputType == 'text' || userInputType == 'password') {
                            hiddenForm.find('input[name=\"' + dataName + '\"]')[0].setAttribute('value', userInput.val());
                        }
                    });
                    $('.form-hidden-container').html('');
                    $('.form-hidden-container').append(hiddenForm);                    
                    hiddenForm.submit();
                }         
            }); 
            
        });
            ");

        if( !VmConfig::get('oncheckout_ajax',false)) {
            vmJsApi::addJScript('vm-STisBT',"
                jQuery(document).ready(function($) {
        
                    if ( $('#STsameAsBTjs').is(':checked') ) {
                        $('#output-shipto-display').hide();
                    } else {
                        $('#output-shipto-display').show();
                    }
                    $('#STsameAsBTjs').click(function(event) {
                        if($(this).is(':checked')){
                            $('#STsameAsBT').val('1') ;
                            $('#output-shipto-display').hide();
                        } else {
                            $('#STsameAsBT').val('0') ;
                            $('#output-shipto-display').show();
                        }
                        var form = jQuery('#checkoutFormSubmit');
                        form.submit();
                    });
                });
            ");
        }

        $this->_component->addCheckRequiredJs();
        vmJsApi::addJScript('vmprices', false, false);
        ?>
        <div style="display:none;" id="cart-js">
            <?php echo vmJsApi::writeJS(); ?>
        </div>
        <?php
    }
}
