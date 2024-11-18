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
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;

/**
 *  Google Structured Data Migrator
 *
 *  The purpose of this class is to migrate data and offer a non-breaking update from v3.1.9 and v3.2.0 (dev) to v4.0.0.
 * 	
 * 	Changes
 *  - Adds 'title' column to gsd table
 *  - Adds 'contenttype' column to gsd table
 *  - Adds 'note' column to gsd table
 *  - Removes 'thing' column from gsd table
 *  - Removes Dynamic Items completely
 *  - Adds missing override_property introduced after 3.2.0 came out
 *  - Fixes brand property
 *  - Fixes new custom code path
 *  - Renames 'categories' assignment property name to 'category'
 */
class GSDMigrator4
{
	/**
	 *  Override the database object for testing purposes and sharing data between different databases
	 *
	 *  @var  mixed
	 */
	private $db;

	/**
	 * The extension's version before the update
	 *
	 * @var string
	 */
	private $version;

	 /**
	  * Class constructor
	  *
	  * @param string $version      The extension's version before update
	  * @param mixed $db			Database options
	  */
	public function __construct($version = null, $db = null)
	{
		// Load GSD model
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/models');
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/tables');

		$this->version = $version;
		$this->db = $db;
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
		$app = Factory::getApplication();

		// Migrate Items
		$total_items = $this->migrateItems();

		if ($total_items > 0)
		{
			$app->enqueueMessage(
				$total_items . ' Structured Data Items have been updated to comply with the new extension requirements. '
			, 'notice');
		}

		// Migrate Dynamic Items
		$total_dynamic_items = $this->migrateDynamicItems();
		if ($total_dynamic_items > 0)
		{
			$app->enqueueMessage(
				'The Dynamic Items section introduced in the v3.2.0 developer release is no longer available. 
				To make things simpler and the structured data process faster, we decided to merge the Dynamic Items section
				with the old Items Section and manage all our structured in one place. 
				Therefore, your <b>' . $total_dynamic_items . '</b> Dynamic Items are now available in the Items section.'
			,'warning');
		}

		// Migrate Auto-Mode
		if ($automode_plugins = $this->migrateAutoMode())
		{
			foreach ($automode_plugins as $key => $plugin)
			{
				$plugin = ucfirst($plugin);
				$app->enqueueMessage('The <b>' . $plugin . ' Auto Mode</b> option has been removed. To keep the Product Structured Data generated on <b>all ' . $plugin . ' product pages</b>, we have automatically created the respective Item in the Items section.', 'warning');
			}
		}

		// Migrate Mapping Options
		$migrate_mapping_options = $this->migrateMappingOptions();
	}

	/**
	 * Transform content type properites in the database based on the new MappingOptions field.
	 *
	 * @return void
	 */
	public function migrateMappingOptions()
	{
		$db = $this->getDB();
		$query = $db->getQuery(true);
		$query
			->select('*')
			->from('#__gsd')
			->where('state >= 0');
		$db->setQuery($query);

		if (!$items = $db->loadObjectList())
		{
			return;
		}
		
		$total_items_migrated = 0;

		foreach ($items as $key => $item)
		{
			$type = $item->contenttype;

			if (in_array($type, ['custom_code', 'jobposting']))
			{
				continue;
			}

			$params = json_decode($item->params);
			
			// If params type offset doesn't exist, the item is probably not saved after the content type selection.
			if (!isset($params->$type))
			{
				continue;
			}

			// If headline.option does exist then the item is already migrated.
			if (isset($params->$type->headline->option))
			{
				continue;
			}

			$properties = $params->$type;
			$properties_new = [];

			foreach ($properties as $key => $property)
			{
				$skip_properties = [
					'multiple', // Fact Check
				];

				if (in_array($key, $skip_properties))
				{
					$properties_new[$key] = $property;
					continue;
				}

				$option_ = null;

				if (is_object($property))
				{
					switch ($key)
					{
						case 'author':
							switch ($property->option)
							{
								case '0':
									$option_ = 'user.name';
									break;
								case '1':
									$option_ = 'fixed';
									$fixed_  = $property->user;
									break;
								case '2':
									$option_ = '_custom_';
									$custom_ = $property->custom;
									break;
							}
							break;
						case 'image':
							switch ($property->option)
							{
								case '1': // Inherit
									$option_ = 'gsd.item.image';
									break;
								case '2': // Upload
									$option_ = 'fixed';
									$fixed_  = $property->file;
									break;
								case '3': // Custom
									$option_ = '_custom_';
									$custom_ = $property->url;
									break;
							}
							break;
						case 'rating':
							switch ($property->option)
							{
								case '0': // Disabled
									$properties_new['rating_value'] = [
										'option' => '_custom_',
										'custom' => '0',
									];
									break;
								case '1': // Inherit
									$properties_new['rating_value'] = [
										'option' => 'gsd.item.ratingValue',
									];
									$properties_new['review_count'] = [
										'option' => 'gsd.item.reviewCount',
									];
									continue;
									break;
								case '2': // Custom
									$properties_new['rating_value'] = [
										'option' => '_custom_',
										'custom' => $property->ratingValue,
									];

									if (isset($property->reviewCount))
									{
										$properties_new['review_count'] = [
											'option' => '_custom_',
											'custom' => $property->reviewCount,
										];
									}

									break;
							}
							break;
					}
				}

				if (is_string($property))
				{
					$fixed_  = '';
					$property = trim($property);

					// Some values however belong to fixed values.
					$fixed_property_values = [
						'offerAvailability',
						'offerItemCondition',
						'currency',
						'offerCurrency',
						'claimAuthorType',
						'factcheckRating',
						'performerType'
					];

					if (in_array($key, $fixed_property_values) && !empty($property))
					{
						$option_ = 'fixed';
						$fixed_  = $property;
					} 
					else 
					{
						if (empty($property) || $property == '0000-00-00 00:00:00')
						{
							$option_ = 'gsd.item.' . $key;
							$custom_ = '';
						} else
						{
							$option_ = '_custom_';
							$custom_ = $property;	
						}
					}
				}

				if (!is_null($option_))
				{
					$properties_new[$key] = [
						'option' => $option_,
						'fixed'  => $fixed_,
						'custom' => $custom_
					];
				}
			}

			$params->$type = $properties_new;
			 
			$table = Table::getInstance('Item', 'GSDTable');
			$table->load($item->id);
			$table->params = json_encode($params);

			if ($table->store()){
				$total_items_migrated++;
			}
		}

		return $total_items_migrated;
	}

