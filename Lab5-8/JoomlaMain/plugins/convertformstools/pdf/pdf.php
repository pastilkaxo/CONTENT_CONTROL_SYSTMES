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

JLoader::register('PDFHelper', __DIR__ . '/helper/pdfhelper.php');

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class PlgConvertFormsToolsPDF extends CMSPlugin
{
    /**
     *  Application Object
     *
     *  @var  object
     */
    protected $app;

    

    /**
     *  Auto loads the plugin language file
     *
     *  @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     *  Add plugin fields to the form
     *
     *  @param   Form   $form  
     *  @param   object  $data
     *
     *  @return  boolean
     */
    public function onConvertFormsFormPrepareForm($form, $data)
    {
        $form->loadFile(__DIR__ . '/form/form.xml', false);

        
    }

    
}