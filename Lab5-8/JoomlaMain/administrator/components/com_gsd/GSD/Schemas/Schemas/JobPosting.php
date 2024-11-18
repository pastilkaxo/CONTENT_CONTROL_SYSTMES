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

class JobPosting extends \GSD\Schemas\Base
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
        'locality'    => 'addressLocality',
        'region'      => 'addressRegion',
        'postal_code' => 'postalCode'
    ];

    /**
     * The HTML tags allowed to be used in certain schema properties, such as the headline and the description.
     *
     * @var mixed
     */
    protected $allowed_HTML_tags = '<p><br><ul><li>';

    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $props = [
            'hiring_organization_logo' => Helper::cleanImage(Helper::absURL($this->data['hiring_organization_logo'])),
            'valid_through'            => Helper::date($this->data['valid_through'], true),
            'salary'                   => $this->data['salary'] ? (strpos($this->data['salary'], '-') === false ? Helper::formatPrice($this->data['salary']) : explode('-', $this->data['salary'])) : '',
        ];

        $this->data->loadArray($props);

        parent::initProps();
    }
}