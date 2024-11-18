<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Utility;

defined('_JEXEC') or die;

class GridHelper
{
    /**
     * Build styles for grid
     *
     * @param array $props      Grid properties
     * @param int   $itemsCount Items count
     *
     * @return string
     */
    public static function buildGridAutoRowsStyles($props, $itemsCount)
    {
        $stylesResult = '';
        foreach ($props as $prop) {
            $autoRows = GridHelper::calcGridAutoRows(
                array(
                    'items' => $itemsCount,
                    'columns' => $prop['columns'],
                    'gap' => $prop['gap']
                )
            );
            $stylesResult .= str_replace('[[' . $prop['mode'] . '_VALUE]]', $autoRows, $prop['styles']);
        }
        return $stylesResult ? '<style>' . $stylesResult . '</style>' : '';
    }

    /**
     * Calculate grid auto rows value
     *
     * @param array $params Grid parameters
     *
     * @return string
     */
    public static function calcGridAutoRows($params = array()) {
        $rows = isset($params['rows']) ? $params['rows'] : null;
        $columns = isset($params['columns']) ? $params['columns'] : 1;

        if (!$rows) {
            $rows = ceil($params['items'] / $columns);
        }

        $gap = floatval($params['gap']);
        $gapMultiplier = $gap * ($rows - 1) / $rows;
        $autoRowsValue = (floor(100 / $rows * 100) / 100) . '%';

        return $gapMultiplier > 0 ? 'calc(' . $autoRowsValue . ' - ' . $gapMultiplier . 'px)' : $autoRowsValue;
    }
}