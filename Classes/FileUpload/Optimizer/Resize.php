<?php
namespace Fab\Media\FileUpload\Optimizer;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Media\FileUpload\ImageOptimizerInterface;

/**
 * Class that optimize an image according to some settings.
 */
class Resize implements ImageOptimizerInterface
{

    /**
     * @var \TYPO3\CMS\Frontend\Imaging\GifBuilder
     */
    protected $gifCreator;

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage
     */
    protected $storage;

    /**
     * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
     * @return Resize
     */
    public function __construct($storage = null)
    {
        $this->storage = $storage;
        $this->gifCreator = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Imaging\\GifBuilder');
        $this->gifCreator->init();
        $this->gifCreator->absPrefix = PATH_site;
    }

    /**
     * Optimize the given uploaded image.
     *
     * @param \Fab\Media\FileUpload\UploadedFileInterface $uploadedFile
     * @return \Fab\Media\FileUpload\UploadedFileInterface
     */
    public function optimize($uploadedFile)
    {

        $imageInfo = getimagesize($uploadedFile->getFileWithAbsolutePath());

        $currentWidth = $imageInfo[0];
        $currentHeight = $imageInfo[1];

        // resize an image if this one is bigger than telling by the settings.
        if (is_object($this->storage)) {
            $storageRecord = $this->storage->getStorageRecord();
        } else {
            // Will only work in the BE for now.
            $storage = $this->getMediaModule()->getCurrentStorage();
            $storageRecord = $storage->getStorageRecord();
        }

        if (strlen($storageRecord['maximum_dimension_original_image']) > 0) {

            /** @var \Fab\Media\Dimension $imageDimension */
            $imageDimension = GeneralUtility::makeInstance('Fab\Media\Dimension', $storageRecord['maximum_dimension_original_image']);
            if ($currentWidth > $imageDimension->getWidth() || $currentHeight > $imageDimension->getHeight()) {

                // resize taking the width as reference
                $this->resize($uploadedFile->getFileWithAbsolutePath(), $imageDimension->getWidth(), $imageDimension->getHeight());
            }
        }
        return $uploadedFile;
    }

    /**
     * Resize an image according to given parameter.
     *
     * @throws \Exception
     * @param string $fileNameAndPath
     * @param int $width
     * @param int $height
     * @return void
     */
    public function resize($fileNameAndPath, $width = 0, $height = 0)
    {

        // Skip profile of the image
        $imParams = '###SkipStripProfile###';
        $options = array(
            'maxW' => $width,
            'maxH' => $height,
        );

        $tempFileInfo = $this->gifCreator->imageMagickConvert($fileNameAndPath, '', '', '', $imParams, '', $options, true);
        if ($tempFileInfo) {

            // Overwrite original file
            @unlink($fileNameAndPath);
            @rename($tempFileInfo[3], $fileNameAndPath);
        }
    }

    /**
     * Escapes a file name so it can safely be used on the command line.
     *
     * @see \TYPO3\CMS\Core\Imaging\GraphicalFunctions
     * @param string $inputName filename to safeguard, must not be empty
     * @return string $inputName escaped as needed
     */
    protected function wrapFileName($inputName)
    {
        if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem']) {
            $currentLocale = setlocale(LC_CTYPE, 0);
            setlocale(LC_CTYPE, $GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLocale']);
        }
        $escapedInputName = escapeshellarg($inputName);
        if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem']) {
            setlocale(LC_CTYPE, $currentLocale);
        }
        return $escapedInputName;
    }

    /**
     * @return MediaModule
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
    }

}
