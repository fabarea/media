<?php

namespace Fab\Media\Thumbnail;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Media\Utility\ImagePresetUtility;
use Fab\Media\Utility\Path;

/**
 * Application Thumbnail Processor
 */
abstract class AbstractThumbnailProcessor implements ThumbnailProcessorInterface
{
    /**
     * @var ThumbnailService
     */
    protected $thumbnailService;

    /**
     * Store a Processed File along the processing.
     *
     * @var ProcessedFile
     */
    protected $processedFile;

    /**
     * Define what are the rendering steps for a thumbnail.
     */
    protected array $renderingSteps = [
        ThumbnailInterface::OUTPUT_URI => 'renderUri',
        ThumbnailInterface::OUTPUT_IMAGE => 'renderTagImage',
        ThumbnailInterface::OUTPUT_IMAGE_WRAPPED => 'renderTagAnchor',
    ];

    public function setThumbnailService(ThumbnailService $thumbnailService): AbstractThumbnailProcessor
    {
        $this->thumbnailService = $thumbnailService;
        return $this;
    }

    /**
     * Return what needs to be rendered
     */
    protected function getRenderingSteps(): array
    {
        $position = array_search($this->thumbnailService->getOutputType(), array_keys($this->renderingSteps));
        return array_slice($this->renderingSteps, 0, $position + 1);
    }


    /**
     * Render additional attribute for this DOM element.
     */
    protected function renderAttributes(): string
    {
        $result = '';
        $attributes = $this->thumbnailService->getAttributes();
        if (is_array($attributes)) {
            foreach ($attributes as $attribute => $value) {
                $result .= sprintf(
                    '%s="%s" ',
                    htmlspecialchars($attribute),
                    htmlspecialchars($value)
                );
            }
        }
        return $result;
    }

    protected function getConfiguration(): array
    {
        $configuration = $this->thumbnailService->getConfiguration();
        if (!$configuration) {
            $dimension = ImagePresetUtility::getInstance()->preset('image_thumbnail');
            $configuration = array(
                'width' => $dimension->getWidth(),
                'height' => $dimension->getHeight(),
            );
        }
        return $configuration;
    }

    /**
     * Returns a path to an icon given an extension.
     *
     * @param string $extension File extension
     */
    protected function getIcon($extension): string
    {
        $resource = Path::getRelativePath(sprintf('Icons/MimeType/%s.png', $extension));

        // If file is not found, fall back to a default icon
        if (Path::notExists($resource)) {
            $resource = Path::getRelativePath('Icons/MissingMimeTypeIcon.png');
        }

        return $resource;
    }

    /**
     * @param string $uri
     */
    public function prefixUri($uri): string
    {
        if ($this->isFrontendMode() && $this->getFrontendObject()->absRefPrefix) {
            $uri = $this->getFrontendObject()->absRefPrefix . $uri;
        }
        return $uri;
    }

    /**
     * Returns true whether an thumbnail can be generated
     *
     * @param string $extension File extension
     * @return boolean
     */
    protected function isThumbnailPossible($extension): bool
    {
        return GeneralUtility::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], strtolower($extension));
    }

    /**
     * @return File
     */
    protected function getFile(): File
    {
        return $this->thumbnailService->getFile();
    }

    /**
     * Returns whether the current mode is Frontend
     *
     * @return bool
     */
    protected function isFrontendMode(): bool
    {
        return !Environment::isCli() && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

    protected function getFrontendObject(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
