<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Scanner\Logger;

defined('_JEXEC') || die;

/**
 * Log levels
 */
abstract class LogLevel
{
	public const ERROR = 1;

	public const WARNING = 2;

	public const INFO = 3;

	public const DEBUG = 4;
}
