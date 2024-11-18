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

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

class JFormFieldTFImageDimensionsControl extends FormField
{
    /**
	 * Renders the input field with the video previewer.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
        $this->assets();

        $isNew = $this->form->getData()->get('id') == 0;

        $by = isset($this->element['by']) ? (string) $this->element['by'] : '';
        $width = isset($this->element['width']) ? (string) $this->element['width'] : '';
        $height = isset($this->element['height']) ? (string) $this->element['height'] : '';
        $hide_disabled_option = isset($this->element['hide_disabled_option']) ? (string) $this->element['hide_disabled_option'] === 'true' : false;
        $disabled_label = isset($this->element['disabled_label']) ? (string) $this->element['disabled_label'] : 'JDISABLED';
        $hide_dropdown = isset($this->element['hide_dropdown']) ? (string) $this->element['hide_dropdown'] === 'true' : false;

		if (is_string($this->value))
        {
            $this->value = json_decode($this->value, true);
        }

        $elName = (string) $this->element['name'];

        $by_field = '';
        if (!$hide_dropdown)
        {
            $by_field = '
            <field name="by" type="list" 
                hiddenLabel="true"
                default="' . ($isNew && isset($this->value['by']) ? $this->value['by'] : (is_null($this->value) ? $by : ''))  .'"
            >
                ' . (!$hide_disabled_option ? '<option value="disabled">' . $disabled_label . '</option>' : '') . '
                <option value="width">NR_RESIZE_BY_WIDTH</option>
                <option value="height">NR_RESIZE_BY_HEIGHT</option>
                <option value="custom">NR_CUSTOM_SIZE</option>
            </field>';
        }
        else
        {
            $by_field = '<field name="by" type="hidden" default="' . ($isNew && isset($this->value['by']) ? $this->value['by'] : $by) . '" />';
        }
        
        $xml = new SimpleXMLElement('
            ' . ($this->group ? '<fields name="' . $this->group . '">' : '') . '
                <fields name="' . $elName . '">
                    ' . $by_field . '
                    <field name="width" type="nrnumber" 
                        hiddenLabel="true"
                        min="0"
                        filter="raw"
                        addon="px"
                        hint="NR_WIDTH"
                        default="' . ($isNew && isset($this->value['width']) ? $this->value['width'] : (is_null($this->value) ? $width : '')) .'"
                        showon="by:width[OR]by:custom"
                    />
                    <field name="x_label" type="note"
                        class="separator-label"
                        description="NR_TIMES_UNICODE"
                        showon="by:custom"
                    />
                    <field name="height" type="nrnumber" 
                        hiddenLabel="true"
                        min="0"
                        filter="raw"
                        addon="px"
                        hint="NR_HEIGHT"
                        default="' . ($isNew && isset($this->value['height']) ? $this->value['height'] : (is_null($this->value) ? $height : '')) .'"
                        showon="by:height[OR]by:custom"
                    />
                </fields>
            ' . ($this->group ? '</fields>' : '') . '
        ');

        $this->form->setField($xml);

        $html = [];

        $fields = isset($xml->fields) ? $xml->fields->field : $xml->field;
        foreach ($fields as $key => $field)
        {
            $name = $field->attributes()->name;
            $html[] = $this->form->renderField($name, ($this->group ? $this->group . '.' : '') . $elName);
        }

        return '<div class="tf-imagedimensions-control">' . implode('', $html) . '</div>';
	}

	private function assets()
	{
		HTMLHelper::stylesheet('plg_system_nrframework/controls/imagedimensions.css', ['relative' => true, 'version' => 'auto']);
		HTMLHelper::script('plg_system_nrframework/controls/imagedimensions.js', ['relative' => true, 'version' => 'auto']);
	}
}