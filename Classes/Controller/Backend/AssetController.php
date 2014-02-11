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
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Media\Domain\Model\Asset;
use TYPO3\CMS\Media\FileUpload\UploadedFileInterface;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Media\Utility\ConfigurationUtility;

/**
 * Controller which handles actions related to Asset.
 */
class AssetController extends ActionController {

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
	 * @var \TYPO3\CMS\Media\Service\VariantService
	 * @inject
	 */
	protected $variantService;

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
		$asset = $this->assetRepository->findByUid($asset);
		$assetData = array(
			'uid' => $asset->getUid(),
			'title' => $asset->getTitle(),
			'name' => $asset->getTitle(), // "name" is the label of sys_file used in the flash message.
		);

		$result['status'] = $asset->delete();
		$result['action'] = 'delete';
		if ($result['status']) {
			$result['object'] = $assetData;
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

				/** @var Asset $asset */
				if ($asset->getStorage()->getUid() !== $storage->getUid()) {

					// Retrieve target directory in the new storage. The folder will only be returned if the User has the correct permission.
					$targetFolder = ObjectFactory::getInstance()->getTargetFolder($storage, $asset);
					$asset->moveTo($targetFolder, $asset->getName(), 'renameNewFile');
				}
			}
		}

		# Json header is not automatically respected in the BE... so send one the hard way.
		header('Content-type: application/json');
		return json_encode(TRUE);
	}

	/**
	 * Mass delete a media
	 * This action is expected to have a parameter format = json
	 *
	 * @param array $assets
	 * @return string
	 */
	public function massDeleteAction($assets) {

		$result = array();
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

		/** @var $asset Asset */
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
	 * @validate $storageIdentifier \TYPO3\CMS\Media\Domain\Validator\StorageValidator
	 * @return string
	 */
	public function createAction($storageIdentifier) {

		/** @var UploadedFileInterface $uploadedFileObject */
		$uploadedFileObject = $this->handleUpload();
		if (!is_object($uploadedFileObject)) {
			return htmlspecialchars(json_encode($uploadedFileObject), ENT_NOQUOTES);
		}

		// Get the target folder
		$targetFolder = ObjectFactory::getInstance()->getContainingFolder($uploadedFileObject, $storageIdentifier);

		try {
			$conflictMode = 'changeName';
			$fileName = $uploadedFileObject->getName();
			$newFile = $targetFolder->addFile($uploadedFileObject->getFileWithAbsolutePath(), $fileName , $conflictMode);

			// Call the indexer service for updating the metadata of the file.
			/** @var $indexerService \TYPO3\CMS\Core\Resource\Service\IndexerService */
			$indexerService = GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Service\IndexerService');
			$indexerService->indexFile($newFile);

			/** @var $asset Asset */
			$asset = $this->assetRepository->findByUid($newFile->getUid());

			$categoryList = ConfigurationUtility::getInstance()->get('default_categories');
			$categories = GeneralUtility::trimExplode(',', $categoryList, TRUE);
			foreach ($categories as $category) {
				$asset->addCategory($category);
			}

			$this->variantService->createVariants($asset);

			// Persist the asset
			$this->assetRepository->update($asset);

			/** @var $thumbnailService \TYPO3\CMS\Media\Service\ThumbnailService */
			$thumbnailService = GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService');
			$thumbnailService->setAppendTimeStamp(TRUE);

			$response = array(
				'success' => TRUE,
				'uid' => $newFile->getUid(),
				'name' => $newFile->getName(),
				'thumbnail' => $asset->getThumbnailWrapped($thumbnailService),
			);
		} catch (UploadException $e) {
			$response = array('error' => 'The upload has failed, no uploaded file found!');
		} catch (InsufficientUserPermissionsException $e) {
			$response = array('error' => 'You are not allowed to upload files!');
		} catch (UploadSizeException $e) {
			$response = array('error' => vsprintf('The uploaded file "%s" exceeds the size-limit', array($uploadedFileObject->getName())));
		} catch (InsufficientFolderWritePermissionsException $e) {
			$response = array('error' => vsprintf('Destination path "%s" was not within your mount points!', array($targetFolder->getIdentifier())));
		} catch (IllegalFileExtensionException $e) {
			$response = array('error' => vsprintf('Extension of file name "%s" is not allowed in "%s"!', array($uploadedFileObject->getName(), $targetFolder->getIdentifier())));
		} catch (ExistingTargetFileNameException $e) {
			$response = array('error' => vsprintf('No unique filename available in "%s"!', array($targetFolder->getIdentifier())));
		} catch (\RuntimeException $e) {
			$response = array('error' => vsprintf('Uploaded file could not be moved! Write-permission problem in "%s"?', array($targetFolder->getIdentifier())));
		}

		// to pass data through iframe you will need to encode all html tags
		header("Content-Type: text/plain");
		return htmlspecialchars(json_encode($response), ENT_NOQUOTES);
	}

	/**
	 * Handle file upload for an existing file.
	 *
	 * @param int $fileIdentifier
	 * @validate $fileIdentifier \TYPO3\CMS\Media\Domain\Validator\FileValidator
	 * @return string
	 */
	public function updateAction($fileIdentifier) {

		$uploadedFileObject = $this->handleUpload();
		if (!is_object($uploadedFileObject)) {
			return htmlspecialchars(json_encode($uploadedFileObject), ENT_NOQUOTES);
		}

		/** @var $fileObject \TYPO3\CMS\Core\Resource\File */
		$fileObject = ResourceFactory::getInstance()->getFileObject($fileIdentifier);
		$fileObject->getType();
		$targetFolderObject = ObjectFactory::getInstance()->getContainingFolder($fileObject, $fileObject->getStorage()->getUid());

		try {
			$conflictMode = 'replace';
			$fileName = $fileObject->getName();
			$newFile = $targetFolderObject->addFile($uploadedFileObject->getFileWithAbsolutePath(), $fileName, $conflictMode);

			// Call the indexer service for updating the metadata of the file.
			/** @var $indexerService \TYPO3\CMS\Core\Resource\Service\IndexerService */
			$indexerService = GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Service\IndexerService');
			$indexerService->indexFile($newFile, TRUE);

			/** @var $asset Asset */
			$asset = $this->assetRepository->findByUid($newFile->getUid());
			$this->updateVariants($asset);

			// @todo fix me at the core level.
			$properties['tstamp'] = time(); // Force update tstamp - which is not done by addFile()
			$asset->updateProperties($properties);

			// Persist the asset
			$this->assetRepository->update($asset);

			/** @var $thumbnailService \TYPO3\CMS\Media\Service\ThumbnailService */
			$thumbnailService = GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService');
			$thumbnailService->setAppendTimeStamp(TRUE);

			$response = array(
				'success' => TRUE,
				'uid' => $newFile->getUid(),
				'name' => $newFile->getName(),
				'thumbnail' => $asset->getThumbnailWrapped($thumbnailService),
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
