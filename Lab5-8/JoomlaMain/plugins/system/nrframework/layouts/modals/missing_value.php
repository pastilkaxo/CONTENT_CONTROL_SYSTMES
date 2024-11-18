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

use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

HTMLHelper::stylesheet('plg_system_nrframework/modals/missing_value.css', ['relative' => true, 'version' => 'auto']);
?>
<div class="tf-missing-setting-value-modal">
    <svg width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="70" height="70" rx="35" fill="#FFEEEE"/>
        <mask id="mask0_445_958" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="10" y="10" width="50" height="50">
            <rect x="10" y="10" width="50" height="50" fill="#D9D9D9"/>
        </mask>
        <g mask="url(#mask0_445_958)">
            <path d="M12.0831 53.7495L34.9997 14.1661L57.9164 53.7495H12.0831ZM19.2706 49.5828H50.7289L34.9997 22.4995L19.2706 49.5828ZM34.9997 47.4995C35.59 47.4995 36.0848 47.2998 36.4841 46.9005C36.8834 46.5012 37.0831 46.0064 37.0831 45.4161C37.0831 44.8259 36.8834 44.3311 36.4841 43.9318C36.0848 43.5324 35.59 43.3328 34.9997 43.3328C34.4095 43.3328 33.9147 43.5324 33.5154 43.9318C33.1161 44.3311 32.9164 44.8259 32.9164 45.4161C32.9164 46.0064 33.1161 46.5012 33.5154 46.9005C33.9147 47.2998 34.4095 47.4995 34.9997 47.4995ZM32.9164 41.2495H37.0831V30.8328H32.9164V41.2495Z" fill="#DE1C1C"/>
        </g>
    </svg>

    <p><?php echo $description; ?></p>

    <a href="<?php echo $link; ?>" target="_blank"><?php echo $link_text; ?></a>
</div>