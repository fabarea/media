<?php
namespace Fab\Media\Thumbnail;

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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Media\Exception\InvalidKeyInArrayException;
use Fab\Media\Exception\MissingTcaConfigurationException;
use Fab\Media\Utility\Logger;

/**
 * Thumbnail Service
 */
class ThumbnailService
{

    /**
     * @var array
     */
    protected $allowedOutputTypes = array(
        ThumbnailInterface::OUTPUT_IMAGE,
        ThumbnailInterface::OUTPUT_IMAGE_WRAPPED,
        ThumbnailInterface::OUTPUT_URI,
    );

    /**
     * Configure the output of the thumbnail service whether it is wrapped or not.
     * Default output is: ThumbnailInterface::OUTPUT_IMAGE
     *
     * @var string
     */
    protected $outputType = ThumbnailInterface::OUTPUT_IMAGE;

    /**
     * @var File
     */
    protected $file;

    /**
     * Define width, height and all sort of attributes to render a thumbnail.
     * @see TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::Image
     * @var array
     */
    protected $configuration = array();

    /**
     * Define width, height and all sort of attributes to render the anchor file
     * which is wrapping the image
     *
     * @see TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::Image
     * @var array
     */
    protected $configurationWrap = array();

    /**
     * DOM attributes to add to the image preview.
     *
     * @var array
     */
    protected $attributes = array(
        'class' => 'thumbnail',
    );

    /**
     * Define in which window will the thumbnail be opened.
     * Does only apply if the thumbnail is wrapped (with an anchor).
     *
     * @var string
     */
    protected $target = ThumbnailInterface::TARGET_BLANK;

    /**
     * URI of the wrapping anchor pointing to the file.
     * replacing the "?" <a href="?">...</a>
     * The URI is automatically computed if not set.
     * @var string
     */
    protected $anchorUri;

    /**
     * Whether a time stamp is appended to the image.
     * Appending the time stamp can prevent caching
     *
     * @var bool
     */
    protected $appendTimeStamp = FALSE;

    /**
     * Define the processing type for the thumbnail.
     * As instance for image the default is ProcessedFile::CONTEXT_IMAGECROPSCALEMASK.
     *
     * @var string
     */
    protected $processingType;

    /**
     * Constructor
     *
     * @param File $file
     */
    public function __construct(File $file = NULL)
    {
        $this->file = $file;
    }

    /**
     * Render a thumbnail of a media
     *
     * @throws MissingTcaConfigurationException
     * @return string
     */
    public function create()
    {

        if (empty($this->file)) {
            throw new MissingTcaConfigurationException('Missing File object. Forgotten to set a file?', 1355933144);
        }

        // Default class name
        $className = 'Fab\Media\Thumbnail\FallBackThumbnailProcessor';
        if (File::FILETYPE_IMAGE == $this->file->getType()) {
            $className = 'Fab\Media\Thumbnail\ImageThumbnailProcessor';
        } elseif (File::FILETYPE_AUDIO == $this->file->getType()) {
            $className = 'Fab\Media\Thumbnail\AudioThumbnailProcessor';
        } elseif (File::FILETYPE_VIDEO == $this->file->getType()) {
            $className = 'Fab\Media\Thumbnail\VideoThumbnailProcessor';
        } elseif (File::FILETYPE_APPLICATION == $this->file->getType() || File::FILETYPE_TEXT == $this->file->getType()) {
            $className = 'Fab\Media\Thumbnail\ApplicationThumbnailProcessor';
        }

        /** @var $processorInstance \Fab\Media\Thumbnail\ThumbnailProcessorInterface */
        $processorInstance = GeneralUtility::makeInstance($className);

        $thumbnail = '';
        if ($this->file->exists()) {
            $thumbnail = $processorInstance->setThumbnailService($this)->create();
        } else {
            $logger = Logger::getInstance($this);
            $logger->warning(sprintf('Resource not found for File uid "%s" at %s', $this->file->getUid(), $this->file->getIdentifier()));
        }

        return $thumbnail;
    }

    /**
     * @return array
     */
    public function getConfigurationWrap()
    {
        return $this->configurationWrap;
    }

    /**
     * @param array $configurationWrap
     * @return $this
     */
    public function setConfigurationWrap($configurationWrap)
    {
        $this->configurationWrap = $configurationWrap;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     * @return $this
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return string
     */
    public function getOutputType()
    {
        return $this->outputType;
    }

    /**
     * @throws InvalidKeyInArrayException
     * @param string $outputType
     * @return $this
     */
    public function setOutputType($outputType)
    {
        if (!in_array($outputType, $this->allowedOutputTypes)) {
            throw new InvalidKeyInArrayException(
                sprintf('Output type "%s" is not allowed', $outputType),
                1373020076
            );
        }
        $this->outputType = $outputType;
        return $this;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return string
     */
    public function getAnchorUri()
    {
        return $this->anchorUri;
    }

    /**
     * @param string $anchorUri
     * @return $this
     */
    public function setAnchorUri($anchorUri)
    {
        $this->anchorUri = $anchorUri;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAppendTimeStamp()
    {
        return $this->appendTimeStamp;
    }

    /**
     * @param boolean $appendTimeStamp
     * @return $this
     */
    public function setAppendTimeStamp($appendTimeStamp)
    {
        $this->appendTimeStamp = (bool)$appendTimeStamp;
        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingType()
    {
        $this->processingType;
    }

    /**
     * @param string $processingType
     * @return $this
     */
    public function setProcessingType($processingType)
    {
        $this->processingType = $processingType;
        return $this;
    }

}
