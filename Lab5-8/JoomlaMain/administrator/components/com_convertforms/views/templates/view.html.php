<?php

/**
 * @package         Convert Forms
 * @version         4.4.6 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * Templates View
 */
class ConvertFormsViewTemplates extends HtmlView
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
        $this->config    = ComponentHelper::getParams('com_convertforms');
        $this->templates = $this->getTemplates();

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
     *  Get list of all available templates
     *
     *  @return  array
     */
    function getTemplates()
    {
        $templatesPath = JPATH_ROOT . "/media/com_convertforms/templates/";
        $xmlFile  = $templatesPath . "templates.xml";

        if (!file_exists($xmlFile))
        {
            return;
        }

        if (!$templateGroups = simplexml_load_file($xmlFile))
        {
            return;
        }

        $templates = array();

        foreach ($templateGroups as $templateGroup)
        {
            $templateGroupName = (string) $templateGroup["name"];

            foreach ($templateGroup as $template)
            {
                $templateName = (string) $template["name"];

                // Check if template thumb file exists
                if (!file_Exists($templatesPath . $templateName . ".jpg"))
                {
                    continue;
                }

                $templateInfo = array(
                    "name"  => $templateName,
                    "label" => (string) $template["label"],
                    "thumb" => Uri::root() . 'media/com_convertforms/templates/' . $templateName . '.jpg',
                    "link"  => Uri::base() . "index.php?option=com_convertforms&view=form&layout=edit&template=" . $templateName
                );

                // Check if template thumb file exists
                if (!file_Exists($templatesPath . $templateName . ".cnvf"))
                {
                    unset($templateInfo["link"]);
                }

                $templates[$templateGroupName][] = $templateInfo;
            }
        }

        return $templates;
    }

    /**
     *  Add Toolbar to layout
     */
    protected function addToolBar() 
    {
        ToolbarHelper::title(Text::_('COM_CONVERTFORMS') . ": " . Text::_('COM_CONVERTFORMS_TEMPLATES'));
    }
}