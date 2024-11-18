<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Virtuemart;

defined('_JEXEC') or die;

use \VmModel;

/**
 * Class CategoryMapper
 */
class MediaMapper
{
    /**
     * @return mixed
     */
    public static function getModel()
    {
        return VmModel::getModel('Media');
    }

    /**
     * @param $imageData
     *
     * @return mixed
     */
    public static function addMedia($imageData) {
        $data = array(
            'file_description' => '',
            'file_meta' => '',
            'file_class' => '',
            'file_url_thumb' => '',
            'file_type' => 'product',
            'virtuemart_media_id' => null,
            'file_extension' => '',
            'virtuemart_vendor_id' => 0,
            'file_is_downloadable' => 0,
            'file_is_forSale' => 0,
            'shared' => 0,
            'published' => 1,
            'file_params' => '',
            'file_lang' => '',
            'created_on' => false,
            'created_by' => 0,
            'modified_on' => '',
            'modified_by' => 0,
            'locked_on' => '',
            'locked_by' => 0,
            'media_role' => 'file_is_displayable',
        );
        $data = array_merge($data, $imageData);
        $table = self::getModel()->getTable('medias');
        $table->bind($data);
        $table->bindChecknStore($data);
        return $table->virtuemart_media_id;
    }

    /**
     * @param $product_id
     * @param $mediaIds
     */
    public static function addProductMedia($product_id, $mediaIds)
    {
        $productMediaData = array(
            'id' => null,
            'virtuemart_product_id' => $product_id,
            'virtuemart_media_id' => $mediaIds,
        );
        $table = self::getModel()->getTable('product_medias');
        $table->bind($productMediaData);
        // Bind the media to the product
        $table->bindChecknStore($productMediaData);
    }
}
