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

namespace ConvertForms;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
 *  This is the main Convert Forms API helper class meant to be used ONLY by 3rd party developers and advanced users. 
 *  Do not ever use this class to implement and rely any core feture.
 */
class Api
{
    /**
     * Delete a submission from the database
     *
     * @param  integer $id  The submission's primary key
     *
     * @return bool True on success
     */
    public static function removeSubmission($submission_id)
    {
        if (!$submission_id)
        {
            return;
        }

		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/tables');
        $table = Table::getInstance('Conversion', 'ConvertFormsTable');
        return $table->delete($submission_id);
    }

    /**
     * Delete all form submissions from the database
     *
     * @param  integer $form_id     The form's primary key
     *
     * @return bool
     */
    public static function removeFormSubmissions($form_id)
    {
        if (!$form_id)
        {
            return;
        }

        $db = Factory::getDbo();

        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__convertforms_conversions'))
            ->where($db->quoteName('form_id') . ' = ' . $form_id);
        
        $db->setQuery($query);
        
        return $db->execute();
    }

    /**
     * Return all form submissions
     *
     * @param   integer $form_id    The form's ID
     *
     * @return  Object
     */
    public static function getFormSubmissions($form_id)
    {
        if (!$form_id)
        {
            return;
        }

        $db = Factory::getDbo();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__convertforms_conversions'))
            ->where($db->quoteName('form_id') . ' = ' . $form_id);
    
        $db->setQuery($query);
        
        $rows = $db->loadObjectList();

        foreach ($rows as $key => $row)
        {
            $row->params = json_decode($row->params);
        }

        return $rows;
    }

    /**
     * Return the total number of form total submissions
     *
     * @param   integer $form_id    The form's ID
     *
     * @return  integer
     */
    public static function getFormSubmissionsTotal($form_id)
    {
        return number_format(Form::getSubmissionsTotal($form_id));
    }

    /**
     * Get the visitor's device type: desktop, tablet, mobile
     *
     * @return string
     */
    public static function getDeviceType()
    {
        return \NRFramework\WebClient::getDeviceType();
    }

    /**
     * Indicate if the visitor is browsing the site via a mobile
     *
     * @return bool
     */
    public static function isMobile()
    {
        return self::getDeviceType() == 'mobile';
    }

    /**
     * Update a field value of the given submission
     *
     * @param   Integer $submission_id  The id of the submission
     * @param   string  $field_name     The name of the field to update
     * @param   string  $field_value    The new value of the field
     * 
     * @return void
     */
    public static function updateSubmissionField($submission_id, $field_name, $field_value = '')
    {
        if (!$submission_id || !$field_name)
        {
            return;
        }

        $table = Table::getInstance('Conversion', 'ConvertFormsTable');
        $table->load($submission_id);

        $params = json_decode($table->params);

        $params->$field_name = $field_value;
        $table->params = json_encode($params);
        $table->store();
    }
}