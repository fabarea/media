<?php

namespace Fab\Media\Thumbnail;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use Fab\Media\Utility\Path;

/**
 * Fallback thumbnail processor.
 */
class FallBackThumbnailProcessor extends AbstractThumbnailProcessor
{
    /**
     * Render a fallback thumbnail if no type was found for the given resource.
     */
    public function create(): string
    {
        return sprintf(
            '<img src="%s" hspace="2" class="" alt="" />',
            Path::getRelativePath('Icons/UnknownMimeType.png')
        );
    }

    /**
     * Render the URI of the thumbnail.
     */
    public function renderUri(): string
    {
        return '';
    }

    /**
     * Render the tag image which is the main one for a thumbnail.
     *
     */
    public function renderTagImage($result): string
    {
        return '';
    }

    /**
     * Render a wrapping anchor around the thumbnail.
     *
     */
    public function renderTagAnchor($result): string
    {
        return '';
    }
}
