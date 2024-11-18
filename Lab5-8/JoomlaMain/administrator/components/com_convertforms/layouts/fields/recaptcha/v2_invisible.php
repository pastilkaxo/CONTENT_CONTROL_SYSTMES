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
use Joomla\CMS\Factory;

extract($displayData);

HTMLHelper::_('script', 'com_convertforms/recaptcha_v2_invisible.js', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('script', 'https://www.google.com/recaptcha/api.js?onload=ConvertFormsInitInvisibleReCaptcha&render=explicit&hl=' . Factory::getLanguage()->getTag());
?>
<div class="g-invisible-recaptcha" data-sitekey="<?php echo $site_key; ?>" data-badge="<?php echo $badge; ?>"></div>