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
use Joomla\String\StringHelper;
class Course extends \GSD\Schemas\Base
{
    /**
     * A key => value array with schema properties that needs to be renamed.
     * 
     * The left value represents the name of the property as defined in the schema's XML file.
     * The right value represents the name of the property as it's expected in JSON class.
     *  
     * @Todo - We should rename all properties directly in each schema XML file and then get rid of this property.
     * 
     * @var array
     */
    protected $rename_properties = [
        'country'     => 'addressCountry',
        'address'     => 'streetAddress',
        'locality'    => 'addressLocality',
        'region'      => 'addressRegion',
        'postal_code' => 'postalCode',
        'start_date'  => 'startDate',
        'end_date'    => 'endDate'
    ];

    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $props = [
            'courseWorkload' => Helper::convert_to_ISO8601($this->data->get('courseWorkload'), 'H'),
            'validFrom' => Helper::date($this->data->get('validFrom'), true),
            'start_date' => Helper::date($this->data->get('start_date'), true),
            'end_date'   => Helper::date($this->data->get('end_date'), true),
        ];

        $this->data->loadArray($props);

        parent::initProps();
    }

    /**
     * Beyond the default housekeeping, limit the characters in the headline property to 110 in order to comply with Google's guidelines.
     * 
     * Reference: https://developers.google.com/search/docs/appearance/structured-data/course
     *
     * @return void
     */
    protected function cleanProps()
    {
        parent::cleanProps();

        $this->data->set('description', StringHelper::substr($this->data->get('description'), 0, 500));
    }
}