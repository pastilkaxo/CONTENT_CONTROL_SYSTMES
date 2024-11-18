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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

JLoader::register('NRFormField', JPATH_PLUGINS . '/system/nrframework/helpers/field.php');

class JFormFieldFastEdit extends NRFormField
{
    /**
     *  Disable field label
     *
     *  @return  boolean
     */
    protected function getLabel()
    {
        return false;
    }

    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of options.
     */
    protected function getInput()
    {
        // Load framework language file
        NRFramework\Functions::loadLanguage();

        $thing = $this->get('thing', 0);
        $error = $this->get('error', 'GSD_SAVE_FIRST');
        $html  = '';

        if (!defined('nrJ4'))
        {
            HTMLHelper::stylesheet('com_gsd/joomla3.css', ['relative' => true, 'version' => 'auto']);
        }

        HTMLHelper::_('stylesheet', 'com_gsd/fastedit.css', array('version' => 'auto', 'relative' => true));

        // In order to be able to assosiate a snippet the item must be saved first.
        if (!$thing)
        {
            return $html . '<div class="alert alert-info gsdFastEdit">' . Text::_($error) . '</div>';
        }

        // Cool. The item is saved.
        $plugin = $this->get('plugin', null);
        $contentTypes = GSD\Helper::getContentTypes();

        $query = [
            'override_item' => true,
            'title' => $this->get('thing_title'),
            'plugin' => $plugin,
            'assignments' => [
                $this->get('plugin_assignment_name') => [
                    'assignment_state' => 1,
                    'selection' => $thing
                ]
            ]
        ];

        $addURL = Route::_('index.php?option=com_gsd&tmpl=component&layout=modal&view=item&' . http_build_query($query));

        // Add Media
        HTMLHelper::_('script', 'com_gsd/fastedit.js', ['version' => 'auto', 'relative' => true]);

        // Add language strings used by the JS plugin
        Text::script('GSD_ADD_SNIPPET');
        Text::script('GSD_EDIT_SNIPPET');
        Text::script('NR_ARE_YOU_SURE');

        Factory::getDocument()->addScriptOptions('gsd_fastedit', [
            'base_url'    => Uri::base(true),
            'add_url'     => $addURL,
            'plugin'      => $plugin,
            'thing_id'    => $thing,
            'thing_catid' => $this->get('thing_catid'),
            'plugin_assignment_name' => $this->get('plugin_assignment_name')
        ]);

        $html .= '<div class="gsdFastEdit" data-src="'. $addURL . '">
                ' . $this->renderModal() . '

                <div class="fast-edit-dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" data-toggle="dropdown">
                    <span class="icon-new"></span>'
                    . Text::_('GSD_ADD_SNIPPET') .'
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
             ';

        foreach ($contentTypes as $contentType) 
        {
            $html .= '<li><a data-contenttype="' . $contentType . '" data-bs-toggle="modal" data-toggle="modal" href="#gsdModal">' . Text::_('GSD_' . strtoupper($contentType)) . '</a></li>';
        }

        $html .= '</ul></div><div class="items"></div></div>';

        return $html;
    }

    /**
     *  Render the modal is going to be used by all buttons
     *
     *  @return  string
     */
    private function renderModal()
    {
        $options = array(
            'title'       => Text::_('GSD_EDIT_SNIPPET'),
            'url'         => '#',
            'height'      => '400px',
            'width'       => '800px',
            'backdrop'    => 'static',
            'bodyHeight'  => '70',
            'modalWidth'  => '70',
            'footer'      => '<a type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" aria-hidden="true">'
                    . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
                    . '<button type="button" class="btn btn-primary" aria-hidden="true"'
                    . ' onclick="document.querySelector(\'#gsdModal iframe\').contentDocument.querySelector(\'#saveBtn\').click();">'
                    . Text::_('JSAVE') . '</button>'
                    . '<button type="button" class="btn btn-success" aria-hidden="true"'
                    . ' onclick="document.querySelector(\'#gsdModal iframe\').contentDocument.querySelector(\'#applyBtn\').click();">'
                    . Text::_('JAPPLY') . '</button>',
        );

        return HTMLHelper::_('bootstrap.renderModal', 'gsdModal', $options);
    }

    public function getRows()
    {
        $input = Factory::getApplication()->input;

        $thing      = $input->getInt('thing');
        $plugin     = $input->get('plugin');
        $assignment = $input->get('assignment');

        $model = BaseDatabaseModel::getInstance('Items', 'GSDModel', ['ignore_request' => true]);
        $model->setState('list.limit', 0);
        $model->setState('filter.thing', $thing);
        $model->setState('filter.plugin', $plugin);

        $items = $model->getItems();

        foreach ($items as $key => &$item)
        {
            if (!isset($item->assignments) || 
                !isset($item->assignments->$assignment) || 
                !isset($item->assignments->$assignment->selection) ||
                $item->assignments->$assignment->assignment_state !== '1' ||
                !in_array($thing, (array) $item->assignments->$assignment->selection))
            {
                unset($items[$key]);
            }
        }

        return $items;
    }
}