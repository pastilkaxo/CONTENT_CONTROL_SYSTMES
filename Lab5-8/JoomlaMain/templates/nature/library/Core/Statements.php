<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

class CoreStatements {

    public static function containsModules()
    {
        $doc = Factory::getApplication()->getDocument();
        foreach (func_get_args() as $position)
            if (0 != $doc->countModules($position))
                return true;
        return false;
    }

    public static function position($position, $style = null, $id = '', $variation = '')
    {
        if (!self::containsModules($position)) {
            return '';
        }

        $app = Factory::getApplication();
        $document = $app->getDocument();

        $attributes = array(
            'type'      => 'modules',
            'name'      => $position,
            'style'     => 'upstyle',
            'variation' => $variation,
            'upstyle'   => (null != $style ?  $style : ''),
            'title'     => 'name-' . $id,
            'id'        => $id,
            'count'     => $document->countModules($position)
        );

        $modules = ModuleHelper::getModules($position);
        $positionsHelper = PositionsHelper::getInstance();
        foreach ($modules as $module) {
            $positionsHelper->addPositionInfo($module->id, $attributes);
        }

        $themeHelper = ThemeHelper::getInstance();
        if (isset($themeHelper->pageType) && $themeHelper->pageType === '404') {
            return $document->getBuffer('modules', $position, $attributes);
        }

        $config = $app->getConfig();
        $isProgressiveCaching = $config->get('caching') && $config->get('caching', 2) == 2 ? true : false;
        if ($isProgressiveCaching) {
            unset($attributes['title']);
        }

        $str = '';
        foreach ($attributes as $key => $value) {
            $str .= $key . '="' . $value . '" ';
        }
        return '<jdoc:include ' . $str . '/>';
    }

    public static function head()
    {
        return '<jdoc:include type="head" />';
    }

    public static function message()
    {
        return '<jdoc:include type="message" />';
    }

    public  static function component()
    {
        return '<jdoc:include type="component" />';
    }
}
