<?php
namespace Fab\Media\FileUpload;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handle a posted file encoded in base 64.
 */
class Base64File extends \Fab\Media\FileUpload\UploadedFileAbstract
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
     * @var string
     */
    protected $image;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @return \Fab\Media\FileUpload\Base64File
     */
    public function __construct()
    {

        // Processes the encoded image data and returns the decoded image
        $encodedImage = GeneralUtility::_POST($this->inputName);
        if (preg_match('/^data:image\/(jpg|jpeg|png)/i', $encodedImage, $matches)) {
            $this->extension = $matches[1];
        } else {
            return false;
        }

        // Remove the mime-type header
        $data = reset(array_reverse(explode('base64,', $encodedImage)));

        // Use strict mode to prevent characters from outside the base64 range
        $this->image = base64_decode($data, true);

        if (!$this->image) {
            return false;
        }

        $this->setName(uniqid() . '.' . $this->extension);
    }

    /**
     * Save the file to the specified path
     *
     * @throws \Fab\Media\Exception\EmptyPropertyException
     * @return boolean true on success
     */
    public function save()
    {

        if (is_null($this->uploadFolder)) {
            throw new \Fab\Media\Exception\EmptyPropertyException('Upload folder is not defined', 1362587741);
        }

        if (is_null($this->name)) {
            throw new \Fab\Media\Exception\EmptyPropertyException('File name is not defined', 1362587742);
        }

        return file_put_contents($this->getFileWithAbsolutePath(), $this->image) > 0;
    }

    /**
     * Get the original file name.
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->getName();
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
