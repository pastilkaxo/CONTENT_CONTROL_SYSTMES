<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2023 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD\Schemas\Schemas;

// No direct access
defined('_JEXEC') or die;

use GSD\Helper;
use GSD\Schemas\Base;
use NRFramework\DOMCrawler;

class HowTo extends Base
{
    /**
     * The HTML tags allowed to be used in certain schema properties, such as the headline and the description.
     *
     * @var mixed
     */
    protected $allowed_HTML_tags = '<p><br><ul><li><strong><em><b>';

    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $this->data->set('totalTime', $this->data['totalTime'] ? 'PT' . $this->data['totalTime'] . 'M' : null);
        
        $steps = [];

        switch ($this->data->get('mode', 'auto'))
        {
            case 'manual':
                $steps = array_values(json_decode(json_encode($this->data['howto_repeater']), true));
                break;

            // Auto Mode
            case 'auto':
                $crawler = new DOMCrawler();
                
                $names  = $crawler->readCSSSelectorField($this->data->get('name_selector'), false);
                $texts  = $crawler->readCSSSelectorField($this->data->get('text_selector'), false);
                $images = $crawler->readCSSSelectorField($this->data->get('image_selector'), false);
                $urls   = $crawler->readCSSSelectorField($this->data->get('url_selector'), false);

                $steps = array_map(function($name, $text, $image, $url)
                {
                    return [
                        'name'  => $name,
                        'text'  => $text,
                        'image' => $image,
                        'url'   => $url
                    ];
                }, $names, $texts, $images, $urls);
        }

        // Prepare steps
        $steps = array_map(function($step)
        {
            return [
                'name'  => isset($step['name']) ? $step['name'] : '',
                'text'  => isset($step['text']) ? $step['text'] : '',
                'image' => isset($step['image']) ? Helper::absURL($step['image']) : '',
                'url'   => isset($step['url']) ? Helper::absURL($step['url']) : ''
            ];
        }, $steps);

        $this->data->set('step', $steps);

        parent::initProps();
    }

    /**
	 * Convert a string to UTF8 encoding
	 * 
	 * @param  string
	 * 
	 * @return string
	 */
	private function stringToUTF8($string)
	{
		if (!function_exists('mb_convert_encoding'))
		{
			return $string;
		}

		return mb_encode_numericentity(iconv('UTF-8', 'UTF-8', $string), [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
	}
}