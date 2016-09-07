<?php
namespace Fab\Media\FileUpload;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Media\Exception\FailedFileUploadException;
use Fab\Media\Utility\PermissionUtility;

/**
 * Class that encapsulates the file-upload internals
 *
 * @see original implementation: https://github.com/valums/file-uploader/blob/master/server/php.php
 */
class UploadManager
{

    const UPLOAD_FOLDER = 'typo3temp/pics';

    /**
     * @var int|null|string
     */
    protected $sizeLimit;

    /**
     * @var string
     */
    protected $uploadFolder;

    /**
     * @var FormUtility
     */
    protected $formUtility;

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage
     */
    protected $storage;

    /**
     * Name of the file input in the DOM.
     *
     * @var string
     */
    protected $inputName = 'qqfile';

    /**
     * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
     * @return UploadManager
     */
    function __construct($storage = null)
    {

        $this->initializeUploadFolder();

        // max file size in bytes
        $this->sizeLimit = GeneralUtility::getMaxUploadFileSize() * 1024;
        $this->checkServerSettings();

        $this->formUtility = FormUtility::getInstance();
        $this->storage = $storage;
    }

    /**
     * Handle the uploaded file.
     *
     * @return UploadedFileInterface
     */
    public function handleUpload()
    {

        /** @var $uploadedFile UploadedFileInterface */
        $uploadedFile = false;
        if ($this->formUtility->isMultiparted()) {

            // Default case
            $uploadedFile = GeneralUtility::makeInstance('Fab\Media\FileUpload\MultipartedFile');
        } elseif ($this->formUtility->isOctetStreamed()) {

            // Fine Upload plugin would use it if forceEncoded = false and paramsInBody = false
            $uploadedFile = GeneralUtility::makeInstance('Fab\Media\FileUpload\StreamedFile');
        } elseif ($this->formUtility->isUrlEncoded()) {

            // Used for image resizing in BE
            $uploadedFile = GeneralUtility::makeInstance('Fab\Media\FileUpload\Base64File');
        }

        if (!$uploadedFile) {
            $this->throwException('Could not instantiate an upload object... No file was uploaded?');
        }

        $fileName = $this->getFileName($uploadedFile);

        $this->checkFileSize($uploadedFile->getSize());
        $this->checkFileAllowed($fileName);

        $saved = $uploadedFile->setInputName($this->inputName)
            ->setUploadFolder($this->uploadFolder)
            ->setName($fileName)
            ->save();

        if (!$saved) {
            $this->throwException('Could not save uploaded file. The upload was cancelled, or server error encountered');
        }

        // Optimize file if the uploaded file is an image.
        if ($uploadedFile->getType() == \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE) {
            $uploadedFile = ImageOptimizer::getInstance($this->storage)->optimize($uploadedFile);
        }
        return $uploadedFile;
    }

