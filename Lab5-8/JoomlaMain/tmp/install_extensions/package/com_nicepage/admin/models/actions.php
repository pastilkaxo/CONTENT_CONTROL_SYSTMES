<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use NP\Uploader\FileUploader;
use NP\Uploader\Chunk;
use NP\Editor\SitePostsBuilder;
use NP\Editor\MenuItemsSaver;
use NP\Editor\PageSaver;
use NP\Editor\ConfigSaver;
use NP\Utility\Utility;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Input\Input;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

/**
 * Class NicepageModelActions
 */
class NicepageModelActions extends AdminModel
{
    /**
     * NicepageModelActions constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Method to get the record form.
     *
     * @param array   $data     Data for the form. [optional]
     * @param boolean $loadData True if the form is to load its own data (default case), false if not. [optional]
     *
     * @return JForm|boolean A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_nicepage.page', 'page', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Get data
     *
     * @param array $data Data parameters
     *
     * @return array|Input|string
     */
    private function _getRequestData($data) {
        $saveType = $data->get('saveType', '');
        switch ($saveType) {
        case 'base64':
            return new Input(json_decode(base64_decode($data->get('data', '', 'RAW')), true));
            break;
        case 'chunks':
            $chunk = new Chunk();
            $ret = $chunk->save($data);
            if (is_array($ret)) {
                return array($ret);
            }
            if ($chunk->last()) {
                $result = $chunk->complete();
                if ($result['status'] === 'done') {
                    return new Input(json_decode(base64_decode($result['data']), true));
                } else {
                    $result['result'] = 'error';
                    return array($result);
                }
            } else {
                return 'processed';
            }
            break;
        default:
        }
        return $data;
    }

    /**
     * Get service worker
     */
    public function getSw() {
        $sw = JPATH_ADMINISTRATOR . '/components/com_nicepage/assets/app/sw.js';
        if (file_exists($sw)) {
            $content = file_get_contents($sw);
            header('Content-Type: application/javascript');
            exit($content);
        }
    }

    /**
     * Main Action - Get pseudo posts to build new page
     *
     * @param Input $data Data parameters
     *
     * @return mixed|string
     */
    public function getSitePosts($data) {
        $builder = new SitePostsBuilder();
        return $this->_response(
            array(
                'result' => 'done',
                'data' => $builder->getSitePosts($data),
            )
        );
    }

    /**
     * Save local storage key
     *
     * @param Input $data Data parameters
     *
     * @return mixed|string
     */
    public function saveLocalStorageKey($data) {
        $data = $this->_getRequestData($data);
        if (is_string($data) || (is_array($data) && isset($data['status']) && $data['status'] === 'error')) {
            return $this->_response($data);
        }
        $json = $data->get('json', array(), 'RAW');
        ConfigSaver::saveConfig(array('localStorageKey' => $json));
        return $this->_response(
            array(
                'result' => 'done',
                'data' => $json,
            )
        );
    }

    /**
     * Save custom settings for editor
     *
     * @param array|Input $data Data parameters
     *
     * @return mixed|string
     */
    public function saveConfig($data)
    {
        ConfigSaver::saveConfig($data);
        return $this->_response(
            array(
                'result' => 'done'
            )
        );
    }

