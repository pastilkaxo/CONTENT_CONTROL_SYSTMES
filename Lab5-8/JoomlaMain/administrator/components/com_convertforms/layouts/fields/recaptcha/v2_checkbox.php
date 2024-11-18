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
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

Text::script('COM_CONVERTFORMS_RECAPTCHA_NOT_LOADED');
HTMLHelper::_('script', 'plg_captcha_recaptcha/recaptcha.min.js', ['version' => 'auto', 'relative' => true]);

$callback = defined('nrJ4') ? 'init' : 'Init'; // Why the hell did you guys rename the method?
HTMLHelper::_('script', 'https://www.google.com/recaptcha/api.js?onload=Joomla' . $callback . 'ReCaptcha2&render=explicit&hl=' . Factory::getLanguage()->getTag());

HTMLHelper::_('script', 'com_convertforms/recaptcha_v2_checkbox.js', ['version' => 'auto', 'relative' => true]);

?>

<div class="nr-recaptcha g-recaptcha"
	data-sitekey="<?php echo $site_key; ?>"
	data-theme="<?php echo $theme; ?>"
	data-size="<?php echo $size; ?>">
</div>