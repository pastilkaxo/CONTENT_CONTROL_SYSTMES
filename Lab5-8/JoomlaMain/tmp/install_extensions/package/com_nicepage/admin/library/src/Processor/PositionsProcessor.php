<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Layout\LayoutHelper;

class PositionsProcessor
{
    /**
     * @var array All blocks on the page
     */
    public static $blockLayouts = array();

    /**
     * Process all positions on the page
     *
     * @param string $content Page content
     *
     * @return mixed
     */
    public static function process($content) {
        return preg_replace_callback('/<\!--position-->([\s\S]+?)<\!--\/position-->/', self::class . '::processPosition', $content);
    }

    /**
     * Process position
     *
     * @param array $match Position match
     *
     * @return mixed|string
     */
    public static function processPosition($match) {
        $block = $match[1];
        preg_match('/<\!--block-->([\s\S]*?)<\!--\/block-->/', $block, $matches);
        $blockContent = $matches[1];
        $blockContainerParts = explode($matches[0], $block);
        preg_match('/data-position="([^"]*)"/', $block, $match2);
        $position = $match2[1];
        $i = count(self::$blockLayouts) + 1; // blockLayouts used in modules.php
        self::$blockLayouts[$i] = $blockContent;
        $document = Factory::getDocument();
        if ($position && $document->countModules($position) !== 0) {
            $positionContent = '';
            foreach (ModuleHelper::getModules($position) as $mod) {
                // Render the module content
                $attribs = array(
                    'style' => 'upstylefromplugin',
                    'iterator' => $i,
                    'title' => 'name-' . $i,
                    'id' => $i,
                    'name' => $position
                );
                $mod->npAttrs = json_encode($attribs);
                $mod->content = ModuleHelper::renderRawModule($mod, new Registry($mod->params));
                $displayData = array(
                    'module' => $mod,
                    'attribs' => $attribs,
                );
                $positionContent .= LayoutHelper::render('chromes.upstylefromplugin', $displayData, JPATH_ADMINISTRATOR . '/components/com_nicepage/layouts');
            }
            return $blockContainerParts[0] . $positionContent . $blockContainerParts[1];
        } else {
            return '<div class="hidden-position" style="display: none"></div>';
        }
    }
}