    /**
     * Get site object by page id
     *
     * @param int $currentPageId Current page id
     *
     * @return array
     */
    public function getSite($currentPageId = null)
    {
        $config = NicepageHelpersNicepage::getConfig();
        $siteSettings = isset($config['siteSettings']) ? $config['siteSettings'] : '{}';
        $siteSettings = str_replace('[[site_path_editor]]', Utility::getSiteUrl(), $siteSettings);
        //set default site title
        $settings = json_decode($siteSettings);
        if ($settings) {
            $settings->title = Factory::getConfig()->get('sitename', 'My Site');
            $siteSettings = json_encode($settings);
        }
        $site = array(
            'id' => '1',
            'isFullLoaded' => true,
            'items' => array(),
            'order' => 0,
            'publicUrl' => $this->getHomeUrl(),
            'status' => 2,
            'title' => Factory::getConfig()->get('sitename', 'My Site'),
            'settings' => $siteSettings
        );

        $pages = array();
        $sectionsPageIds = NicepageHelpersNicepage::getSectionsTable()->getAllPageIds();
        if (count($sectionsPageIds) > 100) {
            $sectionsPageIds = array_slice($sectionsPageIds, 0, 100);
        }
        if ($currentPageId) {
            array_push($sectionsPageIds, $currentPageId);
        }
        if (count($sectionsPageIds) > 0) {
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__content');
            $query->where('(state = 1 or state = 0)');
            $query->where('id in (' . implode(',', $sectionsPageIds) . ')');
            $query->order('created DESC');
            $db->setQuery($query);
            $list = $db->loadObjectList();

            foreach ($list as $item) {
                $pages[] = $this->_getPageData($item);
            }
        }
        $site['items'] = $pages;
        return $site;
    }

    /**
     *
     * @return string
     */
    public function getPageHtml()
    {
        $html = '';
        $pageId = Factory::getApplication()->input->get('pageId', -1);
        $page = NicepageHelpersNicepage::getSectionsTable();
        if ($page->load(array('page_id' => $pageId))) {
            $props = $page->autosave_props ? $page->autosave_props : $page->props;
            $html = isset($props['html']) ? $props['html'] : '';
            $html = NicepageHelpersNicepage::processSectionsHtml($html, array('isPublic' => false));
        }
        return $html;
    }

    /**
     * Convert cms post to editor format
     *
     * @param object $postObject Cms post object
     *
     * @return array
     */
    private function _getPageData($postObject)
    {
        $head = null;
        $page = NicepageHelpersNicepage::getSectionsTable();
        if ($page->load(array('page_id' => $postObject->id))) {
            $head = isset($page->props['head']) ? $page->props['head'] : '';
        }
        $domain = Factory::getApplication()->input->get('domain', '', 'RAW');
        $current = dirname(dirname((Uri::current())));
        $adminPanelUrl = $current . '/administrator';
        return array(
            'siteId' => '1',
            'title' => $postObject->title,
            'publicUrl' => $this->getArticleUrlById($postObject->id),
            'publishUrl' => $this->getArticleUrlById($postObject->id),
            'canShare' => false,
            'html' => null,
            'head' => $head,
            'keywords' => null,
            'imagesUrl' => array(),
            'id' => (int) $postObject->id,
            'order' => 0,
            'status' => 2,
            'editorUrl' => $adminPanelUrl . '/index.php?option=com_nicepage&task=nicepage.autostart&postid=' . $postObject->id . ($domain ? '&domain=' . $domain : ''),
            'htmlUrl' => $adminPanelUrl . '/index.php?option=com_nicepage&task=actions.getPageHtml&pageId=' . $postObject->id
        );
    }

    /**
     * Main Action - Upload new image
     *
     * @param Input $data Data parameters
     *
     * @return bool|mixed|string
     */
    public function uploadImage($data)
    {
        $files = Factory::getApplication()->input->files;
        if (!$files) {
            Factory::getApplication()->enqueueMessage(Text::_('File not found'), 'error');
            return false;
        }

        $file = $files->get('async-upload');

        $imagesPaths = $this->getImagesPaths();
        $name = $file['name'];
        $file['filepath'] = $imagesPaths['realpath'] . '/' . $name;

        if (file_exists($file['filepath'])) {
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $name = md5($file['name'] . microtime()) . '.' . $ext;
            $file['filepath'] = $imagesPaths['realpath'] . '/' . $name;
        }

        $objectFile = new JObject($file);
        if (!File::upload($objectFile->tmp_name, $objectFile->filepath)) {
            Factory::getApplication()->enqueueMessage(Text::_('Unable to upload file'), 'error');
            return false;
        }

        $info = @getimagesize($file['filepath']);
        $imagesUrl = str_replace(JPATH_ROOT, $this->getHomeUrl(), $file['filepath']);
        $imagesUrl = str_replace('\\', '/', $imagesUrl);
        return $this->_response(
            array(
                'status' => 'done',
                'image' => array(
                    'sizes' => array(
                        array(
                            'height' => @$info[1],
                            'url' => $imagesUrl,
                            'width' => @$info[0],
                        )
                    ),
                    'type' => 'image',
                    'id' => $name,
                    'fileName' => $name
                )
            )
        );
    }

