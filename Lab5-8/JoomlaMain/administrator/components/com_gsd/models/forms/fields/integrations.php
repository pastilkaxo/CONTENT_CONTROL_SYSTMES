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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\Language\Text;

class JFormFieldIntegrations extends GroupedlistField
{
    protected $layout = 'joomla.form.field.groupedlist-fancy-select';

    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of options.
     */
    protected function getGroups()
    {
        $groups = [];
        
        // Get a list with all available plugins
        $plugins = Helper::getPlugins();

        $showselect = (string) $this->element['showselect'];
        if ($showselect === 'true')
        {
            $groups[''][] = HTMLHelper::_('select.option', '', '- ' . Text::_('GSD_INTEGRATION_SELECT') . ' -');
        } else 
        {
            // If we don't have a value get the default plugin
            $this->value = empty($this->value) ? Helper::getDefaultPlugin() : $this->value;
        }

        // Sort alphabetically
        asort($plugins);
        
        foreach ($plugins as $option)
        {
            $groups[''][] = HTMLHelper::_('select.option', $option["alias"], $option["name"]);
        }

        return $groups;
    }
    
    protected function getInput()
    {
		$this->class = '" search-placeholder="' . Text::_('GSD_SEARCH_INTEGRATIONS');

        return '<div class="d-flex gap-1"> ' . parent::getInput() . '</div>';
    }
}