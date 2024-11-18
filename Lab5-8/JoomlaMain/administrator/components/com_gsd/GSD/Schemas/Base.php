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

namespace GSD\Schemas;

// No direct access
defined('_JEXEC') or die;

use GSD\Helper;
use Joomla\Registry\Registry;
use NRFramework\Functions;
use NRFramework\Cache;
use GSD\MappingOptions;
use Joomla\CMS\Uri\Uri;

class Base
{
    /**
     * The schema properties
     *
     * @var object
     */
    protected $data;

    /**
     * The HTML tags allowed to be used in certain schema properties, such as the headline and the description.
     *
     * @var mixed
     */
    protected $allowed_HTML_tags = null;

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
    protected $rename_properties;

    /**
     * Class constructor
     *
     * @param Registry $data The schema properties
     */
    public function __construct($data = null)
    {
        $this->setData($data);
    }

    /**
     * Return all schema properties
     *
     * @return Registry
     */
    public function get()
    {
        $this->initProps();
        $this->cleanProps();

        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run a housekeeping on each property. Remove unwanted HTML tags and whitespace and encode remaining HTML.
     *
     * @return void
     */
    protected function cleanProps()
    {   
        $props = $this->data->toArray();

        array_walk_recursive($props, function(&$prop)
        {
            if (!is_null($prop)) // Make PHP 8.1 happy.
            {
                $this->cleanProp($prop);
            }
        });

        $this->data = new Registry($props);
    }

    /**
     * Make text safe to be used in a JSON-LD script
     *
     * @param  text $prop   The text to clean
     * 
     * @return void
     */
    protected function cleanProp(&$prop)
    {
        // Remove all <script> tags and their content
        $prop = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $prop);

        // Remove invalid HTML tags
        $prop = strip_tags($prop, $this->allowed_HTML_tags);

        // Convert remaining HTML tags into HTML entities to prevent structured data errors.
        $prop = htmlspecialchars($prop, ENT_QUOTES, 'UTF-8');

        // Remove whitespace
        $prop = preg_replace('/(\s)+/s', ' ', $prop);

        // Remove whitespace from the beginning and end of the prop
        $prop = trim($prop);
    }
    
    /**
     * Prepare common schema properties.
     * 
     * - Rename properties
     * - Add timezone offset and format dates to ISO8601
     * - Strip HTML tags from certain properties
     * - Convert relative paths to absolute URLs
     *
     * @return void
     */
    protected function initProps()
    {
        $this->renameProperties();
        $this->fixMultivalueProperties();
        $this->fixPriceRangeProperties();

        // Fix dates in the Reviews property. Used in schemas: Product, Movie, Local Business
        if ($reviews = $this->data->get('reviews'))
        {
            foreach ($reviews as &$review)
            {
                if (!isset($review['datePublished']))
                {
                    continue;
                }
    
                // Convert date to ISO8601
                $review['datePublished'] = Helper::date($review['datePublished'], true);
            }
    
            $this->data->set('reviews', $reviews);
        }

        // Common properties
        $props = [
            'contentType'   => $this->getName(),
            // Make sure the @id property is unique, to prevent structured data awkwardly merged by the Google Structured Data Testing Tool
            'id'            => Uri::current() . '#' .  $this->getName() . $this->data['snippet_id'],
            'title'         => $this->data['headline'],
            'description'   => $this->data['description'],
            'image'         => Helper::cleanImage(Helper::absURL($this->data->get('image'))), 

            // Author / Publisher
            'authorType'   => 'Person',
            'authorName'   => $this->data['author'],
            'authorUrl'    => isset($this->data['authorUrl']) ? $this->data['authorUrl'] : Uri::current(),

            // Rating
            'ratingValue'   => $this->data['rating_value'],
            'reviewCount'   => $this->data['review_count'],
            'bestRating'    => $this->data['bestRating'],
            'worstRating'   => $this->data['worstRating'],

            // Dates
            'datePublished' => Helper::date($this->data['publish_up'], true),
            'dateCreated'   => Helper::date($this->data['created'], true),
            'dateModified'  => Helper::date($this->data['modified'], true),

            // Site based
            'url'           => Uri::current(),
            'siteurl'       => Helper::getSiteURL(),
            'sitename'      => Helper::getSiteName(),
        ];

        $this->data->merge(new Registry($props));
    }

