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
 
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Forms View Class
 */
class ConvertFormsViewForms extends HtmlView
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
        ConvertForms\Helper::authorise('convertforms.forms.manage', true);

        $this->items         = $this->get('Items');
        $this->state         = $this->get('State');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->config        = \Joomla\CMS\Component\ComponentHelper::getParams('com_convertforms');

        ConvertForms\Helper::addSubmenu('forms');
        $this->sidebar = Sidebar::render();
        $this->moduleID = NRFramework\Extension::getID('mod_convertforms', 'module');

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Set the toolbar
        $this->addToolBar();

        // Load the ConvertForms Templates Library
        new ConvertForms\Library();

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

        // Joomla J4
        if (defined('nrJ4'))
        {
            $toolbar = Toolbar::getInstance('toolbar');

            if ($viewLayout == 'import')
            {
                $title = Text::_('COM_CONVERTFORMS') . ': ' . Text::_('NR_IMPORT_ITEMS');

                Factory::getDocument()->setTitle($title);
                ToolbarHelper::title($title);
                ToolbarHelper::back();
            }
            else
            {
                ToolbarHelper::title(Text::_('COM_CONVERTFORMS') . ": " . Text::_('COM_CONVERTFORMS_FORMS'));
                
                if ($canDo->get('core.create'))
                {
                    $newGroup = $toolbar->dropdownButton('new-group');
                    $newGroup->configure(
                        function (Toolbar $childBar)
                        {
                            $childBar->popupButton('new')->text('NR_NEW')->selector('cfSelectTemplate')->icon('icon-new')->buttonClass('btn btn-success');
                            $childBar->addNew('form.add')->text('COM_CONVERTFORMS_TEMPLATES_BLANK');
                            $childBar->standardButton('import')->text('NR_IMPORT')->task('forms.import')->icon('icon-upload');
                        }
                    );
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
                    $childBar->publish('forms.publish')->listCheck(true);
                    $childBar->unpublish('forms.unpublish')->listCheck(true);
                    $childBar->standardButton('copy')->text('JTOOLBAR_DUPLICATE')->task('forms.duplicate')->listCheck(true);
                    $childBar->standardButton('export')->text('NR_EXPORT')->task('forms.export')->icon('icon-download')->listCheck(true);
                    $childBar->trash('forms.trash')->listCheck(true);
                }

                if ($this->state->get('filter.state') == -2)
                {
                    $toolbar->delete('forms.delete')
                        ->text('JTOOLBAR_EMPTY_TRASH')
                        ->message('JGLOBAL_CONFIRM_DELETE')
                        ->listCheck(true);
                }

                if ($canDo->get('core.admin'))
                {
                    $toolbar->preferences('com_convertforms');
                }

                $toolbar->help('JHELP', false, "http://www.tassos.gr/joomla-extensions/convert-forms/docs");
            }

            return;
        }

        if ($viewLayout == 'import')
        {
            Factory::getDocument()->setTitle(Text::_('COM_CONVERTFORMS') . ': ' . Text::_('NR_IMPORT_ITEMS'));
            ToolbarHelper::title(Text::_('COM_CONVERTFORMS') . ': ' . Text::_('NR_IMPORT_ITEMS'));
            ToolbarHelper::back();
        }
        else
        {
            ToolbarHelper::title(Text::_('COM_CONVERTFORMS') . ": " . Text::_('COM_CONVERTFORMS_FORMS'));

            if ($canDo->get('core.create'))
            {
                ToolbarHelper::addNew('form.add');
            }
            
            if ($canDo->get('core.edit'))
            {
                ToolbarHelper::editList('form.edit');
            }

            if ($canDo->get('core.create'))
            {
                ToolbarHelper::custom('forms.duplicate', 'copy', 'copy', 'JTOOLBAR_DUPLICATE', true);
            }

            if ($canDo->get('core.edit.state') && $state->get('filter.state') != 2)
            {
                ToolbarHelper::publish('forms.publish', 'JTOOLBAR_PUBLISH', true);
                ToolbarHelper::unpublish('forms.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            }

            if ($canDo->get('core.delete') && $state->get('filter.state') == -2)
            {
                ToolbarHelper::deleteList('', 'forms.delete', 'JTOOLBAR_EMPTY_TRASH');
            }
            else if ($canDo->get('core.edit.state'))
            {
                ToolbarHelper::trash('forms.trash');
            }

            if ($canDo->get('core.create'))
            {
                ToolbarHelper::custom('forms.export', 'box-add', 'box-add', 'NR_EXPORT');
                ToolbarHelper::custom('forms.import', 'box-remove', 'box-remove', 'NR_IMPORT', false);
            }

            if ($canDo->get('core.admin'))
            {
                ToolbarHelper::preferences('com_convertforms');
            }
        }

        ToolbarHelper::help("Help", false, "http://www.tassos.gr/joomla-extensions/convert-forms/docs");
    }
}