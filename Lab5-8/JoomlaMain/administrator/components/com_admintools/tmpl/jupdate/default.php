<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * @var \Akeeba\Component\AdminTools\Administrator\View\Jupdate\HtmlView $this
 */

?>
<div class="card border-primary mt-4">
	<h3 class="card-header bg-primary text-light">
		<?= Text::_('COM_ADMINTOOLS_JUPDATE_BTN_RESET') ?>
	</h3>
	<div class="card-body">
		<p>
			<?= Text::_('COM_ADMINTOOLS_JUPDATE_LBL_INFO') ?>
		</p>
		<p>
			<?= Text::sprintf('COM_ADMINTOOLS_JUPDATE_LBL_AFTER', 'index.php?option=com_joomlaupdate') ?>
		</p>
		<div class="my-5">
			<form action="index.php" method="post" class="d-flex justify-content-center">
				<input type="hidden" name="option" value="com_admintools">
				<input type="hidden" name="view" value="jupdate">
				<input type="hidden" name="task" value="reset">
				<?= HTMLHelper::_('form.token'); ?>

				<button type="submit"
				        class="btn btn-lg btn-danger w-75"
				>
					<span class="fa fa-rotate-right pe-2" aria-hidden="true"></span>
					<?= Text::_('COM_ADMINTOOLS_JUPDATE_BTN_RESET') ?>
				</button>
			</form>
		</div>
		<p class="text-muted">
			<span class="fa fa-info-circle pe-1" aria-hidden="true"></span>
			<?= Text::_('COM_ADMINTOOLS_JUPDATE_LBL_WARN') ?>
		</p>
		<div class="alert alert-warning my-3 small">
			<?= Text::_('COM_ADMINTOOLS_JUPDATE_LBL_DISCLAIMER') ?>
		</div>
	</div>
</div>