<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Uploader;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;

/**
 * Class FileUploader
 */
class FileUploader
{
    /**
     * @param string $uploadPath Upload path
     * @param bool   $isLast     Last chunk flag
     *
     * @return array
     */
    public function upload($uploadPath, $isLast)
    {
        $files = Factory::getApplication()->input->files;
        $chunk = $files->get('chunk');
        if (!$chunk || !file_exists($chunk['tmp_name'])) {
            trigger_error('Empty chunk data', E_USER_ERROR);
        }

        $rangeBegin = 0;
        $contentRange = isset($_SERVER['HTTP_CONTENT_RANGE']) ? $_SERVER['HTTP_CONTENT_RANGE'] : '';

        if ($contentRange) {
            $contentRange = str_replace('bytes ', '', $contentRange);
            list($range, $total) = explode('/', $contentRange);
            list($rangeBegin, $rangeEnd) = explode('-', $range);
        }

        $tmpPath = dirname($uploadPath) . '/uptmp/' . basename($uploadPath);
        Folder::create(dirname($tmpPath));

        $f = fopen($tmpPath, 'c');

        if (flock($f, LOCK_EX)) {
            fseek($f, (int) $rangeBegin);
            fwrite($f, file_get_contents($chunk['tmp_name']));

            flock($f, LOCK_UN);
            fclose($f);
        }

        $result = array(
            'status' => 'processed'
        );
        if ($isLast) {
            if (file_exists($uploadPath)) {
                $uploadPath = $this->_getNewUploadPath($uploadPath);
            }
            Folder::create(dirname($uploadPath));
            File::move($tmpPath, $uploadPath);
            Folder::delete(dirname($tmpPath));
            $result = array(
                'status' => 'done',
                'fileName' => basename($uploadPath),
                'path' => $uploadPath,
            );
        }
        return $result;
    }

    /**
     * Get new file path
     *
     * @param string $filePath File path
     *
     * @return mixed
     */
    private function _getNewUploadPath($filePath) {
        $baseName = basename($filePath);
        $fileParts = explode('.', $baseName);
        $fileName = $fileParts[0];
        $fileExt = $fileParts[1];
        $i = 1;
        do {
            $newBaseName = $fileName . '-' . $i . '.' . $fileExt;
            $newFilePath = str_replace($baseName, $newBaseName, $filePath);
            $i++;
        } while (file_exists($newFilePath) && $i < 20);
        return $newFilePath;
    }
}