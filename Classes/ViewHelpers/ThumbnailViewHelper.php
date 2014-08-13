<?php
namespace TYPO3\CMS\Media\ViewHelpers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Utility\ImagePresetUtility;

/**
 * View helper which returns a configurable thumbnail of an Asset
 */
class ThumbnailViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

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
		$this->registerArgument('fileIdentifier', 'string', 'File identifier to retrieve a file.', FALSE, '');
		$this->registerArgument('storage', 'int', 'The storage where the to find the file identifier.', FALSE, 0);
	}

	/**
	 * Returns a configurable thumbnail of an asset
	 *
	 * @throws \Exception
	 * @return string
	 */
	public function render() {

		$preset = $this->arguments['preset'];
		$configuration = $this->arguments['configuration'];
		$configurationWrap = $this->arguments['configurationWrap'];
		$attributes = $this->arguments['attributes'];
		$output = $this->arguments['output'];

		$fileIdentifier = $this->arguments['fileIdentifier'];
		if ($fileIdentifier) {
			$storage = $this->arguments['storage'];
			if ($storage < 1) {
				throw new \Exception('Missing storage argument', 1407166892);
			}
			$storageObject = ResourceFactory::getInstance()->getStorageObject($storage);
			if ($storageObject->hasFile($fileIdentifier)) {
				$file = $storageObject->getFile($fileIdentifier);
			}
		}

		/** @var $file \TYPO3\CMS\Media\Domain\Model\Asset */
		if ($preset) {
			$imageDimension = ImagePresetUtility::getInstance()->preset($preset);
			$configuration['width'] = $imageDimension->getWidth();
			$configuration['height'] = $imageDimension->getHeight();
		}

		/** @var $thumbnailService \TYPO3\CMS\Media\Service\ThumbnailService */
		$thumbnail = '';
		if ($file) {
			$thumbnailService = GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService', $file);
			$thumbnail = $thumbnailService->setConfiguration($configuration)
				->setConfigurationWrap($configurationWrap)
				->setAttributes($attributes)
				->setOutputType($output)
				->create();
		}

		return $thumbnail;
	}
}