    /**
     * Main Action - Save new template type of page
     *
     * @param Input $data Data parameters
     */
    public function savePageType($data) {
        $id   = $data->get('pageId', '');
        $type = $data->get('pageType', '');
        $passwordProtection = $data->get('passwordProtection', '');
        if ($id && $type) {
            $page = NicepageHelpersNicepage::getSectionsTable();
            if ($page->load(array('page_id' => $id))) {
                $props = $page->props;
                $props['pageView'] = $type;
                if ($passwordProtection !== 'pass-not-changes') {
                    $props['passwordProtection'] = $passwordProtection;
                }
                $page->save(array('props' => $props));
            }
        }
    }

    /**
     * Save products json
     *
     * @param Input $data Data parameters
     *
     * @return mixed|string
     */
    public function saveProductsJson($data)
    {
        $data = $this->_getRequestData($data);
        if (is_string($data) || (is_array($data) && isset($data['status']) && $data['status'] === 'error')) {
            return $this->_response($data);
        }
        $productsJson = $data->get('productsData', array(), 'RAW');
        ConfigSaver::saveConfig(array('productsJson' => $productsJson));
        return $this->_response(
            array(
                'result' => 'done'
            )
        );
    }

    /**
     * Save site setttings action
     *
     * @param Input $data Data parameters
     *
     * @return mixed|string
     */
    public function saveSiteSettings($data)
    {
        $data = $this->_getRequestData($data);
        if (is_string($data) || (is_array($data) && isset($data['status']) && $data['status'] === 'error')) {
            return $this->_response($data);
        }
        $settings = $data->get('settings', '', 'RAW');
        ConfigSaver::saveSiteSettings($settings);
        return $this->_response(
            array(
                'result' => 'done'
            )
        );
    }

    /**
     * @param Input $data Data parameters
     *
     * @return mixed|string
     */
    public function savePreferences($data)
    {
        $data = $this->_getRequestData($data);
        if (is_string($data) || (is_array($data) && isset($data['status']) && $data['status'] === 'error')) {
            return $this->_response($data);
        }

        $settings = $data->get('settings', '', 'RAW');
        if ($settings) {
            if (is_string($settings)) {
                $settings = json_decode($settings, true);
            }
            $disableAutoSave = isset($settings['disableAutosave']) ? $settings['disableAutosave'] : '1';
            $toSave = array('disableAutosave' => $disableAutoSave);
            ConfigSaver::saveConfig($toSave);
        }
        return $this->_response(
            array(
                'result' => 'done'
            )
        );
    }

    /**
     * @param Input $data Data parameters
     *
     * @return mixed|string
     */
    public function saveMenuItems($data)
    {
        $menuData = $data->get('menuData', '', 'RAW');
        $menuItemsSaver = new MenuItemsSaver($menuData);
        $result = $menuItemsSaver->save();
        return $this->_response($result);
    }

    /**
     * Remove custom font
     *
     * @param Input $data Data parameters
     *
     * @return mixed|string
     */
    public function removeFont($data)
    {
        $fileName = $data->get('fileName', '', 'RAW');
        $customFontPath = dirname(JPATH_BASE) . '/' . 'images/nicepage-fonts/fonts/' . $fileName;
        $success = true;
        if (File::exists($customFontPath) && !File::delete($customFontPath)) {
            $success = false;
        }
        return $this->_response(
            array(
                'result' => 'done',
                'success' => $success,
            )
        );
    }

