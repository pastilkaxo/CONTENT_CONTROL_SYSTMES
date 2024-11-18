<?php
defined('_JEXEC') or die;

Core::load("Core_Content_Site_ProductDetails");
class CoreContentSiteProductVariablesCreator
{
    private $_product;

    public function __construct($productModel)
    {
        $this->_product = new CoreContentSiteProductDetails($productModel);
    }

    public function getVariables()
    {
        return array(
            'title0'                 => $this->_product->title,
            'titleLink0'             => $this->_product->titleLink,
            'content0'               => $this->_product->description,
            'fullContent0'           => $this->_product->fullDescription,
            'image0'                 => $this->_product->getImage(),
            'galleryImages'          => $this->_product->getGalleryImages(),
            'variations'             => $this->_product->variations(),
            'productRegularPrice0'   => $this->_product->getRegularPrice(),
            'productOldPrice0'       => $this->_product->getOldPrice(),
            'productCategories0'     => $this->_product->categoriesData,
            'productOutOfStock0'     => $this->_product->outOfStock(),
            'productSku0'            => $this->_product->sku(),
            'productIsNew0'          => $this->_product->isNew(),
            'productIsSale0'         => $this->_product->sale(),
            'productId0'             => $this->_product->id,
            'productJson0'           => $this->_product->toJson(),
        );
    }
}
