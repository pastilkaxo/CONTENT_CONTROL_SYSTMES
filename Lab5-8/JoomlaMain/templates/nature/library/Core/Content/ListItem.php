<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

Core::load("Core_Content_Item");

abstract class CoreContentListItem extends CoreContentItem
{
    public $intro;

    protected function __construct($component, $componentParams, $article, $articleParams)
    {
        parent::__construct($component, $componentParams, $article, $articleParams);
        $this->isPublished = 0 != $this->_article->state;
        $this->titleLink = $this->_articleParams->get('link_titles') && $this->_articleParams->get('access-view')
            ? Route::_(RouteHelper::getArticleRoute($this->_article->slug, $this->_article->catid))
            : '';
        $this->intro = $this->_article->introtext;
        if ($this->_articleParams->get('show_readmore') && $this->_article->readmore) {
            if (!$this->_articleParams->get('access-view'))
                $this->readmore = Text::_('JGLOBAL_REGISTER_TO_READ_MORE');
            elseif ($this->readmore = $this->_article->alternative_readmore) {
                if ($this->_articleParams->get('show_readmore_title', 0) != 0)
                    $this->readmore .= HTMLHelper::_('string.truncate', ($this->_article->title), $this->_articleParams->get('readmore_limit'));
            } elseif ($this->_articleParams->get('show_readmore_title', 0) == 0)
                $this->readmore = Text::_('JGLOBAL_READ_MORE');
            else {
                $this->readmore = Text::sprintf('JGLOBAL_READ_MORE_TITLE', HTMLHelper::_('string.truncate', $this->_article->title, $this->_articleParams->get('readmore_limit')));
            }
            if ($this->_articleParams->get('access-view')){
                $link = Route::_(RouteHelper::getArticleRoute($this->_article->slug, $this->_article->catid));
                $this->readmoreLink = $link;
            } else {
                $menu = Factory::getApplication()->getMenu();
                $active = $menu->getActive();
                $itemId = $active->id;
                $link1 = Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
                $returnURL = Route::_(RouteHelper::getArticleRoute($this->_article->slug, $this->_article->catid));
                $link = new Uri($link1);
                $link->setVar('return', base64_encode($returnURL));
                $this->readmoreLink = $link->__toString();
            }
        } else {
            $this->readmore = '';
            $this->readmoreLink = '';
        }
    }

    public function intro($intro)
    {
        return $intro;
    }
}
