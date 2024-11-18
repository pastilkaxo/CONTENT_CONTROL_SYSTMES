<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die;

use NP\Processor\PositionsProcessor;

ob_start();
?>
<?php echo $menuProps['menu_template']; ?>
<?php
$menuTemplate = PositionsProcessor::process(ob_get_clean());


if (!function_exists('renderMegaPopup')) {
    /**
     * Build mega menu
     *
     * @param string $content   Menu content
     * @param array  $menuProps Menu options
     *
     * @return string
     */
    function renderMegaPopup($content, $menuProps)
    {
        if (!isset($menuProps['megapopups'])) {
            return $content;
        }

        $megaPopups = json_decode($menuProps['megapopups'], true);
        preg_match_all('/<\!--start\_megapopup_([\d]+)-->([\s\S]+?)<\!--end\_megapopup-->/', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $template = array_key_exists($match[1], $megaPopups) ? $megaPopups[$match[1]] : $megaPopups['default'];
            $template = preg_replace('/url\("([\s\S]+?)"\)/', 'url($1)', $template);
            $subitems = $match[2];
            $template = str_replace('[[popup_menu]]', $subitems, $template);
            $template = PositionsProcessor::process($template);
            $content = str_replace($match[0], $template, $content);
        }
        return $content;
    }
}

