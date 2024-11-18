<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use NP\Utility\Utility;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Class NicepageViewImport
 */
class NicepageViewImport extends BaseHtmlView
{
    /**
     * Render display html page
     *
     * @param null $tpl Template name
     */
    public function display($tpl = null)
    {
        HTMLHelper::_('jquery.framework');

        $this->maxRequestSize = Utility::getMaxRequestSize();
        $this->adminUrl = dirname(dirname((Uri::current()))) . '/administrator';
        ToolbarHelper::title(Text::_('COM_NICEPAGE_IMPORT_HEADER'));

        NicepageHelpersNicepage::addSubmenu('import');
        $this->sidebar = Sidebar::render();

        return parent::display($tpl);
    }
}