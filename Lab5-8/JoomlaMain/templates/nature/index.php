<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

$indexDir = dirname(__FILE__);
require_once $indexDir . '/functions.php';

HTMLHelper::_('jquery.framework');
HTMLHelper::_('bootstrap.framework');

$app = Factory::getApplication();
$config = $app->getConfig();
$sef = $app->get('sef', false);

$defaultLogo = getLogoInfo(array('src' => "/images/default-logo.png"));

// Create alias for $this object reference:
$document = $this;

$currentUrl = Uri::getInstance()->toString();
if ($sef)
{
    $document->setBase($currentUrl);
}

$metaGeneratorContent = 'Nicepage 6.20.0, nicepage.com';
if ($metaGeneratorContent) {
    $document->setMetaData('generator', $metaGeneratorContent);
}
$metaReferrer = '';
if ($metaReferrer) {
    $document->setMetaData('referrer', 'origin');
}

$templateUrl = $document->baseurl . '/templates/' . $document->template;
$faviconPath = "" ? $templateUrl . '/images/' . "" : '';

Core::load("Core_Page");
Core::load("Core_PageProperties");

// Initialize $view:
$view = new CorePage($this);

$pageProperties = new CorePageProperties($this);
$styles = $pageProperties->getStyles();
$bodyClass = $pageProperties->getBodyClass('u-body u-xl-mode');
$bodyStyle = $pageProperties->getBodyStyle();
$backToTop = $pageProperties->getBackToTop();
$popupDialogs = $pageProperties->getPopupDialogs();
$showHeader = $pageProperties->showHeader();
$showFooter = $pageProperties->showFooter();
$localFontsFile = $pageProperties->getLocalFontsFile();
$themeHelper = $themeHelper = ThemeHelper::getInstance();
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <?php if ($faviconPath) : ?>
        <link href="<?php echo $faviconPath; ?>" rel="icon" type="image/x-icon" />
    <?php endif; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta data-intl-tel-input-cdn-path="<?php echo $templateUrl; ?>/scripts/intlTelInput/" />
    
    
    <?php echo CoreStatements::head(); ?>
    <?php if ($styles) : ?>
        <?php echo $styles; ?>
    <?php endif; ?>
    <meta name="theme-color" content="#efa69d">
    <link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/default.css" media="screen" type="text/css" />
    <?php if($view->isFrontEditing()) : ?>
        <link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/frontediting.css" media="screen" type="text/css" />
    <?php endif; ?>
    <link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/media.css" id="theme-media-css" media="screen" type="text/css" />
    <?php if ($localFontsFile) : ?><link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/<?php echo $localFontsFile; ?>" media="screen" type="text/css" /><?php else : ?><?php endif; ?><link id="u-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i|Open+Sans:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i">
    <?php include_once "$indexDir/styles.php"; ?>
    <?php if ($this->params->get('jquery', '0') == '1') : ?>
        <script src="<?php echo $templateUrl; ?>/scripts/jquery.js"></script>
    <?php endif; ?>
    <script src="<?php echo $templateUrl; ?>/scripts/script.js"></script>
    <?php echo getProductsScript(); ?>
    
    <?php if ($this->params->get('jsonld', '0') == '1') : ?>
    <script type="application/ld+json">
{
	"@context": "http://schema.org",
	"@type": "Organization",
	"name": "<?php echo $config->get('sitename'); ?>",
	"sameAs": [],
	"url": "<?php echo JUri::getInstance()->toString(); ?>",
	"logo": "<?php echo $defaultLogo['src']; ?>"
}
</script>
    <?php
    if ($currentUrl == Uri::base()) {
    ?>
        <script type="application/ld+json">
    {
      "@context": "http://schema.org",
      "@type": "WebSite",
      "name": "<?php echo $config->get('sitename'); ?>",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo Uri::base() . 'index.php?searchword={query' . '}&option=com_search'; ?>",
        "query-input": "required name=query"
      },
      "url": "<?php echo $currentUrl; ?>"
    }
    </script>
    <?php } ?>
    <?php endif; ?>
    <?php if ($this->params->get('metatags', '0') == '1') : ?>
        <?php
        renderSeoTags(ThemeHelper::getInstance()->seoTags);
        ?>
    <?php endif; ?>
    
    
    
</head>
<body <?php echo $bodyClass . $bodyStyle; ?>>

<?php
if ($showHeader) {
    $view->renderHeader($indexDir, $this->params);
}
?>
<?php $view->renderLayout(); ?>
<?php
if ($showFooter) {
    $view->renderFooter($indexDir, $this->params);
}
?>
<section class="u-backlink u-clearfix u-grey-80">
            <p class="u-text">
                <span>This site was created with the </span>
                <a class="u-link" href="https://nicepage.com/" target="_blank" rel="nofollow">
                    <span>Nicepage</span>
                </a>
             </p>
    </section>

<?php echo $backToTop; ?>
<?php echo $popupDialogs; ?>
</body>
</html>
