<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use NP\Factory as NpFactory;

class NicepageViewProduct extends BaseHtmlView
{
    /**
     * @param null $tpl
     *
     * @return mixed
     */
    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $pageId = $app->input->get('page_id', '');
        $productName = $app->input->get('product_name', '');

        $page = NpFactory::getPage($pageId);
        $page->setProductName($productName);
        $page->buildPageElements();
        $this->html = $page->getHtml();
        return parent::display($tpl);
    }
}
