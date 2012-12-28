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
class TextThumbnail extends \TYPO3\CMS\Media\Service\Thumbnail {

	/**
	 * Render a thumbnail of a media
	 *
	 * @return string
	 */
	public function create() {

		if ($this->isThumbnailPossible($this->file->getExtension())) {
			$processedFile = $this->file->process(\TYPO3\CMS\Core\Resource\ProcessedFile::CONTEXT_IMAGEPREVIEW, array());
			$icon = $processedFile->getPublicUrl(TRUE);
		} else {
			$icon = $this->getIcon($this->file->getExtension());
		}

		$thumbnail = sprintf('<img src="%s" hspace="2" title="%s" class="%s" alt="" />',
			$icon,
			htmlspecialchars($this->file->getName()),
			$this->isThumbnailPossible($this->file->getExtension()) ? 'thumbnail' : ''
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

//		// @todo implementation of secure download not ideal for now. Improve it! Make it compatible with the FE, too.
		$uri = '/typo3/mod.php?M=user_MediaTxMediaM1&tx_media_user_mediatxmediam1[media]=%s&tx_media_user_mediatxmediam1[action]=download&tx_media_user_mediatxmediam1[controller]=Media';
		$template = <<<EOF
<a href="$uri" target="_blank">%s</a>
<div class="metadata">%s K</div>
EOF;

		return sprintf($template,
			$this->file->getUid(),
			$thumbnail,
			round($this->file->getSize() / 1000)
		);
	}

}
?>