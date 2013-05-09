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
class Thumbnail implements \TYPO3\CMS\Media\Service\ThumbnailInterface {

	/**
	 * Whether the thumbnail should be wrapped with an anchor.
	 *
	 * @var bool
	 */
	protected $wrap = FALSE;

	/**
	 * @var \TYPO3\CMS\Core\Resource\File|\TYPO3\CMS\Media\Domain\Model\Asset
	 */
	protected $file = FALSE;

	/**
	 * Define width, height and all sort of attributes to render a thumbnail.
	 * @see TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::Image
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * DOM attributes to add to the image preview.
	 *
	 * @var array
	 */
	protected $attributes = array(
		'class' => 'thumbnail',
	);

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
		} elseif (\TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION == $this->file->getType() ||
			\TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT == $this->file->getType()) {
				$className = 'TYPO3\CMS\Media\Service\Thumbnail\ApplicationThumbnail';
		}

		/** @var $serviceInstance \TYPO3\CMS\Media\Service\ThumbnailInterface */
		$serviceInstance = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($className);

		$thumbnail = '';
		if ($this->file->exists()) {
			$thumbnail = $serviceInstance->setFile($this->file)
				->setConfiguration($this->getConfiguration())
				->setAttributes($this->getAttributes())
				->doWrap($this->wrap)
				->create();
		} else {
			$logger = \TYPO3\CMS\Media\Utility\Logger::getInstance($this);
			$logger->warning(sprintf('Resource not found for File uid "%s" at %s', $this->file->getUid(), $this->file->getIdentifier()));
		}

		return $thumbnail;
	}

	/**
	 * Returns a path to an icon given an extension.
	 *
	 * @param string $extension File extension
	 * @return string
	 */
	public function getIcon($extension) {
		$resource = \TYPO3\CMS\Media\Utility\Path::getRelativePath(sprintf('Icons/MimeType/%s.png', $extension));

		// If file is not found, fall back to a default icon
		if (\TYPO3\CMS\Media\Utility\Path::notExists($resource)) {
			$resource = \TYPO3\CMS\Media\Utility\Path::getRelativePath('Icons/MissingMimeTypeIcon.png');
		}

		return $resource;
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
	 * Render additional attribute for this DOM element.
	 *
	 * @return string
	 */
	public function renderAttributes() {
		$result = '';
		if (!empty($this->attributes)) {
			foreach ($this->attributes as $attribute => $value) {
				$result .= sprintf('%s="%s" ',
					htmlspecialchars($attribute),
					htmlspecialchars($value)
				);
			}
		}
		return $result;
	}

	/**
	 * @return mixed
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * @throws \RuntimeException
	 * @param object $file
	 * @return \TYPO3\CMS\Media\Service\Thumbnail
	 */
	public function setFile($file) {
		if (!is_object($file)) {
			throw new \RuntimeException('Given parameter "file" should be an object', 1362999411);
		}
		$this->file = $file;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getConfiguration() {
		if (empty($this->configuration)) {
			$dimension = \TYPO3\CMS\Media\Utility\SettingImagePreset::getInstance()->preset('image_thumbnail');
			$this->configuration = array(
				'width' => $dimension->getWidth(),
				'height' => $dimension->getHeight(),
			);
		}
		return $this->configuration;
	}

	/**
	 * @param array $configuration
	 * @return \TYPO3\CMS\Media\Service\Thumbnail
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * @param array $attributes
	 * @return \TYPO3\CMS\Media\Service\Thumbnail
	 */
	public function setAttributes($attributes) {
		$this->attributes = $attributes;
		return $this;
	}
}
?>