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
use Joomla\CMS\Language\Text;

extract($displayData);

$activeTab = count($items) > 0 ? 'fmAllFields' : 'fmaddField';

HTMLHelper::script('com_convertforms/vendor/sortable.min.js', ['relative' => true, 'version' => 'auto']);
?>
<div class="fm" data-formcontrol="<?php echo $formControl ?>" data-nextid="<?php echo $nextid; ?>">

	<?php 
		echo HTMLHelper::_('bootstrap.startTabSet', 'fieldsManager', array('active' => $activeTab));
		echo HTMLHelper::_('bootstrap.addTab', 'fieldsManager', 'fmaddField', Text::_('COM_CONVERTFORMS_ADD_FIELD'));
	?>

	<div class="fmAvailableFields">
		<?php foreach ($fieldgroups as $group => $fieldgroup) { 
			?>
			<div class="fmFieldGroup">
				<h5><?php echo $fieldgroup['title'] ?></h5>
				<div class="fmFields">
					<?php foreach ($fieldgroup['fields'] as $key => $field) { 
						$isProOnly = !$field['class'];
					?>
					<div>
						<button class="cf-btn btn-dark addField" type="button" data-type="<?php echo $field['name']; ?>" title="<?php echo $field['desc']; ?>"<?php if ($isProOnly) { ?> data-pro-only="<?php echo str_replace('Field', '', $field['title']) . ' Field' ?>"<?php } ?>>
							<?php echo $field['title'] ?>
							<?php if ($isProOnly) { ?>
								<span class="icon-lock right"></span>
							<?php } ?>
						</button>
					</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>
	
	<?php 
		echo HTMLHelper::_('bootstrap.endTab'); 
		echo HTMLHelper::_('bootstrap.addTab', 'fieldsManager', 'fmAllFields', Text::_('COM_CONVERTFORMS_ALL_FIELDS'));
	?>

	<div class="fmAddedFields">
		<?php foreach ($items as $key => $item) { ?>
			<div class="item" data-key="<?php echo $item['data']['key'] ?>">
				<span class="fmFieldLabel"></span>
				<span class="fmFieldControl">
					<a href="#" class="copyField" title="<?php echo Text::_('COM_CONVERTFORMS_FIELDS_COPY') ?>">
						<span class="cf-icon-copy"></span>
					</a>
					<a href="#" class="removeField" title="<?php echo Text::_('COM_CONVERTFORMS_FIELDS_DELETE') ?>">
						<span class="cf-icon-cancel"></span>
					</a>
				</span>
			</div>
		<?php } ?>
	</div>

	<?php
		echo HTMLHelper::_('bootstrap.endTab'); 
		echo HTMLHelper::_('bootstrap.addTab', 'fieldsManager', 'fmItems', Text::_('JOPTIONS')); 
	?>

	<div class="fmItems">
		<?php 
			foreach ($items as $key => $item)
			{
				echo $item['form'];
			}
		?>
	</div>

	<?php 
		echo HTMLHelper::_('bootstrap.endTab'); 
		echo HTMLHelper::_('bootstrap.endTabSet');
	?>
	
</div>