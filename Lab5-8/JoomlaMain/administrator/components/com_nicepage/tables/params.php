<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

class PagesTableParams extends Table
{
    private $_s = '_';
    /**
     * Constructor
     *
     * @param DatabaseDriver $db Database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__nicepage_params', 'id', $db);
    }

    /**
     * Get params from table
     *
     * @return array
     */
    public function getParameters()
    {
        if (!$this->load(array('name' => 'com_nicepage'))) {
            $parameters = $this->getAll();
            $result = array();
            if (count($parameters) > 0) {
                $content = '';
                $extraParam = null;
                for ($i = 0; $i < count($parameters); $i++) {
                    $param = $extraParam ?: $parameters[$i];
                    $name = $param['name'];
                    $content .= $param['params'];

                    $nextIndex = $i + 1;
                    if ($nextIndex < count($parameters)) {
                        $nextParam = $parameters[$nextIndex];
                        $parts = explode($this->_s, $nextParam['name']);
                        if (count($parts) > 1 && filter_var($parts[1], FILTER_VALIDATE_INT) !== false) {
                            $extraParam = array(
                                'name' => $parts[0],
                                'params' => $nextParam['params'],
                            );
                            continue;
                        }
                    }

                    $result[$name] = json_decode($content);
                    $content = '';
                    $extraParam = null;
                }
            }
            return $result;
        }
        $registry = new Registry();
        $registry->loadString($this->params);
        return $registry->toArray();
    }

    /**
     * Save params to table
     *
     * @param array $params Editor params
     */
    public function saveParameters($params)
    {
        $maxLength = 500 * 1024; //500kb
        if (count($params) > 0) {
            $this->removeAll();
            foreach ($params as $key => $value) {
                $value = json_encode($value);
                $newParams = array($key => $value);
                if (strlen($value) > $maxLength) {
                    $newParams = array();
                    $parts = str_split($value, $maxLength);
                    foreach ($parts as $i => $part) {
                        $newParams[$i !== 0 ? ($key . $this->_s . $i) : $key] = $part;
                    }
                }
                foreach ($newParams as $n => $v) {
                    Table::getInstance('Params', 'PagesTable')->save(
                        array(
                            'name' => $n,
                            'params'   => $v,
                        )
                    );
                }
            }
        }
    }

    /**
     * Get all parameters
     *
     * @return mixed
     */
    public function getAll()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($this->_tbl);
        $db->setQuery($query);
        return $db->loadAssocList();
    }

    /**
     * Remove all parameters
     */
    public function removeAll()
    {
        $db = $this->getDbo();
        $db->setQuery('TRUNCATE TABLE ' . $this->_tbl)->execute();
    }
}