<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Helper;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;

class Storage
{
	use DatabaseAwareTrait;

	/** @var  self  Singleton instance */
	static $instance = null;

	/** @var  Registry  The internal values registry */
	private $config = null;

	/**
	 * Storage constructor.
	 */
	public function __construct(?DatabaseDriver $dbo = null)
	{
		$this->setDatabase($dbo ?? Factory::getContainer()->get(DatabaseInterface::class));
		$this->load();
	}

	/**
	 * Singleton implementation
	 *
	 * @return  Storage
	 */
	public static function &getInstance()
	{
		if (is_null(static::$instance))
		{
			static::$instance = new Storage();
		}

		return static::$instance;
	}

	/**
	 * Retrieve a value
	 *
	 * @param   string  $key      The key to retrieve
	 * @param   mixed   $default  Default value if the key is not set
	 *
	 * @return  mixed  The key's value (or the default value)
	 */
	public function getValue($key, $default = null)
	{
		return $this->config->get($key, $default);
	}

	/**
	 * Set a configuration value
	 *
	 * @param   string  $key    Key to set
	 * @param   mixed   $value  Value to set the key to
	 * @param   bool    $save   Should I save everything to database?
	 *
	 * @return  mixed  The old value of the key
	 */
	public function setValue($key, $value, $save = false)
	{
		$x = $this->config->set($key, $value);

		if ($save)
		{
			$this->save();
		}

		return $x;
	}

	/**
	 * Resets the storage
	 *
	 * @param   bool  $save  Should I save everything to database?
	 */
	public function resetContents($save = false)
	{
		$this->config->loadArray([]);

		if ($save)
		{
			$this->save();
		}
	}

	/**
	 * Load the configuration information from the database
	 *
	 * @return  void
	 */
	public function load()
	{
		$db    = $this->getDatabase();
		$query = (method_exists($db, 'createQuery') ? $db->createQuery() : $db->getQuery(true))
			->select($db->quoteName('value'))
			->from($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key') . ' = ' . $db->quote('cparams'));
		$db->setQuery($query);

		$error = 0;

		try
		{
			$res = $db->loadResult();
		}
		catch (Exception $e)
		{
			$error = $e->getCode();
		}

		if ($error)
		{
			$res = null;
		}

		$this->config = new Registry($res);
	}

	/**
	 * Save the configuration information to the database
	 *
	 * @return  void
	 */
	public function save()
	{
		$db   = $this->getDatabase();
		$data = $this->config->toString('JSON');

		$query = (method_exists($db, 'createQuery') ? $db->createQuery() : $db->getQuery(true))
			->delete($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key') . ' = ' . $db->quote('cparams'));
		$db->setQuery($query);
		$db->execute();

		$object = (object) [
			'key'   => 'cparams',
			'value' => $data,
		];

		$db->insertObject('#__admintools_storage', $object);
	}
}