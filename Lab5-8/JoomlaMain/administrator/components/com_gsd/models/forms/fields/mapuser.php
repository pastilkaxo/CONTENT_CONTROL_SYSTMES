<?php
/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/map.php';

class JFormFieldMapUser extends JFormFieldMap
{
    protected function getInput()
    {
        $el = $this->element;

        // Add Fixed option
        $fixed = $el->addChild('option', 'GSD_FIXED_USER');
        $fixed->addAttribute('value', 'fixed');

        // Create subform
        $subform = $el->addChild('subform');

        // Add Calendar field to subform
        $user = $subform->addChild('field');

        $user->addAttribute('name', 'fixed');
        $user->addAttribute('hiddenLabel', 'true');
        $user->addAttribute('type', 'user');
        $user->addAttribute('showon', 'option:fixed');
        $user->addAttribute('hint', 'John Doe');

        return parent::getInput();
    }
}