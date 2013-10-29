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
use TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException;
use TYPO3\CMS\Core\Resource\Exception\IllegalFileExtensionException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientUserPermissionsException;
use TYPO3\CMS\Core\Resource\Exception\UploadException;
use TYPO3\CMS\Core\Resource\Exception\UploadSizeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\FileUpload\UploadedFileInterface;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Media\Utility\ConfigurationUtility;

/**
 * Controller which handles actions related to Asset.
 */
class AssetController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\AssetRepository
	 * @inject
	 */
	protected $assetRepository;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 * @inject
	 */
	protected $variantRepository;

	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 * @inject
	 */
	protected $pageRenderer;

	/**
	 * @throws \TYPO3\CMS\Media\Exception\StorageNotOnlineException
	 */
	public function initializeAction() {
		$this->pageRenderer->addInlineLanguageLabelFile('EXT:media/Resources/Private/Language/locallang.xlf');
	}

	/**
	 * Delete a row given a media uid.
	 * This action is expected to have a parameter format = json
	 *
	 * @param int $asset
	 * @return string
	 */
	public function deleteAction($asset) {
		$assetObject = $this->assetRepository->findByUid($asset);
		$result['status'] = $this->assetRepository->remove($assetObject);
		$result['action'] = 'delete';
		if ($result['status']) {
			$result['asset'] = array(
				'uid' => $assetObject->getUid(),
				'title' => $assetObject->getTitle(),
			);
		}

		# Json header is not automatically respected in the BE... so send one the hard way.
		header('Content-type: application/json');
		return json_encode($result);
	}

	/**
	 * Mass delete a media
	 * This action is expected to have a parameter format = json
	 *
	 * @param int $storageIdentifier
	 * @param array $assets
	 * @return string
	 */
	public function moveAction($storageIdentifier, $assets = array()) {

		$storage = ObjectFactory::getInstance()->getStorage($storageIdentifier);

		if ($storage) {

			foreach ($assets as $assetIdentifier) {

				$asset = $this->assetRepository->findByIdentifier($assetIdentifier);

				/** @var \TYPO3\CMS\Media\Domain\Model\Asset $asset */
				if ($asset->getStorage()->getUid() !== $storage->getUid()) {

					// Retrieve target directory in the new storage. The folder will only be returned if the User has the correct permission.
					// @todo add a try / catch since exception can be risen.
					$targetFolder = ObjectFactory::getInstance()->getTargetFolder($storage, $asset);
					# @todo implement me! Moving file across storages is not yet implemented.
					#$asset->moveToStorage($targetFolder);

					# @todo Quick and dirty implementation!
					$sourceStorageConfiguration = $asset->getStorage()->getConfiguration();
					$sourceFileNameAndPath = sprintf('%s/%s/%s',
						rtrim(PATH_site, '/'),
						trim($sourceStorageConfiguration['basePath'], '/'),
						trim($asset->getIdentifier(), '/')
					);

					$storageConfiguration = $storage->getConfiguration();
					$targetFileNameAndPath = sprintf('%s/%s/%s/%s',
						rtrim(PATH_site, '/'),
						trim($storageConfiguration['basePath'], '/'),
						trim($targetFolder->getIdentifier(), '/'),
						basename($asset->getIdentifier())
					);

					if (!file_exists($targetFileNameAndPath)) {

						rename($sourceFileNameAndPath, $targetFileNameAndPath);

						// Change file data
						$newIdentifier = sprintf('/%s/%s',
							trim($targetFolder->getIdentifier(), '/'),
							basename($asset->getIdentifier())
						);

						/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $db */
						$asset->updateProperties(
							array(
								'storage' => $storage->getUid(),
								'identifier' => $newIdentifier,
								'tstamp' => time(),
							)
						);
						$this->assetRepository->update($asset);
					}
				}
			}
		}

		$result = 1;
		# Json header is not automatically respected in the BE... so send one the hard way.
		header('Content-type: application/json');
		return json_encode($result);
	}

	/**
	 * Mass delete a media
	 * This action is expected to have a parameter format = json
	 *
	 * @param array $assets
	 * @return string
	 */
	public function massDeleteAction($assets) {

		foreach ($assets as $asset) {
			$result = $this->deleteAction($asset);
		}

		# Json header is not automatically respected in the BE... so send one the hard way.
		header('Content-type: application/json');
		return json_encode($result);
	}

	/**
	 * Read and output file to the browser.
	 *
	 * @todo secure download should be implemented somewhere else (Core?). Put it here for the time being for pragmatic reasons...
	 * @param int $asset
	 * @return string|boolean
	 */
	public function showAction($asset) {

		/** @var $asset \TYPO3\CMS\Media\Domain\Model\Asset */
		$asset = $this->assetRepository->findByUid($asset);

		// Consider also adding check "$asset->checkActionPermission('read')" <- should be handled in the Grid as well
		if (is_object($asset) && $asset->exists()) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $asset->getMimeType());
			header('Content-Disposition: inline; filename="' . $asset->getName() . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . $asset->getSize());
			flush();
			readfile(PATH_site .  $asset->getPublicUrl());
			return TRUE;
		}
		else {
			$result = "Access denied!";
		}
		return $result;
	}

	/**
	 * Handle file upload for a new file.
	 *
	 * @param int $storageIdentifier
	 * @validate $storageIdentifier TYPO3\CMS\Media\Domain\Validator\StorageValidator
	 * @return string
	 */
	public function createAction($storageIdentifier) {

		/** @var UploadedFileInterface $uploadedFileObject */
		$uploadedFileObject = $this->handleUpload();
		if (!is_object($uploadedFileObject)) {
			return htmlspecialchars(json_encode($uploadedFileObject), ENT_NOQUOTES);
		}

		// Get the target folder
		$targetFolderObject = ObjectFactory::getInstance()->getContainingFolder($uploadedFileObject, $storageIdentifier);

		try {
			$conflictMode = 'changeName';
			$fileName = $uploadedFileObject->getName();
			$newFileObject = $targetFolderObject->addFile($uploadedFileObject->getFileWithAbsolutePath(), $fileName , $conflictMode);

			// Call the indexer service for updating the metadata of the file.
			/** @var $indexerService \TYPO3\CMS\Core\Resource\Service\IndexerService */
			$indexerService = GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Service\IndexerService');
			$indexerService->indexFile($newFileObject, TRUE);

			/** @var $assetObject \TYPO3\CMS\Media\Domain\Model\Asset */
			$assetObject = $this->assetRepository->findByUid($newFileObject->getUid());

			$categoryList = ConfigurationUtility::getInstance()->get('default_categories');
			$categories = GeneralUtility::trimExplode(',', $categoryList);
			foreach ($categories as $category) {
				$assetObject->addCategory($category);
			}
			$this->createVariants($assetObject);

			// Persist the asset
			$this->assetRepository->update($assetObject);

			/** @var $thumbnailService \TYPO3\CMS\Media\Service\ThumbnailService */
			$thumbnailService = GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService');
			$thumbnailService->setAppendTimeStamp(TRUE);

			$response = array(
				'success' => TRUE,
				'uid' => $newFileObject->getUid(),
				'name' => $newFileObject->getName(),
				'thumbnail' => $assetObject->getThumbnailWrapped($thumbnailService),
			);
		} catch (UploadException $e) {
			$response = array('error' => 'The upload has failed, no uploaded file found!');
		} catch (InsufficientUserPermissionsException $e) {
			$response = array('error' => 'You are not allowed to upload files!');
		} catch (UploadSizeException $e) {
			$response = array('error' => vsprintf('The uploaded file "%s" exceeds the size-limit', array($uploadedFileObject->getName())));
		} catch (InsufficientFolderWritePermissionsException $e) {
			$response = array('error' => vsprintf('Destination path "%s" was not within your mount points!', array($targetFolderObject->getIdentifier())));
		} catch (IllegalFileExtensionException $e) {
			$response = array('error' => vsprintf('Extension of file name "%s" is not allowed in "%s"!', array($uploadedFileObject->getName(), $targetFolderObject->getIdentifier())));
		} catch (ExistingTargetFileNameException $e) {
			$response = array('error' => vsprintf('No unique filename available in "%s"!', array($targetFolderObject->getIdentifier())));
		} catch (\RuntimeException $e) {
			$response = array('error' => vsprintf('Uploaded file could not be moved! Write-permission problem in "%s"?', array($targetFolderObject->getIdentifier())));
		}

		// to pass data through iframe you will need to encode all html tags
		header("Content-Type: text/plain");
		return htmlspecialchars(json_encode($response), ENT_NOQUOTES);
	}

	/**
	 * Handle file upload for an existing file.
	 *
	 * @param int $fileIdentifier
	 * @validate $fileIdentifier TYPO3\CMS\Media\Domain\Validator\FileValidator
	 * @return string
	 */
	public function updateAction($fileIdentifier) {

		$uploadedFileObject = $this->handleUpload();
		if (!is_object($uploadedFileObject)) {
			return htmlspecialchars(json_encode($uploadedFileObject), ENT_NOQUOTES);
		}


		/** @var $fileObject \TYPO3\CMS\Core\Resource\File */
		$fileObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getFileObject($fileIdentifier);
		$fileObject->getType();
		$targetFolderObject = ObjectFactory::getInstance()->getContainingFolder($fileObject, $fileObject->getStorage()->getUid());

		try {
			$conflictMode = 'replace';
			$fileName = $fileObject->getName();
			$newFileObject = $targetFolderObject->addFile($uploadedFileObject->getFileWithAbsolutePath(), $fileName, $conflictMode);

			// Call the indexer service for updating the metadata of the file.
			/** @var $indexerService \TYPO3\CMS\Core\Resource\Service\IndexerService */
			$indexerService = GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Service\IndexerService');
			$indexerService->indexFile($newFileObject, TRUE);

			/** @var $assetObject \TYPO3\CMS\Media\Domain\Model\Asset */
			$assetObject = $this->assetRepository->findByUid($newFileObject->getUid());

			$this->updateVariants($assetObject);

			// @todo fix me at the core level.
			$properties['tstamp'] = time(); // Force update tstamp - which is not done by addFile()
			$assetObject->updateProperties($properties);

			// Persist the asset
			$this->assetRepository->update($assetObject);

			/** @var $thumbnailService \TYPO3\CMS\Media\Service\ThumbnailService */
			$thumbnailService = GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService');
			$thumbnailService->setAppendTimeStamp(TRUE);

			$response = array(
				'success' => TRUE,
				'uid' => $newFileObject->getUid(),
				'name' => $newFileObject->getName(),
				'thumbnail' => $assetObject->getThumbnailWrapped($thumbnailService),
			);
		} catch (UploadException $e) {
			$response = array('error' => 'The upload has failed, no uploaded file found!');
		} catch (InsufficientUserPermissionsException $e) {
			$response = array('error' => 'You are not allowed to upload files!');
		} catch (UploadSizeException $e) {
			$response = array('error' => vsprintf('The uploaded file "%s" exceeds the size-limit', array($uploadedFileObject->getName())));
		} catch (InsufficientFolderWritePermissionsException $e) {
			$response = array('error' => vsprintf('Destination path "%s" was not within your mount points!', array($targetFolderObject->getIdentifier())));
		} catch (IllegalFileExtensionException $e) {
			$response = array('error' => vsprintf('Extension of file name "%s" is not allowed in "%s"!', array($uploadedFileObject->getName(), $targetFolderObject->getIdentifier())));
		} catch (ExistingTargetFileNameException $e) {
			$response = array('error' => vsprintf('No unique filename available in "%s"!', array($targetFolderObject->getIdentifier())));
		} catch (\RuntimeException $e) {
			$response = array('error' => vsprintf('Uploaded file could not be moved! Write-permission problem in "%s"?', array($targetFolderObject->getIdentifier())));
		}

		// to pass data through iframe you will need to encode all html tags
		header("Content-Type: text/plain");
		return htmlspecialchars(json_encode($response), ENT_NOQUOTES);
	}

	/**
	 * Create variants for new uploaded file.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $assetObject
	 * @return void
	 */
	protected function createVariants(\TYPO3\CMS\Media\Domain\Model\Asset $assetObject) {

		$storageIdentifier = $assetObject->getStorage()->getUid();

		// Check whether Variant should be automatically created upon upload.
		$variations = \TYPO3\CMS\Media\Utility\VariantUtility::getInstance($storageIdentifier)->getVariations();
		if (!empty($variations)) {

			/** @var \TYPO3\CMS\Media\Service\VariantService $variantService */
			$variantService = $this->objectManager->get('TYPO3\CMS\Media\Service\VariantService');

			/** @var \TYPO3\CMS\Media\Dimension $variationDimension */
			foreach ($variations as $variationDimension) {
				$configuration = array(
					'width' => $variationDimension->getWidth(),
					'height' => $variationDimension->getHeight(),
				);
				$variantService->create($assetObject, $configuration);
			}
		}
	}

	/**
	 * Update variants for existing uploaded file.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return void
	 */
	protected function updateVariants(\TYPO3\CMS\Media\Domain\Model\Asset $asset) {

		/** @var \TYPO3\CMS\Media\Service\VariantService $variantService */
		$variantService = $this->objectManager->get('TYPO3\CMS\Media\Service\VariantService');
		foreach ($asset->getVariants() as $variant) {

			/** @var \TYPO3\CMS\Media\Dimension $variationDimension */
			$configuration = array(
				'width' => $variant->getVariant()->getWidth(),
				'height' => $variant->getVariant()->getHeight(),
			);
			$variantService->update($asset, $variant->getVariant(), $configuration);
		}
	}

	/**
	 * Handle file upload.
	 *
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface|array
	 */
	protected function handleUpload() {

		/** @var $uploadManager \TYPO3\CMS\Media\FileUpload\UploadManager */
		$uploadManager = GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\UploadManager');

		try {
			/** @var $result \TYPO3\CMS\Media\FileUpload\UploadedFileInterface */
			$result = $uploadManager->handleUpload();
		} catch (\Exception $e) {
			$result = array('error' => $e->getMessage());
		}

		return $result;
	}
}
?>
