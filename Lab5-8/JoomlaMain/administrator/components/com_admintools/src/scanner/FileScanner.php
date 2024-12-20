<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Scanner;

defined('_JEXEC') || die;

use Akeeba\Component\AdminTools\Administrator\Scanner\Diff\RendererTextUnified;
use Akeeba\Component\AdminTools\Administrator\Scanner\Logger\Logger;
use Akeeba\Component\AdminTools\Administrator\Scanner\Util\Configuration;
use Akeeba\Component\AdminTools\Administrator\Scanner\Util\Filesystem;
use Akeeba\Component\AdminTools\Administrator\Scanner\Util\Session;
use Exception;
use JDatabaseDriver;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use ReflectionClass;
use ReflectionException;
use stdClass;
use Throwable;

/**
 * File Scanner
 */
class FileScanner
{
	/**
	 * Should I generate diffs for each modified file?
	 *
	 * @var   bool
	 */
	private $generateDiff = null;

	/**
	 * Should I ignore files with zero threat score?
	 *
	 * @var   bool
	 */
	private $ignoreNonThreats = null;

	/**
	 * Size threshold for reading file contents. To calculate the score we have to read the whole file, with large ones
	 * (ie log files) we could run out of memory, causing a fatal error.
	 *
	 * @var   int
	 */
	private $oversizeFileThreshold = 5242880;

	/**
	 * Scanner configuration
	 *
	 * @var   Configuration
	 */
	private $configuration;

	/**
	 * Logger
	 *
	 * @var   Logger
	 */
	private $logger;

	/**
	 * Temporary session storage
	 *
	 * @var   Session
	 */
	private $session;

	/**
	 * Database driver object
	 *
	 * @var   DatabaseDriver
	 */
	private $db;

	/**
	 * FileScanner constructor.
	 *
	 * @param   Configuration  $configuration  Scanner configuration
	 * @param   Session        $session        Temporary session storage
	 * @param   Logger         $logger         Logger
	 */
	public function __construct(Configuration $configuration, Session $session, Logger $logger)
	{
		// Get the injected objects
		$this->configuration = $configuration;
		$this->session       = $session;
		$this->logger        = $logger;

		// Read the configuration
		$this->db                    = Factory::getContainer()->get(DatabaseInterface::class);
		$this->generateDiff          = $configuration->get('scandiffs');
		$this->ignoreNonThreats      = $configuration->get('scanignorenonthreats');
		$this->oversizeFileThreshold = $configuration->get('oversizeFileThreshold');
	}

