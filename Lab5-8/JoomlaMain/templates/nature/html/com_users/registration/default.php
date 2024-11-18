<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

$formClass = <<<FORMCLASS
u-clearfix u-form-custom-backend u-form-spacing-10 u-form-vertical u-inner-form
FORMCLASS;

ob_start();
?><div class="u-align-left u-form-group u-form-submit">
          <a href="#" class="u-btn u-btn-submit u-button-style u-btn-1"><?php echo JText::_('JREGISTER'); ?></a>
          <input type="submit" value="submit" class="u-form-control-hidden">
        </div><?php
$formSubmit = ob_get_clean();

ob_start();
?><div class="u-form-group u-form-name">
          <label for="[[label_for]]" class="u-label" name="[[label_name]]">[[label_content]]</label>
          <input type="[[input_type]]" placeholder="[[input_placeholder]]" id="[[input_id]]" name="[[input_name]]" class="u-input u-input-rectangle u-none u-input-1" required="">
        </div><?php
$inputHtml = ob_get_clean();

?>
<section class="u-clearfix">
    <div class="u-clearfix u-sheet">
        <div class="registration<?php echo $this->pageclass_sfx; ?>">
            <?php if ($this->params->get('show_page_heading')) : ?>
                <div class="page-header">
                    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
                </div>
            <?php endif; ?>
            <div class="u-form">
                <form id="member-registration" action="<?php echo Route::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="<?php echo $formClass ? $formClass : 'form-validate form-horizontal well'; ?>" enctype="multipart/form-data">
                    <?php
                    foreach ($this->form->getFieldsets() as $fieldset) {
                        $fields = $this->form->getFieldset($fieldset->name);
                        if (count($fields)) {
                            if (isset($fieldset->label)) {
                                echo '<p>' . Text::_($fieldset->label) . '</p>';
                            }
                            if ($inputHtml) {
                                $fields = $this->form->getFieldset($fieldset->name);
                                foreach ($fields as $field) {
                                    if ($field->type == 'Spacer') {
                                        echo '<br />';
                                        continue;
                                    }
                                    if ($field->type == 'Captcha') {
                                        echo $field->renderField();
                                        continue;
                                    }
                                    echo str_replace(
                                        array('[[label_for]]', '[[label_name]]', '[[label_content]]', '[[input_type]]', '[[input_placeholder]]', '[[input_id]]', '[[input_name]]'),
                                        array($field->id, $field->id . '-lbl', $field->title, $field->type, '', $field->id, $field->name),
                                        $inputHtml
                                    );
                                }
                            } else { ?>
                                <fieldset>
                                    <?php // If the fieldset has a label set, display it as the legend. ?>
                                    <?php if (isset($fieldset->label)) : ?>
                                        <legend><?php echo Text::_($fieldset->label); ?></legend>
                                    <?php endif; ?>
                                    <?php echo $this->form->renderFieldset($fieldset->name); ?>
                                </fieldset>
                            <?php }
                        }
                    }
                    ?>
                    <?php if ($formSubmit) : ?>
                        <?php echo $formSubmit; ?>
                    <?php else: ?>
                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-primary validate">
                                    <?php echo Text::_('JREGISTER'); ?>
                                </button>
                                <a class="btn" href="<?php echo Route::_(''); ?>" title="<?php echo Text::_('JCANCEL'); ?>">
                                    <?php echo Text::_('JCANCEL'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <input type="hidden" name="option" value="com_users" />
                    <input type="hidden" name="task" value="registration.register" />
                    <?php echo HTMLHelper::_('form.token'); ?>
                </form>
            </div>
        </div>
    </div>
</section>
