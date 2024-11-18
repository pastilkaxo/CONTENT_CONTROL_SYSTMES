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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

class ConvertFormsViewEditorbutton extends HtmlView
{
	/**
	 * Items view display method
	 * @return void
	 */
	public function display($tpl = null)
	{

		// Load plugin language file
		NRFramework\Functions::loadLanguage("plg_editors-xtd_convertforms");

		// Get editor name
		$eName = Factory::getApplication()->input->getCmd('e_name');

		// Get form fields
		$xml  = JPATH_PLUGINS . "/editors-xtd/convertforms/form.xml";
		$form = new Form("com_convertforms.button", array('control' => 'jform'));
		$form->loadFile($xml, false);

		// Template properties
		$this->eName = preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $eName);
		$this->form  = $form;

		parent::display($tpl);
		return;

	}
}