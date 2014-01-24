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
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Domain\Model\Variant;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Media\Utility\Logger;
use TYPO3\CMS\Media\Utility\VariantUtility;

/**
 * A class providing services related to Variants.
 */
class VariantService {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 * @inject
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
		$this->gifCreator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Imaging\\GifBuilder');
		$this->gifCreator->init();
		$this->gifCreator->absPrefix = PATH_site;
	}

	/**
	 * Create variants for new uploaded file.
	 *
	 * @param File $file
	 * @return void
	 */
	public function createVariants(File $file) {

		// Check whether Variant should be automatically created upon upload.
		$variations = $this->fetchVariations($file->getStorage());
		if (!empty($variations)) {

			/** @var \TYPO3\CMS\Media\Dimension $dimension */
			foreach ($variations['dimensions'] as $dimension) {
				$configuration = array(
					'width' => $dimension->getWidth(),
					'height' => $dimension->getHeight(),
				);
				$this->create($file, $configuration);
			}
		}
	}

	/**
	 * Create a Variant out of File according to a configuration array.
	 *
	 * @param File $file
	 * @param array $configuration
	 *              + width maximum width of image
	 *              + height maximum height of image
	 * @return Variant|NULL
	 */
	public function create(File $file, array $configuration) {

		$result = NULL;
		if ($this->doProcess($file, $configuration)) {

			// Retrieve Variant container.
			$storageIdentifier = $file->getProperty('storage');
			$targetFolderObject = ObjectFactory::getInstance()->getVariantTargetFolder($storageIdentifier);
			$newFile = $file->copyTo($targetFolderObject, 'variant_' . $file->getName(), 'renameNewFile');

			/** @var \TYPO3\CMS\Media\Domain\Model\Variant $variant */
			$variant = $this->objectManager->get('TYPO3\CMS\Media\Domain\Model\Variant',
				$newFile->getProperties(),
				$newFile->getStorage(),
				$file
			);

			$fileNameWithPath = PATH_site . $variant->getPublicUrl();

			// Create file (optimizer)
			$fileInfo = $this->resize($fileNameWithPath, $configuration['width'], $configuration['height']);
			$variation = sprintf('resize({width: %s, height: %s})', $fileInfo[0], $fileInfo[1]);
			$this->getIndexerService()->indexFile($variant); // Re-index the file

			$variant->updateProperties(array(
				'is_variant' => 1,
				'variation' => $variation,
			));

			$this->variantRepository->add($variant);
			$result = $variant;
		}

		return $result;
	}

	/**
	 * Update variants for existing uploaded file.
	 *
	 * @param File $file
	 * @return void
	 */
	public function updateVariants(File $file) {

		foreach ($this->variantRepository->findByOriginalResource($file) as $variant) {

			/** @var \TYPO3\CMS\Media\Dimension $variationDimension */
			$configuration = array(
				'width' => $variant->getProperty('width'),
				'height' => $variant->getProperty('height'),
			);
			$this->update($variant, $configuration);
		}
	}

	/**
	 * Update a Variant according to its configuration.
	 *
	 * @param Variant $variant
	 * @param array $configuration
	 *              + width maximum width of image
	 *              + height maximum height of image
	 * @return Variant
	 */
	public function update(Variant $variant, array $configuration) {

		// Retrieve Variant container.
		$targetFolderObject = $variant->getStorage()->getFolder(dirname($variant->getIdentifier()));

		$variant->getOriginalResource()->copyTo($targetFolderObject, $variant->getName(), 'overrideExistingFile');
		$fileNameWithPath = PATH_site . $variant->getPublicUrl();

		// Create file (optimizer)
		$this->resize($fileNameWithPath, $configuration['width'], $configuration['height']);
		$this->getIndexerService()->indexFile($variant); // Re-index the file

		$this->variantRepository->update($variant);
		return $variant;
	}

	/**
	 * Tell whether to create a Variant or not
	 *
	 * @param File $file
	 * @param array $configuration
	 * @return boolean
	 */
	protected function doProcess($file, $configuration) {

		if (empty($configuration['width']) || empty($configuration['height'])) {

			// For now write a log entry. Height and Width could become optional. If you need this report an issue.
			$logger = Logger::getInstance($this);
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
	protected function resize($fileNameAndPath, $width = 0, $height = 0) {
		// Keep profile of the image
		$imParams = '###SkipStripProfile###';
		$options = array(
			'maxW' => $width,
			'maxH' => $height,
		);

		// Renamed image is typo3temp directory
		$tempFileInfo = $this->gifCreator->imageMagickConvert($fileNameAndPath, '', '', '', $imParams, '', $options, TRUE);
		if (!empty($tempFileInfo)) {
			@rename($tempFileInfo[3], $fileNameAndPath);
		}
		return $tempFileInfo;
	}

	/**
	 * @param ResourceStorage $storage
	 * @return array
	 */
	protected function fetchVariations(ResourceStorage $storage) {
		$variations = array();
		$storageRecord = $storage->getStorageRecord();
		if (strlen($storageRecord['default_variations']) > 0) {
			$dimensions = GeneralUtility::trimExplode(',', $storageRecord['default_variations'], TRUE);
			foreach ($dimensions as $dimension) {

				/** @var \TYPO3\CMS\Media\Dimension $dimension */
				$variations['dimensions'][] = GeneralUtility::makeInstance('TYPO3\CMS\Media\Dimension', $dimension);
			}
		}
		return $variations;
	}

	/**
	 * Return the Indexer Service
	 *
	 * @return \TYPO3\CMS\Core\Resource\Service\IndexerService
	 */
	protected function getIndexerService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Service\IndexerService');
	}
}

?>