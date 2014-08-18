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
use TYPO3\CMS\Media\Utility\ModuleUtility;

/**
 */
class ApplicationThumbnail extends \TYPO3\CMS\Media\Service\ThumbnailService
	implements \TYPO3\CMS\Media\Service\ThumbnailRenderableInterface {

	/**
	 * Render a thumbnail of a resource of type application.
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
			$this->processedFile = $this->file->process($this->getProcessingType(), $this->getConfiguration());
			$result = $this->processedFile->getPublicUrl(TRUE);

			// Update time stamp of processed image at this stage. This is needed for the browser to get new version of the thumbnail.
			if ($this->processedFile->getProperty('originalfilesha1') != $this->file->getProperty('sha1')) {
				$this->processedFile->updateProperties(array('tstamp' => $this->file->getProperty('tstamp')));
			}
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
		return sprintf('<img src="%s%s" title="%s" alt="%s" %s/>',
			$result,
			$this->getAppendTimeStamp() ? '?' . $this->getTimeStamp() : '',
			$this->getTitle(),
			$this->getTitle(),
			$this->renderAttributes()
		);
	}

	/**
	 * Compute and return the time stamp.
	 *
	 * @return int
	 */
	protected function getTimeStamp(){
		$result = $this->file->getProperty('tstamp');
		if ($this->processedFile) {
			$result = $this->processedFile->getProperty('tstamp');
		}
		return $result;
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
		$uri = $this->getAnchorUri();
		if (! $uri) {
			$uri = sprintf('%s&%s[asset]=%s',
				ModuleUtility::getUri('show', 'Asset'),
				ModuleUtility::getParameterPrefix(),
				$this->file->getUid()
			);
		}

		return sprintf('<a href="%s" target="_blank" data-uid="%s">%s</a>',
			$uri,
			$this->file->getUid(),
			$result
		);
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
