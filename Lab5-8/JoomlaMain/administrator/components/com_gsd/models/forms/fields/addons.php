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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Registry\Registry;

require_once JPATH_PLUGINS . '/system/nrframework/helpers/field.php';

class JFormFieldAddons extends NRFormField
{
    /**
     *  Indicates if the field is being referred to plugins
     *
     *  @var  bool
     */
    private $pluginmode;

    /**
     *  Disable input's label
     *
     *  @return  null
     */
    protected function getLabel()
    {
        return;
    }

    /**
     * Method to get a list of options for a list input.
     *
     * @return   string
     */
    protected function getInput()
    {
        $this->pluginmode = $this->get('pluginmode', true) === true;

        if ($this->pluginmode)
        {
            HTMLHelper::_('script', 'com_gsd/addons.js', array('relative' => true, 'version' => 'auto'));
        }

        return LayoutHelper::render('addons', [ 'items' => $this->getItems()]);
    }

    /**
     *  Get addons data from the xml file
     *
     *  @return  array
     */
    private function getItems()
    {
        // Load XML file
        $xmlfile = JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/' . $this->get('xmlfile') . '.xml';

        if (!$xmlItems = simplexml_load_file($xmlfile))
        {
            return;
        }

        $items = array();
        $itemsComingSoon = array();

        foreach ($xmlItems as $key => $item)
        {
            $item = (array) $item;
            $item = new Registry($item["@attributes"]);

            $enabled = false;
            $id      = null;
            $name    = $item->get('name');

            if ($this->pluginmode && ($plugin = NRFramework\Extension::get($name, 'plugin', 'gsd')))
            {
                $enabled = $plugin['enabled'] == 1 ? true : false;
                $id      = $plugin['state'] > -1 ? $plugin['extension_id'] : $id;
            }

            $obj = array(
                'id'          => $id,
                'name'        => $name,
                'isEnabled'   => $enabled,
                'label'       => $item->get('label'),
                'image'       => !empty($item->get('icon')) ? 'https://www.tassos.gr/images/icons/' . $item->get('icon') . '.svg' : 'https://www.tassos.gr/images/integrations/gsd/' . $name . '.png',
                'description' => $item->get('description'),
                'docalias'    => $item->get('docalias'),
                'proonly'     => $item->get('proonly', true),
                'comingsoon'  => $item->get('comingsoon', false)
            );

            if ($obj['comingsoon'])
            {
                $itemsComingSoon[$name] = $obj;
            } else 
            {
                $items[$name] = $obj;
            }
        }

        // Sort by key value both arrays
        ksort($items);
        ksort($itemsComingSoon);

        return array_merge($items, $itemsComingSoon);
    }
}