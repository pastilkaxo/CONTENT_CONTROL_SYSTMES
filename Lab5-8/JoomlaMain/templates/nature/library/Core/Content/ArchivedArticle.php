<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\HTML\HTMLHelper;

Core::load("Core_Content_ArticleBase");

class CoreContentArchivedArticle extends CoreContentArticleBase
{
    public $intro;

    public function __construct($component, $componentParams, $article, $articleParams)
    {
        parent::__construct($component, $componentParams, $article, $articleParams);
        $this->titleLink = $this->_articleParams->get('link_titles')
            ? Route::_(RouteHelper::getArticleRoute($this->_article->slug, $this->_article->catid))
            : '';
        $this->category = $this->_articleParams->get('show_category') ? $this->_article->category_title : '';
        $this->categoryLink = $this->_articleParams->get('link_category') && $this->_article->catid
            ? Route::_(ContentHelperRoute::getCategoryRoute($this->_article->catid))
            : '';
        $this->parentCategoryLink = $this->_articleParams->get('link_parent_category') && !empty($this->_article->parent_id)
            ? Route::_(ContentHelperRoute::getCategoryRoute($this->_article->parent_id))
            : '';
        $this->intro = $this->_articleParams->get('show_intro') ? $this->_article->introtext : '';
    }

    public function intro($intro)
    {
        return HTMLHelper::_('string.truncate', $intro, $this->_articleParams->get('introtext_limit'));
    }
}
