<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') || die;

use Akeeba\Component\AdminTools\Administrator\Extension\AdminToolsComponent;
use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Version;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;
use Joomla\Registry\Registry;

/**
 * Model to reset the Joomla! Update information.
 *
 * This is really only necessary for Joomla! 5.1.0 or later, where the TUF-based updates get constantly broken.
 *
 * @since   7.6.0
 */
final class JupdateModel extends BaseDatabaseModel
{
	/**
	 * Joomla update URL for TUF updates.
	 *
	 * @var    string
	 * @since  7.6.0
	 */
	private const JOOMLA_TUF_URL = 'https://update.joomla.org/cms/';

	/**
	 * Joomla update URL for legacy XML updates.
	 *
	 * @var    string
	 * @since  7.6.0
	 */
	private const JOOMLA_LEGACY_URL = 'https://update.joomla.org/core/list.xml';

	/**
	 * Database tables relevant to the Joomla! update process.
	 *
	 * @var    array
	 * @since  7.6.0
	 */
	private const UPDATE_RELEVANT_TABLES = ['#__extensions', '#__update_sites', '#__update_sites_extensions', '#__updates', '#__tuf_metadata'];

	/**
	 * Resets Joomla! Update.
	 *
	 * This method:
	 * - Makes sure there is a files_joomla extension in the database.
	 * - Makes sure there is one and only one update site for Joomla!, of the correct type, in the database.
	 * - Makes sure there is one and only one TUF metadata record for Joomla! in the database (Joomla! 5.1.0 or later).
	 * - Resets the TUF snapshot, targets, and timestamp metadata (Joomla! 5.1.0 or later).
	 * - Removes all found Joomla! updates from the database.
	 * - Resets the Joomla! Update component options.
	 *
	 * This ensures that Joomla! Update can, in fact, work properly on the site.
	 *
	 * @return  void
	 * @throws  Exception
	 * @since   7.6.0
	 */
	public function resetJoomlaUpdate(): void
	{
		$isJoomla510plus = version_compare(JVERSION, '5.1.0', 'ge');
		$type            = $isJoomla510plus ? 'tuf' : 'collection';

		// Check and repair update-relevant database tables.
		foreach (self::UPDATE_RELEVANT_TABLES as $tableName)
		{
			$this->checkAndRepairTable($tableName);
		}

		// Make sure we have an update site
		$updateSiteId = $this->ensureJoomlaUpdateSite($type);

		// Make sure we do not have duplicate update sites for Joomla!.
		$this->ensureOnlyJoomlaUpdateSite($updateSiteId);

		// Reset TUF metadata on Joomla! 5.1.0 or later
		if ($isJoomla510plus)
		{
			$this->resetJoomlaTUFMetadata($updateSiteId);
		}

		// Reset the options in the Joomla! Update component
		$this->resetUpdateComponentsOptions();

		// Delete update records
		$this->deleteUpdateRecordsByUpdateSiteId($updateSiteId);
	}

	/**
	 * Resets the TUF metadata for Joomla!.
	 *
	 * @param   int  $updateSiteId  The Joomla update site ID
	 *
	 * @return  void
	 * @since   7.6.0
	 */
	private function resetJoomlaTUFMetadata(int $updateSiteId): void
	{
		// Make sure there is a TUF metadata entry for Joomla
		$tufMetadata = $this->getTUFMetadata($updateSiteId);

		if (empty($tufMetadata))
		{
			$this->createTUFMetadata($updateSiteId);

			return;
		}

		// Make sure there is only one TUF metadata record
		$this->ensureOnlyTUFMetadata($tufMetadata->id, $updateSiteId);

		// Update the TUF metadata
		$tufMetadata->root      = $this->getJoomlaTUFRootJson();
		$tufMetadata->targets   = null;
		$tufMetadata->snapshot  = null;
		$tufMetadata->timestamp = null;
		$tufMetadata->mirrors   = null;

		$db = $this->getDatabase();
		$db->updateObject('#__tuf_metadata', $tufMetadata, 'id');
	}

