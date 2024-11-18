<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Builder;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Component\Content\Site\Helper\RouteHelper;

/**
 * Class PostDataBuilder
 */
class PostDataBuilder extends DataBuilder
{
    private $_item;
    private $_buildType;
    private $_data;

    /**
     * PostDataBuilder constructor.
     *
     * @param object $item      Article object
     * @param string $buildType Build type
     */
    public function __construct($item, $buildType)
    {
        $lang = Factory::getApplication()->getLanguage();
        $lang->load('com_content', JPATH_BASE, null, false, true);
        $lang->load('com_content', JPATH_BASE . '/components/com_content', null, false, true);

        HTMLHelper::addIncludePath(JPATH_BASE . '/components/com_content/helpers');

        $this->_item = $item;
        $this->_buildType = $buildType;

        //create article slug
        $this->_item->slug = $this->_item->alias ? ($this->_item->id . ':' . $this->_item->alias) : $this->_item->id;
        $image = $this->image();
        $this->_data = array(
            'post-header' => $this->title(),
            'post-header-link' => $this->titleLink(),
            'post-content' => $this->content(),
            'post-image' => $image['src'],
            'post-alt-image' => $image['alt'],
            'post-readmore-text' => $this->readmoreText(),
            'post-readmore-link' => $this->readmoreLink(),
            'post-metadata-author' => $this->author(),
            'post-metadata-date' => $this->date(),
            'post-metadata-category' => $this->category(),
            'post-metadata-edit' => $this->edit(),
            'post-tags' => $this->tags(),
            'post-type' => $this->_buildType,
        );
    }

    /**
     * Get post data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Get article title
     *
     * @return mixed
     */
    public function title()
    {
        return $this->_item->title;
    }

    /**
     * Get article content
     *
     * @return false|string|string[]|null
     */
    public function content()
    {
        $type = $this->_buildType == 'blog' ? 'intro' : 'fulltext';
        $text = $this->_item->introtext . $this->_item->fulltext;
        if ($type == 'fulltext') {
            return $text;
        } else {
            return $this->excerpt($text, 150, '...', true);
        }
    }

    /**
     * Get article title link
     *
     * @return string
     */
    public function titleLink()
    {
        return $this->_item->params->get('access-view')
            ? Route::_(RouteHelper::getArticleRoute($this->_item->slug, $this->_item->catid))
            : '';
    }

    /**
     * Get article image
     *
     * @return array
     */
    public function image()
    {
        $image = array('src' => '', 'alt' => '');
        $type = $this->_buildType == 'blog' ? 'intro' : 'fulltext';
        $images = json_decode($this->_item->images, true);
        if ($images && isset($images['image_' . $type]) && $images['image_' . $type]) {
            $image['src'] = Uri::root(true) . '/' . $images['image_' . $type];
            $image['alt'] = $images['image_' . $type . '_alt'];
        }
        if (!$image['src']) {
            $content = $this->_item->introtext . $this->_item->fulltext;
            if (preg_match_all('/src=[\'"]([\s\S]+?)[\'"]/', $content, $imageMatches, PREG_SET_ORDER)) {
                foreach ($imageMatches as $imageMatch) {
                    $imageUrl = $imageMatch[1];
                    if (preg_match('/^https?/', $imageUrl)) {
                        $image['src'] = $imageUrl;
                        break;
                    }
                    $realImagePath = JPATH_ROOT . '/' . $imageUrl;
                    if (file_exists($realImagePath)) {
                        $image['src'] = Uri::root(true) . '/' .  $imageUrl;
                        break;
                    }
                }
            }
        }
        return $image;
    }

    /**
     * Get article tags
     *
     * @return string
     */
    public function tags()
    {
        $tags = array();
        if ($this->_item->params->get('show_tags', 1) && !empty($this->_item->tags)) {
            $this->_item->tagLayout = new FileLayout('joomla.content.tags');
            $tagsContent = $this->_item->tagLayout->render($this->_item->tags->itemTags);
            if (preg_match_all('/<a[^>]+>[\s\S]+?<\/a>/', $tagsContent, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    array_push($tags, $match[0]);
                }
            }
        }
        return count($tags) > 0 ? implode(', ', $tags) : '';
    }

    /**
     * Get article edit link
     *
     * @return mixed|string
     */
    public function edit()
    {
        if ($this->_item->params->get('access-edit')) {
            $text = HTMLHelper::_('icon.edit', $this->_item, $this->_item->params);
            $src = dirname((Uri::current())) . '/components/com_nicepage/assets';
            preg_match('/<a[^>]*>([\s\S]*?)<\/a>/', $text, $matches);
            $linkContent = $matches[1];
            $newLinkContent = '<img src="' . $src . '/images/edit.png" alt="Edit " />';
            $text = str_replace($linkContent, $newLinkContent, $text);

            if (preg_match('/title="([^"]*)"/', $linkContent, $matches)) {
                $tooltipText = $matches[1];
                $tooltipText = preg_replace('/<strong>(.*?)<\/strong><br \/>/', '$1 :: ', $tooltipText);
                $text = '<span class="hasTip" title="' . $tooltipText . '">' . $text . '</span>';
            }
            return $text;
        } else {
            return '';
        }
    }

