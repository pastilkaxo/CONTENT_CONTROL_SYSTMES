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

use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class JFormFieldContentTypes extends GroupedlistField
{
    protected $layout = 'joomla.form.field.groupedlist-fancy-select';
    
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of options.
     */
    protected function getGroups()
    {
        $contentTypes = GSD\Helper::getContentTypes();

        $groups = [];
        $showselect = (string) $this->element['showselect'];
        if ($showselect === 'true')
        {
            $groups[''][] = HTMLHelper::_('select.option', '', '- ' . Text::_('GSD_CONTENT_TYPE_SELECT') . ' -');
        }

        foreach ($contentTypes as $contentType)
        {
            $groups[''][] = HTMLHelper::_('select.option', $contentType, Text::_('GSD_' . strtoupper($contentType)));
        }

        return $groups;
    }

    protected function getInput()
    {
		$this->class = '" search-placeholder="' . Text::_('GSD_SEARCH_CONTENT_TYPES');

        $showhelp = (string) $this->element['showhelp'];
        if ($showhelp === 'false')
        {
            return parent::getInput();
        }

        $doc = Factory::getDocument();
        $doc->addScriptDeclaration('
            document.addEventListener("DOMContentLoaded", function() {
                const select = document.querySelector("#' . $this->id . '");

                select.addEventListener("change", function(e) {
                    gsd_set_help_value(e.target.value);
                });
                
                // set initialvalue to help URL
                gsd_set_help_value(select.value);

                function gsd_set_help_value(content_type) {
                    href = "https://www.tassos.gr/joomla-extensions/google-structured-data-markup/docs/" + content_type.replace("_", "") + "-schema";
                    document.querySelector(".contentTypeHelp").href = href;
                }
            });
        ');

        return '
            <div class="d-flex gap-1"> ' . parent::getInput() . '
                <a class="btn btn-secondary contentTypeHelp" target="_blank" title="' . Text::_('GSD_CONTENTTYPE_HELP') . '">
                    <span class="icon-help" style="margin-right:0;"></span>
                </a>
            </div>
        ';
    }
}