<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

abstract class CoreContentArticleBase
{
    protected $_component;
    protected $_componentParams;
    protected $_article;
    protected $_articleParams;

    public $title;

    public $titleLink;

    public $created;

    public $modified;

    public $published;

    public $hits;

    public $author;

    public $authorLink;

    public $shareLink;

    public $category;

    public $categoryLink;

    public $parentCategory;

    public $parentCategoryLink;

    protected function __construct($component, $componentParams, $article, $articleParams)
    {
        // Initialization:
        $this->_component = $component;
        $this->_componentParams = $componentParams;
        $this->_article = $article;
        $this->_articleParams = $articleParams;

        // Configuring properties:
        $this->title = $this->_article->title;
        $this->created = $this->_articleParams->get('show_create_date')
            ? $this->_article->created : '';
        $this->modified = $this->_articleParams->get('show_modify_date')
            ? $this->_article->modified : '';
        $this->published = $this->_articleParams->get('show_publish_date')
            ? $this->_article->publish_up : '';
        $this->hits = $this->_articleParams->get('show_hits')
            ? $this->_article->hits : '';
        $this->author = $this->_articleParams->get('show_author') && !empty($this->_article->author)
            ? ($this->_article->created_by_alias ? $this->_article->created_by_alias : $this->_article->author)
            : '';
        $this->authorLink = strlen($this->author) && !empty($this->_article->contactid) && $this->_articleParams->get('link_author')
            ? 'index.php?option=com_contact&view=contact&id=' . $this->_article->contactid
            : '';
        $this->shareLink = dirname(Uri::current()) . '/' . RouteHelper::getArticleRoute($this->_article->slug, $this->_article->catid);
    }

    /**
     * @see $created
     */
    public function createdDateInfo($created)
    {
        return '<time datetime="' . HTMLHelper::_('date', $created, 'c') . '" itemprop="dateCreated">' .
            Text::sprintf('COM_CONTENT_CREATED_DATE_ON', HTMLHelper::_('date', $created, Text::_('DATE_FORMAT_LC3'))) .
            '</time>';
    }

    /**
     * @see $modified
     */
    public function modifiedDateInfo($modified)
    {
        return '<time datetime="' . HTMLHelper::_('date', $modified, 'c') . '" itemprop="dateModified">' .
            Text::sprintf('COM_CONTENT_LAST_UPDATED', HTMLHelper::_('date', $modified, Text::_('DATE_FORMAT_LC3'))) .
            '</time>';
    }

    /**
     * @see $published
     */
    public function publishedDateInfo($published)
    {
        return '<time datetime="' . HTMLHelper::_('date', $published, 'c') . '" itemprop="datePublished">' .
            Text::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', HTMLHelper::_('date', $published, Text::_('DATE_FORMAT_LC3'))) .
            '</time>';
    }

    /**
     * @see $author
     */
    public function authorInfo($author, $authorLink)
    {
        if (strlen($authorLink))
            return Text::sprintf('COM_CONTENT_WRITTEN_BY',
                HTMLHelper::_('link', Route::_($authorLink), $author, array('itemprop' => 'url')));
        return Text::sprintf('COM_CONTENT_WRITTEN_BY', $author);
    }

    public function articleSeparator() { return '<div class="item-separator">&nbsp;</div>'; }

    /**
     * @see $section, $sectionLink, $category, $categoryLink
     */
    public function categories($parentCategory, $parentCategoryLink, $category, $categoryLink)
    {
        if (0 == strlen($parentCategory) && 0 == strlen($category))
            return '';
        ob_start();
        if (strlen($parentCategory)) {
            if (strlen($parentCategoryLink)) {
                echo '<a href="' . $parentCategoryLink . '" itemprop="genre">' . $this->_component->escape($parentCategory) . '</a>';
            } else {
                echo '<span  itemprop="genre">' . $this->_component->escape($parentCategory) . '</span>';
            }
            if (strlen($category)) {
                echo ' / ';
            }
        }
        if (strlen($category)) {
            if (strlen($categoryLink)) {
                echo '<a href="' . $categoryLink . '" itemprop="genre">' . $this->_component->escape($category) . '</a>';
            } else {
                echo '<span itemprop="genre">' . $this->_component->escape($category) . '</span>';
            }
        }
        return Text::sprintf('COM_CONTENT_CATEGORY', ob_get_clean());
    }

    public function hitsInfo($hits)
    {
        return '<meta itemprop="interactionCount" content="UserPageVisits:' . $hits . '" />' .
            Text::sprintf('COM_CONTENT_ARTICLE_HITS', $hits);
    }

    public function event($name)
    {
        return $this->_article->event->{$name};
    }

    public function getArticleViewParameters()
    {
        return array('metadata-header-icons' => array(), 'metadata-footer-icons' => array());
    }

    public function article($article)
    {
        return funcPost($article);
    }
}
