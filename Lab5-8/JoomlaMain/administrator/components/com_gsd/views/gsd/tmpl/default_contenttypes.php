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

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

$url = Uri::base() . 'index.php?option=com_gsd&view=items';

?>

<div class="nr-box-title">
    <a href="<?php echo $url ?>"><?php echo Text::_('GSD_ITEMS_OVERVIEW'); ?></a>
    <div><?php echo Text::_('GSD_ITEMS_OVERVIEW_DESC'); ?></div>
</div>
<div class="nr-box-content" style="max-height:300px; overflow:auto;">
    <table class="nr-app-stats">
        <?php foreach ($this->stats['items'] as $key => $item) { ?>
        <tr>
            <td>
                <?php echo Text::_('GSD_' . $key); ?>
                <div class="bar"><span style="width:<?php echo $item['share']; ?>%"></span></div>
            </td>
            <td width="12%" class="text-center"><?php echo $item['count']; ?></td>
            <td width="12%" class="text-center"><?php echo $item['share']; ?>%</td>
        </tr>
        <?php } ?>
    </table>
</div>
