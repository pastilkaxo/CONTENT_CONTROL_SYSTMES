<?php
/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\FormField;

class JFormFieldMap extends FormField
{
    protected function getLabel()
    {
        return;
    }

    protected function getInput()
    {
        $subform_class = (string) $this->element['subform_class'];
        $label = (string) $this->element['label'];
        $description = (string) $this->element['description'];
        $name = (string) $this->element['name'];

        $subform_classes = $subform_class ? ' ' . $subform_class : '';

        $group_parts = explode('.', $this->group);
        $contentType = end($group_parts);
        $this->group .= '.' . $name;

        $xml = new SimpleXMLElement('
            <fields name="' . $contentType . '">
                <fields name="' . $name . '">
                    ' . $this->getOptionField() . '
                    ' . $this->getSubform() . '
                </fields>
            </fields>
        ');

        $this->form->setField($xml);

        // Render XML
        foreach ($xml->fields->field as $key => $field)
        {
            $name = $field->attributes()->name;
            $html[] = $this->form->renderField($name, $this->group);
        }

        return '
            <div class="subform' . $subform_classes . '">
                <div class="subform-parent"> ' . $html[0] . ' </div>
                <div class="subform-child"> ' . implode('', array_slice($html, 1)) . ' </div>
            </div>
        ';
    }

    private function elementHasOption($option_value)
    {
        $options = $this->element->option;

        foreach ($options as $key => $option)
        {
            if ((string) $option->attributes()->value == $option_value)
            {
                return true;
            }
        }

        return false;
    }

    private function subformHasField($name = 'custom')
    {
        if (!$el = $this->element->subform)
        {
            return;
        }

        $fields = $el->children();

        foreach ($fields as $key => $field)
        {
            $field_name = (string) $field->attributes()->name;

            if ($field_name == $name)
            {
                return true;
            }
        }

        return false;
    }

    private function getSubform()
    {
        $xml = '';

        $customFieldMultiple = (bool) $this->element['custom_value_multiple'];

        if ($customFieldMultiple)
        {
            $xml_custom_field = '
                <field name="custom" type="textarea" 
                    showon="option:_custom_"
                    hiddenLabel="true"
                    filter="raw"
                    rows="10"
                    class="' . (string) $this->element['custom_class'] . '"
                    hint="You can enter multiple values, each on a separate line."
                />
            ';
        } else
        {
            $xml_custom_field = '
                <field name="custom" type="text" 
                    showon="option:_custom_"
                    hiddenLabel="true"
                    filter="raw"
                    class="' . (string) $this->element['custom_class'] . '"
                    hint="' . (string) $this->element['hint'] . '"
                />
            ';
        }

        if (!$this->subformHasField())
        {
            $xml = $xml_custom_field;
        }

        if ($subform = $this->element->subform)
        {
            if (count($subform->children()) == 1)
            {
                $el = $subform->children()->field;

                // Add missing showon attribute
                if (is_null($el->attributes()->showon))
                {
                    $el->addAttribute('showon', 'option:_custom_');
                }

                // Add missing hiddenLabel attribute
                if (is_null($el->attributes()->hiddenLabel))
                {
                    $el->addAttribute('hiddenLabel', true);
                }
            }

            $xml .= $subform->children()->asXml();
        }

        

        return $xml;
    }

    private function getOptionField()
    {
        $el = clone $this->element;

        $el->attributes()->name = 'option';
        $el->attributes()->type = 'mappingoptions';

        if ($el['required'])
        {
            $el->attributes()->required = (string) $el['required'];
        }

        unset($el->attributes()->showon);
        unset($el->subform);

        // Add Fixed option
        if ($this->subformHasField('fixed') && !$this->elementHasOption('fixed'))
        {
            $fixed = $el->addChild('option', 'Fixed Option');
            $fixed->addAttribute('value', 'fixed');
        }

        $show_custom_value = is_null($this->element['hidecustomvalue']) ? true : false;
        if ($show_custom_value)
        {
            // Add Custom Value option
            $custom = $el->addChild('option', 'Custom Value');
            $custom->addAttribute('value', '_custom_');
        }

        

        return $el->asXml();
    }
}