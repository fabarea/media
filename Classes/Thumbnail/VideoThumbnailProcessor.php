<?php
namespace Fab\Media\Thumbnail;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Utility\Path;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Video Thumbnail Processor
 */
class VideoThumbnailProcessor extends AbstractThumbnailProcessor
{

    /**
     * Render a thumbnail of a resource of type video.
     *
     * @return string
     */
    public function create()
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
     * @return string
     */
    public function renderUri()
    {

        $relativePath = sprintf('Icons/MimeType/%s.png', $this->getFile()->getProperty('extension'));
        $fileNameAndPath = GeneralUtility::getFileAbsFileName('EXT:media/Resources/Public/' . $relativePath);
        if (!file_exists($fileNameAndPath)) {
            $relativePath = 'Icons/UnknownMimeType.png';
        }

        $uri = Path::getRelativePath($relativePath);
        return $this->prefixUri($uri);
    }

    /**
     * Render the tag image which is the main one for a thumbnail.
     *
     * @param string $result
     * @return string
     */
    public function renderTagImage($result)
    {

        // Variable $result corresponds to an URL in this case.
        // Analyse the URL and compute the adequate separator between arguments.
        $parameterSeparator = strpos($result, '?') === false ? '?' : '&';

        return sprintf(
            '<img src="%s%s" title="%s" alt="%s" %s/>',
            $result,
            $this->thumbnailService->getAppendTimeStamp() ? $parameterSeparator . $this->getFile()->getProperty('tstamp') : '',
            $this->getTitle(),
            $this->getTitle(),
            $this->renderAttributes()
        );
    }

    /**
     * Compute and return the title of the file.
     *
     * @return string
     */
    protected function getTitle()
    {
        $result = $this->getFile()->getProperty('title');
        if (!$result) {
            $result = $this->getFile()->getName();
        }
        return htmlspecialchars($result);
    }

    /**
     * Render a wrapping anchor around the thumbnail.
     *
     * @param string $result
     * @return string
     */
    public function renderTagAnchor($result)
    {

        $file = $this->getFile();

        return sprintf(
            '<a href="%s%s" target="%s" data-uid="%s">%s</a>',
            $this->thumbnailService->getAnchorUri() ? $this->thumbnailService->getAnchorUri() : $file->getPublicUrl(true),
            $this->thumbnailService->getAppendTimeStamp() ? '?' . $file->getProperty('tstamp') : '',
            $this->thumbnailService->getTarget(),
            $file->getUid(),
            $result
        );
    }
}
