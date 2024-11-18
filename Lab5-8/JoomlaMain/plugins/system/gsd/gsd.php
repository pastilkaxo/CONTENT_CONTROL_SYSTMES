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

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use GSD\Helper;

class plgSystemGSD extends CMSPlugin
{
	/**
	 *  Auto loads the plugin language file
	 *
	 *  @var  boolean
	 */
	protected $autoloadLanguage = true;

	/**
	 *  The loaded indicator of helper
	 *
	 *  @var  boolean
	 */
	protected $init;

	/**
	 *  Application Object
	 *
	 *  @var  object
	 */
	protected $app;

	/**
	 *  JSON Helper
	 *
	 *  @var  class
	 */
	private $json;

	/**
	 *  onBeforeCompileHead event to add JSON markup to the document
	 *
	 *  @return void
	 */
	public function onBeforeCompileHead()
	{	
		// Load Helper
		if (!$this->getHelper())
		{
			return;
		}

		if (!$this->params->get('wait_page_render', false) && $markup = $this->getMarkup())
		{
			Factory::getDocument()->addCustomTag($markup);
		}

		// Add Snippets Control
		$this->addRobotSnippetControl();
	}

	/**
	 *  This event is triggered after the framework has rendered the application.
	 *
	 *  @return void
	 */
	public function onAfterRender()
	{
		// Load Helper
		if (!$this->getHelper())
		{
			return;
		}

		

		if ($this->params->get('wait_page_render', false) && $markup = $this->getMarkup())
		{
			$buffer = $this->app->getBody();

			// If </body> exists prepend the markup
			if (strpos($buffer, '</body>'))
			{
				$buffer = str_replace('</body>', $markup . '</body>', $buffer);
			} else 
			// If </body> is not found append markup to document's end
			{
				$buffer .= $markup;
			}
			
			$this->app->setBody($buffer);
		}

		// Output log messages if debug is enabled
    	if ($this->params->get('debug', false) && Factory::getUser()->authorise('core.admin'))
    	{
			echo LayoutHelper::render('debug', ['logs' => Helper::$log], JPATH_ADMINISTRATOR . '/components/com_gsd/layouts');
    	}
	}

	/**
	 *  Adds Google Structured Markup to the document in JSON Format
	 *
	 *  @return void
	 */
	private function getMarkup()
	{
		Helper::log($this->app->input->getArray());

		// Get JSON markup for each available type
		$data = [
			$this->getJSONWebsite(),
			$this->getJSONLogo(),
			$this->getJSONSocialProfiles(),
			
			$this->getCustomCode(),
			$this->getJSONBreadcrumbs()
		];

        // Load and trigger plugins
        Helper::event('onGSDBeforeRender', array(&$data));

		// Convert data array to string
		$markup = implode("\n", array_filter($data));

		// Return if markup is empty
		if (!$markup || empty($markup) || is_null($markup))
		{
			return;
		}

		// Minify output
		if ($this->params->get('minifyjson', false))
		{
			$markup = Helper::minify($markup);
		}

		Helper::log($markup);

		return '
			<!-- Start: ' . Text::_("GSD") . ' -->
			' . $markup . '
			<!-- End: ' . Text::_("GSD") . ' -->
		';
	}

	/**
	 *  Route default form's prepare event to onGSDPluginForm to help our plugins manipulate the form
	 *
	 *  @param   Form  $form  The form to be altered.
	 *  @param   mixed  $data  The associated data for the form.
	 *
	 *  @return  boolean
	 */
	public function onContentPrepareForm($form, $data)
	{
		// Run only on backend
		if (!$this->app->isClient('administrator') || !$form instanceof Form)
		{
			return;
		}

		// Load libraries
		if (!$this->setup())
		{
			return;
		}

		Helper::event('onGSDPluginForm', array($form, $data));
	}

	/**
	 * Add Robots Snippet Control
	 * https://webmasters.googleblog.com/2019/09/more-controls-on-search.html
	 *
	 * @return void
	 */
	private function addRobotSnippetControl()
	{
		$robots = Factory::getDocument()->getMetaData('robots');
		
		// Skip, if the existing value contains any of the following text
		if (\NRFramework\Functions::strpos_arr(['noindex', 'nosnippet', 'max-'], $robots))
		{
			return;
		}

		$value = 'max-snippet:-1, max-image-preview:large, max-video-preview:-1';
		$robots = empty($robots) ? $value : $robots . ', ' . $value;

		Factory::getDocument()->setMetaData('robots', $robots);
	}

	

	/**
	 *  Returns Breadcrumbs structured data markup
	 *  https://developers.google.com/structured-data/breadcrumbs
	 *
	 *  @return  string
	 */
	private function getJSONBreadcrumbs()
	{
		if (!$this->params->get('breadcrumbs_enabled', true))
		{
			return;
		}

		$include_home = $this->params->get('include_home', true);
		$home_text    = $this->params->get('breadcrumbs_home', Text::_('GSD_BREADCRUMBS_HOME'));

		// Generate JSON
		return $this->json->setData(array(
			'contentType' => 'breadcrumbs',
			'crumbs'      => Helper::getCrumbs($home_text, $include_home)
		))->generate();
	}

