<?php
/**
 * @package   ShackDefaultFiles
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2018-2024 Joomlashack.com. All rights reserved
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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/**
 * @var array $displayData
 */

$class         = $displayData['class'];
$media         = $displayData['media'];
$jslogo        = $media . '/' . $displayData['jslogo'];
$jedurl        = $displayData['jedurl'];
$showGoProAd   = $displayData['showGoProAd'];
$goProUrl      = $displayData['goProUrl'];
$fromInstaller = $displayData['fromInstaller'];

$footerCss = HTMLHelper::_('stylesheet', $media . '/field_customfooter.css', ['relative' => true, 'pathOnly' => true]);
$adminCss  = HTMLHelper::_('stylesheet', $media . '/admin-default.css', ['relative' => true, 'pathOnly' => true]);
?>
<link href="<?php echo $footerCss; ?>" rel="stylesheet"/>
<link href="<?php echo $adminCss; ?>" rel="stylesheet"/>
<div class="<?php echo $class; ?>">
    <div>
        <?php
        if ($showGoProAd) :
            ?>
            <div class="gopro-ad">
                <?php
                echo HTMLHelper::_(
                    'link',
                    $goProUrl,
                    Text::_('SHACKDEFAULTFILES_GO_PRO'),
                    'class="gopto-btn" target="_blank"'
                );
                ?>
            </div>
        <?php endif;

        if ($jedurl) : ?>
            <div class="joomlashack-jedlink">
                <?php
                echo Text::_('SHACKDEFAULTFILES_LIKE_THIS_EXTENSION');
                echo '&nbsp;'
                    . HTMLHelper::_(
                        'link',
                        $jedurl,
                        Text::_('SHACKDEFAULTFILES_LEAVE_A_REVIEW_ON_JED'),
                        'target="_blank"'
                    );
                echo '&nbsp;' . str_repeat("<i class=\"icon-star\"></i>", 5);
                ?>
            </div>
        <?php endif; ?>
        <div class="poweredby">
            Powered by
            <?php
            echo HTMLHelper::_(
                'link',
                'https://www.joomlashack.com',
                HTMLHelper::_('image', $jslogo, 'Joomlashack', 'class="joomlashack-logo" width="150"', true),
                'target="_blank"'
            );
            ?>
        </div>

        <div class="joomlashack-copyright">
            <?php echo '&copy; ' . date('Y'); ?> Joomlashack.com. All rights reserved.
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let footer    = document.getElementsByClassName('joomlashack-footer').item(0),
            container = footer ? footer.parentElement : null,
            wrapper   = null;

        if (container && container.classList.contains('controls')) {
            wrapper = document.getElementById('content') || document.querySelector('.container-popup');
        }

        if (footer && wrapper) {
            wrapper.parentNode.insertBefore(footer, wrapper.nextSibling);
        } else if (footer) {
            footer.remove();
        }
    });
</script>
