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
use NRFramework\Functions;
use Joomla\CMS\Factory;

class Review extends \GSD\Schemas\Base
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
        'address' => 'streetAddress'
    ];

    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        // We need a better and more dynamic way to handle Repeatable Field values.
        // We can move this block to MappingOptions somehow.
        $actors = $this->data->get('actors', '');
        $actors_ = [];

        if (!empty($actors) && is_string($actors))
        {
            $actors = explode(',', $actors);

            foreach ($actors as $actor)
            {
                $actors_[] = (object)[
                    'name' => $actor
                ];
            }
        } else 
        {
            $actors_ = $actors;
        }

        $props = [
            'itemReviewedPublishedDate' => $this->data['item_reviewed_published_date'],
            'movie_director'   => $this->data['item_reviewed_movie_director'],
            'product_sku'      => $this->data['item_reviewed_product_sku'],
            'product_brand'    => $this->data['item_reviewed_product_brand'],
            'product_description' => $this->data['item_reviewed_product_description'],
            'currency'         => $this->data['item_reviewed_product_currency'],
            'condition'        => $this->data['item_reviewed_product_offeritemcondition'],
            'availability'     => $this->data['item_reviewed_product_offeravailability'],
            'offerprice'       => $this->data['item_reviewed_product_offerprice'],
            'pricevaliduntil'  => $this->data['item_reviewed_product_pricevaliduntil'],
            'book_author'      => $this->data['item_reviewed_book_author'],
            'book_author_url'  => $this->data['item_reviewed_book_author_url'],
            'book_isbn'        => $this->data['item_reviewed_book_isbn'],
            'review'           => $this->data['reviews'],
            'actors'           => $actors_,
            'language_code'    => explode('-', Factory::getLanguage()->getTag())[0]
        ];

        $this->data->loadArray($props);

        parent::initProps();
    }

    /**
     * This method runs everytime a structured data item is saved in the backend. 
     *
     * @param  array    $data   The data to be stored in the database
     * 
     * @return void
     */
    public function onSave(&$data)
    {
        parent::onSave($data);

        if ($data['item_reviewed_product_pricevaliduntil']['option'] == 'fixed')
        {
            $data['item_reviewed_product_pricevaliduntil']['fixed'] = Functions::dateToUTC($data['item_reviewed_product_pricevaliduntil']['fixed']);
        }
    }
}