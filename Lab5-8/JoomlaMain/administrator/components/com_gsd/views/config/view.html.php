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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Config View
 */
class GSDViewConfig extends HtmlView
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
        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        $this->form    = $this->get('Form');
        $this->config  = ComponentHelper::getParams('com_gsd');
        $this->sidebar = Helper::renderSideBar();

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

        ToolbarHelper::title(Text::_('GSD') . ': ' . Text::_('GSD_CONFIG'));

        if ($canDo->get('core.admin'))
        {
            ToolbarHelper::preferences('com_gsd');
        }

        ToolbarHelper::apply('config.apply');

        ToolbarHelper::help('Help', false, 'https://www.tassos.gr/joomla-extensions/google-structured-data-markup/docs');
    }
}