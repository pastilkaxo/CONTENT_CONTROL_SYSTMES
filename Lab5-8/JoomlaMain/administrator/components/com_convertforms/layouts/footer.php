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

?>
<div class="text-center pt-4">
    
    <div style="padding:15px;">
        <p>
            <?php echo Text::sprintf("NR_USING_THE_FREE_VERSION", Text::_("COM_CONVERTFORMS")) ?>
        </p>
        <?php NRFramework\HTML::renderProButton(); ?>
    </div>
    
    <?php echo Text::_('COM_CONVERTFORMS') . " v" . NRFramework\Functions::getExtensionVersion("com_convertforms", true) ?>
    <br>
    <?php if ($this->config->get("showcopyright", true)) { ?>
        <div class="footer_review">
            <?php echo Text::_("NR_LIKE_THIS_EXTENSION") ?>
            <a href="https://extensions.joomla.org/extensions/extension/contacts-and-feedback/forms/convert-forms/" target="_blank"><?php echo Text::_("NR_LEAVE_A_REVIEW") ?></a> 
            <a href="https://extensions.joomla.org/extensions/extension/contacts-and-feedback/forms/convert-forms/" target="_blank" class="stars"><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span></a>
        </div>
        <Br>
        &copy; <?php echo Text::sprintf('NR_COPYRIGHT', date("Y")) ?></p>
    <?php } ?>
</div>