	/**
	 *
	 *  @return  void
	 */
	private function migrateItems()
	{
		if (!$snippets = $this->getSnippets())
		{
			return;
		}

		$total_items_migrated = 0;

		// Loop through each snippet, validate it and save it to gsd table
		foreach ($snippets as $snippet)
		{
			$id = $snippet->id;

			$this->validateSnippet($snippet);

			$model = BaseDatabaseModel::getInstance('Item', 'GSDModel');
			$item = $model->validate(null, $snippet);

			if ($result = $model->save($item))
			{
				$total_items_migrated++;
			}
		}

		return $total_items_migrated;
	}

	/**
	 * Move Dynamic Items to Items and renames 'dynamic' table to avoid transfering the same times again.
	 *
	 * @return void
	 */
	private function migrateDynamicItems()
	{
		if (!$items = $this->getDynamicSnippets())
		{
			return;
		}

		$total_transfered = 0;

		foreach ($items as $key => $item)
		{
			$params = json_decode($item->params);
			
			// Unset id in order to force model to create a new row in the db.
			unset($item->id);
			
			// Rename component property to plugin
			$item->plugin = $item->component;
			unset($item->component);

			// Rename 'categories' assignment to 'category'
			if (isset($params->assignments) && isset($params->assignments->categories))
			{
				$params->assignments->category = $params->assignments->categories;
				unset($params->assignments->categories);
			}

			$item->note = 'Moved Dynamic Item';

			$item->params = json_encode($params);
			
			$model = BaseDatabaseModel::getInstance('Item', 'GSDModel');
			$data = $model->validate(null, $item);

			if ($result = $model->save($data))
			{
				$total_transfered++;
			}
		}

		// Finally rename the dynamic table if all items have been successfully transfered.
		if (count($items) == $total_transfered)
		{
			$db = $this->getDB();
			$db->setQuery('RENAME TABLE ' . $db->quoteName('#__gsd_dynamic') .' to ' . $db->quoteName('#__gsd_dynamic_bck' . rand(1,100000)));
			$db->execute();
		}

		return $total_transfered;
	}

