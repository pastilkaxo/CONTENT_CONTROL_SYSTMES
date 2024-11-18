<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/
defined('_JEXEC') or die('Restricted access');

use GSD\Helper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
 
/**
 * Items View
 */
class GSDViewItems extends HtmlView
{
    /**
     * Items view display method
     * 
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     * 
     * @return  mixed  A string if successful, otherwise a JError object.
     */
    function display($tpl = null) 
    {
        $this->items         = $this->get('Items');
        $this->state         = $this->get('State');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->config        = Helper::getParams();
        $this->sidebar       = Helper::renderSideBar();

        if (defined('nrJ4'))
        {
            $tpl = '4';
        }

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
        $canDo = Helper::getActions();
        $state = $this->get('State');
        $viewLayout = Factory::getApplication()->input->get('layout', 'default');

        ToolBarHelper::title(Text::_('GSD') . ': ' . Text::_('GSD_ITEMS'));

        // Joomla J4
        if (defined('nrJ4'))
        {
            $toolbar = Toolbar::getInstance('toolbar');

            if ($canDo->get('core.create'))
            {
                $toolbar->addNew('item.add');
            }

            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fas fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();
            
            if ($canDo->get('core.edit.state'))
            {
                $childBar->publish('items.publish')->listCheck(true);
                $childBar->unpublish('items.unpublish')->listCheck(true);
                $childBar->standardButton('copy')->text('JTOOLBAR_DUPLICATE')->task('items.duplicate')->listCheck(true);
                $childBar->trash('items.trash')->listCheck(true);
            }

            if ($this->state->get('filter.state') == -2)
            {
                $toolbar->delete('items.delete')
                    ->text('JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }

            if ($canDo->get('core.admin'))
            {
                $toolbar->preferences('com_gsd');
            }

            $toolbar->help('JHELP', false, 'http://www.tassos.gr/joomla-extensions/google-structured-data-markup/docs');

            return;
        }

        if ($canDo->get('core.create'))
        {
            ToolbarHelper::addNew('item.add');
        }
        
        if ($canDo->get('core.edit'))
        {
            ToolbarHelper::editList('item.edit');
        }

        if ($canDo->get('core.create'))
        {
            ToolbarHelper::custom('items.duplicate', 'copy', 'copy', 'JTOOLBAR_DUPLICATE', true);
        }

        if ($canDo->get('core.edit.state') && $state->get('filter.state') != 2)
        {
            ToolbarHelper::publish('items.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('items.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        if ($canDo->get('core.delete') && $state->get('filter.state') == -2)
        {
            ToolbarHelper::deleteList('', 'items.delete', 'JTOOLBAR_EMPTY_TRASH');
        }
        else if ($canDo->get('core.edit.state'))
        {
            ToolbarHelper::trash('items.trash');
        }

        if ($canDo->get('core.admin'))
        {
            ToolbarHelper::preferences('com_gsd');
        }

        ToolbarHelper::help("Help", false, "https://www.tassos.gr/joomla-extensions/google-structured-data-markup/docs");
    }
}