	/**
	 * Make sure there is a Joomla! update site record in the database.
	 *
	 * @param   string  $type  The type of Joomla! update site: 'tuf' or 'collection'.
	 *
	 * @return  int  The update site ID
	 * @since   7.6.0
	 */
	private function ensureJoomlaUpdateSite(string $type = 'tuf'): int
	{
		if (!in_array($type, ['tuf', 'collection']))
		{
			$type = 'tuf';
		}

		// Make sure there is a files_joomla extension, and that it's correct.
		$extRecord = $this->getJoomlaExtensionRecord();

		if (!$extRecord)
		{
			$extensionId = $this->createJoomlaExtension();
		}
		else
		{
			$extensionId = $extRecord->extension_id;

			$this->updateJoomlaExtension($extRecord);
		}

		// Make sure there is an update site for files_joomla
		$updateSite = $this->getJoomlaUpdateSite($type);

		if (empty($updateSite))
		{
			$updateSiteId = $this->createJoomlaUpdateSite($extensionId, $type);
		}
		else
		{
			$this->updateJoomlaUpdateSite($type, $updateSite);

			$updateSiteId = $updateSite->update_site_id;
		}

		return $updateSiteId;
	}

	/**
	 * Get the TUF root metadata for Joomla.
	 *
	 * @return  string|null
	 * @since   7.6.0
	 */
	private function getJoomlaTUFRootJson(): ?string
	{
		$version     = new Version();
		$httpOptions = new Registry();

		$httpOptions->set('userAgent', $version->getUserAgent('Joomla', true, false));
		$httpOptions->set('follow_location', true);

		try
		{
			$http     = HttpFactory::getHttp($httpOptions);
			$response = $http->get(self::JOOMLA_TUF_URL . 'root.json');
		}
		catch (Exception $e)
		{
			return null;
		}

		if ($response->code !== 200)
		{
			return null;
		}

		$body = trim($response->body ?: '');

		if (empty($body))
		{
			return null;
		}

		// TODO Validate format. See https://joomla.social/@hleithner/112763832172599868

		return $body;
	}

	/**
	 * Returns a new database query object
	 *
	 * @return  QueryInterface
	 * @since   7.6.0
	 */
	private function makeQuery(): QueryInterface
	{
		$db = $this->getDatabase();

		return method_exists($db, 'createQuery')
			? $db->createQuery()
			: $db->getQuery(true);
	}

	/**
	 * Get the Update Site record pointing to Joomla's update source.
	 *
	 * @param   string  $type  The update site type: 'tuf', or 'collection'.
	 *
	 * @return  object|null  NULL if there is no update site
	 * @since   7.6.0
	 */
	private function getJoomlaUpdateSite(string $type = 'tuf'): ?object
	{
		if (!in_array($type, ['tuf', 'collection']))
		{
			$type = 'tuf';
		}

		$db         = $this->getDatabase();
		$innerQuery = $this->makeQuery()
			->select('1')
			->from($db->quoteName('#__extensions', 'x'))
			->where(
				[
					$db->quoteName('x.extension_id') . '=' . $db->quoteName('u.extension_id'),
					$db->quoteName('x.element') . '=' . $db->quote('joomla'),
					$db->quoteName('x.type') . '=' . $db->quote('file'),
				]
			);

		$middleQuery = $this->makeQuery()
			->select('1')
			->from($db->quoteName('#__update_sites_extensions', 'u'))
			->where(
				[
					$db->quoteName('u.update_site_id') . '=' . $db->quoteName('s.update_site_id'),
					'EXISTS(' . $innerQuery . ')',
				]
			);

		$query = $this->makeQuery()
			->select('*')
			->from($db->quoteName('#__update_sites', 's'))
			->where(
				[
					'EXISTS(' . $middleQuery . ')',
					$db->quoteName('s.type') . ' = :type',
				]
			)
			->bind(':type', $type, ParameterType::STRING);

		return $db->setQuery($query)->loadObject() ?: null;
	}

	/**
	 * Get Joomla's `#__extensions` record.
	 *
	 * @return  object|null
	 * @since   7.6.0
	 */
	private function getJoomlaExtensionRecord(): ?object
	{
		$db    = $this->getDatabase();
		$query = $this->makeQuery()
			->select('*')
			->from($db->quoteName('#__extensions'))
			->where(
				[
					$db->quoteName('element') . '=' . $db->quote('joomla'),
					$db->quoteName('type') . '=' . $db->quote('file'),
				]
			);

		return $db->setQuery($query)->loadObject() ?: null;
	}

