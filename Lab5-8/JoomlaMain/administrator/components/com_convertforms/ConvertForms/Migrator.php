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

use Joomla\Registry\Registry;
use \NRFramework\Countries;
use Joomla\CMS\Factory;

/**
 *  Convert Forms Migrator
 */
class Migrator
{
    /**
     * The database class
     *
     * @var object
     */
    private $db;

    /**
     * Indicates the current installed version of the extension
     *
     * @var string
     */
    private $installedVersion;

    /**
     * Class constructor
     *
     * @param string $installedVersion  The current extension version
     */
    public function __construct($installedVersion)
    {
        $this->installedVersion = $installedVersion;
        $this->db = Factory::getDbo();
    }

	/**
	 *  Start migration process
	 *
	 *  @return  void
	 */
	public function start()
	{
        $this->set_sib_campaigns_default_v2_version();
        
        if (!$items = $this->getForms())
        {
            return;
        }
        
		foreach ($items as $item)
        {
            $item->params = new Registry($item->params);

            if (version_compare($this->installedVersion, '4.4.4', '<='))
            {
                $this->fixNewEditorValueInTasks($item);
            }

            if (version_compare($this->installedVersion, '4.0.2', '<')) 
            {
                $this->fixInputMask($item);
            }

            if (version_compare($this->installedVersion, '2.7.3', '<=')) 
            {
				$this->fixUploadFolder($item);
            }
           
            

            // Update item using id as the primary key.
            $item->params = json_encode($item->params);
            $this->db->updateObject('#__convertforms', $item, 'id');
        }
	}

    private function fixNewEditorValueInTasks(&$item)
    {
        if (!$tasks = \ConvertForms\Tasks\ModelTasks::getItems($item->id))
        {
            return;
        }

        if (!$tasks || !is_array($tasks) || !count($tasks))
        {
            return;
        }
        
        $allowedApps = [
            
            'email'
        ];

        foreach ($tasks as $ordering => $_task)
        {
            if (!in_array($_task['app'], $allowedApps))
            {
                continue;
            }

            

            if ($_task['app'] === 'email')
            {
                $_task['options']['body'] = str_replace(["\r\n", "\r", "\n"], '<br>', $_task['options']['body']);
            }

            // Update task
            $_task['ordering'] = $ordering;
            $_task['form_id'] = $item->id;
            \ConvertForms\Tasks\ModelTasks::save($_task);
        }
    }

    private function fixInputMask(&$item)
    {
        if (!$fields = $item->params->get('fields'))
        {
            return;
        }

        foreach ($fields as &$field)
        {
            if (!isset($field->inputmask) || empty($field->inputmask) || !is_scalar($field->inputmask))
            {
                continue;
            }

            $field->inputmask = [
                'options' => 'custom',
                'custom'  => $field->inputmask
            ];
        }
    }

	private function fixUploadFolder(&$item)
	{
        if (!$fields = $item->params->get('fields'))
        {
            return;
        }

        foreach ($fields as &$field)
        {
            if ($field->type != 'fileupload')
            {
                continue;
            }

            if (isset($field->upload_folder_type))
            {
                continue;
            }

            $field->upload_folder_type = 'custom';
        }
    }
    
    /**
     * Finds all SendInBlue campaigns and sets the version to v2 if not set.
     * 
     * @since   2.8.4
     * 
     * @return  void
     */
    private function set_sib_campaigns_default_v2_version()
    {
        if (version_compare($this->installedVersion, '2.8.4', '>=')) 
        {
            return;
        }

        if (!$campaigns = $this->getCampaigns('sendinblue'))
        {
            return;
        }
        
		foreach ($campaigns as $item)
        {
            $item->params = new Registry($item->params);

            // version exists, move on
            if ($version = $item->params->get('version'))
            {
                continue;
            }
            
            // if no version is found, set it to v2
            $item->params->set('version', '2');

            // Also set update existing to default value, true
            $item->params->set('updateexisting', '1');
            
            // Update item using id as the primary key.
            $item->params = json_encode($item->params);
            $this->db->updateObject('#__convertforms_campaigns', $item, 'id');
        }
    }

    

	/**
	 *  Get Forms List
	 *
	 *  @return  object
	 */
	public function getForms()
	{
		$db = $this->db;
		$query = $db->getQuery(true);

		$query
			->select('*')
			->from('#__convertforms');

		$db->setQuery($query);

		return $db->loadObjectList();
	}
    
	/**
	 * Get form submissions.
	 *
	 * @return  object
	 */
	public function getFormSubmissions($form_id = null)
	{
        if (!$form_id)
        {
            return;
        }
        
		$db = $this->db;
		$query = $db->getQuery(true);

		$query
			->select('*')
			->from('#__convertforms_conversions')
			->where($this->db->quoteName('form_id') . ' = ' . $this->db->quote($form_id));

		$db->setQuery($query);

		return $db->loadObjectList();
	}
    

	/**
	 *  Get Campaigns List
	 *
     *  @param   string  $service
     * 
	 *  @return  object
	 */
	public function getCampaigns($service = null)
	{
		$db = $this->db;
		$query = $db->getQuery(true);

		$query
			->select('*')
            ->from('#__convertforms_campaigns');
        
        if ($service)
        {
			$query->where($this->db->quoteName('service') . ' = ' . $this->db->quote($service));
        }

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}