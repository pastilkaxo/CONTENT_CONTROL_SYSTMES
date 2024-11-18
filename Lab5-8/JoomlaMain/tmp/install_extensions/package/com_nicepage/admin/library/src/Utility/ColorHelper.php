<?php

namespace NP\Utility;

defined('_JEXEC') or die;

/**
 * Class ColorHelper
 *
 * @package NP\Utility
 */
class ColorHelper
{
    /**
     * @var array
     */
    private $_siteStyleCssParts;

    /**
     * ColorHelper constructor.
     *
     * @param string $styles All css content
     */
    public function __construct($styles)
    {
        $this->_siteStyleCssParts = $this->_processAllColors($styles);
    }

    /**
     * @param string $styles Common styles
     *
     * @return array
     */
    private function _processAllColors($styles)
    {
        $split = preg_split('#(\/\*begin-color [^*]+\*\/[\s\S]*?\/\*end-color [^*]+\*\/)#', $styles, -1, PREG_SPLIT_DELIM_CAPTURE);
        $parts = array();
        foreach ($split as $part) {
            $part = trim($part);
            if (!$part) {
                continue;
            }

            if (preg_match('#\/\*begin-color ([^*]+)\*\/#', $part, $m)) {
                $id = 'color_' . $m[1];
                $parts[] = array(
                    'type' => 'color',
                    'id' => $id,
                    'css' => $part,
                );
            } else {
                $parts[] = array(
                    'type' => '',
                    'css' => $part,
                );
            }
        }
        return $parts;
    }

    /**
     * @param string $html Html content
     *
     * @return array
     */
    private function _processUsedColors($html)
    {
        $usedIds = array();

        if (!$html) {
            return $usedIds;
        }

        foreach ($this->_siteStyleCssParts as &$part) {
            if (isset($part['id']) && strpos($html, preg_replace('#^color_#', '', $part['id'])) !== false) {
                $usedIds[$part['id']] = true;
            }
        }
        return $usedIds;
    }

    /**
     * Get used colors parts
     *
     * @param string $html Html content
     *
     * @return false|string
     */
    public function getUsedColors($html)
    {
        return json_encode($this->_processUsedColors($html));
    }

    /**
     * Get all colors parts
     *
     * @return false|string
     */
    public function getAllColors()
    {
        return json_encode($this->_siteStyleCssParts);
    }

    /**
     * Build site style css
     *
     * @param array  $params         plugin config parameters
     * @param string $pageCssUsedIds used css ids
     * @param bool   $buildAll       Build all styles
     *
     * @return string
     */
    public static function buildSiteStyleCss($params, $pageCssUsedIds, $buildAll = false)
    {
        $result = '';
        $partsJson = isset($params['siteStyleCssParts']) ? $params['siteStyleCssParts'] : '';
        if ($partsJson) {
            $cssParts = json_decode($partsJson, true);
            $usedIds = $pageCssUsedIds ? json_decode($pageCssUsedIds, true) : array();
            $headerFooterCssUsedIds = isset($params['headerFooterCssUsedIds']) ? json_decode($params['headerFooterCssUsedIds'], true) : array();
            $cookiesCssUsedIds = isset($params['cookiesCssUsedIds']) ? json_decode($params['cookiesCssUsedIds'], true) : array();
            $backToTopCssUsedIds = isset($params['backToTopCssUsedIds']) ? json_decode($params['backToTopCssUsedIds'], true) : array();
            foreach ($cssParts as $part) {
                if ($buildAll) {
                    $result .= $part['css'];
                } else if ($part['type'] !== 'color'
                    || !empty($usedIds[$part['id']])
                    || !empty($headerFooterCssUsedIds[$part['id']])
                    || !empty($cookiesCssUsedIds[$part['id']])
                    || !empty($backToTopCssUsedIds[$part['id']])
                ) {
                    $result .= $part['css'];
                }
            }
        }

        if (strpos($result, '--theme-sheet') === false) {
            $result .=<<<VARS
.u-body {
    --theme-sheet-width-xl: 1140px;
    --theme-sheet-width-lg: 940px;
    --theme-sheet-width-md: 720px;
    --theme-sheet-width-sm: 540px;
    --theme-sheet-width-xs: 340px;
    --theme-sheet-width-xxl: 1320px;
}
VARS;
        }

        return $result;
    }
}
