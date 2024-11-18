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

use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use NRFramework\Countries;

class Tel extends \ConvertForms\Field
{
	protected $inheritInputLayout = 'text';
	
	/**
	 *  Renders the field's input element
	 *
	 *  @return  string  	HTML output
	 */
	protected function getInput()
	{
		return parent::getInput();
	}

	

	/**
	 * Prepare value to be displayed to the user as HTML/text
	 *
	 * @param   mixed   $value
	 *
	 * @return  string
	 */
	public function prepareValueHTML($value)
	{
		$value = $this->prepareValue($value);

		
		
		return '<a href="tel:' . $value . '">' . $value . '</a>';
	}
}