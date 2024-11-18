<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD\Schemas\Schemas;

// No direct access
defined('_JEXEC') or die;

use GSD\Helper;

class Recipe extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $props = [
            'prepTime'     => $this->data['prepTime'] ? 'PT' . $this->data['prepTime'] . 'M' : null,
            'cookTime'     => $this->data['cookTime'] ? 'PT' . $this->data['cookTime'] . 'M' : null,
            'totalTime'    => $this->data['totalTime'] ? 'PT' . $this->data['totalTime'] . 'M' : null,
            'ingredient'   => Helper::makeArrayFromNewLine(strip_tags($this->data['ingredient'] ? $this->data['ingredient'] : '')),
            'instructions' => Helper::makeArrayFromNewLine(strip_tags($this->data['instructions'] ? $this->data['instructions'] : ''))
        ];

        $this->data->loadArray($props);

        parent::initProps();
    }
}