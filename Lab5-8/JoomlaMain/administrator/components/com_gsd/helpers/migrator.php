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

defined('_JEXEC') or die('Restricted Access');

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Filesystem\File;
use Joomla\CMS\Table\Table;

/**
 *  Google Structured Data Migrator
 *
 *  The purpose of this class is to migrate data and offer a non-breaking update from 2.1 to 3.0 version.
 *  Migration should be run on 3.0 versions and below only.
 */
class GSDMigrator
{
	/**
	 *  Log messages array
	 *
	 *  @var  array
	 */
	public $log;

	/**
	 *  If enabled, the main GSD table will be flushed before starting the migration
	 *
	 *  @var  boolean
	 */
	private $flushTable;

	/**
	 *  Override the database object for testing purposes and sharing data between different databases
	 *
	 *  @var  mixed
	 */
	private $db;

	/**
	 *  Supported migration component names
	 *
	 *  @var  array
	 */
	private $migrateComponents = array(
		'content',
		'k2'
	);

	/**
	 *  The name of the System Plugin
	 *
	 *  @var  string
	 */
	private $systemPluginName = 'plg_system_gsd';

	/**
	 *  Class Constructor
	 *
	 *  @param  boolean  $flushTable  Indicates wether the table will be flushed or not
	 *  @param  mixed    $db          Database options
	 */
	public function __construct($flushTable = false, $db = null)
	{
		$this->db = $db;
		$this->flushTable = $flushTable;
		$this->log = array('Migrator: Started');
	}

	/**
	 *  Returns a database instance
	 *
	 *  @return  JDatabase object
	 */
	private function getDB()
	{
		return is_array($this->db) ? DatabaseDriver::getInstance($this->db) : Factory::getDBo();
	}

	/**
	 *  Start the migration process
	 *
	 *  @return  void
	 */
	public function start()
	{
		// Flush the main GSD table
		if ($this->flushTable)
		{
			$this->flushTable();
		}

		// Start the migration process
		$this->migrateSnippets();
		$this->migrateParams();

		// Save log to a file
		$this->saveLog();
	}

	/**
	 *  Save log to a file
	 *
	 *  @return  void
	 */
	private function saveLog()
	{
		array_unshift(
			$this->log,
			str_repeat('-', 19),
			Factory::getDate(),
			str_repeat('-', 19)
		);

		$log = implode(PHP_EOL, $this->log) . PHP_EOL . PHP_EOL;

		try
		{
			$logFile = __DIR__ . '/migrator/migrator.log';

			File::write($logFile, $log);
		}
		catch (Exception $e)
		{
			$this->log[] = "Can't write log file" . $e->getMessage();
		}
	}

	/**
	 *  Move parameters from system plugin to component
	 *
	 *  @return  void
	 */
	private function migrateParams()
	{
		$this->log[] = 'Params: Migration Started.';

		$pluginParams = $this->getPluginParams();

		// Make sure we have valid parameters. Otherwise abort.
		if (empty($pluginParams) || strpos($pluginParams, 'sitename_enabled') === false)
		{
			$this->log[] = 'Params: Migration Skipped. Probably already migrated.';
			return;
		}

		// Store parameters to gsd_config
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/tables');

		$table = Table::getInstance('Config', 'GSDTable');
		$table->load('config');
		$table->params = $pluginParams;

		if (!$table->store())
		{
			$this->log[] = 'Params: Cannot store params to config table.';
			return;
		}

		// Backup parameters to a file
		$this->backup('params', $pluginParams);		

		// Remove parameters from plg_system_gsd
		if (!$this->removePluginParams())
		{
			$this->log[] = 'Params: Unable to remove params from system plugin.';
			return;
		}

		// Success!
		$this->log[] = "Params: Migration finished.";
		return true;
	}