    /**
     * Main Action - New Save or Update page
     *
     * @param Input $data Data parameters
     *
     * @return mixed|string
     */
    public function savePage($data)
    {
        $data = $this->_getRequestData($data);
        if (is_string($data) || (is_array($data) && isset($data['status']) && $data['status'] === 'error')) {
            return $this->_response($data);
        }

        $pageSaver = new PageSaver($data);

        if (!$pageSaver->check()) {
            return $this->_response(
                array(
                    'status' => 'error',
                    'message' => 'The page parameters is incomplete',
                )
            );
        }

        $pageSaver->save();
        $article = $pageSaver->getArticle();
        return $this->_response(
            array(
                'result' => 'done',
                'data' => $this->_getPageData($article),
            )
        );
    }

    /**
     * Clear chunk by id
     *
     * @param Input $data Clear chunks
     */
    public function clearChunks($data) {
        $id = $data->get('id', '', 'RAW');
        Chunk::clearChunksById($id);
        return $this->_response(
            array(
                'result' => 'done'
            )
        );
    }

    /**
     * Main Action - Duplicate page
     *
     * @param Input $data Array of data
     *
     * @return mixed|string
     */
    public function duplicatePage($data)
    {
        $postId = $data->get('postId', '');
        $error = array('status' => 'error');
        $succes = array('result' => 'ok');

        if (!$postId) {
            return $this->_response($error);
        }

        $page = NicepageHelpersNicepage::getSectionsTable();
        if (!$page->load(array('page_id' => $postId))) {
            return $this->_response($error);
        }

        $newPage = NicepageHelpersNicepage::getSectionsTable();
        $pageData = array(
            'page_id'               => 1000000,
            'props'                 => $page->props,
            'preview_props'         => $page->preview_props ?: '',
            'autosave_props'         => $page->autosave_props ?: '',
            $newPage->getKeyName()  => null
        );
        if (!$newPage->save($pageData)) {
            return $this->_response($error);
        }
        return $this->_response($succes);
    }

    /**
     * @param string|array $result Result
     *
     * @return mixed|string
     */
    private function _response($result)
    {
        if (is_string($result)) {
            $result = array('result' => $result);
        }
        return json_encode($result);
    }

    /**
     * @return array
     */
    public function getImagesPaths()
    {
        $imagesFolder = JPATH_ROOT . '/images';
        if (!file_exists($imagesFolder)) {
            Folder::create($imagesFolder);
        }

        $nicepageContentFolder = Path::clean(implode('/', array($imagesFolder, 'nicepage-images')));
        if (!file_exists($nicepageContentFolder)) {
            Folder::create($nicepageContentFolder);
        }

        $nicepageContentFolderUrl = $this->getHomeUrl() . '/images/nicepage-images';

        return array('realpath' => $nicepageContentFolder, 'url' => $nicepageContentFolderUrl);
    }

    /**
     * @return string
     */
    public function getHomeUrl()
    {
        return dirname(dirname(Uri::current()));
    }

    /**
     * @param int $id Article id
     *
     * @return string
     */
    public function getArticleUrlById($id)
    {
        return $this->getHomeUrl() . '/index.php?option=com_content&view=article&id=' . $id;
    }

