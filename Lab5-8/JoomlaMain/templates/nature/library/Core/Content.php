<?php
defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;
use Joomla\Registry\Registry;

/**
 * Contains the article factory method and content component rendering helpers.
 */
Core::load("Core_Content_ArchivedArticle");
Core::load("Core_Content_SingleArticle");
Core::load("Core_Content_CategoryArticle");
Core::load("Core_Content_FeaturedArticle");
Core::load("Core_Content_ProductDetails");
Core::load("Core_Content_ShoppingCart");
Core::load("Core_Content_Checkout");

class CoreContent
{
    protected $_component;
    protected $_componentParams;

    public $pageClassSfx;

    public $pageHeading;

    public function __construct($component, $params = null)
    {
        $this->_component = $component;
        $this->_componentParams = $params;

        $className = strtolower(get_class($component));
        if (strpos($className, 'virtuemart') === false) {
            $this->pageClassSfx = $component->get('pageclass_sfx');
            $this->pageHeading = $this->_componentParams->get('show_page_heading', 1)
                ? $this->_componentParams->get('page_heading') : '';
        }
    }

    public function pageHeading($title = null)
    {
        $heading = '';
        if (strlen($this->pageHeading)) {
            ob_start();
            echo '<section class="u-clearfix"><div class="u-clearfix u-sheet"><h1>';
            echo $this->pageHeading;
            echo '</h1></div></section>';
            $heading = ob_get_clean();
        }
        return $heading;
    }

    public function article($view, $article, $params, $properties = array())
    {
        switch ($view) {
            case 'archive':
                return new CoreContentArchivedArticle($this->_component, $this->_componentParams,
                    $article, $params);
            case 'article':
                return new CoreContentSingleArticle($this->_component, $this->_componentParams,
                    $article, $params, $properties);
            case 'category':
                return new CoreContentCategoryArticle($this->_component, $this->_componentParams,
                    $article, $params);
            case 'featured':
                return new CoreContentFeaturedArticle($this->_component, $this->_componentParams,
                    $article, $params);
        }
    }

    public function product($view, $product) {
        switch ($view) {
            case 'productdetails':
                return new CoreContentProductDetails($this->_component, $this->_componentParams, $product);
        }
    }

    public function cart($cart) {
        return new CoreContentShoppingCart($this->_component, $this->_componentParams, $cart);
    }

    public function checkout() {
        return new CoreContentCheckout($this->_component, $this->_componentParams);
    }

    public function beginPageContainer($class, $attrs = array())
    {
        $str = '';
        foreach($attrs as $name => $value) {
            $str .= ' ' . $name . (!is_null($value) ? ('="' . $value . '"') : '');
        }
        return '<div class="' . $class . $this->pageClassSfx .'"' . $str . '>';
    }

    public function endPageContainer()
    {
        return '</div>';
    }

    public function getCategories($parentId, $category)
    {
        $cats = array();
        $params = new Registry();
        $params->loadArray(array(
            'parent' => 'root',
            'show_description' => '0',
            'numitems' => '0',
            'show_children' => '1',
            'count' => '1',
            'maxlevel' => '0',
            'layout' => '_:default',
            'item_heading' => '4',
            'moduleclass_sfx' => '',
            'owncache' => '1',
            'cache_time' => '900',
            'module_tag' => 'div',
            'header_tag' => 'h3',
            'header_class' => '',
            'style' => '0',
        ));

        $options = array();
        $options['countItems'] = $params->get('numitems', 0);

        if ($parentId !== 'root') {
            $backCat = new stdClass();
            $backCat->id = $parentId;
            $backCat->title = '< Back';
            array_push($cats, $backCat);
        } else {
            $categories = Categories::getInstance('Content', $options);
            $parentCategory   = $categories->get($parentId, true);
            if ($parentCategory !== null)
            {
                $children = $parentCategory->getChildren();
                foreach ($children as $child) {
                    if ($child->id === $category->id) {
                        continue;
                    }
                    array_push($cats, $child);
                }
            }
        }

        $currentCat = new stdClass();
        $currentCat->id = $category->id;
        $currentCat->title = $category->title;
        $currentCat->active = true;
        $currentCat->children = [];
        array_push($cats, $currentCat);

        $categories = Categories::getInstance('Content', $options);
        $category   = $categories->get($category->id, true);

        if ($category !== null)
        {
            $children = $category->getChildren();
            foreach ($children as $child) {
                array_push($currentCat->children, $child);
            }
        }
        return $cats;
    }

    public function getVmCategories($component)
    {
        $cats = array();
        $currentCat = null;
        if ($component->categoryId !== '0') {
            $backCat = new stdClass();
            $backCat->virtuemart_category_id = $component->category->category_parent_id;
            $backCat->category_name = '< Back';
            array_push($cats, $backCat);

            $currentCat = new stdClass();
            $currentCat->virtuemart_category_id = $component->categoryId;
            $currentCat->category_name = $component->category->category_name;
            $currentCat->active = true;
            $currentCat->children = [];
            array_push($cats, $currentCat);
        }

        $children = VirtueMartModelCategory::getChildCategoryListObject(1, $component->categoryId);
        foreach ($children as $child) {
            if ($currentCat && property_exists($currentCat, 'children')) {
                array_push($currentCat->children, $child);
            } else {
                array_push($cats, $child);
            }
        }

        return $cats;
    }

    public function getOrderByList($orderByList) {
        $list = array();
        if (!empty($orderByList) && isset($orderByList['orderby'])) {
            if (preg_match_all('/<a[^>]+?href=\"([^"]+?)\"[^>]*?>([\s\S]+?)<\/a>/', $orderByList['orderby'], $matches, PREG_SET_ORDER)) {
                $selectedText = '';
                $selectedItem = null;
                foreach ($matches as $match) {
                    if (strpos($match[2], '+/-') !== false) {
                        $selectedText = $match[2];
                        $selectedLink = $match[1];
                        continue;
                    }
                    if (strpos($selectedText, $match[2]) !== false) {
                        $selectedItem = array('link' => $match[1], 'text' => $match[2]);
                        continue;
                    }
                    $item = array('link' => $match[1], 'text' => $match[2]);
                    array_push($list, $item);
                }
                if ($selectedItem) {
                    array_unshift($list, $selectedItem);
                } else if ($selectedText) {
                    array_unshift($list, array('link' => $selectedLink, 'text' => $selectedText));
                }

            }
        }
        return $list;
    }
}
