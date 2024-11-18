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

$css = @file_get_contents(JPATH_ROOT . '/media/plg_system_nrframework/css/widgets/slider.css');

echo '
	<style>
		' . $css . '
		.nrf-slider-wrapper.' . $field->input_id . ' {
			--input-bg-color: ' . $field->form['params']->get('inputbg') . ';
			--input-border-color: ' . $field->form['params']->get('inputbordercolor') . ';
		}
	</style>
';

$atts = [
	'min'  => $field->min,
	'max'  => $field->max,
	'step' => $field->step,
];

echo $class->toWidget($atts);