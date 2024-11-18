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

namespace GSD;

defined('_JEXEC') or die('Restricted access');

use GSD\Json;
use GSD\Helper;
use GSD\MappingOptions;
use NRFramework\Cache;
use NRFramework\Assignments;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;

/**
 *  Google Structured Data helper class
 */
class PluginBase extends CMSPlugin
{
    /**
     *  Auto load the plugin language file
     *
     *  @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     *  Joomla Application Object
     *
     *  @var  object
     */
    protected $app;

    /**
     *  The selected app view.
     *
     *  @var  string
     */
    protected $appview;

    /**
     *  Joomla Database Object
     *
     *  @var  object
     */
    protected $db;

    /**
     *  Holds all available snippets for the current active page.
     *
     *  @var  array
     */
    protected $snippets;

    /**
     *  Indicates the query string parameter name that is used by the front-end component
     *
     *  @var  string
     */
    protected $thingRequestIDName = 'id';

    /**
     *  Indicates the request variable name used by plugin's assosiated component
     *
     *  @var  string
     */
    protected $thingRequestViewVar = 'view';

    /**
     *  Plugin constructor
     *
     *  @param  mixed   &$subject
     *  @param  array   $config
     */
    public function __construct(&$subject, $config = [])
    {
        // Load main language file
        Factory::getLanguage()->load('plg_system_gsd', JPATH_PLUGINS . '/system/gsd');

        // execute parent constructor
        parent::__construct($subject, $config);
    }

    /**
     * Return a list of all supported views. 
     * 
     * While in most Apps we support 1 view, in Apps like the J-Business Directory where we support 3 views. The App View dropdown helps us tell what 
     * snippets should be rendered per view without the need for Conditions. 
     * 
     * The App View information helps us improve performance on the front-end and UX on the back-end. In detail using the App View we can:
     * 
     * 1. [Front-end] Fetch only the snippets based on the active view. 
     * 2. [Back-end] Filter the Mapping Dropdown options. (Eg: When marking up a Product page we don't need mapping options related to an Event page.)
     * 3. [Back-end] Filter displayed Conditions per view. (Eg: When marking up a Product page, we don't need Conditions related to Event pages.)
     *
     * @return array
     */
    public function advertiseSupportedViews()
    {
        $methods = get_class_methods($this);
        $supportedViews = [];

        foreach ($methods as $method)
        {
            if (strpos($method, 'view') !== 0)
            {
                continue;
            }

            $viewName = strtolower(str_replace('view', '', $method));

            $supportedViews[$viewName] = Text::_('PLG_GSD_' . strtoupper($this->_name) . '_VIEW_' . strtoupper($viewName));
        }

        return $supportedViews;
    }

    /**
     *  Event triggered to gather all available plugins.
     *  Mostly used by the dropdowns in the backend.
     *
     *  @param   boolean  $mustBeInstalled  If enabled, the assosiated component must be installed
     *
     *  @return  array
     */
    public function onGSDGetType($mustBeInstalled = true)
    {
        if ($mustBeInstalled && !\NRFramework\Extension::isInstalled($this->_name))
        {
            return;
        }

        return [
            'name'  => Text::_('PLG_GSD_' . strtoupper($this->_name) . '_ALIAS'),
            'alias' => $this->_name
        ];
    }

