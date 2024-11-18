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
use Joomla\CMS\Uri\Uri;

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

$licenseUpdate = Uri::root() . '/administrator/index.php?plugin=system_osmylicensesmanager&task=license.save';

if ($this->isLicensesManagerInstalled) :
    ?>
    <div class="joomlashack-license-form">
        <?php
        if (!empty($this->licenseKey)) :
            ?>
            <a href="" class="joomlashack-installer-change-license-button btn btn-success">
                <?php echo Text::_('LIB_SHACKINSTALLER_CHANGE_LICENSE_KEY'); ?>
            </a>
        <?php endif; ?>

        <div id="joomlashack-installer-license-panel">
            <input type="text"
                   name="joomlashack-license-keys"
                   id="joomlashack-license-keys"
                   value="<?php echo $this->licenseKey; ?>"
                   class="form-control"
                   placeholder="<?php echo Text::_('LIB_SHACKINSTALLER_LICENSE_KEYS_PLACEHOLDER'); ?>"/>

            <p class="joomlashack-empty-key-msg">
                <?php echo Text::_('LIB_SHACKINSTALLER_MSG_LICENSE_KEYS_EMPTY'); ?>&nbsp;
                <a href="https://www.joomlashack.com/account/key/" target="_blank">
                    <?php echo Text::_('LIB_SHACKINSTALLER_I_DONT_REMEMBER_MY_KEY'); ?>
                </a>
            </p>

            <a id="joomlashack-license-save-button"
               class="btn btn-success"
               href="#">
                <?php echo Text::_('LIB_SHACKINSTALLER_SAVE_LICENSE_KEY'); ?>
            </a>
        </div>

        <div id="joomlashack-installer-license-success" style="display: none">
            <p><?php echo Text::_('LIB_SHACKINSTALLER_LICENSE_KEY_SUCCESS'); ?></p>
        </div>

        <div id="joomlashack-installer-license-error" style="display: none">
            <p><?php echo Text::_('LIB_SHACKINSTALLER_LICENSE_KEY_ERROR'); ?></p>
        </div>
    </div>

    <script>
        (function() {
            let panel         = document.getElementById('joomlashack-installer-license-panel'),
                updateButtons = document.getElementsByClassName('joomlashack-installer-change-license-button'),
                saveButton    = document.getElementById('joomlashack-license-save-button');

            if (panel) {
                if (updateButtons.length > 0) {
                    panel.style.display = 'none';

                    Array.from(updateButtons).forEach(function(button) {
                        button.addEventListener('click', function(event) {
                            event.preventDefault();

                            panel.style.display = 'block';
                            this.style.display  = 'none';
                        })
                    });
                }

                if (saveButton) {
                    saveButton.addEventListener('click', function(event) {
                        event.preventDefault();

                        let request  = new XMLHttpRequest(),
                            data     = new FormData(),
                            keyField = document.getElementById('joomlashack-license-keys')

                        data.append('license-keys', keyField.value)
                        request.onreadystatechange = function(data) {
                            if (this.readyState === XMLHttpRequest.DONE) {
                                try {
                                    if (this.status === 200) {
                                        let result  = JSON.parse(this.response),
                                            success = document.getElementById('joomlashack-installer-license-success'),
                                            error   = document.getElementById('joomlashack-installer-license-error');

                                        panel.style.display = 'none';

                                        if (result.success) {
                                            success.style.display = 'block';

                                        } else {
                                            error.style.display = 'block';
                                        }

                                    } else {
                                        error.style.display = 'block';
                                    }

                                } catch (e) {
                                    panel.style.display = 'none';
                                    error.style.display = 'block';
                                }
                            }
                        };

                        request.open('POST', '<?php echo $licenseUpdate; ?>');
                        request.send(data);
                    });
                }
            }
        })();
    </script>

<?php else : ?>
    <div class="error">
        <?php echo Text::_('LIB_SHACKINSTALLER_LICENSE_KEYS_MANAGER_REQUIRED'); ?>
    </div>
<?php endif;
