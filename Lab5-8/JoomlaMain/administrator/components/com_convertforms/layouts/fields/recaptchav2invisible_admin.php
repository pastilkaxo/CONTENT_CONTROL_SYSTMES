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

$imageURL = Uri::root() . 'media/com_convertforms/img/recaptcha_invisible.png';
?>
<?php if ($field->badge != 'inline') { ?>
    <div class="badge_<?php echo $field->badge ?>"></div>
    <style>
        .badge_bottomleft, .badge_bottomright {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 70px;
            height: 60px;
            overflow: hidden;
            background-image:url("<?php echo $imageURL ?>");
            border:solid 1px #ccc;
        }
        .badge_bottomright {
            left:auto;
            right:0;
        }
    </style>
<?php } else { ?>
    <img src="<?php echo $imageURL ?>" style="align-self: flex-start;" />
<?php } ?>