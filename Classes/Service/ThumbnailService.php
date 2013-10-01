<?php
namespace TYPO3\CMS\Media\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 */
class ThumbnailService implements \TYPO3\CMS\Media\Service\ThumbnailInterface {

	/**
	 * @var array
	 */
	protected $allowedOutputTypes = array(
		\TYPO3\CMS\Media\Service\ThumbnailInterface::OUTPUT_IMAGE,
		\TYPO3\CMS\Media\Service\ThumbnailInterface::OUTPUT_IMAGE_WRAPPED,
		\TYPO3\CMS\Media\Service\ThumbnailInterface::OUTPUT_URI,
	);

	/**
	 * Configure the output of the thumbnail service. Default output is:
	 * \TYPO3\CMS\Media\Service\ThumbnailInterface::OUTPUT_IMAGE
	 *
	 * @var string
	 */
	protected $outputType = self::OUTPUT_IMAGE;

	/**
	 * Define what are the rendering steps for a thumbnail.
	 *
	 * @var array
	 */
	protected $renderingSteps = array(
		\TYPO3\CMS\Media\Service\ThumbnailInterface::OUTPUT_URI => 'renderUri',
		\TYPO3\CMS\Media\Service\ThumbnailInterface::OUTPUT_IMAGE => 'renderTagImage',
		\TYPO3\CMS\Media\Service\ThumbnailInterface::OUTPUT_IMAGE_WRAPPED => 'renderTagAnchor',
	);

	/**
	 * Whether the thumbnail should be wrapped with an anchor.
	 *
	 * @var bool
	 * @deprecated will be removed in Media 1.2
	 */
	protected $wrap = FALSE;

	/**
	 * @var \TYPO3\CMS\Core\Resource\File|\TYPO3\CMS\Media\Domain\Model\Asset
	 */
	protected $file;

	/**
	 * @var \TYPO3\CMS\Core\Resource\File|\TYPO3\CMS\Media\Domain\Model\Asset
	 */
	protected $overlayFile;

	/**
	 * @var \TYPO3\CMS\Core\Resource\ProcessedFile
	 */
	protected $processedFile;

	/**
	 * Define width, height and all sort of attributes to render a thumbnail.
	 * @see TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::Image
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * Define width, height and all sort of attributes to render the anchor file
	 * which is wrapping the image
	 *
	 * @see TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::Image
	 * @var array
	 */
	protected $configurationWrap = array();

	/**
	 * DOM attributes to add to the image preview.
	 *
	 * @var array
	 */
	protected $attributes = array(
		'class' => 'thumbnail',
	);

	/**
	 * Define in which window will the thumbnail be opened.
	 * Does only apply if the thumbnail is wrapped (with an anchor).
	 *
	 * @var string
	 */
	protected $target = \TYPO3\CMS\Media\Service\ThumbnailInterface::TARGET_BLANK;

	/**
	 * Whether a time stamp is appended to the image.
	 * Appending the time stamp can prevent caching
	 *
	 * @var bool
	 */
	protected $appendTimeStamp = FALSE;

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
		$className = 'TYPO3\CMS\Media\Service\ThumbnailService\FallBackThumbnail';
		if (\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE == $this->file->getType()) {
			$className = 'TYPO3\CMS\Media\Service\ThumbnailService\ImageThumbnail';
		} elseif (\TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO == $this->file->getType()) {
			$className = 'TYPO3\CMS\Media\Service\ThumbnailService\AudioThumbnail';
		} elseif (\TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO == $this->file->getType()) {
			$className = 'TYPO3\CMS\Media\Service\ThumbnailService\VideoThumbnail';
		} elseif (\TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION == $this->file->getType() ||
			\TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT == $this->file->getType()) {
				$className = 'TYPO3\CMS\Media\Service\ThumbnailService\ApplicationThumbnail';
		}

		/** @var $serviceInstance \TYPO3\CMS\Media\Service\ThumbnailService */
		$serviceInstance = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($className);

