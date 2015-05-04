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

/**
 * Thumbnail Interface
 * @todo refactor me to be an enumeration.
 */
interface ThumbnailInterface {

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
