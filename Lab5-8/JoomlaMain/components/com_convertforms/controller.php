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

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class ConvertFormsController extends BaseController
{
    /**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link InputFilter::clean()}.
	 *
	 * @return  Controller  This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
        $viewName = $this->input->getCmd('view');
        
        // Access front-end submissions only through a predefined Convert Forms Menu Item.
        if (in_array($viewName, ['submissions', 'submission']))
        {
            $app  = Factory::getApplication();
            $menu = $app->getMenu()->getActive();
            
            if (!$menu || !$menu->id || $menu->component != 'com_convertforms')
            {
                $app->enqueueMessage(Text::_('COM_CONVERTFORMS_NOT_AUTHORIZED'), 'error');
                return;
            }

            $model = $this->getModel($viewName);
            if (!$model->authorize())
            {
                $app->enqueueMessage(Text::_('COM_CONVERTFORMS_NOT_AUTHORIZED'), 'error');
                return; 
            }
        }

        parent::display($cachable, $urlparams);
        
		return $this;
    }
}