		$thumbnail = '';
		if ($this->file->exists()) {
			$thumbnail = $serviceInstance->setFile($this->file)
				->setOverlayFile($this->overlayFile)
				->setConfiguration($this->getConfiguration())
				->setConfigurationWrap($this->getConfigurationWrap())
				->setAttributes($this->getAttributes())
				->setOutputType($this->getOutputType())
				->setAppendTimeStamp($this->getAppendTimeStamp())
				->setTarget($this->getTarget())
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
	 * @deprecated will be removed in Media 1.2
	 */
	public function isWrapped() {
		return $this->wrap;
	}

	/**
	 * Tell whether to wrap the thumbnail or not with an anchor. This will make the thumbnail clickable.
	 *
	 * @param bool $wrap
	 * @return \TYPO3\CMS\Media\Service\ThumbnailService
	 * @deprecated will be removed in Media 1.2
	 */
	public function doWrap($wrap = TRUE) {
		if ($wrap) {
			$this->wrap = $wrap;
			$this->outputType = self::OUTPUT_IMAGE_WRAPPED;
		}
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
	 * @return array
	 */
	public function getConfiguration() {
		if (empty($this->configuration)) {
			$dimension = \TYPO3\CMS\Media\Utility\ImagePresetUtility::getInstance()->preset('image_thumbnail');
			$this->configuration = array(
				'width' => $dimension->getWidth(),
				'height' => $dimension->getHeight(),
			);
		}
		return $this->configuration;
	}

	/**
	 * @return array
	 */
	public function getConfigurationWrap() {
		return $this->configurationWrap;
	}

	/**
	 * @param array $configurationWrap
	 * @return \TYPO3\CMS\Media\Service\ThumbnailService
	 */
	public function setConfigurationWrap($configurationWrap) {
		$this->configurationWrap = $configurationWrap;
		return $this;
	}

	/**
	 * Return what needs to be rendered
	 *
	 * @return array
	 */
	public function getRenderingSteps() {
		$position = array_search($this->getOutputType(), array_keys($this->renderingSteps));
		return array_slice($this->renderingSteps, 0, $position + 1);
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
	 * @return \TYPO3\CMS\Media\Service\ThumbnailService
	 */
	public function setFile($file) {

		if (!is_object($file)) {
			throw new \RuntimeException('Given parameter "file" should be an object', 1362999411);
		}

		$this->overlayFile = $file;

		// Retrieve a possible original file if the file is detected as overlay.
		// The original file is the source of truth when it comes about width / height, ...
		if ($file->getProperty('sys_language_uid') > 0) {

			/** @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository */
			$fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\FileRepository');
			$file = $fileRepository->findByUid($file->getProperty('l18n_parent'));
		}

		$this->file = $file;

		return $this;
	}

	/**
	 * @return \TYPO3\CMS\Core\Resource\File|\TYPO3\CMS\Media\Domain\Model\Asset
	 */
	public function getOverlayFile() {
		return $this->overlayFile;
	}

	/**
	 * @param \TYPO3\CMS\Core\Resource\File|\TYPO3\CMS\Media\Domain\Model\Asset $overlayFile
	 * @return \TYPO3\CMS\Media\Service\ThumbnailService
	 */
	public function setOverlayFile($overlayFile) {
		$this->overlayFile = $overlayFile;
		return $this;
	}

	/**
	 * @param array $configuration
	 * @return \TYPO3\CMS\Media\Service\ThumbnailService
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
	 * @return \TYPO3\CMS\Media\Service\ThumbnailService
	 */
	public function setAttributes($attributes) {
		$this->attributes = $attributes;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOutputType() {
		return $this->outputType;
	}

	/**
	 * @throws \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 * @param string $outputType
	 * @return \TYPO3\CMS\Media\Service\ThumbnailService
	 */
	public function setOutputType($outputType) {
		if (!in_array($outputType, $this->allowedOutputTypes)) {
			throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException(
				sprintf('Output type "%s" is not allowed', $outputType),
				1373020076
			);
		}
		$this->outputType = $outputType;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * @param string $target
	 * @return \TYPO3\CMS\Media\Service\ThumbnailService
	 */
	public function setTarget($target) {
		$this->target = $target;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getAppendTimeStamp() {
		return $this->appendTimeStamp;
	}

	/**
	 * @param boolean $appendTimeStamp
	 * @return \TYPO3\CMS\Media\Service\ThumbnailService
	 */
	public function setAppendTimeStamp($appendTimeStamp) {
		$this->appendTimeStamp = (bool) $appendTimeStamp;
		return $this;
	}
}
?>