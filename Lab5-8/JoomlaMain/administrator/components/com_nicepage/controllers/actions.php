<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

/**
 * Class NicepageControllerActions
 */
class NicepageControllerActions extends FormController
{
    /**
     * Execute actions controller
     *
     * @param string $action Type of action
     */
    public function execute($action)
    {
        $app = Factory::getApplication();

        $viewName = 'actions';
        $app->input->set('action', $action);
        $app->input->set('view', $viewName);

        $document = Factory::getDocument();
        $document->setType('json');

        return parent::execute($viewName);
    }
}