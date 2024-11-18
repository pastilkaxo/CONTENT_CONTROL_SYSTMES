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

$size = $field->size === 'normal' ? '' : '_' . $field->size;

$imageURL = Uri::root() . 'media/com_convertforms/img/recaptcha_' . $field->theme . $size . '.png';
?>
<img src="<?php echo $imageURL ?>" style="align-self: flex-start;" />