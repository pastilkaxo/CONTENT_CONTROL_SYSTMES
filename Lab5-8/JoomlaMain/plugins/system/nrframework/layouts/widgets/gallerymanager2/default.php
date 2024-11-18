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

extract($displayData);

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

if (!$disabled)
{
	HTMLHelper::_('bootstrap.modal');

	if (strpos($css_class, 'ordering-default') !== false)
	{
		HTMLHelper::script('plg_system_nrframework/vendor/sortable.min.js', ['relative' => true, 'version' => 'auto']);
	}

	// Required in the front-end for the media manager to work
	if (!defined('nrJ4'))
	{
		HTMLHelper::_('behavior.modal');
		// Front-end editing: The below script is required for front-end media library selection to work as its missing from parent window when called
		if (Factory::getApplication()->isClient('site'))
		{
			?>
			<script>
			function jInsertFieldValue(value, id) {
				var old_id = document.id(id).value;
				if (old_id != id) {
					var elem = document.id(id)
					elem.value = value;
					elem.fireEvent("change");
				}
			}
			</script>
			<?php
		}
	}
	else
	{
		HTMLHelper::_('bootstrap.dropdown', '.dropdown-toggle');
		
		$doc = Factory::getApplication()->getDocument();
		$doc->addScriptOptions('media-picker', [
			'images' => array_map(
				'trim',
				explode(
					',',
					ComponentHelper::getParams('com_media')->get(
						'image_extensions',
						'bmp,gif,jpg,jpeg,png'
					)
				)
			)
		]);

		$wam = $doc->getWebAssetManager();
		$wam->useScript('webcomponent.media-select');
		
		Text::script('JFIELD_MEDIA_LAZY_LABEL');
		Text::script('JFIELD_MEDIA_ALT_LABEL');
		Text::script('JFIELD_MEDIA_ALT_CHECK_LABEL');
		Text::script('JFIELD_MEDIA_ALT_CHECK_DESC_LABEL');
		Text::script('JFIELD_MEDIA_CLASS_LABEL');
		Text::script('JFIELD_MEDIA_FIGURE_CLASS_LABEL');
		Text::script('JFIELD_MEDIA_FIGURE_CAPTION_LABEL');
		Text::script('JFIELD_MEDIA_LAZY_LABEL');
		Text::script('JFIELD_MEDIA_SUMMARY_LABEL');
	}
}

// Use admin gallery manager path if browsing via backend
$gallery_manager_path = Factory::getApplication()->isClient('administrator') ? 'administrator/' : '';

// Javascript files should always load as they are used to populate the Gallery Manager via Dropzone
HTMLHelper::script('plg_system_nrframework/dropzone.min.js', ['relative' => true, 'version' => 'auto']);
HTMLHelper::script('plg_system_nrframework/widgets/gallery/manager_init.js', ['relative' => true, 'version' => 'auto']);
HTMLHelper::script('plg_system_nrframework/widgets/gallery/manager.js', ['relative' => true, 'version' => 'auto']);

if ($load_stylesheet)
{
	HTMLHelper::stylesheet('plg_system_nrframework/widgets/gallerymanager.css', ['relative' => true, 'version' => 'auto']);
}

$tags = isset($tags) ? $tags : [];

