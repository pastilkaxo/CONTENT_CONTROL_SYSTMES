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
use Joomla\CMS\Factory;

extract($displayData);

$buttonContainerClasses = [
	'cf-text-' . $field->align,
];

$buttonClasses = [
    $field->size,
    isset($field->inputcssclass) ? $field->inputcssclass : null
];

$buttonVars = [
    'button-border-radius' => (int) $field->borderradius . 'px',
    'button-padding' => (int) $field->vpadding . 'px ' . (int) $field->hpadding . 'px',
    'button-color' => $field->textcolor,
    'button-font-size' =>  (int) $field->fontsize . 'px',
    'button-background-color' => $field->bg
];

$buttonVars = ConvertForms\FieldsHelper::cssVarsToString($buttonVars, '#cf_' . $form['id'] . ' .cf-control-group[data-key="' . $field->key . '"]');

?>

<div class="<?php echo implode(' ', $buttonContainerClasses) ?>">
    <button type="submit" class="cf-btn <?php echo implode(' ', $buttonClasses) ?>"">
        <span class="cf-btn-text"><?php echo Text::_($field->text) ?></span>
        <span class="cf-spinner-container">
            <span class="cf-spinner">
                <span class="bounce1" role="presentation"></span>
                <span class="bounce2" role="presentation"></span>
                <span class="bounce3" role="presentation"></span>
            </span>
        </span>
    </button>
</div>

<?php 
    if (Factory::getApplication()->isClient('site'))
    {
        ConvertForms\Helper::addStyleDeclarationOnce($buttonVars);
    } else 
    {
        // On backend add styles inline 
        echo '<style>' . $buttonVars . '</style>';
    }
?>