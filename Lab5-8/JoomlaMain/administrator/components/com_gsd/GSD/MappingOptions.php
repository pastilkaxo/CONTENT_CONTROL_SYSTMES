<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD;

defined('_JEXEC') or die('Restricted Access');

use GSD\Helper;
use Joomla\Registry\Registry;
use NRFramework\SmartTags;
use Joomla\CMS\Factory;

/**
 *  Support SmartTags in the Structured Data Item properties
 */
class MappingOptions
{
    /**
     * List of available mapping options
     *
     * @var array
     */
    public static $options = [
        'GSD_INTEGRATION' => [
            'gsd.item.id'           => 'ID',
            'gsd.item.alias'        => 'Alias',
            'gsd.item.headline'     => 'NR_TITLE',
            'gsd.item.description'  => 'NR_TEXT',
            'gsd.item.introtext'    => 'GSD_INTROTEXT',
            'gsd.item.fulltext'     => 'GSD_FULLTEXT',
			'gsd.item.image'        => 'NR_IMAGE',
			'gsd.item.imagetext'    => 'GSD_IMAGE_FROM_TEXT',
			'gsd.item.weight'       => 'GSD_PRODUCT_WEIGHT',
			'gsd.item.weightUnit'   => 'GSD_PRODUCT_WEIGHT_UNIT',
            'url'                   => 'NR_URL',
			'user.id'			    => 'Author ID',
            'user.name'             => 'Author Name',
            'user.firstname'        => 'Author First Name',
            'user.lastname'         => 'Author Last Name',
            'user.login'            => 'Author Username',
            'user.email'            => 'Author Email',
            'gsd.item.created'      => 'Date Created',
            'gsd.item.publish_up'   => 'GSD_DATE_PUBLISH_UP',
            'gsd.item.publish_down' => 'GSD_DATE_PUBLISH_DOWN',
            'gsd.item.modified'     => 'GSD_DATE_MODIFIED',
            'gsd.item.ratingValue'  => 'Rating Value',
            'gsd.item.reviewCount'  => 'Review Count',
            'gsd.item.metakey'      => 'Meta Keywords',	
            'gsd.item.metadesc'     => 'Meta Description'
        ],
        'Page' => [
            'page.title'     => 'Page Title',
            'page.browsertitle' => 'Browser Page Title',
            'page.desc'      => 'Page Meta Description',
            'page.keywords'  => 'Page Meta Keywords',		
			'page.lang'      => 'Page Language',
			'page.generator' => 'Page Generator'
        ],
        'Site Info' => [
			'gsd.sitename'  => 'Site Name',
            'gsd.siteurl'   => 'Site URL',
			'gsd.sitelogo'  => 'Site Logo',
            'site.email'    => 'Site Email'
        ]
    ];

    public static function make($string)
    {
        if (empty($string))
        {
            return;
        }

        return '{' . $string . '}';
    }

    /**
     *  Replaces Smart Tags in a snippet.
     *
     *  @param   Registry  $snippet  The snippet data
     *  @param   Registry  $payload  The tags to use
     *
     *  @return  Registry
     */
    public static function replace($snippet, $payload)
    {
        $payload = $payload->toArray();

        // Null property must be converted to an empty string in order to replace the respective Smart Tag.
        foreach ($payload as $key => $value)
        {
            if (!is_null($value))
            {
                continue;
            }

            $payload[$key] = '';
        }

        // Initialize SmartTags class
        $SmartTags = new SmartTags([
            'technology_tags' => false,
            'user' => isset($payload['created_by']) ? $payload['created_by'] : null]
        );
        
        // Add payload to collection
        $SmartTags->add($payload, 'gsd.item.');

        // Add extension global settings to collection
        $settings = [
            'sitename' => Helper::getSiteName(),
            'siteurl'  => Helper::getSiteURL(),
            'sitelogo' => Helper::getSiteLogo()
        ];
        $SmartTags->add($settings, 'gsd.');

        // Replace Smart Tags now
        $data = $SmartTags->replace($snippet->toArray());

        return new Registry($data);   
    }

    public static function prepare(&$properties)
    {
        foreach ($properties as $key => $property)
        {
            if (!is_object($property) || !isset($property->option))
            {
                continue;
            }

            switch ($property->option)
            {
                

                case 'fixed':
                    if (in_array($key, ['author', 'publisher_name']))
                    {
                        if ($user = Factory::getUser($property->fixed))
                        {
                            $property->fixed = $user->name;
                        }
                    }

                    $value = $property->fixed;

                    break;
                case '_custom_':
                    $value = $property->custom;
                    break;
                case '_disabled_':
                    $value = false; 
                    break;
                default:
                    $value = self::make($property->option);
                    break;
            }

            $properties->set($key, $value);
        }
    }

    /**
     * Add mapping options to the collection
     *
     * @param [type] $options
     * @param [type] $newoptions
     * @param string $group_name
     * @param string $prefix
     *
     * @return void
     */
    public static function add(&$options, $newoptions, $group_name = 'GSD_CUSTOM_FIELDS', $prefix = 'gsd.item.cf.')
    {
		foreach ($newoptions as $key => $newoption)
		{
            $new_key = $prefix . $key;
            $newoptions[$new_key] = $newoption;
            unset($newoptions[$key]);
        }

        $options = array_merge_recursive($options, [$group_name => $newoptions]);
    }
}