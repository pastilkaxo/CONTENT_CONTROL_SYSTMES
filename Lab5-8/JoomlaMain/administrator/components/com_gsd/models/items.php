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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

class GSDModelItems extends ListModel
{
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     *
     * @see        JController
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'state', 'a.state',
                'title', 'a.title',
                'created', 'a.created',
                'search',
                'ordering', 'a.ordering',
                'plugin', 'a.plugin',
                'contenttype', 'a.contenttype',
                'language', 'a.language',
                'targetpages'
            );
        }

        parent::__construct($config);
    }
   
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   3.0.1
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
        $this->setState('filter.language', $language);

        parent::populateState($ordering, $direction);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = Factory::getDBO();
        $query = $db->getQuery(true);

        // Select some fields from the item table
        $query
            ->select('a.*')
            ->from('#__gsd a');

        // Filter by search
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('a.id = ' . (int) substr($search, 3));
            }
            else
            {
                $query->where('a.title LIKE ' . $db->quote('%' . trim($search) . '%') . '');
            }
        }  

        // Filter State
        $state = $this->getState('filter.state');
        if (is_numeric($state))
        {
            $query->where('a.state = ' . (int) $state);
        }

        if ($state && strpos($state, ',') !== false)
        {
            $query->where('a.state IN (' . $state . ')');
        }

        if ($state == '')
        {
            $query->where('a.state IN (0,1,2)');
        }

        // Filter Content Type
        if ($thing = $this->getState('filter.contenttype'))
        {
            $query->where('a.contenttype = ' . $db->q($thing));
        }

        // Filter Plugin
        if ($plugin = $this->getState('filter.plugin'))
        {
            $query->where('a.plugin = ' . $db->q($plugin));
        }

        // Filter Publishing Rules
        if ($targetpages = $this->getState('filter.targetpages'))
        {
            $operator = ($targetpages == 'specific') ? 'LIKE' : 'NOT LIKE';
            $query->where($db->quoteName('a.params') . $operator . $db->q('%assignment_state":"1"%'));
        }

		// Filter on the App view
        if ($appview = $this->getState('filter.appview'))
		{
            $appview = is_array($appview) ? $appview : (array) $appview;
            $query->where('a.appview IN (' . implode(',', $db->q($appview)) . ')');
		}

		// Filter on the language.
        if ($language = $this->getState('filter.language'))
		{
            $language = is_array($language) ? $language : (array) $language;
            $query->where('a.language IN (' . implode(',', $db->q($language)) . ')');
		}
        
        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'desc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

    /**
     *  [getItems description]
     *
     *  @return  object
     */
    public function getItems()
    {
        if (!$items = parent::getItems())
        {
            return [];
        }

        $db = Factory::getDbo();

        foreach ($items as &$item)
        {
            $item = (object) array_merge((array) $item, (array) json_decode($item->params));

            // We don't join the #__languages Joomla core table in the getListQuery() method in order to prevent
            // the "Illegal mix of collations" MySQL error caused by different collations in the joined columns.
            if (Factory::getApplication()->isClient('administrator'))
            {
                $query = $db->getQuery(true)
                    ->select('title AS language_title, image AS language_image')
                    ->from('#__languages')
                    ->where('lang_code = ' . $db->q($item->language));

                $db->setQuery($query);

                $lang = $db->loadAssoc();

                $item->language_title = $lang ? $lang['language_title'] : null;
                $item->language_image = $lang ? $lang['language_image'] : null;
            }
        }

        return $items;
    }
}