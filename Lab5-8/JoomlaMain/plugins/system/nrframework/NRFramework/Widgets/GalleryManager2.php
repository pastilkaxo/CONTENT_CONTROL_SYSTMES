<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Widgets;

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Factory;
use NRFramework\Helpers\Widgets\GalleryManager2 as GalleryManagerHelper;
use NRFramework\Image;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

/**
 *  Gallery Manager
 */
class GalleryManager2 extends Widget
{
	/**
	 * Widget default options
	 *
	 * @var array
	 */
	protected $widget_options = [
		// The uploaded images
		'value' => [],
		
		// The input name
		'name' => '',

		// Context of the field
		// module, default
		'context' => 'default',

		// The field ID associated to this Gallery Manager, used to retrieve the field settings on AJAX actions
		'field_id' => null,

		// The item ID associated to this Gallery Manager, used to retrieve the field settings on AJAX actions
		'item_id' => null,

		/**
		 * Max file size in MB.
		 * 
		 * Defults to 0 (no limit).
		 */
		'max_file_size' => 0,

		/**
		 * How many files we can upload.
		 * 
		 * Defaults to 0 (no limit).
		 */
		'limit_files' => 0,

		// Allowed upload file types
		'allowed_file_types' => 'image/*',

		/**
		 * Original Image
		 */
		// Original image resize width
		'original_image_resize_width' => null,

		// Original image resize height
		'original_image_resize_height' => null,

		/**
		 * Thumbnails
		 */
		// Thumbnails width
		'thumb_width' => null,

		// Thumbnails height
		'thumb_height' => null,

		// Thumbnails resize method (crop, stretch, fit)
		'thumb_resize_method' => 'crop',

		// The list of tags already available for this gallery
		'tags' => [],

		// Open AI API Key
		'openai_api_key' => '',

		// The widget name
		'widget' => 'GalleryManager2'
	];

	public function __construct($options = [])
	{
		parent::__construct($options);

		$this->prepare();
	}

	private function prepare()
	{
		$this->includeTempFiles();
		
		// Set css class for readonly state
		if ($this->options['readonly'])
		{
			$this->options['css_class'] .= ' readonly';
		}

		// Adds a css class when the gallery contains at least one item
		if (is_array($this->options['value']) && count($this->options['value']))
		{
			$this->options['css_class'] .= ' dz-has-items';
		}

		// Get the Open AI API key
		$this->options['openai_api_key'] = \NRFramework\Helpers\Settings::getValue('openai_api_key');

		// Load translation strings
        Text::script('NR_GALLERY_MANAGER_CONFIRM_DELETE_ALL_SELECTED');
        Text::script('NR_GALLERY_MANAGER_CONFIRM_DELETE_ALL');
        Text::script('NR_GALLERY_MANAGER_CONFIRM_DELETE');
        Text::script('NR_GALLERY_MANAGER_FILE_MISSING');
        Text::script('NR_GALLERY_MANAGER_REACHED_FILES_LIMIT');
        Text::script('NR_GENERATE_IMAGE_DESC_TO_ALL_IMAGES_CONFIRM');

		$this->prepareTags();
	}

	/**
	 * Find and include temp files in the gallery.
	 * 
	 * @return  void
	 */
	private function includeTempFiles()
	{
		$ds = DIRECTORY_SEPARATOR;
		
		$tempFolder = GalleryManagerHelper::getFullTempFolder($this->options['context'], $this->options['field_id'], $this->options['item_id']);

		if (!is_dir($tempFolder))
		{
			return;
		}
		
		$files = Folder::files($tempFolder, '.', false, false, ['.', '..', 'index.html', 'index.php']);

		if (!$files)
		{
			return;
		}

		$relativeTempFolder = ltrim(str_replace(JPATH_ROOT, '', $tempFolder), $ds);

		foreach ($files as $filename)
		{
			$this->options['value'][] = [
                'source' => implode($ds, [$relativeTempFolder, $filename]),
                'original' =>'',
                'exists' => true,
                'caption' => '',
                'thumbnail' => '',
                'slideshow' => '',
                'alt' => '',
                'tags' => json_encode([]),
				'temp' => true
			];
		}
	}

