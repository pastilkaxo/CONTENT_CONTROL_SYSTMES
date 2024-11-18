<?php
/**
 * @package   ShackDefaultFiles
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2024 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of ShackDefaultFiles.
 *
 * ShackDefaultFiles is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ShackDefaultFiles is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ShackDefaultFiles.  If not, see <https://www.gnu.org/licenses/>.
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * @property bool $fromInstaller
 */
class JFormFieldCustomFooter extends FormField
{
    /**
     * @inheritdoc
     */
    protected $layout = 'alledia.customfooter';

    /**
     * @var bool
     */
    protected $fromInstaller = false;

    /**
     * @inheritDoc
     */
    public function __set($name, $value = null)
    {
        switch ($name) {
            case 'fromInstaller':
                $this->fromInstaller = (bool)$value;
                break;

            default:
                parent::__set($name, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        if ($path = realpath(__DIR__ . '/../..')) {
            Factory::getLanguage()->load('shackdefaultfiles', $path);
        }

        return parent::setup($element, $value, $group);
    }

    /**
     * @inheritDoc
     */
    protected function getInput()
    {
        return $this->getRenderer($this->layout)->render($this->getLayoutData());
    }

    /**
     * @inheritDoc
     */
    protected function getRenderer($layoutId = 'default')
    {
        $renderer = parent::getRenderer($layoutId);

        if ($layoutId == $this->layout) {
            $renderer->addIncludePath(__DIR__ . '/layouts');
        }

        return $renderer;
    }

    /**
     * @inheritDoc
     */
    protected function getLayoutData()
    {
        $displayData = parent::getLayoutData();

        $requiredClasses = [
            'joomlashack-footer',
            'row-fluid'
        ];
        if ($this->fromInstaller) {
            $requiredClasses[] = 'installer';
        }

        $classes = array_unique(
            array_filter(
                array_merge(
                    preg_split('/\s/', $displayData['class']),
                    $requiredClasses
                )
            )
        );

        $goProUrl    = (string)$this->element['showgoproad'] ?: '0';
        $showGoProAd = !($goProUrl == '0' || $goProUrl == 'false');
        if ($showGoProAd && !filter_var($goProUrl, FILTER_VALIDATE_URL)) {
            $goProUrl = 'https://www.joomlashack.com/plans';
        }

        return array_merge(
            $displayData,
            [
                'class'         => join(' ', $classes),
                'media'         => $this->element['media'],
                'jslogo'        => (string)$this->element['jslogo'] ?: 'joomlashack-logo.png',
                'jshome'        => (string)$this->element['jshome'] ?: 'https://www.joomlashack.com',
                'jedurl'        => (string)$this->element['jedurl'],
                'fromInstaller' => $this->fromInstaller,
                'showGoProAd'   => $showGoProAd,
                'goProUrl'      => $goProUrl
            ]
        );
    }

    /**
     * @param ?string $path
     *
     * @return string
     * @TODO: Doesn't seem to be useful
     */
    protected function getStyle(?string $path): string
    {
        if ($path && is_file($path)) {
            return '<style>' . file_get_contents($path) . '</style>';
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    protected function getLabel()
    {
        return '';
    }

    /**
     * @param SimpleXMLElement $element
     *
     * @return ?string
     */
    public function getInputUsingCustomElement(SimpleXMLElement $element): ?string
    {
        $this->element = $element;
        $this->setup($element, null);

        return $this->getInput();
    }
}
