<?php

/**
 * @package         Convert Forms
 * @version         4.4.6 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Conversions View
 */
class ConvertFormsViewConversions extends HtmlView
{
    /**
     * Items view display method
     * 
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     * 
     * @return  mixed  A string if successful, otherwise a JError object.
     */
    public function display($tpl = null) 
    {
		// Access check.
        ConvertForms\Helper::authorise('convertforms.submissions.manage', true);

        $this->items         = $this->get('Items');
        $this->state         = $this->get('State');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->config        = \Joomla\CMS\Component\ComponentHelper::getParams('com_convertforms');

        ConvertForms\Helper::addSubmenu('conversions');
        $this->sidebar = Sidebar::render();

        // Trigger all ConvertForms plugins
        PluginHelper::importPlugin('convertforms');
        Factory::getApplication()->triggerEvent('onConvertFormsServiceName');

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
     *  Add Toolbar to layout
     */
    protected function addToolBar() 
    {
        $canDo = ConvertForms\Helper::getActions();
        $state = $this->get('State');
        $viewLayout = Factory::getApplication()->input->get('layout', 'default');

        ToolbarHelper::title(Text::_('COM_CONVERTFORMS') . ": " . Text::_('COM_CONVERTFORMS_SUBMISSIONS'), "users");

        ConvertForms\Export::renderModal();

        // Joomla J4
        if (defined('nrJ4'))
        {
            $toolbar = Toolbar::getInstance('toolbar');

            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fas fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();
            
            if ($canDo->get('core.edit.state'))
            {
                $childBar->publish('conversions.publish')->listCheck(true);
                $childBar->unpublish('conversions.unpublish')->listCheck(true);
                $childBar->archive('conversions.archive')->listCheck(true);
                $childBar->trash('conversions.trash')->listCheck(true);
            }

            $toolbar
                ->standardButton('export')
                ->text('COM_CONVERTFORMS_LEADS_EXPORT')
                ->icon('icon-download')
                ->listCheck(false);

            if ($this->state->get('filter.state') == -2)
            {
                $toolbar->delete('conversions.delete')
                    ->text('JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }

            if ($canDo->get('core.admin'))
            {
                $toolbar->preferences('com_convertforms');
            }

            $toolbar->help('JHELP', false, 'http://www.tassos.gr/joomla-extensions/convert-forms/docs');

            return;
        }

        if ($canDo->get('core.edit.state') && $state->get('filter.state') != 2)
        {
            ToolbarHelper::publish('conversions.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('conversions.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            ToolbarHelper::archiveList('conversions.archive');
        }

        if ($canDo->get('core.delete') && $state->get('filter.state') == -2)
        {
            ToolbarHelper::deleteList('', 'conversions.delete', 'JTOOLBAR_EMPTY_TRASH');
        }
        else if ($canDo->get('core.edit.state'))
        {
            ToolbarHelper::trash('conversions.trash');
        }

        if ($canDo->get('core.edit'))
        {
            ToolbarHelper::editList('conversion.edit');
        }

        ToolbarHelper::custom('', 'box-add toolbarexportmodal', '', 'COM_CONVERTFORMS_LEADS_EXPORT');

        if ($canDo->get('core.admin'))
        {
            ToolbarHelper::preferences('com_convertforms');
        }

        ToolbarHelper::help("Help", false, "http://www.tassos.gr/joomla-extensions/convert-forms/docs");
    }
}