<?php
defined('_JEXEC') or die;

Core::load("Core_Content_Site_SingleProduct");

class CoreContentSiteProductDetails extends CoreContentSiteSingleProduct
{
    public function __construct($product)
    {
        parent::__construct($product);
    }
}
