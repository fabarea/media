<?php
namespace TYPO3\CMS\Media\Service;

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
class Thumbnail implements \TYPO3\CMS\Media\Service\Thumbnail\ThumbnailInterface {

	/**
	 * Whether the thumbnail should be wrapped with an anchor
	 *
	 * @var bool
	 */
	protected $wrap = FALSE;

	/**
	 * @var mixed
	 */
	protected $file = FALSE;

	/**
	 * Render a thumbnail of a media
	 *
	 * @throws \TYPO3\CMS\Media\Exception\MissingTcaConfigurationException
	 * @return string
	 */
	public function create() {

		if (empty($this->file)) {
			throw new \TYPO3\CMS\Media\Exception\MissingTcaConfigurationException('Missing Media object. Forgotten to set a media?', 1355933144);
		}

		// Default class name
		$className = 'TYPO3\CMS\Media\Service\Thumbnail\FallBackThumbnail';
		if (\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE == $this->file->getType()) {
			$className = 'TYPO3\CMS\Media\Service\Thumbnail\ImageThumbnail';
		} elseif (\TYPO3\CMS\Core\Resource\File::FILETYPE_SOFTWARE == $this->file->getType() ||
			\TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT == $this->file->getType()) {
				$className = 'TYPO3\CMS\Media\Service\Thumbnail\TextThumbnail';
		}

		/** @var $instance \TYPO3\CMS\Media\Service\Thumbnail\ThumbnailInterface */
		$instance = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($className);
		return $instance->setFile($this->file)->doWrap()->create();
	}

	/**
	 * Returns a path to an icon given an extension.
	 *
	 * @param string $extension File extension
	 * @return string
	 */
	public function getIcon($extension) {

		$uri = \TYPO3\CMS\Media\Utility\PublicResource::getPublicPath(sprintf('Icons/MimeType/%s.png', $extension));

		// If file is not found, fall back to a default icon
		if (!is_file(PATH_site . $uri)) {
			$uri = \TYPO3\CMS\Media\Utility\PublicResource::getPublicPath('Icons/MissingMimeTypeIcon.png');
		}
		return '/' . $uri;
	}

	/**
	 * Returns TRUE whether an thumbnail can be generated
	 *
	 * @param string $extension File extension
	 * @return boolean
	 */
	public function isThumbnailPossible($extension) {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], strtolower($extension));
	}

	/**
	 * @return boolean
	 */
	public function isWrapped() {
		return $this->wrap;
	}

	/**
	 * Tell whether to wrap the thumbnail or not with an anchor. This will make the thumbnail clickable.
	 *
	 * @param bool $wrap
	 * @return \TYPO3\CMS\Media\Service\Thumbnail
	 */
	public function doWrap($wrap = TRUE) {
		$this->wrap = $wrap;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * @param mixed $file
	 * @return \TYPO3\CMS\Media\Service\Thumbnail
	 */
	public function setFile($file) {
		$this->file = $file;
		return $this;
	}
}
?>