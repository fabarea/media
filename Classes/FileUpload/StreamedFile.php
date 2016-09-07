<?php
namespace Fab\Media\FileUpload;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Exception\EmptyPropertyException;

/**
 * Handle file uploads via XMLHttpRequest.
 *
 * @see original implementation: https://github.com/valums/file-uploader/blob/master/server/php.php
 */
class StreamedFile extends \Fab\Media\FileUpload\UploadedFileAbstract
{

    /**
     * @var string
     */
    protected $inputName = 'qqfile';

    /**
     * @var string
     */
    protected $uploadFolder;

    /**
     * @var string
     */
    protected $name;

    /**
     * Save the file to the specified path
     *
     * @throws EmptyPropertyException
     * @return boolean true on success
     */
    public function save()
    {

        if (is_null($this->uploadFolder)) {
            throw new EmptyPropertyException('Upload folder is not defined', 1361787579);
        }

        if (is_null($this->name)) {
            throw new EmptyPropertyException('File name is not defined', 1361787580);
        }

        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()) {
            return false;
        }

        $target = fopen($this->getFileWithAbsolutePath(), "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }

    /**
     * Get the original file name.
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $_GET[$this->inputName];
    }

    /**
     * Get the file size
     *
     * @throws \Exception
     * @return integer file-size in byte
     */
    public function getSize()
    {
        if (isset($GLOBALS['_SERVER']['CONTENT_LENGTH'])) {
            return (int)$GLOBALS['_SERVER']['CONTENT_LENGTH'];
        } else {
            throw new \Exception('Getting content length is not supported.');
        }
    }

    /**
     * Get MIME type of file.
     *
     * @return string|boolean MIME type. eg, text/html, false on error
     */
    public function getMimeType()
    {
        $this->checkFileExistence();
        if (function_exists('finfo_file')) {
            $fileInfo = new \finfo();
            return $fileInfo->file($this->getFileWithAbsolutePath(), FILEINFO_MIME_TYPE);
        } elseif (function_exists('mime_content_type')) {
            return mime_content_type($this->getFileWithAbsolutePath());
        }
        return false;
    }
}
