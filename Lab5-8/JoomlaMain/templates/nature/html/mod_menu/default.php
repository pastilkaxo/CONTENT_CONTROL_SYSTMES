<?php
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

require_once dirname(dirname(dirname(__FILE__))) . '/functions.php';

$positionsHelper = PositionsHelper::getInstance();
$attribs = $positionsHelper->getPositionInfo($module->id);

$menuType = isset($attribs['variation']) ? $attribs['variation'] : '';
$modPath = dirname(__FILE__) . '/';
$lang = checkAndGetLanguage();
$templatePath = dirname(__FILE__) . '/hmenu/' . ($lang ? $lang . '/' : '') . 'default_hmenu_' . $attribs['id'] . '.php';
$tagId = ($params->get('tag_id') != NULL) ? ' id="' . $params->get('tag_id') . '"' : '';
if ('' !== $menuType && file_exists($templatePath)) {
    include($templatePath);
} else {
    $menutype = 'default';
    ?>
    <ul class="nav menu<?php echo $class_sfx; ?>"<?php echo $tagId; ?>>
        <?php foreach ($list as $i => &$item)
        {
            $itemParams = $item->getParams();
            $class = 'item-' . $item->id;

            if ($item->id == $default_id)
            {
                $class .= ' default';
            }


            if (($item->id == $active_id) || ($item->type == 'alias' && $itemParams->get('aliasoptions') == $active_id))
            {
                $class .= ' current';
            }

            if (in_array($item->id, $path))
            {
                $class .= ' active';
            }
            elseif ($item->type == 'alias')
            {
                $aliasToId = $itemParams->get('aliasoptions');

                if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
                {
                    $class .= ' active';
                }
                elseif (in_array($aliasToId, $path))
                {
                    $class .= ' alias-parent-active';
                }
            }

            if ($item->type == 'separator')
            {
                $class .= ' divider';
            }

            if ($item->deeper)
            {
                $class .= ' deeper';
            }

            if ($item->parent)
            {
                $class .= ' parent';
            }

            echo '<li class="' . $class . '">';

            switch ($item->type) :
                case 'separator':
                case 'component':
                case 'heading':
                case 'url':
                    require ModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
                    break;

                default:
                    require ModuleHelper::getLayoutPath('mod_menu', 'default_url');
                    break;
            endswitch;

            // The next item is deeper.
            if ($item->deeper)
            {
                echo '<ul class="nav-child unstyled small">';
            }
            // The next item is shallower.
            elseif ($item->shallower)
            {
                echo '</li>';
                echo str_repeat('</ul></li>', $item->level_diff);
            }
            // The next item is on the same level.
            else
            {
                echo '</li>';
            }
        }
        ?></ul>
    <?php
}
?>
