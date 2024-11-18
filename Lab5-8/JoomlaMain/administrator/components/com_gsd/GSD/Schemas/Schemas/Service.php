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

use Joomla\CMS\Uri\Uri;
use GSD\Helper;

class Service extends \GSD\Schemas\Base
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
        'provider_country'       => 'addressCountry',
        'provider_streetAddress' => 'streetAddress',
        'provider_city'          => 'addressLocality',
        'provider_addressRegion' => 'addressRegion',
        'provider_postalCode'    => 'postalCode'
    ];
    
    /**
     * The HTML tags allowed to be used in certain schema properties, such as the headline and the description.
     *
     * @var string
     */
    protected $allowed_HTML_tags = '<p><br><ul><li><h1><h2><h3><h4><h5><strong><em><b>';

    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $props = [
            'offerPrice'     => Helper::formatPrice($this->data['offerPrice']),
            'provider_image' => Helper::cleanImage($this->data['provider_image']),
            'phone'          => $this->data['provider_phone'],
        ];

        $this->data->loadArray($props);

        parent::initProps();
    }
}