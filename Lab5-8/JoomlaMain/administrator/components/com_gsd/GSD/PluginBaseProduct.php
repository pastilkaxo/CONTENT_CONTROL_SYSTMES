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

defined('_JEXEC') or die('Restricted access');

use GSD\PluginBase;
use GSD\MappingOptions;

/**
 *  Google Structured Data Product Plugin Base
 */
class PluginBaseProduct extends PluginBase
{
    /**
	 * The MapOptions Backend Event. Triggered by the mappingoptions fields to help each integration add its own map options.
	 *  
	 * @param	string	$plugin
	 * @param	array	$options
	 *
	 * @return	void
	 */
    public function onMapOptions($plugin, &$options)
    {
		if ($plugin != $this->_name)
        {
			return;
        }
        
		$new_options = [
			'sku'        => 'SKU',
			'mpn'        => 'MPN',
			'brand'      => 'GSD_PRODUCT_BRAND_NAME',
			'offerprice' => 'GSD_PRODUCT_OFFER_PRICE',
			'currency'   => 'GSD_PRODUCT_OFFER_CURRENCY',
			'offerAvailability' => 'GSD_PRODUCT_AVAILABILITY'
        ];

		MappingOptions::add($options, $new_options, 'GSD_INTEGRATION', 'gsd.item.');
	}
	
 	/**
     *  Asks for data from the child plugin based on the active view name
     *
     *  @return  Registry  The payload Registry
     */
    protected function getPayload()
    {
		if (!$payload = parent::getPayload())
		{
			return;
		}

		$schema_prefix = 'https://schema.org/';

		// Add offerAvailability property
		if (!$payload->offsetExists('offerAvailability'))
		{
			$available = method_exists($this, 'productIsAvailable') ? $this->productIsAvailable() : true;
			$payload->set('offerAvailability', $schema_prefix . ($available ? 'InStock' : 'OutOfStock'));
		}

		return $payload;
	}
}