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

// Load dropzone library
HTMLHelper::script('com_convertforms/vendor/dropzone.min.js', ['relative' => true, 'version' => 'auto']);
HTMLHelper::script('com_convertforms/field_fileupload.js', ['relative' => true, 'version' => 'auto']);

// Add language strings used by dropzone.js
Text::script('COM_CONVERTFORMS_ERROR_WAIT_FILE_UPLOADS');
Text::script('COM_CONVERTFORMS_UPLOAD_FILETOOBIG');
Text::script('COM_CONVERTFORMS_UPLOAD_INVALID_FILE');
Text::script('COM_CONVERTFORMS_UPLOAD_FALLBACK_MESSAGE');
Text::script('COM_CONVERTFORMS_UPLOAD_RESPONSE_ERROR');
Text::script('COM_CONVERTFORMS_UPLOAD_CANCEL_UPLOAD');
Text::script('COM_CONVERTFORMS_UPLOAD_CANCEL_UPLOAD_CONFIRMATION');
Text::script('COM_CONVERTFORMS_UPLOAD_REMOVE_FILE');
Text::script('COM_CONVERTFORMS_UPLOAD_MAX_FILES_EXCEEDED');
?>
<div class="cfup-tmpl" style="display:none;">
	<div class="cfup-file">
		<div class="cfup-status"></div>
		<div class="cfup-thumb">
			<img data-dz-thumbnail />
		</div>
		<div class="cfup-details">
			<div class="cfup-name" data-dz-name></div>
			<div class="cfup-error"><div data-dz-errormessage></div></div>
			<div class="cfup-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
		</div>
		<div class="cfup-right">
			<span class="cfup-size" data-dz-size></span>
			<a href="#" class="cfup-remove" data-dz-remove>×</a>
		</div>
	</div>
</div>

<div id="<?php echo $field->input_id ?>" 
	data-name="<?php echo $field->input_name ?>"
	data-key="<?php echo $field->key ?>"
	data-maxfilesize="<?php echo $field->max_file_size ?>"
	data-maxfiles="<?php echo $field->limit_files ?>"
	data-acceptedfiles="<?php echo $field->upload_types ?>"
	class="cfupload">
	<div class="dz-message">
		<span><?php echo Text::_('COM_CONVERTFORMS_UPLOAD_DRAG_AND_DROP_FILES') ?></span>
		<span class="cfupload-browse"><?php echo Text::_('COM_CONVERTFORMS_UPLOAD_BROWSE') ?></span>
	</div>
</div>