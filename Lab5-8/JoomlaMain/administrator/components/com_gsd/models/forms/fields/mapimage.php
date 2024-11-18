<?php
/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/map.php';

class JFormFieldMapImage extends JFormFieldMap
{
    protected function getInput()
    {
        $el = $this->element;

        $el->addAttribute('hint', '/images/post.png');
        $el->addAttribute('custom_class', 'input-xlarge');

        // Add Fixed option
        $fixed = $el->addChild('option', 'GSD_FIXED_IMAGE');
        $fixed->addAttribute('value', 'fixed');

        // Create subform
        $subform = $el->addChild('subform');

        // Add Media field to subform
        $media = $subform->addChild('field');

        $media->addAttribute('name', 'fixed');
        $media->addAttribute('hiddenLabel', 'true');
        $media->addAttribute('type', 'media');
        $media->addAttribute('showon', 'option:fixed');
        $media->addAttribute('preview', 'tooltip');
        $media->addAttribute('class', 'input-xlarge');

        return parent::getInput();
    }
}