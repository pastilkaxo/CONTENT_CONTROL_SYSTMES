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

extract($displayData);

?>
<div class="nrf-widget nrf-rating-wrapper <?php echo $css_class; ?>">
	<?php
	for ($i = 1; $i <= $max_rating; $i++)
	{
		$label_class = '';
		$rating_id = $id . '_' . $i;
		$label_title = $i . ' ' . sprintf(Text::_('NR_STAR'), ($i > 1 ? 's' : ''));
		
		if ($value && $i <= $value)
		{
			$label_class = 'iconFilled';
		}
		?>
		<input type="radio" aria-hidden="true" class="<?php echo $input_class; ?>" id="<?php echo $rating_id; ?>" name="<?php echo $name; ?>"
			value="<?php echo $i; ?>"
			<?php if ($value && $i == $value): ?>
			checked
			<?php endif; ?>
			<?php if ($readonly || $disabled): ?>
			disabled
			<?php endif; ?>
		/>
		<label aria-label="<?php echo $label_title; ?>" for="<?php echo $rating_id; ?>" class="<?php echo $label_class; ?>" title="<?php echo $label_title; ?>">
			<svg class="svg-item">
				<use xlink:href="<?php echo $icon_url; ?>#nrf-ratings-<?php echo $icon; ?>" />
			</svg>
		</label>
		<?php
	}
	?>
</div>