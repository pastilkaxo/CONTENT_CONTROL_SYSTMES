<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Plugin\System\AdminTools\Utility;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Helper\Storage;
use Akeeba\Component\AdminTools\Administrator\Helper\TemplateEmails;
use DateTimeZone;
use Exception;
use Joomla\Application\ApplicationInterface;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;
use Throwable;

class BlockedRequestHandler implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	/**
	 * Plugin parameters
	 *
	 * @var   Registry
	 * @since 7.0.0
	 */
	protected $pluginParams = null;

	/**
	 * WAF parameters
	 *
	 * @var   Storage
	 * @since 7.0.0
	 */
	protected $wafParams = null;

	/**
	 * Component parameters
	 *
	 * @var   Registry
	 * @since 7.0.0
	 */
	protected $cParams = null;

	private ApplicationInterface $application;

	public function __construct(Registry $pluginParams, Storage $wafParams, Registry $cParams)
	{
		$this->pluginParams = $pluginParams;
		$this->wafParams    = $wafParams;
		$this->cParams      = $cParams;
	}

	/**
	 * @param   string     $templateKey  The template key to send, e.g. 'com_admintools.blockedrequest'
	 * @param   User|null  $user         The user to send the email to. NULL for the currently logged in user.
	 * @param   array      $data         Associative array for tag/variable replacement in the email template.
	 *
	 * @return bool
	 */
	public function sendEmail(string $templateKey, ?User $user = null, array $data = []): bool
	{
		// Do not send emails in the Core version
		if (!defined('ADMINTOOLS_PRO') || !ADMINTOOLS_PRO)
		{
			return true;
		}

		$app  = $this->getApplication();
		$user = $user ?: $app->getIdentity();

		$data = $this->getEmailVariables(
			array_merge(
				[
					'USERNAME' => $user->username,
					'FULLNAME' => $user->name,
				], $data
			)
		);

		try
		{
			TemplateEmails::updateTemplate($templateKey);

			return TemplateEmails::sendMail($templateKey, $data, $user);
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Legacy shim to logRequest().
	 *
	 * @param   string  $reason
	 * @param   string  $extraLogInformation
	 * @param   string  $extraLogTableInformation
	 *
	 * @deprecated  8.0  Use the logRequest method; it works identically, it's just better named.
	 *
	 * @return  bool
	 * @see         self::logRequest()
	 */
	public function logWithoutBlocking(
		string $reason, string $extraLogInformation = '', string $extraLogTableInformation = ''
	): bool
	{
		return $this->logRequest($reason, $extraLogInformation, $extraLogTableInformation);
	}

	/**
	 * Logs a possibly malicious request and processes the IP auto-ban.
	 *
	 * This method DOES NOT block (HTTP 403) the request. It only logs it in the database and file.
	 *
	 * This is used when the request needs to be redirected (e.g. admin secret URL parameter), or when we are only
	 * logging potential problems (e.g. failed login).
	 *
	 * @param   string       $reason                    Block reason code
	 * @param   string|null  $extraLogInformation       Extra information to be written to the text log file
	 * @param   string|null  $extraLogTableInformation  Extra information to be written to the extradata field of the log
	 *
	 * @return  bool  False if the current IP address is exempt from WAF blocking.
	 */
	public function logRequest(
		string $reason, ?string $extraLogInformation = '', ?string $extraLogTableInformation = ''
	): bool
	{
		if ($this->isExemptIP())
		{
			return false;
		}

		$extraLogInformation      ??= '';
		$extraLogTableInformation ??= '';

		// Collect the information we need to log
		$reasonAsText = $this->blockingReasonToHumanReadableText($reason, $extraLogTableInformation);
		$tokens       = $this->getEmailVariables(['REASON' => $reasonAsText]);

		// Should I log the request, based on its reason?
		if ($this->shouldLogThisReason($reason))
		{
			// Log to file
			$this->logRequestToFile($reason, $extraLogInformation, $reasonAsText, $tokens);

			// Log to the database table
			$this->logRequestToDatabase($reason, $extraLogTableInformation, $tokens);

			// Process automatic temporary and permanent IP blocking for repeat offenders
			$this->processIPAutoBan($reason);
		}

		// Send the email about the blocked request, if necessary
		if ($this->shouldEmailAboutBlockedRequest($reason))
		{
			$this->sendBlockedRequestEmail($reason, $tokens);
		}

		return true;
	}

	/**
	 * Logs the request, processes the IP auto-ban, and blocks the request.
	 *
	 * This always ends up with a blocked request (HTTP 403).
	 *
	 * This is the full request blocking experience, triggered when we need to immediately abort the request in
	 * progress
	 * to prevent a security issue from affecting the application.
	 *
	 * @param   string       $reason                    Block reason code
	 * @param   string|null  $message                   The message to be shown to the user
	 * @param   string|null  $extraLogInformation       Extra information to be written to the text log file
	 * @param   string|null  $extraLogTableInformation  Extra information to be written to the extradata field of the
	 *                                                  log table (useful for JSON format)
	 *
	 * @return  void  This function never returns BUT may throw an exception, hence not `never-return`.
	 * @throws  Exception
	 */
	public function blockRequest(
		string $reason = 'other', ?string $message = '', ?string $extraLogInformation = '',
		?string $extraLogTableInformation = ''
	): void
	{
		if (!$this->logRequest($reason, $extraLogInformation ?? '', $extraLogTableInformation ?? ''))
		{
			// This was an exempt IP address. Do not block!
			return;
		}

		$message = $message
			?: trim($this->wafParams->getValue('custom403msg', ''))
				?: 'PLG_ADMINTOOLS_MSG_BLOCKED';

		// Merge the default translation with the current translation
		/** @var CMSApplication $app */
		$app = $this->getApplication();

		if ((Text::_('PLG_ADMINTOOLS_MSG_BLOCKED') == 'PLG_ADMINTOOLS_MSG_BLOCKED')
		    && ($message == 'PLG_ADMINTOOLS_MSG_BLOCKED'))
		{
			$message = "Access Denied";
		}
		else
		{
			$message = Text::_($message);
		}

		$message = RescueUrl::processRescueInfoInMessage($message);

		// Show the 403 message
		$use403View = $this->wafParams->getValue('use403view', 0);
		$isFrontend = $app->isClient('site');
		$isApi      = $app->isClient('api');

		if ($isApi)
		{
			@ob_end_clean();

			header('HTTP/1.1 403 Access Denied');

			echo $message;

			$app->close();
		}

		if (!$use403View || !$isFrontend)
		{
			// Using Joomla!'s error page
			$app->input->set('template', null);
			$app->input->set('layout', null);

			throw new Exception($message, 403);
		}

		// Using a view
		$session = $app->getSession();

		if (!$session->get('com_admintools.block', false))
		{
			// This is inside an if-block so that we don't end up in an infinite redirection loop
			$session->set('com_admintools.block', true);
			$session->set('com_admintools.message', $message);

			if ($app->isClient('site') || $app->isClient('administrator'))
			{
				$session->close();
			}

			$app->redirect(Uri::base(), 307);
		}
	}

	/**
	 * Checks if the Rescue URL is being accessed.
	 *
	 * This only applies when IP autoban is enabled and this is an administrator access.
	 *
	 * @return  void
	 */
	public function checkRescueURL(): void
	{
		$autoban = $this->wafParams->getValue('tsrenable', 0);

		if (!$autoban)
		{
			return;
		}

		// If IP auto-ban is enabled we need to check for a Rescue URL
		RescueUrl::processRescueURL($this);
	}

	public function setApplication(ApplicationInterface $application): void
	{
		$this->application = $application;
	}

	private function getApplication(): ApplicationInterface
	{
		return $this->application;
	}

	/**
	 * Get the variables we can use in emails as an associative list (variable => value).
	 *
	 * @param   array  $customVariables  An array of custom variables to add to the return.
	 *
	 * @return  array
	 */
	private function getEmailVariables($customVariables = [])
	{
		$app      = $this->getApplication();
		$siteName = $app->get('sitename');
		$cParams  = ComponentHelper::getParams('com_admintools');
		$emailTz  = $cParams->get('email_timezone', 'AKEEBA/DEFAULT');
		$app      = $this->getApplication();
		$userTz   = $app->getIdentity()->get('timezone', $app->get('offset', 'GMT'));

		try
		{
			$timezone = new DateTimeZone($userTz);
		}
		catch (Exception $e)
		{
			$timezone = null;
		}

		if (!empty($emailTz) && ($emailTz != 'AKEEBA/DEFAULT'))
		{
			try
			{
				$forcedTimezone = new DateTimeZone($emailTz);
				$timezone       = $forcedTimezone;
			}
			catch (Exception $e)
			{
				// Just in case someone puts an invalid timezone in there (you can never be too paranoid).
			}
		}

		$date = clone Factory::getDate();
		$date->setTimezone($timezone ?: new DateTimeZone('GMT'));

		$ip = $this->getVisitorIPAddress() ?: '0.0.0.0';

		if ((strpos($ip, '::') === 0) && (strstr($ip, '.') !== false))
		{
			$ip = substr($ip, strrpos($ip, ':') + 1);
		}

		$currentUser = $app->getIdentity();

		if ($currentUser->guest)
		{
			$currentUser = 'Guest';
		}
		else
		{
			$currentUser = sprintf(
				"%s (%s <%s>)",
				$currentUser->username,
				$currentUser->name,
				$currentUser->email
			);
		}

		$ipLookupURL = 'https://' . $this->wafParams->getValue('iplookup', 'ip-lookup.net/index.php?ip={ip}');
		$ipLookupURL = str_replace('{ip}', $ip, $ipLookupURL);
		$uri         = Uri::getInstance();
		$url         = $uri->toString(['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment']);

		return array_merge(
			[
				'USER'     => $currentUser,
				'SITENAME' => $siteName,
				'DATE'     => ($date)->format('Y-m-d H:i:s T', true),
				'IP'       => $ip,
				'URL'      => $url,
				'LOOKUP'   => $ipLookupURL,
				'UA'       => $_SERVER['HTTP_USER_AGENT'],
			], $customVariables
		);
	}

	/**
	 * Is this IP exempt from being blocked by our Web Application Firewall?
	 *
	 * This will return true in the following cases:
	 * - We cannot get the user's IP address.
	 * - The IP is in the Administrator Exclusive Allow IP List.
	 * - The IP is in the Site IP Allow List.
	 * - The IP is in the "Do not block These IPs" list.
	 * - The IP belongs to domain in the Never Block These Domains list
	 *
	 * @return  bool  True if the IP address is exempt from WAF blocking
	 */
	private function isExemptIP()
	{
		$ip = $this->getVisitorIPAddress();

		return empty($ip)
		       || $this->isIPInAdminExclusiveAllowList()
		       || $this->isIPInSiteAllowList()
		       || $this->isNeverBlockTheseIPs()
		       || $this->isNeverBlockedDomain($ip);
	}

	/**
	 * Checks if an IP address should be automatically banned.
	 *
	 * This checks both for temporary and permanent IP blocking.
	 *
	 * @param   string  $reason  The reason of the ban
	 *
	 * @return  void
	 */
	private function processIPAutoBan($reason = 'other')
	{
		// The Core version does not support auto-banning IP addresses
		if (!defined('ADMINTOOLS_PRO') || !ADMINTOOLS_PRO)
		{
			return;
		}

		// Is the feature enabled
		if (!$this->wafParams->getValue('tsrenable', 0))
		{
			return;
		}

		// Get the IP
		$ip = $this->getVisitorIPAddress();

		// No point continuing if we can't get an address, right?
		if (empty($ip) || $ip === '0.0.0.0')
		{
			return;
		}

		// Get the database object
		try
		{
			/** @var DatabaseDriver $db */
			$db = $this->getDatabase();
		}
		catch (Throwable $e)
		{
			// Failure to get the database prevents anything else from working correctly.
			return;
		}

		$this->lockTables(['#__admintools_ipautoban', '#__admintools_ipautobanhistory', '#__admintools_ipblock', '#__admintools_log']);

		try
		{
			$until            = null;
			$isRepeatOffender = $this->isRepeatOffender($db, $ip, $reason, $until);
		}
		catch (Exception $e)
		{
			$isRepeatOffender = false;
		}
		finally
		{
			$this->unlockTables();
		}

		if (!$isRepeatOffender)
		{
			return;
		}

		// Should I send an optional email?
		if ($this->wafParams->getValue('emailafteripautoban', ''))
		{
			$this->sendIPAutoBanEmail($reason, $until);
		}
	}

	/**
	 * Get the visitor IP address.
	 *
	 * Return null if we cannot get an IP address or if we get 0.0.0.0 (broken IP forwarding).
	 *
	 * @return  null|string
	 */
	private function getVisitorIPAddress(): ?string
	{
		// Get our IP address
		try
		{
			$ip = Filter::getIp();
		}
		catch (Throwable $e)
		{
			return null;
		}

		if ((strpos($ip, '::') === 0) && (strstr($ip, '.') !== false))
		{
			$ip = substr($ip, strrpos($ip, ':') + 1);
		}

		// No point continuing if we can't get an address, right?
		if (empty($ip) || ($ip == '0.0.0.0'))
		{
			return null;
		}

		return $ip;
	}

	/**
	 * Is the IP address in the "Never block these IPs" (safe IPs) list?
	 *
	 * @return  bool
	 */
	private function isNeverBlockTheseIPs()
	{
		$safeIPs = $this->wafParams->getValue('neverblockips', '') ?: [];

		if (is_string($safeIPs))
		{
			$safeIPs = array_map('trim', explode(',', $safeIPs));
		}

		$safeIPs = array_map(
			function ($x) {
				return is_array($x) ? $x[0] : $x;
			}, is_array($safeIPs) ? $safeIPs : []
		);

		return !empty($safeIPs) && Filter::IPinList($safeIPs) ? true : false;
	}

	/**
	 * Is the IP address in the Administrator Exclusive Allow IP?
	 *
	 * @return  bool
	 */
	private function isIPInAdminExclusiveAllowList(): bool
	{
		if ($this->wafParams->getValue('ipwl', 0) != 1)
		{
			return false;
		}

		$ipTable = Cache::getCache('adminiplist');

		if (!empty($ipTable) && Filter::IPinList($ipTable))
		{
			return true;
		}

		return false;
	}

	/**
	 * Is the IP address in the Site IP Allow List?
	 *
	 * @return  bool
	 *
	 * @since   7.2.4
	 */
	private function isIPInSiteAllowList(): bool
	{
		$ipTable = Cache::getCache('ipallow');

		if (!empty($ipTable) && Filter::IPinList($ipTable))
		{
			return true;
		}

		return false;
	}

	/**
	 * Does the IP address resolve to a domain in the the Never Block These Domains list?
	 *
	 * @param   string  $ip
	 *
	 * @return  bool
	 */
	private function isNeverBlockedDomain($ip)
	{
		static $whitelistDomains = null;

		if (is_null($whitelistDomains))
		{
			$whitelistDomains = $this->wafParams->getValue('whitelist_domains', []);

			if (is_string($whitelistDomains))
			{
				$whitelistDomains = array_map('trim', explode(',', $whitelistDomains));
			}

			$whitelistDomains = array_map(
				function ($x) {
					return is_array($x) ? $x[0] : $x;
				}, is_array($whitelistDomains) ? $whitelistDomains : []
			);
		}

		if (!empty($whitelistDomains))
		{
			$remote_domain = @gethostbyaddr($ip);

			if (empty($remote_domain))
			{
				return false;
			}

			foreach ($whitelistDomains as $domain)
			{
				$domain = trim($domain);

				if (strrpos($remote_domain, $domain) === strlen($remote_domain) - strlen($domain))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get the blocking reason in a human readable format
	 *
	 * @param   string  $reason
	 * @param   string  $extraLogTableInformation
	 *
	 * @return  string
	 */
	private function blockingReasonToHumanReadableText($reason, $extraLogTableInformation)
	{
		// Load the component's administrator translation files
		$jlang = $this->getApplication()->getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

		// Get the reason in human readable format
		$txtReason = Text::_('COM_ADMINTOOLS_LOG_LBL_REASON_' . strtoupper($reason));

		if (empty($extraLogTableInformation))
		{
			return $txtReason;
		}

		// Get extra information
		[$logReason,] = explode('|', $extraLogTableInformation);

		return $txtReason . " ($logReason)";
	}

	/**
	 * Should I log a potentially malicious request with the specified reason?
	 *
	 * @param   string  $reason  The reason to check.
	 *
	 * @return  bool
	 */
	private function shouldLogThisReason(string $reason): bool
	{
		// If logging is disabled entirely, we should not log anything.
		if (!$this->wafParams->getValue('logbreaches', 0))
		{
			return false;
		}

		// Get the no logging reasons
		$reasonsNoLog = $this->wafParams->getValue('reasons_nolog', []);

		// Handle legacy data
		if (is_string($reasonsNoLog))
		{
			$reasonsNoLog = explode(',', $reasonsNoLog);
		}

		$reasonsNoLog = is_array($reasonsNoLog) ? $reasonsNoLog : [];

		// We log as long as it isn't a no-log reason.
		return !in_array($reason, $reasonsNoLog);
	}

	/**
	 * Log a security exception to our log file
	 *
	 * @param   string       $reason
	 * @param   string|null  $extraLogInformation
	 * @param   string|null  $txtReason
	 * @param   array        $tokens
	 *
	 * @return  void
	 */
	private function logRequestToFile(string $reason, ?string $extraLogInformation, ?string $txtReason, array $tokens)
	{
		// Write to the log file only if we're told to
		if (!$this->wafParams->getValue('logfile', 0))
		{
			return;
		}

		// Get the log filename
		$logpath = $this->getApplication()->get('log_path');
		$fname   = $logpath . DIRECTORY_SEPARATOR . 'admintools_blocked.php';

		// -- Check the file size. If it's over 1Mb, archive and start a new log.
		if (@file_exists($fname))
		{
			$fsize = filesize($fname);

			if ($fsize > 1048756)
			{
				$altFile = substr($fname, 0, -4) . '.1.php';

				if (@file_exists($altFile))
				{
					unlink($altFile);
				}

				@copy($fname, $altFile);
				@unlink($fname);
			}
		}

		// If the main log file does not exist yet create a new one.
		if (!file_exists($fname))
		{
			$content = <<< END
php
/**
 * =====================================================================================================================
 * Admin Tools debug log file
 * =====================================================================================================================
 *
 * This file contains a dump of the requests which were blocked by Admin Tools. By definition, this file does contain
 * a lot of "hacking signatures" since this is what the Admin Tools component is designed to stop and this is the file
 * logging all these hacking attempts.
 *
 * You can disable the creation of this file by going to Components, Admin Tools, Web Application Firewall, Configure
 * WAF and setting the "Keep a debug log file" option to NO. This is the recommended setting. You should only set this
 * option to YES if you are troubleshooting an issue (Admin Tools is blocking access to your site).
 *
 * Some hosts will mistakenly report this file as suspicious or hacked. As a result they might issue an automated
 * warning and / or block access to your site. Should that happen please ask your host to look in this file and read
 * this header. This file is SAFE since the only executable statement is die() below which prevents the file from being
 * executed at all. If your host does not understand that this file is safe or does not know how to add an exception in
 * their automated scanner to exempt Joomla's log files (all files under this directory) from being flagged as hacked /
 * suspicious we strongly recommend going to a different host that understands how PHP works. It will be safer for you
 * as well. 
 */
 
die();
END;
			$content = "?$content?";
			$content .= ">\n\n";
			file_put_contents($fname, '<' . $content);
		}

		// -- Log the exception
		$fp = @fopen($fname, 'a');

		if ($fp === false)
		{
			return;
		}

		fwrite($fp, str_repeat('-', 79) . PHP_EOL);
		fwrite($fp, "Blocking reason: " . $reason . PHP_EOL . str_repeat('-', 79) . PHP_EOL);
		fwrite($fp, "Reason     : " . $txtReason . PHP_EOL);
		fwrite($fp, 'Timestamp  : ' . gmdate('Y-m-d H:i:s') . " GMT" . PHP_EOL);
		fwrite($fp, 'Local time : ' . $tokens['[DATE]'] . " " . PHP_EOL);
		fwrite($fp, 'URL        : ' . $tokens['[URL]'] . PHP_EOL);
		fwrite($fp, 'User       : ' . $tokens['[USER]'] . PHP_EOL);
		fwrite($fp, 'IP         : ' . $tokens['[IP]'] . PHP_EOL);
		fwrite($fp, 'UA         : ' . $tokens['[UA]'] . PHP_EOL);

		if (!empty($extraLogInformation))
		{
			fwrite($fp, $extraLogInformation . PHP_EOL);
		}

		fwrite($fp, PHP_EOL . PHP_EOL);
		fclose($fp);
	}

	/**
	 * Log a security exception to the database table
	 *
	 * @param   string  $reason
	 * @param   string  $extraLogInformation
	 * @param   array   $tokens
	 */
	private function logRequestToDatabase($reason, $extraLogTableInformation, $tokens)
	{
		try
		{
			/** @var DatabaseDriver $db */
			$db = $this->getDatabase();
		}
		catch (Throwable $e)
		{
			// Failure to get the database prevents anything else from working correctly.
			return;
		}

		$this->lockTables(['#__admintools_ipautoban', '#__admintools_ipautobanhistory', '#__admintools_ipblock', '#__admintools_log']);

		try
		{
			$date = clone Factory::getDate();
			$url  = $tokens['URL'];

			if (strlen($url) > 10240)
			{
				$url = substr($url, 0, 10240);
			}

			$logEntry = (object) [
				'logdate'   => $date->toSql(),
				'ip'        => $tokens['IP'],
				'url'       => $url,
				'reason'    => $reason,
				'extradata' => $extraLogTableInformation,
			];

			$db->insertObject('#__admintools_log', $logEntry);
		}
		catch (Exception $e)
		{
			/**
			 * During high intensity attacks we might get a deadlock in the database, which causes an exception to be
			 * raised. We just need to ignore it, and unlock the tables.
			 */
		}
		finally
		{
			$this->unlockTables();
		}
	}

	private function shouldEmailAboutBlockedRequest(string $reason): bool
	{
		// Cannot send email if not email address is entered
		$emailOnException = $this->wafParams->getValue('emailbreaches', '');

		if (empty($emailOnException))
		{
			return false;
		}

		// Cannot email if it's a no-email reason
		$reasonsNoEmail = $this->wafParams->getValue('reasons_noemail', '') ?: [];
		$reasonsNoEmail = is_string($reasonsNoEmail) ? explode(',', $reasonsNoEmail) : $reasonsNoEmail;

		return !in_array($reason, $reasonsNoEmail);
	}

	/**
	 * Sends an email about a blocked request
	 *
	 * @param   string  $reason
	 * @param   array   $tokens
	 *
	 * @return  void
	 */
	private function sendBlockedRequestEmail($reason, $tokens)
	{
		$emailOnException = $this->wafParams->getValue('emailbreaches', '');

		// Send the email
		try
		{
			$recipients = explode(',', $emailOnException);
			$recipients = array_map('trim', $recipients);

			foreach ($recipients as $recipient)
			{
				if (empty($recipient))
				{
					continue;
				}

				$recipientUser           = new User();
				$recipientUser->username = $recipient;
				$recipientUser->name     = $recipient;
				$recipientUser->email    = $recipient;
				$data                    = array_merge(
					$tokens,
					RescueUrl::getRescueInformation($recipient),
					[
						'REASON' => $reason,
					]
				);

				if (!$this->isSendingAllowedByEmailThrottling())
				{
					continue;
				}

				$this->sendEmail(
					'com_admintools.blockedrequest',
					$recipientUser,
					$data
				);
			}
		}
		catch (Exception $e)
		{
		}
	}

	/**
	 * Is sending an email allowed by the email throttling feature?
	 *
	 * @return  bool
	 *
	 * @since   7.2.2
	 */
	private function isSendingAllowedByEmailThrottling(): bool
	{
		// TODO Needs table locking

		$cParams = ComponentHelper::getParams('com_admintools');

		//  If the throttling feature is disabled allow sending the email.
		if ($cParams->get('email_throttle', 1) != 1)
		{
			return true;
		}

		// Get the frequency limit options
		$maxAllowedEmails = $cParams->get('email_num', 5);
		$timePeriod       = $cParams->get('email_numfreq', 15);
		$timeUOM          = $cParams->get('email_freq', 'minutes');

		switch ($timeUOM)
		{
			case 'seconds':
				$earliestDate = Factory::getDate()->sub(new \DateInterval('PT' . $timePeriod . 'S'));
				break;

			case 'minutes':
				$earliestDate = Factory::getDate()->sub(new \DateInterval('PT' . $timePeriod . 'M'));
				break;

			case 'hours':
				$earliestDate = Factory::getDate()->sub(new \DateInterval('PT' . $timePeriod . 'H'));
				break;

			case 'days':
				$earliestDate = Factory::getDate()->sub(new \DateInterval('P' . $timePeriod . 'D'));
				break;

			case 'ever':
			default:
				$earliestDate = Factory::getDate('2000-01-01 00:00:00');
				break;
		}

		$reasonsNoLog = $this->wafParams->getValue('reasons_nolog', []) ?: [];
		$reasonsNoLog = is_array($reasonsNoLog)
			? $reasonsNoLog
			: array_map('trim', @explode(',', $reasonsNoLog));

		/** @var DatabaseDriver $db */
		$db      = $this->getDatabase();
		$logDate = $earliestDate->toSql();
		$sql     = (method_exists($db, 'createQuery') ? $db->createQuery() : $db->getQuery(true))
			->select('COUNT(*)')
			->from($db->qn('#__admintools_log'))
			->where($db->qn('logdate') . ' >= :logDate')
			->bind(':logDate', $logDate);

		// Apply the where clause only if we have excluded any reason from logging
		if (!empty($reasonsNoLog))
		{
			$sql->whereNotIn($db->qn('reason'), $reasonsNoLog, ParameterType::STRING);
		}

		$db->setQuery($sql);

		try
		{
			$numOffenses = $db->loadResult() ?: 0;
		}
		catch (Exception $e)
		{
			$numOffenses = 0;
		}

		return $numOffenses <= $maxAllowedEmails;
	}

	/**
	 * Is the IP address specified a repeat offender? Also processes IP blocking.
	 *
	 * If the IP address is a repeat offender it is temporarily blocked. If too many temporary blocks have been issued,
	 * a permanent block will be issued (if configured).
	 *
	 * @param   DatabaseDriver  $db
	 * @param   string          $ip
	 * @param   string          $reason
	 *
	 * @return  bool
	 */
	private function isRepeatOffender(
		DatabaseDriver $db, string $ip, string $reason, ?string &$until
	): bool
	{
		// Check for repeat offenses
		$strikes      = $this->wafParams->getValue('tsrstrikes', 3);
		$numfreq      = $this->wafParams->getValue('tsrnumfreq', 1);
		$frequency    = $this->wafParams->getValue('tsrfrequency', 'hour');
		$mindatestamp = 0;

		switch ($frequency)
		{
			case 'second':
				break;

			case 'minute':
				$numfreq *= 60;
				break;

			case 'hour':
				$numfreq *= 3600;
				break;

			case 'day':
				$numfreq *= 86400;
				break;

			case 'ever':
				$mindatestamp = 946706400; // January 1st, 2000
				break;
		}

		$jNow = clone Factory::getDate();

		if ($mindatestamp == 0)
		{
			$mindatestamp = $jNow->toUnix() - $numfreq;
		}

		$jMinDate = clone Factory::getDate($mindatestamp);
		$minDate  = $jMinDate->toSql();

		$sql = (method_exists($db, 'createQuery') ? $db->createQuery() : $db->getQuery(true))
			->select('COUNT(*)')
			->from($db->qn('#__admintools_log'))
			->where($db->qn('logdate') . ' >= ' . $db->q($minDate))
			->where($db->qn('ip') . ' = ' . $db->q($ip));
		$db->setQuery($sql);

		try
		{
			$numOffenses = $db->loadResult();
		}
		catch (Exception $e)
		{
			$numOffenses = 0;
		}

		if ($numOffenses < $strikes)
		{
			return false;
		}

		// Block the IP
		$myIP = @inet_pton($ip);

		if ($myIP === false)
		{
			return false;
		}

		$myIP = inet_ntop($myIP);

		$until     = $jNow->toUnix();
		$numfreq   = $this->wafParams->getValue('tsrbannum', 1);
		$frequency = $this->wafParams->getValue('tsrbanfrequency', 'hour');

		switch ($frequency)
		{
			case 'second':
				$until += $numfreq;
				break;

			case 'minute':
				$numfreq *= 60;
				$until   += $numfreq;
				break;

			case 'hour':
				$numfreq *= 3600;
				$until   += $numfreq;
				break;

			case 'day':
				$numfreq *= 86400;
				$until   += $numfreq;
				break;

			case 'ever':
				$until = 2145938400; // January 1st, 2038 (mind you, UNIX epoch runs out on January 19, 2038!)
				break;
		}

		$until = (clone Factory::getDate($until))->toSql();

		$record = (object) [
			'ip'     => $myIP,
			'reason' => $reason,
			'until'  => $until,
		];

		// If I'm here it means that we have to ban the user. Let's see if this is a simple autoban or
		// we have to issue a permaban as a result of several attacks
		if ($this->wafParams->getValue('permaban', 0))
		{
			// Ok I have to check the number of autoban
			$query = (method_exists($db, 'createQuery') ? $db->createQuery() : $db->getQuery(true))
				->select('COUNT(*)')
				->from($db->qn('#__admintools_ipautobanhistory'))
				->where($db->qn('ip') . ' = ' . $db->q($myIP));

			try
			{
				$bans = $db->setQuery($query)->loadResult();
			}
			catch (Exception $e)
			{
				$bans = 0;
			}

			$limit = (int) $this->wafParams->getValue('permabannum', 0);

			if ($limit && ($bans >= $limit))
			{
				$block = (object) [
					'ip'          => $myIP,
					'description' => 'IP automatically blocked after being banned automatically ' . $bans . ' times',
				];

				try
				{
					$db->insertObject('#__admintools_ipblock', $block);
					Cache::resetCache('ipblock');
				}
				catch (Exception $e)
				{
					// This should never happen, however let's prevent a white page if anything goes wrong
				}
			}
		}

		try
		{
			$db->insertObject('#__admintools_ipautoban', $record);
			Cache::resetCache('ipautoban');
		}
		catch (Exception $e)
		{
			// If the IP was already blocked and I have to block it again, I'll have to update the current record
			$db->updateObject('#__admintools_ipautoban', $record, 'ip');
			Cache::resetCache('ipautoban');
		}

		return true;
	}

	/**
	 * @param   string       $reason
	 * @param   string|null  $until
	 *
	 * @return void
	 */
	private function sendIPAutoBanEmail(string $reason, ?string $until): void
	{
		// Load the component's administrator translation files
		$jlang = $this->getApplication()->getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

		$substitutions = $this->getEmailVariables(
			[
				'REASON' => $reason,
				'UNTIL'  => $until,
			]
		);

		// Send the email
		try
		{
			$recipients = explode(',', $this->wafParams->getValue('emailafteripautoban', ''));
			$recipients = array_map('trim', $recipients);

			foreach ($recipients as $recipient)
			{
				if (empty($recipient))
				{
					continue;
				}

				$recipientUser           = new User();
				$recipientUser->username = $recipient;
				$recipientUser->name     = $recipient;
				$recipientUser->email    = $recipient;
				$data                    = array_merge(RescueUrl::getRescueInformation($recipient), $substitutions);

				$this->sendEmail('com_admintools.ipautoban', $recipientUser, $data);
			}
		}
		catch (Exception $e)
		{
			// Joomla! 3.5 and later throw an exception when crap happens instead of suppressing it and returning false
		}
	}

	private function lockTables(array $tables): void
	{
		try
		{
			$db = $this->getDatabase();
		}
		catch (Throwable $e)
		{
			return;
		}

		$serverType = $db->getServerType();

		if (!in_array($serverType, ['mysql', 'postgresql']))
		{
			return;
		}

		/**
		 * MySQL.
		 *
		 * We use `SET autocommit = 0;` before locking the tables to start an implicit transaction. Note that START
		 * TRANSACTION would not work in this case.
		 *
		 * @see https://dev.mysql.com/doc/refman/8.0/en/lock-tables.html
		 */
		if ($serverType === 'mysql')
		{
			try
			{
				$db->setQuery('SET autocommit = 0')->execute();
			}
			catch (Exception $e)
			{
				return;
			}

			$sql = 'LOCK TABLES ' . implode(
					',',
					array_map(
						function ($table) use ($db) {
							if (substr($table, 0, 1) !== '`')
							{
								$table = $db->quoteName($table);
							}
		
							return $table . ' WRITE';
						},
						$tables
					)
				);

			try
			{
				$db->setQuery($sql)->execute();
			}
			catch (Exception $e)
			{
				return;
			}

			return;
		}

		/**
		 * PostgreSQL.
		 *
		 * We start a transaction (internally, it calls BEGIN WORK), then lock each table in ACCESS EXCLUSIVE MODE.
		 *
		 * @see https://www.postgresql.org/docs/current/sql-lock.html
		 */
		$db->transactionStart();

		foreach ($tables as $table)
		{
			$db->lockTable($table);
		}
	}

	private function unlockTables(): void
	{
		try
		{
			$db = $this->getDatabase();
		}
		catch (Throwable $e)
		{
			return;
		}

		$serverType = $db->getServerType();

		if (!in_array($serverType, ['mysql', 'postgresql']))
		{
			return;
		}

		/**
		 * MySQL.
		 *
		 * We have to manually commit the transaction, then unlock the tables, and finally re-enable AUTOCOMMIT.
		 *
		 * @see https://dev.mysql.com/doc/refman/8.0/en/lock-tables.html
		 */
		if ($serverType === 'mysql')
		{
			try
			{
				$db->setQuery('COMMIT')->execute();
			}
			catch (Exception $e)
			{
				// Ignore. We must reach the SET autocommit.
			}

			try
			{
				$db->unlockTables();
			}
			catch (Exception $e)
			{
				// Ignore. We must reach the SET autocommit.
			}

			try
			{
				$db->setQuery('SET autocommit = 1')->execute();
			}
			catch (Exception $e)
			{
				return;
			}

			return;
		}

		/**
		 * PostgreSQL.
		 *
		 * There is no UNLOCK TABLES command; we just commit the transaction.
		 *
		 * @see https://www.postgresql.org/docs/current/sql-lock.html
		 */
		$db->unlockTables();
	}
}