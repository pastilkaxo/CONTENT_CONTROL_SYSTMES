<?php
/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/map.php';

class JFormFieldMapDate extends JFormFieldMap
{
    protected function getInput()
    {
        $el = $this->element;

        $dateFormat = $el->attributes()->format ? (string) $el->attributes()->format : '%Y-%m-%d %H:%M:%S';
        
        // Add Fixed option
        $fixed = $el->addChild('option', 'GSD_FIXED_DATE');
        $fixed->addAttribute('value', 'fixed');
        
        // Create subform
        $subform = $el->addChild('subform');
        
        // Add Calendar field to subform
        $calendar = $subform->addChild('field');
        
        $calendar->addAttribute('name', 'fixed');
        $calendar->addAttribute('hiddenLabel', 'true');
        $calendar->addAttribute('type', 'calendar');
        $calendar->addAttribute('hint', '0000-00-00 00:00:00');
        $calendar->addAttribute('showtime', 'true');
        $calendar->addAttribute('filter', 'user_utc'); // Date is supposed to be stored in UTC in the database
        $calendar->addAttribute('showon', 'option:fixed');
        $calendar->addAttribute('weeknumbers', 'false');
        $calendar->addAttribute('format', $dateFormat);

        return parent::getInput();
    }
}