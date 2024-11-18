<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Scanner\Util;

defined('_JEXEC') || die;

use Akeeba\Component\AdminTools\Administrator\Scanner\Mixin\Singleton;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

/**
 * File Change Scanner configuration management
 *
 * The configuration is persisted in the component's configuration (Options page)
 */
class Configuration
{
	use Singleton;

	/**
	 * Default configuration for the File Change Scanner
	 *
	 * @var   array
	 */
	protected $defaultConfig = [
		// Log level (see LogLevel class)
		'logLevel'                  => 4,
		// Minimum execution time
		'minExec'                   => 3,
		// Maximum execution time
		'maxExec'                   => 5,
		// Runtime bias
		'runtimeBias'               => 75,
		// Maximum directories to scan per batch
		'dirThreshold'              => 50,
		// Maximum files to scan per batch
		'fileThreshold'             => 100,
		// Directories to exclude
		'directoryFilters'          => [],
		// Files to exclude
		'fileFilters'               => [],
		// File extensions to scan (everything else is excluded)
		'scanExtensions'            => ['php', 'phps', 'phtml', 'php3', 'inc'],
		// Include files with double extensions, e.g. .php.png (if the php extension is in the File Extensions To Scan)
		'doubleExtensions'          => 1,
		// Case-insensitive extension matching
		'caseInsensitiveExtensions' => 0,
		// Large file threshold
		'largeFileThreshold'        => 524288,
		// Create diffs for scanned files
		'scandiffs'                 => false,
		// Do not create a record for non-threat files
		'scanignorenonthreats'      => false,
		// Do not scan file over this threshold
		'oversizeFileThreshold'     => 5242880,
		// Email address to send scan results to
		'scanemail'                 => '',
		// Conditional email sending only when actionable items are found
		'scan_conditional_email'    => false,
	];

	/**
	 * The Admin Tools options storage
	 *
	 * @var   Registry
	 */
	private $componentConfig;

	/**
	 * Config constructor.
	 *
	 * Initializes the storage.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->componentConfig = ComponentHelper::getParams('com_admintools');
	}

	/**
	 * Get a configuration key.
	 *
	 * @param   string  $key  The configuration key to retrieve
	 *
	 * @return  mixed  The value of the configuration key.
	 */
	public function get(string $key)
	{
		$default = array_key_exists($key, $this->defaultConfig) ? $this->defaultConfig[$key] : null;

		return $this->componentConfig->get($key, $default);
	}

	/**
	 * Set a configuration key.
	 *
	 * Do not include the 'filescanner.' prefix, it is added automatically.
	 *
	 * @param   string  $key    The configuration key to set
	 * @param   mixed   $value  The value to set it to
	 *
	 * @return  void
	 */
	public function set(string $key, $value)
	{
		$this->componentConfig->set($key, $value);
	}

	/**
	 * Set multiple configuration keys at once
	 *
	 * Do not include the 'filescanner.' prefix in the keys, it is added automatically.
	 *
	 * @param   array  $params  An array of configuration key => value pairs
	 * @param   bool   $save    Should I persist the keys to the database upon setting their value?
	 *
	 * @return  void
	 */
	public function setMany(array $params, bool $save = true)
	{
		foreach ($params as $k => $v)
		{
			$this->componentConfig->set($k, $v);
		}

		if ($save)
		{
			$this->save();
		}
	}

	/**
	 * Persist the configuration to the database
	 *
	 * @return  void
	 */
	public function save()
	{
		Factory::getApplication()
		       ->bootComponent('com_admintools')
		       ->getComponentParametersService()
		       ->save($this->componentConfig);
	}
}
