<?php
namespace Fab\Media\Thumbnail;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Thumbnail Interface
 * @todo refactor me to be an enumeration.
 */
interface ThumbnailInterface
{

    /**
     * The thumbnail of the asset (default value).
     */
    const OUTPUT_IMAGE = 'image';

    /**
     * The thumbnail will be wrapped with an anchor.
     */
    const OUTPUT_IMAGE_WRAPPED = 'imageWrapped';

    /**
     * Output the URI of the thumbnail.
     */
    const OUTPUT_URI = 'uri';

    /**
     * Open thumbnail in a new window.
     */
    const TARGET_BLANK = '_blank';

    /**
     * Open thumbnail in the same window.
     */
    const TARGET_SELF = '_self';

}
