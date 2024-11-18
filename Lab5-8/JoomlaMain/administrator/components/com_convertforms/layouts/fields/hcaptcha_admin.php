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

// no hCaptcha image is present in invisible-mode
if ($field->hcaptcha_type == 'invisible')
{
	return;
}

$suffix = $field->size == 'compact' ? '_compact' : '';

$imageURL = Uri::root() . 'media/com_convertforms/img/hcaptcha_' . $field->theme . $suffix . '.png';
?>
<img src="<?php echo $imageURL ?>" style="align-self: flex-start;" />