	private function prepareTags()
	{
		if (!is_array($this->options['value']))
		{
			return;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select([$db->quoteName('id'), $db->quoteName('title')])
			->from($db->quoteName('#__tags'))
			->where($db->quoteName('published') . ' = 1')
			->where($db->quoteName('level') . ' > 0');

		$db->setQuery($query);
		$tags = $db->loadAssocList('id', 'title');

		$this->options['tags'] = $tags;
	}

	/**
	 * The upload task called by the AJAX hanler
	 *
	 * @return  void
	 */
	protected function ajax_upload()
	{
        // Increase memory size and execution time to prevent PHP errors on datasets > 20K
        set_time_limit(300); // 5 Minutes
        ini_set('memory_limit', '-1');
		
		$input = Factory::getApplication()->input;

		$random_suffix = $input->get('random_suffix', 'false') === 'true' ? true : false;

		// Make sure we have a valid context
		if (!$context = $input->get('context'))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_CONTEXT_ERROR');
		}

		// Make sure we have a valid file passed
		if (!$file = $input->files->get('file'))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_ERROR_INVALID_FILE');
		}

		// In case we allow multiple uploads the file parameter is a 2 levels array.
		$first_property = array_pop($file);
		if (is_array($first_property))
		{
			$file = $first_property;
		}

		$uploadSettings = [
			'context' => $context,
			'field_id' => $input->getInt('field_id'),
			'item_id' => $input->getInt('item_id'),
			'allow_unsafe' => false,
			'allowed_types' => $this->widget_options['allowed_file_types'],
			'random_suffix' => $random_suffix
		];

		// Upload the file and resize the images as required
		if (!$source = GalleryManagerHelper::upload($file, $uploadSettings))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_ERROR_CANNOT_UPLOAD_FILE');
		}

		echo json_encode([
			'source' => $source
		]);
	}

	/**
	 * The delete task called by the AJAX hanlder
	 *
	 * @return void
	 */
	protected function ajax_delete()
	{
        // Increase memory size and execution time to prevent PHP errors on datasets > 20K
        set_time_limit(300); // 5 Minutes
        ini_set('memory_limit', '-1');
		
		$input = Factory::getApplication()->input;

		// Get source image path.
		$source = $input->getString('source');

		// Get the slideshow image path.
		$slideshow = $input->getString('slideshow', '');

		// Get the original image
		$original = $input->getString('original');

		// Get the thumbnail image
		$thumbnail = $input->getString('thumbnail');

		if (!$context = $input->get('context'))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_CONTEXT_ERROR');
		}

		$field_id = $input->getInt('field_id');
		$item_id = $input->getInt('item_id');
		
		if (!$field_data = GalleryManagerHelper::getSettings($context, $field_id, $item_id))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_INVALID_FIELD_DATA');
		}

		// Delete the source, original, and thumbnail file
		$deleted = GalleryManagerHelper::deleteFile($source, $slideshow, $original, $thumbnail);
		
		echo json_encode(['success' => $deleted]);
	}
	
	public function ajax_generate_caption()
	{
        set_time_limit(300); // 5 Minutes
        ini_set('memory_limit', '-1');
		
		$fullURL = Uri::root() . Factory::getApplication()->input->getString('image');

		$imageToText = new \NRFramework\AI\TextGeneration\ImageToText();
		$generated = $imageToText->generate($fullURL);

		echo json_encode($generated);
	}

	/**
	 * Exits the page with given message.
	 * 
	 * @param   string  $translation_string
	 * 
	 * @return  void
	 */
	private function exitWithMessage($translation_string)
	{
		http_response_code('500');
		die(Text::_($translation_string));
	}
}