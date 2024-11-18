<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class TplNatureHelper
{
    public static function siteproductsAjax() {
        $themeName = Factory::getApplication()->getTemplate();
        $root = str_replace('/', '\/', Uri::root() . 'templates/' . $themeName . '/');

        $result = json_encode(self::getProductsData());
        $result = str_replace(':"images', ':"' . $root . 'images', $result);

        header('Content-Type: application/json');
        exit($result);
    }

    public static function productsAjax() {
        $data = self::getProductsData();
        $allProducts = $data['products'];
        $allCategories = $data['categories'];

        if (count($allProducts) < 1) {
            return '';
        }

        $id = Factory::getApplication()->input->getCmd('catid', '');
        if ($id) {
            $result = array();
            foreach ($allProducts as $product) {
                if (in_array($id, $product['categories'])) {
                    array_push($result, $product);
                }
            }
            $products = $result;
        } else {
            $products = $allProducts;
        }

        $categories = self::buildTreeCategories($allCategories);

        ob_start();
        include_once dirname(__FILE__) . '/ecommerce/category/default.php';
        $result = ob_get_clean();
        return $result;
    }
    public static function productAjax() {
        $productName = Factory::getApplication()->input->get('product_name', '');

        if (!$productName) {
            return '';
        }

        $product = null;
        $data = self::getProductsData();
        foreach ($data['products'] as $p) {
            if (('product-' . $p['id']) === $productName) {
                $product = $p;
                break;
            }
        }

        if (!$product) {
            return '';
        }

        ob_start();
        include_once dirname(__FILE__) . '/ecommerce/productdetails/default.php';
        $result = ob_get_clean();
        return $result;
    }

    public static function buildTreeCategories($categories, $parentId = 0)
    {
        $result = array();
        if ($parentId === 0) {
            $item = new stdClass();
            $item->title = 'All';
            $item->id = '';
            $item->active = false;
            $item->link = self::getBaseUrl() . '&method=products&product_name=product-list';
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
            $item = new stdClass();
            $item->id = $category['id'];
            $item->title = $category['title'];
            $item->link = $category['link'];
            $item->active = false;
            $item->children = self::buildTreeCategories($categories, $category['id']);
            array_push($result, $item);
        }
        return $result;
    }

    public static function getProductsData() {
        $jsonFile = dirname(__FILE__) . '/ecommerce/products.json';
        if (!file_exists($jsonFile)) {
            return array();
        }
        ob_start();
        include_once dirname(__FILE__) . '/ecommerce/products.json';
        $productsJson = ob_get_clean();
        $data = json_decode($productsJson, true);

        $products = array();
        $categories = array();

        if (!$data) {
            return array(
                'products' => $products,
                'categories' => $categories,
            );
        }

        if (isset($data['products'])) {
            $products =  $data['products'];
        }
        if (isset($data['categories'])) {
            $categories =  $data['categories'];
        }

        $url = self::getBaseUrl();
        foreach ($categories as &$c) {
            $c['link'] = $url . '&method=products&product_name=product-list&catid=' . $c['id'];
        }

        foreach ($products as &$p) {
            $p['categoriesData'] = self::getCategoriesData($categories, $p['categories']);
        }

        return array(
            'products' => $products,
            'categories' => $categories,
        );
    }

    public static function getBaseUrl() {
        $themeName = Factory::getApplication()->getTemplate();
        return 'index.php?option=com_ajax&format=html&template=' . $themeName;
    }

    public static function getCategoriesData($categories, $productCatIds) {
        $categoriesData = array();
        foreach ($categories as $category) {
            if (in_array($category['id'], $productCatIds)) {
                array_push($categoriesData, $category);
            }
        }
        if (count($categoriesData) < 1) {
            array_push($categoriesData, array('id' => 0, 'title' => 'Uncategorized'));
        }
        return $categoriesData;
    }
}
