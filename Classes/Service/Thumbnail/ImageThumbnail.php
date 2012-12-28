<?php
namespace TYPO3\CMS\Media\Service\Thumbnail;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 * @package media
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class ImageThumbnail extends \TYPO3\CMS\Media\Service\Thumbnail {

	/**
	 * Render a thumbnail of a media
	 *
	 * @return string
	 */
	public function create() {

		$thumbnailSize = \TYPO3\CMS\Media\Utility\Configuration::get('thumbnail_size');

		// @todo maxW is not taken into account... why is the world so cruel?
		$configuration = array(
			'width' => $thumbnailSize, 'height' => $thumbnailSize,
			'maxW' => $thumbnailSize, 'maxH' => $thumbnailSize
		);
		/** @var $processedFile \TYPO3\CMS\Core\Resource\ProcessedFile */
		$processedFile = $this->file->process(\TYPO3\CMS\Core\Resource\ProcessedFile::CONTEXT_IMAGEPREVIEW, $configuration);

		$thumbnail = sprintf('<img src="%s?%s" hspace="2" title="%s" class="thumbnail" alt="" />',
			$processedFile->getPublicUrl(TRUE),
			$processedFile->isUpdated() ? time() : $processedFile->getProperty('tstamp'),
			htmlspecialchars($this->file->getName())
		);

		if ($this->isWrapped()) {
			$thumbnail = $this->wrap($thumbnail);
		}
		return $thumbnail;
	}

	/**
	 * Get Wrap template
	 *
	 * @param string $thumbnail
	 * @return string
	 */
	public function wrap($thumbnail) {
		$template = <<<EOF
<a href="%s?%s" target="_blank">%s</a>
<div class="metadata">%s x %s</div>
EOF;

		return sprintf($template,
			$this->file->getPublicUrl(TRUE),
			$this->file->getProperty('tstamp'),
			$thumbnail,
			$this->file->getProperty('width'),
			$this->file->getProperty('height')
		);
	}

}
?>