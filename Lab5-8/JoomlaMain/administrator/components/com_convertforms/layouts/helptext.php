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
?>
<?php if (isset($field->description) && !empty($field->description)) { ?>
	<div class="cf-control-input-desc">
		<?php echo $field->description; ?>
	</div>
<?php } ?>