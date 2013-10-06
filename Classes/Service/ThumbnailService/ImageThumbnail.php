<?php
namespace TYPO3\CMS\Media\Service\ThumbnailService;

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
use TYPO3\CMS\Core\Resource\ProcessedFile;

/**
 */
class ImageThumbnail extends \TYPO3\CMS\Media\Service\ThumbnailService
	implements \TYPO3\CMS\Media\Service\ThumbnailRenderableInterface {

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
		while($step = array_shift($steps)) {
			$result = $this->$step($result);
		}

		return $result;
	}

	/**
	 * Render the URI of the thumbnail.
	 *
	 * @return string
	 */
	public function renderUri(){

		// Makes sure the width and the height of the thumbnail is not bigger than the actual file
		$configuration = $this->getConfiguration();
		if (!empty($configuration['width']) && $configuration['width'] > $this->file->getProperty('width')) {
			$configuration['width'] = $this->file->getProperty('width');
		}
		if (!empty($configuration['height']) && $configuration['height'] > $this->file->getProperty('height')) {
			$configuration['height'] = $this->file->getProperty('height');
		}

		$taskType = \TYPO3\CMS\Core\Resource\ProcessedFile::CONTEXT_IMAGEPREVIEW;

		$this->processedFile = $this->file->process($taskType, $configuration);

		return $this->processedFile->getPublicUrl(TRUE);
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
		$result = $this->overlayFile->getProperty('title');
		if (empty($result)) {
			$result = $this->overlayFile->getName();
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

		$file =  $this->file;

		// Perhaps the wrapping file must be processed
		$configurationWrap = $this->getConfigurationWrap();

		// Make sure we have configurationWrap initialized correctly
		if (!empty($configurationWrap['width']) || !empty($configurationWrap['height'])) {
			$configurationWrap = array_merge($this->defaultConfigurationWrap, $configurationWrap);

			// It looks maxW or maxH does not work as expected with CONTEXT_IMAGEPREVIEW...
			// ... uses "width" and "height" instead.
			if ($configurationWrap['width'] < $this->file->getProperty('width')
				|| $configurationWrap['height'] < $this->file->getProperty('height'))
			{
				$taskType = ProcessedFile::CONTEXT_IMAGEPREVIEW;
				$file = $this->file->process($taskType, $configurationWrap);
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
}
?>