	/**
	 *  Validates snippets parameters and adjusts them to be valid with the new requirements
	 *
	 *  @param   object  $item  The snippet params object
	 *
	 *  @return  array          The new snippet params array
	 */
	private function validateSnippet(&$item)
	{
		$params = json_decode($item->params);

		// Set Title
		$item->title = $this->getItemTitle($item->plugin, $item->thing);

		// Migrate Note
		$item->note = $params->note;
		unset($params->note);

		// Content Type
		$item->contenttype = $params->contenttype;
		unset($params->contenttype);

		// Migrate Thing ID to Single Publishing Rules
		$integrationSingleRuleName = [
			'content' 		=> 'article',
			'k2' 	  		=> 'k2_items',
			'easyblog'	 	=> 'easyblogsingle',
			'eshop' 		=> 'eshopsingle',
			'eventbooking'  => 'eventbookingsingle',
			'hikashop' 		=> 'hikashopsingle',
			'j2store'	    => 'j2storesingle',
			'jshopping' 	=> 'jshoppingsingle',
			'menus' 		=> 'menu',
			'rsblog'		=> 'rsblogsingle',
			'sppagebuilder' => 'sppagebuildersingle',
			'virtuemart' 	=> 'virtuemartsingle',
			'zoo'			=> 'zoosingle'
		];

		$singleRuleName = $integrationSingleRuleName[$item->plugin];

		$params->assignments = [
			$singleRuleName => [
				'assignment_state' => 1,
				'selection'        => (array) $item->thing
			]
		];

		// Fix Brand Property
		// The brand property should have been declared as 'brand' and not as 'brandName' in the product XML file. 
		// Let's rename it.
		if ($item->contenttype == 'product' && isset($params->product) && isset($params->product->brandName) && !empty($params->product->brandName))
		{
			$params->product->brand = $params->product->brandName;
			unset($params->product->brandName);
		}

		// Fix new Custom Code path only on versions below 3.2 (dev)
		if (version_compare($this->version, '3.2', '<'))
		{
			if ($item->contenttype == 'custom_code' && !isset($item->custom_code) && !isset($item->custom_code->custom_code))
			{
				$params->custom_code = [
					'custom_code' => $params->customcode
				];
			}
		}

		// Unset obsolete properties
		unset($item->thing);

		$item->params = json_encode($params);
	}

	/**
	 *  Get snippets from database
	 *
	 *  @return  object
	 */
	public function getSnippets()
	{
		$db = $this->getDB();
		$query = $db->getQuery(true);
		$query
			->select('*')
			->from('#__gsd')
			->where('params LIKE ' . $db->quote("%contenttype%"));
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	private function getDynamicSnippets()
	{
		if (!$this->dynamicTableExists())
		{
			return;
		}

		$db = $this->getDB();
		$query = $db->getQuery(true);
		$query
			->select('*')
			->from('#__gsd_dynamic');

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	private function dynamicTableExists()
	{
		$db = $this->getDB();
		$db->setQuery('SHOW TABLES LIKE ' . $db->quote('%gsd_dynamic'));
		$db->execute();

		return $db->loadResult();
	}

	private function getItemTitle($plugin, $id)
	{
		$method = 'get' . ucfirst($plugin) . 'TitleQuery';

		if (!method_exists($this, $method) || !$id)
		{
			return ucfirst($plugin) . ' Item #'  . $id;
		}

		$query = $this->$method($id);

		$db = Factory::getDbo();
		$db->setQuery($query);
		$db->execute();

		return $db->loadResult() . ' #' . $id;
	}

	private function getEasyBlogTitleQuery($id)
	{
		return 'SELECT title from #__easyblog_post where id = ' . $id;
	}

	private function getRSBlogTitleQuery($id)
	{
		return 'SELECT title from #__rsblog_posts where id = ' . $id;
	}

	private function getContentTitleQuery($id)
	{
		return 'SELECT title from #__content where id = ' . $id;
	}

	private function getK2TitleQuery($id)
	{
		return 'SELECT title from #__k2_items where id = ' . $id;
	}

	public function migrateAutoMode()
	{
		$db = $this->getDB();
		$query = $db->getQuery(true);
		$query
			->select('*')
			->from('#__extensions')
			->where('folder = ' . $db->quote("gsd"))
			->where('params LIKE ' . $db->quote("%automode%"))
			->where('enabled = 1');

		$db->setQuery($query);

		$plugins = $db->loadObjectList();

		$plugins_migrated = [];

		foreach ($plugins as $key => $plugin)
		{
			// Make sure the component is installed
			if (!NRFramework\Extension::isInstalled($plugin->element))
			{
				continue;
			}

			$params = json_decode($plugin->params);

			if (!isset($params->automode) || (int) $params->automode == 0)
			{
				continue;
			}

			$model = BaseDatabaseModel::getInstance('Item', 'GSDModel');

			$data = [
				'title'       => '[Auto Mode] ' . ucfirst($plugin->element),
				'contenttype' => 'product',
				'plugin'      => $plugin->element,
				'state'		  => true,
				'note' 		  => 'Previously known as the Auto-Mode option',
				'colorgroup'  => '#46a546'
			];

			$item = $model->validate(null, $data);

			if ($result = $model->save($item))
			{
				$plugins_migrated[] = $plugin->element;
			}
		}

		return $plugins_migrated;
	}
}