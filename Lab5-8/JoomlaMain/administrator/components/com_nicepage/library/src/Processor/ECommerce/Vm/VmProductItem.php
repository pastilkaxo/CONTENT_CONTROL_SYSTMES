<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor\ECommerce\Vm;

use NP\Models\ContentModelCustomProducts;
use NP\Processor\ECommerce\ProductItem;

defined('_JEXEC') or die;

class VmProductItem extends ProductItem
{
    /**
     * @param array $options Global options
     */
    protected function __construct($options)
    {
        parent::__construct($options);
    }

    /**
     * Get products by source
     *
     * @param array $options Source options
     *
     * @return array
     */
    protected function _getProducts($options)
    {
        $model = new ContentModelCustomProducts($options);
        return $model->getProducts();
    }

    /**
     * Get categories from model
     *
     * @param array $options Source options
     *
     * @return array
     */
    protected function _getCategories($options = array())
    {
        $model = new ContentModelCustomProducts($options);
        return $model->getCategories();
    }
}
