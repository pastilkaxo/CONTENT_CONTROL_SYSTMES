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
<div class="joomlashack-wrapper">
    <div class="joomlashack-content">
        <h2><?php echo $this->welcomeMessage; ?></h2>

        <?php
        if (is_file(__DIR__ . '/default_custom.php')) :
            include __DIR__ . '/default_custom.php';
        endif;

        if ($license->isPro()) :
            include __DIR__ . '/default_license.php';
        endif;

        include __DIR__ . "/default_info.php";
        ?>

        <?php echo $this->footer; ?>
    </div>
</div>
