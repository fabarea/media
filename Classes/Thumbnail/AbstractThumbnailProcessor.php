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
     * @var \TYPO3\CMS\Core\Resource\ProcessedFile
     */
    protected $processedFile;

    /**
     * Define what are the rendering steps for a thumbnail.
     *
     * @var array
     */
    protected $renderingSteps = [
        ThumbnailInterface::OUTPUT_URI => 'renderUri',
        ThumbnailInterface::OUTPUT_IMAGE => 'renderTagImage',
        ThumbnailInterface::OUTPUT_IMAGE_WRAPPED => 'renderTagAnchor',
    ];

    /**
     * @param ThumbnailService $thumbnailService
     * @return $this
     */
    public function setThumbnailService(ThumbnailService $thumbnailService)
    {
        $this->thumbnailService = $thumbnailService;
        return $this;
    }

    /**
     * Return what needs to be rendered
     *
     * @return array
     */
    protected function getRenderingSteps()
    {
        $position = array_search($this->thumbnailService->getOutputType(), array_keys($this->renderingSteps));
        return array_slice($this->renderingSteps, 0, $position + 1);
    }


    /**
     * Render additional attribute for this DOM element.
     *
     * @return string
     */
    protected function renderAttributes()
    {
        $result = '';
        $attributes = $this->thumbnailService->getAttributes();
        if (is_array($attributes)) {
            foreach ($attributes as $attribute => $value) {
                $result .= sprintf('%s="%s" ',
                    htmlspecialchars($attribute),
                    htmlspecialchars($value)
                );
            }
        }
        return $result;
    }

    /**
     * @return array
     * @throws \Fab\Media\Exception\InvalidKeyInArrayException
     * @throws \Fab\Media\Exception\EmptyValueException
     */
    protected function getConfiguration()
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
     * @return string
     */
    protected function getIcon($extension)
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
     * @return string
     */
    public function prefixUri($uri)
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
    protected function isThumbnailPossible($extension)
    {
        return GeneralUtility::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], strtolower($extension));
    }

    /**
     * @return File
     */
    protected function getFile()
    {
        return $this->thumbnailService->getFile();
    }

    /**
     * Returns whether the current mode is Frontend
     *
     * @return bool
     */
    protected function isFrontendMode()
    {
        return TYPO3_MODE === 'FE';
    }

    /**
     * Returns an instance of the Frontend object.
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getFrontendObject()
    {
        return $GLOBALS['TSFE'];
    }
}