    /**
     * Internal function that checks if server's may sizes match the
     * object's maximum size for uploads.
     *
     * @return void
     */
    protected function checkServerSettings()
    {
        $postSize = $this->toBytes(ini_get('post_max_size'));

        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            $this->throwException('increase post_max_size and upload_max_filesize to ' . $size);
        }
    }

    /**
     * Convert a given size with units to bytes.
     *
     * @param string $str
     * @return int|string
     */
    protected function toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    /**
     * Return a file name given an uploaded file
     *
     * @param UploadedFileInterface $uploadedFile
     * @return string
     */
    public function getFileName(UploadedFileInterface $uploadedFile)
    {
        $pathInfo = pathinfo($uploadedFile->getOriginalName());
        $fileName = $this->sanitizeFileName($pathInfo['filename']);
        $fileNameWithExtension = $fileName;
        if (!empty($pathInfo['extension'])) {
            $fileNameWithExtension = sprintf('%s.%s', $fileName, $pathInfo['extension']);
        }
        return $fileNameWithExtension;
    }

    /**
     * Check whether the file size does not exceed the allowed limit
     *
     * @param int $size
     */
    public function checkFileSize($size)
    {
        if ($size == 0) {
            $this->throwException('File is empty');
        }

        if ($size > $this->sizeLimit) {
            $this->throwException('File is too large');
        }
    }

    /**
     * Check whether the file is allowed
     *
     * @param string $fileName
     */
    public function checkFileAllowed($fileName)
    {
        $isAllowed = $this->checkFileExtensionPermission($fileName);
        if (!$isAllowed) {
            $these = PermissionUtility::getInstance()->getAllowedExtensionList();
            $this->throwException('File has an invalid extension, it should be one of ' . $these . '.');
        }
    }

    /**
     * If the fileName is given, check it against the
     * TYPO3_CONF_VARS[BE][fileDenyPattern] + and if the file extension is allowed
     *
     * @see \TYPO3\CMS\Core\Resource\ResourceStorage->checkFileExtensionPermission($fileName);
     * @param string $fileName Full filename
     * @return boolean true if extension/filename is allowed
     */
    public function checkFileExtensionPermission($fileName)
    {
        $isAllowed = GeneralUtility::verifyFilenameAgainstDenyPattern($fileName);
        if ($isAllowed) {
            $fileInfo = GeneralUtility::split_fileref($fileName);
            // Set up the permissions for the file extension
            $fileExtensionPermissions = $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']['webspace'];
            $fileExtensionPermissions['allow'] = GeneralUtility::uniqueList(strtolower($fileExtensionPermissions['allow']));
            $fileExtensionPermissions['deny'] = GeneralUtility::uniqueList(strtolower($fileExtensionPermissions['deny']));
            $fileExtension = strtolower($fileInfo['fileext']);
            if ($fileExtension !== '') {
                // If the extension is found amongst the allowed types, we return true immediately
                if ($fileExtensionPermissions['allow'] === '*' || GeneralUtility::inList($fileExtensionPermissions['allow'], $fileExtension)) {
                    return true;
                }
                // If the extension is found amongst the denied types, we return false immediately
                if ($fileExtensionPermissions['deny'] === '*' || GeneralUtility::inList($fileExtensionPermissions['deny'], $fileExtension)) {
                    return false;
                }
                // If no match we return true
                return true;
            } else {
                if ($fileExtensionPermissions['allow'] === '*') {
                    return true;
                }
                if ($fileExtensionPermissions['deny'] === '*') {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Sanitize the file name for the web.
     * It has been noticed issues when letting done this work by FAL. Give it a little hand.
     *
     * @see https://github.com/alixaxel/phunction/blob/master/phunction/Text.php#L252
     * @param string $fileName
     * @param string $slug
     * @param string $extra
     * @return string
     */
    public function sanitizeFileName($fileName, $slug = '-', $extra = null)
    {
        return trim(preg_replace('~[^0-9a-z_' . preg_quote($extra, '~') . ']+~i', $slug, $this->unAccent($fileName)), $slug);
    }

    /**
     * Remove accent from a string
     *
     * @see https://github.com/alixaxel/phunction/blob/master/phunction/Text.php#L297
     * @param $string
     * @return string
     */
    protected function unAccent($string)
    {
        $searches = array('ç', 'æ', 'œ', 'á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'ä', 'ë', 'ï', 'ö', 'ü', 'ÿ', 'â', 'ê', 'î', 'ô', 'û', 'å', 'e', 'i', 'ø', 'u');
        $replaces = array('c', 'ae', 'oe', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'y', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u');
        $sanitizedString = str_replace($searches, $replaces, $string);

        if (extension_loaded('intl') === true) {
            $sanitizedString = \Normalizer::normalize($sanitizedString, \Normalizer::FORM_KD);
        }
        return $sanitizedString;
    }

    /**
     * @throws FailedFileUploadException
     * @param string $message
     */
    protected function throwException($message)
    {
        throw new FailedFileUploadException($message, 1357510420);
    }

    /**
     * Initialize Upload Folder.
     *
     * @return void
     */
    protected function initializeUploadFolder()
    {
        $this->uploadFolder = PATH_site . self::UPLOAD_FOLDER;

        // Initialize the upload folder for file transfer and create it if not yet existing
        if (!file_exists($this->uploadFolder)) {
            GeneralUtility::mkdir($this->uploadFolder);
        }

        // Check whether the upload folder is writable
        if (!is_writable($this->uploadFolder)) {
            $this->throwException("Server error. Upload directory isn't writable.");
        }
    }

    /**
     * @return int|null|string
     */
    public function getSizeLimit()
    {
        return $this->sizeLimit;
    }

    /**
     * @param int|null|string $sizeLimit
     * @return $this
     */
    public function setSizeLimit($sizeLimit)
    {
        $this->sizeLimit = $sizeLimit;
        return $this;
    }

    /**
     * @return string
     */
    public function getUploadFolder()
    {
        return $this->uploadFolder;
    }

    /**
     * @param string $uploadFolder
     * @return $this
     */
    public function setUploadFolder($uploadFolder)
    {
        $this->uploadFolder = $uploadFolder;
        return $this;
    }

    /**
     * @return string
     */
    public function getInputName()
    {
        return $this->inputName;
    }

    /**
     * @param string $inputName
     * @return $this
     */
    public function setInputName($inputName)
    {
        $this->inputName = $inputName;
        return $this;
    }

    /**
     * @return \TYPO3\CMS\Core\Resource\ResourceStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
     * @return $this
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        return $this;
    }

}
