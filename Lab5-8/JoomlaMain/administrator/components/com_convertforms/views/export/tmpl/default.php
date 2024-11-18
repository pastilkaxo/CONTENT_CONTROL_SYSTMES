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
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

Factory::getDocument()->addScriptDeclaration('
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.querySelector(".export_tool form");
        form.addEventListener("submit", function(e) {
            var btn = form.querySelector("button[type=\'submit\']");
            btn.innerText = "' . Text::_('NR_PLEASE_WAIT') . '...";
            document.querySelector(".export_tool").classList.add("working");
        });

        // Joomla\'s showOn attribute doesn\'t support showing/hiding a field when another field is empty.
        let formIDInput = document.getElementById("filter_search");
        formIDInput.addEventListener("input", showHideFields);
        showHideFields();

        function showHideFields() {
            let state = formIDInput.value.startsWith("id:") == "" ? "block" : "none";
            document.getElementById("filter_state").closest(".control-group").style.display = state;
            document.getElementById("filter_period").closest(".control-group").style.display = state;
        }
    });
');

?>

<div class="export_tool form tmpl-<?php echo $this->tmpl ?>">
    <div class="container">
        <h1><?php echo Text::_('COM_CONVERTFORMS_LEADS_EXPORT') ?></h1>
        <form method="post" action="<?php echo Route::_('index.php') ?>" name="adminForm" id="adminForm" >
            <?php echo $this->form->renderFieldset('submission'); ?>
            <button class="btn btn-primary" type="submit">
                <?php echo Text::_('COM_CONVERTFORMS_LEADS_EXPORT') ?>
            </button>
            <input type="hidden" name="option" value="com_convertforms"/>
            <input type="hidden" name="task" value="export.export"/>
            <input type="hidden" name="tmpl" value="<?php echo $this->tmpl ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>