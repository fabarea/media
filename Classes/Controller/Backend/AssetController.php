<?php
namespace Fab\Media\Controller\Backend;

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

use Fab\Media\Module\MediaModule;
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
use Fab\Media\FileUpload\UploadedFileInterface;
use Fab\Media\Thumbnail\ThumbnailInterface;
use Fab\Media\Thumbnail\ThumbnailService;
use Fab\Vidi\Persistence\MatcherObjectFactory;
use Fab\Vidi\Tca\Tca;

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
	 * @var string
	 */
	protected $dataType = 'sys_file';

	/**
	 * @throws \Fab\Media\Exception\StorageNotOnlineException
	 */
	public function initializeAction() {
		$this->pageRenderer->addInlineLanguageLabelFile('EXT:media/Resources/Private/Language/locallang.xlf');

		// Configure property mapping to retrieve the file object.
		if ($this->arguments->hasArgument('file')) {

			/** @var \Fab\Media\TypeConverter\FileConverter $typeConverter */
			$typeConverter = $this->objectManager->get('Fab\Media\TypeConverter\FileConverter');

			$propertyMappingConfiguration = $this->arguments->getArgument('file')->getPropertyMappingConfiguration();
			$propertyMappingConfiguration->setTypeConverter($typeConverter);
		}

//		if ($this->arguments->hasArgument('storage')) {
//
//			/** @var \Fab\Media\TypeConverter\StorageConverter $typeConverter */
//			$typeConverter = $this->objectManager->get('Fab\Media\TypeConverter\StorageConverter');
//
//			$propertyMappingConfiguration = $this->arguments->getArgument('storage')->getPropertyMappingConfiguration();
//			$propertyMappingConfiguration->setTypeConverter($typeConverter);
//		}
	}

	/**
	 * Force download of the file.
	 *
	 * @param File $file
	 * @param bool $forceDownload
	 * @return bool|string
	 */
	public function downloadAction(File $file, $forceDownload = FALSE) {

		if ($file->exists() && $file->getStorage()->isWithinFileMountBoundaries($file->getParentFolder())) {

			// Emit signal before downloading the file.
			$this->emitBeforeDownloadSignal($file);

			// Read the file and dump it with the flag "forceDownload" set to TRUE or FALSE.
			$file->getStorage()->dumpFileContents($file, $forceDownload);

			$result = TRUE;
		} else {
			$result = 'Access denied!';
		}

		return $result;
	}

	/**
	 * Handle file upload for a new file.
	 *
	 * @param string $combinedIdentifier
	 * @validate $combinedIdentifier \Fab\Media\Domain\Validator\StorageValidator
	 * @return string
	 */
	public function createAction($combinedIdentifier) {
		/** @var UploadedFileInterface $uploadedFile */
		$uploadedFile = $this->handleUpload();
		if (!is_object($uploadedFile)) {
			return htmlspecialchars(json_encode($uploadedFile), ENT_NOQUOTES);
		}

		// Get the target folder.
		if ($this->getMediaModule()->hasFolderTree()) {
			$targetFolder = $this->getMediaModule()->getFolderForCombinedIdentifier($combinedIdentifier);
		} else {
			$storage = ResourceFactory::getInstance()->getStorageObjectFromCombinedIdentifier($combinedIdentifier);
			$targetFolder = $this->getMediaModule()->getTargetFolderForUploadedFile($uploadedFile, $storage);
		}

		try {
			$conflictMode = 'changeName';
			$fileName = $uploadedFile->getName();
			$file = $targetFolder->addFile($uploadedFile->getFileWithAbsolutePath(), $fileName, $conflictMode);

			// Run the indexer for extracting metadata.
			$this->getMediaIndexer($file->getStorage())
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
	 * @param File $file
	 * @return string
	 */
	public function updateAction(File $file) {
		$uploadedFile = $this->handleUpload();
		if (!is_object($uploadedFile)) {
			return htmlspecialchars(json_encode($uploadedFile), ENT_NOQUOTES);
		}

		/** @var $file File */
		$targetFolder = $file->getStorage()->getFolder(dirname($file->getIdentifier()));

		try {
			$conflictMode = 'replace';
			$fileName = $file->getName();
			$updatedFile = $targetFolder->addFile($uploadedFile->getFileWithAbsolutePath(), $fileName, $conflictMode);

			// Run the indexer for extracting metadata.
			$this->getMediaIndexer($updatedFile->getStorage())
				->updateIndex($updatedFile)
				->extractMetadata($updatedFile);

			// Clear cache on pages holding a reference to this file.
			$this->getCacheService()->clearCache($updatedFile);

			$response = array(
				'success' => TRUE,
				'uid' => $updatedFile->getUid(),
				'name' => $updatedFile->getName(),
				'thumbnail' => $this->getThumbnailService($updatedFile)->create(),
				'fileInfo' => $this->getMetadataViewHelper()->render($updatedFile),
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
	 * Returns an editing form for moving Files between storage.
	 *
	 * @param array $matches
	 * @throws \Exception
	 */
	public function editStorageAction(array $matches = array()) {

		$this->view->assign('storages', $this->getMediaModule()->getAllowedStorages());
		$this->view->assign('storageTitle', Tca::table('sys_file_storage')->getTitle());

		$fieldName = 'storage';

		// Instantiate the Matcher object according different rules.
		$matcher = MatcherObjectFactory::getInstance()->getMatcher($matches, $this->dataType);

		// Fetch objects via the Content Service.
		$contentService = $this->getContentService()->findBy($matcher);

		$fieldType = Tca::table($this->dataType)->field($fieldName)->getType();

		$this->view->assign('fieldType', ucfirst($fieldType));
		$this->view->assign('dataType', $this->dataType);
		$this->view->assign('matches', $matches);
		$this->view->assign('fieldNameAndPath', $fieldName);
		$this->view->assign('numberOfObjects', $contentService->getNumberOfObjects());
		$this->view->assign('editWholeSelection', empty($matches['uid'])); // necessary??
	}

	/**
	 * Handle file upload.
	 *
	 * @return \Fab\Media\FileUpload\UploadedFileInterface|array
	 */
	protected function handleUpload() {

		/** @var $uploadManager \Fab\Media\FileUpload\UploadManager */
		$uploadManager = GeneralUtility::makeInstance('Fab\Media\FileUpload\UploadManager');

		try {
			/** @var $result \Fab\Media\FileUpload\UploadedFileInterface */
			$result = $uploadManager->handleUpload();
		} catch (\Exception $e) {
			$result = array('error' => $e->getMessage());
		}

		return $result;
	}

	/**
	 * @return \Fab\Media\ViewHelpers\MetadataViewHelper
	 */
	protected function getMetadataViewHelper() {
		return GeneralUtility::makeInstance('Fab\Media\ViewHelpers\MetadataViewHelper');
	}

	/**
	 * @param File $file
	 * @return ThumbnailService
	 */
	protected function getThumbnailService(File $file) {

		/** @var $thumbnailService ThumbnailService */
		$thumbnailService = GeneralUtility::makeInstance('Fab\Media\Thumbnail\ThumbnailService', $file);
		$thumbnailService->setAppendTimeStamp(TRUE)
			->setOutputType(ThumbnailInterface::OUTPUT_IMAGE_WRAPPED);
		return $thumbnailService;
	}

	/**
	 * Get the instance of the Indexer service to update the metadata of the file.
	 *
	 * @param int|ResourceStorage $storage
	 * @return \Fab\Media\Index\MediaIndexer
	 */
	protected function getMediaIndexer($storage) {
		return GeneralUtility::makeInstance('Fab\Media\Index\MediaIndexer', $storage);
	}

	/**
	 * @return \Fab\Media\Cache\CacheService
	 */
	protected function getCacheService() {
		return GeneralUtility::makeInstance('Fab\Media\Cache\CacheService');
	}

	/**
	 * Signal that is emitted before a file is downloaded.
	 *
	 * @param File $file
	 * @return void
	 * @signal
	 */
	protected function emitBeforeDownloadSignal(File $file) {
		$this->getSignalSlotDispatcher()->dispatch('Fab\Media\Controller\Backend\AssetController', 'beforeDownload', array($file));
	}

	/**
	 * Get the SignalSlot dispatcher.
	 *
	 * @return \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 */
	protected function getSignalSlotDispatcher() {
		return $this->objectManager->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
	}

	/**
	 * Get the Vidi Module Loader.
	 *
	 * @return \Fab\Vidi\Service\ContentService
	 */
	protected function getContentService() {
		return GeneralUtility::makeInstance('Fab\Vidi\Service\ContentService', $this->dataType);
	}

	/**
	 * @return MediaModule
	 */
	protected function getMediaModule() {
		return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
	}

}
