<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */


defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;

class JFormFieldPersonTypes extends ListField
{
	/**
	 * Dropdown options array
	 *
	 * @var  array
	 */
	private $options;

	/**
	 * Schema.org Person Types
	 * https://schema.org/Person#incoming
	 *
	 * @var  array
	 */
	private $personTypes = array(
		'Person',
		'Actor',
		'AdultEntertainment',
		'Architect',
		'Athlete',
		'Author',
		'BusinessPerson',
		'Chef',
		'Comedian',
		'Dentist',
		'Designer',
		'Director',
		'Doctor',
		'Engineer',
		'Entrepreneur',
		'FashionDesigner',
		'FilmDirector',
		'Investor',
		'Journalist',
		'MusicComposer',
		'MusicGroup',
		'Musician',
		'Painter',
		'Photographer',
		'Politician',
		'Scientist',
		'Singer',
		'SoftwareDeveloper',
		'SportsTeam',
		'Writer'
	);

	/**
	 * Returns all options to dropdown field
	 *
	 * @return  array
	 */
	protected function getOptions()
	{
		return array_merge(parent::getOptions(), $this->buildTree($this->personTypes));
	}

	/**
	 * Return the choices.
	 *
	 * @param   array $types
	 *
	 * @return  array
	 */
	private function buildTree($types)
	{
		foreach ($types as $key => $type)
		{
			$this->options[] = array(
				'value'    => $type,
				'text'     => $type,
				'selected' => ($this->value == $type)
			);
		}

		return $this->options;
	}
	
    protected function getInput()
    {
        return '<div class="d-flex gap-1"> ' . parent::getInput() . '</div>';
    }
}