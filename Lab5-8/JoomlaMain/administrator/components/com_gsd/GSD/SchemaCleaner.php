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

use Joomla\String\StringHelper;
use Joomla\CMS\Factory;

class SchemaCleaner
{
    /**
	 * Remove JSON-LD Structured Data injected by the integration or 3rd party extensions.
	 * 
	 * Note: This method should be called either in the onAfterRender or in the onAfterInitialize Events when the document's buffer is available.
	 *
	 * @param	mixed	$schema_type		Search and remove only specific schema types
	 * @param	mixed	$remove_json		If true, the JSON-based markup will be removed from the page
	 * @param	mixed	$remove_microdata	If true, the microdata-based will be removed from the page
	 *
	 * @return	void
	 */
	public static function remove($schema_type, $remove_json = true, $remove_microdata = true)
	{
		if (empty($schema_type))
		{
			return;
		}

		$app = Factory::getApplication();

		// Get document buffer
        $body = $app->getBody();

		$replacements_count = 0;

        $schema_types = (array) $schema_type;
        
        // Search and remove JSON-LD scripts
		if ($remove_json)
		{
            foreach ($schema_types as $schema_type)
            {
                $replacements_count += self::removeJSONSchema($body, strtolower($schema_type));
            }
		}
		
		// Search and remove microdata
		if ($remove_microdata)
		{
            foreach ($schema_types as $schema_type)
            {

                $replacements_count += self::removeMicrodata($body, strtolower($schema_type));
            }
		}

        // If no replacements made, exit.
        if ($replacements_count == 0)
        {
            return;
		}
		
        // Set the new document body back.
		$app->setBody($body);
	}

	/**
	 * Remove microdata from a string
	 *
	 * @param	string	$text			The text to search for
	 * @param	mixed	$schema_type	Search and remove only specific schema types
	 *
	 * @return	integer	
	 */
	private static function removeMicrodata(&$text, $schema_type = null)
	{
        // Simple check to decide whether the plugin should procceed or not.
        if (StringHelper::strpos($text, 'itemtype') === false)
        {
            return;
		}

		// Base replacement pattern
		// We do not include itemprop property here as some components renders the element
		// like itemscope itemtype="http://schema.org/" or itemscope="" itemtype="http://schema.org/"
		$patterns = ['/(itemscope)? itemtype=(\'|")?http(s?):\/\/(www.)?schema.org\/' . $schema_type . '(\'|")?/msi'];

		if ($schema_type == 'all')
		{
			$patterns = [
				'/(itemscope)? itemtype=(\'|")?http(s?):\/\/(.*?)schema.org\/(.*?(\'|"))(\'|")?/msi',
				'/<meta(.*?)(itemscope|itemprop)(.*?)\/?>/',
				'/itemprop=("|\')(.*?)("|\')/'
			];
		}

        // Extra rules for the Event type
		if ($schema_type == 'event')
		{
			$extra_patterns = [
				'/<meta itemprop="(url|startDate|addressRegion|postalCode|latitude|longitude|streetAddress|addressLocality)"[^>]+>/'
			];

			$patterns = array_merge($patterns, $extra_patterns);
        }


        // Extra rules for the Article type
		if ($schema_type == 'article')
		{
			$extra_patterns = [
				'/itemprop="(url|name|author|headline|image|keywords|articleBody|datePublished)"/',
				'/<meta itemprop="(inLanguage|datePublished)"[^>]+>/'
			];

			$patterns = array_merge($patterns, $extra_patterns);
        }

        // Extra rules for the Product type
		if ($schema_type == 'product')
		{
			$extra_patterns = [
				'/<meta itemprop="(price|priceCurrency)"[^>]+>/',
				'/itemprop="(sku|description|offers|name)"/',
				'/<link itemprop="availability" href="http(s?):\/\/schema.org\/InStock" \/>/'
			];

			$patterns = array_merge($patterns, $extra_patterns);
        }

        // Extra rules for the Breadcrumbs type
        if ($schema_type == 'breadcrumblist')
		{
			$extra_patterns = [
				'/itemprop="(itemListElement|position|item)"/',
				'/itemscope itemtype="http(s?):\/\/schema.org\/ListItem"/'
			];

			$patterns = array_merge($patterns, $extra_patterns);
		}
		
        // Extra rules for the AggregateRating type
        if ($schema_type == 'aggregaterating')
		{
			$extra_patterns = [
				'/itemprop="(aggregateRating|ratingValue|bestRating)"/',
				'/<meta itemprop="(ratingCount|bestRating|worstRating)"[^>]+>/',
			];

			$patterns = array_merge($patterns, $extra_patterns);
		}

		// Do the replacements and return the number of replacements
		$text = preg_replace($patterns, '', $text, -1, $count);

		return $count;
	}
	
	 /**
	  * Remove JSON-LD scripts from a string
	  *
	  * @param	string	$text			The text to search for
	  * @param	mixed	$schema_type	Search and remove only specific schema types
	  *
	  * @return	integer	
	  */
    private static function removeJSONSchema(&$text, $schema_type = null)
	{
		// Simple check to decide whether we should procceed or not.
		if (StringHelper::strpos($text, '//schema.org/') === false)
		{
			return;
		}

		$re = '/<script[^>]*type="application\/ld\+json"[^>]*>([\s\S]*?)<\/script>/msi';

		preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);

		if (!$matches)
		{
			return;
		}

		$replacements_count = 0;

        foreach ($matches as $match)
        {
			// Ignore our scripts
			if (strpos($match[0], 'data-type="gsd"') !== false)
			{
				continue;
			}

			// If we are searching for a specific schema type, make sure it exists.
			if (!is_null($schema_type) && !preg_match('/"@type"\s*:\s*"' . $schema_type . '"/si', $match[1]))
			{
				continue;
			}

			$text = str_replace($match[0], '', $text);
			
			$replacements_count++;
		}
		
		return $replacements_count;
	}
}