<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use NP\Editor\Editor;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Class NicepageViewTheme
 */
class NicepageViewTheme extends BaseHtmlView
{
    /**
     * Render display html page
     *
     * @param null $tpl Template name
     */
    public function display($tpl = null)
    {
        HTMLHelper::_('jquery.framework');

        $editor = new Editor();
        $editor->addCommonScript();
        $editor->addLinkDialogScript();
        $editor->addDataBridgeScript();
        $editor->addMainScript();
        $editor->includeScripts();

        return parent::display($tpl);
    }
}