	/**
	 * Scans a file
	 *
	 * @param   string  $filePath         Absolute file name to read data from
	 *                                    true
	 *
	 * @return  void
	 */
	public function processFile($filePath)
	{
		$this->logger->debug(sprintf("Scanning %s", $filePath));

		$shouldReadContents = true;
		$fileSize           = @filesize($filePath);
		$relativePath       = Filesystem::relativePath($filePath);

		$filedata = (object) [
			'path'       => $relativePath,
			'filedate'   => @filemtime($filePath),
			'filesize'   => $fileSize,
			'data'       => '',
			'checksum'   => hash_file('md5', $filePath),
			'sourcePath' => $filePath,
		];

		// Skip any file larger than 15Mb
		if ($fileSize > $this->oversizeFileThreshold)
		{
			$shouldReadContents = false;
		}

		if ($this->generateDiff && $shouldReadContents)
		{
			$filedata->data = gzdeflate(@file_get_contents($filePath), 9);
		}

		$db = $this->db;

		try
		{
			$this->neutralizeJoomlaQueryLog($db);
		}
		catch (ReflectionException $e)
		{
			// OK, this failed but I can go on like nothing happened.
		}

		try
		{
			$sql       = (method_exists($db, 'createQuery') ? $db->createQuery() : $db->getQuery(true))
				->select('*')
				->from($db->quoteName('#__admintools_filescache'))
				->where($db->qn('path') . ' = :relativePath')
				->setLimit(0, 1)
				->bind(':relativePath', $relativePath);
			$oldRecord = $db->setQuery($sql)->loadObject();
		}
		catch (Exception $e)
		{
			$oldRecord = null;
		}

		if (!is_null($oldRecord))
		{
			// Check for changes
			$fileModified = false;

			if ($oldRecord->filedate != $filedata->filedate)
			{
				$fileModified = true;
			}

			if ($oldRecord->filesize != $filedata->filesize)
			{
				$fileModified = true;
			}

			if ($oldRecord->checksum != $filedata->checksum)
			{
				$fileModified = true;
			}

			if ($fileModified)
			{
				// ### MODIFIED FILE ###
				$this->logFileChange($filedata, $oldRecord);
				unset($oldRecord);

				// Replace the old record
				$sql = (method_exists($db, 'createQuery') ? $db->createQuery() : $db->getQuery(true))
					->delete($db->quoteName('#__admintools_filescache'))
					->where($db->quoteName('path') . ' = :relativePath')
					->bind(':relativePath', $relativePath);
				$db->setQuery($sql);
				$db->execute();

				unset($filedata->sourcePath);
				$db->insertObject('#__admintools_filescache', $filedata);
			}
			else
			{
				unset($oldRecord);

				// Existing file. Get the last log record.
				$sql = (method_exists($db, 'createQuery') ? $db->createQuery() : $db->getQuery(true))
					->select('*')
					->from($db->quoteName('#__admintools_scanalerts'))
					->where($db->quoteName('path') . ' = :relativePath')
					->order($db->qn('scan_id') . ' DESC')
					->setLimit(0, 1)
					->bind(':relativePath', $relativePath);

				$db->setQuery($sql);
				$lastRecord = $db->loadObject();

				// If the file is "acknowledged" (marked safe) we skip its Threat Score calculation
				if (is_object($lastRecord) && $lastRecord->acknowledged)
				{
					unset($lastRecord);

					return;
				}

				unset($lastRecord);

				// The file is not "acknowledged" (marked safe). Calculate its Threat Score.
				$text        = '';
				$threatScore = 5000;
				$diffText    = "###File too large; if this is a log file please exclude it from the scan###\n";

				if ($shouldReadContents)
				{
					$text        = @file_get_contents($filePath);
					$threatScore = $this->calculateThreatScore($text);
					$diffText    = "###SUSPICIOUS FILE###\n";
				}

				// Safe file
				if ($threatScore == 0)
				{
					return;
				}

				// ### SUSPICIOUS EXISTING FILE ###

				// Still here? It's a possible threat! Log it as a modified file.
				$alertRecord = [
					'path'         => $relativePath,
					'scan_id'      => $this->session->get('scanID'),
					'diff'         => $diffText,
					'threat_score' => $threatScore,
					'acknowledged' => 0,
				];

				if ($this->generateDiff)
				{
					$alertRecord['diff'] = <<<ENDFILEDATA
###SUSPICIOUS FILE###
>> Admin Tools detected that this file contains potentially suspicious code.
>> This DOES NOT necessarily mean that it is a hacking script. There is always
>> the possibility of a false alarm. The contents of the file are included
>> below this line so that you can review them.
$text
ENDFILEDATA;
				}

				unset($text);
				$alertRecord = (object) $alertRecord;
				$db->insertObject('#__admintools_scanalerts', $alertRecord);
				unset($alertRecord);
			}
		}
		else
		{
			// ### NEW FILE ###
			$this->logFileChange($filedata);

			// Add a new file record
			unset($filedata->sourcePath);
			$db->insertObject('#__admintools_filescache', $filedata);
			unset($filedata);
		}

		return;
	}

