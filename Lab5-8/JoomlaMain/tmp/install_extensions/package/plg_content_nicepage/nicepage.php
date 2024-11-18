<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_nicepage/library/loader.php';

use NP\Factory as NpFactory;
use NP\Utility\Utility;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Class PlgContentNicepage
 */
class PlgContentNicepage extends JPlugin
{
    /**
     * @param object    $context Context
     * @param object    $row     Row
     * @param JRegistry $params  Parameters
     * @param int       $page    Page number
     */
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        if (!$this->isAllowed($context, $row)) {
            return;
        }

        // For np posts and third party theme need include froala styles
        Utility::includeFroalaStyles();

        $page = NpFactory::getPage($row->id, $context, $row, $params);

        if (!$page) {
            return;
        }

        if (isset($row->id) && $row->id !== $page->getPageId()) {
            return;
        }

        $page->prepare();

        return true;
    }

    /**
     * Check allowed
     *
     * @param string $context Component context
     * @param object $row     Component row
     *
     * @return bool
     */
    public function isAllowed($context, $row)
    {
        //Check is admin page
        if (Factory::getApplication()->isClient('administrator')) {
            return false;
        }

        //Check is article page
        if (!isset($row->id)) {
            return false;
        }

        //Check is article or blog context
        if ($context !== 'com_content.article' && $context !== 'com_content.featured' && $context !== 'com_content.category') {
            return false;
        }

        //Check component installed
        if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_nicepage')) {
            return false;
        }
        if (!ComponentHelper::getComponent('com_nicepage', true)->enabled) {
            return false;
        }

        //Check is plugin second call
        if ((property_exists($row, 'doubleСall') && $row->doubleСall)) {
            return false;
        }
        return true;
    }
}
