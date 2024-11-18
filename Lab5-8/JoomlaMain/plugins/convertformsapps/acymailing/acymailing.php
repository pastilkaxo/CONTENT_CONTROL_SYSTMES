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

use ConvertForms\Tasks\App;
use Joomla\CMS\Language\Text;
use ConvertForms\Tasks\Helper;
use Joomla\CMS\HTML\HTMLHelper;
use AcyMailing\Classes\FieldClass;
use ConvertForms\Tasks\LimitAppUsage;
use NRFramework\Extension;
use Joomla\CMS\Factory;

class plgConvertFormsAppsAcyMailing extends App
{   
    
    // This app can only be used once in the Free version.
    use LimitAppUsage;
    

    /**
     * To be able to use this app, AcyMailing 6 or higher must be installed.
     *
     * @return mixed, null when the requirement is met, array otherwise.
     */
	public function reqAcym()
	{
        if (!Extension::isInstalled('acym'))
        {
            return [
                'text' => Text::sprintf('COM_CONVERTFORMS_TASKS_EXTENSION_IS_MISSING', $this->lang('ALIAS'))
            ];
        }
	}

	/**
	 * The Subscribe trigger
	 *
	 * @return void
	 */
	public function actionSubscribe()
	{
        @include_once implode(DIRECTORY_SEPARATOR, [JPATH_ADMINISTRATOR, 'components', 'com_acym', 'helpers', 'helper.php']);
        
        // Calculate merge tags
        $keysToRemove = [
            'lists',
            'email',
            'double_optin',
            'trigger_acym_notifications'
        ];

        $merge_tags = array_diff_key($this->options, array_flip($keysToRemove));

        $this->prepareLanguageFields($merge_tags);

        $trigger_acym_notifications = isset($this->options['trigger_acym_notifications']) ? $this->options['trigger_acym_notifications'] === '1' : false;

        return \ConvertForms\Helpers\AcyMailing::subscribe($this->options['email'], $merge_tags, $this->options['list'], $this->options['double_optin'], $trigger_acym_notifications);
	}

    /**
     * Prepare the language custom fields.
     * 
     * When a language field is set to "auto", then use the current Joomla language.
     * 
     * @param   array  $merge_tags  The merge tags.
     * 
     * @return  void
     */
    private function prepareLanguageFields(&$merge_tags)
    {
        // Get all AcyMailing Custom Fields
        $customFields = $this->getCustomFields();

        // Get all language fields
        $languageFields = array_filter($customFields, function($field) {
            return $field['type'] === 'language';
        });

        if (!$languageFields)
        {
            return;
        }
        
        $languageFields = array_column($languageFields, 'tag');

        // Find all language fields within $merge_tags, and if their value is set to "auto", then use the current Joomla language
        foreach ($merge_tags as $key => &$value)
        {
            if (!in_array($key, $languageFields) || $value !== 'auto')
            {
                continue;
            }

            $value = Factory::getLanguage()->getTag();
        }
    }

    /**
     * Get a list with the fields needed to setup the app's event.
     *
     * @return array
     */
	public function getActionSubscribeSetupFields()
	{
        @include_once implode(DIRECTORY_SEPARATOR, [JPATH_ADMINISTRATOR, 'components', 'com_acym', 'helpers', 'helper.php']);

        $mergeTags = [
            $this->commonField('email')
        ];

        if ($customFields = $this->getCustomFields())
        {
            $languages = $this->getLanguages();

            foreach ($customFields as $customField)
            {
                $payload = [
                    'label' => $customField['label'],
                    'hint' => Text::sprintf('COM_CONVERTFORMS_TASKS_CUSTOM_FIELD', $customField['label'], $this->lang('ALIAS')),
                    'required' => $customField['required'] === 1
                ];
                
                // Language field requires a special treatment
                if ($customField['type'] === 'language')
                {
                    $payload['options'] = $languages;
                    $payload['value'] = 'auto';
                }
                
                $mergeTags[] = $this->field($customField['tag'], $payload);
            }
        }

        $fields = [
            [
                'name' => Text::_('COM_CONVERTFORMS_APP_SETUP_ACTION'),
                'fields' => [
                    $this->field('list', [
                        'loadOptions' => $this->getAjaxEndpoint('getLists'),
                        'includeSmartTags' => 'Fields'
                    ]),
                    $this->commonField('double_optin'),
                    $this->field('trigger_acym_notifications', [
                        'type' => 'bool',
                        'required' => false,
                        'value' => '0',
                        'includeSmartTags' => false
                    ]),
                ]
            ],
            [
                'name' => Text::_('COM_CONVERTFORMS_APP_MATCH_FIELDS'),
                'fields' => $mergeTags
            ]
        ];

        return $fields;
	}

    /**
     * Returns the languages.
     * 
     * @return  array
     */
    private function getLanguages()
    {
        // Get languages
        $languages = acym_getLanguages(true, true);
        $languages = array_values($languages);
        
        array_walk($languages, function($value) {
            $value->value = $value->language;
            $value->label = $value->name;
        });

        // Add to the beginning of the array the "auto" option
        array_unshift($languages, (object) ['value' => 'auto', 'label' => Text::_('PLG_CONVERTFORMSAPPS_ACYMAILING_CURRENT_USER_LANGUAGE')]);

        return $languages;
    }

    /**
     * Returns all custom fields.
     * 
     * @return  array
     */
    public function getCustomFields()
    {
        $fields = [];

        $sql = 'SELECT id, name, type, required FROM #__acym_field WHERE active = 1 AND id NOT IN (2) ORDER BY ordering';

        foreach (acym_loadObjectList($sql) as $field)
        {
            /**
             * Some of the AcyMailing custom fields names are by default set to language strings (e.g. "ACYM_LANGUAGE")
             * 
             * For this reason, we need to translate them to get the correct field name.
             * 
             * Otherwise, we won't be able to match the fields correctly.
             */
            $tag = strpos($field->name, 'ACYM_') !== false ? Text::_($field->name) : $field->name;

            $fields[] = [
                'tag' => $tag,
                'label' => Text::_($field->name),
                'type' => $field->type,
                'required' => $field->required
            ];
        }

        return $fields;
    }

    /**
     * Returns all lists.
     * 
     * @return  array
     */
    public function getLists()
    {
        @include_once(JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php');

        $lists = acym_get('class.list')->getAll();

        if (!is_array($lists))
        {
            return;
        }

        $lists_ = [];

        foreach ($lists as $list)
        {
            if (!$list->active)
            {
                continue;
            }

            $lists_[] = [
                'value' => $list->id,
                'label' => isset($list->display_name) && !empty($list->display_name) ? $list->display_name : $list->name,
                'desc'  => $list->description
            ];
        }

        return $lists_;
    }
}