     /**
     *  Prepare form.
     *
     *  @param   Form  $form  The form to be altered.
     *  @param   mixed  $data  The associated data for the form.
     *
     *  @return  boolean
     */
    public function onContentPrepareForm($form, $data)
    {
        // Make sure we are on the right context
        if ($this->app->isClient('site') || $form->getName() != 'com_gsd.item')
        {
            return;
        }

        // When item is not saved yet, the $data variable is type of Array.
        $tempData = (object) $data;

        if (!isset($tempData->plugin) || is_null($tempData->plugin) || $tempData->plugin != $this->_name)
        {
            return;
        }

        $view = isset($tempData->appview) ? $tempData->appview : '';

        $this->appview = $view;
        
        $viewXMLName = !empty($view) && $view !== '*' ? $view : 'assignments';

        // The assignments XML file base
        $assignmentsXMLFileBase = JPATH_PLUGINS . '/gsd/' . $this->_name . '/form/';

        $assignmentsXML = $assignmentsXMLFileBase . $viewXMLName . '.xml';

        /**
         * The XML file can be found in the following files:
         * 
         * - {VIEW}.xml
         *      Used individually for each view to provide different assignments.
         * - assignments.xml
         *      Used by single-view integrations or multi-view integrations that offer
         *      the same assignments per view (i.e. J2Store).
         */

        // Check view-based XML
        if (!is_file($assignmentsXML))
        {
            $assignmentsXML = $assignmentsXMLFileBase . 'assignments.xml';
        
            // Check generic XML
            if (!is_file($assignmentsXML))
            {
                return;
            }
        }

        $form->loadFile($assignmentsXML, false);
    }
    
    /**
     *  The event triggered before the JSON markup be appended to the document.
     *
     *  @param   array  &$data   The JSON snippets to be appended to the document
     *
     *  @return  void
     */
    public function onGSDBeforeRender(&$data)
    {
        // Quick filtering on component check
        if (!$this->passContext())
        {
            return;
        }

        // Let's check if the plugin supports the current component's view.
        if (!$payload = $this->getPayload())
        {
            return;
        }

        // Now, let's see if we have valid snippets for the active page. If not abort.
        if (!$this->snippets = $this->getSnippets())
        {
            $this->log('No valid items found');
            return;
        }

        // Prepare snippets
        foreach ($this->snippets as $snippet)
        {
            // Here, the payload must be merged with the snippet data
            $jsonData = $this->preparePayload($snippet, $payload);

            // Create JSON
            $jsonClass = new Json($jsonData);
            $json = $jsonClass->generate();

            // Add json back to main data object
            $data[] = $json;
        }
    }

    /**
     *  Validate context to decide whether the plugin should run or not.
     *
     *  @return   bool
     */
    protected function passContext()
    {
        return Helper::getComponentAlias() == $this->_name;
    }

    /**
     *  Get Item's ID
     *
     *  @return  string
     */
    protected function getThingID()
    {
        return $this->app->input->getInt($this->thingRequestIDName);
    }

    /**
     *  Get component's items and validate conditions
     *
     *  @return  Mixed   Null if no items found, The valid items array on success
     */
    protected function getSnippets()
    {
        BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/models');

        $model = BaseDatabaseModel::getInstance('Items', 'GSDModel', ['ignore_request' => true]);
        $model->setState('filter.plugin', $this->_name);

        // Since we did not code any migration script, pass asterisk to match old rows as well.
        $model->setState('filter.appview', [$this->getView(), '*']); 
        
        $model->setState('filter.state', 1);

        if (Multilanguage::isEnabled())
        {
            $model->setState('filter.language', [Factory::getLanguage()->getTag(), '*']);
        }

        if (!$rows = $model->getItems())
        {
            return;
        }

        // Check publishing assignments for each item
        foreach ($rows as $key => $row)
        {
            if (!isset($row->assignments) || !is_object($row->assignments))
            {
                continue;
            }

            // Prepare assignments
            $assignmentsFound = [];

            foreach ($row->assignments as $alias => $assignment)
            {
                if ($assignment->assignment_state == '0')
                {
                    continue;
                }

                // Remove unwanted assignments added by Free Pro code blocks
                if (strpos($alias, '@'))
                {
                    continue;
                }

                // If user hasn't made any selection, skip the assignment.
                if (!isset($assignment->selection))
                {
                    continue;
                }

                // Comply with the new conditions requirements
                $condition = (object) [
                    'alias'  => $alias,
                    'value'  => $assignment->selection,
                    'params' => isset($assignment->params) ? $assignment->params : [],
                    'assignment_state' => $assignment->assignment_state
                ];

                // Pass with 'AND' matching method. Hence the assignment to first [0] cell.
                $assignmentsFound[0][] = $condition;
            }

            // Validate assignments
            if (!$pass = (new Assignments())->passAll($assignmentsFound))
            {
                $this->log('Item #' . $row->id . ' does not pass the conditions check');
                unset($rows[$key]);
            }
        }

        $items = array_map(function($row)
        {
            $contentType = $row->contenttype;

            // After we have selected an Integration and a Content Type and we hit Save,
            // the item needs to be re-saved in order to access the Content Type options.
            //
            // We need to find a way to auto-populate the Content Type with default data during 1st save.
            //
            // A possible approach would be: Upon clicking on the New button, we display a popup modal where
            // the user can choose a Content Type, an Integration and a Title for the structured data item.
            // Then they will be redirected to the item editing page with these data prefilled.
            // 
            // UPDATE 04/05/2022: Since we have changed the way the structured data item is saved using the Joomla Loader (state) this may be no longer an issue. It needs a check.
            $contentTypeData = property_exists($row, $contentType) ? $row->{$contentType} : [];

            $s = new Registry($contentTypeData);
            $s->set('contentType', $contentType);
            $s->set('snippet_id', $row->id);

            // Help troubleshooting by logging item ID.
            $this->log('ID: ' . $row->id);

            return $s;
        }, $rows);

        return $items;
    }

