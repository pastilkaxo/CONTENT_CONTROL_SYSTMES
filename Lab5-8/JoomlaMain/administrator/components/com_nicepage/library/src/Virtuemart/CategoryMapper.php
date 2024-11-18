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
class CategoryMapper
{
    /**
     * @return mixed
     */
    public static function getModel()
    {
        return VmModel::getModel('category');
    }

    /**
     * @param array $filter
     *
     * @return array
     */
    public static function find($filter = array())
    {
        if (isset($filter['title'])) {
            $title = $filter['title'];
        }

        $categories = self::getModel()->getCategoryTree('', 0, false, '', '', '', false, '');
        $result = array();
        foreach ($categories as $category) {
            if ($category->category_name === $title) {
                array_push($result, $category);
            }
        }
        return $result;
    }

    /**
     * @param $id
     */
    public static function delete($id) {
        $products = ProductMapper::getProductsByCatId($id);
        foreach ($products as $product) {
            ProductMapper::delete($product->virtuemart_product_id);
        }
        self::getModel()->remove(array($id));
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public static function create($data) {
        return self::getModel()->store($data);
    }
}
