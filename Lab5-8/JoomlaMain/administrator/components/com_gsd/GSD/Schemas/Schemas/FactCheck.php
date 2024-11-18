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

class FactCheck extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        parent::initProps();

        switch ($this->data['factcheckRating'])
        {
            // there is no textual representation for zero (0)
            case '1':
                $textRating = 'False';
                break;

            case '2':
                $textRating = 'Mostly false';
                break;

            case '3':
                $textRating = 'Half true';
                break;

            case '4':
                $textRating = 'Mostly true';
                break;

            case '5':
                $textRating = 'True';
                break;

            default:
                $textRating = 'Hard to categorize';
        }

        $props = [
            'claimDatePublished'   => Helper::date($this->data['claimDatePublished'], true),
            'factcheckURL'         => $this->data['multiple'] ? $this->data['url'] . $this->data['anchorName'] : $this->data['url'],
            'bestFactcheckRating'  => $this->data['factcheckRating'] != '-1' ? '5' : '-1',
            'worstFactcheckRating' => $this->data['factcheckRating'] != '-1' ? '1' : '-1',
            'alternateName'        => $textRating
        ];

        $this->data->loadArray($props);
    }

    /**
     * Beyond the default housekeeping, limit the characters in the headline property to 110 in order to comply with Google's guidelines.
     *
     * Reference: https://developers.google.com/search/docs/appearance/structured-data/factcheck
     * 
     * @return void
     */
    protected function cleanProps()
    {
        parent::cleanProps();

        $this->data->set('title', StringHelper::substr($this->data->get('title'), 0, 75));
    }
}