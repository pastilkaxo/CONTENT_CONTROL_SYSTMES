

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
use Joomla\CMS\Router\Route;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('bootstrap.popover');

extract($displayData);

?>

<table class="table">
    <tbody>
        <?php foreach ($items as $key => $item) { ?>
            <tr data-pk="<?php echo $item->id ?>">
                <td width="1%"><span class="icon-<?php echo $item->state == 1 ? 'publish' : 'unpublish' ?>"></span></td>
                <td><?php echo $item->title; ?></td>
                <td width="5%" class="nowrap"><?php echo Text::_('GSD_' . $item->contenttype); ?></td>
                <td width="1%" class="gsdID nowrap">#<?php echo $item->id ?></td>
                <td width="15%" class="gsd-btn-toolbar nowrap text-right">
                    <a href="#gsdModal"
                        data-src="<?php echo Route::_('index.php?option=com_gsd&view=item&tmpl=component&layout=modal&id='. $item->id) ?>" 
                        class="btn btn-secondary"
                        data-bs-toggle="modal"
                        data-toggle="modal"
                        title="<?php echo Text::_('GSD_EDIT_SNIPPET'); ?>">
                        <span class="icon-edit"></span>
                    </a>
                    <a href="#" class="btn  btn-secondary gsdRemove" title="<?php echo Text::_('GSD_DELETE_SNIPPET'); ?>">
                        <span class="icon-trash"></span>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>