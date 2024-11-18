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
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * Campaign View Class
 */
class ConvertFormsViewCampaign extends HtmlView
{
    /**
     * display method of Item view
     * @return void
     */
    public function display($tpl = null) 
    {
		// Access check.
		ConvertForms\Helper::authorise('convertforms.campaigns.manage', true);

        // get the Data
        $form = $this->get('Form');
        $item = $this->get('Item');

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Assign the Data
        $this->form = $form;
        $this->item = $item;
        $this->isnew = (!isset($_REQUEST["id"])) ? true : false;

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() 
    {
        $input = Factory::getApplication()->input;
        $input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);

        ToolbarHelper::title($isNew ? Text::_('COM_CONVERTFORMS_NEW_CAMPAIGN') : Text::_('COM_CONVERTFORMS_EDIT_CAMPAIGN') . ": " . $this->item->name . " - ". $this->item->id);

        if (defined('nrJ4'))
        {
            $toolbar = Toolbar::getInstance();

            $saveGroup = $toolbar->dropdownButton('save-group');
            
			$saveGroup->configure(
				function (Toolbar $childBar)
				{
                    $childBar->apply('campaign.apply');
                    $childBar->save('campaign.save');
                    $childBar->save2new('campaign.save2new');
					$childBar->save2copy('campaign.save2copy');
				}
            );
            
			$toolbar->cancel('campaign.cancel', 'JTOOLBAR_CLOSE');

            return;
        }

        ToolbarHelper::apply('campaign.apply');
        ToolbarHelper::save('campaign.save');
        ToolbarHelper::save2new('campaign.save2new');
        ToolbarHelper::cancel('campaign.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}