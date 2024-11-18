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

use Joomla\CMS\Button\PublishedButton;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('bootstrap.popover');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$user = Factory::getUser();
?>
<form action="<?php echo Route::_('index.php?option=com_convertforms&view=campaigns'); ?>" class="clearfix" method="post" name="adminForm" id="adminForm">
    <?php if (!defined('nrJ4')) { ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
    <?php } ?>

    <div id="j-main-container">
    <?php
        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>

    <table class="adminlist nrTable table">
        <thead>
            <tr>
                <th class="center" width="2%"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
                <th width="3%" class="nowrap hidden-phone" align="center">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                </th>
                <th>
                    <?php echo HTMLHelper::_('searchtools.sort', 'NR_NAME', 'a.name', $listDirn, $listOrder); ?>
                </th>
                <th width="15%" class="text-center">
                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_CONVERTFORMS_CAMPAIGN_SYNC', 'a.service', $listDirn, $listOrder); ?>
                </th>
                <th width="5%" class="text-center nowrap hidden-phone">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($this->items)) { ?>
                <?php foreach($this->items as $i => $item): ?>
                    <?php 
                        $canChange = $user->authorise('core.edit.state', 'com_convertforms.campaign.' . $item->id);
                        $leadsURL  = Uri::base() . 'index.php?option=com_convertforms&view=conversions&filter.campaign_id='. $item->id .'&filter.form_id';
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                        <td class="text-center">
                            <?php if (defined('nrJ4')) { ?>
                                <?php
                                    $options = [
                                        'task_prefix' => 'campaigns.',
                                        'disabled' => !$canChange,
                                        'id' => 'state-' . $item->id
                                    ];

                                    echo (new PublishedButton)->render((int) $item->state, $i, $options);
                                ?>
                            <?php } else { ?>
                                <div class="btn-group">
                                    <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'campaigns.', $canChange); ?>

                                    <?php
                                    if ($canChange && !defined('nrJ4'))
                                    {
                                        HTMLHelper::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'campaigns');
                                        HTMLHelper::_('actionsdropdown.' . 'duplicate', 'cb' . $i, 'campaigns');
                                            
                                        echo HTMLHelper::_('actionsdropdown.render', $this->escape($item->name));
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="<?php echo Route::_('index.php?option=com_convertforms&task=campaign.edit&id='.$item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?>">
                                <?php echo $this->escape($item->name); ?>
                            </a>
                        </td>
                        <td class="text-center">
                            <?php 
                                if (!empty($item->service))
                                {
                                    echo Text::_("PLG_CONVERTFORMS_" . strtoupper($item->service) . "_ALIAS"); 
                                }
                            ?>
                        </td>
                        <td class="text-center"><?php echo $item->id ?></td>
                    </tr>
                <?php endforeach; ?>  
            <?php } else { ?>
                <tr>
                    <td align="center" colspan="9">
                        <div align="center">
                            <?php echo ConvertForms\Helper::noItemsFound("campaigns"); ?>
                            -
                            <a href="javascript://" onclick="Joomla.submitbutton('campaign.add')"><?php echo Text::_("COM_CONVERTFORMS_CREATE_NEW") ?></a>   
                        </div>
                    </td>
                </tr>
            <?php } ?>        
        </tbody>
    </table>

    <?php echo $this->pagination->getListFooter(); ?>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>

<?php include_once(JPATH_COMPONENT_ADMINISTRATOR . '/layouts/footer.php'); ?>