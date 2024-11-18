<?php
defined('_JEXEC') or die;

Core::load("Core_Content_SingleProduct");
class CoreContentProductDetails extends CoreContentSingleProduct
{
    public function __construct($component, $componentParams, $product)
    {
        parent::__construct($component, $componentParams, $product);
    }
}
