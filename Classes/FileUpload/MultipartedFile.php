<?php
namespace Fab\Media\FileUpload;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 *
 * @see original implementation: https://github.com/valums/file-uploader/blob/master/server/php.php
 */
class MultipartedFile extends UploadedFileAbstract
{

    /**
     * @var string
     */
    protected $inputName = 'qqfile';

    /**
     * Save the file to the specified path
     *
     * @return boolean TRUE on success
     */
    public function save()
    {
        return move_uploaded_file($_FILES[$this->inputName]['tmp_name'], $this->getFileWithAbsolutePath());
    }

    /**
     * Get the original filename
     *
     * @return string filename
     */
    public function getOriginalName()
    {
        return $_FILES[$this->inputName]['name'];
    }

    /**
     * Get the file size
     *
     * @return integer file-size in byte
     */
    public function getSize()
    {
        return $_FILES[$this->inputName]['size'];
    }

    /**
     * Get the mime type of the file.
     *
     * @return int
     */
    public function getMimeType()
    {
        return $_FILES[$this->inputName]['type'];
    }
}
