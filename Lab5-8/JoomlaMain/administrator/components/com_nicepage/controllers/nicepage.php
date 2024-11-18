<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use NP\Editor\PageSaver;

/**
 * Class NicepageControllerNicepage
 */
class NicepageControllerNicepage extends FormController
{
    /**
     * Open new page
     */
    public function start()
    {
        $this->_removeDraftArticles();
        // create draft page
        $article = PageSaver::createPost(array('state' => 2 /*to draft*/));
        if ($article) {
            $this->_removePagesByArticleId($article->id);
            $this->_goToPost($article->id, 'start');
        }
        // new post don't create, go to admin dashboard
        Factory::getApplication()->redirect('index.php');
    }

    /**
     * Open exist page from editor
     */
    public function autostart()
    {
        $postId = Factory::getApplication()->input->getVar('postid', '');
        $this->_goToPost($postId, 'autostart');
    }

    /**
     * Go to post by id and start type
     *
     * @param int    $postId    Article id
     * @param string $startType Type of start context
     *
     * @throws Exception
     */
    private function _goToPost($postId, $startType)
    {
        $domain = NicepageHelpersNicepage::getDomain();
        $session = Factory::getSession();
        $registry = $session->get('registry');
        $registry->set('com_content.edit.article.id', $postId);
        $url = 'index.php?option=com_content&view=article&layout=edit&' . $startType . '=1&id=' . $postId . ($domain ? '&domain=' . $domain : '');
        Factory::getApplication()->redirect($url);
    }

    /**
     * Remove draft articles from db
     */
    private function _removeDraftArticles()
    {
        JLoader::register('Nicepage_Data_Mappers', JPATH_ADMINISTRATOR . '/components/com_nicepage/tables/mappers.php');
        $contentMapper = Nicepage_Data_Mappers::get('content');
        $list = $contentMapper->find(array('extra' => array('state = 2', 'title like \'%Page%\'')));
        if (count($list) > 0) {
            foreach ($list as $item) {
                $contentMapper->delete($item->id);
            }
        }
    }

    /**
     * Remove nice pages by article id
     *
     * @param string $id Article id
     */
    private function _removePagesByArticleId($id)
    {
        $page = NicepageHelpersNicepage::getSectionsTable();
        if ($page->load(array('page_id' => $id))) {
            $page->delete($page->id);
        }
    }
}