    /**
     * Get article category
     *
     * @return string
     */
    public function category()
    {
        $globalParams = ComponentHelper::getParams('com_content', true);
        $category = $this->_item->params->get('show_category', $globalParams->get('show_category')) ? $this->_item->category_title : '';
        $categoryLink = $this->_item->params->get('link_category', $globalParams->get('link_category'))
            ? (dirname((Uri::current())) . '/' . RouteHelper::getCategoryRoute($this->_item->catid))
            : '';
        $parentCategory =  $this->_item->params->get('show_parent_category', $globalParams->get('show_parent_category')) && $this->_item->parent_id != 1
            ? $this->_item->parent_title : '';
        $parentCategoryLink = $this->_item->params->get('link_parent_category', $globalParams->get('link_parent_category'))
            ? (dirname((Uri::current())) . '/' .RouteHelper::getCategoryRoute($this->_item->parent_id))
            : '';

        if (0 == strlen($parentCategory) && 0 == strlen($category)) {
            return '';
        }

        ob_start();
        if (strlen($parentCategory)) {
            if (strlen($parentCategoryLink)) {
                echo '<a href="' . $parentCategoryLink . '" itemprop="genre">' . $parentCategory . '</a>';
            } else {
                echo '<span  itemprop="genre">' . $parentCategory . '</span>';
            }
            if (strlen($category)) {
                echo ' / ';
            }
        }
        if (strlen($category)) {
            if (strlen($categoryLink)) {
                echo '<a href="' . $categoryLink . '" itemprop="genre">' . $category . '</a>';
            } else {
                echo '<span itemprop="genre">' . $category . '</span>';
            }
        }
        return Text::sprintf('COM_CONTENT_CATEGORY', ob_get_clean());
    }

    /**
     * Get article author
     *
     * @return mixed
     */
    public function author()
    {
        $globalParams = ComponentHelper::getParams('com_content', true);
        $author = $this->_item->params->get('show_author', $globalParams->get('show_author')) && !empty($this->_item->author)
            ? ($this->_item->created_by_alias ? $this->_item->created_by_alias : $this->_item->author)
            : '';
        $authorLink = strlen($author) && !empty($this->_item->contactid) && $this->_item->params->get('link_author', $globalParams->get('link_author'))
            ? 'index.php?option=com_contact&view=contact&id=' . $this->_item->contactid : '';
        if (strlen($authorLink)) {
            return Text::sprintf('COM_CONTENT_WRITTEN_BY', HTMLHelper::_('link', Route::_($authorLink), $author, array('itemprop' => 'url')));
        } else {
            return Text::sprintf('COM_CONTENT_WRITTEN_BY', $author);
        }
    }

    /**
     * Get article published date
     *
     * @return string
     */
    public function date()
    {
        $published = $this->_item->publish_up;
        return '<time datetime="' . HTMLHelper::_('date', $published, 'c') . '" itemprop="datePublished">' .
            Text::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', HTMLHelper::_('date', $published, Text::_('DATE_FORMAT_LC3'))) .
            '</time>';
    }

    /**
     * Get article readmore text
     *
     * @return string
     */
    public function readmoreText()
    {
        $globalParams = ComponentHelper::getParams('com_content', true);
        if (!$this->_item->params->get('access-view')) {
            $readmore = Text::_('JGLOBAL_REGISTER_TO_READ_MORE');
        } elseif ($readmore = $this->_item->alternative_readmore) {
            $readmore .= HTMLHelper::_('string.truncate', $this->_item->title, $globalParams->get('readmore_limit'));
        } elseif ($globalParams->get('show_readmore_title', 0) == 0) {
            $readmore = Text::_('JGLOBAL_READ_MORE');
        } else {
            $readmore = Text::sprintf('JGLOBAL_READ_MORE_TITLE', HTMLHelper::_('string.truncate', $this->_item->title, $globalParams->get('readmore_limit')));
        }
        return $readmore;
    }

    /**
     * Get article readmore link
     *
     * @return string
     */
    public function readmoreLink()
    {
        if ($this->_item->params->get('access-view')) {
            $readmoreLink = Route::_(RouteHelper::getArticleRoute($this->_item->slug, $this->_item->catid));
        } else {
            $link = new Uri(Route::_('index.php?option=com_users&view=login'));
            $returnURL = Route::_(RouteHelper::getArticleRoute($this->_item->slug, $this->_item->catid));
            $link->setVar('return', base64_encode($returnURL));
            $readmoreLink = $link->__toString();
        }
        return $readmoreLink;
    }
}
