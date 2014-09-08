<?php
namespace TYPO3\CMS\Media\ViewHelpers\File;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Media\Utility\ImagePresetUtility;
use TYPO3\CMS\Vidi\Domain\Model\Content;

/**
 * View helper which returns a configurable thumbnail for a File.
 */
class ThumbnailViewHelper extends AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('file', 'TYPO3\CMS\Core\Resource\File', 'The source file', FALSE, NULL);
		$this->registerArgument('configuration', 'array', 'Configuration to be given for the thumbnail processing.', FALSE, '');
		$this->registerArgument('attributes', 'array', 'DOM attributes to add to the thumbnail image', FALSE, '');
		$this->registerArgument('preset', 'string', 'Image dimension preset', FALSE, '');
		$this->registerArgument('output', 'string', 'Can be: uri, image, imageWrapped', FALSE, 'image');
		$this->registerArgument('configurationWrap', 'array', 'The configuration given to the wrap.', FALSE, '');
	}

	/**
	 * Returns a configurable thumbnail of an asset
	 *
	 * @throws \Exception
	 * @return string
	 */
	public function render() {

		$file = $this->arguments['file'];
		$preset = $this->arguments['preset'];
		$configuration = $this->arguments['configuration'];
		$configurationWrap = $this->arguments['configurationWrap'];
		$attributes = $this->arguments['attributes'];
		$output = $this->arguments['output'];

		if ($file instanceof Content) {
			$file = $this->getFileConverter()->convert($file);
		}

		if ($preset) {
			$imageDimension = ImagePresetUtility::getInstance()->preset($preset);
			$configuration['width'] = $imageDimension->getWidth();
			$configuration['height'] = $imageDimension->getHeight();
		}

		/** @var $thumbnailService \TYPO3\CMS\Media\Thumbnail\ThumbnailService */
		$thumbnailService = GeneralUtility::makeInstance('TYPO3\CMS\Media\Thumbnail\ThumbnailService', $file);
		$thumbnail = $thumbnailService->setConfiguration($configuration)
			->setConfigurationWrap($configurationWrap)
			->setAttributes($attributes)
			->setOutputType($output)
			->create();

		return $thumbnail;
	}

	/**
	 * @return \TYPO3\CMS\Media\TypeConverter\ContentToFileConverter
	 */
	protected function getFileConverter() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\TypeConverter\ContentToFileConverter');
	}
}
