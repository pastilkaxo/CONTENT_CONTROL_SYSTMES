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

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

require_once __DIR__ . '/script.install.helper.php';

/**
 *  Google Structured Data component installer
 */
class Com_GSDInstallerScript extends Com_GSDInstallerScriptHelper
{
	public $name = 'GSD';
	public $alias = 'gsd';
	public $extension_type = 'component';

	/**
	 *  Runs after current extension installation
	 *
	 *  @return  void
	 */
	public function onAfterInstall()
	{
		// Only on update
		if ($this->install_type == 'install')
		{
			return true;
		}

		$this->loadFramework();
		$installedVersion = \NRFramework\Functions::getExtensionVersion('plg_system_gsd');

		// Let's migrate old versions to 3.0.0 first
		if (version_compare($installedVersion, '3.0.0', '<='))
		{
			require_once $this->getMainFolder() . '/helpers/migrator.php';
			$migrator = new GSDMigrator();
			$migrator->start();
		}

        // Since v3.1.3 the saving policy of Custom Code has been changed
        if (version_compare($installedVersion, '3.1.3', '<='))
        {
            $this->migrateCustomCode();
        }

        if (version_compare($installedVersion, '3.1.10', '<'))
        {
            $this->mergeEventDateAndTimeFields();
        }

        // 4.0.0 Migrator
        if (version_compare($installedVersion, '4.0.0', '<='))
        {
			require_once $this->getMainFolder() . '/helpers/migrator4.php';
			$migrator = new GSDMigrator4($installedVersion);
			$migrator->start();
        }

        // Migrate to the new Remove Structured Data option
        if (version_compare($installedVersion, '4.1.0', '<'))
        {
			$this->updateRemoveMicrodataValue();
        }

        require_once JPATH_ADMINISTRATOR . '/components/com_gsd/autoload.php';
        $mgrt = new GSD\Migrator($installedVersion);
        $mgrt->run();
    }

    private function updateRemoveMicrodataValue()
    {
        Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/tables');
		
        $table = Table::getInstance('Config', 'GSDTable');
        $table->load('config');
        
        $params = json_decode($table->params);

        if (!isset($params->removemicrodata) || empty($params->removemicrodata))
        {
            return;
        }
        
        $current_schemas = $params->removemicrodata;
        $new_schemas = [];
        
        foreach ($current_schemas as $key => $schema)
        {
            if (!is_string($schema))
            {
                continue;
            }

            $new_schemas[$key] = (object) [
                'name'    => $schema,
                'enabled' => true
            ];
        }

        if (!$new_schemas)
        {
            return;
        }
        
        $params->removemicrodata = $new_schemas;
        
        $table->params = json_encode($params);
        $table->store();
    }

    /**
     * Merges separate Date and Time fields into a single Calendar field
     *
     * @return void
     */
    private function mergeEventDateAndTimeFields()
    {
        $items = $this->getItems();
    
        foreach ($items as $key => $item)
        { 
            if ($item->contenttype != 'event')
            {
                continue;
            }
    
            // If startTime is missing the item is already migrated
            if (!isset($item->event->startTime))
            {
                continue;
            }
    
            // OK. The item needs migration
            $table = $this->loadItem($item->id);
    
            $p = json_decode($table->params);
    
            $p->event->startDate .= ' ' . $p->event->startTime;
            $p->event->offerStartDate .= ' ' . $p->event->offerStartTime;
            $p->event->endDate .= ' ' . $p->event->endTime;
    
            unset($p->event->startTime);
            unset($p->event->endTime);
            unset($p->event->offerStartTime);
    
            $table->params = json_encode($p);
            $table->store();
        }
    }

    /**
     *	Since v3.1.3 the saving policy of Custom Code has been changed which is now being saved independently
     *	This method helps up, transition to new requirements. 
     *
     *  @return  void
     */
	private function migrateCustomCode()
    {
        $items = $this->getItems();

        foreach ($items as $key => $item)
        {   
         	if ($item->contenttype == 'custom_code')
            {
                continue;
            }

            if (!isset($item->customcode) || empty($item->customcode) || is_null($item->customcode))
            {
                continue;
            }

            $data = array(
                'thing'       => $item->thing,
                'plugin'      => $item->plugin,
                'params'      => json_encode(array(
                    'contenttype' => 'custom_code',
                    'customcode'  => $item->customcode
                )),
                'state'       => $item->state,
                'note'        => $item->note,
                'colorgroup'  => $item->colorgroup
            );

            Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/tables');

            // Save new row
            $model = BaseDatabaseModel::getInstance('Item', 'GSDModel');
            $data  = $model->validate(null, $data);
            $model->save($data);

            // Remove Custom Code from old row
            $table = Table::getInstance('Item', 'GSDTable');
            $table->load($item->id);

            $p = json_decode($table->params);
            unset($p->customcode);

            $table->params = json_encode($p);
            $table->store();
        }
    }

    public function getItems($table = 'Items')
    {
        $hash = 'get' . $table;

        if (NRFramework\Cache::has($hash))
        {
            return NRFramework\Cache::get($hash);
        }

        BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/models');
        
        if (!$model = BaseDatabaseModel::getInstance($table, 'GSDModel'))
        {
            return;
        }

        return NRFramework\Cache::set($hash, $model->getItems());
    }

    private function loadItem($id, $table = 'Item')
    {
        if (!$table = Table::getInstance($table, 'GSDTable'))
        {
            return;
        }

        $table->load($id);

        return $table;
    }
}

 