<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Models;

defined('_JEXEC') or die;

use NP\Builder\PostDataBuilder;
use Joomla\Registry\Registry;
use Joomla\Component\Content\Site\Model\ArticlesModel;
use Joomla\CMS\Helper\TagsHelper;
use \NicepageHelpersNicepage;
/**
 * Class NicepageContentModelBlog
 */
class ContentModelCustomArticles extends ArticlesModel
{
    private $_options = array();

    /**
     * ContentModelCustomArticles constructor.
     *
     * @param array $options options
     */
    public function __construct($options = array())
    {
        $this->_options = $options;
        parent::__construct();
    }

    /**
     * Set settigns for state
     *
     * @param string $ordering  Order
     * @param string $direction Direction
     */
    protected function populateState($ordering = 'a.id', $direction = 'desc')
    {
        parent::populateState($ordering, $direction);
        if (isset($this->_options['category_id']) && $this->_options['category_id']) {
            $this->setState('filter.category_id', $this->_options['category_id']);
        }
        if (isset($this->_options['tags']) && $this->_options['tags']) {
            $tags = array_map('trim', explode(',', $this->_options['tags']));
            $tagIds = array();
            $tagsHelper = new TagsHelper();
            foreach ($tags as $tag) {
                $items = $tagsHelper->searchTags(array('like' => $tag));
                foreach ($items as $item) {
                    array_push($tagIds, $item->value);
                }
            }
            if (count($tagIds) < 1) {
                $tagIds = array(0, 0);
            }
            $this->setState('filter.tag', $tagIds);
        }
        $this->setState('filter.subcategories', true);
        $this->setState('filter.published', 1);
        $this->setState('list.ordering', 'modified');
        $this->setState('list.direction', 'DESC');
        $this->setState('list.start', 0);
        $limit = 25;
        if (isset($this->_options['count']) && $this->_options['count']) {
            $limit = $this->_options['count'];
        }
        $this->setState('list.limit', (int) $limit);
        $this->setState('params', new Registry());

        // exclude np pages
        $sectionsPageIds = NicepageHelpersNicepage::getSectionsTable()->getAllPageIds();

        $postId = '';
        if (isset($this->_options['postId']) && $this->_options['postId']) {
            $postId = (int) $this->_options['postId'];
        }

        if ($postId) {
            $filterArticleId = array_search($postId, $sectionsPageIds) === false ? $postId : -1;
            $this->setState('filter.article_id', array($filterArticleId));
            $this->setState('filter.article_id.include', true);
        } else {
            if (count($sectionsPageIds) > 0) {
                $this->setState('filter.article_id', $sectionsPageIds);
                $this->setState('filter.article_id.include', false);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getItems() {
        return parent::getItems();
    }

    /**
     * Get posts by category id
     *
     * @param string $buildType Build type
     *
     * @return array
     */
    public function getPosts($buildType = 'blog') {
        $posts = array();
        $items = $this->getItems();
        foreach ($items as $key => $item) {
            $builder = new PostDataBuilder($item, $buildType);
            $post = $builder->getData();
            array_push($posts, $post);
        }
        return $posts;
    }
}
