<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$themeHelper = ThemeHelper::getInstance();
$document = Factory::getDocument();

$pageStyles = <<<STYLES
<style>
 .u-section-1 {
  background-image: none;
}
.u-section-1 .u-sheet-1 {
  min-height: 100vh;
}
.u-section-1 .u-text-1 {
  font-size: 12.5rem;
  font-weight: 700;
  margin: 60px auto 0;
}
.u-section-1 .u-text-2 {
  font-size: 1.875rem;
  font-weight: 700;
  line-height: 1;
  text-transform: uppercase;
  margin: 20px auto 0;
}
.u-section-1 .u-text-3 {
  background-image: none;
  font-size: 1.5rem;
  width: 525px;
  margin: 20px auto 0;
}
.u-section-1 .u-btn-1 {
  border-style: solid;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 4px;
  background-image: none;
  align-self: center;
  margin: 30px auto 60px;
  padding: 10px 54px 10px 52px;
}
@media (max-width: 767px) {
  .u-section-1 .u-sheet-1 {
    min-height: 599px;
  }
}
@media (max-width: 575px) {
  .u-section-1 .u-sheet-1 {
    min-height: 525px;
  }
  .u-section-1 .u-text-1 {
    font-size: 7.5rem;
  }
  .u-section-1 .u-text-2 {
    font-size: 1.5rem;
  }
  .u-section-1 .u-text-3 {
    font-size: 1.25rem;
    width: 340px;
  }
}
</style>
STYLES;
$themeHelper->pageStyles = $pageStyles;

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
$lang = checkAndGetLanguage();
ob_start();
echo '<!--component_settings-->' . json_encode($settings) . '<!--/component_settings-->';
include_once dirname(__FILE__) . '/' . ($lang ? '/' . $lang : '') . '/page404Template_0_error_1.php';
$themeHelper->pageContent = ob_get_clean();
