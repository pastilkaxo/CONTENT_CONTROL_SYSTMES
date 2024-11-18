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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

extract($displayData);

if (!$class->getSiteKey() || !$class->getSecretKey())
{
	echo Text::_('COM_CONVERTFORMS_FIELD_TURNSTILE') . ': ' . Text::_('COM_CONVERTFORMS_FIELD_RECAPTCHA_KEYS_NOTE');
	return;
}

$lang = explode('-', Factory::getLanguage()->getTag())[0];

HTMLHelper::_('script', 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=ConvertFormsInitCloudflareTurnstile', ['version' => 'auto', 'relative' => true], ['async' => true, 'defer' => true]);
HTMLHelper::_('script', 'com_convertforms/turnstile.js', ['version' => 'auto', 'relative' => true], ['defer' => true]);
?>
<div class="cf-turnstile" data-sitekey="<?php echo $class->getSiteKey(); ?>" data-language="<?php echo $lang; ?>" data-theme="<?php echo $field->theme; ?>" data-size="<?php echo $field->size; ?>"></div>