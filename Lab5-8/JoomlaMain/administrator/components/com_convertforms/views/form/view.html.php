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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * Item View
 */
class ConvertFormsViewForm extends HtmlView
{
    /**
     * display method of Item view
     * @return void
     */
    public function display($tpl = null) 
    {
		// Access check.
        ConvertForms\Helper::authorise('convertforms.forms.manage', true);

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Assign the Data
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->isnew = (!isset($_REQUEST["id"])) ? true : false;
        $this->tabs  = $this->get('Tabs');
        $this->name  = $this->form->getValue('name') ?: Text::_('COM_CONVERTFORMS_UNTITLED_BOX');

        PluginHelper::importPlugin('convertformstools');
		Factory::getApplication()->triggerEvent('onConvertFormsBackendEditorDisplay');

        $title = Text::_('COM_CONVERTFORMS') . ' - ' . ($this->isnew ? Text::_("COM_CONVERTFORMS_UNTITLED_BOX") : $this->name);

        Factory::getDocument()->setTitle($title);
        ToolbarHelper::title($title);

        // Display the template
        parent::display($tpl);
    }
}