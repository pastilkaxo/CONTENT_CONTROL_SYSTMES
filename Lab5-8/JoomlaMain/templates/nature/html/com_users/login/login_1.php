<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$document = Factory::getApplication()->getDocument();

$loginStyles = <<<STYLES
<style>
.u-section-1 .u-sheet-1 {
  min-height: 493px;
}
.u-section-1 .u-form-1 {
  width: 570px;
  margin: 80px auto 0;
}
.u-section-1 .u-input-1 {
  background-image: none;
}
.u-section-1 .u-input-2 {
  background-image: none;
}
.u-section-1 .u-btn-1 {
  width: 100%;
  padding: 10px 30px;
}
.u-section-1 .u-btn-2 {
  border-style: none none solid;
  align-self: center;
  margin: 30px 0 0;
  padding: 0;
}
.u-section-1 .u-btn-3 {
  border-style: none none solid;
  margin: 30px 0 0;
  padding: 0;
}
@media (max-width: 1199px) {
  .u-section-1 .u-btn-2 {
    margin-left: 185px;
  }
  .u-section-1 .u-btn-3 {
    margin-right: 185px;
    margin-bottom: 60px;
  }
}
@media (max-width: 991px) {
  .u-section-1 .u-btn-2 {
    margin-left: 75px;
  }
  .u-section-1 .u-btn-3 {
    margin-right: 75px;
  }
}
@media (max-width: 767px) {
  .u-section-1 .u-form-1 {
    width: 540px;
  }
  .u-section-1 .u-btn-2 {
    margin-left: 0;
  }
  .u-section-1 .u-btn-3 {
    margin-right: 0;
  }
}
@media (max-width: 575px) {
  .u-section-1 .u-form-1 {
    width: 340px;
  }
}
</style>
STYLES;
$document->addCustomTag($loginStyles);

ob_start(); ?>

<?php
$backToTop = ob_get_clean();

ob_start();
?>

<?php
$popupDialogs= ob_get_clean();

$settings = array(
    'hideHeader' => false,
    'hideFooter' => false,
    'bodyClass' => 'u-body u-xl-mode',
    'bodyStyle' => "",
    'localFontsFile' => "",
    'backToTop' => $backToTop,
    'popupDialogs' => $popupDialogs,
);
echo '<!--component_settings-->' . json_encode($settings) . '<!--/component_settings-->';
$lang = checkAndGetLanguage();
ob_start();
?>
<?php $return = $this->form->getValue('return', '', $this->params->get('login_redirect_url', $this->params->get('login_redirect_menuitem'))); ?>
    <input type="hidden" name="return" value="<?php echo base64_encode($return); ?>" />
<?php echo HTMLHelper::_('form.token'); ?>
<?php
$hiddenFields = ob_get_clean();
include_once dirname(dirname(dirname(dirname(__FILE__)))) . '/views/' . ($lang ? $lang . '/' : '') . 'loginTemplate_0_login_1.php';
