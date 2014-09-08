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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Utility\ImagePresetUtility;
use TYPO3\CMS\Media\Utility\Path;

/**
 * Application Thumbnail Processor
 */
abstract class AbstractThumbnailProcessor implements ThumbnailProcessorInterface, SingletonInterface {

	/**
	 * @var ThumbnailService
	 */
	protected $thumbnailService;

	/**
	 * Store a Processed File along the processing.
	 *
	 * @var \TYPO3\CMS\Core\Resource\ProcessedFile
	 */
	protected $processedFile;

	/**
	 * Define what are the rendering steps for a thumbnail.
	 *
	 * @var array
	 */
	protected $renderingSteps = array(
		ThumbnailInterface::OUTPUT_URI => 'renderUri',
		ThumbnailInterface::OUTPUT_IMAGE => 'renderTagImage',
		ThumbnailInterface::OUTPUT_IMAGE_WRAPPED => 'renderTagAnchor',
	);

	/**
	 * @param ThumbnailService $thumbnailService
	 * @return $this
	 */
	public function setThumbnailService(ThumbnailService $thumbnailService) {
		$this->thumbnailService = $thumbnailService;
		return $this;
	}

	/**
	 * Return what needs to be rendered
	 *
	 * @return array
	 */
	protected function getRenderingSteps() {
		$position = array_search($this->thumbnailService->getOutputType(), array_keys($this->renderingSteps));
		return array_slice($this->renderingSteps, 0, $position + 1);
	}


	/**
	 * Render additional attribute for this DOM element.
	 *
	 * @return string
	 */
	protected function renderAttributes() {
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
	protected function getConfiguration() {
		if (empty($this->configuration)) {
			$dimension = ImagePresetUtility::getInstance()->preset('image_thumbnail');
			$this->configuration = array(
				'width' => $dimension->getWidth(),
				'height' => $dimension->getHeight(),
			);
		}
		return $this->configuration;
	}

	/**
	 * Tell whether to wrap the thumbnail or not with an anchor. This will make the thumbnail clickable.
	 *
	 * @param bool $wrap
	 * @return $this
	 * @deprecated will be removed in Media 1.2
	 */
	protected function doWrap($wrap = TRUE) {
		if ($wrap) {
			$this->wrap = $wrap;
			$this->outputType = ThumbnailInterface::OUTPUT_IMAGE_WRAPPED;
		}
		return $this;
	}

	/**
	 * Returns a path to an icon given an extension.
	 *
	 * @param string $extension File extension
	 * @return string
	 */
	protected function getIcon($extension) {
		$resource = Path::getRelativePath(sprintf('Icons/MimeType/%s.png', $extension));

		// If file is not found, fall back to a default icon
		if (Path::notExists($resource)) {
			$resource = Path::getRelativePath('Icons/MissingMimeTypeIcon.png');
		}

		return $resource;
	}

	/**
	 * Returns TRUE whether an thumbnail can be generated
	 *
	 * @param string $extension File extension
	 * @return boolean
	 */
	protected function isThumbnailPossible($extension) {
		return GeneralUtility::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], strtolower($extension));
	}

	/**
	 * @return File
	 */
	protected function getFile() {
		return $this->thumbnailService->getFile();
	}
}
