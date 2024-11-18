<?php
defined('_JEXEC') or die;

abstract class CoreContentProductBase
{
    abstract protected function isNew();
    abstract protected function sale();
    abstract protected function outOfStock();
    abstract protected function sku();

    public function addZeroCentsProcess($price, $addZeroCents = false) {
        if (!$price) {
            return '';
        }
        $separator = strpos($price, ',') > -1 ? ',' : '.';
        $currentPrice = '0';
        if (preg_match('/\d+(' . $separator . '\d+)?/', $price, $matches)) {
            $currentPrice = $matches[0];
            $price = str_replace($matches[0], '[[currentPrice]]', $price);
        }
        $priceParams = explode($separator, $currentPrice);
        $cents = isset($priceParams[1]) ? $priceParams[1] : '00';
        if ($cents === '00') {
            $currentPrice = $priceParams[0];
        }
        if ($addZeroCents) {
            $currentPrice = $priceParams[0] . $separator . $cents;
        }
        return str_replace('[[currentPrice]]', $currentPrice, $price);
    }
}
