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

use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\Language\Text;

class JFormFieldCSSClasses extends TextField
{
    /**
     *  Layout class prefix
     *
     *  @var  string
     */
    private $class_prefix = 'cf-';

    /**
     *  List of available layout classes
     *
     *  @var  array
     */
    private $layouts = array(
        'one-half, one-half',
        'one-third, one-third, one-third',
        'one-fourth, one-fourth, one-fourth, one-fourth',
        'one-third, two-thirds',
        'two-thirds, one-third',
        'one-fourth, one-fourth, two-fourths',
        'two-fourths, one-fourth, one-fourth',
        'one-fourth, two-fourths, one-fourth'
    );

    /**
     *  Display a layouts button next to the field label
     *
     *  @return  string
     */
    protected function getLabel()
    {
        $html = '
            <a class="cf-layout-btn" href="#" 
                data-show-label="' . Text::_('COM_CONVERTFORMS_SHOW_LAYOUTS') . '"
                data-hide-label="' . Text::_('COM_CONVERTFORMS_HIDE_LAYOUTS') . '">
                    ' . Text::_('COM_CONVERTFORMS_SHOW_LAYOUTS') . 
            '</a>';

        return parent::getLabel() . $html;
    }

    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getInput()
    {
        $uniqueClasses = array();

        $html = '
            <div class="cf-layout-classes">
                <span>' . Text::_('COM_CONVERTFORMS_SELECT_LAYOUT') . '</span>
                <div class="cf-layout-list">';

                foreach ($this->layouts as $key => $layout)
                {
                    $classes = explode(',', $layout);

                    $html .= '<div class="cf-layout">';

                    foreach ($classes as $key => $class)
                    {
                        $class = trim($class);
                        $classLabel = ucfirst(str_replace('-', ' ', $class));

                        $html .= ' <span title="' . $classLabel . '" class="' . $this->class_prefix . $class . '"></span>';
                    }

                    $html .= '</div>';
                }

        $html .= '</div></div>';

        return $html . parent::getInput();
    }
}