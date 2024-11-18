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

use Joomla\CMS\Form\Field\SubformField;
use Joomla\CMS\Factory;

class JFormFieldSchemaCleaner extends SubformField
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getInput()
    {
        Factory::getDocument()->addStyleDeclaration('
            .schemacleaner {
                max-width: 700px;
                box-sizing: border-box;
            }
            .schemacleaner table {
                border-collapse: collapse;
            }
            .schemacleaner th, .schemacleaner td {
                border: 1px solid #ddd !important;
                color:inherit !important;
                font-weight: bold !important;
                vertical-align:middle;
            }
            .schemacleaner * {
                box-sizing: inherit;
            }
            .schemacleaner .control-label {
                display:none;
            }
            .schemacleaner .controls {
                padding:0;
                min-width:auto;
            }
            .schemacleaner .control-group {
                margin:0;
            }
            .schemacleaner thead tr > th:first-child {
                width: 70px !important;
            }
            .schemacleaner thead tr > th:nth-child(2) {
                width: 100% !important;
            }
            .schemacleaner input {
                padding: 14px 10px;
                width: 100% !important;
                max-width: 100% !important;
            }
            .schemacleaner .nrtoggle {
                top: 3px;
                left: 6px;
            }
        ');

        $html = '<div class="schemacleaner">' . parent::getInput() . '</div>';

        return $html;
    }
}