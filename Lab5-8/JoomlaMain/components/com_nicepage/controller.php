<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\User\User;
use NP\Processor\ProductsProcessor;
use NP\Processor\BlogProcessor;

JLoader::register('NicepageHelpersNicepage', JPATH_ADMINISTRATOR . '/components/com_nicepage/helpers/nicepage.php');
JLoader::register('NicepageHelper', JPATH_COMPONENT . '/helpers/nicepage.php');

/**
 * Class NicepageController
 */
class NicepageController extends BaseController
{
    /**
     * Default display view
     *
     * @param bool $cachable
     * @param bool $urlparams
     *
     * @return mixed
     */
    public function display($cachable = false, $urlparams = false)
    {
        $input = Factory::getApplication()->input;
        $uid = $input->get('uid', '');
        if ($uid) {
            $session = Factory::getSession();
            $user = new User((int) $uid);
            $session->set('user', $user);
            exit(json_encode(array('result' => 'ok')));
        }

        return parent::display($cachable, $urlparams);
    }

    /**
     * Get site products json content
     */
    public function siteproducts() {
        $input = Factory::getApplication()->input;
        $pageId = $input->get('page_id', '');

        $config = NicepageHelpersNicepage::getConfig();
        $result = array();
        if (isset($config['productsJson'])) {
            $result = $config['productsJson'];
        }

        if (is_array($result)) {
            $result = json_encode($result);
        }

        $data = json_decode($result, true);

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

        $baseUrl = Uri::root() . 'index.php?option=com_nicepage&view=product&page_id=' . $pageId . '&product_name=product-';
        foreach ($products as & $product) {
            $product['link'] = $baseUrl . $product['id'];
        }

        $result = json_encode(
            array(
                'products' => $products,
                'categories' => $categories,
            )
        );

        $result = str_replace('[[site_path_editor]]', rtrim(Uri::root(), '/\\'), $result);

        header('Content-Type: application/json');
        exit($result);
    }

    /**
     * Get one blog html
     */
    public function blogposts() {
        $input = Factory::getApplication()->input;
        $pageId = $input->get('pageId', 0);
        $page = NicepageHelpersNicepage::getSectionsTable();
        if (!$page->load(array('page_id' => $pageId))) {
            exit(1);
        }
        $props = $page->getProps();
        $publishHtml = isset($props['publishHtml']) ? $props['publishHtml'] : '';
        if (!$publishHtml) {
            exit(1);
        }

        $blogProcessor = new BlogProcessor($pageId);
        $blogHtml = $blogProcessor->processBlogByAjaxLoad($publishHtml);
        exit($blogHtml);
    }

    /**
     * Get one products html
     */
    public function productlist() {
        $input = Factory::getApplication()->input;
        $pageId = $input->get('pageId', 0);
        $page = NicepageHelpersNicepage::getSectionsTable();
        if (!$page->load(array('page_id' => $pageId))) {
            exit(1);
        }

        $publishHtml = isset($page->props['publishHtml']) ? $page->props['publishHtml'] : '';
        if (!$publishHtml) {
            exit(1);
        }

        $productsProcessor = new ProductsProcessor($pageId);
        $productsHtml = $productsProcessor->processProductsByAjaxLoad($publishHtml);
        exit($productsHtml);
    }

    /**
     * Get product content
     */
    public function product() {
        $input = Factory::getApplication()->input;
        $pageId = $input->get('pageId', 0);
        $productId = $input->get('virtuemart_product_id', 0);
        $dynamic = $input->get('dynamic', '0');

        if ($dynamic == '1') {
            $page = NicepageHelpersNicepage::getSectionsTable();
            if (!$page->load(array('page_id' => $pageId))) {
                exit(1);
            }

            $publishHtml = isset($page->props['publishHtml']) ? $page->props['publishHtml'] : '';
            if (!$publishHtml) {
                exit(1);
            }

            $productHtml = '';
            if (preg_match('/<\!--product-->([\s\S]+?)<\!--\/product-->/', $publishHtml, $productMatches)) {
                $productHtml = $productMatches[0];
                $jsonRe = '/<\!--product_options_json--><\!--([\s\S]+?)--><\!--\/product_options_json-->/';
                if (preg_match($jsonRe, $productMatches[1], $optionsMatches)) {
                    $productOptions = json_decode($optionsMatches[1], true);
                    $productOptions['source'] = $productId;
                    $productHtml = str_replace($optionsMatches[1], json_encode($productOptions), $productHtml);
                }
            }

            if ($productHtml) {
                $products = new ProductsProcessor($pageId);
                $productHtml = $products->process($productHtml);
                exit($productHtml);
            }
        }
        exit(1);
    }

    /**
     *  Get products
     */
    public function products() {
        $source = Factory::getApplication()->input->get('category', '', 'RAW');
        exit(
            json_encode(
                array(
                    'result' => 'done',
                    'products' => NicepageHelper::getProductsBySource($source),
                )
            )
        );
    }

    /**
     * Get cms product categories
     */
    public function productcategories() {
        exit(
            json_encode(
                array(
                    'result' => 'done',
                    'categories' => NicepageHelper::getCategories(),
                )
            )
        );
    }

    /**
     *  Get posts
     */
    public function posts() {
        $source = Factory::getApplication()->input->get('category', '', 'RAW');
        exit(
            json_encode(
                array(
                    'result' => 'done',
                    'blog' => NicepageHelper::getPostsBySource($source),
                )
            )
        );
    }

    /**
     * Get data info
     */
    public function getInfoData() {
        $menuData = NicepageHelper::getMenuInfoData();
        exit(
            json_encode(
                array(
                    'result' => array(
                        'menuItems' => $menuData['menuItems'],
                        'menuOptions' => $menuData['menuOptions'],
                        'blogInfo' => NicepageHelper::getBlogInfoData(),
                        'productsInfo' => NicepageHelper::getProductsInfoData(),
                        'homeItemId' => NicepageHelper::getHomeItemId(),
                    )
                )
            )
        );
    }

    /**
     * Submit form with custom code.
     */
    public function form()
    {
        exit(NicepageHelper::customSendMail());
    }

    /**
     * Submit form with joomla
     */
    public function sendmail()
    {
        exit(NicepageHelper::joomlaSendMail());
    }
}
