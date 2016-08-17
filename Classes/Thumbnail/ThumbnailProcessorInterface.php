<?php
namespace Fab\Media\Thumbnail;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Tell how a thumbnail of a file can be rendered.
 */
interface ThumbnailProcessorInterface
{

    /**
     * @param ThumbnailService $thumbnailService
     * @return $this
     */
    public function setThumbnailService(ThumbnailService $thumbnailService);

    /**
     * Render a thumbnail.
     *
     * @return string
     */
    public function create();

    /**
     * Render the URI of the thumbnail.
     *
     * @return string
     */
    public function renderUri();

    /**
     * Render the tag image which is the main one for a thumbnail.
     *
     * @param string $result
     * @return string
     */
    public function renderTagImage($result);

    /**
     * Render a wrapping anchor around the thumbnail.
     *
     * @param string $result
     * @return string
     */
    public function renderTagAnchor($result);
}
