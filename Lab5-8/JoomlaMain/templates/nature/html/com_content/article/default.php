<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$text = $this->item->introtext;
if (property_exists($this->item, 'nicepage') && strpos($text, '<!--np_landing-->') !== false) {
    echo $text;
    return;
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) .  '/functions.php';

$defaultLogo = getLogoInfo(array('src' => "/images/default-logo.png"));
$logoPath = $defaultLogo['src_path'];
$logoUrl = $defaultLogo['src'];
$logoWidth = '';
$logoHeight = '';
if ($logoPath) {
    $logoInfo = getimagesize($logoPath);
    $logoWidth = $logoInfo[0];
    $logoHeight = $logoInfo[1];
}

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

$app = Factory::getApplication();
$document = $app->getDocument();
$themeHelper = ThemeHelper::getInstance();

Core::load("Core_Content");

$component = new CoreContent($this, $this->params);

$article = $component->article('article', $this->item, $this->item->params, array('print' => $this->print));

$themeHelper->seoTags['og:type'] = array('tag' => 'meta', 'property' => 'og:type', 'content' => 'article');
$themeHelper->seoTags['og:title'] = array('tag' => 'meta', 'property' => 'og:title', 'content' => $article->title);

$themeParams = $app->getTemplate(true)->params;
if ($themeParams->get('jsonld', '0') == '1') {
    ob_start();
    $config = $app->getConfig();
    ?>
    <script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "Article",
    "articleBody": <?php echo json_encode(strip_tags($article->text($article->text))); ?>,
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo Uri::getInstance()->toString(); ?>"
    },
    "headline": "<?php echo $article->title; ?>",
    "description": <?php echo json_encode(strip_tags($article->intro($article->intro))); ?>,
    <?php
        $image = $article->images['fulltext']['image'];
        if ($image) {
            $rootUrl = rtrim(Uri::root(), '/');
            $rootPath = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', JPATH_ROOT), '/');
            $isFullUrl = strpos($image, $rootUrl) !== false;
            if (!$isFullUrl) {
                $imageUrl = $rootUrl . '/' . $image;
                $imagePath = $rootPath .  '/' . $image;
            } else {
                $imageUrl = $image;
                $imagePath = str_replace($rootUrl, $rootPath, $image);
            }
            $info = @getimagesize($imagePath);

            $themeHelper->seoTags['og:image'] = array('tag' => 'meta', 'property' => 'og:image', 'content' => $imageUrl);
            $themeHelper->seoTags['og:secure_url'] = array('tag' => 'meta', 'property' => 'og:image:secure_url', 'content' => str_replace('http:', 'https:', $imageUrl));
            $themeHelper->seoTags['og:height'] = array('tag' => 'meta', 'property' => 'og:image:height', 'content' => @$info[1]);
            $themeHelper->seoTags['og:width'] = array('tag' => 'meta', 'property' => 'og:image:width', 'content' => @$info[0]);
            $alt = $article->images['fulltext']['alt'];
            if ($alt) {
                $themeHelper->seoTags['og:alt'] = array('tag' => 'meta', 'property' => 'og:image:alt', 'content' => $alt);
            }
            ?>
    "image": {
        "@type": "ImageObject",
        "url": "<?php echo $imageUrl; ?>",
        "height": <?php echo ($info && isset($info[1])) ? $info[1] : '""'; ?>,
        "width": <?php echo ($info && isset($info[0])) ? $info[0] : '""'; ?>
    },
    <?php } ?>
    "datePublished": "<?php echo date(DATE_ISO8601, strtotime($this->item->publish_up)); ?>",
    "dateCreated": "<?php echo date(DATE_ISO8601, strtotime($this->item->created)); ?>",
    "dateModified": "<?php echo date(DATE_ISO8601, strtotime($this->item->modified)); ?>",
    "author": {
        "@type": "Person",
        "name": "<?php echo $article->author; ?>"
    },
    "publisher": {
        "@type": "Organization",
          "logo": {
            "@type": "ImageObject",
            "height": "<?php echo $logoHeight; ?>",
            "width": "<?php echo $logoWidth; ?>",
            "url": "<?php echo $logoUrl; ?>"
          },
        "name": "<?php echo $config->get('sitename'); ?>"
    }
}

    </script>
    <?php
    $articleLdJson = ob_get_clean();
    $document->addCustomTag($articleLdJson);
}

$templateName = $themeParams->get('post', '');
$options = getTemplateOptions(dirname(__FILE__), $templateName);

// return if post is nicepage article
if (property_exists($this->item, 'nicepage')) {
    echo '<!--component_settings-->' . json_encode(array('backToTop' => $options['backToTop'])) . '<!--/component_settings-->';
    echo $article->text($article->text);
    return;
}

echo '<!--component_settings-->' . json_encode($options) . '<!--/component_settings-->';

$lang = checkAndGetLanguage();
echo $component->pageHeading();

$beforeDisplayContent = $this->item->event->beforeDisplayContent;

$index = 0;
${'title' . $index} = strlen($article->title) ? $this->escape($article->title) : '';
${'titleLink' . $index} = strlen($article->titleLink) ? $article->titleLink : '';
${'shareLink' . $index} = strlen($article->shareLink) ? $article->shareLink : '';

${'readmore' . $index} = strlen($article->readmore) ? $article->readmore : '';
${'readmoreLink' . $index} = strlen($article->readmoreLink) ? $article->readmoreLink : '';

$content = $beforeDisplayContent;

if (strlen($article->text)) {
    $content = $article->text($article->text);
}
if ($article->introVisible) {
    $content .= $article->intro($article->intro);
}
if (strlen($article->readmore)) {
    $content .= $article->readmore($article->readmore, $article->readmoreLink);
}
${'content' . $index} = $content;

${'altImage' . $index} = $article->images['fulltext']['alt'];
${'image' . $index} = $article->images['fulltext']['image'];
${'tags' . $index} = count($article->tags) > 0 ? implode('', $article->tags) : '';

${'metadata' . $index} = array();
if (strlen($article->author)) {
    ${'metadata' . $index}['author'] = $article->authorInfo($article->author, $article->authorLink);
}
if (strlen($article->published)) {
    ${'metadata' . $index}['date'] = $article->publishedDateInfo($article->published);
}
if (strlen($article->category)) {
    ${'metadata' . $index}['category'] = $article->categories($article->parentCategory, $article->parentCategoryLink, $article->category, $article->categoryLink);
}

if ($this->item->params->get('access-edit')) {
    ${'metadata' . $index}['edit']  = $article->editIcon();
}

include dirname(dirname(dirname(dirname(__FILE__)))) . '/views/' . ($lang ? $lang . '/' : '') . 'postTemplate_0_' . $options['fileName'] . '.php';

$afterDisplayContent = $this->item->event->afterDisplayContent;
if ($afterDisplayContent) {
    ?>
    <section><div class="u-sheet"><?php echo $afterDisplayContent; ?></div></section>
<?php } ?>
