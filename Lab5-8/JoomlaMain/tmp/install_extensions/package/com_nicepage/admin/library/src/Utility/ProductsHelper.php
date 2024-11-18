<?php

namespace NP\Utility;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Class ProductsHelper
 *
 * @package NP\Utility
 */
class ProductsHelper
{
    /**
     * @var data
     */
    private $_data;

    /**
     * ProductsHelper constructor.
     *
     * @param object $data Product json data
     */
    public function __construct($data)
    {
        if (is_array($data)) {
            $this->loadFromArray($data);
        } else {
            $this->loadFromString($data);
        }
    }

    /**
     * @param string $content Json content
     */
    public function loadFromString($content)
    {
        $this->_data = json_decode($content, true);
    }

    /**
     * @param array $list Json array
     */
    public function loadFromArray($list)
    {
        $this->_data = json_decode(json_encode($list), true);
    }

    /**
     * Get products
     *
     * @param string $catId
     *
     * @return array|mixed
     */
    public function getProducts($catId = '')
    {
        $products = isset($this->_data['products']) ? $this->_data['products'] : array();
        $categories = isset($this->_data['categories']) ? $this->_data['categories'] : array();

        if ($catId) {
            $result = array();
            foreach ($products as $product) {
                if (in_array($catId, $product['categories'])) {
                    array_push($result, $product);
                }
                if ($catId === 'featured' && isset($product['isFeatured']) && $product['isFeatured']) {
                    array_push($result, $product);
                }
            }
            $products = $result;
        }

        foreach ($products as &$p) {
            $p['categoriesData'] = $this->_getCategoriesData($categories, $p['categories']);
        }

        return $products;
    }

    /**
     * Get categories data
     *
     * @param array $categories    All categories
     * @param array $productCatIds Product categories
     *
     * @return array
     */
    private function _getCategoriesData($categories, $productCatIds) {
        $categoriesData = array();
        foreach ($categories as $category) {
            if (in_array($category['id'], $productCatIds)) {
                $pageId = Factory::getApplication()->input->get('page_id', '');
                $category['link'] = 'index.php?option=com_nicepage&view=product&page_id=' . $pageId . '&product_name=product-list&catid=' . $category['id'];
                array_push($categoriesData, $category);
            }
        }
        if (count($categoriesData) < 1) {
            array_push($categoriesData, array('id' => 0, 'title' => 'Uncategorized'));
        }
        return $categoriesData;
    }

    /**
     * Get categories data
     *
     * @return array
     */
    public function getCategories() {
        $allCategories = isset($this->_data['categories']) ? $this->_data['categories'] : array();
        $pageId = Factory::getApplication()->input->get('page_id', '');
        foreach ($allCategories as &$c) {
            $c['link'] = 'index.php?option=com_nicepage&view=product&page_id=' . $pageId . '&product_name=product-list&catid=' . $c['id'];
        }
        $categories = self::buildTreeCategories($allCategories);
        return $categories;
    }

    /**
     * Build tree from categories
     *
     * @param array $categories List of categories
     * @param int   $parentId   Parennt id
     *
     * @return array
     */
    public static function buildTreeCategories($categories, $parentId = 0)
    {
        $result = array();
        $pageId = Factory::getApplication()->input->get('page_id', '');
        if ($parentId === 0) {
            $item = new \stdClass();
            $item->title = 'All';
            $item->id = '';
            $item->active = false;
            $item->link = 'index.php?option=com_nicepage&view=product&page_id=' . $pageId . '&product_name=product-list';
            $item->children = array();
            array_push($result, $item);
        }
        if ($parentId === 0) {
            $item = new \stdClass();
            $item->title = 'Featured';
            $item->id = 'featured';
            $item->active = false;
            $item->link = 'index.php?option=com_nicepage&view=product&page_id=' . $pageId . '&product_name=product-list&catid=featured';
            $item->children = array();
            array_push($result, $item);
        }
        foreach ($categories as $category) {
            $categoryId = $category['categoryId'];
            if (!$parentId && $categoryId) {
                continue;
            }
            if ($parentId && $parentId !== $categoryId) {
                continue;
            }
            $item = new \stdClass();
            $item->id = $category['id'];
            $item->title = $category['title'];
            $item->link = $category['link'];
            $item->active = false;
            $item->children = self::buildTreeCategories($categories, $category['id']);
            array_push($result, $item);
        }
        return $result;
    }
}
