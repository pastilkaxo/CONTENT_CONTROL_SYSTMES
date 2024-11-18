<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Filesystem\Folder;
//use Joomla\CMS\Component\ComponentHelper;

require_once JPATH_ADMINISTRATOR . '/components/com_nicepage/library/loader.php';
use NP\Utility\ColorHelper;
use NP\Virtuemart\VirtuemartComponent;
use NP\Virtuemart\ProductMapper;
use NP\Virtuemart\CategoryMapper;
use NP\Virtuemart\MediaMapper;

JLoader::register('Nicepage_Data_Mappers', JPATH_ADMINISTRATOR . '/components/com_nicepage/tables/mappers.php');

/**
 * Class Nicepage_Data_Loader
 */
class Nicepage_Data_Loader
{
    /**
     * @var null Sample data object
     */
    private $_data = null;

    /**
     * @var int Default category id
     */
    private $_blogCategoryId = 0;

    /**
     * @var int products list categories
     */
    private $_productCategoriesItems = array();

    /**
     * Numeric identificator of the currently selected template style in Joomla
     * administrator.
     */
    private $_style;

    /**
     * @var string Sample images path
     */
    private $_images = '';

    /**
     * @var string Sample fonts path
     */
    private $_fonts = '';

    /**
     * @var string Google fonts path
     */
    private $_googleFonts = '';

    /**
     * @var array Sample images path
     */
    private $_foundImages = array();

    /**
     * Name of the template.
     */
    private $_template = '';

    /**
     * @var string Cms root url
     */
    private $_rootUrl = '';

    /**
     * @var string Sample data ids string
     */
    private $_dataIds = array();

    /**
     * @var string
     */
    private $_extOptions = '';
    /**
     * @var array New ids for products
     */
    private $_newProductIds = array();

    /**
     * @var bool Replace sample data flag
     */
    private $_replace = false;

    /**
     * @var bool Update settings of nicepage plugin
     */
    private $_updatePluginSettings = false;

    /**
     * @var bool Import menus
     */
    private $_importMenu = true;

    /**
     * @var bool Import products
     */
    private $_importProducts = false;

    /**
     * @var string Prefix of table sections
     */
    private $_dbName = 'nicepage';

    /**
     * Method to load sample data.
     *
     * @param string $file           File path to sample data
     * @param bool   $isThemeContent Flag for theme
     *
     * @return null|string|void
     */
    public function load($file, $isThemeContent = false)
    {
        $config = Factory::getConfig();
        $live_site = $config->get('live_site', '');
        $p = dirname(dirname(Uri::current()));
        $root = trim($live_site) != '' ? Uri::root(true) : ($isThemeContent ? dirname(dirname($p)) : $p);
        if ('/' === substr($root, -1)) {
            $this->_rootUrl  = substr($root, 0, -1);
        } else {
            $this->_rootUrl  = $root;
        }

        $path = realpath($file);
        if (false === $path) {
            return;
        }

        $images = dirname($path) . DIRECTORY_SEPARATOR . 'images';
        if (file_exists($images) && is_dir($images)) {
            $this->_images = $images;
        }

        $fonts = dirname($path) . DIRECTORY_SEPARATOR . 'fonts';
        if (file_exists($fonts) && is_dir($fonts)) {
            $this->_fonts = $fonts;
        }

        $googleFonts = dirname($path) . DIRECTORY_SEPARATOR . 'google-fonts';
        if (file_exists($googleFonts) && is_dir($googleFonts)) {
            $this->_googleFonts = $googleFonts;
        }

        if ($isThemeContent) {
            $this->_template = basename(dirname(dirname($path)));
        }

        $replaceKey = $this->_template ? 'replace' : 'replaceStatus';
        $params = Factory::getApplication()->input->getArray();
        $this->_replace = isset($params[$replaceKey]) && $params[$replaceKey] == '1' ? true : false;

        $categories = Nicepage_Data_Mappers::get('category');
        $categoryList = $categories->find(array('extension' => 'com_content'));
        foreach ($categoryList as & $categoryListItem) {
            if ($this->_blogCategoryId === 0 || $categoryListItem->id < $this->_blogCategoryId) {
                $this->_blogCategoryId = $categoryListItem->id;
            }
        }

        return $this->_parse($path);
    }

    /**
     * Method to execute installing sample data.
     *
     * @param array $params Sample data installing parameters
     */
    public function execute($params)
    {
        $callback = array();
        $callback[] = $this;
        $callback[] = '_error';
        Nicepage_Data_Mappers::errorCallback($callback);

        if (isset($params['updatePluginSettings']) && $params['updatePluginSettings'] == '1') {
            $this->_updatePluginSettings = true;
        }

        if (isset($params['importMenus']) && $params['importMenus'] == '0') {
            $this->_importMenu = false;
        }

        if (isset($params['importProducts']) && $params['importProducts'] == '1') {
            $this->_importProducts = true;
        }

        if ($this->_template) {
            $action = isset($params['action']) && is_string($params['action']) ? $params['action'] : '';
            if (0 == strlen($action) || !in_array($action, array('check', 'run', 'nicepage'))) {
                return 'Invalid action.';
            }
            $this->_style = isset($params['id']) ? intval($params['id'], 10) : -1;
            if (-1 === $this->_style) {
                return 'Invalid style id.';
            }
            switch ($action) {
            case 'check':
                echo 'result:' . ($this->_contentIsInstalled() ? '1' : '0');
                break;
            case 'run':
                $this->_load();
                $dataIds = $this->_getDataIds();
                $parameters = array();
                if ($dataIds) {
                    $parameters['jform_params_dataIds'] = json_encode($dataIds);
                }
                echo 'result:' . (count($parameters) ? json_encode($parameters) : 'ok');
                break;
            }
        } else {
            $this->_load();
        }
    }

    /**
     * Method to throw errors.
     *
     * @param string $msg  Text message
     * @param int    $code Number error
     *
     * @throws Exception
     */
    public function _error($msg, $code)
    {
        throw new Exception($msg);
    }

