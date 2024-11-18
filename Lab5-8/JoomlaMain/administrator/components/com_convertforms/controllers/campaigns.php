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
 
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Campaigns list controller class.
 */
class ConvertFormsControllerCampaigns extends AdminController
{
	protected $text_prefix = 'COM_CONVERTFORMS_CAMPAIGN';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  BaseDatabaseModel  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Campaign', $prefix = 'ConvertFormsModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 *  Copy items specified by array cid and set Redirection to the list of items
	 *
	 *  @return  void
	 */
	function duplicate()
	{
		$ids = Factory::getApplication()->input->get('cid', array(), 'array');

		// Get the model.
		$model = $this->getModel('Campaign');

  		foreach ($ids as $id)
        {
            $model->copy($id);
		}
		
		Factory::getApplication(Text::sprintf('COM_CONVERTFORMS_CAMPAIGN_N_ITEMS_COPIED', count($ids)));
		Factory::getApplication()->redirect('index.php?option=com_convertforms&view=campaigns');
	}

	/**
	 *  Export campaign submissions specified by campaign ids
	 *
	 *  @return  void
	 */
	function export()
	{
		$ids = Factory::getApplication()->input->get('cid', null, 'INT');

		// Get the Conversions model
		$model = $this->getModel('Conversions');
		$model->export(null, $ids);
	}
}