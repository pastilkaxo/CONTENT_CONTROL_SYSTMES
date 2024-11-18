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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

if ($this->download_link)
{
    Factory::getDocument()->addScriptDeclaration('
        document.addEventListener("DOMContentLoaded", function() {
            window.location.href = "' . $this->download_link . '";
        });
    ');
}
?>
<div class="export_tool completed text-center tmpl-<?php echo $this->tmpl ?>">
    <div class="container">
        <span class="icon-checkmark-2"></span>
        <h2>
            <?php echo Text::sprintf('COM_CONVERTFORMS_EXPORT_COMPLETED', number_format($this->total_submissions_exported)) ?>
        </h2>
        <p>
            <?php echo Text::_('COM_CONVERTFORMS_DOWNLOAD_WILL_START') ?>
        </p>
        <a class="btn btn-primary" href="<?php echo $this->start_over_link ?>">
            <?php echo Text::_('NR_START_OVER') ?>
        </a>
    </div>
</div>