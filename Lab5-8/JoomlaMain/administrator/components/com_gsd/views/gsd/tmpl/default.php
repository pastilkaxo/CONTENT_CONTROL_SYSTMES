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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// Load media files
if (!defined('nrj4'))
{
    HTMLHelper::_('jquery.framework');
    HTMLHelper::script('com_gsd/script.js', ['relative' => true, 'version' => 'auto']);
}

HTMLHelper::stylesheet('com_gsd/styles.css', ['relative' => true, 'version' => 'auto']);
?>
<div class="nr-app <?php echo defined('nrJ4') ? 'j4' : '' ?>">
    <div class="nr-row">
        <?php echo !defined('nrJ4') ? $this->sidebar : '' ?>
        <div class="nr-main-container">
            <?php
                // Display extension notices
                \NRFramework\Notices\Notices::getInstance([
                    'ext_element' => 'gsd',
                    'ext_type' => 'plugin',
                    'ext_xml' => 'plg_system_gsd'
                ])->show();
            ?>
            <div class="nr-main-header">
                <h2><?php echo Text::_('NR_DASHBOARD'); ?></h2>
                <p><?php echo Text::_('GSD_DASHBOARD_DESC'); ?></p>
            </div>
            <div class="nr-main-content">
                <div class="tile is-ancestor">
                    <div class="tile is-vertical">
                        <div class="tile">
                            <div class="tile is-parent">
                                <div class="tile is-child">
                                    <div class="nr-box nr-box-hr">
                                        <div class="nr-box-title">
                                            <a href="<?php echo Uri::base() ?>index.php?option=com_gsd&view=items">
                                                <?php echo Text::_('GSD_TOTAL_ITEMS'); ?>
                                            </a>
                                            <div><?php echo Text::_('GSD_TOTAL_ACTIVE_ITEMS'); ?></div>
                                        </div>
                                        <div class="nr-box-content text-right text-end">
                                            <span class="nr-number"><?php echo $this->stats['itemsCount']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tile is-parent">
                                <div class="tile is-child">
                                    <div class="nr-box nr-box-hr">
                                        <div class="nr-box-title">
                                            <a href="<?php echo Uri::base() ?>index.php?option=com_gsd&view=config&layout=edit#globaldata">
                                                <?php echo Text::_('GSD_GLOBAL_DATA'); ?>
                                            </a>
                                            <div><?php echo Text::_('GSD_GLOBAL_DATA_SUBHEADING'); ?></div>
                                        </div>
                                        <div class="nr-box-content text-right text-end">
                                            <span class="nr-number">
                                                <?php echo $this->stats['siteDataEnabled'] ?>/<?php echo count($this->stats['siteData']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tile is-parent">
                                <div class="tile is-child">
                                    <div class="nr-box nr-box-hr">
                                        <div class="nr-box-title">
                                            <a href="<?php echo Uri::base() ?>index.php?option=com_gsd&view=config&layout=edit#integrations">
                                                <?php echo Text::_('GSD_INTEGRATIONS'); ?>
                                            </a>
                                            <div><?php echo Text::_('GSD_TOTAL_ACTIVE_INTEGRATIONS'); ?></div>
                                        </div>
                                        <div class="nr-box-content text-right text-end">
                                            <span class="nr-number">
                                                 <?php echo $this->stats['integrationsEnabled'] ?>/<?php echo count($this->stats['integrations']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tile">
                            <div class="tile is-parent">
                                <div class="tile is-child nr-box">
                                    <?php echo $this->loadTemplate('contenttypes'); ?>
                                </div>
                            </div>
                            <div class="tile is-parent">
                                <div class="tile is-child nr-box">
                                    <?php echo $this->loadTemplate('sitedata'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tile">
                            <div class="tile is-parent">
                                <div class="tile is-child">
                                    <?php echo $this->loadTemplate('tester'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tile is-3 is-parent">
                        <div class="tile is-child nr-box">
                            <?php echo $this->loadTemplate('right'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>