	/**
	 *  Returns Website Schema
	 *
	 *  @return  string on success, boolean on fail
	 */
	private function getJSONWebsite()
	{
		// Only on homepage
		if (!Helper::isFrontPage())
		{
			return;
		}

		$site_links_search = $this->params->get('sitelinks_enabled', false);
		$site_name = (bool) $this->params->get('sitename_enabled', true);

		// If both Site Name and Site Links Search are disabled, return
		if (!$site_links_search && !$site_name)
		{
			return;
		}

		// Get the sitelinks settings
		if ($site_links_search)
		{
			switch ($site_links_search)
			{
				case '1': // com_search
					$site_links_search = Helper::route('index.php?option=com_search&searchphrase=all&searchword={search_term}');
					break;
				case '2': // com_finder
					$site_links_search = Helper::route('index.php?option=com_finder&view=search&q={search_term}');
					break;
				case '3': // custom URL
					$site_links_search = trim($this->params->get('sitelinks_search_custom_url'));
					break;
			}
		}

		// Generate JSON
		return $this->json->setData([
			'contentType' => 'website',
			'site_name_enabled' => $site_name,
			'site_name'         => Helper::getSiteName(),
			'site_name_alt'     => $this->params->get('sitename_name_alt'),
			'site_url'          => Helper::getSiteURL(),
			'site_links_search' => $site_links_search
		])->generate();
	}

	/**
	 *  Returns Site Logo structured data markup
	 *  https://developers.google.com/search/docs/data-types/logo
	 *
	 *  @return  string on success, boolean on fail
	 */
	private function getJSONLogo()
	{
		// Only on homepage
		if (!Helper::isFrontPage())
		{
			return;
		}

		if (!$logo = Helper::getSiteLogo()) 
		{
			return;
		}

		// Generate JSON
		return $this->json->setData(array(
			"contentType" => "logo",
			"url"         => Helper::getSiteURL(),
			"logo"        => Helper::cleanImage($logo)
		))->generate();
	}

	/**
	 *  Returns Social Profiles structured data markup
	 *  https://developers.google.com/search/docs/data-types/social-profile-links
	 *
	 *  @return  string on success, boolean on fail
	 */
	private function getJSONSocialProfiles()
	{
		// Only on homepage
		if (!Helper::isFrontPage())
		{
			return;
		}

		$predefinedURLs = array(
			$this->params->get("socialprofiles_facebook"),
			$this->params->get("socialprofiles_twitter"),
			$this->params->get("socialprofiles_instagram"),
			$this->params->get("socialprofiles_youtube"),
			$this->params->get("socialprofiles_linkedin"),
			$this->params->get("socialprofiles_pinterest"),
			$this->params->get("socialprofiles_soundcloud"),
			$this->params->get("socialprofiles_tumblr")
		);

		$otherURLs = explode("\n", $this->params->get("socialprofiles_other", ''));

		// Merge arrays and remove empty items
		$URLs = array_filter(
			array_merge(
				$predefinedURLs,
				$otherURLs
			)
		);

		// Remove new line and space characters
		$URLs = array_map(function($url)
		{
			return str_replace([' ', "\n", "\t", "\r"], '', $url);
		}, $URLs);

		// Return if array is empty
		if (count($URLs) == 0)
		{
			return;
		}

		// Generate JSON
		return $this->json->setData(array(
			"contentType" => "socialprofiles",
			"type"        => $this->params->get("socialprofiles_type", "Organization"),
			"siteurl"     => Helper::getSiteURL(),
			"sitename"    => Helper::getSiteName(),
			"links"       => $URLs
		))->generate();
	}

	

	/**
	 *  Returns Custom Code
	 *
	 *  @return  string  The Custom Code
	 */
	private function getCustomCode()
	{
		return trim((string) $this->params->get('customcode'));
	}

	/**
	 *  Load required classes and configuration
	 *
	 *  @return  bool 
	 */
	private function setup()
	{
		// Initialize framework
		if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
		{
			return;
		}

		// Make sure the component is installed and enabled.
		if (!\NRFramework\Extension::componentIsEnabled('gsd'))
		{
			return;
		}

        // Initialize extension library
        if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_gsd/autoload.php'))
        {
            return;
		}

		// Load configuration options
		$this->params = Helper::getParams();

		return true;
	}

	/**
	 *  Loads Helper files
	 *
	 *  @return  boolean
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

		// Only on HTML documents
		if (Factory::getDocument()->getType() !== 'html')
		{
			return false;
		}

		// Load libraries
		if (!$this->setup())
		{
			return;
		}

		// Return if current page is an XML page
		if (NRFramework\Functions::isFeed() || $this->app->input->getInt('print', 0))
		{
			return false;
		}

		// Initialize JSON Generator Class
		$this->json = new \GSD\Json();

		return ($this->init = true);
	}
}
