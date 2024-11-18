<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Utility;

use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * Categories  filter processor
 */
class CategoriesFilter
{
    private $_categories;
    private $_currentCatId;
    private $_isTemplatePage;
    private $_isShop;
    private $_pageLink;

    /**
     * @param array $categories List of categories
     * @param array $options    Options
     */
    public function __construct($categories, $options)
    {
        $this->_categories = $categories;
        $this->_currentCatId = $options['categoryId'];
        $this->_isTemplatePage = $options['productName'];
        $this->_isShop = $options['isShop'];
        $link = Uri::root(true) . '/index.php?option=com_nicepage&task=productlist';
        $link .= '&pageId=' . $options['pageId'] . '&position=' . $options['positionOnPage'];
        $this->_pageLink = $link;
    }

    /**
     * Process categories filter
     *
     * @param string $html Page html
     *
     * @return array|string|string[]|null
     */
    public function process($html)
    {
        $re = '/<\!--products_categories_filter_select-->([\s\S]+?)<\!--\/products_categories_filter_select-->/';
        return preg_replace_callback($re, array(&$this, '_processCategoriesFilterSelect'), $html);
    }

    /**
     * Process filter select
     *
     * @param array $selectMatch Matches
     *
     * @return array|string|string[]
     */
    private function _processCategoriesFilterSelect($selectMatch) {
        $selectHtml = $selectMatch[1];
        $selectHtml = preg_replace('/<option[\s\S]+?<\/option>/', '', $selectHtml);
        $optionTemplate = '<option value="[[value]]">[[content]]</option>';
        $options = $this->_getCategoriesFilterOptions($this->_categories, $optionTemplate);
        $selectHtml = str_replace('</select>', $options . '</select>', $selectHtml);
        $script = '';
        if (!$this->_isTemplatePage && !$this->_isShop) {
            $selectHtml = str_replace('u-select-categories', 'u-select-categories u-select-categories-cms', $selectHtml);
            ob_start();
            ?>
            <script>
                jQuery(function ($) {
                    $('.u-select-categories.u-select-categories-cms').on('change', function (event) {
                        var href = this.value;
                        $.post(href).done(function (html) {
                            $(event.currentTarget).closest('.u-products').replaceWith(html);
                        });
                    })
                });
            </script>
            <?php
            $script = ob_get_clean();
        }
        return $selectHtml . $script;
    }

    /**
     * Build options for select
     *
     * @param array  $categories     Product categories
     * @param string $optionTemplate Option template
     * @param int    $level          Level of tree
     *
     * @return string
     */
    private function _getCategoriesFilterOptions($categories, $optionTemplate, $level = 0)
    {
        $result = '';
        foreach ($categories as $category) {
            $value = $category->id;
            if (!$this->_isTemplatePage && !$this->_isShop) {
                $value = $this->_pageLink . '&category_id=' . $value;
            }
            $title = str_repeat('--', $level) . ' ' . $category->title;
            $option = str_replace('[[value]]', $value, $optionTemplate);
            $option = str_replace('[[content]]', $title, $option);
            if ($this->_currentCatId && $this->_currentCatId == $category->id) {
                $option = str_replace('<option', '<option selected', $option);
            }
            $result .= $option . "\n";
            if (count($category->children) > 0) {
                $result .= $this->_getCategoriesFilterOptions($category->children, $optionTemplate, $level + 1);
            }
        }
        return $result;
    }
}
