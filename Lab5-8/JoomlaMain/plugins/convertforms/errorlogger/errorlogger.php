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

use NRFramework\WebClient;
use ConvertForms\Form;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Factory;

class plgConvertFormsErrorLogger extends CMSPlugin
{
    /**
     * Joomla Application Object
     *
     * @var object
     */
    protected $app;

    /**
     *  Add plugin fields to the form
     *
     *  @param   JForm   $form  
     *  @param   object  $data
     *
     *  @return  boolean
     */
    public function onConvertFormsError($error, $category, $form_id, $data = null)
    {
        // Only on front-end
        if ($this->app->isClient('administrator'))
        {
            return;
        }

        if (isset($data['skip_error_logger']))
        {
            return;
        }

        $user = Factory::getUser();

        // Get form's name
        $form_data = Form::load($form_id);
        $form_name = isset($form_data['name']) ? $form_data['name'] : 'Unknown Form';
        $form_name .= ' (' . $form_id . ')';

$error_message = '

Identity
---------------------------------------------------------------------------
Date Time:          ' . Factory::getDate() . '
Error Category:     ' . $category . '
Error message:      ' . $error . '
Form:               ' . $form_name . '
Session ID:         ' . Factory::getSession()->getId() . '
IP Address:         ' . $this->app->input->server->get('REMOTE_ADDR') . '
User Agent:         ' . WebClient::getClient()->userAgent . '
Device:             ' . WebClient::getDeviceType() . '
Logged In Username: ' . $user->username . '
Logged In Name:     ' . $user->name . '

Data
---------------------------------------------------------------------------
' . print_r($data, true) . '

Request Headers
---------------------------------------------------------------------------
' . print_r($this->app->input->server->getArray(), true) . '
';

        try {
            Log::add($error_message, Log::ERROR, 'convertforms_errors');
        } catch (\Throwable $th) {
        }
    }
}