<?php

/**
 * @package         Convert Forms
 * @version         4.4.6 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Content categories view.
 *
 * @since  1.5
 */
class ConvertFormsViewSubmission extends HtmlView
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		if (!$this->submission = $this->get('Item'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_CONVERTFORMS_SUBMISSION_INVALID'), 'error');
			return;
		}

		$this->_prepareDocument();

		$this->params = Factory::getApplication()->getParams();

		// Layout checks
		if ($this->params->get('layout_type', 'file') == 'custom')
		{
			$layout = $this->params->get('layout_details');
			$layout = \Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $layout);

			if (!empty($layout))
			{
				echo ConvertForms\Submission::replaceSmartTags($this->submission, $layout);
				return;
			}
		}

		$this->menu = $this->get('Menu');
		$this->submissions_link = Route::_('index.php?option=com_convertforms&view=submissions&Itemid=' . $this->menu->id);

		// Display the view
		$this->setLayout($this->params->get('submissions_layout'));
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$doc = Factory::getDocument();
		$app = Factory::getApplication();
		$activeMenuItem = $app->getMenu()->getActive();
		$params = $activeMenuItem->getParams();

		if ($robots_value = $params->get('robots'))
		{
			$robots = $doc->getMetaData('robots');
			$robots = empty($robots) ? $robots_value : $robots . ', ' . $robots_value;
	
			$doc->setMetaData('robots', $robots);
		}

		if ($params->get('menu-meta_keywords'))
		{
			$doc->setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('menu-meta_description'))
		{
			$doc->setDescription($params->get('menu-meta_description'));
		}
	}
}