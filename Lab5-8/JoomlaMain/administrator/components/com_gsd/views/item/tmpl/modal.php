<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

// Dropdown at the bottom of the page breaks layout. https://github.com/harvesthq/chosen/issues/155
// Here's a quick and dirty fix but we need a better way to calculate the remaining height and decide if
// it's going to be dropped down or up.
Factory::getDocument()->addStyleDeclaration('
    .nr-main-content {
        padding-bottom: 245px;
    }
');

?>

<div class="container-popup">
    <?php $this->setLayout('edit'); ?>
    <?php echo $this->loadTemplate(); ?>
</div>

<div class="hidden">
    <button id="applyBtn" type="button" onclick="Joomla.submitbutton('item.apply');"></button>
    <button id="saveBtn" type="button" onclick="Joomla.submitbutton('item.save');"></button>
    <button id="closeBtn" type="button" onclick="Joomla.submitbutton('item.cancel');"></button>
</div>