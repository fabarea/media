<?php

namespace Fab\Media\Thumbnail;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
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
    protected array $allowedOutputTypes = [
        ThumbnailInterface::OUTPUT_IMAGE,
        ThumbnailInterface::OUTPUT_IMAGE_WRAPPED,
        ThumbnailInterface::OUTPUT_URI,
    ];

    /**
     * Configure the output of the thumbnail service whether it is wrapped or not.
     * Default output is: ThumbnailInterface::OUTPUT_IMAGE
     */
    protected string $outputType = ThumbnailInterface::OUTPUT_IMAGE;

    protected ?File $file = null;

    /**
     * Define width, height and all sort of attributes to render a thumbnail.
     * @see TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::Image
     * @var array
     */
    protected array $configuration = [];

    /**
     * Define width, height and all sort of attributes to render the anchor file
     * which is wrapping the image
     *
     * @see TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::Image
     * @var array
     */
    protected array $configurationWrap = [];

    /**
     * DOM attributes to add to the image preview.
     *
     * @var array
     */
    protected array $attributes = [
        'class' => 'thumbnail',
    ];

    /**
     * Define in which window will the thumbnail be opened.
     * Does only apply if the thumbnail is wrapped (with an anchor).
     */
    protected string $target = ThumbnailInterface::TARGET_BLANK;

    /**
     * URI of the wrapping anchor pointing to the file.
     * replacing the "?" <a href="?">...</a>
     * The URI is automatically computed if not set.
     */
    protected string $anchorUri = '';

    /**
     * Whether a time stamp is appended to the image.
     * Appending the time stamp can prevent caching
     */
    protected bool $appendTimeStamp = false;

    /**
     * Define the processing type for the thumbnail.
     * As instance for image the default is ProcessedFile::CONTEXT_IMAGECROPSCALEMASK.
     */
    protected string $processingType = '';

    public function __construct(File $file = null)
    {
        $this->file = $file;
    }

    /**
     * Render a thumbnail of a media
     */
    public function create(): string
    {
        if (!$this->file) {
            throw new MissingTcaConfigurationException('Missing File object. Forgotten to set a file?', 1355933144);
        }

        // Default class name
        $className = FallBackThumbnailProcessor::class;
        if (File::FILETYPE_IMAGE === $this->file->getType()) {
            $className = ImageThumbnailProcessor::class;
        } elseif (File::FILETYPE_AUDIO === $this->file->getType()) {
            $className = AudioThumbnailProcessor::class;
        } elseif (File::FILETYPE_VIDEO === $this->file->getType()) {
            $className = VideoThumbnailProcessor::class;
        } elseif (File::FILETYPE_APPLICATION === $this->file->getType() || File::FILETYPE_TEXT === $this->file->getType()) {
            $className = ApplicationThumbnailProcessor::class;
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
    public function getConfigurationWrap(): array
    {
        return $this->configurationWrap;
    }

    /**
     * @param array $configurationWrap
     * @return $this
     */
    public function setConfigurationWrap($configurationWrap): ThumbnailService
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
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param array|ThumbnailConfiguration $configuration
     * @return $this
     */
    public function setConfiguration($configuration): ThumbnailService
    {
        if ($configuration instanceof ThumbnailConfiguration) {
            $configurationObject = $configuration;
            $configuration = [];

            if ($configurationObject->getWidth() > 0) {
                $configuration['width'] = $configurationObject->getWidth();
            }

            if ($configurationObject->getHeight() > 0) {
                $configuration['height'] = $configurationObject->getHeight();
            }

            if ($configurationObject->getStyle()) {
                $this->attributes['style'] = $configurationObject->getStyle();
            }

            if ($configurationObject->getClassName()) {
                $this->attributes['class'] = $configurationObject->getClassName();
            }
        }

        $this->configuration = $configuration;
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): ThumbnailService
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getOutputType(): string
    {
        return $this->outputType;
    }

    public function setOutputType(string $outputType): ThumbnailService
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

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(string $target): ThumbnailService
    {
        $this->target = $target;
        return $this;
    }

    public function getAnchorUri(): string
    {
        return $this->anchorUri;
    }

    public function setAnchorUri(string $anchorUri): ThumbnailService
    {
        $this->anchorUri = $anchorUri;
        return $this;
    }

    public function getAppendTimeStamp(): bool
    {
        return $this->appendTimeStamp;
    }

    /**
     * @param boolean $appendTimeStamp
     */
    public function setAppendTimeStamp($appendTimeStamp): ThumbnailService
    {
        $this->appendTimeStamp = (bool)$appendTimeStamp;
        return $this;
    }

    public function getProcessingType(): string
    {
        return $this->processingType;
    }

    public function setProcessingType(string $processingType): ThumbnailService
    {
        $this->processingType = $processingType;
        return $this;
    }
}
