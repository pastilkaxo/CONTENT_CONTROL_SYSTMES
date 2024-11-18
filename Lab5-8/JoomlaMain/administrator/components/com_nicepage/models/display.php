<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Class NicepageModelDisplay
 */
class NicepageModelDisplay extends AdminModel
{
    /**
     * Method to get the record form.
     *
     * @param array   $data     Data for the form. [optional]
     * @param boolean $loadData True if the form is to load its own data (default case), false if not. [optional]
     *
     * @return Form|boolean A Form object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_nicepage.display', 'display', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }
}