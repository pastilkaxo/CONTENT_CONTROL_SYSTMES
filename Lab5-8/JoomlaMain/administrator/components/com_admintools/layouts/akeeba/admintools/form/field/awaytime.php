<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * The original file bears the following copyright notice:
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 * @var   array    $inputType       Options available for this field.
 * @var   string   $accept          File types that are accepted.
 * @var   string   $dataAttribute   Miscellaneous data attributes preprocessed for HTML output
 * @var   array    $dataAttributes  Miscellaneous data attribute for eg, data-*.
 * @var   string   $dirname         The directory name
 * @var   string   $addonBefore     The text to use in a bootstrap input group prepend
 * @var   string   $addonAfter      The text to use in a bootstrap input group append
 */

$list = '';

if ($options)
{
	$list = 'list="' . $id . '_datalist"';
}

$attributes = array(
	!empty($class) ? 'class="form-control ' . $class . '"' : 'class="form-control"',
	!empty($size) ? 'size="' . $size . '"' : '',
	!empty($description) ? 'aria-describedby="' . $name . '-desc"' : '',
	$disabled ? 'disabled' : '',
	$readonly ? 'readonly' : '',
	$dataAttribute,
	$list,
	strlen($hint) ? 'placeholder="' . htmlspecialchars($hint, ENT_COMPAT, 'UTF-8') . '"' : '',
	$onchange ? ' onchange="' . $onchange . '"' : '',
	!empty($maxLength) ? $maxLength : '',
	$required ? 'required' : '',
	!empty($autocomplete) ? 'autocomplete="' . $autocomplete . '"' : '',
	$autofocus ? ' autofocus' : '',
	$spellcheck ? '' : 'spellcheck="false"',
	!empty($pattern) ? 'pattern="' . $pattern . '"' : '',

	// @TODO add a proper string here!!!
	!empty($validationtext) ? 'data-validation-text="' . $validationtext . '"' : '',
);

$showTimezoneText = false;

if ($addonBefore === "__TIMEZONE__")
{
	$showTimezoneText = true;
	$addonBefore = '';
}

if ($addonAfter === '__TIMEZONE__')
{
	$serverTZName = Factory::getApplication()->get('offset', 'UTC');

	try
	{
		$timezone = new DateTimeZone($serverTZName);
	}
	catch (Exception $e)
	{
		$timezone = new DateTimeZone('UTC');
	}

	$date = clone Factory::getDate();
	$date->setTimezone($timezone);
	$addonAfter = $date->format('T', true);
}

$addonBeforeHtml = '<span class="input-group-text">' . Text::_($addonBefore) . '</span>';
$addonAfterHtml  = '<span class="input-group-text">' . Text::_($addonAfter) . '</span>';
?>

<?php if (!empty($addonBefore) || !empty($addonAfter)) : ?>
<div class="input-group">
<?php endif; ?>

	<?php if (!empty($addonBefore)) : ?>
		<?php echo $addonBeforeHtml; ?>
	<?php endif; ?>

	<input
		type="<?= ($inputmode ?? '') ?: 'time' ?>"
		name="<?php echo $name; ?>"
		id="<?php echo $id; ?>"
		value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>"
		<?php echo $dirname; ?>
		<?php echo implode(' ', $attributes); ?>>

	<?php if (!empty($addonAfter)) : ?>
		<?php echo $addonAfterHtml; ?>
	<?php endif; ?>

<?php if (!empty($addonBefore) || !empty($addonAfter)) : ?>
</div>
<?php endif; ?>

<?php if ($showTimezoneText): ?>
	<div class="alert alert-info">
		<h3 class="alert-heading">
			<?= Text::sprintf('COM_ADMINTOOLS_CONFIGUREWAF_LBL_AWAYSCHEDULE_TIMEZONE_HEAD', $date->format('H:i T', true), $serverTZName); ?>
		</h3>
		<p>
			<?= Text::sprintf('COM_ADMINTOOLS_CONFIGUREWAF_LBL_AWAYSCHEDULE_TIMEZONE', $serverTZName); ?>
		</p>
	</div>
<?php endif; ?>

<?php if ($options) : ?>
	<datalist id="<?php echo $id; ?>_datalist">
		<?php foreach ($options as $option) : ?>
			<?php if (!$option->value) : ?>
			<?php continue; ?>
			<?php endif; ?>
			<option value="<?php echo $option->value; ?>"><?php echo $option->text; ?></option>
		<?php endforeach; ?>
	</datalist>
<?php endif; ?>
