<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_nicepage/library/loader.php';

use Joomla\CMS\Factory;
use NP\Factory as NpFactory;
/**
 * Class PlgContentNicepage
 */
class PlgSystemNicepage extends JPlugin
{
    /**
     * Process component content
     */

    private $_page = null;

    /**
     * After dispatch event
     */
    public function onAfterDispatch()
    {
        $app = Factory::getApplication();

        if ($app->isClient('administrator') || ($app->get('offline') && !Factory::getUser()->authorise('core.login.offline'))) {
            return;
        }

        $buf = Factory::getDocument()->getBuffer('component');
        if (preg_match('/<\!--np\_(content|landing)-->/', $buf, $matches)) {
            if ($matches[1] === 'landing') {
                $app->set('theme', 'landing');
                $app->set('themes.base', JPATH_ADMINISTRATOR . '/components/com_nicepage/views');
                $app->set('themeFile', 'landing.php');
            }
            if (preg_match('/<\!--np\_page_id-->([\s\S]+?)<\!--\/np\_page_id-->/', $buf, $matches2)) {
                $this->_page = NpFactory::getPage($matches2[1]);
                if ($this->_page) {
                    $this->_page->buildPageElements();
                }
            }
        }
    }

    /**
     *  Proccess page content after rendering
     */
    public function onAfterRender()
    {
        $app = Factory::getApplication();
        $pageContent = $app->getBody();

        // Move dataBridge object initialization at top head tag in admin panel
        if ($app->isClient('administrator') && $this->moveDataBridgeToTop($pageContent)) {
            return;
        }

        if ($app->isClient('administrator') || ($app->get('offline') && !Factory::getUser()->authorise('core.login.offline'))) {
            return;
        }

        //Append page elements to content
        if ($this->_page) {
            $pageContent = $this->_page->get($pageContent);
        }
        // Apply np settings to page
        $config = NpFactory::getConfig();
        $pageContent = $config->applySiteSettings($pageContent);

        //Add id attribute for typography parser
        if ($app->input->get('toEdit', '0') === '1') {
            $pageContent = preg_replace('/class="(item-page|u-page-root)/', ' id="np-test-container" class="$1', $pageContent);
        }

        $app->setBody($pageContent);
    }

    /**
     * Move data bridge to top head
     *
     * @param string $pageContent Page content
     *
     * @return bool
     */
    public function moveDataBridgeToTop($pageContent) {
        if (preg_match('/<\!--np\_databridge_script-->([\s\S]+?)<\!--\/np\_databridge_script-->/', $pageContent, $adminScriptsMatches)) {
            $adminPageScripts = $adminScriptsMatches[1];
            $pageContent = str_replace($adminScriptsMatches[0], '', $pageContent);
            $pageContent = preg_replace('/(<head>)/', '$1[[dataBridgeScript]]', $pageContent, 1);
            $pageContent = str_replace('[[dataBridgeScript]]', $adminPageScripts, $pageContent);
            Factory::getApplication()->setBody($pageContent);
            return true;
        }
        return false;
    }
}
