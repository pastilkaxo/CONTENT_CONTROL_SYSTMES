<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class Pagination
{
    private $_allPosts;
    private $_offset;
    private $_postsPerPage;

    private $_pageLink;
    private $_styleOptions;

    /**
     * Pagination constructor.
     *
     * @param array $options      Pagination options
     * @param array $styleOptions Pagination style options
     * @param array $siteProducts Products
     */
    public function __construct($options = array(), $styleOptions = array())
    {
        $this->_styleOptions = $styleOptions;

        $this->_allPosts = $options['allPosts'];
        $this->_offset = $options['offset'];
        $this->_postsPerPage = $options['postsPerPage'];

        $template = Factory::getApplication()->getTemplate();
        $this->_pageLink = '/index.php?option=com_ajax&format=html&template=' . $template . '&method=products&product_name=product-list';
    }

    /**
     * Get builded pagination
     *
     * @return false|mixed|string
     */
    public function getPagination()
    {
        $list = $this->_buildPagination();

        $ul = $this->_styleOptions['ul'];
        $ulSelector = 'u-pagination';
        if (preg_match('/class=[\'\"]([^\'\"]+?)[\'\"]/', $ul, $matches)) {
            $classesParts = explode(' ', $matches[1]);
            $ulSelector = '';
            foreach ($classesParts as $part) {
                $ulSelector .= '.' . trim($part);
            }
        }
        $li = $this->_styleOptions['li'];

        $li_active = str_replace('class="', 'class="active ', $li);
        $li_start = str_replace('class="', 'class="start ', $li);
        $li_prev = str_replace('class="', 'class="prev ', $li);
        $li_next = str_replace('class="', 'class="next ', $li);
        $li_end = str_replace('class="', 'class="end ', $li);

        $link = $this->_styleOptions['link'];

        ob_start();
        ?>
        <ul <?php echo $ul; ?>>
            <?php if ($list['start']['active']) : ?>
                <li <?php echo $li_start; ?>>
                    <?php echo $list['start']['data']; ?>
                </li>
            <?php endif; ?>
            <?php if ($list['previous']['active']) : ?>
                <li <?php echo $li_prev; ?>>
                    <?php echo $list['previous']['data']; ?>
                </li>
            <?php endif; ?>
            <?php foreach ($list['pages'] as $page) : ?>
                <?php echo '<li ' . ($page['active'] ? $li : $li_active) . '>' . $page['data'] . '</li>'; ?>
            <?php endforeach; ?>
            <?php if ($list['next']['active']) : ?>
                <li <?php echo $li_next; ?>><?php echo $list['next']['data']; ?></li>
            <?php endif; ?>
            <?php if ($list['end']['active']) : ?>
                <li <?php echo $li_end; ?>><?php echo $list['end']['data']; ?></li>
            <?php endif; ?>
        </ul>
        <script>
            jQuery(function ($) {
                $('<?php echo $ulSelector; ?> a').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var a = $(this);
                    var href = a.attr('href');
                    $.post(href).done(function (html) {
                        a.closest('.u-list-control').parents('section').replaceWith(html);
                    });
                })
            });
        </script>
        <?php
        $html = ob_get_clean();

        $html = str_replace('<a ', '<a ' . $link . ' ', $html);
        $html = str_replace('<span', '<span ' . $link . ' ', $html);
        return $html;
    }

    /**
     * Build pagination
     *
     * @return array
     */
    private function _buildPagination()
    {
        $list = array();
        $list['start'] = $this->_getStartLink();
        $list['previous'] = $this->_getPreviousLink();
        $list['pages'] = $this->_getPageLinks();
        $list['next'] = $this->_getNextLink();
        $list['end'] = $this->_getEndLink();
        return $list;
    }

    /**
     * Get start link
     *
     * @return array
     */
    private function _getStartLink()
    {
        if ($this->_offset == 0) {
            $active = false;
            $data = '<span>&#12298</span>';
        } else {
            $active = true;
            $startHref = Uri::root(true) . $this->_pageLink . '&offset=0';
            $data = '<a title="Start" href="' . $startHref . '">&#12298</a>';
        }
        return array(
            'active' => $active,
            'data' => $data,
        );
    }

    /**
     * Get previous link
     *
     * @return array
     */
    private function _getPreviousLink()
    {
        if ($this->_offset == 0) {
            $active = false;
            $data = '<span>&#12296</span>';
        } else {
            $active = true;
            $previousDiv = $this->_offset - $this->_postsPerPage;
            $previousOffset = $previousDiv > 0 ? $previousDiv : 0;
            $previousHref = Uri::root(true) . $this->_pageLink . '&offset=' . $previousOffset;
            $data = '<a title="Prev" href="' . $previousHref . '">&#12296</a>';
        }
        return array(
            'active' => $active,
            'data' => $data,
        );
    }

    /**
     * Get page links
     *
     * @return array
     */
    private function _getPageLinks()
    {
        $result = array();
        $all = $this->_allPosts;
        $limit = 0;
        $i = 1;
        $activeId = 1;
        while ($all > $limit) {
            if ($limit === $this->_offset) {
                $active = false;
                $data = '<span>' . $i .'</span>';
                $activeId = $i;
            } else {
                $active = true;
                $data = '<a href="' . Uri::root(true) . $this->_pageLink . '&offset=' . $limit . '">' . $i .'</a>';
            }
            array_push($result, array('active' => $active, 'data' => $data));
            $limit += $this->_postsPerPage;
            $i += 1;
        }

        $maxPages = 10;
        $halfMaxPages = 5;
        if (count($result) > $maxPages) {
            $leftSide = array_slice($result, 0, $activeId);
            $rightSide = array_slice($result, $activeId);
            if (count($leftSide) < $halfMaxPages) {
                $leftPart = $leftSide;
                $need = $maxPages - count($leftPart);
                $rightPart = array_slice($result, $activeId, $need);
            } else if (count($rightSide) < $halfMaxPages) {
                $rightPart = $rightSide;
                $need = $maxPages - count($rightPart);
                $leftPart = array_slice($result, $activeId - $need, $need);
            } else {
                $leftPart = array_slice($result, $activeId - $halfMaxPages, $halfMaxPages);
                $rightPart = array_slice($result, $activeId, $halfMaxPages);
            }
            $result = array_merge($leftPart, $rightPart);
        }
        return $result;
    }

    /**
     * Get next link
     *
     * @return array
     */
    private function _getNextLink()
    {
        $nextLimitStart = $this->_offset + $this->_postsPerPage;
        if ($nextLimitStart > $this->_allPosts) {
            $active = false;
            $data = '<span>&#12297</span>';
        } else {
            $active = true;
            $nextHref = Uri::root(true) . $this->_pageLink . '&offset=' . $nextLimitStart;
            $data = '<a title="Next" href="' . $nextHref . '">&#12297</a>';
        }
        return array(
            'active' => $active,
            'data' => $data,
        );
    }

    /**
     * Get end link
     *
     * @return array
     */
    private function _getEndLink()
    {
        $endLimitStart = $this->_offset + $this->_postsPerPage;
        if ($endLimitStart > $this->_allPosts) {
            $active = false;
            $data = '<span>&#12299</span>';
        } else {
            $active = true;
            $mod = $this->_allPosts % $this->_postsPerPage;
            $term = $mod === 0 ? $this->_postsPerPage : $mod;
            $endLimitStart = $this->_allPosts - $term;
            $endHref = Uri::root(true) . $this->_pageLink . '&offset=' . $endLimitStart;
            $data = '<a title="End" href="' . $endHref . '">&#12299</a>';
        }
        return array(
            'active' => $active,
            'data' => $data,
        );
    }
}
