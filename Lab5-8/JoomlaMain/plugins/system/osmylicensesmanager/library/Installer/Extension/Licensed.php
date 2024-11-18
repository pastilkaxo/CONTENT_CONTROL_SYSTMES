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

namespace Alledia\Installer\Extension;

defined('_JEXEC') or die();

use Alledia\Installer\AutoLoader;

/**
 * Licensed class, for extensions with Free and Pro versions
 */
class Licensed extends Generic
{
    /**
     * License type: free or pro
     *
     * @var string
     */
    protected $license = null;

    /**
     * The path for the pro library
     *
     * @var string
     */
    protected $proLibraryPath = null;

    /**
     * The path for the free library
     *
     * @var string
     */
    protected $libraryPath = null;

    /**
     * @inheritDoc
     */
    public function __construct(string $namespace, string $type, ?string $folder = '', string $basePath = JPATH_SITE)
    {
        parent::__construct($namespace, $type, $folder, $basePath);

        $this->license = $this->manifest ? strtolower($this->manifest->alledia->license) : 'free';

        $this->getLibraryPath();
        $this->getProLibraryPath();
    }

    /**
     * Check if the license is pro
     *
     * @return bool True for pro license
     */
    public function isPro(): bool
    {
        return $this->license === 'pro';
    }

    /**
     * Check if the license is free
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return !$this->isPro();
    }

    /**
     * Get the path for the free library, based on the extension type
     *
     * @return string The path for pro
     */
    public function getLibraryPath(): string
    {
        if (empty($this->libraryPath)) {
            $basePath = $this->getExtensionPath();

            $this->libraryPath = $basePath . '/library';
        }

        return $this->libraryPath;
    }

    /**
     * Get path for the pro library, based on the extension type
     *
     * @return string The path for pro
     */
    public function getProLibraryPath(): string
    {
        if ($this->proLibraryPath === null) {
            $basePath = $this->getLibraryPath();

            $this->proLibraryPath = $basePath . '/Pro';
        }

        return $this->proLibraryPath;
    }

    /**
     * Loads the library, if existent (including the Pro Library)
     *
     * @return bool
     * @throws \Exception
     */
    public function loadLibrary(): bool
    {
        $libraryPath = $this->getLibraryPath();

        // If we have a library path, lets load it
        if (is_dir($libraryPath)) {
            if ($this->isPro()) {
                // Check if the pro library exists
                if (!is_dir($this->getProLibraryPath())) {
                    throw new \Exception("Pro library not found: {$this->type}, {$this->element}");
                }
            }

            AutoLoader::register('Alledia\\' . $this->namespace, $libraryPath);

            return true;
        }

        return false;
    }
}
