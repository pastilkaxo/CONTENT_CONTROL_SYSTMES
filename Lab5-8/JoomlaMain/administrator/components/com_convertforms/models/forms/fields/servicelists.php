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
use Joomla\CMS\HTML\HTMLHelper;

class JFormFieldServiceLists extends TextField
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of options.
     */
    protected function getInput()
    {
        $this->addMedia();

        return implode(" ", array(
            parent::getInput(),
            '<button type="button" class="btn btn-secondary viewLists">
                <span class="icon-loop"></span> Lists
            </button>
            <ul class="cflists"></ul>'
        ));
    }

    /**
     *  Adds field's JavaScript and CSS files to the document
     */
    private function addMedia()
    {
        HTMLHelper::stylesheet('com_convertforms/servicelists.css', ['relative' => true, 'version' => 'auto']);
        HTMLHelper::script('com_convertforms/servicelists.js', ['relative' => true, 'version' => 'auto']);
    }
}