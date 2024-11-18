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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 *  Conversions Class
 */
class ConvertFormsModelConversions extends ListModel
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
                'created', 'a.created',
                'search',
                'campaign_id', 'a.campaign_id',
                'form_id', 'a.form_id',
                'created_from', 'created_to',
                'columns', 'a.columns',
                'period', 'a.period'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = 'a.id', $direction = 'desc')
    {
        // Get the previously set form ID before populating the State.
		$session = Factory::getSession();
		$registry = $session->get('registry');
        $previous_form_id = $registry->get($this->context . '.filter.form_id');

        // List state information.
        parent::populateState($ordering, $direction);

        $state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
        $this->setState('filter.state', $state);
        
        $formID = $this->getUserStateFromRequest($this->context . '.filter.form_id', 'filter_form_id', $this->getLastFormID());
        $this->setState('filter.form_id', $formID);
        
        $campaignID = $this->getUserStateFromRequest($this->context . '.filter.campaign_id', 'filter_campaign_id');
        $this->setState('filter.campaign_id', $campaignID);

        $period = $this->getUserStateFromRequest($this->context . '.filter.period', 'filter_period');
        $this->setState('filter.period', $period);
        
        $columns = $this->getUserStateFromRequest($this->context . '.filter.columns', 'filter_columns');
        $columns = is_array($columns) ? array_filter($columns) : (array) $columns;

        $sameForm = !is_null($previous_form_id) ? $previous_form_id == $formID : true;

        // Get form fields from the database when the user has switched to another form using
        // the search filters or when the filters frorm has been reset.
        if (!$sameForm || empty($columns))
        {
            $columns = \ConvertForms\Helper::getColumns($formID);

            // Pre-select the first 8 only
            $columns = array_slice($columns, 0, 8);
        }

        $this->setState('filter.columns', $columns);
    }

    /**
	 * Allows preprocessing of the JForm object.
	 *
	 * @param   JForm   $form   The form object
	 * @param   array   $data   The data to be merged into the form object
	 * @param   string  $group  The plugin group to be executed
	 *
	 * @return  void
	 *
	 * @since    3.6.1
	 */
	protected function preprocessForm(Joomla\CMS\Form\Form $form, $data, $group = 'content')
	{
        if (!isset($data->filter))
        {
            $data->filter = [];
        } else 
        {
            if (is_object($data->filter))
            {
                $data->filter = (array) $data->filter;
            }
        }

        $data->filter['form_id'] = $this->getState('filter.form_id');

        $columns = $this->getState('filter.columns');

        $data->filter['columns'] = $columns;

		parent::preprocessForm($form, $data, $group);
	}

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    public function getListQuery()
    {
        // Create a new query object.
        $db = Factory::getDBO();
        $query = $db->getQuery(true);

        // Select some fields from the item table
        $query
            ->select('a.*')
            ->from('#__convertforms_conversions a');
        
        // Filter State
        $filter = $this->getState('filter.state');

        if ($filter !== 'skip')
        {
            if (is_numeric($filter))
            {
                $query->where($db->quoteName('a.state') . ' = ' . (int) $filter);
            }

            if (is_array($filter))
            {
                $query->where('(' . $db->quoteName('a.state') . ' IN (' . implode(',', $filter) . '))');
            }

            if (is_null($filter) || $filter == '')
            {
                $query->where('(a.state = 0 OR a.state = 1)');
            }
        }

        // Join Forms Table
        if ($this->getState('filter.join_forms') != 'skip')
        {
            $query->select("c.name as form_name");
            $query->join('LEFT', $db->quoteName('#__convertforms', 'c') . ' ON 
                (' . $db->quoteName('a.form_id') . ' = ' . $db->quoteName('c.id') . ')');
        }

        // Filter the list over the search string if set.
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $ids = str_replace('id:', '', $search);
                $ids = array_map('trim', explode(',', $ids));

                $query->where('a.id IN (' . implode(', ', (array) $ids) . ')');
            } else 
            {
                $query->where($db->quoteName('a.params') . ' LIKE ' . $db->q('%' . $search . '%'));
            }
        }

        // Filter by ID
        if ($id = $this->getState('filter.id'))
        {
            $query->where('a.id IN (' . implode(', ', (array) $id) . ')');
        }

        // Filter by Campaign ID
        if ($campaign_id = $this->getState('filter.campaign_id'))
        {
            $query->where('a.campaign_id IN (' . implode(', ', (array) $campaign_id) . ')');
        }

        // Filter Form
        if ($form_id = $this->getState('filter.form_id'))
        {
            $query->where('a.form_id = ' . $form_id);
        }

        // Period
        if ($period = $this->getState('filter.period'))
        {
            switch ($period)
            {
                case 'today':
                    $date_from = 'now';
                    $date_to = 'now';
                    break;
                    
                case 'yesterday':
                    $date_from = '-1 day';
                    $date_to = '-1 day';
                    break;

                case 'this_week':
                    $date_from = 'monday this week';
                    $date_to = 'sunday this week';
                    break;

                case 'this_month':
                    $date_from = 'first day of this month';
                    $date_to = 'last day of this month';
                    break;

                case 'this_year':
                    $date_from = 'first day of January';
                    $date_to = 'last day of December';
                    break;

                case 'last_week':
                    $date_from = 'monday previous week';
                    $date_to = 'sunday previous week';
                    break;

                case 'last_month':
                    $date_from = 'first day of previous month';
                    $date_to = 'last day of previous month';
                    break;

                case 'last_year':
                    $date_from = 'first day of January ' . (date('Y') - 1);
                    $date_to = 'last day of December ' . (date('Y') - 1);
                    break;

                default:
                    $date_from = $this->getState('filter.created_from');
                    $date_to = $this->getState('filter.created_to');
                    break;
            }

            // MySQL optimizer won't use an index once a column in the WHERE clause is wrapped with a function.
            // So we should never never never use MONTH(), YEAR(), DATE() methods again if we do care about performance.
            if ($date_from)
            {
                $query->where($db->quoteName('a.created') . ' >= ' . $db->q(HTMLHelper::date($date_from, 'Y-m-d 00:00:00', null)));
            }

            if ($date_to)
            {
                $query->where($db->quoteName('a.created') . ' <= ' . $db->q(HTMLHelper::date($date_to, 'Y-m-d 23:59:59', null)));
            }
        }

        // Filter User
        $filter_user = $this->getState('filter.user_id');

        if ($filter_user != '')
        {
            if (is_numeric($filter_user))
            {
                $query->where($db->quoteName('a.user_id') . ' = ' . $filter_user);
            } else 
            {
                $query->where('(' . $db->quoteName('a.state') . ' IN (' . $filter_user . '))');
            }
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
        };

        BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/models', 'ConvertFormsModel');
        $submission_model = BaseDatabaseModel::getInstance('Conversion', 'ConvertFormsModel', ['ignore_request' => true]);

        foreach ($items as $key => $item)
        {
            $items[$key]->params = json_decode($item->params);

            $item->created = \NRFramework\Functions::applySiteTimezoneToDate($item->created);

            $submission_model->prepare($item);
        }


        return $items;
    }
    
    private function getLastFormID()
    {
        $model = BaseDatabaseModel::getInstance('Forms', 'ConvertFormsModel', ['ignore_request' => true]);
        $model->setState('list.limit', 1);
        $model->setState('list.direction', 'asc');

        $forms = $model->getItems();

        return empty($forms) ? null : $forms[0]->id;
    }
}