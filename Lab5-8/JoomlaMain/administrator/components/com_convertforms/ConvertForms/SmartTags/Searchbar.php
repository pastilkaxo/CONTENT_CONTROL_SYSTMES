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

class Searchbar extends SmartTag
{
	/**
	 * Returns the search bar.
	 * 
	 * @return  string
	 */
	public function getSearchbar()
	{
		if (!isset($this->data['front_end_submission']['searchbar']))
		{
			return '';
		}
		
		return $this->data['front_end_submission']['searchbar'];
	}
}