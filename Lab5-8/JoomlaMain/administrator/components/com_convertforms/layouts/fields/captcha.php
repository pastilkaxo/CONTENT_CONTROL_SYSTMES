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

extract($displayData);
?>
<div class="captcha-container">
	<div class="captcha-equation">
		<?php echo $field->question['number1']; ?>
		<?php echo $field->question['comparator']; ?>
		<?php echo $field->question['number2']; ?> =
	</div>
	<?php // We do not use $this->input_name here in order to prevent the field to be included in the submitted data ?>
	<input type="text" name="<?php echo $field->name; ?>" id="<?php echo $field->input_id; ?>"
		required
		autocomplete="off"
		placeholder="<?php echo isset($field->placeholder) ? $field->placeholder : '' ?>"
		class="<?php echo $field->class ?>"
	/>
	<input type="hidden" name="<?php echo md5($field->key) ?>"
		value="<?php echo $field->question['solution']; ?>"
	/>
</div>