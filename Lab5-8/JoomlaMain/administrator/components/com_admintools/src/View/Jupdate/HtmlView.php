<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Jupdate;

defined('_JEXEC') || die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	use ViewTaskBasedEventsTrait;

	public function onBeforeMain()
	{
		ToolbarHelper::title(Text::_('COM_ADMINTOOLS_TITLE_JUPDATE'), 'admintools');
		ToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_admintools');
		ToolbarHelper::help(null, false, 'https://www.akeeba.com/documentation/admin-tools-joomla/reset-joomla-update.html');
	}
}