    /**
     * Some schema properties are declared with the wrong name in Schema XML files. With this method, we attemp to rename those properties with the proper name expected by the JSON class.
     * 
     * @todo Rename all properties in XML files and create a migration script that will update users database. Then, we can get get rid of this method.
     *
     * @return void
     */
    private function renameProperties()
    {
        if (!$this->rename_properties)
        {
            return;
        }

        foreach ($this->rename_properties as $old_property_name => $new_property_name)
        {
            if (!isset($this->data[$old_property_name]))
            {
                continue;
            }

            $this->data[$new_property_name] = $this->data[$old_property_name];

            // Remove old property as we no longer need it.
            unset($this->data[$old_property_name]);
        }
    }

    /**
     * Return the name of this schema type
     *
     * @return string
     */
    private function getName()
    {
        $reflect = new \ReflectionClass($this);
        return strtolower($reflect->getShortName());
    }

    // Temporary workaround. See comments in the Custom_Code class.
    public function onPayloadPrepare(&$payload) {}

    /**
     * This method runs everytime a structured data item is saved in the backend. 
     *
     * @param  array    $data   The data to be stored in the database
     * 
     * @return void
     */
    public function onSave(&$data)
    {
        if (!$data)
        {
            return;
        }

        foreach ($data as $optionKey => &$optionValue)
        {
            $commonDateFieldNames = [
                'publish_up',
                'modified',
                'created',
                'valid_through',
                'validFrom',
                'priceValidUntil'
            ];

            // Skip certain field names
            if (in_array($optionKey, ['birthDate']))
            {
                continue;
            }

            // Find date fields by their name.
            if (strpos(strtolower($optionKey), 'date') === false && !in_array($optionKey, $commonDateFieldNames))
            {
                continue;
            }

            // Only when the mapping option is using the "Fixed Dates" option
            // The "Custom Option" is ignored as it may include a shortcode or some other formatted value.
            if ($optionValue['option'] !== 'fixed' || empty($optionValue['fixed']))
            {
                continue;
            }

            $optionValue['fixed'] = Functions::dateToUTC($optionValue['fixed']);
        }
    }

    /**
     * Finds all properties that accept multiple values per line and convert the string into an array.
     *
     * @return void
     */
    private function fixPriceRangeProperties()
    {
        foreach ($this->getXMLFields() as $key => $field)
        {
            if (!isset($field['real_type']) || $field['real_type'] !== 'pricerange')
            {
                continue;
            }

            if (!$currentValue = $this->data->get($key))
            {
                continue;
            }

            $newValue = explode('-', $currentValue, 2);

            if (count($newValue) == 1)
            {
                $newValue = [$currentValue, $currentValue];
            }

            $this->data->set($key, $newValue);
        }
    }

    /**
     * Finds all properties that accept multiple values per line and convert the string into an array.
     *
     * @return void
     */
    private function fixMultivalueProperties()
    {
        foreach ($this->getXMLFields() as $key => $field)
        {
            if (!isset($field['custom_value_multiple']))
            {
                continue;
            }

            $newValue = Helper::makeArrayFromNewLine($this->data->get($key));

            if (!$newValue || count($newValue) == 1)
            {
                continue;
            }

            $this->data->set($key, $newValue);
        }
    }

    /**
     * Returns a list of all schema properties declared in the XML file
     *
     * @return array
     */
    private function getXMLFields()
    {
        $hash = md5('xmlFields' . $this->getName());

        if (Cache::has($hash))
        {
            return Cache::get($hash);
        }

        $xmlItems = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_gsd/models/forms/contenttypes/' . $this->getName() . '.xml');

        $fields = [];

        foreach ($xmlItems->fieldset->fields->field as $field)
        {
            $field = (array) $field;
            $field = $field["@attributes"];
            $fields[$field['name']] = $field;
        }

        return Cache::set($hash, $fields);
    }
}