	/**
	 *  Move snippet data from each component to com_gsd
	 *
	 *  Note: Items with the snippet property disabled are skipped.
	 *
	 *  @return  void
	 */
	private function migrateSnippets()
	{
		foreach ($this->migrateComponents as $component)
		{
			// Skip component if it's not installed.
			if (!\NRFramework\Functions::extensionInstalled($component))
			{
				$this->log[] = "Snippets - $component: Not installed. Skipped.";
				continue;
			}

			// Get component's snippets from database
			if (!$snippets = $this->getComponentSnippets($component))
			{
				$this->log[] = "Snippets - $component: No snippets found. Skipped.";
				continue;
			}

			// Backup Snippets
			$this->backup($component . '_snippets', json_encode($snippets, JSON_PRETTY_PRINT));

			// Loop through each snippet, validate it and save it to gsd table
			foreach ($snippets as $snippet)
			{
				$params = $this->validateSnippetParams($snippet->params);

				// Remove parameters from component's item
				$this->removeComponentSnippet($component, $snippet->id);
				
				if (is_null($params))
				{
					$this->log[] = "Snippets - $component: Item #$snippet->id skipped.";
					continue;
				}

				// Prepare data array
				$data = array(
					'thing'  => $snippet->id,
					'plugin' => $component,
					'params' => json_encode($params),
					'state'  => 1
				);

				// Load GSD model and save the data into the database
				BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/models');
        		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/tables');

				$model  = BaseDatabaseModel::getInstance('Item', 'GSDModel');
				$item   = $model->validate(null, $data);

				$result = $model->save($item);
				
				$this->log[] = "Snippets - $component: Item #$snippet->id transfered.";
			}

			$this->log[] = "Snippets - $component: Migration finished.";
		}
	}

	/**
	 *  Validates snippets parameters and adjusts them to be valid with the new requirements
	 *
	 *  Checks:
	 *  
	 *  1. Removes gsd offset
	 *  2. Renames snippet property to contenttype
	 *  3. Alters rating property
	 *  4. Alters image property
	 *
	 *  @param   object  $data  The snippet params object
	 *
	 *  @return  array          The new snippet params array
	 */
	private function validateSnippetParams($data)
	{
		// Prepare Params
		$data = new Registry($data);

		// Abort if we don't have the required property
		if (!$data->offsetExists('gsd'))
		{
			return;
		}

		$p = $data->offsetGet('gsd');

		// Abort if there is no selected content type
		if (!isset($p->snippet) || $p->snippet == '0')
		{
			return;
		}

		$contentType = $p->snippet;

		//Shorthand for snippet data
		$s = $p->$contentType;

		/**
		 *  Rating Properties
		 */
		if ($contentType != 'review' && isset($s->ratingValue) && isset($s->reviewCount))
		{
			$ratingValue = (float) $s->ratingValue;
			$reviewCount = (int) $s->reviewCount;

			// If ratingValue and reviewCount are greater than 0 means that we have
			// a valid custom rating data. Otherwise we disable the rating.
			$ratingOption = ($ratingValue > 0 && $reviewCount > 0) ? 2 : 0;

			// Add rating data to object
			$s->rating = array(
				'option'      => $ratingOption,
				'ratingValue' => $ratingValue,
				'reviewCount' => $reviewCount
			);
		}

		// On Reviews Content Type we have a ratingValue property only
		if ($contentType == 'review' && isset($s->ratingValue) && (float) $s->ratingValue > 0)
		{
			$s->rating = array(
				'option'      => '2',
				'ratingValue' => $s->ratingValue
			);
		}

		// Unset old properties
		unset($s->ratingValue);
		unset($s->reviewCount);

		/**
		 *  Image Property
		 *
		 *  Since the image option was available only in the Reviews Content Type we are going to
		 *  check only that content type. All the other content types will be set to Inherit.
		 */
		if ($contentType == 'review' && isset($s->image))
		{
			$s->image = array(
				'option' => $s->image,
				'file'   => $s->imgupload,
				'url'    => $s->imgurl
			);

			// Unset old properties
			unset($s->imgupload);
			unset($s->imgurl);
		}

		$data = array(
			'contenttype' => $contentType,
			$contentType  => $s,
			'customcode'  => $p->customcode
		);

		return $data;
	}

