<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');
 
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Items controller class.
 */
class GSDControllerItems extends AdminController
{
	protected $text_prefix = 'GSD';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Joomla\CMS\MVC\Model\BaseDatabaseModel  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Item', $prefix = 'GSDModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 *  Copy items specified by array cid and set Redirection to the list of items
	 *
	 *  @return  void
	 */
	public function duplicate()
	{
		$app = Factory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');

		// Get the model.
		$model = $this->getModel('Item');

  		foreach ($ids as $id)
        {
            $model->copy($id);
        }

		$app->enqueueMessage(Text::sprintf('GSD_CAMPAIGN_N_ITEMS_COPIED', count($ids)));
        $app->redirect('index.php?option=com_gsd&view=items');
	}
}