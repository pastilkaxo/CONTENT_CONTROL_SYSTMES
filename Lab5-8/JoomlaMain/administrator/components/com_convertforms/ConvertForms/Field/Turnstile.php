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

use \ConvertForms\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

class Turnstile extends \ConvertForms\Field
{
	/**
	 *  Exclude all common fields
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = array(
		'name',
		'required',
		'size',
		'value',
		'placeholder',
		'browserautocomplete',
		'inputcssclass'
	);

	/**
	 *  Set field object
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
		parent::setField($field);

		$this->field->required = true;

		return $this;
	}

	/**
	 *  Get the Cloudflare Turnstile Site Key used in Javascript code
	 *
	 *  @return  string
	 */
	public function getSiteKey()
	{
		return Helper::getComponentParams()->get('turnstile_sitekey');
	}

	/**
	 *  Get the Cloudflare Turnstile Secret Key used in communication between the website and the Cloudflare Turnstile server
	 *
	 *  @return  string
	 */
	public function getSecretKey()
	{
		return Helper::getComponentParams()->get('turnstile_secretkey');
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

        $integration = new \NRFramework\Integrations\Turnstile(
            ['secret' => $this->getSecretKey()]
        );

		$response = isset($this->data['cf-turnstile-response']) ? $this->data['cf-turnstile-response'] : null;

        $integration->validate($response);

        if (!$integration->success())
        {
            throw new \Exception($integration->getLastError());
        }
	}

	/**
	 *  Display a text before the form options
	 *
	 * 	@param   object  $form
	 *
	 *  @return  string  The text to display
	 */
	protected function getOptionsFormHeader($form)
	{
		if ($this->getSiteKey() && $this->getSecretKey())
		{
			return;
		}

		$url = Uri::base() . 'index.php?option=com_config&view=component&component=com_convertforms#turnstile';

		return 
			Text::_('COM_CONVERTFORMS_FIELD_RECAPTCHA_KEYS_NOTE') . 
			' <a onclick=\'window.open("' . $url . '", "cfrecaptcha", "width=1000, height=750");\' href="#">' 
				. Text::_("COM_CONVERTFORMS_FIELD_TURNSTILE_CONFIGURE") . 
			'</a>.';
	}
}