<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('JPATH_BASE') or die();

use Joomla\CMS\Factory;
use Joomla\Filesystem\Path;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Application\ApplicationHelper;

/**
 * Class NicepageInstaller
 */
class NicepageInstaller extends Installer
{
    /**
     * Exception no replace
     */
    const EXCEPTION_NO_REPLACE = 'noreplace';

    /**
     * @var array
     */
    protected $no_overwrite = array();

    /**
     * @var
     */
    protected $backup_dir;

    /**
     * @var
     */
    protected $itemInfo;

    /**
     * @var
     */
    protected $installtype = 'install';

    /**
     * NicepageInstaller constructor.
     *
     * @param null $basepath      Base path
     * @param null $classprefix   Class prefix
     * @param null $adapterfolder Adapter folder
     */
    public function __construct($basepath = null, $classprefix = null, $adapterfolder = null)
    {
        parent::__construct();

        $this->_basepath = dirname(__FILE__);
        $this->_classprefix = 'NicepageInstaller';
        $this->_adapterfolder = 'adapters';
    }

    /**
     * Get NicepageInstaller instance
     *
     * @param null $basepath      Base path
     * @param null $classprefix   Class prefix
     * @param null $adapterfolder Adapter folder
     *
     * @return NicepageInstaller
     */
    public static function getInstance($basepath = null, $classprefix = null, $adapterfolder = null)
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new NicepageInstaller;
        }
        return $instance;
    }

    /**
     * Method to install extension
     *
     * @param null $path Path
     *
     * @return mixed
     */
    public function install($path = null)
    {
        $result = parent::install($path);

        $type = (string) $this->manifest->attributes()->type;

        return $result;
    }

    /**
     * @param $type
     */
    public function setInstallType($type)
    {
        $this->installtype = $type;
    }

    /**
     * Get install type component|plugin|module
     *
     * @return mixed
     */
    public function getInstallType()
    {
        return $this->installtype;
    }

    /**
     * Mrthod prepExceptions
     *
     * @param object $element Element
     * @param int    $cid     Cid
     *
     * @return bool|int
     */
    protected function prepExceptions($element, $cid = 0)
    {
        $config = Factory::getConfig();

        $this->backup_dir = $config->get('tmp_path') . '/' . uniqid('backup_');

        if (!Folder::create($this->backup_dir)) {
            Factory::getApplication()->enqueueMessage('Installer::install: ' . Text::_('Failed to create directory') . ' "' . $this->backup_dir . '"', 'warning');
            return false;
        }

        // Get the client info
        jimport('joomla.application.helper');
        $client = ApplicationHelper::getClientInfo($cid);

        if (!is_object($element) || !count($element->children())) {
            return 0;
        }

        $files = $element->children();

        if (count($files) == 0) {
            return 0;
        }

        /*
         * Here we set the folder we are going to remove the files from.
         */
        if ($client) {
            $pathname    = 'extension_' . $client->name;
            $destination = $this->getPath($pathname);
        } else {
            $pathname    = 'extension_root';
            $destination = $this->getPath($pathname);
        }

        // Process each file in the $files array (children of $tagName).
        foreach ($files as $file) {
            $exception_type = $file->attributes('type');
            $current_file   = $destination . '/' . $file->data();

            if ($exception_type == self::EXCEPTION_NO_REPLACE && file_exists($current_file)) {
                $type = ($file->name() == 'folder') ? 'folder' : 'file';

                $backuppath['src']  = $current_file;
                $backuppath['dest'] = $this->backup_dir . '/' . $file->data();
                $backuppath['type'] = $type;

                $replacepath['src']  = $backuppath['dest'];
                $replacepath['dest'] = $backuppath['src'];
                $replacepath['type'] = $type;

                $this->no_overwrite[] = $replacepath;

                if (!$this->copyFiles(array($backuppath))) {
                    Factory::getApplication()->enqueueMessage('Installer::install: ' . Text::_('Failed to copy backup to ') . ' "' . $backuppath['dest'] . '"', 'warning');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Method finishExceptions
     */
    public function finishExceptions()
    {
        if (($this->upgrade && !empty($this->no_overwrite)) || !$this->upgrade) {
            foreach ($this->no_overwrite as $restore) {
                if (Path::canChmod($restore['dest'])) {
                    Path::setPermissions($restore['dest']);
                }
            }
            if ($this->copyFiles($this->no_overwrite)) {
                Folder::delete($this->backup_dir);
            }
        }
    }

    /**
     * Copy files
     *
     * @param array $files     Files list
     * @param null  $overwrite Flag overwrite
     *
     * @return mixed
     */
    public function copyFiles($files, $overwrite = null)
    {
        // To allow for manual override on the overwriting flag, we check to see if
        // the $overwrite flag was set and is a boolean value. If not, use the object
        // allowOverwrite flag.
        if (is_null($overwrite) || !is_bool($overwrite)) {
            $overwrite = $this->overwrite;
        }

        $ftp = ClientHelper::getCredentials('ftp');

        if (!$ftp['enabled'] && $overwrite && is_array($files)) {
            foreach ($files as $file) {
                $filedest = Path::clean($file['dest']);
                $filetype = array_key_exists('type', $file) ? $file['type'] : 'file';

                switch ($filetype) {
                case 'file':
                    if (File::exists($filedest) && Path::isOwner($filedest)) {
                        Path::setPermissions($filedest);
                    }
                    break;

                case 'folder':
                    if (Folder::exists($filedest) && Path::isOwner($filedest)) {
                        Path::setPermissions($filedest);
                    }
                    break;
                }
            }
        }
        return parent::copyFiles($files, $overwrite);
    }

    /**
     * Set custom item info
     *
     * @param array $itemInfo Item info
     */
    public function setItemInfo($itemInfo)
    {
        $this->itemInfo = $itemInfo;
    }

    /**
     * Get custom item info
     *
     * @return mixed
     */
    public function getItemInfo()
    {
        return $this->itemInfo;
    }
}
