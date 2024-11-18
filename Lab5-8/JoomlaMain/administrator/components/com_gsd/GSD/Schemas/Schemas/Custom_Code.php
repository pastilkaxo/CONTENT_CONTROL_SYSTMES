<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD\Schemas\Schemas;

// No direct access
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

class Custom_Code extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        parent::initProps();

        // Since v5.3.1, the SchemaCleaner supports removing structured data also from the <head> that does not have the data-type="gsd" property.
        // In order to prevent the user defined custom code from being removed, we need to add the data-type property to every custom JSON+LD script.  
        $safe_custom_code = str_replace('<script type="application', '<script data-type="gsd" type="application', $this->data->get('custom_code'));
        $this->data->set('custom_code', $safe_custom_code);
    }

    /**
     * Since in the Custom Code we do not have a real array but a string, the result is not passed into the cleanProps() method, thus, we may end up
     * with unescaped characters and HTML that can break the structured data. With this override, we filter all payload props before they get replaced in the snippet.
     * 
     * Consider this as a temporary workaround. In addition, it's worth consideration to filter payload props on all Schemas by default, so we don't need to do so later.
     *
     * @param   object  $payload
     * 
     * @return  void
     */
    public function onPayloadPrepare(&$payload)
    {
        $props = $payload->toArray();

        array_walk_recursive($props, function(&$prop)
        {
            if (!is_null($prop)) // Make PHP 8.1 happy.
            {
                $this->cleanProp($prop);
            }
        });

        $payload = new Registry($props);
    }

    /**
     * Do not clean custom script
     *
     * @return void
     */
    protected function cleanProps()
    {
    }
}