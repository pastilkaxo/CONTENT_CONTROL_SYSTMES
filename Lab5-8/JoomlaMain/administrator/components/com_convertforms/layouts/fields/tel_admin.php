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

use Joomla\CMS\Uri\Uri;

extract($displayData);

$country_selector_enabled = isset($field->enable_country_selector) && $field->enable_country_selector === '1';
if ($country_selector_enabled)
{
	$value_country_code = isset($field->value['code']) ? $field->value['code'] : 'af';
	$css = @file_get_contents(JPATH_ROOT . '/media/plg_system_nrframework/css/controls/phone.css');
	$flag_base_url = implode('/', [rtrim(Uri::root(), '/'), 'media', 'plg_system_nrframework', 'img', 'flags']);
	$selected_country = \NRFramework\Countries::getCountry($value_country_code);
	$country_code = isset($selected_country['code']) ? strtolower($selected_country['code']) : 'af';
	$country_calling_code = isset($selected_country['calling_code']) ? $selected_country['calling_code'] : 93;
	?>
	<style><?php echo $css; ?></style>
	<div class="tf-phone-control<?php echo $field->class ? ' ' . $field->class : ''; ?>"<?php echo $field->readonly === '1' ? ' readonly' : ''; ?>>
		<div class="tf-phone-control--skeleton tf-phone-control--flag" style="padding-left:0;">
			<img width="27" height="13.5" src="<?php echo implode('/', [$flag_base_url, $country_code . '.png']); ?>"></img>
			<svg class="tf-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="17"><path fill="currentColor" d="M480-333 240-573l51-51 189 189 189-189 51 51-240 240Z"/></svg>
			<span class="tf-flag-calling-code">+<?php echo $country_calling_code; ?></span>
		</div>
		<input
			type="tel"
			class="tf-phone-control--number<?php echo !empty($field->inputcssclass) ? ' ' . $field->inputcssclass : ''; ?>"
			<?php echo $field->required === '1' ? ' required' : ''; ?>
			<?php echo $field->readonly === '1' ? ' readonly' : ''; ?>
			placeholder="<?php echo !empty($field->placeholder) ? $field->placeholder : '_ _ _ _ _ _'; ?>"
			value="<?php echo isset($field->value['value']) ? $field->value['value'] : ''; ?>"
		/>
	</div>
	<?php
}
else
{
	echo $class->getInput();
}