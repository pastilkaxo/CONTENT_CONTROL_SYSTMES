<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

class CoreContentCheckout
{
    private $_component;
    private $_componentParams;
    private $_cart;
    private $_buttons;

    public function __construct($component, $componentParams)
    {
        $this->_component = $component;
        $this->_componentParams = $componentParams;
        $this->_cart = $component->cart;

        $this->_buttons = array();
        $rview = 'user';
        if ($this->_cart->_fromCart or $this->_cart->getInCheckOut()) {
            $rview = 'cart';
        }
        if (VmConfig::get ('oncheckout_show_register', 1) &&
            $this->_component->userDetails->JUser->id == 0 &&
            $this->_component->address_type == 'BT' &&
            $rview == 'cart') {
            array_push($this->_buttons, array(
                'id' => 'register',
                'url' => '#',
                'content' => vmText::_ ('COM_VIRTUEMART_REGISTER_AND_CHECKOUT'),
                'fakeBtn' => '<button name="register" style="display: none;" type="submit" onclick="javascript:return myValidator(userForm,true);"></button>',
            ));
            if (!VmConfig::get ('oncheckout_only_registered', 0)) {
                array_push($this->_buttons, array(
                    'id' => 'save',
                    'url' => '#',
                    'content' => vmText::_ ('COM_VIRTUEMART_CHECKOUT_AS_GUEST'),
                    'fakeBtn' => '<button name="save"  style="display: none;" type="submit" onclick="javascript:return myValidator(userForm, false);"></button>',
                ));
            }
        } else {
            array_push($this->_buttons, array(
                'id' => 'register',
                'url' => '#',
                'content' => vmText::_ ('COM_VIRTUEMART_SAVE'),
                'fakeBtn' => '<button name="register" style="display: none;" type="submit" onclick="javascript:return myValidator(userForm,true);"></button>',
            ));
        }
        array_push($this->_buttons, array(
            'id' => 'cancel',
            'url' => '#',
            'content' => vmText::_ ('COM_VIRTUEMART_CANCEL'),
            'fakeBtn' => '<button name="cancel" style="display: none;" type="reset" onclick="window.location.href=\'' . Route::_ ('index.php?option=com_virtuemart&view=' . $rview.'&task=cancel') . '\'"></button>',
        ));
    }

    public function renderButtons($template) {
        $result = '';
        foreach ($this->_buttons as $button) {
            $btn = str_replace('[[url]]', $button['url'], $template);
            $btn = str_replace('[[content]]', $button['content'], $btn);
            $btn = str_replace('<a', '<a id="' . $button['id'] . '" ', $btn);
            $result .= $btn . ' ';
        }
        $result .= <<<SCRIPT
<script>
    jQuery('#register').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();        
        jQuery('button[name="register"]').click();
    });
    jQuery('#save').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();        
        jQuery('button[name="save"]').click();
    });
    jQuery('#cancel').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();        
        jQuery('button[name="cancel"]').click();
    });
