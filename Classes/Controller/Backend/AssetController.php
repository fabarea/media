<?php
namespace TYPO3\CMS\Media\Controller\Backend;

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

use TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException;
use TYPO3\CMS\Core\Resource\Exception\IllegalFileExtensionException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientUserPermissionsException;
use TYPO3\CMS\Core\Resource\Exception\UploadException;
use TYPO3\CMS\Core\Resource\Exception\UploadSizeException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Media\FileUpload\UploadedFileInterface;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Media\Service\ThumbnailInterface;
use TYPO3\CMS\Media\Service\ThumbnailService;

/**
 * Controller which handles actions related to Asset.
 */
class AssetController extends ActionController {

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

		// Configure property mapping to retrieve the file object.
		if ($this->arguments->hasArgument('file')) {

			/** @var \TYPO3\CMS\Media\TypeConverter\FileConverter $typeConverter */
			$typeConverter = $this->objectManager->get('TYPO3\CMS\Media\TypeConverter\FileConverter');

			$propertyMappingConfiguration = $this->arguments->getArgument('file')->getPropertyMappingConfiguration();
			$propertyMappingConfiguration->setTypeConverter($typeConverter);
		}
	}

	/**
	 * Delete a row given a file.
	 * This action is expected to have a parameter format = json
	 *
	 * @param File $file
	 * @return string
	 */
	public function deleteAction(File $file) {
		$fileData = array(
			'uid' => $file->getUid(),
			'title' => $file->getProperty('title'),
			'name' => $file->getProperty('title'), // "name" is the label of sys_file used in the flash message.
		);

		$result['status'] = $file->delete();
		$result['action'] = 'delete';
		if ($result['status']) {
			$result['object'] = $fileData;
		}

		# Json header is not automatically sent in the BE...
		$this->response->setHeader('Content-Type', 'application/json');
		$this->response->sendHeaders();
		return json_encode($result);
	}

	/**
	 * Mass delete a file.
	 * This action is expected to have a parameter format = json
	 *
	 * @param int $storageIdentifier
	 * @param array $assets
	 * @return string
	 */
	public function moveAction($storageIdentifier, $assets = array()) {

		$storage = ResourceFactory::getInstance()->getStorageObject($storageIdentifier);

		if ($storage) {

			foreach ($assets as $fileIdentifier) {

				$file = ResourceFactory::getInstance()->getFileObject($fileIdentifier);

				/** @var File $file */
				if ($file->getStorage()->getUid() !== $storage->getUid()) {

					// Retrieve target directory in the new storage. The folder will only be returned if the User has the correct permission.
					$targetFolder = ObjectFactory::getInstance()->getTargetFolder($storage, $file);
					$file->moveTo($targetFolder, $file->getName(), 'renameNewFile');
				}
			}
		}

		# Json header is not automatically sent in the BE...
		$this->response->setHeader('Content-Type', 'application/json');
		$this->response->sendHeaders();
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

		# Json header is not automatically sent in the BE...
		$this->response->setHeader('Content-Type', 'application/json');
		$this->response->sendHeaders();
		return json_encode($result);
	}

	/**
	 * Force download of the file.
	 *
	 * @param File $file
	 * @throws \Exception
	 * @return bool|string
	 */
	public function downloadAction(File $file) {

		if ($file->exists() && $file->getStorage()->isWithinFileMountBoundaries($file->getParentFolder())) {

			// Emit signal before downloading the file.
			$this->emitBeforeDownloadSignal($file);

			// Read the file and dump it with flag "forceDownload".
			$file->getStorage()->dumpFileContents($file, TRUE);

			$result = TRUE;
		} else {
			$result = 'Access denied!';
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

		/** @var UploadedFileInterface $uploadedFile */
		$uploadedFile = $this->handleUpload();
		if (!is_object($uploadedFile)) {
			return htmlspecialchars(json_encode($uploadedFile), ENT_NOQUOTES);
		}

		// Get the target folder
		$storage = ResourceFactory::getInstance()->getStorageObject($storageIdentifier);
		$targetFolder = ObjectFactory::getInstance()->getContainingFolder($uploadedFile, $storage);

		try {
			$conflictMode = 'changeName';
			$fileName = $uploadedFile->getName();
			$file = $targetFolder->addFile($uploadedFile->getFileWithAbsolutePath(), $fileName , $conflictMode);

			// Run the indexer for extracting metadata.
			$this->getIndexer($file->getStorage())
				->extractMetadata($file)
				->applyDefaultCategories($file);

			$response = array(
				'success' => TRUE,
				'uid' => $file->getUid(),
				'name' => $file->getName(),
				'thumbnail' => $this->getThumbnailService($file)->create(),
			);
		} catch (UploadException $e) {
			$response = array('error' => 'The upload has failed, no uploaded file found!');
		} catch (InsufficientUserPermissionsException $e) {
			$response = array('error' => 'You are not allowed to upload files!');
		} catch (UploadSizeException $e) {
			$response = array('error' => vsprintf('The uploaded file "%s" exceeds the size-limit', array($uploadedFile->getName())));
		} catch (InsufficientFolderWritePermissionsException $e) {
			$response = array('error' => vsprintf('Destination path "%s" was not within your mount points!', array($targetFolder->getIdentifier())));
		} catch (IllegalFileExtensionException $e) {
			$response = array('error' => vsprintf('Extension of file name "%s" is not allowed in "%s"!', array($uploadedFile->getName(), $targetFolder->getIdentifier())));
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

		$uploadedFile = $this->handleUpload();
		if (!is_object($uploadedFile)) {
			return htmlspecialchars(json_encode($uploadedFile), ENT_NOQUOTES);
		}

		/** @var $fileObject File */
		$fileObject = ResourceFactory::getInstance()->getFileObject($fileIdentifier);
		$fileObject->getType();
		$targetFolderObject = ObjectFactory::getInstance()->getContainingFolder($fileObject, $fileObject->getStorage());

		try {
			$conflictMode = 'replace';
			$fileName = $fileObject->getName();
			$file = $targetFolderObject->addFile($uploadedFile->getFileWithAbsolutePath(), $fileName, $conflictMode);

			// Run the indexer for extracting metadata.
			$this->getIndexer($file->getStorage())->extractMetadata($file);

			// Clear cache on pages holding a reference to this file.
			$this->getCacheService()->clearCache($file);

			$response = array(
				'success' => TRUE,
				'uid' => $file->getUid(),
				'name' => $file->getName(),
				'thumbnail' => $this->getThumbnailService($file)->create(),
				'fileInfo' => $this->getMetadataViewHelper()->render($file),
			);
		} catch (UploadException $e) {
			$response = array('error' => 'The upload has failed, no uploaded file found!');
		} catch (InsufficientUserPermissionsException $e) {
			$response = array('error' => 'You are not allowed to upload files!');
		} catch (UploadSizeException $e) {
			$response = array('error' => vsprintf('The uploaded file "%s" exceeds the size-limit', array($uploadedFile->getName())));
		} catch (InsufficientFolderWritePermissionsException $e) {
			$response = array('error' => vsprintf('Destination path "%s" was not within your mount points!', array($targetFolderObject->getIdentifier())));
		} catch (IllegalFileExtensionException $e) {
			$response = array('error' => vsprintf('Extension of file name "%s" is not allowed in "%s"!', array($uploadedFile->getName(), $targetFolderObject->getIdentifier())));
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

	/**
	 * @return \TYPO3\CMS\Media\ViewHelpers\MetadataViewHelper
	 */
	protected function getMetadataViewHelper() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\ViewHelpers\MetadataViewHelper');
	}

	/**
	 * @param File $file
	 * @return ThumbnailService
	 */
	protected function getThumbnailService(File $file) {

		/** @var $thumbnailService ThumbnailService */
		$thumbnailService = GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService', $file);
		$thumbnailService->setAppendTimeStamp(TRUE)
			->setOutputType(ThumbnailInterface::OUTPUT_IMAGE_WRAPPED);
		return $thumbnailService;
	}

	/**
	 * Instantiate the indexer service to update the metadata of the file.
	 *
	 * @param int|ResourceStorage $storage
	 * @return \TYPO3\CMS\Media\Index\Indexer
	 */
	protected function getIndexer($storage) {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Index\Indexer', $storage);
	}

	/**
	 * @return \TYPO3\CMS\Media\Cache\CacheService
	 */
	protected function getCacheService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Cache\CacheService');
	}

	/**
	 * Signal that is emitted before a file is downloaded.
	 *
	 * @param File $file
	 * @return void
	 * @signal
	 */
	protected function emitBeforeDownloadSignal(File $file) {
		$this->getSignalSlotDispatcher()->dispatch('TYPO3\CMS\Media\Controller\Backend\AssetController', 'beforeDownload', array($file));
	}

	/**
	 * Get the SignalSlot dispatcher.
	 *
	 * @return \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 */
	protected function getSignalSlotDispatcher() {
		return $this->objectManager->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
	}
}
