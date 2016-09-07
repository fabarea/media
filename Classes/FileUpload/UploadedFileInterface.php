<?php
namespace Fab\Media\FileUpload;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * A interface dealing with uploaded file.
 */
interface UploadedFileInterface
{

    /**
     * Save the file to the specified path.
     *
     * @return boolean true on success
     */
    public function save();

    /**
     * Get the original filename.
     *
     * @return string filename
     */
    public function getOriginalName();

    /**
     * Get the file size.
     *
     * @return int
     */
    public function getSize();

    /**
     * Get the file name.
     *
     * @return int
     */
    public function getName();

    /**
     * Get the file type.
     *
     * @return int
     */
    public function getType();

    /**
     * Get the mime type of the file.
     *
     * @return int
     */
    public function getMimeType();

    /**
     * Get the file with its absolute path.
     *
     * @return string
     */
    public function getFileWithAbsolutePath();

    /**
     * Get the file's public URL.
     *
     * @return string
     */
    public function getPublicUrl();

    /**
     * Set the file input name from the DOM.
     *
     * @param string $inputName
     * @return \Fab\Media\FileUpload\UploadedFileInterface
     */
    public function setInputName($inputName);

    /**
     * Set the upload folder
     *
     * @param string $uploadFolder
     * @return \Fab\Media\FileUpload\UploadedFileInterface
     */
    public function setUploadFolder($uploadFolder);

    /**
     * Set the file name to be saved
     *
     * @param string $name
     * @return \Fab\Media\FileUpload\UploadedFileInterface
     */
    public function setName($name);
}
