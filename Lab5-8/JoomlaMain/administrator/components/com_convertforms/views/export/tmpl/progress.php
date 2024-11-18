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
<div class="export_tool inprogress text-center tmpl-<?php echo $this->tmpl ?>">
    <div class="container">
        <span class="icon-health"></span>
        <h2>
            <?php echo Text::_('COM_CONVERTFORMS_EXPORT_WORKING') ?>
        </h2>
        <p>
            <?php echo Text::sprintf('COM_CONVERTFORMS_EXPORT_PROCESSING', number_format($this->processed), number_format($this->total)); ?>
        </p>
        <a href="<?php echo $this->start_over_link ?>" onclick="return confirm('<?php echo Text::_('NR_ARE_YOU_SURE') ?>')">
            <?php echo Text::_('NR_CANCEL'); ?>
        </a>
    </div>
</div>