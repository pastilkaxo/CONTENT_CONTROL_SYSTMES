<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use ConvertForms\Helper;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class ConvertFormsControllerCron extends FormController
{
    /**
     * Joomla Application Object
     * 
     * Joomla 4 requires this to be protected or weaker.
     *
     * @var object
     */
    protected $app;

    /**
     * The secret key configured in the configuration page
     *
     * @var string
     */
    private $secret;

	/**
     *  Class Constructor
     *
     *  @param  string  $key  User API Key
     */
    public function __construct()
    {
        // Register a new generic Convert Forms CRON logger
        Log::addLogger(['text_file' => 'convertforms_cron.php'], Log::ALL, ['convertforms.cron']);

        $this->app    = Factory::getApplication();
        $this->secret = Helper::getComponentParams()->get('api_key');
        
		parent::__construct();
    }

    /**
     * Start the cron task
     *
     * @return void
     */
    public function cron()
    {
        $this->log('Starting CRON job', Log::DEBUG);

        // Makes sure SiteGround's SuperCache doesn't cache the CRON view
        $this->app->setHeader('X-Cache-Control', 'False', true);

        if (empty($this->secret))
        {
            $this->log('No secret key configured', Log::ERROR);
			header('HTTP/1.1 503 Service unavailable due to configuration');
            jexit();
        }

        // Authenticate request
        if ($this->app->input->get('secret', null, 'raw') != $this->secret)
        {
            $this->log('Wrong secret key provided in URL', Log::ERROR);
			header('HTTP/1.1 403 Forbidden');
            jexit();
        }

        // Validate command to run
        $command        = $this->app->input->get('command', null, 'raw');
        $command        = trim(strtolower($command));
        $commandEscaped = InputFilter::getInstance()->clean($command, 'cmd');

        if (empty($command))
        {
            $this->log('No command provided in URL', Log::ERROR);
			header('HTTP/1.1 501 Not implemented');
            jexit();
        }

        // Register a new task-specific Convert Forms CRON logger
        Log::addLogger(['text_file' => "convertforms_cron_$commandEscaped.php"], Log::ALL, ['convertforms.cron.' . $command]);
        $this->log("Starting execution of command $commandEscaped", Log::DEBUG);

        // Import plugins and trigger the cron task event
        PluginHelper::importPlugin('system');
        PluginHelper::importPlugin('convertforms');
        PluginHelper::importPlugin('convertformstools');
        $this->app->triggerEvent('onConvertFormsCronTask', [$command, ['time_limit' => 10]]);

        $this->log("Finished running command $commandEscaped", Log::DEBUG);
        
        echo $commandEscaped . ' OK';
        jexit();
    }

    /**
     * Log message to the default log file
     *
     * @param string $msg
     * @param object $type
     *
     * @return void
     */
    private function log($msg, $type)
    {
        try {
            Log::add($msg, $type, 'convertforms.cron');
        } catch (\Throwable $th) {
        }
    }
}