
<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

$input  = Factory::getApplication()->input;
$option = $input->getCmd('option');
$view   = $input->getCmd('view');
$id     = $input->getInt('id');
ob_start();
?>
<li class="u-categories-item u-root u-expand-open"><div class="u-categories-item-content" style="padding: 10px 0px; margin-bottom: 0px; margin-right: 0px; font-size: 0.875rem;"><a class="u-category-link u-button-style u-nav-link" href="[[href]]">
                    <span class="u-icon">
                        <svg x="0px" y="0px" style="width: 1em; height: 1em;" viewBox="0 0 16 16" fill-opacity="1">
                            <use xlink:href="#icon-categories-open"></use>
                        </svg>
                    </span>
                    [[title]]
                </a></div>[[children]]</li>
<?php $template = ob_get_clean(); ?>
<?php function buildCategories($template, $startLevel, $list, $params) {
    $input  = Factory::getApplication()->input;
    $option = $input->getCmd('option');
    $view   = $input->getCmd('view');
    $id     = $input->getInt('id');
    ob_start();
    ?>
    <ul class="u-unstyled">
        <?php
        foreach ($list as $item) : ?>
            <?php
            $isRoot = (int) $startLevel === (int) $item->level - 1 ? true : false;
            $href = Route::_(ContentHelperRoute::getCategoryRoute($item->id));
            $title = $item->title;
            $activeLinkClass = '';
            $activeContainerClass = '';
            if ($id == $item->id && in_array($view, array('category', 'categories')) && $option == 'com_content') {
                $activeContainerClass = 'u-active';
                $activeLinkClass = 'active';
            }
            $currentTemplate = str_replace('u-categories-item-content', $activeContainerClass . ' u-categories-item-content', $template);
            $currentTemplate = str_replace('[[href]]', $href, $currentTemplate);
            $currentTemplate = str_replace('[[title]]', $title, $currentTemplate);
            $currentTemplate = str_replace('u-category-link', $activeLinkClass . ' u-category-link', $currentTemplate);
            if (!$isRoot) {
                $currentTemplate = str_replace('u-root', '', $currentTemplate);
            }
            $children = '';
            ?>
            <?php if ($params->get('show_children', 0) && count($item->getChildren())) : ?>
                <?php $list = $item->getChildren(); ?>
                <?php $children = buildCategories($template, $startLevel, $list, $params)//ob_get_clean(); ?>
            <?php endif; ?>
            <?php
            $currentTemplate = str_replace('[[children]]', $children, $currentTemplate);
            $currentTemplate = str_replace('u-expand-open', 'u-expand-' . ($children ? 'closed' : 'leaf'), $currentTemplate);
            $currentTemplate = str_replace('icon-categories-open', 'icon-categories-' . ($children ? 'closed' : 'leaf'), $currentTemplate);
            if (!$children) {
                $currentTemplate = str_replace('fill-opacity="1"', 'fill-opacity="0"', $currentTemplate);
            }
            echo $currentTemplate;
            ?>

        <?php endforeach; ?>
    </ul>
    <?php
    return ob_get_clean();
}
echo buildCategories($template, $startLevel, $list, $params);
