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

class LocalBusiness extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $props = [
            'name'   => $this->data->get('name', Helper::getSiteName()),
            'geo'    => array_map('trim', explode(',', $this->data->get('geo', ''), 2)),
            'review' => $this->data->get('reviews')
        ];

        $this->data->loadArray($props);

        parent::initProps();
    }
}