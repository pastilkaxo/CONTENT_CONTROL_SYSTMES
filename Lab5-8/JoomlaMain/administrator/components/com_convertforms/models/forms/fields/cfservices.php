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

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class JFormFieldCFServices extends ListField
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of options.
     */
    protected function getOptions()
    {
        // Trigger all ConvertForms plugins
        PluginHelper::importPlugin('convertforms');

        // Get a list with all available services
        $services = Factory::getApplication()->triggerEvent('onConvertFormsServiceName');

        $options[] = HTMLHelper::_('select.option', '0', Text::_('JDISABLED'));

        // Alphabetically sort services
        asort($services);

        foreach ($services as $option)
        {
            $options[] = HTMLHelper::_('select.option', $option['alias'], $option['name']);
        }

        return array_merge(parent::getOptions(), $options);
    }
}