    /**
     *  Asks for data from the child plugin based on the active view name
     *
     *  @return  Registry  The payload Registry
     */
    protected function getPayload()
    {
        $view   = $this->getView();
        $method = 'view' . ucfirst($view);

        if (!$view || !method_exists($this, $method))
        {
            $this->log('View ' . $view . ' is not supported');
            return;
        }

        // Yeah. Let's call the method. 
        $payload = $this->$method();

        // We need a valid array
        if (!is_array($payload))
        {  
            $this->log('Invalid Payload Array');
            return;
        }

        // If the payload contains any objects, convert them to an associative array
        $payload = json_decode(json_encode($payload), true);

        // Convert payload to Registry object and return it
        return new Registry($payload);
    }

    /**
     *  Prepares the payload to be used in the JSON class
     *
     *  @return  string
     */
    private function preparePayload($snippet, $payload)
    {   
        $schema = \GSD\Schemas\Helper::getInstance($snippet['contentType']);
        $schema->onPayloadPrepare($payload); // Temporary workaround. See comments in the Custom_Code class.

        MappingOptions::prepare($snippet);

        // Create a new combined object by merging the snippet data into the payload
        // Note: In order to produce a valid merged object, payload's array keys should match the field names
        // as declared in the form's XML file.
        $p = clone $payload;
        $s = $p->merge($snippet, false);

        // Replace Smart Tags - This can be implemented with a Plugin
        $s = MappingOptions::replace($s, $payload);

        $prepareContent = Helper::getParams()->get('preparecontent', false);

        // Content Preparation
        if ($prepareContent)
        {
            $s['headline']    = $this->prepareText($s['headline']);
            $s['description'] = $this->prepareText($s['description']);
        }

        return $schema->setData($s)->get();
    }

    /**
     *  Get View Name
     *
     *  @return  string  Return the current executed view in the front-end
     */
    protected function getView()
    {
        return $this->app->input->get($this->thingRequestViewVar);
    }

    /**
     * Prepare given text with Content and Field plugins
     *
     * @param  string $text
     *
     * @return string 
     */
    private function prepareText($text)
    {
        if (!$text)
        {
            return;
        }
        
        return \Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $text);
    }

    /**
     *  Log messages
     *
     *  @param   string  $message  The message to log
     *
     *  @return  void
     */
    protected function log($message)
    {
        Helper::log(Text::_('PLG_GSD_' . $this->_name . '_ALIAS') . ' - ' . $message);
    }
}