if (!function_exists('buildMenu')) {
    /**
     * Build menu
     *
     * @param array  $list       Array items
     * @param int    $default_id Default id
     * @param int    $active_id  Active id
     * @param string $path       Path
     * @param array  $options    Options
     *
     * @return string
     */
    function buildMenu($list, $default_id, $active_id, $path, $options)
    {
        $isMegaMenu = isset($options['megamenu_on']) && $options['megamenu_on'] == 'true' ? true : false;
        if ($isMegaMenu) {
            $elements = array();
            $needMarkMegaMenu = false;
            foreach ($list as $i => &$item) {
                $level = (int) $item->level;
                array_push($elements, $item);

                $nextIndex = $i + 1;
                $nextElementExists = array_key_exists($nextIndex, $list) ? true : false;
                if ($level > 2) {
                    $needMarkMegaMenu = true;
                    $item->parent = false;
                    $item->deeper = false;
                    if (!$nextElementExists || ($nextElementExists && (int)$list[$nextIndex]->level < 3)) {
                        $item->shallower = true;
                    } else {
                        $item->shallower = false;
                    }
                    $item->level = '3';
                }

                if (!$nextElementExists || ($nextElementExists && (int)$list[$nextIndex]->level == 1)) {
                    if ($needMarkMegaMenu) {
                        foreach ($elements as $j => $element) {
                            $element->isMegaMenu = true;
                            if ($j == count($elements) - 1) {
                                $element->needMegaMenuDiff = true;
                            }
                        }
                    }
                    $needMarkMegaMenu = false;
                    $elements = array();
                }
            }
        }
        ob_start();
        $popupKey = 0;
        ?>
        <ul class="<?php echo $options['menu_class']; ?>">
            <?php
            foreach ($list as $i => &$item) {
                $itemParams = $item->getParams();
                if ($item->level == 1) {
                    ++$popupKey;
                }
                $class = 'item-' . $item->id;
                if ($item->id == $default_id) {
                    $class .= ' default';
                }
                $itemIsCurrent = false;
                if (($item->id == $active_id) || ($item->type == 'alias' && $itemParams->get('aliasoptions') == $active_id)) {
                    $class .= ' current';
                    $itemIsCurrent = true;
                }

                if (in_array($item->id, $path)) {
                    $class .= ' active';
                } elseif ($item->type == 'alias') {
                    $aliasToId = $itemParams->get('aliasoptions');
                    if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
                        $class .= ' active';
                    } elseif (in_array($aliasToId, $path)) {
                        $class .= ' alias-parent-active';
                    }
                }

                if ($item->type == 'separator') {
                    $class .= ' divider';
                }

                if ($item->deeper) {
                    $class .= ' deeper';
                }

                if ($item->parent) {
                    $class .= ' parent';
                }

                $submenuItemClass = $options['submenu_item_class'];
                if ($item->level > 2 && isset($item->isMegaMenu)) {
                    $submenuItemClass = $options['mega_submenu_item_class'];
                }
                echo '<li class="' . ($item->level == 1 ? $options['item_class'] : $submenuItemClass) . ' ' . $class . '">';

                $submenuLinkClass = $options['submenu_link_class'];
                if ($item->level > 2 && isset($item->isMegaMenu)) {
                    $submenuLinkClass = $options['mega_submenu_link_class'];
                }
                $linkClassName = $item->level == 1 ? $options['link_class'] : $submenuLinkClass;
                $linkClassName = str_replace(array('u-dialog-link', 'u-file-link'), array('', ''), $linkClassName);

                $submenuLinkStyle = $options['submenu_link_style'];
                if ($item->level > 2 && isset($item->isMegaMenu)) {
                    $submenuLinkStyle = $options['mega_submenu_link_style'];
                }
                $linkInlineStyles = $item->level == 1 ? $options['link_style'] : $submenuLinkStyle;

                switch ($item->type) :
                case 'separator':
                case 'component':
                case 'heading':
                case 'url':
                    include dirname(__FILE__) . '/default_' . $item->type . '.php';
                    break;
                default:
                    include dirname(__FILE__) . '/default_url.php';
                    break;
                endswitch;

                // The next item is deeper.
                if ($item->deeper) {
                    if ($item->level == 1 && isset($item->isMegaMenu)) {
                        echo '<!--start_megapopup_' . $popupKey .'-->';
                    } else {
                        if (isset($item->isMegaMenu) && $item->level == 2) {
                            echo '<div class="level-3 u-nav-popup">';
                        } else {
                            echo '<div class="u-nav-popup">';
                        }
                    }
                    if (isset($item->isMegaMenu) && $item->level == 2) {
                        echo '<ul class="' . $options['mega_submenu_class'] . '">';
                    } else {
                        $options['submenu_class'] = $isMegaMenu && !isset($item->isMegaMenu)
                            ? preg_replace('/u-nav-[\d]+/', '', $options['submenu_class'])
                            : $options['submenu_class'];
                        echo '<ul class="' . $options['submenu_class'] . '">';
                    }
                } elseif ($item->shallower) { // The next item is shallower.
                    $levelDiff = $item->level_diff;
                    $closeDiff = '';
                    if (isset($item->isMegaMenu) && isset($item->needMegaMenuDiff)) {
                        $levelDiff = $item->level == 2 ? 0 : 1;
                        $closeDiff = '</ul><!--end_megapopup--></li>';
                    }
                    echo '</li>';
                    echo str_repeat('</ul></div></li>', $levelDiff);
                    echo $closeDiff;
                } else {// The next item is on the same level.
                    echo '</li>';
                }
            }
            ?>
        </ul>
        <?php
        $menuResult = ob_get_clean();
        $menuResult = renderMegaPopup($menuResult, $options);
        return $menuResult;
    }
}
$menu_html_options = array(
    'container_class' => $menuProps['container_class'],
    'menu_class' => $menuProps['menu_class'],
    'item_class' => $menuProps['item_class'],
    'link_class' => $menuProps['link_class'],
    'link_style' => $menuProps['link_style'],
    'submenu_class' => $menuProps['submenu_class'],
    'submenu_item_class' => $menuProps['submenu_item_class'],
    'submenu_link_class' => $menuProps['submenu_link_class'],
    'submenu_link_style' => $menuProps['submenu_link_style'],
);
if (isset($menuProps['megamenu_on'])) {
    $menu_html_options = array_merge(
        $menu_html_options,
        array(
            'megamenu_on' => $menuProps['megamenu_on'],
            'mega_submenu_class' => $menuProps['mega_submenu_class'],
            'mega_submenu_item_class' => $menuProps['mega_submenu_item_class'],
            'mega_submenu_link_class' => $menuProps['mega_submenu_link_class'],
            'mega_submenu_link_style' => $menuProps['mega_submenu_link_style'],
            'megapopups' => $menuProps['megapopups'],
        )
    );
}
$menu_html = buildMenu($list, $default_id, $active_id, $path, $menu_html_options);

$resp_menu_options = array(
    'container_class' => $menuProps['container_class'],
    'menu_class' => $menuProps['responsive_menu_class'],
    'item_class' => $menuProps['responsive_item_class'],
    'link_class' => $menuProps['responsive_link_class'],
    'link_style' => $menuProps['responsive_link_style'],
    'submenu_class' => $menuProps['responsive_submenu_class'],
    'submenu_item_class' => $menuProps['responsive_submenu_item_class'],
    'submenu_link_class' => $menuProps['responsive_submenu_link_class'],
    'submenu_link_style' => $menuProps['responsive_submenu_link_style']
);
$resp_menu = buildMenu($list, $default_id, $active_id, $path, $resp_menu_options);

if (preg_match('#<ul[\s\S]*ul>#', $resp_menu, $m)) {
    $responsive_nav = $m[0];
    if (preg_match('#<ul[\s\S]*ul>#', $menu_html, $m)) {
        $regular_nav = $m[0];
        $menu_html = strtr($menuTemplate, array('[[menu]]' => $regular_nav, '[[responsive_menu]]' => $responsive_nav));
        $menu_html = preg_replace('#<\/li>\s+<li#', '</li><li', $menu_html); // remove spaces
        echo $menu_html;
    }
}
