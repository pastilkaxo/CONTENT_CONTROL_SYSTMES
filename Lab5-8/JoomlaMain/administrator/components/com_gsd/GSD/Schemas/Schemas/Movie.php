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

class Movie extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $props = [
            'genre'     => $this->readRepeatableValue('genre'),
            'creators'  => $this->readRepeatableValue('creators'),
            'directors' => $this->readRepeatableValue('directors'),
            'actors'    => $this->readRepeatableValue('actors'),
            'duration'  => !empty($this->data['duration']) ? 'PT' . $this->data['duration'] . 'M' : null,
            'review'    => $this->data['reviews'],
        ];

        $this->data->loadArray($props);

        parent::initProps();
    }

    private function readRepeatableValue($prop)
    {
        $items = $this->data->get($prop, '');
        $found = [];

        if (!empty($items) && is_string($items))
        {
            $items = explode(',', $items);

            foreach ($items as $item)
            {
                $found[] = (object) [
                    'name' => $item
                ];
            }
        } else 
        {
            $found = $items;
        }

        return $found;
    }
}