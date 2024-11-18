<?php
defined('_JEXEC') or die;
$listOptions = array();
?>

<?php
$templateStyles = <<<STYLES
<style>
.u-section-1 .u-sheet-1 {
  min-height: 835px;
}
.u-section-1 .u-post-details-1 {
  min-height: 375px;
  margin-top: 60px;
  margin-bottom: -10px;
}
.u-section-1 .u-container-layout-1 {
  padding: 30px;
}
.u-section-1 .u-image-1 {
  height: 486px;
  margin-top: 0;
  margin-bottom: 0;
  margin-left: 0;
}
.u-section-1 .u-text-1 {
  margin-top: 20px;
  margin-bottom: 0;
  margin-left: 0;
}
.u-section-1 .u-metadata-1 {
  margin-top: 30px;
  margin-bottom: 0;
  margin-left: 0;
}
.u-section-1 .u-text-2 {
  margin-bottom: 0;
  margin-top: 20px;
  margin-left: 0;
}
@media (max-width: 1199px) {
  .u-section-1 .u-image-1 {
    margin-left: initial;
  }
}
@media (max-width: 991px) {
  .u-section-1 .u-sheet-1 {
    min-height: 782px;
  }
  .u-section-1 .u-post-details-1 {
    margin-bottom: 60px;
  }
  .u-section-1 .u-image-1 {
    height: 423px;
    margin-left: initial;
  }
}
@media (max-width: 767px) {
  .u-section-1 .u-sheet-1 {
    min-height: 722px;
  }
  .u-section-1 .u-container-layout-1 {
    padding-left: 10px;
    padding-right: 10px;
  }
  .u-section-1 .u-image-1 {
    height: 354px;
    margin-top: 9px;
    margin-left: initial;
  }
}
@media (max-width: 575px) {
  .u-section-1 .u-sheet-1 {
    min-height: 656px;
  }
  .u-section-1 .u-image-1 {
    height: 275px;
    margin-left: initial;
  }
}
</style>
STYLES;

ob_start(); ?>
    
<?php
$backToTop = ob_get_clean();

ob_start();
?>
    
<?php
$popupDialogs= ob_get_clean();

$listOptions['post_1'] = array(
    'isDefault' => true,
    'fileName' => 'post_1',
    'styles' => $templateStyles,
    'hideHeader' => false,
    'hideFooter' => false,
    'bodyClass' => 'u-body u-xl-mode',
    'bodyStyle' => "",
    'localFontsFile' => "",
    'backToTop' => $backToTop,
    'popupDialogs' => $popupDialogs,
);
?>
