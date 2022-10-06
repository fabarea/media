<?php

namespace Fab\Media\Thumbnail;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use Fab\Vidi\Utility\BackendUtility;
use TYPO3\CMS\Core\Resource\ProcessedFile;

/**
 * Application Thumbnail Processor
 */
class ApplicationThumbnailProcessor extends AbstractThumbnailProcessor
{
    /**
     * Render a thumbnail of a resource of type application.
     *
     */
    public function create(): string
    {
        $steps = $this->getRenderingSteps();

        $result = '';
        while ($step = array_shift($steps)) {
            $result = $this->$step($result);
        }

        return $result;
    }

    /**
     * Render the URI of the thumbnail.
     *
     */
    public function renderUri(): string
    {
        if ($this->isThumbnailPossible($this->getFile()->getExtension())) {
            $this->processedFile = $this->getFile()->process($this->getProcessingType(), $this->getConfiguration());
            $uri = $this->processedFile->getPublicUrl(true);

            // Update time stamp of processed image at this stage. This is needed for the browser to get new version of the thumbnail.
            if ($this->processedFile->getProperty('originalfilesha1') !== $this->getFile()->getProperty('sha1')) {
                $this->processedFile->updateProperties(array('tstamp' => $this->getFile()->getProperty('tstamp')));
            }
        } else {
            $uri = $this->getIcon($this->getFile()->getExtension());
        }
        return $this->prefixUri($uri);
    }

    /**
     * Render the tag image which is the main one for a thumbnail.
     *
     * @param string $result
     */
    public function renderTagImage($result): string
    {
        // Variable $result corresponds to an URL in this case.
        // Analyse the URL and compute the adequate separator between arguments.
        $parameterSeparator = strpos($result, '?') === false ? '?' : '&';

        return sprintf(
            '<img src="%s%s" title="%s" alt="%s" %s/>',
            $result,
            $this->thumbnailService->getAppendTimeStamp() ? $parameterSeparator . $this->getTimeStamp() : '',
            $this->getTitle(),
            $this->getTitle(),
            $this->renderAttributes()
        );
    }

    /**
     * Compute and return the time stamp.
     *
     * @return int
     */
    protected function getTimeStamp()
    {
        $result = $this->getFile()->getProperty('tstamp');
        if ($this->processedFile) {
            $result = $this->processedFile->getProperty('tstamp');
        }
        return $result;
    }

    /**
     * Compute and return the title of the file.
     */
    protected function getTitle(): string
    {
        $result = $this->getFile()->getProperty('title');
        if (empty($result)) {
            $result = $this->getFile()->getName();
        }
        return htmlspecialchars($result);
    }

    /**
     * Render a wrapping anchor around the thumbnail.
     *
     * @param string $result
     */
    public function renderTagAnchor($result): string
    {
        $uri = $this->thumbnailService->getAnchorUri();
        if (!$uri) {
            $uri = $this->getUri();
        }

        return sprintf(
            '<a href="%s" target="_blank" data-uid="%s">%s</a>',
            $uri,
            $this->getFile()->getUid(),
            $result
        );
    }

    protected function getUri(): string
    {
        $urlParameters = [
            MediaModule::getParameterPrefix() => [
                'controller' => 'Asset',
                'action' => 'download',
                'file' => $this->getFile()->getUid(),
            ],
        ];
        return BackendUtility::getModuleUrl(MediaModule::getSignature(), $urlParameters);
    }

    public function getProcessingType(): string
    {
        if (!$this->thumbnailService->getProcessingType()) {
            return ProcessedFile::CONTEXT_IMAGECROPSCALEMASK;
        }
        return $this->thumbnailService->getProcessingType();
    }
}