    /**
     * Method check content installing
     *
     * @return bool
     */
    private function _contentIsInstalled()
    {
        $content = Nicepage_Data_Mappers::get('content');

        if (($ids = $this->_getDataIds(true)) !== '') {
            foreach ($ids as $id) {
                $contentList = $content->find(array('id' => $id));
                if (0 != count($contentList)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Load products to virtuemart
     *
     * @return false
     */
    private function _loadProducts() {
        if (!$this->_importProducts) {
            return false;
        }

        if (!isset($this->_data['Products']) || count($this->_data['Products']) == 0) {
            return false;
        }

        if (!VirtuemartComponent::exists()) {
            return false;
        }

        VirtuemartComponent::init();

        $defaultCategory = array(
            'id' => -1,
            'title' => 'Site Products',
            'categoryId' => null,
            'created' => '',
            'updated' => '',
        );
        $categories = isset($this->_data['ProductsCategories']) ? $this->_data['ProductsCategories'] : array();
        array_push($categories, $defaultCategory);

        foreach ($categories as $id => $category) {
            $categoryData = array(
                'id' => $category['id'],
                'parentId' => $this->_checkAndGetProperty($category, 'categoryId', 0),
                'title' => $category['title'],
            );

            $categoryList = CategoryMapper::find(array('title' => $category['title']));
            if (count($categoryList) > 0) {
                $categoryListItem = $categoryList[0];
                if ($this->_replace) {
                    CategoryMapper::delete($categoryListItem->virtuemart_category_id);
                } else {
                    $categoryData['joomla_parent_id'] = $categoryListItem->category_parent_id;
                    $categoryData['joomla_id'] = $categoryListItem->virtuemart_category_id;
                }
            }
            $this->_productCategoriesItems[$categoryData['id']] = $categoryData;
        }

        foreach ($this->_data['Products'] as &$productData) {
            $imagesData = $this->_checkAndGetProperty($productData, 'images', array());
            $categoriesData = $this->_checkAndGetProperty($productData, 'categories', array());

            $joomlaCategoriesData = $this->getJoomlaCategoryIds($categoriesData);

            $product = array(
                'product_parent_id' => '0',
                'published' => '1',
                'slug' => '',
                'categories' => $joomlaCategoriesData,
                'product_name' => $this->_checkAndGetProperty($productData, 'title', 'Product'),
                'product_s_desc' => $this->_checkAndGetProperty($productData, 'description'),
                'product_sku' => $this->_checkAndGetProperty($productData, 'sku'),
                'mprices' => array(
                    'product_price' => array($this->_checkAndGetProperty($productData, 'price')),
                    'virtuemart_product_price_id' => array('0'),
                    'product_currency' => array('144'), // currency need to do
                    'virtuemart_shoppergroup_id' => array(''),
                    'basePrice' => array('0'),
                    'salesPrice' => array(''),
                    'product_price_publish_up' => array('0'),
                    'product_price_publish_down' => array('0'),
                    'product_override_price' => array(''),
                    'product_tax_id' => array(''),
                    'product_discount_id' => array(''),
                    'price_quantity_start' => array(''),
                    'price_quantity_end' => array(''),

                ),
                'has_medias' => count($imagesData) > 0 ? 1 : 0,
            );
            $product_id = ProductMapper::create($product);
            ProductMapper::updateDate($product_id);
            $productData['joomla_id'] = $product_id;

            if (count($imagesData) > 0) {
                $mediaIds = array();
                foreach ($imagesData as $imageData) {
                    $image = $this->_processingContent($imageData['url'], 'relative');
                    $pathinfo = pathinfo($image);
                    $baseName = $pathinfo['basename'];
                    $fileName = $pathinfo['filename'];
                    $fileExt = $pathinfo['extension'];
                    $newFileName = md5(microtime()) . '_' . substr($fileName, 0, 70) . '.' . $fileExt;
                    $this->_copyProductImage($baseName, $newFileName);
                    $data = array(
                        'file_title' => $newFileName,
                        'file_url' => 'images/virtuemart/product/' . $newFileName,
                        'file_name' => $newFileName,
                        'file_mimetype' => 'image/' . $fileExt,
                    );
                    $media_id = MediaMapper::addMedia($data);
                    array_push($mediaIds, $media_id);
                    MediaMapper::addProductMedia($product_id, $mediaIds);
                }
            }
        }
    }

    /**
     * Get generated joomla ids
     *
     * @param array $data Categories data
     *
     * @return array
     */
    public function getJoomlaCategoryIds($data)
    {
        if (count($data) === 0) {
            $data = array(-1);
        }

        $joomlaIds = array();
        foreach ($data as $catId) {
            $needCreateCategoryId = '';
            if (isset($this->_productCategoriesItems[$catId])) {
                $item = $this->_productCategoriesItems[$catId];
                if (isset($item['joomla_id'])) {
                    array_push($joomlaIds, $item['joomla_id']);
                    break;
                } else {
                    $needCreateCategoryId = $catId;
                }
            }
            if ($needCreateCategoryId) {
                array_push($joomlaIds, $this->createCategory($needCreateCategoryId));
            }
        }
        return $joomlaIds;
    }

    /**
     * Create category and all his parents
     *
     * @param int $catId Category id
     *
     * @return mixed
     */
    public function createCategory($catId)
    {
        $currentItem = $this->_productCategoriesItems[$catId];
        $parentId = $currentItem['parentId'];
        $needCreateCategories = array($currentItem);

        while ($parentId) {
            $c = $this->_productCategoriesItems[$parentId];
            if (!isset($c['joomla_id'])) {
                array_push($needCreateCategories, $c);
            }
            $parentId = $c['parentId'];
        }

        $needCreateCategories = array_reverse($needCreateCategories);

        foreach ($needCreateCategories as $category) {
            $pId = $category['parentId'];
            $cid = $category['id'];
            if ($pId) {
                $pId = $this->_productCategoriesItems[$pId]['joomla_id'];
            }
            $data = array(
                'category_name' => $category['title'],
                'published' => '1',
                'category_parent_id' => $pId,
                'virtuemart_category_id' => '',
                'virtuemart_vendor_id' => 1,
            );
            $this->_productCategoriesItems[$cid]['joomla_id'] = CategoryMapper::create($data);
            $this->_productCategoriesItems[$cid]['joomla_parent_id'] = $pId;
        }
        return $this->_productCategoriesItems[$catId]['joomla_id'];
    }

    /**
     * Installing sample data.
     */
    private function _load()
    {
        $this->_loadProducts();
        $this->_loadPosts();
        $this->_loadPages();
        $this->_saveDataIds();
        if ($this->_template) {
            if ($this->_importMenu) {
                $this->_loadMenus();
                $this->_loadModules();
                $this->_configureModulesVisibility();
            }
            $this->_configureEditor();
        }
        $this->_updatePages();
        $this->_loadParameters();
        $this->_copyImages();
        $this->_copyFonts();
    }

    /**
     * Import client mode option
     */
    public function importClientLicenseMode()
    {
        if (!isset($this->_data['Parameters'])) {
            return;
        }

        $parameters = $this->_data['Parameters'];
        if (!isset($parameters['nicepageSiteSettings'])) {
            return;
        }

        $siteSettings = json_decode($parameters['nicepageSiteSettings'], true);
        if (empty($siteSettings)) {
            return;
        }

        $cliendMode = isset($siteSettings['clientMode']) ? $siteSettings['clientMode'] : false;

        $config = NicepageHelpersNicepage::getConfig();

        if (isset($config['siteSettings'])) {
            $newSiteSettings = json_decode($config['siteSettings'], true);
            $newSiteSettings['clientMode'] = $cliendMode;
            NicepageHelpersNicepage::saveConfig(array('siteSettings' => json_encode($newSiteSettings)));
        }
    }

    /**
     * Load Parameters
     */
    private function _loadParameters()
    {
        if (!isset($this->_data['Parameters'])) {
            return;
        }

        $parameters = $this->_data['Parameters'];
        $config = NicepageHelpersNicepage::getConfig();

        $colorHelper = null;
        if (isset($parameters['publishNicePageCss'])) {
            $colorHelper = new ColorHelper($parameters['publishNicePageCss']);
        }

        $new = array();
        if (($this->_updatePluginSettings || !isset($config['siteSettings'])) && $parameters['nicepageSiteSettings']) {
            $new['siteSettings'] = $this->_processingContent($parameters['nicepageSiteSettings'], 'editor');
        }
        if (($this->_updatePluginSettings || !isset($config['publishDialogs'])) && isset($parameters['publishDialogs'])) {
            $new['publishDialogs'] = $this->_processingContent($parameters['publishDialogs'], 'publish');
        }

        if (($this->_updatePluginSettings || !isset($config['cookiesConsent'])) && isset($parameters['cookiesConsent'])) {
            $new['cookiesConsent'] = $this->_processingContent($parameters['cookiesConsent'], 'publish');
        }

        if (isset($parameters['productsJson'])) {
            $productsJson = $this->_processingContent($parameters['productsJson'], 'editor');
            if ($this->_replace || !isset($config['productsJson'])) {
                $new['productsJson'] = $productsJson;
            } else {
                $newJsonData = json_decode($productsJson, true);
                $newProducts = $newJsonData['products'];
                $newCategories = isset($newJsonData['categories']) ? $newJsonData['categories'] : array();

                if (is_array($config['productsJson'])) {
                    $currentProducts = $config['productsJson'];
                    $currentCategories = array();// old variant compatibility
                } else {
                    $jsonData = json_decode($config['productsJson'], true);
                    $currentProducts = $jsonData['products'];
                    $currentCategories = $jsonData['categories'];
                }

                $oldNewIds = $this->_newProductIds;
                foreach ($newProducts as $product) {
                    if (!array_key_exists($product['id'], $oldNewIds)) {
                        continue;
                    }
                    $product['id'] = $oldNewIds[$product['id']];
                    array_push($currentProducts, $product);
                }

                $newJsonData['products'] = $currentProducts;
                $newJsonData['categories'] = array_merge($currentCategories, $newCategories);
                $new['productsJson'] = json_encode($newJsonData);
            }
        }

        $templateNames = array('header', 'footer', 'password');
        $htmlListForBuildCss = array('headerFooter' => '');
        foreach ($templateNames as $templateName) {
            $publishHtmlTemplate = '';
            if (($this->_updatePluginSettings || !$config[$templateName]) && isset($parameters[$templateName])) {
                $template = $parameters[$templateName];
                $template['html'] = $this->_processingContent($template['html'], 'editor');
                $new[$templateName] = $this->_processingContent(json_encode($template), 'publish');
                if (isset($parameters[$templateName . 'Translations'])) {
                    $json = json_encode($parameters[$templateName .'Translations']);
                    $new[$templateName . 'Translations'] = $this->_processingContent($json, 'publish');
                }
                $publishHtmlTemplate .= $template['html'] . (isset($template['dialogs']) ? $template['dialogs'] : '');
            }
            if ($templateName === 'header' || $templateName === 'footer') {
                $htmlListForBuildCss['headerFooter'] .= $publishHtmlTemplate;
            } else {
                $htmlListForBuildCss[$templateName] = $publishHtmlTemplate;
            }
        }

        foreach ($htmlListForBuildCss as $name => $htmlForBuildCss) {
            if ($htmlForBuildCss && $colorHelper) {
                $new[$name . 'CssUsedIds'] = $colorHelper->getUsedColors($htmlForBuildCss);
            }
        }

        if (($this->_updatePluginSettings || !isset($config['product'])) && isset($parameters['product'])) {
            $properties = $this->_updateProperties($parameters['product']);
            $new['product'] = call_user_func('base' . '64_encode', serialize($properties));
        }

        if (($this->_updatePluginSettings || !isset($config['products'])) && isset($parameters['products'])) {
            $properties = $this->_updateProperties($parameters['products']);
            $new['products'] = call_user_func('base' . '64_encode', serialize($properties));;
        }

        $publishBackToTop = '';
        if (($this->_updatePluginSettings || !isset($config['backToTop'])) && isset($parameters['backToTop'])) {
            $new['backToTop'] = $this->_processingContent($parameters['backToTop'], 'publish');
            $publishBackToTop = $new['backToTop'];
        }

        if ($publishBackToTop && $colorHelper) {
            $new['backToTopCssUsedIds'] = $colorHelper->getUsedColors($publishBackToTop);
        }

        if (($this->_updatePluginSettings || !isset($config['siteStyleCssParts'])) && $colorHelper) {
            $siteStyleCssParts = $colorHelper->getAllColors();
            $new['siteStyleCssParts'] = $this->_processingContent($siteStyleCssParts);
        }

        if (count($new) > 0) {
            NicepageHelpersNicepage::saveConfig($new);
        }
    }

    /**
     * Get joomla lang code
     *
     * @param string $lang Language
     *
     * @return string
     */
    private function _getLangCode($lang)
    {
        $existingLanguages = LanguageHelper::getContentLanguages(array(0, 1));
        foreach ($existingLanguages as $langCode => $language) {
            if ($language->sef === $lang) {
                return $language->lang_code;
            }
        }
        return '*';
    }

    /**
     * Enable language filter plugin
     */
    private function _enableLanguageFilter()
    {
        $multilang = Multilanguage::isEnabled();
        if (!$multilang) {
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->update('#__extensions');
            $query->set('enabled = 1');
            $query->where($db->quoteName('element') . ' = ' . $db->quote('languagefilter'));
            $db->setQuery($query);
            $db->execute();
        }
    }

    /**
     * Load menus from content data
     */
    private function _loadMenus()
    {
        if (!isset($this->_data['Menus'])) {
            return;
        }

        $parameters = isset($this->_data['Parameters']) ? $this->_data['Parameters'] : null;

        $menuHomePageId = $parameters && isset($parameters['menuHomePageId']) ? $parameters['menuHomePageId'] : '';

        //$defaultLang = $parameters && isset($parameters['defaultLang']) ? $parameters['defaultLang'] : '';
        $siteLangs = $parameters && isset($parameters['siteLangs']) ? $parameters['siteLangs'] : array();
        $isMultiLangMenu = count($siteLangs) > 1 ? true : false;
        if ($isMultiLangMenu) {
            $this->_enableLanguageFilter();
            //$this->_installNonExistsLanguages($siteLangs);
        }

        //$installedLangs = LanguageHelper::getInstalledLanguages(0);
        //$lang_codes   = LanguageHelper::getLanguages('lang_code');
        //$cmsDefaultLang = ComponentHelper::getParams('com_languages')->get('site', 'en-GB');

        if (count($this->_data['Menus']) > 0) {
            $menusMapper = Nicepage_Data_Mappers::get('menu');
            $menuItemsMapper = Nicepage_Data_Mappers::get('menuItem');

            $home = $menuItemsMapper->find(array('home' => 1));
            $homeItem = count($home) > 0 ? $home[0] : null;
            $defaultMenuDataFound = false;
            foreach ($this->_data['Menus'] as $menuData) {
                foreach ($menuData['items'] as $key => $itemData) {
                    if (isset($itemData['default']) && $itemData['default']) {
                        $defaultMenuDataFound = true;
                    }
                }
            }
            // Create a temporary menu with one item to clean up the Home flag:
            $rndMenu = null;
            if ($homeItem && $defaultMenuDataFound) {
                $rndMenu = $menusMapper->create();
                $rndMenu->title = $rndMenu->menutype = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 10);
                $status = $menusMapper->save($rndMenu);
                if (is_string($status)) {
                    trigger_error($status, E_USER_ERROR);
                }
                $rndItem = $menuItemsMapper->create();
                $rndItem->home = '1';
                $rndItem->checked_out = $homeItem->checked_out;
                $rndItem->menutype = $rndMenu->menutype;
                $rndItem->alias = $rndItem->title = $rndMenu->menutype;
                $rndItem->link = 'index.php?option=com_content&view=article&id=';
                $rndItem->type = 'component';
                $rndItem->component_id = '19';
                $rndItem->params = $this->_paramsToString(array());
                $status = $menuItemsMapper->save($rndItem);
                if (is_string($status)) {
                    trigger_error($status, E_USER_ERROR);
                }
            }


            foreach ($this->_data['Menus'] as  $index => $menuData) {
                if ($index == 'default') {
                    continue;
                }
                $menuList = $menusMapper->find(array('title' => $menuData['caption']));
                foreach ($menuList as $menuListItem) {
                    $status = $menusMapper->delete($menuListItem->id);
                    if (is_string($status)) {
                        trigger_error($status, E_USER_ERROR);
                    }
                }
            }

            $blogPostsCount = '4';
            if ($parameters && isset($parameters['blogPostsCount'])) {
                $blogPostsCount = $parameters['blogPostsCount'];
            }

            $foundHomeItem = false;
            foreach ($this->_data['Menus'] as $index => $menuData) {
                $lang = isset($menuData['lang']) && $isMultiLangMenu ? $this->_getLangCode($menuData['lang']) : '*';
                if ($index == 'default') {
                    continue;
                }
                if ($foundHomeItem && $index == 'home') {
                    continue;
                }
                $menu = $menusMapper->create();
                $menu->title = $menuData['caption'];
                $menu->menutype = $menuData['name'];
                $status = $menusMapper->save($menu);
                if (is_string($status)) {
                    trigger_error($status, E_USER_ERROR);
                }
                $foundMultiLangHomeMenu = false;
                foreach ($menuData['items'] as $key => $itemData) {
                    $item = $menuItemsMapper->create();
                    $item->language = $lang;
                    $item->menutype = $menu->menutype;
                    $item->title = $itemData['caption'];
                    $item->alias = $itemData['name'];
                    if ($isMultiLangMenu && $lang === '*') {
                        $item->alias .= '-m' . $index . '-i' . $key;
                    }
                    $href = $this->_getPropertyValue('href', $itemData, '');
                    $type = $this->_getPropertyValue('type', $itemData, '');

                    $postId = '';
                    $contentPageId = '';
                    $pageData = null;
                    if (preg_match('/\[(page|post)_(\d+)\]/', $href, $matches)) {
                        $type = $matches[1];
                        $pid = $matches[2];
                        if ($type === 'post') {
                            $pages = $this->_data['Posts'];
                        } else {
                            $pages = $this->_data['Pages'];
                        }

                        $hrefParts = explode('#', $href);
                        $pageData = isset($pages[$pid]) ? $pages[$pid] : array();
                        if (isset($pageData['joomla_id'])) {
                            $contentPageId = $matches[2];
                            $type = 'single-article';
                            $postId = $pageData['joomla_id'];
                            if (count($hrefParts) > 1) {
                                $postId .= '#' . $hrefParts[1];
                            }
                        } else {
                            $href = '#';
                            $type = 'custom';
                        }
                    }

                    if (substr($href, 0, 1) === '#') {
                        $hrefPart = substr($href, 1);
                        $images = isset($this->_data['Images']) ? $this->_data['Images'] : array();
                        foreach ($images as $image) {
                            if ($image['fileName'] === $hrefPart) {
                                $href = 'images/nicepage-images/' . $hrefPart;
                            }
                        }
                    }

                    if (preg_match('/\[blog_(\d+)\]/', $href, $matches)) {
                        $type = 'category-blog-layout';
                        $categoryId = $this->_blogCategoryId ?: $this->_getDefaultCategory();
                    }

                    $db = Factory::getDBO();
                    $query = $db->getQuery(true);
                    $query->select('template')->from('#__template_styles')->where('id=' . $this->_style);
                    $db->setQuery($query);
                    $themeName = $db->loadResult();

                    if (preg_match('/\[products_(\d+)\]/', $href, $matches)) {
                        $type = 'custom';
                        $href = 'index.php?option=com_ajax&format=html&template=' . $themeName . '&method=products&product_name=product-list';
                    }

                    if (preg_match('/(product-\d+)/', $href, $matches)) {
                        $type = 'custom';
                        $href = 'index.php?option=com_ajax&format=html&template=' . $themeName . '&method=product&product_name=' . $matches[0];
                    }

                    if (!$postId && !$type) {
                        $type = 'custom';
                    }

                    if ($isMultiLangMenu && $itemData['home']) {
                        if ($lang !== '*' && !$foundMultiLangHomeMenu) {
                            $item->home = '1';
                            $foundMultiLangHomeMenu = true;
                        }
                        if ($lang === '*' && !$foundHomeItem) {
                            $item->home = '1';
                            $foundHomeItem = true;
                        }
                    }

                    if (!$isMultiLangMenu && !$foundHomeItem) {
                        if ($menuHomePageId && strpos($href, (string)$menuHomePageId) !== false) {
                            $item->home = '1';
                            $foundHomeItem = true;
                        }

                        if (!$menuHomePageId && $postId) {
                            $item->home = '1';
                            $foundHomeItem = true;
                        }
                    }


                    switch ($type) {
                    case 'single-article':
                        $item->link = 'index.php?option=com_content&view=article&id=' . $postId;
                        $item->type = 'component';
                        $item->component_id = '19';
                        $params = array
                        (
                            'show_title' => '1',
                            'link_titles' => '',
                            'show_intro' => '0',
                            'show_category' => '0',
                            'link_category' => '',
                            'show_parent_category' => '0',
                            'link_parent_category' => '',
                            'show_author' => '0',
                            'link_author' => '',
                            'show_create_date' => '0',
                            'show_modify_date' => '0',
                            'show_publish_date' => '0',
                            'show_item_navigation' => '0',
                            'show_vote' => '0',
                            'show_icons' => '0',
                            'show_print_icon' => '0',
                            'show_email_icon' => '0',
                            'show_hits' => '0',
                            'show_noauth' => '',
                            'menu-anchor_title' => '',
                            'menu-anchor_css' => '',
                            'menu_image' => '',
                            'menu_text' => '1',
                            'page_title' => '',
                            'show_page_heading' => '0',
                            'page_heading' => '',
                            'pageclass_sfx' => '',
                            'menu-meta_description' => $pageData && isset($pageData['description']) ? $pageData['description'] : '',
                            'menu-meta_keywords' => $pageData && isset($pageData['keywords']) ? $pageData['keywords'] : '',
                            'robots' => '',
                            'secure' => '0',
                            'page_title' => $pageData && isset($pageData['titleInBrowser']) ? $pageData['titleInBrowser'] : ''
                        );
                        break;
                    case 'category-blog-layout':
                        $item->link = 'index.php?option=com_content&view=category&layout=blog&id=' . $categoryId;
                        $item->type = 'component';
                        $item->component_id = '19';
                        $params = array
                        (
                            'layout_type' => 'blog',
                            'show_category_title' => '',
                            'show_description' => '',
                            'show_description_image' => '',
                            'maxLevel' => '',
                            'show_empty_categories' => '',
                            'show_no_articles' => '',
                            'show_subcat_desc' => '',
                            'show_cat_num_articles' => '',
                            'page_subheading' => '',
                            'num_leading_articles' => '0',
                            'num_intro_articles' => $blogPostsCount,
                            'num_columns' => '1',
                            'num_links' => '',
                            'multi_column_order' => '',
                            'show_subcategory_content' => '',
                            'orderby_pri' => '',
                            'orderby_sec' => 'rdate',
                            'order_date' => '',
                            'show_pagination' => '',
                            'show_pagination_results' => '',
                            'show_title' => '',
                            'link_titles' => '',
                            'show_intro' => '',
                            'show_category' => '',
                            'link_category' => '',
                            'show_parent_category' => '',
                            'link_parent_category' => '',
                            'show_author' => '',
                            'link_author' => '',
                            'show_create_date' => '',
                            'show_modify_date' => '',
                            'show_publish_date' => '',
                            'show_item_navigation' => '',
                            'show_vote' => '',
                            'show_readmore' => '',
                            'show_readmore_title' => '',
                            'show_icons' => '',
                            'show_print_icon' => '',
                            'show_email_icon' => '',
                            'show_hits' => '',
                            'show_noauth' => '',
                            'show_feed_link' => '',
                            'feed_summary' => '',
                            'menu-anchor_title' => '',
                            'menu-anchor_css' => '',
                            'menu_image' => '',
                            'menu_text' => 1,
                            'page_title' => '',
                            'show_page_heading' => 0,
                            'page_heading' => '',
                            'pageclass_sfx' => '',
                            'menu-meta_description' => '',
                            'menu-meta_keywords' => '',
                            'robots' => '',
                            'secure' => 0,
                            'page_title' => ''
                        );
                        break;
                    default:
                        $item->link = $href;
                        $item->type = 'url';
                        $item->component_id = '0';
                        $params = array
                        (
                            'menu-anchor_title' => '',
                            'menu-anchor_css' => '',
                            'menu_image' => '',
                            'menu_text' => 1
                        );
                    }

                    // parameters:
                    $item->params = $this->_paramsToString($params);

                    // parent:
                    if (isset($itemData['parent'])) {
                        $item->setLocation($this->_data['Menus'][$index]['items'][$itemData['parent']]['joomla_id'], 'last-child');
                    }

                    $status = $menuItemsMapper->save($item);
                    if (is_string($status)) {
                        trigger_error($status, E_USER_ERROR);
                    }

                    $this->_data['Menus'][$index]['items'][$key]['joomla_id'] = $item->id;
                    if ($contentPageId && $type == 'single-article') {
                        $this->_data['Pages'][$contentPageId]['joomla_menu_id'] = $item->id;
                    }
                }
            }
            if (!$foundHomeItem && $homeItem && $rndItem) {
                $homeItem->checked_out = $rndItem->checked_out;
                $homeItem->home = '1';
                $status = $menuItemsMapper->save($homeItem);
                if (is_string($status)) {
                    trigger_error($status, E_USER_ERROR);
                }
            }
            if ($rndMenu) {
                $status = $menusMapper->delete($rndMenu->id);
                if (is_string($status)) {
                    trigger_error($status, E_USER_ERROR);
                }
            }
        }
    }

    /**
     * Create modules from import data
     */
    private function _loadModules() {
        if (!isset($this->_data['Modules'])) {
            return;
        }

        $modulesMapper = Nicepage_Data_Mappers::get('module');

        foreach ($this->_data['Modules'] as $moduleData) {
            $modulesList = $modulesMapper->find(array('title' => $moduleData['title']));
            foreach ($modulesList as $modulesListItem) {
                $status = $modulesMapper->delete($modulesListItem->id);
            }
        }

        $order = array();

        foreach ($this->_data['Modules'] as $key => $moduleData) {
            $module = $modulesMapper->create();
            $module->title = $moduleData['title'];
            $module->position = $moduleData['position'];
            $style = isset($moduleData['style']) ? $moduleData['style'] : '';
            $params = array();

            if ($moduleData['type'] == 'cart' && !file_exists(JPATH_ROOT .'/modules/mod_virtuemart_cart/tmpl/default.php')) {
                continue;
            }

            switch ($moduleData['type']) {
            case 'menu':
                $module->module = 'mod_menu';
                $params = array
                (
                    'menutype' => $moduleData['menu'],
                    'startLevel' => '1',
                    'endLevel' => '0',
                    'showAllChildren' => '1',
                    'tag_id' => '',
                    'class_sfx' => '',
                    'window_open' => '',
                    'layout' => '_:default',
                    'moduleclass_sfx' => $style,
                    'cache' => '1',
                    'cache_time' => '900',
                    'cachemode' => 'itemid'
                );
                break;
            case 'breadcrumbs':
                $module->module = 'mod_breadcrumbs';
                $params = array
                (
                    'showHere' => '1',
                    'showHome' => '1',
                    'homeText' => '',
                    'showLast' => '1',
                    'separator' => '1',
                    'layout' => '_:default',
                    'moduleclass_sfx' => '',
                    'cache' => '0',
                    'cache_time' => '0',
                    'cachemode' => 'itemid'
                );
                break;
            case 'cart':
                $module->module = 'mod_virtuemart_cart';
                $params = array
                (
                    'moduleid_sfx' => '',
                    'moduleclass_sfx' => '',
                    'show_price' => '1',
                    'show_product_list' => '1',
                    'separator' => '1',
                    'layout' => '_:default',
                    'module_tag' => 'div',
                    'bootstrap_size' => '0',
                    'header_tag' => 'h3',
                    'header_class' => '',
                    'style' => "0"
                );
                break;
            case 'custom':
                $module->module = 'mod_custom';
                $module->content = $this->_processingContent($moduleData['content']);
                $params = array
                (
                    'prepare_content' => '1',
                    'layout' => '_:default',
                    'moduleclass_sfx' => '',
                    'cache' => '1',
                    'cache_time' => '900',
                    'cachemode' => 'static'
                );
                break;
            }

            if (!$module->content) {
                $module->content = '';
            }

            $module->showtitle = 'true' == $moduleData['showTitle'] ? '1' : '0';
            // style:
            if (isset($moduleData['style']) && isset($params['moduleclass_sfx'])) {
                $params['moduleclass_sfx'] = $moduleData['style'];
            }
            // parameters:
            $module->params = $this->_paramsToString($params);

            // ordering:
            if (!isset($order[$moduleData['position']])) {
                $order[$moduleData['position']] = 1;
            }
            $module->ordering = $order[$moduleData['position']];
            $order[$moduleData['position']]++;

            $status = $modulesMapper->save($module);
            if (is_string($status)) {
                trigger_error($status, E_USER_ERROR);
            }
            $this->_data['Modules'][$key]['joomla_id'] = $module->id;
        }
    }

    /**
     * To configure visibility of modules
     */
    private function _configureModulesVisibility()
    {
        if (!isset($this->_data['Modules'])) {
            return;
        }
        if (!isset($this->_data['Modules'])) {
            return;
        }

        $contentMenuItems = array();

        foreach ($this->_data['Menus'] as $index => $menuData) {
            if ($index == 'default') {
                continue;
            }
            foreach ($menuData['items'] as $itemData) {
                if (isset($itemData['joomla_id'])) {
                    $contentMenuItems[] = $itemData['joomla_id'];
                }
            }
        }

        $contentModules = array();
        foreach ($this->_data['Modules'] as $widgetData) {
            $contentModules[] = $widgetData['joomla_id'];
        }

        $modules = Nicepage_Data_Mappers::get('module');
        $menuItems = Nicepage_Data_Mappers::get('menuItem');

        $userMenuItems = array();
        $menuItemList = $menuItems->find(array('scope' => 'site'));
        foreach ($menuItemList as $menuItem) {
            if (in_array($menuItem->id, $contentMenuItems)) {
                continue;
            }
            $userMenuItems[] = $menuItem->id;
        }

        $moduleList = $modules->find(array('scope' => 'site'));
        foreach ($moduleList as $moduleListItem) {
            if (in_array($moduleListItem->id, $contentModules)) {
                $modules->enableOn($moduleListItem->id, $contentMenuItems);
            } else {
                $pages = $modules->getAssignment($moduleListItem->id);
                if (1 == count($pages) && '0' == $pages[0]) {
                    $modules->disableOn($moduleListItem->id, $contentMenuItems);
                }
                if (0 < count($pages) && 0 > $pages[0]) {
                    $disableOnPages = array_unique(array_merge(array_map('abs', $pages), $contentMenuItems));
                    $modules->disableOn($moduleListItem->id, $disableOnPages);
                }
            }
        }
    }

    /**
     * Get value from json by property name
     *
     * @param string $property Property name
     * @param array  $a        Data
     * @param string $default  Default value if not exists
     *
     * @return mixed|string
     */
    private function _getPropertyValue($property, $a = array(), $default = '')
    {
        if (array_key_exists($property, $a)) {
            return $a[$property];
        }
        return $default;
    }

    /**
     * Delete previous content
     *
     * @param array $ids Article ids
     */
    private function _deletePreviousContent($ids)
    {
        $content = Nicepage_Data_Mappers::get('content');

        foreach ($ids as $id) {
            $contentList = $content->find(array('id' => $id));
            if (0 != count($contentList)) {
                $content->delete($contentList[0]->id);
                // delete sections
                $db = Factory::getDBO();
                $query = $db->getQuery(true);
                $query->delete('#__' . $this->_dbName . '_sections')
                    ->where($db->qn('page_id') . ' = ' . $db->q($contentList[0]->id));
                $db->setQuery($query);
                try {
                    $db->execute();
                }
                catch (Exception $exc) {
                    // Nothing
                }
            }
        }
    }

    /**
     * Method to save sample data ids
     */
    private function _saveDataIds()
    {
        if (count($this->_dataIds) < 1) {
            return;
        }

        $parameters = $this->_getExtOptions();
        $parameters['dataIds'] = json_encode($this->_dataIds);
        $this->_setExtOptions($parameters);
    }

    /**
     * Method to get sample data ids
     *
     * @param bool $force Get data ids
     *
     * @return array|string
     */
    private function _getDataIds($force = false)
    {
        if (!$this->_replace && !$force) {
            return array();
        }

        $parameters = $this->_getExtOptions();
        if (isset($parameters['dataIds']) && $parameters['dataIds']) {
            $dataIds = json_decode($parameters['dataIds'], true);
            if (!$dataIds) {
                $dataIds = explode(',', $parameters['dataIds']);
            }
            return $dataIds;
        } else {
            return array();
        }
    }

    /**
     * Method to get or create default category id
     *
     * @throws Exception
     */
    private function _getDefaultCategory()
    {
        $categories = Nicepage_Data_Mappers::get('category');

        $categoryList = $categories->find(array('title' => 'Uncategorised', 'extension' => 'com_content'));
        foreach ($categoryList as & $categoryListItem) {
            return  $categoryListItem->id;
        }

        $category = $categories->create();
        $category->title = 'Uncategorised';
        $category->extension = 'com_content';
        $category->metadata = $this->_paramsToString(array('robots' => '', 'author' => '', 'tags' => ''));
        $status = $categories->save($category);
        if (is_string($status)) {
            return $this->_error($status, 1);
        }
        return $category->id;
    }

    /**
     * Method load posts to cms
     *
     * @throws Exception
     */
    private function _loadPosts()
    {
        if (!isset($this->_data['Posts']) || count($this->_data['Posts']) == 0) {
            return;
        }

        $categories = Nicepage_Data_Mappers::get('category');
        $content = Nicepage_Data_Mappers::get('content');

        $categoryList = $categories->find(array('title' => 'Articles'));

        if ($this->_replace) {
            foreach ($categoryList as & $categoryListItem) {
                $categories->delete($categoryListItem->id);
            }
        }

        if ($this->_replace || count($categoryList) == 0) {
            $category = $categories->create();
            $category->title = 'Articles';
            $category->extension = 'com_content';
            $category->metadata = $this->_paramsToString(array('robots' => '', 'author' => '', 'tags' => ''));
            $status = $categories->save($category);
            if (is_string($status)) {
                return $this->_error($status, 1);
            }
            $this->_blogCategoryId = $category->id;
        } else {
            $this->_blogCategoryId = $categoryList[0]->id;
        }

        $key = 0;
        $date = Factory::getDate();
        $contentPostIds = array_keys($this->_data['Posts']);
        foreach ($this->_data['Posts'] as $alias => & $articleData) {
            $postKey = array_shift($contentPostIds);
            $contentPostId = 'post_' . $this->_data['Posts'][$postKey]['id'];
            $titleData = isset($articleData['title']) && $articleData['title'] ? $articleData['title'] : 'Post';
            $key++;
            $article = $content->create();
            $article->catid = $this->_blogCategoryId;
            list($title, $alias) = $this->_generateNewTitle($this->_blogCategoryId, $titleData, $alias, $key);
            $article->title = $title;
            $article->alias = $alias;

            $excerpt = $this->_processingContent(isset($articleData['excerpt']) ? $articleData['excerpt'] : '', 'relative');
            $html = $this->_processingContent(isset($articleData['html']) ? $articleData['html'] : '', 'relative');
            $article->introtext = $excerpt;
            $article->fulltext = $html;

            $image = $this->_processingContent(isset($articleData['featured']) ? $articleData['featured'] : '', 'relative');
            $images = array(
                'image_intro' => $image,
                'float_intro' => '',
                'image_intro_alt' => '',
                'image_intro_caption' => '',
                'image_fulltext' => $image,
                'float_fulltext' => '',
                'image_fulltext_alt' => '',
                'image_fulltext_caption' => '',
            );
            $article->images = json_encode($images);

            $article->attribs = $this->_paramsToString(
                array (
                    'show_title' => '',
                    'link_titles' => '',
                    'show_intro' => '0',
                    'show_category' => '',
                    'link_category' => '',
                    'show_parent_category' => '',
                    'link_parent_category' => '',
                    'show_author' => '',
                    'link_author' => '',
                    'show_create_date' => '',
                    'show_modify_date' => '',
                    'show_publish_date' => '',
                    'show_item_navigation' => '',
                    'show_icons' => '',
                    'show_print_icon' => '',
                    'show_email_icon' => '',
                    'show_vote' => '',
                    'show_hits' => '',
                    'show_noauth' => '',
                    'alternative_readmore' => '',
                    'article_layout' => ''
                )
            );
            $article->metadata = $this->_paramsToString(array('robots' => '', 'author' => '', 'rights' => '', 'xreference' => '', 'tags' => ''));
            $article->metakey = ''; //support postgresql
            $article->metadesc = ''; //support postgresql
            $status = $content->save($article);
            if (is_string($status)) {
                return $this->_error($status, 1);
            }

            // Update modified date
            $date->modify('-1 second');
            $strDate = $date->toSql();
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->update('#__content');
            $query->set($db->quoteName('modified') . '=' . $db->quote($strDate));
            $query->set($db->quoteName('created') . '=' . $db->quote($strDate));
            $query->set($db->quoteName('publish_up') . '=' . $db->quote($strDate));
            $query->where('id=' . $article->id);
            $db->setQuery($query);
            $db->execute();

            $articleData['joomla_id'] = $article->id;
            $this->_dataIds[$contentPostId] = $article->id;
        }
    }

    /**
     * Method load sample pages to cms
     *
     * @throws Exception
     */
    private function _loadPages()
    {
        $content = Nicepage_Data_Mappers::get('content');
        $defaultCategoryId = $this->_getDefaultCategory();
        $key = 0;
        $contentPageIds = array_keys($this->_data['Pages']);

        $oldIds = $this->_getDataIds();
        foreach ($this->_data['Pages'] as & $articleData) {
            $contentPageId = array_shift($contentPageIds);
            $key++;

            $article = null;
            if ($this->_replace && isset($oldIds[$contentPageId])) {
                $contentList = $content->find(array('id' => $oldIds[$contentPageId]));
                if (count($contentList) > 0) {
                    $article = $contentList[0];
                }
            }
            if (!$article) {
                $article = $content->create();
            }

            $article->catid = $defaultCategoryId;
            list($title, $alias) = $this->_generateNewTitle($defaultCategoryId, $articleData['caption'], '', $key);
            $article->title = $title;
            $article->alias = $alias;
            $article->introtext = isset($articleData['introHtml']) ? $articleData['introHtml'] : '';
            $article->attribs = $this->_paramsToString(
                array (
                    'show_title' => '',
                    'link_titles' => '',
                    'show_intro' => '0',
                    'show_category' => '',
                    'link_category' => '',
                    'show_parent_category' => '',
                    'link_parent_category' => '',
                    'show_author' => '',
                    'link_author' => '',
                    'show_create_date' => '',
                    'show_modify_date' => '',
                    'show_publish_date' => '',
                    'show_item_navigation' => '',
                    'show_icons' => '',
                    'show_print_icon' => '',
                    'show_email_icon' => '',
                    'show_vote' => '',
                    'show_hits' => '',
                    'show_noauth' => '',
                    'alternative_readmore' => '',
                    'article_layout' => ''
                )
            );
            $article->metadata = $this->_paramsToString(array('robots' => '', 'author' => '', 'rights' => '', 'xreference' => '', 'tags' => ''));
            $article->metakey = ''; //support postgresql
            $article->metadesc = ''; //support postgresql
            $status = $content->save($article);
            if (is_string($status)) {
                return $this->_error($status, 1);
            }
            $articleData['joomla_id'] = $article->id;
            $this->_dataIds[$contentPageId] = $article->id;
        }
        if (!$this->_replace) {
            $this->_deletePreviousContent($oldIds);
        }
    }

    /**
     * Update page content
     *
     * @throws Exception
     */
    private function _updatePages() {
        $content = Nicepage_Data_Mappers::get('content');
        foreach ($this->_data['Pages'] as & $articleData) {
            $article = $content->fetch($articleData['joomla_id']);
            $properties = array(
                'titleInBrowser' => '',
                'keywords' => '',
                'description' => ''
            );
            if (!is_null($article)) {
                if (isset($articleData['properties'])) {
                    $properties = $this->_updateProperties($articleData, $article->id);
                    $db = Factory::getDBO();

                    // remove nice pages with article id, before adding a new nice page
                    $db->setQuery('delete from #__' . $this->_dbName . '_sections WHERE ' . $db->quoteName('page_id') . '=' . $db->quote($article->id));
                    $db->execute();

                    $query = $db->getQuery(true);
                    $query->insert('#__' . $this->_dbName . '_sections');
                    $query->columns(
                        array(
                            $db->quoteName('props'),
                            $db->quoteName('page_id'),
                            $db->quoteName('preview_props'),
                            $db->quoteName('autosave_props'),
                        )
                    );
                    $query->values(
                        $db->quote(call_user_func('base' . '64_encode', serialize($properties))) . ', ' .
                        $db->quote($article->id) . ', ' .
                        $db->quote('') . ', ' .
                        $db->quote('')
                    );
                    $db->setQuery($query);
                    $db->execute();
                }
                $article->introtext = $this->_processingContent($article->introtext, 'publish');
                $article->fulltext = '<!--np_fulltext-->' . $properties['publishHtml'] . '<!--/np_fulltext-->';
                $article->metakey = $properties['keywords'];
                $article->metadesc = $properties['description'];
                $attribs = $this->_stringToParams($article->attribs);
                $attribs['article_page_title'] = $properties['titleInBrowser'];
                $article->attribs = $this->_paramsToString($attribs);

                $status = $content->save($article);
                if (is_string($status)) {
                    return $this->_error($status, 1);
                }
            }
        }
    }

    /**
     * Update properties
     *
     * @param array  $articleData Article data
     * @param string $id          Article id
     *
     * @return mixed
     */
    private function _updateProperties($articleData, $id = '')
    {
        $config = NicepageHelpersNicepage::getConfig();
        $properties = $articleData['properties'];
        $parameters =  isset($this->_data['Parameters']) ? $this->_data['Parameters'] : array();

        $properties['dialogs'] = isset($properties['dialogs']) ? $this->_processingContent($properties['dialogs'], 'publish') : '';
        if (($this->_updatePluginSettings || !isset($config['siteStyleCssParts'])) && isset($parameters['publishNicePageCss'])) {
            $colorHelper = new ColorHelper($parameters['publishNicePageCss']);
            $pageCssUsedIds = $colorHelper->getUsedColors($properties['publishHtml'] . $properties['dialogs']);
            $properties['pageCssUsedIds'] = $pageCssUsedIds;
        }

        $properties['head'] = $this->_processingContent($properties['head'], 'publish');
        $properties['bodyStyle'] = isset($properties['bodyStyle']) ? $this->_processingContent($properties['bodyStyle'], 'publish') : '';
        $properties['bodyDataBg'] = isset($properties['bodyDataBg']) ? $this->_processingContent($properties['bodyDataBg'], 'publish') : '';
        $properties['html'] = $this->_processingContent($properties['html'], 'editor');
        $properties['publishHtml'] = $this->_processingContent($properties['publishHtml'], 'publish');

        if (isset($properties['publishHtmlTranslations'])) {
            foreach ($properties['publishHtmlTranslations'] as $lang => $html) {
                $properties['publishHtmlTranslations'][$lang] = $this->_processingContent($html, 'publish');
            }
        }

        if (isset($parameters['header']) || isset($parameters['footer'])) {
            $properties['pageView'] = 'landing';
        } else {
            $properties['pageView'] = 'landing_with_header_footer';
        }

        $properties['titleInBrowser']   = isset($articleData['titleInBrowser']) ? $articleData['titleInBrowser'] : '';
        $properties['keywords']         = isset($articleData['keywords']) ? $articleData['keywords'] : '';
        $properties['description']      = isset($articleData['description']) ? $articleData['description'] : '';
        $properties['canonical']        = isset($articleData['canonical']) ? $articleData['canonical'] : '';

        $properties['metaTags']         = isset($articleData['metaTags']) ? $articleData['metaTags'] : '';
        $properties['customHeadHtml']   = isset($articleData['customHeadHtml']) ? $articleData['customHeadHtml'] : '';
        $properties['metaGeneratorContent']   = isset($articleData['metaGeneratorContent']) ? $articleData['metaGeneratorContent'] : '';
        $properties['metaReferrer']   = isset($articleData['metaReferrer']) ? $articleData['metaReferrer'] : '';

        $properties['introImgStruct'] = $this->_processingContent($properties['introImgStruct'], 'publish');
        $properties['ogTags'] = isset($articleData['ogTags']) ? $this->_processingContent($articleData['ogTags'], 'publish') : '';

        $fontsPath = '[[site_path_live]]components/com_nicepage/assets/css/fonts/';
        if ($id) {
            $properties['fonts'] = str_replace('page-fonts.css', $fontsPath . 'page-' . $id . '-fonts.css', $properties['fonts']);
        }
        $properties['fonts'] = str_replace('"fonts.css', '"' . $fontsPath . 'fonts.css', $properties['fonts']);
        return $properties;
    }

    /**
     * Generate new title for page
     *
     * @param int    $catId Category Id
     * @param string $title Start title
     * @param string $alias Start alias
     * @param int    $key   Custom key for alias
     *
     * @return array
     */
    private function _generateNewTitle($catId, $title, $alias = '', $key = 0)
    {
        $title = $title ? strip_tags($title) : 'Post';
        $alias = $alias ? $alias : $title;
        if (Factory::getConfig()->get('unicodeslugs') == 1) {
            $alias = OutputFilter::stringURLUnicodeSlug($alias);
        } else {
            $alias = OutputFilter::stringURLSafe($alias);
        }
        $table = Table::getInstance('Content');
        while ($table->load(array('alias' => $alias, 'catid' => $catId))) {
            $alias = StringHelper::increment($alias, 'dash');
        }
        while ($table->load(array('title' => $title, 'catid' => $catId))) {
            $title = StringHelper::increment($title);
        }
        if (!$alias) {
            $date = new Date();
            $alias = $date->format('Y-m-d-H-i-s') . '-' . $key;
        }
        return array($title, $alias);
    }

    /**
     * Process link hrefs with '[page_', '[post_' placeholder
     *
     * @param array $matches Href matches
     *
     * @return string
     */
    private function _parseHref($matches)
    {
        $type = $matches[1];
        $pageId = $matches[2];

        $page = null;
        if ($type === 'post') {
            foreach ($this->_data['Posts'] as $post) {
                if ($post['id'] == $pageId) {
                    $page = $post;
                    break;
                }
            }
        }

        if ($type === 'page' && isset($this->_data['Pages'][$pageId])) {
            $page = $this->_data['Pages'][$pageId];
        }

        if (!$page) {
            return $matches[0];
        }

        if (empty($page['joomla_id'])) {
            return '#';
        }

        $content = Nicepage_Data_Mappers::get('content');
        $article = $content->fetch($page['joomla_id']);

        $menuId = isset($page['joomla_menu_id']) ? '&amp;Itemid=' . $page['joomla_menu_id'] : '';
        if (!is_null($article)) {
            return 'index.php?option=com_content&amp;view=article' .
                '&amp;id=' . $article->id . '&amp;catid=' . $article->catid . $menuId;
        }
    }

    /**
     * Method to proccess page content
     *
     * @param string $content Page sample content
     * @param string $state   Type path
     *
     * @return mixed
     */
    private function _processingContent($content, $state = 'full')
    {
        if ($content == '') {
            return $content;
        }

        $old = $this->_rootUrl;

        switch ($state) {
        case 'full':
            $this->_rootUrl .= '/';
            break;
        case 'publish':
            $this->_rootUrl = '[[site_path_live]]';
            break;
        case 'editor':
            $this->_rootUrl = '[[site_path_editor]]/';
            break;
        case 'relative':
            $this->_rootUrl = '';
            break;
        }
        $content = $this->_replacePlaceholdersForImages($content);
        $this->_rootUrl =  $old;

        $content = preg_replace_callback('/\[(page|post)_(\d+)\]/', array( &$this, '_parseHref'), $content);

        $content = preg_replace('/\[blog_(\d+)\]/', 'index.php?option=com_content&view=category&layout=blog&id=' . $this->_blogCategoryId, $content);
        return $content;
    }

    /**
     * Replace image placeholders in page content
     *
     * @param string $content Page sample content
     *
     * @return mixed
     */
    private function _replacePlaceholdersForImages($content)
    {
        //change default image
        $content = str_replace('[image_default]', $this->_rootUrl . 'components/com_nicepage/assets/images/nicepage-images/default-image.jpg', $content);
        $content = preg_replace_callback('/\[image_(\d+)\]/', array(&$this, '_replacerImages'), $content);
        return $content;
    }

    /**
     * Callback function for replacement image placeholders
     *
     * @param array $match
     *
     * @return string
     */
    private function _replacerImages($match)
    {
        $full = $match[0];
        $n = $match[1];
        if (isset($this->_data['Images'][$n])) {
            $imageName = $this->_data['Images'][$n]['fileName'];
            array_push($this->_foundImages, $imageName);
            return $this->_rootUrl . 'images/nicepage-images/' . $imageName;
        }
        return $full;
    }

    /**
     * To configure editor
     *
     * @return null|void
     * @throws Exception
     */
    private function _configureEditor()
    {
        $extensions = Nicepage_Data_Mappers::get('extension');
        $tinyMce = $extensions->findOne(array('element' => 'tinymce'));
        if (is_string($tinyMce)) {
            return $this->_error($tinyMce, 1);
        }
        if (!is_null($tinyMce)) {
            $params = $this->_stringToParams($tinyMce->params);
            $elements = isset($params['extended_elements']) && strlen($params['extended_elements']) ? explode(',', $params['extended_elements']) : array();
            $invalidElements = isset($params['invalid_elements']) && strlen($params['invalid_elements']) ? explode(',', $params['invalid_elements']) : array();
            if (in_array('script', $invalidElements)) {
                array_splice($invalidElements, array_search('script', $invalidElements), 1);
            }
            if (!in_array('style', $elements)) {
                $elements[] = 'style';
            }
            if (!in_array('script', $elements)) {
                $elements[] = 'script';
            }
            if (!in_array('div[*]', $elements)) {
                $elements[] = 'div[*]';
            }
            $params['extended_elements'] = implode(',', $elements);
            $params['invalid_elements'] = implode(',', $invalidElements);
            $tinyMce->params = $this->_paramsToString($params);
            $status = $extensions->save($tinyMce);
            if (is_string($status)) {
                return $this->_error($status, 1);
            }
        }
        return null;
    }

    /**
     * @param string $image   Product image
     * @param string $newName New name
     */
    private function _copyProductImage($image, $newName) {
        $imgDir = dirname(JPATH_BASE) . '/images';
        $vmPath = dirname(JPATH_BASE) . '/images/virtuemart';
        if (!file_exists($vmPath)) {
            mkdir($vmPath);
        }
        $vmProductPath = dirname(JPATH_BASE) . '/images/virtuemart/product';
        if (!file_exists($vmProductPath)) {
            mkdir($vmProductPath);
        }
        if (file_exists($this->_images . '/' . $image)) {
            copy($this->_images . '/' . $image, $vmProductPath . '/' . $newName);
        }
    }

    /**
     * Method to copy sample images to cms
     *
     * @param bool $onlyFound Flag
     */
    private function _copyImages($onlyFound = false)
    {
        if (!$this->_images) {
            return;
        }
        $imgDir = dirname(JPATH_BASE) . DIRECTORY_SEPARATOR . 'images';
        $contentDir = $imgDir . DIRECTORY_SEPARATOR . 'nicepage-images';
        if (!file_exists($contentDir)) {
            mkdir($contentDir);
        }
        if ($handle = opendir($this->_images)) {
            while (false !== ($file = readdir($handle))) {
                if ('.' == $file || '..' == $file || is_dir($file)) {
                    continue;
                }
                if ($onlyFound && array_search($file, $this->_foundImages) === false) {
                    continue;
                }
                copy($this->_images . DIRECTORY_SEPARATOR . $file, $contentDir . DIRECTORY_SEPARATOR . $file);
            }
            closedir($handle);
        }
    }

    /**
     * Method to copy sample fonts to cms
     */
    private function _copyFonts() {
        if (!isset($this->_data['Pages'])) {
            return;
        }
        self::_importCustomFonts();
        self::_importGoogleEmbedFonts();
    }

    /**
     *  Import custom fonts
     */
    private function _importCustomFonts() {
        if (!$this->_fonts) {
            return;
        }
        $imgDir = dirname(JPATH_BASE) . '/' . 'images';
        $contentFontsDir = $imgDir . '/' . 'nicepage-fonts';
        if (!file_exists($contentFontsDir)) {
            mkdir($contentFontsDir);
        }

        //copy font files
        self::_copyFiles($this->_fonts . '/fonts', $imgDir . '/' . 'nicepage-fonts' . '/fonts');
        //copy pages css
        self::_importPageFontsCss($this->_fonts, $contentFontsDir, 'custom');
    }

    /**
     * Import google font if font embed enable
     */
    private function _importGoogleEmbedFonts() {
        if (!$this->_googleFonts) {
            return;
        }
        $fontsFolder = dirname(JPATH_ADMINISTRATOR) . '/components/com_nicepage/assets/css/fonts';
        if (!Folder::exists($fontsFolder)) {
            if (!Folder::create($fontsFolder)) {
                return;
            }
        }
        //copy font files
        self::_copyFiles($this->_googleFonts . '/fonts', $fontsFolder);
        //copy pages css
        self::_importPageFontsCss($this->_googleFonts, $fontsFolder, 'google');
    }

    /**
     * Copy files from $fromDir to $toDir
     *
     * @param $fromDir
     * @param $toDir
     */
    private function _copyFiles($fromDir, $toDir) {
        if (file_exists($fromDir)) {
            if (!file_exists($toDir)) {
                mkdir($toDir);
            }
            if ($handle = opendir($fromDir)) {
                while (false !== ($file = readdir($handle))) {
                    $fileSource = $fromDir . '/' . $file;
                    if ('.' == $file || '..' == $file || is_dir($fileSource)) {
                        continue;
                    }
                    copy($fileSource, $toDir . '/' . $file);
                }
                closedir($handle);
            }
        }
    }

    /**
     * Import css files with fonts connection information
     *
     * @param $fromDir
     * @param $toDir
     * @param $type
     */
    private function _importPageFontsCss($fromDir, $toDir, $type) {
        if (file_exists($fromDir)) {
            if ($handle = opendir($fromDir)) {
                while (false !== ($file = readdir($handle))) {
                    $fileSource = $fromDir . '/' . $file;
                    if ('.' == $file || '..' == $file || is_dir($fileSource)) {
                        continue;
                    }
                    $fileInfo = pathinfo($file);
                    if ($type === 'custom') {
                        $fileNameParts = explode('_', $fileInfo['filename']);
                        if (count($fileNameParts) > 1 && isset($this->_data['Pages'][$fileNameParts[1]])) {
                            $joomlaPageId = $this->_data['Pages'][$fileNameParts[1]]['joomla_id'];
                            copy($fileSource, $toDir . '/' . str_replace($fileNameParts[1], $joomlaPageId, $file));
                        }
                    } else {
                        // google embed fonts
                        if (preg_match('/page-(\d+?)-fonts/', $fileInfo['filename'], $matches)) {
                            $npPageId = isset($matches[1]) ? $matches[1] : false;
                            if ($npPageId && isset($this->_data['Pages'][$npPageId])) {
                                $joomlaPageId = $this->_data['Pages'][$npPageId]['joomla_id'];
                                $content = file_get_contents($fileSource);
                                file_put_contents($toDir . '/' . str_replace($npPageId, $joomlaPageId, $file), str_replace('fonts/', '', $content));
                            }
                        } else {
                            $content = file_get_contents($fileSource);
                            file_put_contents($toDir . '/' . $file, str_replace('fonts/', '', $content));
                        }
                    }
                }
                closedir($handle);
            }
        }
    }

    /**
     * Method to get Nicepage Component options
     *
     * @return mixed
     */
    private function _getExtOptions()
    {
        if ($this->_extOptions) {
            return $this->_extOptions;
        }

        if ($this->_template) {
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->select('params')
                ->from('#__template_styles')
                ->where('id=' . $query->escape($this->_style));
            $db->setQuery($query);
            $this->_extOptions = $this->_stringToParams($db->loadResult());
        } else {
            $this->_extOptions = NicepageHelpersNicepage::getConfig();
        }
        return $this->_extOptions;
    }

    /**
     * Method to save Nicepage Component options
     *
     * @param array $parameters
     */
    private function _setExtOptions($parameters)
    {
        if ($this->_template) {
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->update('#__template_styles')
                ->set($db->quoteName('params') . '=' . $db->quote($this->_paramsToString($parameters)))
                ->where('id=' . $query->escape($this->_style));
            $db->setQuery($query);
            $db->execute();
        } else {
            NicepageHelpersNicepage::saveConfig(array('dataIds' => $parameters['dataIds']));
        }
    }

    /**
     * Convert parameters array to string
     *
     * @param array $params
     *
     * @return mixed
     */
    private function _paramsToString($params)
    {
        $registry = new Registry();
        $registry->loadArray($params);
        return $registry->toString();
    }

    /**
     * Convert parameters string to array
     *
     * @param string $string
     *
     * @return mixed
     */
    private function _stringToParams($string)
    {
        $registry = new Registry();
        $registry->loadString($string);
        return $registry->toArray();
    }

    /**
     * Check and get property
     *
     * @param array  $item    Any array variable
     * @param string $prop    Property name
     * @param string $default Default property value
     *
     * @return mixed|string
     */
    private function _checkAndGetProperty($item, $prop, $default = '')
    {
        if (isset($item[$prop]) && $item[$prop]) {
            return $item[$prop];
        } else {
            return $default;
        }
    }

    /**
     * Parsing of sample data file
     *
     * @param string $file
     *
     * @return null|string
     */
    private function _parse($file)
    {
        $error = null;
        if (!($fp = fopen($file, 'r'))) {
            $error = 'Could not open json input';
        }
        $contents = '';
        if (is_null($error)) {
            while (!feof($fp)) {
                $contents .= fread($fp, 4096);
            }
            fclose($fp);
        }

        $contents = $this->_fixProductIds($contents);
        $this->_data = json_decode($contents, true);

        return $error;
    }

    /**
     * Fix product ids
     *
     * @param string $content Page content
     *
     * @return array|mixed|string|string[]
     */
    private function _fixProductIds($content) {
        $config = NicepageHelpersNicepage::getConfig();

        if (!isset($config['productsJson'])) {
            return $content;
        }

        if ($this->_replace) {
            return $content;
        }

        if (is_array($config['productsJson'])) {
            $products = json_decode(json_encode($config['productsJson']), true);
        } else {
            $jsonData = json_decode($config['productsJson'], true);
            $products = $jsonData['products'];
        }

        $maxId = 0;
        foreach ($products as $product) {
            $id = (int) $product['id'];
            if ($id > $maxId) {
                $maxId = $id;
            }
        }

        preg_match_all('/data-product-id=\\\\\\"(\d+)\\\\\\"/', $content, $matches, PREG_SET_ORDER);
        $replacedIds = array();
        $oldNewIds = array();
        foreach ($matches as $match) {
            if (in_array($match[0], $replacedIds)) {
                continue;
            }
            $maxId++;
            array_push($replacedIds, $match[0]);
            $oldNewIds[$match[1]] = (string) $maxId;
            $content = str_replace($match[0], 'data-product-id=\"' . $maxId . '\"', $content);
        }
        $this->_newProductIds = $oldNewIds;

        return $content;
    }

    /**
     * Parsing of sample data file
     *
     * @param string $file
     */
    public function parse($file) {
        $this->_parse($file);
    }

    /**
     * Set root url
     *
     * @param string $url
     */
    public function setRootUrl($url)
    {
        $this->_rootUrl = $url;
    }

    /**
     * Set images path
     *
     * @param string $path Path
     */
    public function setImagesPath($path)
    {
        $this->_images = $path;
    }

    /**
     * Copy only found images
     */
    public function copyOnlyFoundImages()
    {
        $this->_copyImages(true);
    }

    /**
     * Processing content
     *
     * @param string $content
     * @param string $state
     *
     * @return mixed
     */
    public function processingContent($content, $state = 'full')
    {
        return $this->_processingContent($content, $state);
    }

    /**
     * Load parameters
     */
    public function loadParameters()
    {
        $this->_loadParameters();
    }
}
