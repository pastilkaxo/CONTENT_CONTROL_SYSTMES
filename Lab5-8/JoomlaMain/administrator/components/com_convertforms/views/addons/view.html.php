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
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;

/**
 * Addons View
 */
class ConvertFormsViewAddons extends HtmlView
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
		// Access check.
		ConvertForms\Helper::authorise('convertforms.addons.manage', true);

        $this->config = \Joomla\CMS\Component\ComponentHelper::getParams('com_convertforms');

        ConvertForms\Helper::addSubmenu('addons');
        $this->sidebar = Sidebar::render();
        $this->availableAddons = $this->getAddons();

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
     *  Get list of all available addons
     *
     *  @return  array
     */
    function getAddons()
    {
        // Load XML file
        $xmlfile = JPATH_COMPONENT_ADMINISTRATOR . '/ConvertForms/xml/addons.xml';

        if (!file_exists($xmlfile))
        {
            return;
        }

        if (!$xmlItems = simplexml_load_file($xmlfile))
        {
            return;
        }

        $addons = [];

        foreach ($xmlItems as $key => $item)
        {
            $item = (array) $item;
            $item = new Registry($item["@attributes"]);

            $extensionType   = $item->get("extension_type", "plugin");
            $extensionFolder = $item->get("extension_folder", "convertforms");
            $extensionName   = $item->get("name");
            $extensionID     = NRFramework\Extension::getID($extensionName, $extensionType, $extensionFolder);
            $backEndURL      = "";

            if ($extensionID)
            {
                if ($extensionType == "plugin")
                {
                    $backEndURL = "index.php?option=com_plugins&task=plugin.edit&extension_id=" . $extensionID;
                }
            }

            $url = $item->get("customurl") ?: NRFramework\Functions::getUTMURL($item->get("url", "https://www.tassos.gr/joomla-extensions/convert-forms/"));

            $obj = array(
                "name"         => $extensionName,
                "label"        => $item->get("label"),
                "description"  => $item->get("description"),
                "image"        => $item->get("image"),
                "url"          => $url,
                "customlabel"  => $item->get("customlabel"),
                "docalias"     => $item->get("docalias"),
                "extensionid"  => $extensionID,
                "backendurl"   => Uri::base() . $backEndURL,
                "proonly"      => $item->get("proonly", true),
                "comingsoon"   => $item->get("comingsoon", false)
            );

            $addons[] = $obj;
        }

        asort($addons);

        return $addons;
    }

    /**
     *  Add Toolbar to layout
     */
    protected function addToolBar() 
    {
        $canDo = ConvertForms\Helper::getActions();

        ToolbarHelper::title(Text::_('COM_CONVERTFORMS') . ": " . Text::_('COM_CONVERTFORMS_ADDONS'), "puzzle");

        if ($canDo->get('core.admin'))
        {
            ToolbarHelper::preferences('com_convertforms');
        }

        ToolbarHelper::help("Help", false, "http://www.tassos.gr/joomla-extensions/convert-forms/docs");
    }
}