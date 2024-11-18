<?php

defined('JPATH_PLATFORM') or die;
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

use Joomla\CMS\Factory;
use Joomla\Component\Templates\Administrator\Table\StyleTable;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Component\ComponentHelper;

class JFormFieldImport extends FormField
{
    protected $type = 'Import';
    protected $pluginName = 'nicepage';

    protected function getInput()
    {
        $text = $this->element['text'] ?: '';

        $app = Factory::getApplication();
        $inputs = $app->input;

        $id = $inputs->get('id');
        $table = new StyleTable(Factory::getDbo());

        $table->load($id);
        $themeName = $table->template;
        $dataFolder = Uri::root(true).'/templates/'. $themeName .'/content';

        $editorName = $this->pluginName;
        $editorIsInstalled = $this->_npInstalled() ? '1' : '0';
        ob_start();
        ?>
        <script>if ('undefined' != typeof jQuery) document._jQuery = jQuery;</script>
        <script src="<?php echo Uri::root() . 'templates/' . $themeName . '/scripts/jquery.js' ?>" type="text/javascript"></script>
        <script>jQuery.noConflict();</script>

        <link href="<?php echo Uri::root() . 'templates/' . $themeName . '/content/squeezebox/modal.css'; ?>" rel="stylesheet">
        <script src="<?php echo Uri::root() . 'templates/' . $themeName . '/content/squeezebox/modal.js'; ?>" type="text/javascript"></script>

        <script src="<?php echo Uri::root() . 'templates/' . $themeName . '/content/loader.js' ?>" type="text/javascript"></script>
        <script>if (document._jQuery) jQuery = document._jQuery;</script>
        <button class="import-button" type="submit" name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>">
            <?php echo Text::_($text); ?>
        </button>
        <input type="hidden" id="dataFolder" value="<?php echo $dataFolder; ?>">
        <input type="hidden" id="editorIsInstalled" value="<?php echo $editorIsInstalled; ?>">
        <input type="hidden" id="themeId" value="<?php echo $id; ?>">
        <div id="log" style="float:left;width:100%;margin-left:150px"></div>
        <?php
        return ob_get_clean();
    }

    private function _npInstalled() {
        if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_' . $this->pluginName)) {
            return false;
        }
        if (!ComponentHelper::getComponent('com_' . $this->pluginName, true)->enabled) {
            return false;
        }

        return true;
    }

}
