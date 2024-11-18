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

class Heading extends \ConvertForms\Field
{
	/**
	 * Indicates the default required behavior on the form
	 *
	 * @var bool
	 */
	protected $required = false; 

	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = array(
		'name',
		'placeholder',
		'browserautocomplete',
		'size',
		'required',
		'hidelabel',
		'inputcssclass',
		'value'
	);

	/**
	 * Prepare value to be displayed to the user as plain text
	 *
	 * @param  mixed $value
	 *
	 * @return string
	 */
	public function prepareValue($value)
	{
		return $this->field->get('label');
	}
}