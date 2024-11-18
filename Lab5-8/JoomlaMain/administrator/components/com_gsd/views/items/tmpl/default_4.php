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

use Joomla\CMS\Button\PublishedButton;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::stylesheet('com_gsd/styles.css', ['relative' => true, 'version' => 'auto']);

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$showcolors = $this->config->get('colorgroup', true);
$user       = Factory::getUser();

?>

<form action="<?php echo Route::_('index.php?option=com_gsd&view=items'); ?>" method="post" name="adminForm" id="adminForm">
    <?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
    <table class="adminlist nrTable table">
        <thead>
            <tr>
                <th class="center" width="2%"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
                <th width="3%" class="nowrap hidden-phone">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                </th>
                <?php if ($showcolors) { ?>
                    <th width="1%"></th>
                <?php } ?>
                <th>
                    <?php echo HTMLHelper::_('searchtools.sort', 'NR_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th width="14%">
                    <?php echo HTMLHelper::_('searchtools.sort', 'GSD_CONTENT_TYPE', 'a.contenttype', $listDirn, $listOrder); ?>
                </th>
                <th width="14%">
                    <?php echo Text::_('GSD_INTEGRATION'); ?>
                </th>
                <th width="14%">
                    <?php echo Text::_('GSD_ITEM_RULES'); ?>
                </th>
                <th width="10%" class="nowrap hidden-phone">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                </th>
                <th width="14%">
                    <?php echo HTMLHelper::_('searchtools.sort', 'NR_CREATED_DATE', 'a.created', $listDirn, $listOrder); ?>
                </th>
                <th width="3%" class="text-center nowrap hidden-phone">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($this->items)) { ?>
                <?php foreach($this->items as $i => $item): ?>
                    <?php 
                        $canChange  = $user->authorise('core.edit.state', 'com_gsd.item.' . $item->id);
                    ?>
                    <tr>
                        <td class="center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                        <td class="text-center">
                            <?php
                                $options = [
                                    'task_prefix' => 'items.',
                                    'disabled' => !$canChange,
                                    'id' => 'state-' . $item->id
                                ];

                                echo (new PublishedButton)->render((int) $item->state, $i, $options);
                            ?>
                        </td>
                        <?php if ($showcolors) : ?>
                            <td class="center inlist">
                                <?php $color = isset($item->colorgroup) ? $item->colorgroup : ""; ?>
                                <span class="boxColor">
                                    <span style="background-color: <?php echo $color ?>;"></span>
                                </span>
                            </td>
                        <?php endif; ?>
                        <td>
                            <a href="<?php echo Route::_('index.php?option=com_gsd&task=item.edit&id='.$item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?>">
                                <?php echo $item->title; ?>
                            </a>
                            <?php if (isset($item->note)) { ?>
                                <div class="small" style="opacity:.6;"><?php echo $item->note; ?></div>
                            <?php } ?>
                        </td>
                        <td><?php echo Text::_('GSD_' . $item->contenttype); ?></td>
                        <td>
                            <?php echo Text::_('PLG_GSD_' . strtoupper($item->plugin) . '_ALIAS'); ?>
                        </td>
                        <td>
                            <?php 
                                $all_pages = true;

                                if (isset($item->assignments))
                                {
                                    foreach ($item->assignments as $key => $assignment)
                                    {
                                        if ($assignment->assignment_state == '1')
                                        {
                                            $all_pages = false;
                                            break;
                                        }
                                    }
                                }
                            ?>
                            <?php echo ($all_pages) ? Text::_('COM_GSD_TARGETS_ALL_PAGES') : Text::_('COM_GSD_TARGETS_SPECIFIC_PAGES'); ?>
                        </td>
                        <td class="hidden-phone">
                            <?php echo LayoutHelper::render('joomla.content.language', $item); ?>
                        </td>
                        <td><?php echo $item->created; ?></td>
                        <td class="text-center"><?php echo $item->id ?></td>
                    </tr>
                <?php endforeach; ?>  
            <?php } else { ?>
                <tr>
                    <td align="center" colspan="9">
                        <div align="center">
                            <?php echo Text::_('NR_NO_ITEMS_FOUND'); ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>        
        </tbody>
    </table>

    <?php echo $this->pagination->getListFooter(); ?>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>