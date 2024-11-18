<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor;

defined('_JEXEC') or die;

class ControlsProcessor
{
    public static $controlName = '';
    public static $options = null;

    /**
     * Process all custom controls on the header
     *
     * @param string $content content
     * @param null   $options Site Settings
     *
     * @return mixed
     */
    public static function process($content, $options = null) {
        $controls = array('menu', 'logo', 'headline', 'search', 'login', 'language');
        foreach ($controls as $value) {
            self::$controlName = $value;
            self::$options = $options;
            $content =  preg_replace_callback(
                '/<\!--np_' . $value . '--><!--np_json-->([\s\S]+?)<\!--\/np_json-->([\s\S]*?)<\!--\/np_' . $value . '-->/',
                self::class . '::processControl',
                $content
            );
        }
        return $content;
    }

    /**
     * Process control
     *
     * @param array $matches Matches
     *
     * @return false|string
     */
    public static function processControl($matches) {
        $controlProps = json_decode(trim($matches[1]), true);
        $controlTemplate = $matches[2];
        $pluginOptions = self::$options;
        ob_start();
        include JPATH_ADMINISTRATOR . '/components/com_nicepage/views/controls/'. self::$controlName . '/' . self::$controlName . '.php';
        return ob_get_clean();
    }
}
