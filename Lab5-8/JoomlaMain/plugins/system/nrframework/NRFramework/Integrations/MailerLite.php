<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Integrations;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

class MailerLite extends Integration
{
	/**
	 * Create a new instance
	 * @param array $options The service's required options
	 * @throws \Exception
	 */
	public function __construct($options)
	{
		parent::__construct();
		$this->setKey($options);
		$this->setEndpoint('https://connect.mailerlite.com/api');
		$this->options->set('headers.Authorization', 'Bearer ' . $this->key);
	}

	/**
	 *  Subscribes a user to a MailerLite Account
	 *
	 *  API Reference v3:
	 *  https://developers.mailerlite.com/docs/subscribers.html#create-upsert-subscriber
	 *
	 *  @param   string   $email     		  The user's email
	 *  @param   array    $fields    		  All the custom fields
	 *  @param   string   $groupIds  		  The Group IDs
	 *  @param   string   $subscriber_status  The subscriber status (active, unsubscribed, unconfirmed, bounced, junk)
	 *  @param   boolean  $update_existing    Whether to update the existing contact (Only in v3)
	 *
	 *  @return  boolean
	 */
	public function subscribe($email, $fields, $groupIds = [], $subscriber_status = '', $update_existing = true)
	{
		// Abort if we don't want to update existing subscribers and the subscriber already exists
		if (!$update_existing && $this->subscriberExists($email))
		{
			throw new \Exception(Text::_('NR_YOU_ARE_ALREADY_A_SUBSCRIBER'), 1);
		}
		
		$data = [
			'email'  => $email,
			'fields' => $fields
		];

		if ($subscriber_status)
		{
			$data['status'] = $subscriber_status;
		}

		if ($groupIds)
		{
			$data['groups'] = $groupIds;
		}

		$this->post('subscribers', $data);

		return true;
	}

	/**
	 * Check if a subscriber exists
	 * 
	 * @param   string   $email  The subscriber's email
	 * 
	 * @return  boolean
	 */
	private function subscriberExists($email = '')
	{
		if (!$email)
		{
			return false;
		}

		$response = $this->get('subscribers/' . $email);

		return isset($response['data']['email']) && $response['data']['email'] == $email;
	}

	/**
	 *  Returns all groups
	 *
	 *  API Reference v3:
	 *  https://developers.mailerlite.com/docs/groups.html#list-all-groups
	 *
	 *  @return  array
	 */
	public function getGroups()
	{
		$data = [
			'offset' => 0,
			'limit' => 50
		];

		$lists = [];

		$data = $this->get('groups', $data);

		// sanity check
		if (!isset($data['data']) || !is_array($data['data']))
		{
			return $lists;
		}

		foreach ($data['data'] as $key => $list)
		{
			$lists[] = [
				'id'   => $list['id'],
				'name' => $list['name']
			];
		}

		return $lists;
		
	}

	/**
	 *  Get the last error returned by either the network transport, or by the API.
	 *
	 *  API Reference:
	 *  https://developers.mailerlite.com/docs/#validation-errors
	 *
	 *  @return  string
	 */
	public function getLastError()
	{
		$body    = $this->last_response->body;

		return isset($body['message']) ? $body['message'] : 'An error has occurred.';
	}
}