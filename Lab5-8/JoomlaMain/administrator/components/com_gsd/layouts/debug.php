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

?>

<div style="background-color:#fff; padding:30px; border:solid 1px #ccc; margin:20px; position:relative; grid-column: full-start/full-end; z-index:9999999999;">
    <div style="font-size:20px; font-weight:bold; margin-bottom:10px;">Google Structured Data Runtime Logs</div>
    <div>This log can help troubleshoot issues with your structured data. It's visible to Super Users only.</div>

    <div style="margin:20px 0;">
        <pre>
            <?php 
                highlight_string("<?php\n\$data =\n" . var_export($displayData, true) . ";\n?>");
            ?>
        </pre>
    </div>

    <div>To disable runtime logs, go to Components » Google Structured Data » Configuration » Advancecd and turn the <b>Debug</b> option off.</div>
</div>