	/**
	 *  Get snippets from database
	 *
	 *  @param   string  $component  The component's alias name
	 *
	 *  @return  object
	 */
	private function getComponentSnippets($component)
	{
		$db    = $this->getDB();
		$query = $db->getQuery(true);

		if ($component == 'k2')
		{
			$query
				->select('id, plugins as params')
				->from('#__k2_items')
				->where($db->quoteName('plugins') . ' LIKE ' . $db->quote('%gsd%'));
		}

		if ($component == 'content')
		{
			$query
		    	->select('id, attribs as params')
		    	->from('#__content')
		    	->where($db->quoteName('attribs') . ' LIKE ' . $db->quote('%gsd%'))
		    	->where($db->quoteName('state') . ' IN (0,1,2)');
		}

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 *  Remove snippet parameters from component's item
	 *
	 *  @param   integer  $id  The item's primary key
	 *
	 *  @return  bool
	 */
	public function removeComponentSnippet($component, $id)
	{
		if (!in_array($component, $this->migrateComponents))
		{
			return;
		}

		if ($component == 'content')
		{
			$column = 'attribs';
			$table  = 'content';
		}

		if ($component == 'k2')
		{
			$column = 'plugins';
			$table  = 'k2_items';
		}

		$db = $this->getDB();

        $params = $db->setQuery(
            $db->getQuery(true)
                ->select($db->quoteName($column))
                ->from($db->quoteName('#__' . $table))
                ->where($db->quoteName('id') . ' = ' . $id)
        )->loadResult();

        $params = json_decode($params);

        if (!isset($params->gsd))
        {
        	return;
        }

        unset($params->gsd);

        $db->setQuery(
        	$db->getQuery(true)
			->update($db->quoteName('#__' . $table))
			->set($db->quoteName($column) . ' = ' . $db->quote(json_encode($params)))
			->where($db->quoteName('id') . ' = ' . $id)
        );

        return $db->execute();
	}

	/**
	 *  Get GSD System Plugin Parameters
	 *
	 *  @return  mixed    Registry object on success, false on failure
	 */
	private function getPluginParams()
	{
		$db = $this->getDB();

        return $db->setQuery(
            $db->getQuery(true)
                ->select('params')
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('name') . ' = ' . $db->quote($this->systemPluginName))
        )->loadResult();
	}

	/**
	 *  Removes saved parameters from system plugin in order to avoid future re-migration.
	 *
	 *  @return  bool
	 */
	private function removePluginParams()
	{
		$db = $this->getDB();
		 
        $db->setQuery(
        	$db->getQuery(true)
				->update($db->quoteName('#__extensions'))
				->set(array($db->quoteName('params') . ' = ' . $db->quote('')))
				->where(array($db->quoteName('name') . ' = ' . $db->quote($this->systemPluginName)))
        );
		 
		return $db->execute();
	}

	/**
	 *  Remove all records from local table
	 *
	 *  @return  void
	 */
	private function flushTable()
	{
		$this->log[] = "Migrator: Flushing GSD main table.";

		$db = Factory::getDBo();
		$db->setQuery('TRUNCATE #__gsd');
		$db->execute();
	}

	/**
	 *  Writes data to a backup file
	 *
	 *  @param   string  $filename  The filename
	 *  @param   string  $data      The data to be written into the file
	 *
	 *  @return  void
	 */
	private function backup($filename, $data)
	{
		try
		{
			File::write(__DIR__ . '/migrator/backup/' . $filename . '.bak', $data);
		}
		catch (Exception $e)
		{
			$this->log[] = "Can't write backup file: $filename" . $e->getMessage();
		}
	}
}