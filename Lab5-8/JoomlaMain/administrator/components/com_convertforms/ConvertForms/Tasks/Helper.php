<?php

/**
 * @package         Convert Forms
 * @version         4.4.6 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms\Tasks;

defined('_JEXEC') or die('Restricted access');

class Helper
{
    public static function readRepeatSelect($items)
    {
        if (!$items)
        {
            return;
        }

        return array_filter(array_map(function($item)
        {
            if (isset($item['value']))
            {
                return $item['value'];
            }
        }, $items));
    }

    public static function getAllowedCustomFieldsTypesInRepeater()
    {
        return [
            'acfarticles',
            'acfconvertforms',
            'acfcountry',
            'acfcurrency',
            'acfdownloadbutton',
            'acffacebook',
            'acfgravatar',
            'acfhtml5audio',
            'acfiframe',
            'acfmodule',
            'acfphp',
            'acfprogressbar',
            'acfqrcode',
            'acftelephone',
            'acftimepicker',
            'acftruefalse',
            'acftwitter',
            'acfvideo',
            'acfwhatsappctc',
            'calendar',
            'color',
            'editor',
            'integer',
            'list',
            'imagelist',
            'location',
            'radio',
            'checkboxes',
            'text',
            'textarea',
            'url',
            'user',
            'usergrouplist',
        ];
    }
}