	/**
	 * Creates the files_joomla extension and returns the ID of the created record.
	 *
	 * @return  int
	 * @since   7.6.0
	 */
	private function createJoomlaExtension(): int
	{
		$version = new Version();
		$db      = $this->getDatabase();
		$o       = (object) [
			'extension_id'     => null,
			'package_id'       => 0,
			'name'             => 'files_joomla',
			'type'             => 'file',
			'element'          => 'joomla',
			'changelogurl'     => null,
			'folder'           => '',
			'client_id'        => 0,
			'enabled'          => 1,
			'access'           => 1,
			'protected'        => 1,
			'locked'           => 1,
			'manifest_cache'   => sprintf(
				'{"name":"files_joomla","type":"file","creationDate":"%s","author":"Joomla! Project","copyright":"(C) %s Open Source Matters, Inc.","authorEmail":"admin@joomla.org","authorUrl":"www.joomla.org","version":"%s","description":"FILES_JOOMLA_XML_DESCRIPTION","group":""}',
				gmdate('Y-m'),
				gmdate('Y'),
				$version->getShortVersion()
			),
			'params'           => '',
			'checked_out'      => null,
			'checked_out_time' => null,
			'ordering'         => 0,
			'state'            => 0,
			'note'             => null,
			'custom_data'      => null,
		];

		$db->insertObject('#__extensions', $o, 'extension_id');

		return $o->extension_id;
	}

	/**
	 * Create the update site for Joomla (update type TUF) and return its ID.
	 *
	 * @param   int     $extensionId  The Joomla extension ID
	 * @param   string  $type         The update site type: 'tuf', 'extension', or 'collection'.
	 *
	 * @return  int
	 * @since   7.6.0
	 */
	private function createJoomlaUpdateSite(int $extensionId, string $type = 'tuf'): int
	{
		if (!in_array($type, ['tuf', 'collection']))
		{
			$type = 'tuf';
		}

		if ($type == 'tuf')
		{
			$url = self::JOOMLA_TUF_URL;
		}
		else
		{
			$url = self::JOOMLA_LEGACY_URL;
		}

		$db = $this->getDatabase();
		/**
		 * !!!!! IMPORTANT !!!!!
		 *
		 * Joomla Update **assumes** that the Joomla core update site has an ID of 1. Therefore, we MUST create an
		 * update site with an ID of 1.
		 */
		$updateSite = (object) [
			// IMPORTANT! Read the comment above
			'update_site_id'       => 1,
			'name'                 => 'Joomla! Core',
			'type'                 => $type,
			'location'             => $url,
			'enabled'              => 1,
			'last_check_timestamp' => 0,
			'extra_query'          => '',
			'checked_out'          => null,
			'checked_out_time'     => null,
		];

		// Create the update site
		if (!$db->insertObject('#__update_sites', $updateSite, 'update_site_id'))
		{
			// Lets delete update site #1, as this MUST always be Joomla's update site.
			$this->deleteUpdateSiteById(1);
			// Now try to create it afresh
			$db->insertObject('#__update_sites', $updateSite, 'update_site_id');
		}

		// Make sure the update site was, in fact, created correctly
		$updateSiteId = $updateSite->update_site_id;

		if (empty($updateSiteId))
		{
			return 0;
		}

		// Create glue record
		$updateSiteExtensions = (object) [
			'update_site_id' => $updateSiteId,
			'extension_id'   => $extensionId,
		];
		$db->insertObject('#__update_sites_extensions', $updateSiteExtensions);

		// Return update site ID
		return $updateSiteId;
	}

	/**
	 * Get the Joomla TUF metadata record.
	 *
	 * @param   int  $updateSiteId  The update site ID for which to get TUF metadata.
	 *
	 * @return  object|null
	 * @since   7.6.0
	 */
	private function getTUFMetadata(int $updateSiteId): ?object
	{
		$db    = $this->getDatabase();
		$query = $this->makeQuery()
			->select('*')
			->from($db->quoteName('#__tuf_metadata'))
			->where('update_site_id = :update_site_id')
			->bind(':update_site_id', $updateSiteId, ParameterType::INTEGER);

		return $db->setQuery($query)->loadObject() ?: null;
	}

	/**
	 * Create a new TUF metadata record for the given update site ID.
	 *
	 * @noinspection PhpReturnValueOfMethodIsNeverUsedInspection
	 *
	 * @param   int  $updateSiteId  The update site ID to create a TUF metadata record for.
	 *
	 * @return  int|null
	 * @since        7.6.0
	 */
	private function createTUFMetadata(int $updateSiteId): ?int
	{
		$db = $this->getDatabase();
		$o  = (object) [
			'id'             => null,
			'update_site_id' => $updateSiteId,
			'root'           => null,
			'targets'        => null,
			'snapshot'       => null,
			'timestamp'      => null,
			'mirrors'        => null,

		];
		$db->insertObject('#__tuf_metadata', $o, 'id');

		return $o->id;
	}

