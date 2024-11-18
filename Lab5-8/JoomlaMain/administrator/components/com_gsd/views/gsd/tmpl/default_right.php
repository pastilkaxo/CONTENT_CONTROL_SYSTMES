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

defined('_JEXEC') or die('Restricted Access');

use Joomla\CMS\Language\Text;
use NRFramework\Functions;

$installedVersion = Functions::getExtensionVersion("plg_system_gsd", false);
$isPro = Functions::extensionHasProInstalled("plg_system_gsd");
$FreePro = $isPro ? "Pro" : "Free";
?>
<div class="mod mod-version-check">
    <div class="mod-head"><?php echo Text::_('GSD') . " " . $FreePro ?></div>
    <div class="mod-content">
        <p>
            <?php echo Text::_('GSD_INSTALLED_VERSION') ?>: <?php echo $installedVersion; ?>
        </p>
        
        <?php NRFramework\HTML::renderProButton(); ?>
        
    </div>
</div>

<div class="mod">
    <div class="mod-head">
        <span class="icon-star"></span>
        <?php echo Text::_("NR_LIKE_THIS_EXTENSION") ?>
    </div>
    <div class="mod-content">
        <p>
            <?php echo Text::_("GSD_WRITE_REVIEW_ON_JED") ?>
            <a href="https://extensions.joomla.org/extensions/extension/search-a-indexing/web-search/google-structured-data/" target="_blank"><?php echo Text::_("NR_LEAVE_A_REVIEW") ?></a>
        </p>
    </div>
</div>

<div class="mod">
    <div class="mod-head">
        <span class="icon-heart"></span>
        <?php echo Text::_("GSD_FOLLOW_US") ?>
    </div>
    <div class="mod-content">
        <ul class="socialNav">
            <li><a target="_blank" href="https://www.facebook.com/wwwtassosgr/"><?php echo Text::_("GSD_LIKE_FACEBOOK") ?></a></li>
            <li><a target="_blank" href="https://twitter.com/tassosm"><?php echo Text::_("GSD_FOLLOW_TWITTER") ?></a></li>
            <li><a target="_blank" href="https://plus.google.com/u/0/+TassosMarinos85"><?php echo Text::_("GSD_FOLLOW_GOOGLE_PLUS") ?></a></li>
        </ul>
    </div>
</div>

<div class="mod copy">
    &copy; <?php echo Text::sprintf('NR_COPYRIGHT', date("Y")) ?></p>
</div>