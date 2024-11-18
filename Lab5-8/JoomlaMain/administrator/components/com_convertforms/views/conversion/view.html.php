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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Conversion View Class
 */
class ConvertFormsViewConversion extends HtmlView
{
    /**
     * display method of Item view
     * @return void
     */
    public function display($tpl = null) 
    {
        // Access check.
        ConvertForms\Helper::authorise('convertforms.submissions.manage', true);

        // get the Data
        $form = $this->get('Form');
        $item = $this->get('Item');

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Assign the Data
        $this->form = $form;
        $this->item = $item;
        $this->isnew = (!isset($_REQUEST["id"])) ? true : false;

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() 
    {
        $input = Factory::getApplication()->input;
        $input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);

        ToolbarHelper::title($isNew ? Text::_('COM_CONVERTFORMS_NEW_CONVERSION') : Text::_('COM_CONVERTFORMS_EDIT_CONVERSION'));
        ToolbarHelper::apply('conversion.apply');
        ToolbarHelper::save('conversion.save');
        ToolbarHelper::cancel('conversion.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}