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

$theme = $field->theme === 'auto' ? 'light' : $field->theme;

$file_name = $theme . '-' . $field->size . '.png';

$imageURL = Uri::root() . 'media/com_convertforms/img/turnstile/' . $file_name;
?>
<img src="<?php echo $imageURL ?>" alt="Cloudflare Turnstile Demo" style="align-self: flex-start;" />