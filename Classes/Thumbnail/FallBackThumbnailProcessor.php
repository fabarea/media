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
use Fab\Media\Utility\Path;

/**
 * Fallback thumbnail processor.
 */
class FallBackThumbnailProcessor extends AbstractThumbnailProcessor {

	/**
	 * Render a fallback thumbnail if no type was found for the given resource.
	 *
	 * @return string
	 */
	public function create() {
		return sprintf('<img src="%s" hspace="2" class="" alt="" />',
			Path::getRelativePath('Icons/UnknownMimeType.png')
		);
	}

	/**
	 * Render the URI of the thumbnail.
	 *
	 * @return string
	 */
	public function renderUri() {
		// Nothing to implement.
	}

	/**
	 * Render the tag image which is the main one for a thumbnail.
	 *
	 * @param string $result
	 * @return string
	 */
	public function renderTagImage($result) {
		// Nothing to implement.
	}

	/**
	 * Render a wrapping anchor around the thumbnail.
	 *
	 * @param string $result
	 * @return string
	 */
	public function renderTagAnchor($result) {
		// Nothing to implement.
	}
}
