<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;

class JFormFieldCampaigns extends ListField
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of options.
     */
    protected function getOptions() 
    {
        $lists = ConvertForms\Helper::getCampaigns();

        if (!count($lists))
        {
            return;
        }

        $options = array();

        foreach ($lists as $option)
        {
            $options[] = HTMLHelper::_('select.option', $option->id, $option->name);
        }

        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}