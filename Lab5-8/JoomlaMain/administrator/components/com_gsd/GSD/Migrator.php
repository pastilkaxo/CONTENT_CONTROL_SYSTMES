<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD;

defined('_JEXEC') or die('Restricted Access');

use GSD\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Multilanguage;

/**
 * Google Structured Data Migrator Helper
 */
class Migrator
{
    /**
     * Indicates the current installed version of the extension
     *
     * @var  string
     */
    protected $installedVersion;

    /**
     * Shorthand of the Joomla Application Object
     *
     * @var  object
     */
    protected $app;

    /**
     * Class constructor
     *
     * @param string $installedVersion  The version of the extension
     */
    public function __construct($installedVersion)
    {
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/models');
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/tables');

        $this->installedVersion = $installedVersion;
        $this->app = Factory::getApplication();
    }

    /**
     * The main method to run migrations
     *
     * @return void
     */
    public function run()
    {
        try
        {
            $this->checkAndAddAppviewColumn();
            $this->moveGlobalLocalBusinessToItems();
        } catch (\Throwable $th)
        {
            $this->app->enqueueMessage($th->getMessage(), 'error');
        }
    }

    /**
     * Since v4.4.0, the Local Business Schema is available as an indepedent Schema Type. Given than update, the Local Business options
     * available in the extension's configuration page are no longer needed and they are migrated as a structured data item in the Items section.
     *
     * @return mixed Null if the migration doesn't run, True if it does run.
     */
    public function moveGlobalLocalBusinessToItems()
    {
        // Local Business Content Type introduced in v4.4.0
		if (version_compare($this->installedVersion, '4.4.0', '>'))
		{
            return;
        }

        $params = Helper::getParams();

        if (!$params->get('businesslisting_enabled'))
        {
            return;
        }

        // Enable Menu Manager Integration
        $menu_manager_plugin = \NRFramework\Extension::get('menus', 'plugin', 'gsd');
        if ($menu_manager_plugin && !$menu_manager_plugin['enabled'])
        {
            $table = Table::getInstance('Extension', 'Joomla\\CMS\\Table\\');
            $table->load($menu_manager_plugin['extension_id']);
            $table->enabled = 1;
            $table->store();
        }

        // Get homepage menu item
        $menu = $this->app->getMenu('site');
        $lang = Factory::getLanguage();
        $home = Multilanguage::isEnabled() ? $menu->getDefault($lang->getTag()) : $menu->getDefault();
        $homepage_menuitem = (int) $home->id;

        $item = [
            'title' => 'Website Local Business',
            'contenttype' => 'localbusiness',
            'plugin' => 'menus',
            'state' => $homepage_menuitem ? 1 : 0,
            'note' => 'Moved from extension configruation page',
            'localbusiness' => [
                'type' => $params->get('businesslisting_type'),
                'name' => [
                    'option' => 'gsd.sitename'
                ],
                'image' => [
                    'option' => 'gsd.sitelogo'
                ],
                'telephone' => [
                    'option' => '_custom_',
                    'custom' => $params->get('businesslisting_telephone')
                ],
                'priceRange' => [
                    'option' => '_custom_',
                    'custom' => $params->get('price_range')
                ],
                'openinghours' => [
                    'option' => 'fixed',
                    'fixed'  => [
                        'option' => $params->get('businesslisting_hours_available'),
                        'monday' => [
                            'enabled' => $params->get('businesslisting_monday'),
                            'start' => $params->get('businesslisting_monday_start'),
                            'end' => $params->get('businesslisting_monday_end')
                        ],
                        'tuesday' => [
                            'enabled' => $params->get('businesslisting_tuesday'),
                            'start' => $params->get('businesslisting_tuesday_start'),
                            'end' => $params->get('businesslisting_tuesday_end')
                        ],
                        'wednesday' => [
                            'enabled' => $params->get('businesslisting_wednesday'),
                            'start' => $params->get('businesslisting_wednesday_start'),
                            'end' => $params->get('businesslisting_wednesday_end')
                        ],
                        'thursday' => [
                            'enabled' => $params->get('businesslisting_thursday'),
                            'start' => $params->get('businesslisting_thursday_start'),
                            'end' => $params->get('businesslisting_thursday_end')
                        ],
                        'friday' => [
                            'enabled' => $params->get('businesslisting_friday'),
                            'start' => $params->get('businesslisting_friday_start'),
                            'end' => $params->get('businesslisting_friday_end')
                        ],
                        'saturday' => [
                            'enabled' => $params->get('businesslisting_saturday'),
                            'start' => $params->get('businesslisting_saturday_start'),
                            'end' => $params->get('businesslisting_saturday_end')
                        ],
                        'sunday' => [
                            'enabled' => $params->get('businesslisting_sunday'),
                            'start' => $params->get('businesslisting_sunday_start'),
                            'end' => $params->get('businesslisting_sunday_end')
                        ]
                    ],
                ],
                'addressCountry' => [
                    'option' => 'fixed',
                    'fixed' => $params->get('businesslisting_address_country')
                ],
                'addressLocality' => [
                    'option' => '_custom_',
                    'custom' => $params->get('businesslisting_address_locality')
                ],
                'streetAddress' => [
                    'option' => '_custom_',
                    'custom' => $params->get('businesslisting_street_address')
                ],
                'addressRegion' => [
                    'option' => '_custom_',
                    'custom' => $params->get('businesslisting_address_region')
                ],
                'postalCode' => [
                    'option' => '_custom_',
                    'custom' => $params->get('businesslisting_postal_code')
                ],
                'geo' => [
                    'option' => '_custom_',
                    'custom' => $params->get('businesslisting_latlng')
                ],  
                'servesCuisine' => [
                    'option' => '_custom_',
                    'custom' => $params->get('servesCuisine')
                ]
            ],
            'assignments' => [
                'menu' => [
                    'assignment_state' => 1,
                    'selection' => [$homepage_menuitem]
                ]
            ]
        ];

        if (!$this->createItem($item))
        {
            return;
        }
        
        $this->app->enqueueMessage('Your Local Business Listing options previously found in the extension configuration page has been migrated as a Structured Data Item in the Items section.', 'warning');
        
        // To ensure the migration runs once, disable the Local Business option in the configuration
		$table = Table::getInstance('Config', 'GSDTable');
        $table->load('config');
        
        $p_ = json_decode($table->params);
        $p_->businesslisting_enabled = false;

        $table->params = json_encode($p_);
	    $table->store();
        
        return true;
    }

    /**
     * Create a new structured data item
     *
     * @param  array $params
     *
     * @return boolean
     */
    private function createItem($params)
    {
        $model = BaseDatabaseModel::getInstance('Item', 'GSDModel');
        $item = $model->validate(null, $params);
        return $model->save($item);
    }

    /**
     * The "appview" column was introduced in 5.1.0 and due to
     * the fact that we did not include it in the main "gsd" table
     * SQL file right away, some users may be missing it.
     * 
     * We check whether this column exists and if not, add it, otherwise, abort.
     * 
     * @return  void
     */
    private function checkAndAddAppviewColumn()
    {
        $db = Factory::getDBO();
        $query = "SHOW COLUMNS FROM `#__gsd` LIKE 'appview'";
        $db->setQuery($query);

        // Column exists
        if ($res = $db->loadResult())
        {
            return;
        }

        // Add column
        $sql = "ALTER TABLE `#__gsd` ADD `appview` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '*' AFTER `plugin`";
        $db->setQuery($sql);
        $db->execute();
    }
}