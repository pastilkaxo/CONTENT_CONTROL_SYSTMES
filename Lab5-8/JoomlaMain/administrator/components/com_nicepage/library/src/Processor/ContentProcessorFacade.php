<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Processor;

defined('_JEXEC') or die;

use NP\Virtuemart\VirtuemartComponent;

class ContentProcessorFacade
{
    private $_isPulic;
    private $_pageId;
    private $_settings;
    private $_propsId;
    private $_productName;

    /**
     * ContentProcessorFacade constructor.
     *
     * @param null $options Site Settings
     */
    public function __construct($options = array())
    {
        $this->_isPulic = isset($options['isPublic']) ? $options['isPublic'] : true;
        $this->_pageId = isset($options['pageId']) ? $options['pageId'] : '';
        $this->_propsId = isset($options['propsId']) ? $options['propsId'] : '';
        $this->_settings = isset($options['settings']) ? $options['settings'] : null;
        $this->_productName = isset($options['productName']) ? $options['productName'] : '';
    }

    /**
     * Process content
     *
     * @param string $content Page content
     *
     * @return mixed|string|string[]|null
     */
    public function process($content)
    {
        $common = new CommonProcessor();
        $content = $common->processDefaultImage($content);
        if ($this->_isPulic) {
            $content = $common->processLinks($content, $this->_pageId, $this->_propsId);
            $content = $common->processForm($content, $this->_pageId);
            $content = $common->processCustomPhp($content);
            $content = $common->processGoogleMaps($content);
            $content = ControlsProcessor::process($content, $this->_settings);

            $blog = new BlogProcessor($this->_pageId);
            $content = $blog->process($content);

            $products = new ProductsProcessor($this->_pageId, $this->_productName);
            $content = $products->process($content);

            if (VirtuemartComponent::exists()) {
                $shoppingCart = new ShoppingCartProcessor();
                $content = $shoppingCart->process($content);
            }
        }
        $content = PositionsProcessor::process($content);
        return $content;
    }
}
