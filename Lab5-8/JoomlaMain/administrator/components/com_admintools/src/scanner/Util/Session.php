<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Scanner\Util;

defined('_JEXEC') || die;

use Akeeba\Component\AdminTools\Administrator\Scanner\Mixin\Singleton;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\Session\SessionInterface;

/**
 * Temporary session data management.
 *
 * This is used to manage the persistence of temporary information between consecutive steps of the file change scanner
 * engine in the session.
 */
class Session
{
	use Singleton;

	/**
	 * Known temporary variable keys. Used for reset().
	 *
	 * @var   array
	 */
	private $knownKeys = [
		// Position of the DirectoryIterator when scanning subfolders
		'dirPosition',
		// Position of the DirectoryIterator when scanning files
		'filePosition',
		// Step break flag
		'breakFlag',
		// Files already scanned
		'scannedFiles',
		// ID of this scan
		'scanID',
		// Previously completed step number
		'step',
		// Directories to scan
		'directoryQueue',
		// Files to scan
		'fileQueue',
		// Have I finished listing files in the current directory?
		'hasScannedFiles',
		// Have I finished listing folders in the current directory?
		'hasScannedFolders',
		// Current directory being processed
		'currentDirectory',
		// Current state of the Crawler engine
		'crawlerState',
	];

	/**
	 * @var SessionInterface
	 */
	private $session;

	public function __construct()
	{
		/** @var CMSApplication $app */
		$app           = Factory::getApplication();
		$this->session = $app->getSession();
	}


	/**
	 * Get the value of a temporary variable
	 *
	 * @param   string      $key      The temporary variable to retrieve
	 * @param   null|mixed  $default  Default value to return if the variable is not set
	 *
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		return $this->session->get('com_admintools.filescanner.' . $key, $default);
	}

	/**
	 * Set the value of a temporary variable
	 *
	 * @param   string  $key    The temporary variable to set
	 * @param   mixed   $value  The value to set it to
	 *
	 * @return  void
	 */
	public function set($key, $value)
	{
		if (!in_array($key, $this->knownKeys))
		{
			$this->knownKeys[] = $key;
		}

		$this->session->set('com_admintools.filescanner.' . $key, $value);
	}

	/**
	 * Remove (unset) a temporary variable
	 *
	 * @param   string  $key  The variable to unset
	 *
	 * @return  void
	 */
	public function remove($key)
	{
		$this->session->remove('com_admintools.filescanner.' . $key);
	}

	/**
	 * Remove all temporary variables from the session.
	 *
	 * IMPORTANT! This only removes the variables in $knownKeys unless you pass it a list of key names to reset. In the
	 * latter case BOTH the known keys AND the $resetKeys will be reset.
	 *
	 * @param   array  $resetKeys  Optional. Additional keys to reset beyond $knownKeys
	 */
	public function reset(array $resetKeys = [])
	{
		foreach (array_unique(array_merge($this->knownKeys, $resetKeys)) as $key)
		{
			$this->remove($key);
		}
	}

	public function getKnownKeys()
	{
		return $this->knownKeys;
	}
}