	/**
	 * Neutralize the Joomla! SQL query log.
	 *
	 * We are making lots of queries to the database. If the log is not neutralized it may end up consuming all of the
	 * PHP memory, causing the scanner to fail miserably.
	 *
	 * On newer Joomla! versions we kill the Joomla query monitor completely. This happens once and is super efficient.
	 *
	 * On older Joomla! versions the database driver has a protected $log property that holds an array of logged
	 * queries. We will only empty it every 100 queries because using Reflection is very time-consuming and we don't
	 * want to destroy the performance of our code.
	 *
	 * @param   JDatabaseDriver  $db
	 *
	 * @throws  ReflectionException
	 */
	protected function neutralizeJoomlaQueryLog(DatabaseDriver $db)
	{
		// Sorry, I can't disable SQL query logging. Turn off Joomla's debug mode.
		if (!class_exists('ReflectionClass'))
		{
			return;
		}

		// Joomla 4 onwards -- using a query monitor
		if (method_exists($db, 'getMonitor'))
		{
			$monitor = $db->getMonitor();

			if (is_null($monitor))
			{
				return;
			}

			$mirror = new ReflectionClass($db);

			if (!$mirror->hasProperty('monitor'))
			{
				return;
			}

			$property = $mirror->getProperty('monitor');
			$property->setAccessible(true);
			$property->setValue($db, null);

			return;
		}
	}

	/**
	 * Adds a log entry to the #__admintools_scanalerts table, marking a modified, added or suspicious file.
	 *
	 * @param   stdClass       $newFileRecord   The record of the current version of the file
	 * @param   stdClass|null  $oldFileRecord   The record of the old version of the file (or null if it's an added
	 *                                          file)
	 *
	 * @return  void
	 */
	private function logFileChange(&$newFileRecord, &$oldFileRecord = null)
	{
		// Initialise the new alert record
		$alertRecord = [
			'path'         => $newFileRecord->path,
			'scan_id'      => $this->session->get('scanID'),
			'diff'         => '',
			'threat_score' => 0,
			'acknowledged' => 0,
		];

		// Placeholders in case the file is too large
		$newText     = '';
		$threatScore = 5000;
		$diffText    = "###File too large; if this is a log file please exclude it from the scan###\n";

		// Produce the diff if there is an old file
		if (!is_null($oldFileRecord))
		{
			if ($this->generateDiff)
			{
				// Modified file, generate diff
				$newText  = gzinflate($newFileRecord->data);
				$newText  = str_replace("\r\n", "\n", $newText);
				$newText  = str_replace("\r", "\n", $newText);
				$newLines = explode("\n", $newText);
				unset($newText);

				$oldText  = gzinflate($oldFileRecord->data);
				$oldText  = str_replace("\r\n", "\n", $oldText);
				$oldText  = str_replace("\r", "\n", $oldText);
				$oldLines = explode("\n", $oldText);
				unset($oldText);

				$diffObject          = new Diff($oldLines, $newLines);
				$renderer            = new RendererTextUnified();
				$alertRecord['diff'] = $diffObject->render($renderer);

				unset($renderer);
				unset($diffObject);
				unset($newLines);
				unset($oldLines);

				$alertRecord['threat_score'] = $this->calculateThreatScore($alertRecord['diff']);
			}
			else
			{
				// Read file contents (and calculate the score) only if the file is within the threshold
				if ($newFileRecord->filesize < $this->oversizeFileThreshold)
				{
					$newText     = @file_get_contents($newFileRecord->sourcePath);
					$threatScore = $this->calculateThreatScore($newText);
					$diffText    = "###MODIFIED FILE###\n";
				}

				// Modified file, do not generate diff
				$alertRecord['diff']         = $diffText;
				$alertRecord['threat_score'] = $threatScore;
				unset($newText);
			}
		}
		else
		{
			// Read file contents (and calculate the score) only if the file is within the threshold
			if ($newFileRecord->filesize < $this->oversizeFileThreshold)
			{
				$newText     = @file_get_contents($newFileRecord->sourcePath);
				$threatScore = $this->calculateThreatScore($newText);
			}

			// New file
			$alertRecord['threat_score'] = $threatScore;
			unset($newText);
		}

		// Do not create a record for non-threat files
		if ($this->ignoreNonThreats && !$alertRecord['threat_score'])
		{
			return;
		}

		$alertRecord = (object) $alertRecord;

		try
		{
			$this->db->insertObject('#__admintools_scanalerts', $alertRecord);
		}
		catch (Throwable $e)
		{
			// We might get "packet size too big" when there's a diff of big a file. If so, retry without the diff.
			if (strlen($alertRecord->diff ?? '') > 0)
			{
				$alertRecord->diff = "###File too large; if this is a log file please exclude it from the scan###\n";

				$this->db->insertObject('#__admintools_scanalerts', $alertRecord);

				return;
			}

			throw $e;
		}

		unset($alertRecord);
	}

