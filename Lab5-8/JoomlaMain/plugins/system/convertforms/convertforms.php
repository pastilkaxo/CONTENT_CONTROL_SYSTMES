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

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Log\Log;
use ConvertForms\Helper;
use Joomla\Registry\Registry;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;

class PlgSystemConvertForms extends CMSPlugin
{
    /**
     *  Application Object
     *
     *  @var  object
     */
    protected $app;

    /**
     *  Component's param object
     *
     *  @var  Registry
     */
    private $param;

    /**
     *  The loaded indicator of helper
     *
     *  @var  boolean
     */
    private $init;

    /**
     *  Log Object
     *
     *  @var  Object
     */ 
    private $log;

    /**
     *  AJAX Response
     *
     *  @var  stdClass
     */
    private $response;

    /**
     *  Plugin constructor
     *
     *  @param  mixed   &$subject
     *  @param  array   $config
     */
    public function __construct(&$subject, $config = array())
    {
        $component = \Joomla\CMS\Component\ComponentHelper::getComponent('com_convertforms', true);

        /**
         * Εxecute parent constructor early as $app is not available when
         * we uninstall the administrator component.
         */
        parent::__construct($subject, $config);

        if (!$component->enabled)
        {   
            return;
        }

        // Load required classes
        if (!$this->loadClasses())
        {
            return;
        }

        // Declare extension logger
        Log::addLogger(
            array('text_file' => 'com_convertforms.php'),
            Log::ALL, 
            array('com_convertforms')
        );
    }

    /**
     * Replace shortcodes in the component's buffer without the need of the "Content Prepare" option. 
     * 
     * Note: This does not parse modules output. The "Custom Module" still requires the Content Prepare option to be enabled.
     * 
     * Note: The replacement that's performed here is causing issues when editing Joomla! articles (replaces the shortcode with actual content).
     * This hook may be removed in the future.
     *
     * @return void
     */
    public function onBeforeRender()
    {
        if (!$this->getHelper())
        {
            return;
        }

        if (!$this->canReplaceBeforeRender())
        {
            return;
        }

        $doc = $this->app->getDocument();

        $buffer = $doc->getBuffer('component');

        Helper::doShortcodeReplacements($buffer, false);

        $doc->setBuffer($buffer, 'component');
    }

    /**
     * Prevent the replacement when editing specific components
     * on the front-end of the website.
     */
    private function canReplaceBeforeRender()
    {
        $pass = true;

        switch ($this->app->input->get('option'))
        {
            case 'com_content':
                $pass = $this->app->input->get('view') !== 'form' && $this->app->input->get('layout') !== 'edit';
                break;
            case 'com_jevents':
                $pass = $this->app->input->get('task') !== 'icalevent.edit';
                break;
        }

        return $pass;
    }

    /**
     *  Handles the content preparation event fired by Joomla!
     *
     *  @param   mixed     $context     Unused in this plugin.
     *  @param   stdClass  $article     An object containing the article being processed.
     *  @param   mixed     $params      Unused in this plugin.
     *  @param   int       $limitstart  Unused in this plugin.
     *
     *  @return  bool
     */
    public function onContentPrepare($context, &$article)
    {
        if (!isset($article->text))
        {
            return true;
        }
        
        // Get Helper
        if (!$this->getHelper())
        {
            return true;
        }

        Helper::doShortcodeReplacements($article->text);
    }

    /**
     * Special integration for EngageBox that ensures Convert Forms shortcodes are parsed even if the EngageBox's Content Prepare option is disabled or not supported like in EngageBox Free.
     * 
     * If one day we decide to offer Content Prepare in EngageBox Free, we can get rid of this hook.
     * 
     * @param   string    $html   The campaign's final HTML
     * 
     * @return  void
     */
    public function onEngageBoxAfterRender(&$html)
    {
        if (!$this->getHelper())
        {
            return true;
        }

        Helper::doShortcodeReplacements($html, false);
    }

    /**
     * Improve performance and prevent conflicts by not allowing modules to be rendered in the form builder.
     * 
     * Don't use the onPrepareModuleList event as it does not work in Joomla 5.
     *
     * @param  array $modules The list of modules to be rendered in the page
     * 
     * @return void
     */
    public function onAfterModuleList(&$modules)
    {
        if ($this->app->isClient('administrator') && $this->app->input->get('option') == 'com_convertforms' && $this->app->input->get('view') == 'form')
        {
            $modules = [];
        }
    }

