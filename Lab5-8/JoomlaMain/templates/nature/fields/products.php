<?php

defined('JPATH_PLATFORM') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

use Joomla\CMS\Factory;
use Joomla\Component\Templates\Administrator\Table\StyleTable;
use Joomla\CMS\Form\Field\NoteField;

class JFormFieldProducts extends NoteField
{
    protected $type = 'Products';

    protected function getLabel()
    {
        $text = parent::getLabel();
        $app = Factory::getApplication();
        $inputs = $app->input;
        $id = $inputs->get('id');
        $table = new StyleTable(Factory::getDbo());
        $table->load($id);
        $themeName = $table->template;

        $productListLink = 'index.php?option=com_ajax&format=html&template=' . $themeName . '&method=products&product_name=product-list';
        $text = str_replace('[[products_link]]', '<strong style="font-size:15px">' . $productListLink . '</strong>', $text);

        $productLinks = array(
            'index.php?option=com_ajax&format=html&template=' . $themeName . '&method=product&product_name=product-1',
            'index.php?option=com_ajax&format=html&template=' . $themeName . '&method=product&product_name=product-2',
            '(…)',
            'index.php?option=com_ajax&format=html&template=' . $themeName . '&method=product&product_name=product-99',

        );
        $text = str_replace('[[product_link]]', '<strong style="font-size:15px">' . implode('<br />', $productLinks) . '</strong>', $text);
        return $text;
    }
}