	/**
	 * Performs a threat score assessment on the given file contents.
	 *
	 * @param   string  $text  The file contents to scan
	 *
	 * @return  int
	 */
	private function calculateThreatScore($text)
	{
		// These are the lists of signatures, initially empty
		static $suspiciousWords = null;
		static $knownHackSignatures = null;
		static $suspiciousRegEx = null;

		// ****
		// Note to self: The encoded configuration is built by the build/hacksignatures/create_lists.php
		// ****
		//
		// Build the lists of signatures from the encoded, compressed configuration.
		//
		// We have to go through this silly method because some eager malware scanners would consider the signatures
		// as an indication that this is a hacking script thus renaming or deleting the file, or even suspending the
		// hosting account! Ironically enough, thinking as a real hacker (zip and hex encode the part of the file
		// triggering the malware scanner) is enough to bypass this kind of protection.
		if (is_null($suspiciousWords) || is_null($knownHackSignatures) || is_null($suspiciousRegEx))
		{
			/** @var string $encodedConfig Defined in the included file */
			require_once __DIR__ . '/encodedconfig.php';

			$zipped = pack('H*', $encodedConfig);
			unset($encodedConfig);

			$json_encoded = gzinflate($zipped);
			unset($zipped);

			$new_list = json_decode($json_encoded, true);
			extract($new_list);

			unset($new_list);
		}

		$text = $this->stripPhpComments($text);

		$score = 0;
		$hits  = 0;
		$count = 0;

		foreach ($suspiciousWords as $word)
		{
			$count = substr_count($text, $word);

			if ($count)
			{
				$hits  += $count;
				$score += $count;
			}
		}

		foreach ($knownHackSignatures as $signature => $sigscore)
		{
			$count = substr_count($text, $signature);

			if ($count)
			{
				$hits  += $count;
				$score += $count * $sigscore;
			}
		}

		foreach ($suspiciousRegEx as $pattern => $value)
		{
			$count = preg_match_all($pattern, $text, $matches);

			if ($count)
			{
				$hits  += $count;
				$score += $value * $count;
			}
		}

		unset($count);

		if ($hits == 0)
		{
			unset($hits);

			return 0;
		}

		unset($hits);

		return (int) $score;
	}

	/**
	 * Strip PHP comments from a given text.
	 *
	 * This method removes both regular comments and doc comments from the provided text.
	 * It uses the `token_get_all` function to tokenize the PHP code and filter out the comments.
	 *
	 * @param   string  $text  The text to strip comments from.
	 *
	 * @return  string  The text without PHP comments.
	 * @since   7.5.5
	 */
	private function stripPhpComments(string $text): string
	{
		if (!function_exists('token_get_all') || !defined('T_COMMENT') || !defined('T_DOC_COMMENT'))
		{
			return $text;
		}

		$tokens = token_get_all($text);
		$newStr = '';

		foreach ($tokens as $token)
		{
			if (is_array($token)) {
				if (in_array($token[0], [
					T_COMMENT,
					T_DOC_COMMENT
				])) {
					continue;
				}

				$token = $token[1];
			}

			$newStr .= $token;
		}

		return $newStr;
	}

}
