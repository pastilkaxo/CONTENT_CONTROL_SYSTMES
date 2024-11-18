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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

if (!defined('nrJ4'))
{
    HTMLHelper::_('formbehavior.chosen', 'select:not(.noChosen)');
}
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'conversion.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
        {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    }
</script>

<div class="form-horizontal">
    <form action="<?php echo Route::_('index.php?option=com_convertforms&view=conversion&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">
        <div class="<?php echo defined('nrJ4') ? 'row' : 'row-fluid' ?>">
            <span class="span6 col-md-7">
                <h3><?php echo Text::_('COM_CONVERTFORMS_LEAD_USER_SUBMITTED_DATA') ?></h3>
                <?php echo $this->form->renderFieldset('params') ?>
            </span>
            <span class="span6 col-md-5">
                <h3><?php echo Text::_('COM_CONVERTFORMS_LEAD_INFO') ?></h3>
                <?php echo $this->form->renderFieldset('main') ?>
            </span>
        </div>
        <?php echo HTMLHelper::_('form.token'); ?>
        <input type="hidden" name="task" value="conversion.edit" />
    </form>
</div>


