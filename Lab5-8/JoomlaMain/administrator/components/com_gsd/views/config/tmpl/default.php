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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

HTMLHelper::stylesheet('com_gsd/styles.css', ['relative' => true, 'version' => 'auto']);

HTMLHelper::_('behavior.formvalidator');

$tabs_prefix = 'uitab';

if (!defined('nrJ4'))
{
    $tabs_prefix = 'bootstrap';
    HTMLHelper::_('bootstrap.popover');
    HTMLHelper::_('behavior.modal');
    HTMLHelper::_('behavior.tabstate');
    HTMLHelper::_('formbehavior.chosen', 'select');
}
else 
{
    Factory::getDocument()->addStyleDeclaration('
        #content select:not([class*="input-"]), #content input:not([class*="input-"]) {
            max-width:270px;
        }
    ');

    NRFramework\HTML::fixFieldTooltips();
}

?>

<div class="nr-app<?php echo defined('nrJ4') ? ' j4' : ''; ?> nr-app-config">
    <div class="nr-row">
        <?php echo !defined('nrJ4') ? $this->sidebar : '' ?>
        <div class="nr-main-container">
            <div class="nr-main-header">
                <h2><?php echo Text::_('GSD_CONFIG'); ?></h2>
                <p><?php echo Text::_('GSD_CONFIG_DESC'); ?></p>
            </div>
            <div class="nr-main-content">
        		<form action="<?php echo Route::_('index.php?option=com_gsd&view=config'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    		      <div class="form-horizontal">
                    	<?php 
                            echo HTMLHelper::_($tabs_prefix . '.startTabSet', 'tab', ['recall' => true, 'active' => 'globaldata']);

                            foreach ($this->form->getFieldSets() as $key => $fieldset)
                            {
                                echo HTMLHelper::_($tabs_prefix . '.addTab', 'tab', $fieldset->name, Text::_($fieldset->label));
                                echo $this->form->renderFieldSet($fieldset->name);
                                echo HTMLHelper::_($tabs_prefix . '.endTab');
                            }

                            echo HTMLHelper::_($tabs_prefix . '.endTabSet');
                        ?>
        		    </div>

        		    <?php echo HTMLHelper::_('form.token'); ?>
        		    <input type="hidden" name="task" value="" />
        		    <input type="hidden" name="name" value="config" />
        		</form>
            </div>
        </div>
    </div>
</div>