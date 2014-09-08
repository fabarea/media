<?php
namespace TYPO3\CMS\Media\Thumbnail;

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
 * Tell how a thumbnail of a file can be rendered.
 */
interface ThumbnailProcessorInterface {

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