</script>
SCRIPT;
        return $result;
    }

    public function getAddressTypeHeaderText() {
        if ($this->_component->address_type == 'BT') {
            return vmText::_ ('COM_VIRTUEMART_USER_FORM_EDIT_BILLTO_LBL');
        }
        return vmText::_ ('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL');
    }

    public function getAddressFormData() {
        $layout = $this->_component->getLayout();
        $addressType = $this->_component->address_type;
        $virtuemartUserInfoId = $this->_component->virtuemart_userinfo_id;
        $token = HTMLHelper::_('form.token');
        $hiddenFields = <<<DATA
<input type="hidden" name="option" value="com_virtuemart"/>
	<input type="hidden" name="view" value="user"/>
	<input type="hidden" name="controller" value="user"/>
	<input type="hidden" name="task" value="saveUser"/>
	<input type="hidden" name="layout" value="$layout"/>
	<input type="hidden" name="address_type" value="$addressType"/>
	$token
DATA;
        if (!empty($virtuemartUserInfoId)) {
            $hiddenFields .= '<input type="hidden" name="shipto_virtuemart_userinfo_id" value="' . (int)$virtuemartUserInfoId . '" />';
        }

        foreach ($this->_buttons as $button) {
            $hiddenFields .= $button['fakeBtn'];
        }

        return array(
            'name' => 'userForm',
            'action' => Route::_('index.php?option=com_virtuemart&view=user', $this->_component->useXHTML, $this->_component->useSSL),
            'hiddenFields' => $hiddenFields,
        );
    }

    public function includeScripts()
    {
        //vmJsApi::css('vmpanels');
        $scripts = vmJsApi::getJScripts();
        if ($scripts && isset($scripts['vm-validator'])) {
            $vmValidator = $scripts['vm-validator'];
            $vmValidator['script'] = preg_replace('/\[[^]]+?\]/', '[]', $vmValidator['script']);
            vmJsApi::addJScript('vm-validator', $vmValidator['script']);
        }
    }

    public function getCartTotals()
    {
        $this->_cart->prepareCartData();
        $currencyDisplay = CurrencyDisplay::getInstance();
        $subTotal = $currencyDisplay->createPriceDiv('salesPrice', '', $this->_cart->cartPrices, true);
        $total = $currencyDisplay->createPriceDiv('billTotal', '', $this->_cart->cartPrices['billTotal'], true);
        return array(
            'header' => vmText::_('COM_VIRTUEMART_CART_TOTAL'),
            'subtotalText' => vmText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'),
            'subtotal' => $subTotal,
            'totalText' => vmText::_('COM_VIRTUEMART_CART_TOTAL'),
            'total' => $total,
        );
    }

    public function getUserFields()
    {
        $userFields = array();
        foreach($this->_component->userFields['fields'] as $field) {
            if ($field['type'] == 'delimiter' || $field['hidden'] == true) {
                continue;
            }
            $html  = $field['formcode'];
            $id = '';
            if (preg_match('/id=("|\')([^\'"]+?)("|\')/', $html, $matches)) {
                $id = $matches[2];
            }
            $type = 'text';
            if (strpos($html, '<textarea') !== false) {
                $type = 'textarea';
            } else if (strpos($html, '<select') !== false) {
                $type = 'select';
            } else if (preg_match('/type=("|\')([^\'"]+?)("|\')/', $html, $matches)) {
                $type = $matches[2];
            }
            array_push($userFields, array(
                'desc' => strip_tags(empty($field['description']) ? $field['title'] : $field['description']),
                'name' => $field['name'],
                'title' => $field['title'],
                'required' => $field['required'] === 1 ? true : false,
                'type' => $type,
                'hidden' => $field['hidden'],
                'id' => $id,
                'formcode' => $field['formcode'],
            ));
        }
        return $userFields;
    }

    public function renderFields($templates)
    {
        $types = array(
            'email' => 'input',
            'text' => 'input',
            'password' => 'input',
            'select' => 'select',
        );
        $result = '';
        $fields = $this->getUserFields();
        for ($i = 0; $i < count($fields); $i++) {
            $field = $fields[$i];
            if (array_key_exists($field['type'], $types)) {
                $template = $templates[$types[$field['type']]];

                $labelTemplate = $template['label'];
                $labelTemplate = str_replace('[[content]]', $field['title'], $labelTemplate);
                $labelTemplate = preg_replace('/for=("|\')([^\'"]+?)("|\')/', 'for=$1' . $field['id'] . '$3' , $labelTemplate);

                $fieldTemplate = $template['field'];

                if (preg_match('/<select[^>]+?>([\s\S]*?)<\/select>/', $fieldTemplate, $matches)) {
                    preg_match('/<select[^>]+?>([\s\S]*?)<\/select>/', $field['formcode'], $matches2);
                    $fieldTemplate = str_replace($matches[1], $matches2[1], $fieldTemplate);
                }

                $fieldTemplate = str_replace('[[type]]', $field['type'], $fieldTemplate);
                $fieldTemplate = str_replace('[[name]]', $field['name'], $fieldTemplate);
                $fieldTemplate = str_replace('[[placeholder]]', '', $fieldTemplate);
                $fieldTemplate = preg_replace('/id=("|\')([^\'"]+?)("|\')/', 'id=$1' . $field['id'] . '$3' , $fieldTemplate);

                if ($field['required']) {
                    $fieldTemplate = str_replace('[[required]]', 'required', $fieldTemplate);
                } else {
                    $fieldTemplate = str_replace('required="[[required]]"', '', $fieldTemplate);
                }

                $groupTemplate = $template['group'];
                $groupTemplate = str_replace('[[label]]', $labelTemplate, $groupTemplate);
                $groupTemplate = str_replace('[[field]]', $fieldTemplate, $groupTemplate);
                $result .= $groupTemplate . "\n\r";
            }
        }
        return $result;
    }
}
