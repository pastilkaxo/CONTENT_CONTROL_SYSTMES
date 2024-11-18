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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class ConvertformsControllerForms extends AdminController
{
	protected $text_prefix = 'COM_CONVERTFORMS_FORM';

    /**
     * Proxy for getModel.
     * @since       2.5
     */
    public function getModel($name = 'form', $prefix = 'ConvertFormsModel', $config = array('ignore_request' => true)) 
    {
        return parent::getModel($name, $prefix, $config);
    }

	/**
	 * Import Method
	 * Set layout to import
	 */
	public function import()
	{
		$app = Factory::getApplication();

		$file = $app->input->files->get("file");

		if (!empty($file))
		{
			if (isset($file['name']))
			{
				// Get the model.
				$model      = $this->getModel('Forms');
				$model_item = $this->getModel('Form');
				$model->import($model_item);
			}
			else
			{
				$app->enqueueMessage(Text::_('NR_PLEASE_CHOOSE_A_VALID_FILE'), 'error');
				$app->redirect('index.php?option=com_convertforms&view=forms&layout=import');
			}
		}
		else
		{
			$app->redirect('index.php?option=com_convertforms&view=forms&layout=import');
		}
	}

	/**
	 * Export Method
	 * Export the selected items specified by id
	 */
	public function export()
	{
		$ids = Factory::getApplication()->input->get('cid', array(), 'array');

		// Get the model.
		$model = $this->getModel('Forms');
		$model->export($ids);
	}

	/**
	 * Copy Method
	 * Copy all items specified by array cid
	 * and set Redirection to the list of items
	 */
	public function duplicate()
	{
		$ids = Factory::getApplication()->input->get('cid', array(), 'array');

		// Get the model.
		$model = $this->getModel('Form');

  		foreach ($ids as $id)
        {
            $model->copy($id);
        }

		Factory::getApplication(Text::sprintf('COM_CONVERTFORMS_FORM_N_ITEMS_COPIED', count($ids)));
		Factory::getApplication()->redirect('index.php?option=com_convertforms&view=forms');
	}
}