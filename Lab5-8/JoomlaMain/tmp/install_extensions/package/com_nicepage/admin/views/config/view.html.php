<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Class NicepageViewConfigHtml
 */
class NicepageViewConfig extends BaseHtmlView
{
    /**
     * Render display html page
     *
     * @param null $tpl Template name
     */
    public function display($tpl = null)
    {
        HTMLHelper::_('jquery.framework');
        HTMLHelper::_('bootstrap.popover', '.hasPopover', ['trigger' => 'hover focus']);

        ToolbarHelper::title(Text::_('COM_NICEPAGE_CONFIGURATION'), 'equalizer nicepage config');
        $this->adminUrl = dirname(dirname((Uri::current()))) . '/administrator';

        $params = NicepageHelpersNicepage::getConfig();

        $disableAutosave = isset($params['siteStyleCssParts']) ? true : false; // autosave disable for new user
        if (isset($params['disableAutosave'])) {
            $disableAutosave = $params['disableAutosave'] == '1' ? true : false;
        }
        $default = '<select class="nicepage-select-autosave" name="nicepage-select-autosave" id="nicepage-select-autosave"><option value="1" %1$s>Off</option><option value="0" %2$s>On</option></select>';
        $this->disableAutoSaveTemplate = $disableAutosave ? Text::sprintf($default, 'selected', '') : Text::sprintf($default, '', 'selected');

        $this->jQuery = '';
        if (isset($params['jquery'])) {
            $this->jQuery = $params['jquery'] == '1'? 'checked' : '';
        };

        $this->autoResponsive = 'checked';
        if (isset($params['autoResponsive'])) {
            $this->autoResponsive = $params['autoResponsive'] == '1' ? 'checked' : '';
        }

        $this->templateOptions = '';
        $pageView = 'landing';
        if (isset($params['pageType'])) {
            $pageView = $params['pageType'];
        }
        switch($pageView) {
        case 'landing':
            $this->templateOptions = Text::sprintf('COM_NICEPAGE_CONFIG_TEMPLATE_OPTIONS', 'selected', '', '');
            break;
        case 'landing_with_header_footer':
            $this->templateOptions = Text::sprintf('COM_NICEPAGE_CONFIG_TEMPLATE_OPTIONS', '', 'selected', '');
            break;
        case 'default':
            $this->templateOptions = Text::sprintf('COM_NICEPAGE_CONFIG_TEMPLATE_OPTIONS', '', '', 'selected');
            break;
        }

        NicepageHelpersNicepage::addSubmenu('config');
        $this->sidebar = Sidebar::render();

        return parent::display($tpl);
    }
}
