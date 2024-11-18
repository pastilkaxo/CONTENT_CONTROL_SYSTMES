<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Helpers\Widgets;

defined('_JEXEC') or die;

use NRFramework\File;
use NRFramework\Image;
use NRFramework\Functions;
use Joomla\Registry\Registry;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Path;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File as JoomlaCMSFile;

class GalleryManager2
{
	/**
	 * How long the files can stay in the temp folder.
	 * 
	 * After each save a clean up is run and all files older
	 * than this value in days are removed.
	 * 
	 * @var  int
	 */
	private static $temp_files_cleanup_days = 1;
	
	/**
	 * Uploads the source image to the temp folder.
	 *
	 * @param	array	$file			  The request file as posted by form
	 * @param	string	$upload_settings  The upload settings
	 *
	 * @return	array|bool The uploaded image paths or false on failure
	 */
	public static function upload($file, $uploadSettings)
	{
		$fullTempFolder = self::getFullTempFolder($uploadSettings['context'], $uploadSettings['field_id'], $uploadSettings['item_id']);

		// Create source folder if not exists
		File::createDirs($fullTempFolder);

		// Move the image to the tmp folder
		try {
			$source = File::upload($file, $fullTempFolder, $uploadSettings['allowed_types'], $uploadSettings['allow_unsafe'], null, $uploadSettings['random_suffix']);
		} catch (\Throwable $th)
		{
			return false;
		}

		if (!$source)
		{
			return false;
		}

		return str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '', $source);
	}

	public static function maybeRegenerateImages($context = 'default', $items = [], $field_id = null, $item_id = null, $oldData = [])
	{
		if (!$oldData)
		{
			return false;
		}
		
		if (!$field_data = self::getSettings($context, $field_id, $item_id))
		{
			return false;
		}

		/**
		 * In order to proceed, there must be changes in the following data:
		 * 
		 * - provider
		 * - full size image
		 * - slideshow image
		 * - thumbnails
		 * - watermark
		 */
		$images = self::canRegenerateImages($field_data, $oldData);

		$items = is_string($items) ? json_decode($items, true) : $items;

		foreach ($items as &$item)
		{
			self::generateFromSource($item, $images, $field_data, false);
		}

		return $items;
	}

	/**
	 * Checks if the images need to be regenerated.
	 * 
	 * @param   object  $new_data
	 * @param   object  $old_data
	 * 
	 * @return  array
	 */
	public static function canRegenerateImages($new_data = [], $old_data = [])
	{
		$images = [
			'thumb' => false,
			'slideshow' => false,
			'full' => false
		];

		// On provider change
		if ($old_data->get('provider') != $new_data->get('provider'))
		{
			if ($new_data->get('provider') === 'slideshow')
			{
				$images['slideshow'] = true;
	
				if ($new_data->get('show_thumbnails') === '1')
				{
					$images['thumb'] = true;
				}
			}
			else
			{
				$images['thumb'] = true;
			}
		}
		// On thumbnails dimensions change
		else if ($old_data->get('thumbnail_size') != $new_data->get('thumbnail_size') || $old_data->get('justified_item_height') != $new_data->get('justified_item_height') || $old_data->get('show_thumbnails') != $new_data->get('show_thumbnails') || $old_data->get('masonry_thumbnails_width') != $new_data->get('masonry_thumbnails_width') || $old_data->get('slideshow_thumbnail_size') != $new_data->get('slideshow_thumbnail_size'))
		{
			$images['thumb'] = true;
		}
		
		// Check if full image has changed
		if ($old_data->get('full_image') != $new_data->get('full_image') || ($old_data->get('lightbox') != $new_data->get('lightbox') && $new_data->get('lightbox') === '1'))
		{
			$images['full'] = true;
		}

		// Check if slideshow image has changed
		if ($new_data->get('provider') === 'slideshow' && $old_data->get('slideshow_image') != $new_data->get('slideshow_image'))
		{
			$images['slideshow'] = true;
		}

		// Check if watermark settings have changed
		if ($old_data->get('watermark') != $new_data->get('watermark'))
		{
			if ($new_data->get('lightbox') === '1')
			{
				$images['full'] = true;
			}

			if ($old_data->get('slideshow_image') != $new_data->get('slideshow_image') || $new_data->get('provider') === 'slideshow')
			{
				$images['slideshow'] = true;
			}

			if ($old_data->get('provider') !== 'slideshow' || ($new_data->get('provider') === 'slideshow' && $new_data->get('show_thumbnails') === '1'))
			{
				$images['thumb'] = true;
			}
		}

		return $images;
	}

	/**
	 * Returns the settings for the given context.
	 * 
	 * @param   string  $context
	 * @param   int     $field_id
	 * @param   int     $item_id
	 * 
	 * @return  mixed
	 */
	public static function getSettings($context = 'default', $field_id = null, $item_id = null)
	{
		// Make sure we have a valid context
		if (!$context)
		{
			return false;
		}

		$field_data = [];

		if ($context === 'default')
		{
			// Make sure we have a valid field id
			if (!$field_id)
			{
				return Text::_('NR_GALLERY_MANAGER_FIELD_ID_ERROR');
			}

			if (!$field_data = \NRFramework\Helpers\CustomField::getData($field_id))
			{
				return Text::_('NR_GALLERY_MANAGER_INVALID_FIELD_DATA');
			}
		}
		else if ($context === 'module')
		{
			// Make sure we have a valid item id
			if (!$item_id)
			{
				return Text::_('NR_GALLERY_MANAGER_ITEM_ID_ERROR');
			}
			
			if (!$field_data = \NRFramework\Helpers\Module::getData($item_id))
			{
				return Text::_('NR_GALLERY_MANAGER_INVALID_FIELD_DATA');
			}
		}

		return $field_data;
	}

	/**
	 * Moves all given temp items over to the destination folder.
	 * 
	 * @param   array   $value
	 * @param   object  $field
	 * @param   string  $destination_folder
	 * 
	 * @return  void
	 */
	public static function moveTempItemsToDestination($value, $field, $destination_folder)
	{
		if (!$destination_folder)
		{
			return;
		}

		// Create destination folder if missing
		if (!File::createDirs($destination_folder))
		{
			return;
		}

		// Make field params use Registry
		if (!$field->fieldparams instanceof Registry)
		{
			$field->fieldparams = new Registry($field->fieldparams);
		}

		/**
		 * Prepare the items for backwards compatibility
		 */
		$items = is_string($value) ? json_decode($value, true) ?? [['value' => $value]] : $value;

		$items = isset($items['items']) ? $items['items'] : $items;

		if (isset($items['value']))
		{
			$items = [$items];
		}

		$limit_files = (int) $field->fieldparams->get('limit_files', 0);

		// Handle single file
		if ($limit_files === 1 && is_array($items))
		{
			$items = [reset($items)];
		}

		// Compatibility Start: Migrate old items to new folder structure
		self::maybeMigrateOldItems($items, $field->fieldparams);
		// Compatibility End

		$ds = DIRECTORY_SEPARATOR;

		$images = [
			'thumb' => in_array($field->fieldparams->get('provider', 'grid'), ['grid', 'masonry', 'justified']) || ($field->fieldparams->get('provider', 'grid') === 'slideshow' && $field->fieldparams->get('show_thumbnails', '0') === '1'),
			'slideshow' => $field->fieldparams->get('provider', 'grid') === 'slideshow',
			'full' => $field->fieldparams->get('lightbox', '0') === '1'
		];

		$tmpdir = Factory::getConfig()->get('tmp_path');
		$tmpRelativeDirectory = ltrim(str_replace(JPATH_ROOT, '', $tmpdir), $ds);

		// Move all files from the temp folder over to the `upload folder`
		foreach ($items as $key => &$item)
		{
			/**
			 * Skip invalid files.
			 * 
			 * These "files" can appear when we try to move files
			 * over to the destination folder when the gallery manager
			 * is still working to upload queueed files.
			 */
			if ($key === 'ITEM_ID')
			{
				continue;
			}

			// Skip if source does not start with the temp relative directory
			$testSourcePath = ltrim(rtrim($item['source'], $ds), $ds) . $ds;
			if (!Functions::startsWith($testSourcePath, $tmpRelativeDirectory . $ds))
			{
				continue;
			}

			// Move source image to final directory
			try {
				$source_clean = pathinfo($item['source'], PATHINFO_BASENAME);
				$source_path = implode($ds, [JPATH_ROOT, $item['source']]);

				$new_source_path = implode($ds, [rtrim($destination_folder, $ds), md5('source'), $source_clean]);
				$new_source_path = File::move($source_path, $new_source_path);
				$item['source'] = ltrim(rtrim(str_replace(JPATH_ROOT, '', $new_source_path), $ds), $ds);
			} catch (\Throwable $th) {}

			// Generate the rest of images from the source image
			self::generateFromSource($item, $images, $field->fieldparams);
		}

		return $items;
	}

	/**
	 * Check and migrate old items to the new folder structure.
	 * 
	 * @param   array   $items
	 * @param   object  $field_data
	 * 
	 * @return  void
	 */
	public static function maybeMigrateOldItems(&$items = [], $field_data = [])
	{
		if (!is_array($items) || empty($items))
		{
			return;
		}

		$ds = DIRECTORY_SEPARATOR;

		$tmpdir = Factory::getConfig()->get('tmp_path');

		// Migrate images to new folders and create source images from the full image if source is missing
		foreach ($items as &$value)
		{
			// We skip this process if the "source" directory exists in the uploaded files path.
			// This means that the new structure is present, so don't do anything.
			if (!empty($value['source']))
			{
				$fullSourcePath = implode($ds, [JPATH_ROOT, $value['source']]);
				$checkDirectory = dirname($fullSourcePath);

				if (is_dir($checkDirectory) && (Functions::startsWith($checkDirectory, $tmpdir) || Functions::endsWith($checkDirectory, '/' . md5('source'))))
				{
					continue;
				}
			}

			$sourceNewPath = null;

			$fullImageCurrentPath = implode($ds, [JPATH_ROOT, $value['image']]);
			$fullImageData = pathinfo($fullImageCurrentPath);

			// Create source if missing
			if (empty($value['source']))
			{
				$sourceNewPath = implode($ds, [dirname($fullImageCurrentPath), md5('source'), $fullImageData['basename']]);
				$sourceNewPath = File::copy($fullImageCurrentPath, $sourceNewPath);
				$value['source'] = ltrim(rtrim(str_replace(JPATH_ROOT, '', $sourceNewPath), $ds), $ds);
			}
			else
			{
				// Move source image
				$sourceOldPath = implode($ds, [JPATH_ROOT, $value['source']]);
				$sourceNewPath = implode($ds, [JPATH_ROOT, dirname($value['source']), md5('source'), $fullImageData['basename']]);
				$sourceNewPath = File::move($sourceOldPath, $sourceNewPath);
				$value['source'] = ltrim(rtrim(str_replace(JPATH_ROOT, '', $sourceNewPath), $ds), $ds);
			}

			// Delete thumbnail and recreate from source
			$needThumb = false;
			$thumbOldPath = implode($ds, [JPATH_ROOT, $value['thumbnail']]);
			if (is_file($thumbOldPath))
			{
				$needThumb = true;
				JoomlaCMSFile::delete($thumbOldPath);
				$value['thumbnail'] = '';

				if ($field_data->get('provider', 'grid') === 'slideshow' && $field_data->get('show_thumbnails', '0') === '0')
				{
					$needThumb = false;
				}
			}

			// Delete full image and recreate from source
			$needFull = false;
			$fullImageOldPath = implode($ds, [JPATH_ROOT, $value['image']]);
			if (is_file($fullImageOldPath))
			{
				JoomlaCMSFile::delete($fullImageOldPath);
				$value['image'] = '';

				if ($field_data->get('lightbox', '0') === '1')
				{
					$needFull = true;
				}
			}

			self::generateFromSource($value, [
				'thumb' => $needThumb,
				'slideshow' => $field_data->get('provider', 'grid') === 'slideshow',
				'full' => $needFull
			], $field_data);
		}
	}

	/**
	 * Clean up unneeded images.
	 * 
	 * @param   array   $item
	 * @param   object  $field_data
	 */
	public static function cleanUpImages(&$item = [], $field_data = [])
	{
		$ds = DIRECTORY_SEPARATOR;
		$provider = $field_data->get('provider', 'grid');

		/**
		 * Clean up of unneeded images.
		 */
		/**
		 * Delete thumbs if:
		 * 1) We're using the slideshow provider and show_thumbnails is disabled and the item has a thumbnail
		 * 2) We're not using the slideshow provider and the item has a thumbnail and a slideshow image
		 */
		if (($provider === 'slideshow' && $field_data->get('show_thumbnails', '0') === '0' && $item['thumbnail']) || ($provider !== 'slideshow' && $item['thumbnail'] && $item['slideshow']))
		{
			$thumbnails_path = implode($ds, [JPATH_ROOT, $item['thumbnail']]);
			if (is_file($thumbnails_path))
			{
				JoomlaCMSFile::delete($thumbnails_path);
			}
			$item['thumbnail'] = null;
		}

		// Delete full image if lightbox is disabled and the item has a full image
		if ($field_data->get('lightbox') === '0' && $item['image'])
		{
			$full_image_path = implode($ds, [JPATH_ROOT, $item['image']]);
			if (is_file($full_image_path))
			{
				JoomlaCMSFile::delete($full_image_path);
			}
			$item['image'] = null;
		}

		// Delete slideshow image if exists and not in slideshow provider
		if ($provider !== 'slideshow' && $item['slideshow'])
		{
			$slideshow_image_path = implode($ds, [JPATH_ROOT, $item['slideshow']]);
			if (is_file($slideshow_image_path))
			{
				JoomlaCMSFile::delete($slideshow_image_path);
			}
			$item['slideshow'] = null;
		}
	}

	/**
	 * This method generates the full image, thumbnail, and slideshow images from the sources.
	 * 
	 * @param   array   $item
	 * @param   array   $images
	 * @param   object  $field_data
	 * @param   bool    $unique_filename
	 * 
	 * @return  void
	 */
	public static function generateFromSource(&$item, $images = [], $field_data = [], $unique_filename = true)
	{
		$ds = DIRECTORY_SEPARATOR;
		$provider = $field_data->get('provider', 'grid');

		/**
		 * Clean up of unneeded images.
		 */
		self::cleanUpImages($item, $field_data);

		$full_source_path = implode($ds, [JPATH_ROOT, $item['source']]);

		// Create full image
		if ($images['full'])
		{
			$full_image_path = implode($ds, [JPATH_ROOT, dirname(dirname($item['source'])), 'full', basename($item['source'])]);
			$full_image_resizing_dimensions = self::getImageResizingDimensions('full_image', $field_data);
			if (!empty(array_filter($full_image_resizing_dimensions)))
			{
				// Create the full image directory if not exists
				File::createDirs(dirname($full_image_path));
		
				// Resize the full image
				$full_image_path = Image::resizeByWidthOrHeight($full_source_path, $full_image_resizing_dimensions['width'], $full_image_resizing_dimensions['height'], 70, $full_image_path, 'crop', $unique_filename);
	
				$item['image'] = ltrim(rtrim(str_replace(JPATH_ROOT, '', $full_image_path), $ds), $ds);
			}
			else if ($field_data->get('full_image.by', '') === 'disabled')
			{
				// Create the full image directory if not exists
				File::createDirs(dirname($full_image_path));
		
				// Copy image to full folder
				$full_image_path = File::copy($full_source_path, $full_image_path, !$unique_filename);

				$item['image'] = ltrim(rtrim(str_replace(JPATH_ROOT, '', $full_image_path), $ds), $ds);
			}
		}

		// Create thumbnail image
		if ($images['thumb'])
		{
			$thumbs_folder_name = $provider === 'slideshow' ? 'thumb' : '';
			$thumbnail_image_path = implode($ds, array_filter([JPATH_ROOT, dirname(dirname($item['source'])), $thumbs_folder_name, basename($item['source'])]));
			$thumbnail_resizing_dimensions = [
				'width' => null,
				'height' => null
			];
	
			if ($provider === 'justified')
			{
				$thumbnail_resizing_dimensions['height'] = $field_data->get('justified_item_height', 200);
			}
			else if ($provider === 'masonry')
			{
				$thumbnail_resizing_dimensions['width'] = $field_data->get('masonry_thumbnails_width', 200);
			}
			else
			{
				$thumbnail_size = $provider === 'slideshow' ? $field_data->get('slideshow_thumbnail_size', 200) : $field_data->get('thumbnail_size', 200);
	
				$thumbnail_resizing_dimensions = [
					'width' => $thumbnail_size,
					'height' => $thumbnail_size
				];
	
				// If slideshow, require show_thumbnails to be enabled
				if ($provider === 'slideshow' && $field_data->get('show_thumbnails', '0') === '0')
				{
					$thumbnail_resizing_dimensions = null;
				}
			}

			if ($thumbnail_resizing_dimensions)
			{
				// Create the full image directory if not exists
				File::createDirs(dirname($thumbnail_image_path));
		
				// Resize the full image
				$thumbnail_image_path = Image::resizeByWidthOrHeight($full_source_path, $thumbnail_resizing_dimensions['width'], $thumbnail_resizing_dimensions['height'], 70, $thumbnail_image_path, 'crop', $unique_filename);
				
				$item['thumbnail'] = ltrim(rtrim(str_replace(JPATH_ROOT, '', $thumbnail_image_path), $ds), $ds);
			}
		}

		// Create slideshow image
		if ($images['slideshow'] && $provider === 'slideshow')
		{
			$slideshow_image_path = implode($ds, [JPATH_ROOT, dirname(dirname($item['source'])), basename($item['source'])]);
			if ($slideshow_resizing_dimensions = self::getImageResizingDimensions('slideshow_image', $field_data))
			{
				// Create the full image directory if not exists
				File::createDirs(dirname($slideshow_image_path));
		
				// Resize the full image
				$slideshow_image_path = Image::resizeByWidthOrHeight($full_source_path, $slideshow_resizing_dimensions['width'], $slideshow_resizing_dimensions['height'], 70, $slideshow_image_path, 'crop', $unique_filename);

				$item['slideshow'] = ltrim(rtrim(str_replace(JPATH_ROOT, '', $slideshow_image_path), $ds), $ds);
			}
		}

		self::applyWatermarkOnImages($item, $images, $field_data);
	}

	/**
	 * Applies the watermark on the images.
	 * 
	 * @param   array   $payload
	 * @param   array   $images
	 * @param   object  $field_data
	 * 
	 * @return  void
	 */
	public static function applyWatermarkOnImages($payload = [], $images = [], $field_data = [])
	{
		if ($field_data->get('watermark.type', 'disabled') === 'disabled')
		{
			return;
		}

		$ds = DIRECTORY_SEPARATOR;

		$watermarkSettings = (array) $field_data->get('watermark', []);

		$apply_on_thumbnails = $watermarkSettings['apply_on_thumbnails'] === '1';

		$watermarkSettings = array_merge($watermarkSettings, [
			'image' => !empty($watermarkSettings['image']) ? explode('#', JPATH_SITE . DIRECTORY_SEPARATOR . $watermarkSettings['image'])[0] : null,
		]);

		if ($images['full'])
		{
			// Add watermark to full image
			$watermarkPayload = array_merge($watermarkSettings, [
				'source' => implode($ds, [JPATH_ROOT, $payload['image']])
			]);
			\NRFramework\Image::applyWatermark($watermarkPayload);
		}

		if ($images['slideshow'])
		{
			// Add watermark to slideshow image
			if ($field_data->get('provider', 'grid') === 'slideshow')
			{
				$watermarkPayload = array_merge($watermarkSettings, [
					'source' => implode($ds, [JPATH_ROOT, $payload['slideshow']])
				]);
				\NRFramework\Image::applyWatermark($watermarkPayload);
			}
		}

		if ($apply_on_thumbnails && $images['thumb'])
		{
			// Add watermark to thumbnail
			$watermarkPayload = array_merge($watermarkSettings, [
				'source' => implode($ds, [JPATH_ROOT, $payload['thumbnail']])
			]);
			\NRFramework\Image::applyWatermark($watermarkPayload);
		}
	}

	public static function getImageResizingDimensions($key, $field_data)
	{
		$width = $height = null;

		if (in_array($field_data->get($key . '.by', ''), ['width', 'custom']))
		{
			$width = $field_data->get($key . '.width', null);
		}
		if (in_array($field_data->get($key . '.by', ''), ['height', 'custom']))
		{
			$height = $field_data->get($key . '.height', null);
		}

		$data = array_filter([
			'width' => $width,
			'height' => $height
		]);

		if (!isset($data['width']))
		{
			$data['width'] = null;
		}

		if (!isset($data['height']))
		{
			$data['height'] = null;
		}
		
		return $data;
	}

	/**
	 * Saves the tags for each item.
	 * 
	 * @param   array  $value
	 * 
	 * @return  array
	 */
	public static function saveItemTags($value = [])
	{
		if (!is_array($value))
		{
			return $value;
		}

		foreach ($value as &$item)
		{
			if (!isset($item['tags']) || !is_string($item['tags']))
			{
				$item['tags'] = [];
				continue;
			}

			if (!$itemTags = json_decode($item['tags'], true))
			{
				$item['tags'] = [];
				continue;
			}

			if (!is_array($itemTags))
			{
				$item['tags'] = [];
				continue;
			}

			if (!$itemTags)
			{
				$item['tags'] = [];
				continue;
			}

			// Make $itemTags an array of strings
			$itemTags = array_map(function($tag) {
				return (string) $tag;
			}, $itemTags);

			/**
			 * Creates the new tags in the #__tags table.
			 * 
			 * This returns an array of the new tag ids. If a tag isn't new (doesn't have #new# prefix), it will return 0 as its id.
			 * 
			 * We will now store the IDs returned as the tags for the item.
			 */
			$item['tags'] = self::createTagsFromField($itemTags);
		}

		return $value;
	}

    /**
     * Create any new tags by looking for #new# in the strings
     *
     * @param   array  $tags  Tags text array from the field
     *
     * @return  mixed   If successful, metadata with new tag titles replaced by tag ids. Otherwise false.
     *
     * @since   3.1
     */
    public static function createTagsFromField($tags)
    {
        if (empty($tags) || $tags[0] == '')
		{
            return;
        }

		// We will use the tags table to store them
		if (defined('nrJ4'))
		{
			$tagTable = Factory::getApplication()->bootComponent('com_tags')->getMVCFactory()->createTable('Tag', 'Administrator');
		}
		else
		{
			Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tags/tables');
			$tagTable = Table::getInstance('Tag', 'TagsTable');
		}

		$newTags = [];

		foreach ($tags as $key => $tag)
		{
			// Remove the #new# prefix that identifies new tags
			$tagText = str_replace('#new#', '', $tag);

			if ($tagText === $tag)
			{
				$newTags[] = (int) $tag;
			}
			else
			{
				// Clear old data if exist
				$tagTable->reset();

				// Try to load the selected tag
				if ($tagTable->load(['title' => $tagText]))
				{
					$newTags[] = (int) $tagTable->id;
				}
				else
				{
					// Prepare tag data
					$tagTable->id          = 0;
					$tagTable->title       = $tagText;
					$tagTable->published   = 1;
					$tagTable->description = '';

					$tagTable->language = '*';
					$tagTable->access   = 1;

					// Make this item a child of the root tag
					$tagTable->setLocation($tagTable->getRootId(), 'last-child');

					// Try to store tag
					if ($tagTable->check())
					{
						// Assign the alias as path (autogenerated tags have always level 1)
						$tagTable->path = $tagTable->alias;

						if ($tagTable->store())
						{
							$newTags[] = (int) $tagTable->id;
						}
					}
				}
			}
		}

		// At this point $newTags is an array of all tag ids
        return $newTags;
    }

	/**
	 * Sets the custom field item id > field id value "source" to given source image path for the original image path
	 */
	public static function setItemFieldSource($item_id, $field_id, $sourceImagePath, $originalImagePath)
	{
		// Get "value" column from #__fields_values where item_id = $item_id and $field_id = $field_id
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn('value'))
			->from($db->qn('#__fields_values'))
			->where($db->qn('item_id') . ' = ' . $db->q($item_id))
			->where($db->qn('field_id') . ' = ' . $db->q($field_id));

		$db->setQuery($query);

		$value = $db->loadResult();

		// If value is empty, return
		if (!$value)
		{
			return;
		}

		// Decode value
		$value = json_decode($value, true);

		// If value is empty, return
		if (!$value)
		{
			return;
		}

		// If value is not an array, return
		if (!is_array($value))
		{
			return;
		}

		// If value has no items, return
		if (!isset($value['items']))
		{
			return;
		}

		foreach ($value['items'] as $key => &$item)
		{
			if ($item['image'] !== $originalImagePath)
			{
				continue;
			}

			$item['source'] = $sourceImagePath;
		}

		// Update value
		$query = $db->getQuery(true)
			->update($db->qn('#__fields_values'))
			->set($db->qn('value') . ' = ' . $db->q(json_encode($value)))
			->where($db->qn('item_id') . ' = ' . $db->q($item_id))
			->where($db->qn('field_id') . ' = ' . $db->q($field_id));

		$db->setQuery($query);

		$db->execute();
	}
	
	/**
	 * Media Uploader files look like: https://example.com/images/sampledata/parks/banner_cradle.png
	 * We remove the first part (https://example.com/images/) and keep the other part (relative path to image).
	 * 
	 * @param   string  $filename
	 * 
	 * @return  string
	 */
	private static function getFilePathFromMediaUploaderFile($filename)
	{
		$filenameArray = explode('images/', $filename, 2);
		unset($filenameArray[0]);
		$new_filepath = join($filenameArray);
		return 'images/' . $new_filepath;
	}

	/**
	 * Deletes an uploaded files: source, slideshow, original, and thumbnail.
	 *
	 * @param   string  $source			The source image path.
	 * @param   string  $slideshow		The slideshow image path.
	 * @param   string  $original		The original image path.
	 * @param   string  $thumbnail		The thumbnail image path.
	 *
	 * @return  bool
	 */
	public static function deleteFile($source = null, $slideshow = null, $original = null, $thumbnail = null)
	{
		return [
			'deleted_source_image' => self::findAndDeleteFile($source),
			'deleted_slideshow_image' => self::findAndDeleteFile($slideshow),
			'deleted_full_image' => self::findAndDeleteFile($original),
			'deleted_thumbnail' => self::findAndDeleteFile($thumbnail)
		];
	}

	/**
	 * Deletes the file.
	 * 
	 * @param   string  $filepath
	 * 
	 * @return  mixed
	 */
	private static function findAndDeleteFile($filepath)
	{
		if (!$filepath)
		{
			return;
		}
		
		$file = Path::clean(implode(DIRECTORY_SEPARATOR, [JPATH_ROOT, $filepath]));

		return is_file($file) ? JoomlaCMSFile::delete($file) : false;
	}

	/**
	 * Cleans the temp folder.
	 * 
	 * Removes any image that is 1 day or older.
	 * 
	 * @return  void
	 */
	public static function clean()
	{
		$temp_folder = self::getFullTempFolder();
		
		if (!is_dir($temp_folder))
		{
			return;
		}

		// Get images
		$files = array_diff(scandir($temp_folder), ['.', '..', '.DS_Store', 'index.html']);

		$found = [];

		foreach ($files as $key => $filename)
		{
			$file_path = implode(DIRECTORY_SEPARATOR, [$temp_folder, $filename]);
			
			// Skip directories
			if (is_dir($file_path))
			{
				continue;
			}

			$diff_in_miliseconds = time() - filemtime($file_path);

			// Skip the file if it's not old enough
			if ($diff_in_miliseconds < (60 * 60 * 24 * self::$temp_files_cleanup_days))
			{
				continue;
			}

			$found[] = $file_path;
		}

		if (!$found)
		{
			return;
		}

		// Delete found old files
		foreach ($found as $file)
		{
			unlink($file);
		}
	}

	/**
	 * Full temp directory where images are uploaded
	 * prior to them being saved in the final directory.
	 * 
	 * @param   string  $context
	 * @param   string  $field_id
	 * @param   string  $item_id
	 * 
	 * @return  string
	 */
	public static function getFullTempFolder($context = 'default', $field_id = '', $item_id = '')
	{
		$tmpdir = Factory::getConfig()->get('tmp_path');

		$paths = [
			$tmpdir,
			'tassos',
			\NRFramework\VisitorToken::getInstance()->get(),
			$context === 'module' ? 'smilepack' : 'acf',
			'gallery',
			$item_id,
			$field_id
		];

		$paths = array_filter($paths);
		
		return implode(DIRECTORY_SEPARATOR, $paths);
	}

	/**
	 * Deletes a specific tag from every gallery item.
	 * 
	 * @param   int     $tag_id
	 * @param   string  $context
	 * 
	 * @return  void
	 */
	public static function deleteTagFromFieldsValues($tag_id = null, $context = '')
	{
		if (!$tag_id)
		{
			return;
		}

		if ($context === '')
		{
			self::deleteTagFromCustomFieldsByTagId($tag_id);
			self::deleteTagFromSubformCustomFieldsByTagId($tag_id);
		}
	}

	/**
	 * Deletes a specific tag from every gallery item custom field.
	 * 
	 * @param   int  $tag_id
	 * 
	 * @return  void
	 */
	private static function deleteTagFromCustomFieldsByTagId($tag_id = null)
	{
		if (!$tag_id)
		{
			return;
		}

		$db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('f.id as field_id, fv.item_id as item_id, fv.value as value')
            ->from('#__fields as f')
            ->join('LEFT', '#__fields_values AS fv ON fv.field_id = f.id')
            ->where('f.type = ' . $db->quote('acfgallery'));

        $db->setQuery($query);

        $fields = $db->loadAssocList();

		if (!$fields)
		{
			return;
		}

        foreach ($fields as $field)
        {
            if (!$decoded_value = json_decode($field['value'], true))
            {
                continue;
            }

            if (!isset($decoded_value['items']))
            {
                continue;
            }

            $update = false;

            foreach ($decoded_value['items'] as &$item)
            {
                if (!isset($item['tags']))
                {
                    continue;
                }

                if (!is_array($item['tags']))
                {
                    continue;
                }

                if (!count($item['tags']))
                {
                    continue;
                }

                $item['tags'] = array_values($item['tags']);

                if (($key = array_search($tag_id, $item['tags'])) !== false)
                {
                    $update = true;
                    unset($item['tags'][$key]);
                }

                $item['tags'] = array_values($item['tags']);
            }

            if (!$update)
            {
				continue;
			}
		
			$field['value'] = json_encode($decoded_value);

			// Update field value
			$query->clear()
				->update('#__fields_values')
				->set($db->quoteName('value') . ' = ' . $db->quote($field['value']))
				->where($db->quoteName('field_id') . ' = ' . $db->quote($field['field_id']))
				->where($db->quoteName('item_id') . ' = ' . $db->quote($field['item_id']));
			$db->setQuery($query);
			$db->execute();
        }
	}

	/**
	 * Deletes a specific tag from every gallery item that exists in a subform custom field.
	 * 
	 * @param   int  $tag_id
	 * 
	 * @return  void
	 */
	private static function deleteTagFromSubformCustomFieldsByTagId($tag_id = null)
	{
		if (!$tag_id)
		{
			return;
		}

		if (!$tag_id)
		{
			return;
		}

		$db = Factory::getDbo();

		// Get all ACF Gallery custom field IDs
		$query = $db->getQuery(true)
			->select('distinct f.id')
			->from('#__fields as f')
			->join('LEFT', '#__fields_values AS fv ON fv.field_id = f.id')
			->where('f.type = ' . $db->quote('acfgallery'));
			$db->setQuery($query);
		$gallery_field_ids = array_keys($db->loadAssocList('id'));

		if (!$gallery_field_ids)
		{
			return;
		}
		
		// Get all Subform custom fields
        $query->clear()
            ->select('f.id as field_id, fv.item_id as item_id, fv.value as value')
            ->from('#__fields as f')
            ->join('LEFT', '#__fields_values AS fv ON fv.field_id = f.id')
            ->where('f.type = ' . $db->quote('subform'));
        $db->setQuery($query);
        $subform_fields = $db->loadAssocList();

		foreach ($subform_fields as $subform_field)
		{
			if (!$subform_field_items = json_decode($subform_field['value'], true))
			{
				continue;
			}

			$update = false;

			foreach ($subform_field_items as $row => &$row_items)
			{
				if (!is_array($row_items))
				{
					continue;
				}

				foreach ($row_items as $field_name => &$field_value)
				{
					// Get the field id
					$field_id = str_replace('field', '', $field_name);
					
					// Check if its a gallery field
					if (!in_array($field_id, $gallery_field_ids))
					{
						continue;
					}

					if (!isset($field_value['items']))
					{
						continue;
					}

					foreach ($field_value['items'] as &$item)
					{
						if (!isset($item['tags']))
						{
							continue;
						}

						if (!is_array($item['tags']))
						{
							continue;
						}

						if (!count($item['tags']))
						{
							continue;
						}

						$item['tags'] = array_values($item['tags']);

						if (($key = array_search($tag_id, $item['tags'])) !== false)
						{
							$update = true;
							unset($item['tags'][$key]);
						}

						$item['tags'] = array_values($item['tags']);
					}
				}
			}

			if (!$update)
			{
				continue;
			}
			
			$subform_field['value'] = json_encode($subform_field_items);

			// Update subform field value
			$query->clear()
				->update('#__fields_values')
				->set($db->quoteName('value') . ' = ' . $db->quote($subform_field['value']))
				->where($db->quoteName('field_id') . ' = ' . $db->quote($subform_field['field_id']))
				->where($db->quoteName('item_id') . ' = ' . $db->quote($subform_field['item_id']));
			$db->setQuery($query);
			$db->execute();
		}
	}
}