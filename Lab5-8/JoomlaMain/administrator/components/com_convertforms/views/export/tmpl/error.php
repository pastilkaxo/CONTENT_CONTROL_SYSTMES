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

use Joomla\CMS\Language\Text;
?>
<div class="export_tool error text-center tmpl-<?php echo $this->tmpl ?>">
    <div class="container">
        <span class="icon-smiley-sad-2"></span>
        <h2><?php echo Text::_('NR_ERROR') ?></h2>
        <p class="error_message"><?php echo $this->error; ?></p>
        <a class="btn btn-primary" href="<?php echo $this->start_over_link ?>">
            <?php echo Text::_('NR_TRY_AGAIN') ?>
        </a>
    </div>
</div>