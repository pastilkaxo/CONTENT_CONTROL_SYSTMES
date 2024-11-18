<?php
/**
 * @package   ShackInstaller
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2023 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of ShackInstaller.
 *
 * ShackInstaller is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ShackInstaller is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ShackInstaller.  If not, see <https://www.gnu.org/licenses/>.
 */

use Alledia\Installer\AbstractScript;
use Alledia\Installer\Extension\Generic;
use Alledia\Installer\Extension\Licensed;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/**
 * @var AbstractScript $this
 * @var string         $type
 * @var Licensed       $license
 * @var string         $name
 * @var string         $configPath
 * @var string         $customFooterPath
 * @var string         $extensionPath
 * @var Generic        $licensesManagerExtension
 * @var string         $string
 * @var string         $path
 */

?>
<div class="joomlashack-details-container">
    <a href="javascript:void(0);" id="joomlashack-installer-footer-toggler">
        <?php echo Text::_('LIB_SHACKINSTALLER_SHOW_DETAILS'); ?>
    </a>

    <div id="joomlashack-installer-footer" style="display: none;">
        <div class="joomlashack-license">
            <?php echo Text::sprintf('LIB_SHACKINSTALLER_RELEASE_V', (string)$this->manifest->version); ?>
        </div>
        <br>
        <?php if (!empty($this->manifest->alledia->relatedExtensions)) : ?>
            <table class="joomlashack-related-table">
                <thead>
                <tr>
                    <th colspan="2"><?php echo Text::_('LIB_SHACKINSTALLER_RELATED_EXTENSIONS'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($this->relatedExtensionFeedback as $data) : ?>
                    <tr>
                        <td><?php echo Text::_($data['name']); ?></td>
                        <td>
                            <?php
                            $messages = [$data['message']];

                            if (isset($data['publish']) && $data['publish']) {
                                $messages[] = Text::_('LIB_SHACKINSTALLER_PUBLISHED');
                            }

                            if (isset($data['ordering'])) {
                                $messages[] = Text::sprintf('LIB_SHACKINSTALLER_SORTED', $data['ordering']);
                            }

                            $messages = implode(', ', $messages);
                            echo $messages;
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="joomlashack-license">
            <?php
            echo Text::sprintf(
                'LIB_SHACKINSTALLER_LICENSED_AS',
                $this->getName(),
                '<a href="https://www.gnu.org/licenses/gpl-3.0">GNU/GPL v3.0</a>'
            );
            ?>.
        </div>
    </div>

</div>

<script>
    (function() {
        let footer = document.getElementById('joomlashack-installer-footer'),
            toggle = document.getElementById('joomlashack-installer-footer-toggler');

        if (footer && toggle) {
            toggle.addEventListener('click', function(event) {
                event.preventDefault();

                footer.style.display = 'block';
                this.style.display   = 'none';
            });
        }
    })();
</script>
