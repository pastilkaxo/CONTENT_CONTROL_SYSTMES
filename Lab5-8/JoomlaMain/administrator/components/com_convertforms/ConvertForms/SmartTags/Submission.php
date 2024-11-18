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

namespace ConvertForms\SmartTags;

defined('_JEXEC') or die('Restricted access');

use NRFramework\SmartTags\SmartTag;
use Joomla\CMS\Language\Text;

// @To-do: Use fetchValue() to catch all scenarios instead of individual methods.
class Submission extends SmartTag
{
	/**
	 * Returns the submsission ID
	 * 
	 * @return  string
	 */
	public function getID()
	{
		return isset($this->data['submission']->id) ? $this->data['submission']->id : '';
	}

	/**
	 * Returns the submsission User ID
	 * 
	 * @return  string
	 */
	public function getUser_ID()
	{
		return isset($this->data['submission']->user_id) ? $this->data['submission']->user_id : '';
	}

	/**
	 * Returns the user name of the user submitted the form
	 * 
	 * @return  string
	 */
	public function getUser_UserName()
	{
		return isset($this->data['submission']->user_id) ? \NRFramework\User::get($this->data['submission']->user_id)->username : null;
	}

	/**
	 * Returns the submission created date
	 * 
	 * @return  string
	 */
	public function getCreated()
	{
		return isset($this->data['submission']->created) ? $this->data['submission']->created : '';
	}

	/**
	 * Returns the submission modified date
	 * 
	 * @return  string
	 */
	public function getModified()
	{
		return isset($this->data['submission']->modified) ? $this->data['submission']->modified : '';
	}

	/**
	 * Returns the submission created date
	 * 
	 * @return  string
	 */
	public function getDate()
	{
		return isset($this->data['submission']->created) ? $this->data['submission']->created : '';
	}

	/**
	 * Returns the submission campaign id
	 * 
	 * @return  string
	 */
	public function getCampaign_ID()
	{
		return isset($this->data['submission']->campaign_id) ? $this->data['submission']->campaign_id : '';
	}

	/**
	 * Returns the submission form id
	 * 
	 * @return  string
	 */
	public function getForm_ID()
	{
		return isset($this->data['submission']->form_id) ? $this->data['submission']->form_id : '';
	}

	/**
	 * Returns the submission visitor id
	 * 
	 * @return  string
	 */
	public function getVisitor_ID()
	{
		return isset($this->data['submission']->visitor_id) ? $this->data['submission']->visitor_id : '';
	}

	/**
	 * Returns the submission status
	 * 
	 * @return  string
	 */
	public function getStatus()
	{
		return isset($this->data['submission']->state) && (int) $this->data['submission']->state === 1 ? Text::_('COM_CONVERTFORMS_SUBMISSION_CONFIRMED') : Text::_('COM_CONVERTFORMS_SUBMISSION_UNCONFIRMED');
	}

	/**
	 * Returns the submission PDF
	 * 
	 * @return  string
	 */
	public function getPDF()
	{
		if (!isset($this->data['extra_data']['pdf']))
		{
			return '';
		}

		return $this->data['extra_data']['pdf'];
	}
}