<?php
/**
 * @package   ShackDefaultFiles
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2024 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of ShackDefaultFiles.
 *
 * ShackDefaultFiles is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ShackDefaultFiles is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ShackDefaultFiles.  If not, see <https://www.gnu.org/licenses/>.
 */

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

FormHelper::loadFieldClass('Spacer');

class JFormFieldSubtitle extends JFormFieldSpacer
{
    /**
     * @inheritDoc
     */
    protected function getLabel()
    {
        $html  = [];
        $class = $this->class ?: sprintf(' class="%s"', $this->class);
        $tag   = $this->element['tag'] ? (string)$this->element['tag'] : 'h4';

        $html[] = '<span class="spacer">';
        $html[] = '<span class="before"></span>';
        $html[] = '<span' . $class . '>';

        if ((string)$this->element['hr'] == 'true') {
            $html[] = '<hr' . $class . '>';
        } else {
            $label = '';

            // Get the label text from the XML element, defaulting to the element name.
            $text = (string)$this->element['label'] ?: (string)$this->element['name'];
            $text = $this->translateLabel ? Text::_($text) : $text;

            // Build the class for the label.
            $class = $this->description ? 'hasTooltip' : '';
            $class = $this->required == true ? $class . ' required' : $class;

            // Add the opening label tag and main attributes attributes.
            $label .= '<' . $tag . ' id="' . $this->id . '-lbl" class="' . $class . '"';

            if ($this->description) {
                // Use description to build a tooltip.
                HTMLHelper::_('bootstrap.tooltip');
                $label .= sprintf(
                    ' title="%s"',
                    HTMLHelper::tooltipText(trim($text, ':'), Text::_($this->description), 0)
                );
            }

            // Add the label text and closing tag.
            $label  .= '>' . $text . '</' . $tag . '>';
            $html[] = $label;
        }

        $html[] = '</span>';
        $html[] = '<span class="after"></span>';
        $html[] = '</span>';

        return implode('', $html);
    }
}
