<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Builder;

defined('_JEXEC') or die;

use NP\Utility\TagBalancer;

/**
 * Class DataBuilder
 */
abstract class DataBuilder
{
    /**
     * Get title data
     */
    abstract protected function title();

    /**
     * Get title link data
     */
    abstract protected function titleLink();

    /**
     * Get content data
     */
    abstract protected function content();

    /**
     * Get image data
     */
    abstract protected function image();

    /**
     * Create excerpt
     *
     * @param string $text       Article content
     * @param int    $max_length Max count
     * @param string $cut_off    Cut off text
     *
     * @return false|string|string[]|null
     */
    public function excerpt($text, $max_length = 140, $cut_off = '...')
    {
        $result = '';
        if (strlen($text) > $max_length) {
            for ($i = 0; $i < strlen($text); $i++) {
                $result .= $text[$i];
                $lastSymbol = substr($result, -1);
                $onlyText = strip_tags($result);
                if (strlen($onlyText) >= $max_length && ($lastSymbol == ' ' || $lastSymbol == '.')) {
                    $result = substr($result, 0, strlen($result) - 1);
                    break;
                }
            }
            $result .= strlen($onlyText) >= $max_length ? $cut_off : '';
        } else {
            $result = $text;
        }

        $allowed_tags = array(
            '<a>',
            '<abbr>',
            '<blockquote>',
            '<b>',
            '<cite>',
            '<pre>',
            '<code>',
            '<em>',
            '<label>',
            '<i>',
            '<p>',
            '<strong>',
            '<ul>',
            '<ol>',
            '<li>',
            '<h1>',
            '<h2>',
            '<h3>',
            '<h4>',
            '<h5>',
            '<h6>',
            '<object>',
            '<param>',
            '<embed>'
        );
        $result = strip_tags($result, join('', $allowed_tags));
        $result = TagBalancer::process($result);
        return $result;
    }
}