$ai_icon = '<svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23.7471 8.7502C20.687 8.11795 18.3098 5.71543 17.6775 2.65536C17.6523 2.50362 17.5258 2.40247 17.3488 2.40247C17.197 2.40247 17.0706 2.50362 17.02 2.65536C16.3878 5.71543 13.9852 8.11795 10.9505 8.7502C10.7987 8.77549 10.6976 8.90194 10.6976 9.07897C10.6976 9.2307 10.7987 9.35715 10.9505 9.40773C14.0105 10.04 16.3878 12.4425 17.02 15.4773C17.0453 15.629 17.1718 15.7302 17.3488 15.7302C17.5005 15.7302 17.627 15.629 17.6775 15.4773C18.3098 12.4172 20.7123 10.04 23.7471 9.40773C23.8988 9.38244 24 9.25599 24 9.07897C24 8.90194 23.8988 8.77549 23.7471 8.7502Z" fill="currentColor"/><path d="M13.5554 15.882C11.1022 15.3762 9.18022 13.4542 8.67443 10.9758C8.64914 10.8493 8.54798 10.7734 8.42153 10.7734C8.29508 10.7734 8.19392 10.8493 8.16863 10.9758C7.66284 13.4542 5.74081 15.3762 3.28771 15.882C3.16126 15.9073 3.08539 16.0084 3.08539 16.1349C3.08539 16.2613 3.16126 16.3625 3.28771 16.3878C5.74081 16.8936 7.66284 18.8156 8.16863 21.2687C8.19392 21.3951 8.29508 21.471 8.42153 21.471C8.54798 21.471 8.64914 21.3951 8.67443 21.2687C9.18022 18.8156 11.1022 16.8936 13.5554 16.3878C13.6818 16.3625 13.7577 16.2613 13.7577 16.1349C13.7577 16.0084 13.6818 15.9073 13.5554 15.882Z" fill="currentColor"/><path d="M4.83035 10.0906C4.88093 10.2424 5.00737 10.3435 5.15911 10.3435C5.31085 10.3435 5.46259 10.2424 5.48788 10.0906C6.01897 7.83983 7.83983 6.04426 10.0653 5.51317C10.2171 5.46259 10.3182 5.33614 10.3182 5.1844C10.3182 5.03266 10.2171 4.88093 10.0653 4.85564C7.78925 4.29926 6.01897 2.52898 5.48788 0.252898C5.46259 0.101159 5.31085 0 5.15911 0C5.00737 0 4.85564 0.101159 4.83035 0.252898C4.27397 2.52898 2.52898 4.29926 0.252898 4.85564C0.101159 4.90622 0 5.03266 0 5.1844C0 5.33614 0.101159 5.48788 0.252898 5.51317C2.4784 6.04426 4.27397 7.83983 4.83035 10.0906Z" fill="currentColor"/></svg>';
?>
<!-- Gallery Manager -->
<div
	class="nrf-widget tf-gallery-manager<?php echo $css_class; ?>"
	data-context="<?php echo $context; ?>"
	data-field-id="<?php echo $field_id; ?>"
	data-item-id="<?php echo $item_id; ?>"
	data-widget="<?php echo $widget; ?>"