    /**
     *  Listens to AJAX requests on ?option=com_ajax&format=raw&plugin=convertforms
     *  Method aborts on invalid token or task
     *
     *  @return void
     */
    public function onAjaxConvertForms()
    {
        // Disable all PHP reporting to ensure a success AJAX response.
        $debug = ConvertForms\Helper::getComponentParams()->get('debug', false);
        if (!$debug)
        {
            error_reporting(E_ALL & ~E_NOTICE);
        }

        $input = $this->app->input;
        $form_id = isset($input->getArray()['cf']) ? $input->getArray()['cf']['form_id'] : 0;

        // Check if we have a valid task
        $task = $input->get('task', null);

        if (is_null($task))
        {
            die('Invalid task');
        }

        // An access token is required on all requests except on the API task which 
        // has a native authentication method through an API Key
        if (!in_array($task, ['api']) && !Session::checkToken('request'))
        {
            ConvertForms\Helper::triggerError(Text::_('JINVALID_TOKEN'), $task, $form_id, $input->request->getArray());
            jexit(Text::_('JINVALID_TOKEN'));
        }

        // Cool access granted.
        $componentPath = JPATH_ADMINISTRATOR . '/components/com_convertforms/';
        BaseDatabaseModel::addIncludePath($componentPath . 'models');
        Table::addIncludePath($componentPath . 'tables');

        // Load component language file
        NRFramework\Functions::loadLanguage('com_convertforms');

        // Check if we have a valid method task
        $taskMethod = 'ajaxTask' . $task;

        if (!method_exists($this, $taskMethod))
        {
            die('Task not found');
        }

        // Success! Let's call the method.
        $this->response = new stdClass();

        try
        {
           $this->$taskMethod();
        }
        catch (Exception $e)
        {
            ConvertForms\Helper::triggerError($e->getMessage(), $task, $form_id, $input->request->getArray());
            $this->response->error = $e->getMessage();
        }

        echo json_encode($this->response);

        // Stop execution
        jexit();
    }

    
    
    /**
     *  Map onContentAfterSave event to onConvertFormsConversionAfterSave
     *  
     *  Content is passed by reference, but after the save, so no changes will be saved.
     *  Method is called right after the content is saved.
     *
     *  @param   string  $context  The context of the content passed to the plugin (added in 1.6)
     *  @param   object  $article  A JTableContent object
     *  @param   bool    $isNew    If the content has just been created
     *
     *  @return  void
     * 
     *  @deprecated Remove this code block and update the onConvertFormsConversionAfterSave() method to use onConvertFormsSubmissionAfterSave().
     */
    public function onContentAfterSave($context, $article, $isNew)
    {
        if ($context != 'com_convertforms.conversion' || $this->app->isClient('administrator'))
        {
            return;
        }

        PluginHelper::importPlugin('convertforms');
        PluginHelper::importPlugin('convertformstools');

        // Load item row
        $model = BaseDatabaseModel::getInstance('Conversion', 'ConvertFormsModel', array('ignore_request' => true));
        if (!$conversion = $model->getItem($article->id))
        {
            return;
        }

        Factory::getApplication()->triggerEvent('onConvertFormsConversionAfterSave', array($conversion, $model, $isNew));
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
        // Return if we are in frontend
        if ($this->app->isClient('site'))
        {
            return true;
        }

        // Check we have a form
        if (!($form instanceof Joomla\CMS\Form\Form))
        {
            return false;
        }

        // Check we have a valid form context
        $validForms = array(
            "com_convertforms.campaign",
            "com_convertforms.form"
        );

        if (!in_array($form->getName(), $validForms))
        {
            return true;
        }

        // Load ConvertForms plugins
        PluginHelper::importPlugin('convertforms');
        PluginHelper::importPlugin('convertformstools');

        // Campaign Forms
        if ($form->getName() == 'com_convertforms.campaign')
        {
            if (!isset($data->service) || !$service = $data->service)
            {
                return true;
            }
            
            $result = Factory::getApplication()->triggerEvent('onConvertFormsCampaignPrepareForm', [$form, $data, $service]);
        }

        // Form Editing Page
        if ($form->getName() == 'com_convertforms.form')
        {
            $result = Factory::getApplication()->triggerEvent('onConvertFormsFormPrepareForm', [$form, $data]);
        }

        return true;
    }

    /**
     *  Silent load of Convert Forms and Framework classes
     *
     *  @return  boolean
     */
    private function loadClasses()
    {
        // Initialize Convert Forms Library
        if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_convertforms/autoload.php'))
        {
            return false;
        }

        // Load Framework
        if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
        {
            return false;
        }

        // Declare extension's error log file
        Joomla\CMS\Log\Log::addLogger(
            [
                'text_file' => 'convertforms_errors.php',
                'text_entry_format' => '{MESSAGE}'
            ], 
            Joomla\CMS\Log\Log::ERROR, 
            ['convertforms_errors']
        );

        return true;
    }

    /**
     *  Loads the helper classes of plugin
     *
     *  @return  bool
     */
    private function getHelper()
    {
        // Return if is helper is already loaded
        if ($this->init)    
        {
            return true;
        }

        // Return if we are not in frontend
        if (!$this->app->isClient('site'))
        {
            return false;
        }

        // Return if document type is Feed
        if (NRFramework\Functions::isFeed())
        {
            return false;
        }

        // Load language
        Factory::getLanguage()->load('com_convertforms', JPATH_ADMINISTRATOR . '/components/com_convertforms');

        return ($this->init = true);
    }
}