	/**
	 * Deletes update records for the given update site.
	 *
	 * @param   int  $updateSiteId  The update site ID to nuke updates for.
	 *
	 * @return  void
	 * @since   7.6.0
	 */
	private function deleteUpdateRecordsByUpdateSiteId(int $updateSiteId)
	{
		$db    = $this->getDatabase();
		$query = $this->makeQuery()
			->delete($db->quoteName('#__updates'))
			->where($db->quoteName('update_site_id') . ' = :update_site_id')
			->bind(':update_site_id', $updateSiteId, ParameterType::INTEGER);

		$db->setQuery($query)->execute();
	}

	/**
	 * Updates the Joomla extension record if necessary
	 *
	 * @param   object  $extRecord  The extension record to update
	 *
	 * @return  void
	 * @since   7.6.0
	 */
	private function updateJoomlaExtension(object $extRecord): void
	{
		$currentHash = md5(serialize($extRecord));

		$extRecord->name      = 'files_joomla';
		$extRecord->folder    = '';
		$extRecord->client_id = 0;
		$extRecord->enabled   = 1;
		$extRecord->access    = 1;
		$extRecord->protected = 1;
		$extRecord->locked    = 1;
		$extRecord->state     = 0;

		$newHash = md5(serialize($extRecord));

		if ($newHash === $currentHash)
		{
			return;
		}

		$db = $this->getDatabase();
		$db->updateObject('#__extensions', $extRecord, 'extension_id');
	}

	/**
	 * Make sure there is only ONE update site for Joomla.
	 *
	 * @param   int  $keepSiteId  The update site ID we want to keep in the database.
	 *
	 * @return  void
	 * @since   7.6.0
	 */
	private function ensureOnlyJoomlaUpdateSite(int $keepSiteId)
	{
		// Find all update sites for Joomla which are not the update site ID we want to keep.
		$db    = $this->getDatabase();
		$extId = $this->getJoomlaExtensionRecord()->extension_id;
		$query = $this->makeQuery()
			->select('*')
			->from($db->quoteName('#__update_sites_extensions'))
			->where(
				[
					$db->quoteName('extension_id') . ' = :extension_id',
					$db->quoteName('update_site_id') . ' != :update_site_id',
				]
			)
			->bind(':extension_id', $extId, ParameterType::INTEGER)
			->bind(':update_site_id', $keepSiteId, ParameterType::INTEGER);

		$updateSiteIds = $db->setQuery($query)->loadColumn() ?: [];

		// If nothing is found, there's nothing to do.
		if (empty($updateSiteIds))
		{
			return;
		}

		// Delete the update sites
		$query = $this->makeQuery()
			->delete($db->quoteName('#__update_sites'))
			->whereIn($db->quoteName('update_site_id'), $updateSiteIds, ParameterType::INTEGER);
		$db->setQuery($query)->execute();

		// Delete the glue records
		$query = $this->makeQuery()
			->delete($db->quoteName('#__update_sites_extensions'))
			->whereIn($db->quoteName('update_site_id'), $updateSiteIds, ParameterType::INTEGER);
		$db->setQuery($query)->execute();

		// Delete the TUF metadata records (Joomla! 5.1.0 only)
		if (version_compare(JVERSION, '5.1.0', 'ge'))
		{
			$query = $this->makeQuery()
				->delete($db->quoteName('#__tuf_metadata'))
				->whereIn($db->quoteName('update_site_id'), $updateSiteIds, ParameterType::INTEGER);
			$db->setQuery($query)->execute();
		}
	}

	/**
	 * Updates the Joomla! update site if necessary.
	 *
	 * @param   string  $type        The update type: 'tuf', or 'collection'
	 * @param   object  $updateSite  The update site object
	 *
	 * @return  void
	 * @since   7.6.0
	 */
	private function updateJoomlaUpdateSite(string $type, object $updateSite)
	{
		if (!in_array($type, ['tuf', 'collection']))
		{
			$type = 'tuf';
		}

		if ($type == 'tuf')
		{
			$url = self::JOOMLA_TUF_URL;
		}
		else
		{
			$url = self::JOOMLA_LEGACY_URL;
		}

		$currentHash = md5(serialize($updateSite));

		$updateSite->type                 = $type;
		$updateSite->location             = $url;
		$updateSite->enabled              = 1;
		$updateSite->last_check_timestamp = 0;
		$updateSite->extra_query          = '';

		$newHash = md5(serialize($updateSite));

		if ($newHash === $currentHash)
		{
			return;
		}

		$db = $this->getDatabase();
		$db->updateObject('#__update_sites', $updateSite, 'update_site_id');
	}

