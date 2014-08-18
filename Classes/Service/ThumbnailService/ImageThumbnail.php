<?php
namespace TYPO3\CMS\Media\Service\ThumbnailService;

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

use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Media\Service\ThumbnailRenderableInterface;
use TYPO3\CMS\Media\Service\ThumbnailService;

/**
 */
class ImageThumbnail extends ThumbnailService implements ThumbnailRenderableInterface {

	/**
	 * @var array
	 */
	protected $defaultConfigurationWrap = array(
		'width' => 0,
		'height' => 0,
	);

	/**
	 * Render a thumbnail of a resource of type image.
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

		// Makes sure the width and the height of the thumbnail is not bigger than the actual file
		$configuration = $this->getConfiguration();
		if (!empty($configuration['width']) && $configuration['width'] > $this->file->getProperty('width')) {
			$configuration['width'] = $this->file->getProperty('width');
		}
		if (!empty($configuration['height']) && $configuration['height'] > $this->file->getProperty('height')) {
			$configuration['height'] = $this->file->getProperty('height');
		}

		$configuration = $this->computeFinalImageDimension($configuration);
		$this->processedFile = $this->file->process($this->getProcessingType(), $configuration);
		$result = $this->processedFile->getPublicUrl(TRUE);

		// Update time stamp of processed image at this stage. This is needed for the browser to get new version of the thumbnail.
		if ($this->processedFile->getProperty('originalfilesha1') != $this->file->getProperty('sha1')) {
			$this->processedFile->updateProperties(array('tstamp' => $this->file->getProperty('tstamp')));
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
		return sprintf('<img src="%s%s" title="%s" alt="%s" %s/>',
			$result,
			$this->getAppendTimeStamp() ? '?' . $this->processedFile->getProperty('tstamp') : '',
			$this->getTitle(),
			$this->getTitle(),
			$this->renderAttributes()
		);
	}

	/**
	 * Compute and return the title of the file.
	 *
	 * @return string
	 */
	protected function getTitle() {
		$result = $this->file->getProperty('title');
		if (empty($result)) {
			$result = $this->file->getName();
		}
		return htmlspecialchars($result);
	}

	/**
	 * Render a wrapping anchor around the thumbnail.
	 *
	 * @param string $result
	 * @return string
	 */
	public function renderTagAnchor($result) {

		$file = $this->file;

		// Perhaps the wrapping file must be processed
		$configurationWrap = $this->getConfigurationWrap();

		// Make sure we have configurationWrap initialized correctly
		if (!empty($configurationWrap['width']) || !empty($configurationWrap['height'])) {
			$configurationWrap = array_merge($this->defaultConfigurationWrap, $configurationWrap);

			// It looks maxW or maxH does not work as expected with CONTEXT_IMAGEPREVIEW...
			// ... uses "width" and "height" instead.
			if ($configurationWrap['width'] < $this->file->getProperty('width')
				|| $configurationWrap['height'] < $this->file->getProperty('height')
			) {
				$configurationWrap = $this->computeFinalImageDimension($configurationWrap);
				$file = $this->file->process($this->getProcessingType(), $configurationWrap);
			}
		}

		return sprintf('<a href="%s%s" target="%s" data-uid="%s">%s</a>',
			$this->getAnchorUri() ? $this->getAnchorUri() : $file->getPublicUrl(TRUE),
			$this->getAppendTimeStamp() && !$this->getAnchorUri() ? '?' . $file->getProperty('tstamp') : '',
			$this->getTarget(),
			$file->getUid(),
			$result
		);
	}

	/**
	 * Compute the final configuration for the image preview.
	 * Keep ratio of width / height for the image.
	 *
	 * @param array $configuration
	 * @return array
	 */
	protected function computeFinalImageDimension(array $configuration) {
		$ratio = $this->computeImageRatio();

		if ($ratio > 1) {
			$configuration['height'] = round($configuration['width'] / $ratio);
		} else {
			$configuration['width'] = round($configuration['height'] * $ratio);
		}
		return $configuration;
	}

	/**
	 * Compute the width / height ratio of the image.
	 *
	 * @return NULL|float
	 */
	protected function computeImageRatio() {
		$ratio = NULL;
		if ($this->file->getProperty('width') > 0 && $this->file->getProperty('height') > 0) {
			$ratio = $this->file->getProperty('width') / $this->file->getProperty('height');
		}
		return $ratio;
	}

	/**
	 * @return string
	 */
	public function getProcessingType() {
		if ($this->processingType === NULL) {
			return ProcessedFile::CONTEXT_IMAGECROPSCALEMASK;
		}
		return $this->processingType;
	}
}
