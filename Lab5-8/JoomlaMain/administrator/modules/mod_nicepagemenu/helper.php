<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
/**
 * Class ModNicepageMenuHelper
 */
abstract class ModNicepageMenuHelper
{
    /**
     * @return bool|stdClass
     */
    public static function getNicepageComponent()
    {
        $db = Factory::getDBO();
        $q = 'SELECT m.id, m.title, m.alias, m.link, m.parent_id, m.img, e.element FROM #__menu as m
				LEFT JOIN #__extensions AS e ON m.component_id = e.extension_id
		         WHERE m.client_id = 1 AND e.enabled = 1 AND m.id > 1 AND e.element = \'com_nicepage\'
		         AND (m.parent_id=1 OR m.parent_id =
			                        (SELECT m.id FROM #__menu as m
									LEFT JOIN #__extensions AS e ON m.component_id = e.extension_id
			                        WHERE m.parent_id=1 AND m.client_id = 1 AND e.enabled = 1 AND m.id > 1 AND e.element = \'com_nicepage\'))
		         ORDER BY m.lft';
        $db->setQuery($q);
        $nicepageComponentItems = $db->loadObjectList();

        $result = new stdClass();
        $lang = Factory::getLanguage();
        if ($nicepageComponentItems) {
            // Parse the list of extensions.
            foreach ($nicepageComponentItems as &$nicepageComponentItem) {
                if ($nicepageComponentItem->parent_id == 1) {
                    $result = $nicepageComponentItem;
                    if (!isset($result->submenu)) {
                        $result->submenu = array();
                    }

                    if (empty($nicepageComponentItem->link)) {
                        $nicepageComponentItem->link = 'index.php?option=' . $nicepageComponentItem->element;
                    }

                    $nicepageComponentItem->text = $lang->hasKey($nicepageComponentItem->title) ? Text::_($nicepageComponentItem->title) : $nicepageComponentItem->alias;
                } else {
                    // Sub-menu level.
                    if (isset($result)) {
                        // Add the submenu link if it is defined.
                        if (isset($result->submenu) && !empty($nicepageComponentItem->link)) {
                            $nicepageComponentItem->text = $lang->hasKey($nicepageComponentItem->title) ? Text::_($nicepageComponentItem->title) : $nicepageComponentItem->alias;
                            $class = preg_replace('#\.[^.]*$#', '', basename($nicepageComponentItem->img));
                            $class = preg_replace('#\.\.[^A-Za-z0-9\.\_\- ]#', '', $class);
                            $nicepageComponentItem->class = '';
                            $result->submenu[] = &$nicepageComponentItem;
                        }
                    }
                }
            }
            $props = get_object_vars($result);
            if (!empty($props)) {
                return $result;
            }
        }
        return false;
    }
}
