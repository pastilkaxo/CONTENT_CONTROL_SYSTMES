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

namespace ConvertForms\Field;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class Editor extends Textarea
{
	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = [
		'placeholder',
		'browserautocomplete',
		'size',
    ];

	/**
	 * Event fired before the field options form is rendered in the backend
	 *
	 * @param  object $form
	 *
	 * @return void
	 */
	protected function onAfterRenderOptionsForm(&$html)
	{
		// Remove the 'None' editor from dropdown options
		$html = str_replace('<option value="none">Editor - None</option>', '', $html);
	}

    /**
	 *  Renders the field's input element
	 *
	 *  @return  string  	HTML output
	 */
	protected function getInput()
	{
        $selected_editor = empty($this->field->editor) ? Factory::getConfig()->get('editor') : $this->field->editor;

		if (!$selected_editor)
		{
			return Text::sprintf('COM_CONVERTFORMS_EDITOR_NOT_FOUND', $selected_editor);
		}

        // Instantiate the editor
        $editor = \Joomla\CMS\Editor\Editor::getInstance($selected_editor);
        
        $id 	  = $this->field->input_id;
        $name 	  = $this->field->input_name;
        $contents = htmlspecialchars($this->field->value, ENT_COMPAT, 'UTF-8');
        $width	  = '100%';
        $height   = (int) $this->field->height;
        $row 	  = 1;
        $col 	  = 10;
        $buttons  = false;
        $author   = null;
        $asset	  = null;
        $params = [
            'readonly' => $this->field->readonly,
        ];
        
        $this->field->richeditor = $editor->display($name, $contents, $width, $height, $col, $row, $buttons, $id, $author, $asset, $params);

		return parent::getInput();
	}

	/**
	 * Return the HTML version of the submitted value.
	 *
	 * @param  string $value
	 * 
	 * @return string
	 */
	public function prepareValueHTML($value)
	{
		// Editor's value is already in HTML format in the database
		return $value;
	}
}