    /**
     * Main Action - Import data from plugin
     *
     * @param Input $data Data parameters
     *
     * @return mixed|string
     * @throws Exception
     */
    public function importData($data)
    {
        $fileName   = $data->get('filename', '');
        $isLast     = $data->get('last', '');

        if ('' === $fileName) {
            throw new Exception("Empty filename");
        } else {
            $unzipHere = '';

            $tmp = JPATH_SITE . '/tmp';
            if (file_exists($tmp) && is_writable($tmp)) {
                $unzipHere = $tmp . '/' . $fileName;
            }

            $images = JPATH_SITE . '/images';
            if (!$unzipHere && file_exists($images) && is_writable($images)) {
                $unzipHere = $images . '/' . $fileName;
            }

            if (!$unzipHere) {
                throw new Exception("Upload dir don't writable");
            }
            $uploader = new FileUploader();
            $result = $uploader->upload($unzipHere, $isLast);
            if ($result['status'] == 'done') {
                $contentDir = $this->_contentUnZip($unzipHere);

                $contentJsonPath = $contentDir . '/content/content.json';
                if (!file_exists($contentJsonPath)) {
                    $pathInfo = pathinfo($unzipHere);
                    $contentJsonPath = $contentDir . '/' . $pathInfo['filename']. '/content/content.json';
                }

                if (file_exists($contentJsonPath)) {
                    JLoader::register('Nicepage_Data_Loader', JPATH_ADMINISTRATOR . '/components/com_nicepage' . '/helpers/import.php');
                    $loader = new Nicepage_Data_Loader();
                    $loader->load($contentJsonPath);
                    $loader->execute(Factory::getApplication()->input->getArray());
                }
            }
        }
        return $this->_response(
            array(
                'result' => 'done'
            )
        );
    }

    /**
     * Upload file
     *
     * @param Input $data File data
     *
     * @return mixed|string
     * @throws Exception
     */
    public function uploadFile($data)
    {
        $fileName   = $data->get('filename', '');
        $isLast     = $data->get('last', '');
        $isFont     = $data->get('isFont', '');

        if ('' === $fileName) {
            throw new Exception("Empty filename");
        } else {
            $uploadHere = '';

            $params = ComponentHelper::getParams('com_media');
            $filesPath = JPATH_SITE . '/' . $params->get('image_path', 'images');

            if ($isFont) {
                $filesPath = $filesPath . '/' . 'nicepage-fonts/fonts';
                if (!Folder::exists($filesPath)) {
                    if (!Folder::create($filesPath)) {
                        throw new Exception("Fonts dir don't created");
                    }
                }
            }

            if (file_exists($filesPath) && is_writable($filesPath)) {
                $uploadHere = $filesPath . '/' . $fileName;
            }

            if (!$uploadHere) {
                throw new Exception("Upload dir $uploadHere don't writable");
            }

            $uploader = new FileUploader();
            $result = $uploader->upload($uploadHere, $isLast);
            if ($result['status'] == 'done') {
                if ($isFont) {
                    $fileInfo = pathinfo($result['fileName']);
                    $response = array(
                        'fileName' => $result['fileName'],
                        'id' => 'user-file-' . $result['fileName'],
                        'name' => isset($fileInfo['filename']) ? $fileInfo['filename'] : $fileInfo['basename'],
                        'publicUrl' => str_replace(JPATH_SITE, $this->getHomeUrl(), $result['path']),
                        'result' => 'done',
                    );
                } else {
                    $response = array(
                        'url' => str_replace(JPATH_SITE, $this->getHomeUrl(), $result['path']),
                        'title' => $result['fileName'],
                        'result' => 'done',
                    );
                }
                return $this->_response($response);
            }
        }
        return $this->_response(
            array(
                'result' => 'done'
            )
        );
    }

    /**
     * @param string $zipPath Zip path
     *
     * @return string
     */
    private function _contentUnZip($zipPath)
    {
        $tmpdir = dirname($zipPath) . '/' . md5(round(microtime(true)));
        if (class_exists('ZipArchive')) {
            $this->_nativeUnzip($zipPath, $tmpdir);
        }
        File::delete($zipPath);
        return $tmpdir;
    }

    /**
     * Native unzip
     *
     * @param string $zipPath Zip path
     * @param string $tmpdir  Tmp path
     */
    private function _nativeUnzip($zipPath, $tmpdir)
    {
        $zip = new ZipArchive;

        if ($zip->open($zipPath) === true) {
            $zip->extractTo($tmpdir);
            $zip->close();
        }
    }
}
