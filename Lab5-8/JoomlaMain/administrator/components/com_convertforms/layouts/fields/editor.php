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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

HTMLHelper::script('com_convertforms/field_editor.js', ['relative' => true , 'version' => 'auto']);
HTMLHelper::stylesheet('com_convertforms/field_editor.css', ['relative' => true , 'version' => 'auto']);

Factory::getDocument()->addStyleDeclaration('
    #cf_' . $form['id'] . ' .cf-control-group[data-key="' . $field->key . '"] {
        --height:' . $field->height . 'px
    }
');

echo $field->richeditor;