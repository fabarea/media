<?php
namespace TYPO3\CMS\Media\Service\ThumbnailService;

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
class ApplicationThumbnail extends \TYPO3\CMS\Media\Service\ThumbnailService
	implements \TYPO3\CMS\Media\Service\ThumbnailRenderableInterface {

	/**
	 * Render a thumbnail of a media
	 *
	 * @return string
	 */
	public function create() {

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
	public function renderUri() {
		if ($this->isThumbnailPossible($this->file->getExtension())) {
			$this->processedFile = $this->file->process(\TYPO3\CMS\Core\Resource\ProcessedFile::CONTEXT_IMAGEPREVIEW, $this->getConfiguration());
			$result = $this->processedFile->getPublicUrl(TRUE);
		} else {
			$result = $this->getIcon($this->file->getExtension());
		}
		return $result;
	}

	/**
	 * Render the tag image which is the main one for a thumbnail.
	 *
	 * @param string $result
	 * @return string
	 */
	public function renderTagImage($result) {
		$imageTitle = $this->overlayFile->getProperty('title');
		if (empty($imageTitle)) {
			$imageTitle = $this->overlayFile->getName();
		}
		return sprintf('<img src="%s%s" title="%s" alt="%s" %s/>',
			$result,
			$this->getAppendTimeStamp() ? '?' . $this->processedFile->getProperty('tstamp') : '',
			htmlspecialchars($imageTitle),
			htmlspecialchars($imageTitle),
			$this->renderAttributes()
		);
	}

	/**
	 * Render a wrapping anchor around the thumbnail.
	 *
	 * @param string $result
	 * @return string
	 */
	public function renderTagAnchor($result) {
		// @todo implementation of secure download not ideal for now. Improve it!
		// @todo improve me! Make it compatible with the FE.
			$uri = 'mod.php?M=user_MediaM1&tx_media_user_mediam1[asset]=%s&tx_media_user_mediam1[action]=download&tx_media_user_mediam1[controller]=Asset';
		$template = <<<EOF
<a href="$uri" target="_blank">%s</a>
EOF;
		return sprintf($template,
			$this->file->getUid(),
			$result
		);
	}
}
?>