<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\String\StringHelper;

$searchFormData = array();
$searchFormData['action'] = Route::_($this->query->toUri());
$searchFormData['input'] = $this->escape($this->query->input);
$searchFormData['totalText'] = '';
?>

<?php if (($this->total === 0) || ($this->total === null)) : ?>
    <?php ob_start(); ?>
    <div style="padding: 30px;" class="u-search-not-found-results u-text u-align-center"/>'
        <div id="search-result-empty" class="com-finder__empty">
            <h2><?php echo Text::_('COM_FINDER_SEARCH_NO_RESULTS_HEADING'); ?></h2>
            <?php $multilang = Factory::getApplication()->getLanguageFilter() ? '_MULTILANG' : ''; ?>
            <p><?php echo Text::sprintf('COM_FINDER_SEARCH_NO_RESULTS_BODY' . $multilang, $this->escape($this->query->input)); ?></p>
        </div>
    </div>
    <?php $searchFormData['totalText'] = ob_get_clean(); ?>
<?php endif; ?>

<?php // Activate the highlighter if enabled. ?>
<?php if (!empty($this->query->highlight) && $this->params->get('highlight_terms', 1)) : ?>
    <?php
    // Allow a maximum of 10 tokens to be highlighted. Otherwise the URL can get too long.
    $this->document->getWebAssetManager()->useScript('highlight');
    $this->document->addScriptOptions(
        'highlight',
        [[
            'class'      => 'js-highlight',
            'highLight'  => array_slice($this->query->highlight, 0, 10),
        ]]
    );
    ?>
<?php endif; ?>

<?php
$document = Factory::getApplication()->getDocument();

$searchStyles = <<<STYLES
<style>
.u-section-1 .u-sheet-1 {
  min-height: 973px;
}
.u-section-1 .u-text-1 {
  width: 318px;
  margin: 30px auto 0;
}
.u-section-1 .u-search-1 {
  height: auto;
  width: 660px;
  min-height: 38px;
  margin: 20px auto 0;
}
.u-section-1 .u-blog-1 {
  margin: 40px 0 60px;
}
.u-section-1 .u-repeater-1 {
  grid-template-columns: repeat(1, 100%);
  min-height: 584px;
  grid-auto-columns: 100%;
  grid-gap: 10px;
}
.u-section-1 .u-container-layout-1 {
  padding: 30px;
}
.u-section-1 .u-text-2 {
  margin: 0;
}
.u-section-1 .u-text-3 {
  margin: 20px 0 0;
}
.u-section-1 .u-container-layout-2 {
  padding: 30px;
}
.u-section-1 .u-text-4 {
  margin: 0;
}
.u-section-1 .u-text-5 {
  margin: 20px 0 0;
}
.u-section-1 .u-container-layout-3 {
  padding: 30px;
}
.u-section-1 .u-text-6 {
  margin: 0;
}
.u-section-1 .u-text-7 {
  margin: 20px 0 0;
}
@media (max-width: 1199px) {
  .u-section-1 .u-blog-1 {
    min-height: 768px;
    height: auto;
    margin-right: initial;
    margin-left: initial;
  }
  .u-section-1 .u-repeater-1 {
    grid-template-columns: 100%;
  }
}
@media (max-width: 767px) {
  .u-section-1 .u-search-1 {
    width: 540px;
  }
  .u-section-1 .u-container-layout-1 {
    padding-left: 10px;
    padding-right: 10px;
  }
  .u-section-1 .u-container-layout-2 {
    padding-left: 10px;
    padding-right: 10px;
  }
  .u-section-1 .u-container-layout-3 {
    padding-left: 10px;
    padding-right: 10px;
  }
}
@media (max-width: 575px) {
  .u-section-1 .u-search-1 {
    width: 340px;
  }
}

</style>
STYLES;
$document->addCustomTag($searchStyles);

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

?>
<?php

$funcsInfo = array(
   array('repeatable' => true, 'name' => 'searchTemplate_0_search_1', 'itemsExists' => true),

);

$funcsStaticInfo = array(

);

$lang = checkAndGetLanguage();
if (count($funcsInfo)) {
    foreach ($funcsInfo as $funcInfo) {
        if (!$funcInfo['itemsExists']) {
            include $themePath . '/views/' . $funcInfo['name'] . '.php';
            continue;
        }
        if (file_exists($themePath . '/views/' . ($lang ? $lang . '/' : '') . $funcInfo['name'] . '_start.php')) {
            include $themePath . '/views/' . ($lang ? $lang . '/' : '') . $funcInfo['name'] . '_start.php';
        }

        $show_description = $this->params->get('show_description', 1);
        if ($this->results && is_array($this->results)) {
            foreach ($this->results as $itemIndex => $item) {
                $this->result = &$item;
                $this->result->counter = $itemIndex + 1;

                $j = 0;
                //$article = $component->article('category', $item, $item->params);
                //$beforeDisplayContent = $item->event->beforeDisplayContent;
                ${'title' . $j} = $this->result->title;;
                ${'titleLink' . $j} = Route::_($this->result->route);

                $description = '';
                if ($show_description) {
                    // Calculate number of characters to display around the result
                    $term_length = StringHelper::strlen($this->query->input);
                    $desc_length = $this->params->get('description_length', 255);
                    $pad_length = $term_length < $desc_length ? (int)floor(($desc_length - $term_length) / 2) : 0;

                    // Make sure we highlight term both in introtext and fulltext
                    $full_description = $this->result->description;
                    if (!empty($this->result->summary) && !empty($this->result->body)) {
                        $full_description = Helper::parse($this->result->summary . $this->result->body);
                    }

                    // Find the position of the search term
                    $pos = $term_length ? StringHelper::strpos(StringHelper::strtolower($full_description), StringHelper::strtolower($this->query->input)) : false;

                    // Find a potential start point
                    $start = ($pos && $pos > $pad_length) ? $pos - $pad_length : 0;

                    // Find a space between $start and $pos, start right after it.
                    $space = StringHelper::strpos($full_description, ' ', $start > 0 ? $start - 1 : 0);
                    $start = ($space && $space < $pos) ? $space + 1 : $start;

                    $description = HTMLHelper::_('string.truncate', StringHelper::substr($full_description, $start), $desc_length, true);
                }

                ${'content' . $j} = $description;


                include $themePath . '/views/' . ($lang ? $lang . '/' : '') . $funcInfo['name'] . '.php';
            }
        }
        if (file_exists($themePath . '/views/' . ($lang ? $lang . '/' : '') . $funcInfo['name'] . '_end.php')) {
            include $themePath . '/views/' . ($lang ? $lang . '/' : '') . $funcInfo['name'] . '_end.php';
        }
    }
}

if (count($funcsStaticInfo)) {
    for ($i = 0; $i < count($funcsStaticInfo); $i++) {
        include_once $themePath . '/views/' . ($lang ? $lang . '/' : '') . $funcsStaticInfo[$i]['name'] . '.php';
    }
}
