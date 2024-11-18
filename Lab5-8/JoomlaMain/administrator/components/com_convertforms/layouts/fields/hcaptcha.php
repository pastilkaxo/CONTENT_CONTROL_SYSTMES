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

extract($displayData);

if (!$class->getSiteKey() || !$class->getSecretKey())
{
	echo Text::_('COM_CONVERTFORMS_FIELD_HCAPTCHA') . ': ' . Text::_('COM_CONVERTFORMS_FIELD_RECAPTCHA_KEYS_NOTE');
	return;
}

// Load callback first for browser compatibility
HTMLHelper::_('script', 'com_convertforms/hcaptcha.js', ['version' => 'auto', 'relative' => true]);

// Load hCAPTCHA API JS
HTMLHelper::_('script', 'https://hcaptcha.com/1/api.js?onload=ConvertFormsInitHCaptcha&render=explicit&hl=' . Factory::getLanguage()->getTag());

?>
<div class="h-captcha"
	data-sitekey="<?php echo $class->getSiteKey(); ?>"
	data-theme="<?php echo $field->theme ?>"
	data-size="<?php echo $field->hcaptcha_type == 'invisible' ? $field->hcaptcha_type : $field->size ?>">
</div>