<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Layout\FileLayout;

Core::load("Core_Content_ListItem");

class CoreContentFeaturedArticle extends CoreContentListItem
{
    public $tags = array();

    public function __construct($component, $componentParams, $article, $articleParams)
    {
        parent::__construct($component, $componentParams, $article, $articleParams);
        $this->category = $this->_articleParams->get('show_category') ? $this->_article->category_title : '';
        $this->categoryLink = $this->_articleParams->get('link_category') && $this->_article->catid
            ? Route::_(RouteHelper::getCategoryRoute($this->_article->catid))
            : '';
        $this->parentCategory = $this->_articleParams->get('show_parent_category') && !empty($this->_article->parent_id)
            ? $this->_article->parent_title : '';
        $this->parentCategoryLink = $this->_articleParams->get('link_parent_category') && !empty($this->_article->parent_id)
            ? Route::_(RouteHelper::getCategoryRoute($this->_article->parent_id))
            : '';
        if ($this->_articleParams->get('show_tags', 1) && !empty($this->_article->tags)) {
            $this->_article->tagLayout = new FileLayout('joomla.content.tags');
            $tagsContent = $this->_article->tagLayout->render($this->_article->tags->itemTags);
            if (preg_match_all('/<a[^>]+>[\s\S]+?<\/a>/', $tagsContent, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    array_push($this->tags, $match[0]);
                }
            }
        }
    }
}
