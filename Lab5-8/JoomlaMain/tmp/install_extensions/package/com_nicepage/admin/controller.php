<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
/**
 * Class NicepageController
 */
class NicepageController extends BaseController
{
    /**
     * NicepageController constructor.
     *
     * @param array $config Config for controller
     */
    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->default_view = 'display';
    }
}