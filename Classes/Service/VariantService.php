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
use TYPO3\CMS\Media\ObjectFactory;

/**
 * A class providing services related to Variants.
 */
class VariantService {

	/**
	 * @var \TYPO3\CMS\Core\Resource\FileRepository
	 * @inject
	 */
	protected $fileRepository;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 */
	protected $variantRepository;

	/**
	 * @var \TYPO3\CMS\Frontend\Imaging\GifBuilder
	 */
	protected $gifCreator;

	/**
	 * @return \TYPO3\CMS\Media\Service\VariantService
	 */
	public function __construct() {
		$this->fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\FileRepository');

		/** @var \TYPO3\CMS\Media\Domain\Repository\VariantRepository $variantRepository */
		$variantRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Domain\Repository\VariantRepository');
		$this->variantRepository = $variantRepository;

		$this->gifCreator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Imaging\\GifBuilder');
		$this->gifCreator->init();
		$this->gifCreator->absPrefix = PATH_site;
	}

	/**
	 * Create a Variant out of File according to configuration.
	 *
	 * @param \TYPO3\CMS\Core\Resource\File $file
	 * @param array $configuration
	 *              + width maximum width of image
	 *              + height maximum height of image
	 * @return \TYPO3\CMS\Media\Domain\Model\Variant|NULL
	 */
	public function create($file, array $configuration) {

		$result = NULL;
		if ($this->doProcess($file, $configuration)) {

			// Retrieve Variant container.
			$storageIdentifier = $file->getProperty('storage');
			$targetFolderObject = ObjectFactory::getInstance()->getVariantTargetFolder($storageIdentifier);
			$variantFile = $file->copyTo($targetFolderObject, 'variant_' . $file->getName(), 'renameNewFile');

			$fileNameWithPath = PATH_site . $variantFile->getPublicUrl();

			// Create file (optimizer)
			$fileInfo = $this->resize($fileNameWithPath, $configuration['width'], $configuration['height']);
			$variation = sprintf('resize({width: %s, height: %s})',
				$fileInfo[0],
				$fileInfo[1]
			);

			$variantFile->updateProperties(array(
				'tstamp' => time(), // Update the tstamp - which is not updated by addFile()
				'is_variant' => 1,
			));

			/** @var $fileRepository \TYPO3\CMS\Core\Resource\FileRepository */
			$this->fileRepository->update($variantFile);

			// Persist Variation
			$variant['original'] = $file->getUid();
			$variant['variant'] = $variantFile->getUid();
			$variant['variation'] = $variation;

			$variantObject = new \TYPO3\CMS\Media\Domain\Model\Variant($variant);
			$result = $this->variantRepository->add($variantObject);
		}

		return $result;
	}

	/**
	 * Update a Variant according to its configuration.
	 *
	 * @param \TYPO3\CMS\Core\Resource\File $file
	 * @param \TYPO3\CMS\Core\Resource\File $variantFile
	 * @param array $configuration
	 *              + width maximum width of image
	 *              + height maximum height of image
	 * @return \TYPO3\CMS\Media\Domain\Model\Variant
	 */
	public function update($file, $variantFile, array $configuration) {

		// Retrieve Variant container.
		$targetFolderObject = $variantFile->getStorage()->getFolder(dirname($variantFile->getIdentifier()));

		$variantFile = $file->copyTo($targetFolderObject, $variantFile->getName(), 'overrideExistingFile');
		$fileNameWithPath = PATH_site . $variantFile->getPublicUrl();

		// Create file (optimizer)
		$this->resize($fileNameWithPath, $configuration['width'], $configuration['height']);

		$variantFile->updateProperties(array(
			'tstamp' => time(), // Update the tstamp - which is not updated by addFile()
		));

		/** @var $fileRepository \TYPO3\CMS\Core\Resource\FileRepository */
		$this->fileRepository->update($variantFile);

		return $variantFile;
	}

	/**
	 * Tell whether to create a Variant or not
	 *
	 * @param \TYPO3\CMS\Core\Resource\File $file
	 * @param array $configuration
	 * @return boolean
	 */
	public function doProcess($file, $configuration) {
		if (empty($configuration['width']) || empty($configuration['height'])) {

			// For now write a log entry. Height and Width could become optional. If you need this report an issue.
			$logger = \TYPO3\CMS\Media\Utility\Logger::getInstance($this);
			$logger->warning('Missing width or height as configuration', $configuration);
			$result = FALSE;
		} else if ($file->getProperty('width') > $file->getProperty('height')) { // image orientation
			$result = $file->getProperty('width') > $configuration['width'];
		} else {
			$result = $file->getProperty('height') > $configuration['height'];
		}
		return $result;
	}

	/**
	 * Resize an image according to given parameter
	 *
	 * @throws \Exception
	 * @param string $fileNameAndPath
	 * @param int $width
	 * @param int $height
	 * @return array
	 */
	public function resize($fileNameAndPath, $width = 0, $height = 0) {
		// Keep profile of the image
		$imParams = '###SkipStripProfile###';
		$options = array(
			'maxW' => $width,
			'maxH' => $height,
		);

		// Renamed image is typo3temp directory
		$tempFileInfo = $this->gifCreator->imageMagickConvert($fileNameAndPath, '', '', '', $imParams, '', $options, TRUE);
		if ($tempFileInfo) {
			@rename($tempFileInfo[3], $fileNameAndPath);
		}
		return $tempFileInfo;
	}
}
?>