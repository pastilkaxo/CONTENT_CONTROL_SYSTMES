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

class Pagination extends SmartTag
{
	/**
	 * Returns the pagination links.
	 * Used in Convert Forms Front End Submissions View.
	 * 
	 * @return  string
	 */
	public function getLinks()
	{
		if (!isset($this->data['front_end_submission']['pagination']))
		{
			return '';
		}
		
		return $this->data['front_end_submission']['pagination']->getPagesLinks();
	}

	/**
	 * Returns the pagination counter.
	 * Used in Convert Forms Front End Submissions View.
	 * 
	 * @return  string
	 */
	public function getCounter()
	{
		if (!isset($this->data['front_end_submission']['pagination']))
		{
			return '';
		}
		
		return $this->data['front_end_submission']['pagination']->getPagesCounter();
	}

	/**
	 * Returns the pagination results.
	 * Used in Convert Forms Front End Submissions View.
	 * 
	 * @return  string
	 */
	public function getResults()
	{
		if (!isset($this->data['front_end_submission']['pagination']))
		{
			return '';
		}
		
		return $this->data['front_end_submission']['pagination']->getResultsCounter();
	}
}