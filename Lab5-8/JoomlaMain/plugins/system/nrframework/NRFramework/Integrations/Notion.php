<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Integrations;

defined('_JEXEC') or die;

class Notion extends Integration
{
	protected $endpoint = 'https://api.notion.com/v1';

	/**
	 * Create a new instance
	 * 
	 * @param array $options The service's required options
	 */
	public function __construct($options)
	{
		parent::__construct();

		$this->setKey($options);

		$this->options->set('headers.Authorization', 'Bearer ' . $this->key);
		$this->options->set('headers.Notion-Version', '2022-06-28');
	}
}