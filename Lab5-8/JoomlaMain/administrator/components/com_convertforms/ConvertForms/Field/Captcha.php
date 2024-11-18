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

use Joomla\CMS\Factory;
use NRFramework\Executer;
use Joomla\CMS\Language\Text as JoomlaText;

defined('_JEXEC') or die('Restricted access');

class Captcha extends Text
{
	/**
	 *  Exclude all common fields
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = [
		'name',
		'required',
		'size',
		'value',
		'placeholder',
		'browserautocomplete',
		'inputcssclass'
	];

	/**
	 *  Set field object
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
		parent::setField($field);

		// Once we start calling $this->setField() in the constructo, we can get rid of this line.
		$this->field->required = true;

		$complexity = isset($this->field->complexity) ? $this->field->complexity : '';

		switch ($complexity)
		{
			case 'high':
				$min = 1;
				$max = 30;
				$comparators = ['+', '-', '*'];
				break;

			case 'medium':
				$min = 1;
				$max = 20;
				$comparators = ['+', '-'];
				break;

			// low
			default: 
				$min = 1;
				$max = 10;
				$comparators = ['+'];
		}

		// Pick random numbers
		$number1 = rand($min, $max);
		$number2 = rand($min, $max);

		// Pick a random math comparison operator
		shuffle($comparators);
		$comparator = end($comparators);
		
		// Calculate the Captcha answer
		$equation = "return ($number1 $comparator $number2)";
		$executer = new Executer($equation);
		$solution = $executer->run();

		// Pass data to template
		$this->field->question = [
			'number1'    => $number1,
			'number2'    => $number2,
			'comparator' => $comparator,
			'solution'   => md5($solution . '_' . $field['key']),
		];

		return $this;
	}

	/**
	 *  Validate field value
	 *
	 *  @param   mixed  $value           The field's value to validate
	 *
	 *  @return  mixed                   True on success, throws an exception on error
	 */
	public function validate(&$value)
	{
		// In case this is a submission via URL, skip the check.
		if (Factory::getApplication()->input->get('task') == 'optin')
		{
			return true;
		}

		$field = $this->getField();

		// The md5-ed solution
		$math_solution = $this->data[md5($field->key)];

		// Once we start calling $this->setField() in the constructor we can easily find the field's name by using $this->field->name instead of relying on the submitted data.
		$user_solution = md5($this->data['captcha_' . $field->key] . '_' . $field->key);

		// In v3.2.9 we added an option to set the Wrong Answer Text in the Field Settings. In the previous version we were using a language strings instead. 
		// To prevnt breaking the user's form, we need to check whether the new option is available. Otherwise we fallback to the old language string.
		// We can get rid of compatibility check in a few months.
		$wrong_answer_text = isset($field->wrong_answer_text) && !empty($field->wrong_answer_text) ? $field->wrong_answer_text : JoomlaText::_('COM_CONVERTFORMS_FIELD_CAPTCHA_WRONG_ANSWER');

		if ($math_solution !== $user_solution)
		{
			$this->throwError($wrong_answer_text);
		}
	}

	/**
	 * Event fired before the field options form is rendered in the backend
	 *
	 * @param  object $form
	 *
	 * @return void
	 */
	protected function onBeforeRenderOptionsForm($form)
	{
		// Joomla does not support translating the default attribute in the XML.
		$form->setFieldAttribute('wrong_answer_text', 'default', JoomlaText::_($form->getFieldAttribute('wrong_answer_text', 'default')));
	}
}