<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Emailtemplates;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	public function display($tpl = null)
	{
		ToolbarHelper::title(sprintf(Text::_('COM_ADMINTOOLS_TITLE_EMAILTEMPLATES')), 'icon-admintools');
		ToolbarHelper::back('JTOOLBAR_BACK', Route::_('index.php?option=com_admintools&view=Webapplicationfirewall', false));

		ToolbarHelper::help(null, false, 'https://www.akeeba.com/documentation/admin-tools-joomla/waf-emailtemplates.html');

		parent::display($tpl);
	}

}