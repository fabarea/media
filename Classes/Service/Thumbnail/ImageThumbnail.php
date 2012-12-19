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

		$thumbnailSize = \TYPO3\CMS\Media\Utility\GeneralSettings::get('thumbnail_size');
		$processedFile = $this->media->process(\TYPO3\CMS\Core\Resource\ProcessedFile::CONTEXT_IMAGEPREVIEW, array('width' => $thumbnailSize, 'height' => $thumbnailSize));

		$thumbnail = sprintf('<img src="%s" hspace="2" title="%s" class="thumbnail" alt="" />',
			$processedFile->getPublicUrl(TRUE),
			htmlspecialchars($this->media->getName())
		);

		if ($this->isWrapped()) {
			$thumbnail = $this->wrap($thumbnail);
		}
		return $thumbnail;
	}

	/**
	 * Get Wrap template
	 */
	public function wrap($thumbnail) {
		$template = <<<EOF
<a href="%s" target="_blank">%s</a>
<div class="metadata">%s x %s</div>
EOF;

		return sprintf($template,
			$this->media->getPublicUrl(TRUE),
			$thumbnail,
			$this->media->getWidth(),
			$this->media->getHeight()
		);
	}

}
?>