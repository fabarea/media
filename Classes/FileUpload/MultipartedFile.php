<?php
namespace Fab\Media\FileUpload;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
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
     * @return boolean true on success
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
