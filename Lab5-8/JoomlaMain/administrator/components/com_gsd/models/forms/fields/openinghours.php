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

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;

class JFormFieldOpeningHours extends ListField
{
    /**
     * Get opening hours dropdown options
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            0 => Text::_('GSD_BUSINESSLISTING_NO_HOURS_AVAILABLE'),
            1 => Text::_('GSD_BUSINESSLISTING_ALWAYS_OPEN'),
            2 => Text::_('GSD_BUSINESSLISTING_OPEN_ON_SELECTED_HOURS')
        ];
    }

    /**
     * Render the Opening Hours
     * 
     * @return string
     */
    protected function getInput()
    {
        $name_  = $this->name;

        $this->name .= '[option]';
        $html = parent::getInput();

        // Load css js
        HTMLHelper::_('stylesheet', 'com_gsd/openinghours.css', ['version' => 'auto', 'relative' => true]);
        HTMLHelper::_('script', 'com_gsd/openinghours.js', ['version' => 'auto', 'relative' => true]);
        
        // Draw opening hours
        $html .= '<div class="gsd-oh-container" data-showon=\'[{"field":"' . $this->name . '","values":["2"],"sign":"=","op":""}]\'>';
        $weekdays = \GSD\Helper::getWeekdays(true);
        $this->name = $name_;

        foreach ($weekdays as $day)
        {
            $html .= $this->getRowLayout($day);
        }

        $html .= '</div>';

        return $html;
    }
    
    /**
     * Render a row's layout
     * 
     * @param   string  $day
     * 
     * @return  string
     */
    private function getRowLayout($day)
    {
        $strlower_day = strtolower($day);

        $day_value = isset($this->value[$strlower_day]) ? $this->value[$strlower_day] : [];
        $day_value = new Registry($day_value);

        $checkbox_name = $this->name . '[' . $strlower_day . '][enabled]';
        $checkbox_id = str_replace(array('[', ']'), '_', $checkbox_name);
        $checkbox_id = rtrim($checkbox_id, '_');

        $day_checked = (bool) $day_value->get('enabled', false);
        $day_checked = $day_checked ? ' checked="checked"' : '';

        // Hours Range 1
        $oh_start = $day_value->get('start');
        $oh_end = $day_value->get('end');
        $hours_visible = $oh_start || $oh_end;
        $oh_picker_class = $hours_visible ? ' is-visible' : '';
        $gsd_oh_add_btn_class = $hours_visible ? ' is-hidden' : '';
        
        // Hours Range 2
        $oh_more_start = $day_value->get('start1');
        $oh_more_end = $day_value->get('end1');
        $hours2_visible = $hours_visible && ($oh_more_start || $oh_more_end);
        $oh_more_class = $hours2_visible ? ' is-visible' : '';
        $gsd_oh_more_btn_class = $hours_visible && !$hours2_visible ? ' is-visible' : ' is-hidden';

        return '
            <div class="gsd-oh-row">
                <input type="checkbox"' . $day_checked . ' value="1" id="' . $checkbox_id . '" name="' . $checkbox_name . '" />
                <label for="' . $checkbox_id . '" id="' . $checkbox_id . '-lbl">' . $day . '</label>
                <a href="#" class="gsd-oh-add-btn icon-save-new' . $gsd_oh_add_btn_class . '">
                    <span class="add_hours">' . Text::_('GSD_LOCALBUSINESS_ADD_HOURS_BTN') . '</span>
                </a>
                <div class="oh-picker first' . $oh_picker_class . '">
                    ' . $this->renderTimePicker($day, $oh_start, 'start') . '
                    ' . $this->renderTimePicker($day, $oh_end, 'end') . '
                </div>
                <a href="#" class="gsd-oh-more-btn icon-save-new' . $gsd_oh_more_btn_class . '"></a>
                <div class="oh-more' . $oh_more_class . '">
                    <div class="oh-picker">
                    ' . $this->renderTimePicker($day, $oh_more_start, 'start1') . '
                    ' . $this->renderTimePicker($day, $oh_more_end, 'end1') . '
                    </div>
                    <a href="#" class="gsd-oh-remove-btn icon-delete"></a>
                </div>
            </div>
        ';
    }

    /**
     * Renders a Time Picker
     * 
     * @param   string  $day   The day of the week
     * @param   string  $more  Whether if it's a second time of the day
     * @param   string  $type  Whether if its start or end time
     * 
     * @return  string
     */
    private function renderTimePicker($day, $value, $name)
    {
        include_once JPATH_PLUGINS . '/system/nrframework/fields/time.php';

        $field = new \JFormFieldNR_Time;
        $field_name = $this->name . '[' . strtolower($day) . '][' . $name . ']';
        $field->setValue($value);

        $element = new \SimpleXMLElement('
            <field name="' . $field_name . '" type="nr_time"
                hiddenLabel="true"
            />
        ');
            
        $field->setup($element, null);

        return $field->__get('input');
    }
}