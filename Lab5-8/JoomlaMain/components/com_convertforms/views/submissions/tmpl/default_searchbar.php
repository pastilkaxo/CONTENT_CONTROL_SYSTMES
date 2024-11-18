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

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$query = $this->state->get('filter.search');
?>
<form method="get" class="convertforms-submissions-search-bar">
    <input type="text" name="q" value="<?php echo $query; ?>" placeholder="<?php echo Text::_('COM_CONVERTFORMS_SEARCH_HINT'); ?>" />
    <?php if ($query): ?>
        <a href="#" onclick="cfSearchBarResetAndSubmitForm(this); return false;"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M256-213.847 213.847-256l224-224-224-224L256-746.153l224 224 224-224L746.153-704l-224 224 224 224L704-213.847l-224-224-224 224Z" fill="currentColor" /></svg></a>
    <?php else: ?>
        <button><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M781.692-136.924 530.461-388.155q-30 24.769-69 38.769t-80.692 14q-102.55 0-173.582-71.014t-71.032-173.537q0-102.524 71.014-173.601 71.014-71.076 173.538-71.076 102.523 0 173.6 71.032T625.384-580q0 42.846-14.385 81.846-14.385 39-38.385 67.846l251.231 251.231-42.153 42.153Zm-400.923-258.46q77.308 0 130.962-53.654Q565.385-502.692 565.385-580q0-77.308-53.654-130.962-53.654-53.654-130.962-53.654-77.308 0-130.962 53.654Q196.154-657.308 196.154-580q0 77.308 53.653 130.962 53.654 53.654 130.962 53.654Z" fill="currentColor" /></svg></button>
    <?php endif; ?>
</form>
<?php if ($query): ?>
    <div class="convertforms-submissions-search-bar-info">
        <?php echo '<p>' . Text::sprintf('COM_CONVERTFORMS_SEARCH_RESULTS_FOR', $this->total_submissions, $query) . '</p>'; ?>
    </div>
<?php endif; ?>
<script>
    function cfSearchBarResetAndSubmitForm(input) {
        input.closest('form').querySelector('input[type=text]').value='';
        input.closest('form').submit();
    }
</script>