>
	<?php if ($required) { ?>
		<!-- Make Joomla client-side form validator happy by adding a fake hidden input field when the Gallery is required. -->
		<input type="hidden" required class="required" id="<?php echo $id; ?>"/>
	<?php } ?>

	<!-- Actions -->
	<div class="tf-gallery-actions">
		<div class="btn-group tf-gallery-actions-dropdown<?php echo !defined('nrJ4') ? ' dropdown' : ''; ?> " title="<?php echo Text::_('NR_GALLERY_MANAGER_SELECT_UNSELECT_IMAGES'); ?>">
			<button class="btn btn-secondary add tf-gallery-actions-dropdown-current tf-gallery-actions-dropdown-action select" onclick="return false;"><i class="me-2 icon-checkbox-unchecked"></i></button>
			<button class="btn btn-secondary add dropdown-toggle dropdown-toggle-split" data-<?php echo defined('nrJ4') ? 'bs-' : ''; ?>toggle="dropdown" title="<?php echo Text::_('NR_GALLERY_MANAGER_ADD_DROPDOWN'); ?>">
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li><a href="#" class="dropdown-item tf-gallery-actions-dropdown-action select"><?php echo Text::_('NR_GALLERY_MANAGER_SELECT_ALL_ITEMS'); ?></a></li>
				<li><a href="#" class="dropdown-item tf-gallery-actions-dropdown-action unselect is-hidden"><?php echo Text::_('NR_GALLERY_MANAGER_UNSELECT_ALL_ITEMS'); ?></a></li>
			</ul>
		</div>
		<a class="tf-gallery-remove-selected-items-button icon-button" title="<?php echo Text::_('NR_GALLERY_MANAGER_REMOVE_SELECTED_IMAGES'); ?>">
			<i class="icon-trash"></i>
		</a>
		<a href="#" class="tf-gallery-ai-apply-all-button" title="<?php echo Text::_('NR_GENERATE_IMAGE_DESC_TO_ALL_IMAGES'); ?>">
			<?php echo $ai_icon; ?>
		</a>
		<div class="btn-group add-button<?php echo !defined('nrJ4') ? ' dropdown' : ''; ?>">
			<button class="btn btn-success add tf-gallery-add-item-button" onclick="return false;" title="<?php echo Text::_('NR_GALLERY_MANAGER_ADD_IMAGES'); ?>"><i class="me-2 icon-pictures"></i><?php echo Text::_('NR_GALLERY_MANAGER_ADD_IMAGES'); ?></button>
			<button class="btn btn-success add dropdown-toggle dropdown-toggle-split" data-<?php echo defined('nrJ4') ? 'bs-' : ''; ?>toggle="dropdown" title="<?php echo Text::_('NR_GALLERY_MANAGER_ADD_DROPDOWN'); ?>">
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li>
					<a <?php echo !defined('nrJ4') ? 'rel="{handler: \'iframe\', size: {x: 1000, y: 750}}" href="' . Uri::root() . $gallery_manager_path . '?option=com_media&view=images&tmpl=component&fieldid=' . $id . '_uploaded_file"' : 'href="#" data-bs-toggle="modal" data-bs-target="#tf-GalleryMediaManager-' . $id . '"'; ?> class="dropdown-item tf-gallery-browse-item-button<?php echo !defined('nrJ4') ? ' modal' : ''; ?> popup" title="<?php echo Text::_('NR_GALLERY_MANAGER_BROWSE_MEDIA_LIBRARY'); ?>"><i class="me-2 icon-folder-open"></i><?php echo Text::_('NR_GALLERY_MANAGER_BROWSE_MEDIA_LIBRARY'); ?></a>
				</li>
			</ul>
		</div>
		<input type="hidden" class="media_uploader_file" id="<?php echo $id; ?>_uploaded_file" />
	</div>
	<!-- /Actions -->

	<!-- Dropzone -->
	<div
		data-inputname="<?php echo $name; ?>"
		data-maxfilesize="<?php echo $max_file_size; ?>"
		data-maxfiles="<?php echo $limit_files; ?>"
		data-acceptedfiles="<?php echo $allowed_file_types; ?>"
		data-value='<?php echo $value ? json_encode($value, JSON_HEX_APOS) : ''; ?>'
		data-baseurl="<?php echo Uri::base(); ?>"
		data-rooturl="<?php echo Uri::root(); ?>"
		class="tf-gallery-dz">
		<!-- DZ Message Wrapper -->
		<div class="dz-message">
			<!-- Message -->
			<div class="dz-message-center">
				<span class="text"><?php echo Text::_('NR_GALLERY_MANAGER_DRAG_AND_DROP_TEXT'); ?></span>
				<span class="browse"><?php echo Text::_('NR_GALLERY_MANAGER_BROWSE'); ?></span>
			</div>
			<!-- /Message -->
		</div>
		<!-- /DZ Message Wrapper -->
	</div>
	<!-- /Dropzone -->

	<!-- Dropzone Preview Template -->
	<template class="previewTemplate">
		<div class="tf-gallery-preview-item template" data-item-id="">
			<div class="checkmark-edited-icon"><?php echo Text::_('NR_UPDATED'); ?></div>
			<div class="select-item-checkbox" title="<?php echo Text::_('NR_GALLERY_MANAGER_CHECK_TO_DELETE_ITEMS'); ?>">
				<input type="checkbox" id="<?php echo $name; ?>[select-item]" />
				<label for="<?php echo $name; ?>[select-item]">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_279_439" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="20"><rect width="20" height="20" fill="#D9D9D9"/></mask><g mask="url(#mask0_279_439)"><path d="M8.83342 13.8333L14.7084 7.95829L13.5417 6.79163L8.83342 11.5L6.45841 9.12496L5.29175 10.2916L8.83342 13.8333ZM10.0001 18.3333C8.8473 18.3333 7.76397 18.1145 6.75008 17.677C5.73619 17.2395 4.85425 16.6458 4.10425 15.8958C3.35425 15.1458 2.7605 14.2638 2.323 13.25C1.8855 12.2361 1.66675 11.1527 1.66675 9.99996C1.66675 8.84718 1.8855 7.76385 2.323 6.74996C2.7605 5.73607 3.35425 4.85413 4.10425 4.10413C4.85425 3.35413 5.73619 2.76038 6.75008 2.32288C7.76397 1.88538 8.8473 1.66663 10.0001 1.66663C11.1529 1.66663 12.2362 1.88538 13.2501 2.32288C14.264 2.76038 15.1459 3.35413 15.8959 4.10413C16.6459 4.85413 17.2397 5.73607 17.6772 6.74996C18.1147 7.76385 18.3334 8.84718 18.3334 9.99996C18.3334 11.1527 18.1147 12.2361 17.6772 13.25C17.2397 14.2638 16.6459 15.1458 15.8959 15.8958C15.1459 16.6458 14.264 17.2395 13.2501 17.677C12.2362 18.1145 11.1529 18.3333 10.0001 18.3333ZM10.0001 16.6666C11.8612 16.6666 13.4376 16.0208 14.7292 14.7291C16.0209 13.4375 16.6667 11.8611 16.6667 9.99996C16.6667 8.13885 16.0209 6.56246 14.7292 5.27079C13.4376 3.97913 11.8612 3.33329 10.0001 3.33329C8.13897 3.33329 6.56258 3.97913 5.27091 5.27079C3.97925 6.56246 3.33341 8.13885 3.33341 9.99996C3.33341 11.8611 3.97925 13.4375 5.27091 14.7291C6.56258 16.0208 8.13897 16.6666 10.0001 16.6666Z" fill="currentColor"/></g></svg>
				</label>
			</div>
			<div class="tf-gallery-preview-item--actions">
				<a href="#" class="tf-gallery-preview-edit-item" title="<?php echo Text::_('NR_GALLERY_MANAGER_CLICK_TO_EDIT_ITEM'); ?>" data-toggle="modal" data-target="#tf-GalleryEditItem-<?php echo $id; ?>" data-bs-toggle="modal" data-bs-target="#tf-GalleryEditItem-<?php echo $id; ?>"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_279_182" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="20"><rect width="20" height="20" fill="#D9D9D9"/></mask><g mask="url(#mask0_279_182)"><path d="M4.16667 15.8333H5.35417L13.5 7.6875L12.3125 6.5L4.16667 14.6458V15.8333ZM2.5 17.5V13.9583L13.5 2.97917C13.6667 2.82639 13.8507 2.70833 14.0521 2.625C14.2535 2.54167 14.4653 2.5 14.6875 2.5C14.9097 2.5 15.125 2.54167 15.3333 2.625C15.5417 2.70833 15.7222 2.83333 15.875 3L17.0208 4.16667C17.1875 4.31944 17.309 4.5 17.3854 4.70833C17.4618 4.91667 17.5 5.125 17.5 5.33333C17.5 5.55556 17.4618 5.76736 17.3854 5.96875C17.309 6.17014 17.1875 6.35417 17.0208 6.52083L6.04167 17.5H2.5ZM12.8958 7.10417L12.3125 6.5L13.5 7.6875L12.8958 7.10417Z" fill="currentColor"/></g></svg></a>
				<a href="#" class="tf-gallery-preview-remove-item" title="<?php echo Text::_('NR_GALLERY_MANAGER_CLICK_TO_DELETE_ITEM'); ?>" data-dz-remove><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_279_185" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="20"><rect width="20" height="20" fill="#D9D9D9"/></mask><g mask="url(#mask0_279_185)"><path d="M5.83301 17.5C5.37467 17.5 4.98231 17.3368 4.65592 17.0104C4.32954 16.684 4.16634 16.2917 4.16634 15.8333V5H3.33301V3.33333H7.49967V2.5H12.4997V3.33333H16.6663V5H15.833V15.8333C15.833 16.2917 15.6698 16.684 15.3434 17.0104C15.017 17.3368 14.6247 17.5 14.1663 17.5H5.83301ZM14.1663 5H5.83301V15.8333H14.1663V5ZM7.49967 14.1667H9.16634V6.66667H7.49967V14.1667ZM10.833 14.1667H12.4997V6.66667H10.833V14.1667Z" fill="currentColor"/></g></svg></a>
			</div>
			<div class="dz-status"></div>
			<div class="dz-thumb">
				<div class="tf-gallery-preview-item--temp-label" title="<?php echo Text::_('NR_GALLERY_TEMPORARY_IMAGE_TITLE'); ?>"><?php echo Text::_('NR_TEMPORARY'); ?></div>
				<div class="dz-progress"><span class="text"><?php echo Text::_('NR_GALLERY_MANAGER_UPLOADING'); ?></span><span class="dz-upload" data-dz-uploadprogress></span></div>
				<div class="tf-gallery-preview-in-queue"><?php echo Text::_('NR_GALLERY_MANAGER_IN_QUEUE'); ?></div>
				<img data-dz-thumbnail />
			</div>
			<div class="tf-gallery-preview-item--alt">
				<div class="tf-gallery-preview-item--alt--existing"></div>
				<div class="tf-gallery-preview-item--alt--fields">
					<textarea name="<?php echo $name; ?>[alt]" class="item-alt" placeholder="<?php echo Text::_('NR_GALLERY_MANAGER_IMAGE_DESCRIPTION_HINT'); ?>" title="<?php echo Text::_('NR_GALLERY_MANAGER_ALT_HINT'); ?>" rows="2"></textarea>
					<?php if (!$pro): ?>
						<div class="tf-gallery-ai-button" title="<?php echo Text::_('NR_GENERATE_IMAGE_DESCRIPTION_USING_AI') ?>">
							<span data-pro-only="<?php echo Text::_('NR_AI_IMAGE_DESCRIPTION_GENERATION') ?>"><?php echo $ai_icon; ?></span>
						</div>
					<?php elseif (!$openai_api_key): ?>
						<div class="tf-gallery-ai-button" title="<?php echo Text::_('NR_GENERATE_IMAGE_DESCRIPTION_USING_AI') ?>" data-toggle="modal" data-target="#tf-GalleryMissingValue-<?php echo $id; ?>" data-bs-toggle="modal" data-bs-target="#tf-GalleryMissingValue-<?php echo $id; ?>">
							<?php echo $ai_icon; ?>
						</div>
					<?php else: ?>
						<div class="tf-gallery-ai-button tf-gallery-preview-generate-caption-item" title="<?php echo Text::_('NR_GENERATE_IMAGE_DESCRIPTION_USING_AI') ?>">
							<?php if ($pro): ?>
							<svg class="loading-state" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="#e8eaed"><path d="M480-96q-78.72 0-148.8-30.24-70.08-30.24-122.4-82.56-52.32-52.32-82.56-122.4Q96-401.28 96-480q0-79.68 30.22-149.28 30.21-69.6 82.49-121.92 52.28-52.32 122.3-82.56Q401.04-864 479.69-864 496-864 506-854t10 25.51q0 15.51-10 26T480-792q-129.67 0-220.84 90.5Q168-611 168-480.5T259.16-259q91.17 91 220.84 91 131 0 221.5-91.16Q792-350.33 792-480q0-16 10.49-26t26-10Q844-516 854-506q10 10 10 26.31 0 78.65-30.24 148.68-30.24 70.02-82.56 122.3-52.32 52.28-121.92 82.49Q559.68-96 480-96Z" fill="currentColor" /></svg>
							<?php endif; ?>
							<?php echo $ai_icon; ?>
						</div>
						<div class="tf-gallery-preview-item--alt--choices">
							<div class="tf-gallery-preview-item--alt--choices--item tf-gallery-preview-generate-caption-item-accept">
								<svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.95837 11L0.208374 6.24996L1.39587 5.06246L4.95837 8.62496L12.6042 0.979126L13.7917 2.16663L4.95837 11Z" fill="currentColor"/></svg>
								Accept
							</div>
							<div class="tf-gallery-preview-item--alt--choices--item tf-gallery-preview-generate-caption-item-discard">
								<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.33335 11.8333L0.166687 10.6666L4.83335 5.99996L0.166687 1.33329L1.33335 0.166626L6.00002 4.83329L10.6667 0.166626L11.8334 1.33329L7.16669 5.99996L11.8334 10.6666L10.6667 11.8333L6.00002 7.16663L1.33335 11.8333Z" fill="currentColor"/></svg>
								Discard
							</div>
							<div class="tf-gallery-preview-item--alt--choices--item tf-gallery-preview-generate-caption-item">
								<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.8333 11.8333V8.49996C13.8333 7.80552 13.5903 7.21524 13.1042 6.72913C12.6181 6.24302 12.0278 5.99996 11.3333 5.99996H3.6875L6.6875 8.99996L5.5 10.1666L0.5 5.16663L5.5 0.166626L6.6875 1.33329L3.6875 4.33329H11.3333C12.4861 4.33329 13.4688 4.73954 14.2812 5.55204C15.0938 6.36454 15.5 7.34718 15.5 8.49996V11.8333H13.8333Z" fill="currentColor"/></svg>
								Try again
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="tf-gallery-preview-error"><div data-dz-errormessage></div></div>
			<input type="hidden" value="" class="item-source" name="<?php echo $name; ?>[source]" />
			<input type="hidden" value="" class="item-slideshow" name="<?php echo $name; ?>[slideshow]" />
			<input type="hidden" value="" class="item-original" name="<?php echo $name; ?>[image]" />
			<input type="hidden" value="" class="item-thumbnail" name="<?php echo $name; ?>[thumbnail]" />
			<input type="hidden" value="" class="item-caption" name="<?php echo $name; ?>[caption]" />
			<input type="hidden" value="" class="item-tags" name="<?php echo $name; ?>[tags]" />
		</div>
	</template>
	<!-- /Dropzone Preview Template -->

	<?php
	if (!$disabled)
	{
		// Add the modal that appears when the user hasn't added an OpenAI API key
		if (!$openai_api_key)
		{
			$opts = [
				'title'       => 'Missing OpenAI API Key',
                'height'      => '100%',
                'width'       => '100%',
				'backdrop' 	  => 'static'
			];
	
			$content = LayoutHelper::render('missing_value', [
				'description' => Text::_('NR_AI_MISSING_KEY_MODAL_DESC'),
				'link' => 'https://www.tassos.gr/kb/general/ai-features-joomla',
				'link_text' => Text::_('NR_WHERE_CAN_I_FIND_MY_OPENAI_API_KEY')
			], implode(DIRECTORY_SEPARATOR, [JPATH_PLUGINS, 'system', 'nrframework', 'layouts', 'modals']));
	
			echo HTMLHelper::_('bootstrap.renderModal', 'tf-GalleryMissingValue-' . $id, $opts, $content);
		}
		
		// Print Joomla 4 Media Manager modal only if Gallery is not disabled
		if (defined('nrJ4'))
		{
			$opts = [
				'title'       => Text::_('NR_GALLERY_MANAGER_SELECT_ITEM'),
				'height'      => '400px',
				'width'       => '800px',
				'bodyHeight'  => 80,
				'modalWidth'  => 80,
				'backdrop' 	  => 'static',
				'footer'      => '<button type="button" class="btn btn-primary tf-gallery-button-save-selected" data-bs-dismiss="modal">' . Text::_('JSELECT') . '</button>' . '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . Text::_('JCANCEL') . '</button>',
				'isJoomla' 	  => true
			];
			HTMLHelper::_('bootstrap.modal', '#tf-GalleryMediaManager-' . $id, $opts);

			$layoutData = [
				'selector' => 'tf-GalleryMediaManager-' . $id,
				'params'   => $opts,
				'body'     => LayoutHelper::render('media_library', ['gallery_manager_path' => $gallery_manager_path], __DIR__)
			];
	
			echo LayoutHelper::render('libraries.html.bootstrap.modal.main', $layoutData);
		}

		if (!$readonly)
		{
			// Print Edit Modal
			$opts = [
				'title'       => Text::_('NR_GALLERY_MANAGER_EDIT_ITEM'),
                'height'      => '100%',
                'width'       => '100%',
				'backdrop' 	  => 'static',
				'footer'      => '<button type="button" class="btn btn-primary tf-gallery-button-save-edited-item" data-bs-dismiss="modal" data-dismiss="modal">' . Text::_('NR_SAVE') . '</button>' .
								 '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">' . Text::_('JCANCEL') . '</button>'
			];
	
			$content = LayoutHelper::render('edit', [
				'tags' => $tags
			], __DIR__);
	
			echo HTMLHelper::_('bootstrap.renderModal', 'tf-GalleryEditItem-' . $id, $opts, $content);
		}
	}
	?>
</div>
<!-- /Gallery Manager -->