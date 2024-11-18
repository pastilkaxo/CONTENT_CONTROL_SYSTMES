<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Virtuemart;

defined('_JEXEC') or die;

use \VmModel;
use Joomla\CMS\Factory;

/**
 * Class ProductMapper
 */
class ProductMapper
{
    /**
     * @return mixed
     */
    public static function getModel()
    {
        return VmModel::getModel('product');
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public static function create($data)
    {
        return self::getModel()->store($data);
    }

    /**
     * @param $catid
     *
     * @return mixed
     */
    public static function getProductsByCatId($catid)
    {
        $ids = self::getModel()->sortSearchListQuery(false, $catid, false, false);
        return self::getModel()->getProducts($ids, false, false, false, true);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public static function delete($id)
    {
        return self::getModel()->remove(array($id));
    }

    /**
     * @param $id
     */
    public static function updateDate($id)
    {
        $date = Factory::getDate();
        $date->modify('-1 second');
        $strDate = $date->toSql();
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->update('#__virtuemart_products');
        $query->set($db->quoteName('modified_on') . '=' . $db->quote($strDate));
        $query->set($db->quoteName('created_on') . '=' . $db->quote($strDate));
        $query->where('virtuemart_product_id=' . $id);
        $db->setQuery($query);
        $db->execute();
    }
}