	/**
	 * Make sure there is only ONE record for TUF metadata for the given update site.
	 *
	 * @param   int  $keepId        The TUF metadata record to keep
	 * @param   int  $updateSiteId  The Joomla! update site ID
	 *
	 * @return  void
	 * @since   7.6.0
	 */
	private function ensureOnlyTUFMetadata(int $keepId, int $updateSiteId)
	{
		$db    = $this->getDatabase();
		$query = $this->makeQuery()
			->delete($db->quoteName('#__tuf_metadata'))
			->where(
				[
					$db->quoteName('update_site_id') . ' = :update_site_id',
					$db->quoteName('id') . ' != :id',
				]
			)
			->bind(':update_site_id', $updateSiteId, ParameterType::INTEGER)
			->bind(':id', $keepId, ParameterType::INTEGER);

		$db->setQuery($query)->execute();
	}

	/**
	 * Resets the component options for Joomla! Update and Extensions Update.
	 *
	 * @return  void
	 * @throws  Exception
	 * @since   7.6.0
	 */
	private function resetUpdateComponentsOptions(): void
	{
		/** @var AdminToolsComponent $componentExtension */
		$app                = Factory::getApplication();
		$componentExtension = $app->bootComponent('com_admintools');
		$paramsService      = $componentExtension->getComponentParametersService();

		// Change com_joomlaupdate options, default update source, stable versions, no custom URL
		$cParams = ComponentHelper::getParams('com_joomlaupdate');
		$cParams->set('updatesource', 'default');
		$cParams->set('minimum_stability', '4');
		$cParams->set('customurl', '');

		$paramsService->save($cParams, 'com_joomlaupdate');

		// Change com_installer options, updates caching to 6 and stability to Stable
		$cParams = ComponentHelper::getParams('com_installer');
		$cParams->set('cachetimeout', '6');
		$cParams->set('minimum_stability', '4');

		$paramsService->save($cParams, 'com_installer');
	}

	/**
	 * Deletes an update site given its ID
	 *
	 * @param   int  $updateSiteId
	 *
	 * @return  void
	 * @since   7.6.0
	 */
	private function deleteUpdateSiteById(int $updateSiteId): void
	{
		$db = $this->getDatabase();

		$query = $this->makeQuery()
			->delete($db->quoteName('#__update_sites'))
			->where($db->quoteName('update_site_id') . ' = :update_site_id')
			->bind(':update_site_id', $updateSiteId, ParameterType::INTEGER);

		$db->setQuery($query)->execute();

		$query = $this->makeQuery()
			->delete($db->quoteName('#__update_sites_extensions'))
			->where($db->quoteName('update_site_id') . ' = :update_site_id')
			->bind(':update_site_id', $updateSiteId, ParameterType::INTEGER);

		$db->setQuery($query)->execute();

		if (version_compare(JVERSION, '5.1.0', 'ge'))
		{
			$query = $this->makeQuery()
				->delete($db->quoteName('#__tuf_metadata'))
				->where($db->quoteName('update_site_id') . ' = :update_site_id')
				->bind(':update_site_id', $updateSiteId, ParameterType::INTEGER);

			$db->setQuery($query)->execute();
		}
	}

	private function checkAndRepairTable(string $tableName): void
	{
		$db = $this->getDatabase();

		$this->executeUnpreparedQuery('REPAIR TABLE ' . $db->quoteName($tableName));
		$this->executeUnpreparedQuery('OPTIMIZE TABLE ' . $db->quoteName($tableName));
	}

	/**
	 * Executes an unprepared SQL statement.
	 *
	 * The PDO driver doesn't distinguish between prepared and unprepared statements. Therefore we can just run anything
	 * we please. The MySQLi driver, however, has a distinction between prepared and unprepared statements. We cannot
	 * run certain SQL comments (such as OPTIMIZE and REPAIR) over a prepared statement. The MySQLi driver has a handy
	 * method called executeUnpreparedStatement which is protected and which runs this kind of statements.
	 *
	 * This here method tries to figure out if the database driver object has that method and use it instead of the
	 * prepared statement.
	 *
	 * @param   string  $sql
	 *
	 * @return  mixed
	 */
	private function executeUnpreparedQuery($sql)
	{
		$db     = $this->getDatabase();
		$sql    = $db->replacePrefix($sql);
		$refObj = new \ReflectionObject($db);

		try
		{
			$method = $refObj->getMethod('executeUnpreparedQuery');
			$method->setAccessible(true);

			return $method->invoke($db, $sql);
		}
		catch (\ReflectionException $e)
		{
			return $db->setQuery($sql)->execute();
		}
	}
}