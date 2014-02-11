<?php
namespace TYPO3\CMS\Media\Controller\Backend;
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
 * Controller which handles actions related to File Variants.
 */
class VariantController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 * @inject
	 */
	protected $variantRepository;

	/**
	 * Handle the file upload action for a File Variant
	 *
	 * @throws \TYPO3\CMS\Media\Exception\MissingKeyInArrayException
	 * @param array $variant
	 * @return string
	 */
	public function uploadAction(array $variant = array()) {

		$uploadedFileObject = NULL;

		if (empty($variant['original'])) {
			throw new \TYPO3\CMS\Media\Exception\MissingKeyInArrayException('Missing "original" value', 1362673433);
		}

		/** @var $uploadManager \TYPO3\CMS\Media\FileUpload\UploadManager */
		$uploadManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\UploadManager');
		try {
			/** @var $uploadedFileObject \TYPO3\CMS\Media\FileUpload\UploadedFileInterface */
			$uploadedFileObject = $uploadManager->handleUpload();
		} catch (\Exception $e) {
			$response = array('error' => $e->getMessage());
		}

		if (is_object($uploadedFileObject)) {

			// TRUE means a file already exists and we should update it.
			$fileObject = NULL;
			if (!empty($variant['variant']) && (int) $variant['variant'] > 0) {
				/** @var $fileObject \TYPO3\CMS\Core\Resource\File */
				$fileObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getFileObject($variant['variant']);
			}
			$targetFolderObject = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getVariantTargetFolder();

			try {
				$conflictMode = is_object($fileObject) ? 'replace' : 'changeName';
				$fileName = is_object($fileObject) ? $fileObject->getName() : 'variant_' . $uploadedFileObject->getName();
				$newFileObject = $targetFolderObject->addFile($uploadedFileObject->getFileWithAbsolutePath(), $fileName , $conflictMode);

				$newFileObject->updateProperties(array(
					'tstamp' => time(), // Update the tstamp - which is not updated by addFile()
					'is_variant' => 1,
				));

				/** @var $fileRepository \TYPO3\CMS\Core\Resource\FileRepository */
				$fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\FileRepository');
				$fileRepository->update($newFileObject);

				// Call the indexer service for updating the metadata of the file, e.g width, height.
				/** @var $indexerService \TYPO3\CMS\Core\Resource\Service\IndexerService */
				$indexerService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Service\IndexerService');
				$indexerService->indexFile($newFileObject, TRUE);

				// Reminder: Variant Object make the join between the original file and the Variant file.
				// Additionally, it also stores the variation kind.

				// Persist variation of the File.
				// Case 1: the file exists and variation needs to be updated
				// Case 2: new file!
				if (is_object($fileObject)) {

					/** @var $variantObject \TYPO3\CMS\Media\Domain\Model\Variant */
					$variantObject = $this->variantRepository->findOneByVariant($fileObject);
					$variantObject->setVariation($variant['variation']);
					$this->variantRepository->update($variantObject);
				} else {
					$variant['variant'] = $newFileObject->getUid();
					$variantObject = new \TYPO3\CMS\Media\Domain\Model\Variant($variant);
					$this->variantRepository->add($variantObject);
				}

				$response = array(
					'success' => TRUE,
					'uid' => $newFileObject->getUid(),
					'original' => $variant['original'],
					'name' => $newFileObject->getName(),
					'publicUrl' => $newFileObject->getPublicUrl(),
					'timeStamp' => $newFileObject->getProperty('tstamp'),
					'width' => $newFileObject->getProperty('width'),
					'height' => $newFileObject->getProperty('height'),
				);
			} catch (\TYPO3\CMS\Core\Resource\Exception\UploadException $e) {
				$response = array('error' => 'The upload has failed, no uploaded file found!');
			} catch (\TYPO3\CMS\Core\Resource\Exception\InsufficientUserPermissionsException $e) {
				$response = array('error' => 'You are not allowed to upload files!');
			} catch (\TYPO3\CMS\Core\Resource\Exception\UploadSizeException $e) {
				$response = array('error' => vsprintf('The uploaded file "%s" exceeds the size-limit', array($fileName)));
			} catch (\TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException $e) {
				$response = array('error' => vsprintf('Destination path "%s" was not within your mount points!', array($targetFolderObject->getIdentifier())));
			} catch (\TYPO3\CMS\Core\Resource\Exception\IllegalFileExtensionException $e) {
				$response = array('error' => vsprintf('Extension of file name "%s" is not allowed in "%s"!', array($fileName, $targetFolderObject->getIdentifier())));
			} catch (\TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException $e) {
				$response = array('error' => vsprintf('No unique filename available in "%s"!', array($targetFolderObject->getIdentifier())));
			} catch (\RuntimeException $e) {
				$response = array('error' => vsprintf('Uploaded file could not be moved! Write-permission problem in "%s"?', array($targetFolderObject->getIdentifier())));
			}
		}

		header("Content-Type: text/json");
		return htmlspecialchars(json_encode($response), ENT_NOQUOTES);
	}
}
