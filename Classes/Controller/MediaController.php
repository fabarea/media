<?php
namespace TYPO3\CMS\Media\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
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
 * Controller which handles Media actions
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class MediaController extends \TYPO3\CMS\Media\Controller\BaseController {

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\AssetRepository
	 */
	protected $assetRepository;

	/**
	 * @param \TYPO3\CMS\Media\Domain\Repository\AssetRepository $assetRepository
	 * @return void
	 */
	public function injectAssetRepository(\TYPO3\CMS\Media\Domain\Repository\AssetRepository $assetRepository) {
		$this->assetRepository = $assetRepository;
	}

	/**
	 * List action for this controller. Displays a list of medias
	 *
	 * @return string The rendered view
	 */
	public function listAction() {
		$this->view->assign('columns', \TYPO3\CMS\Media\Tca\ServiceFactory::getGridService('sys_file')->getFieldList());
		$this->view->assign('medias', $this->assetRepository->findAll());
	}

	/**
	 * List Row action for this controller. Output a json list of medias
	 * This action is expected to have a parameter format = json
	 *
	 * @return string The rendered view
	 */
	public function listRowAction() {

		// Initialize some objects related to the query
		$filterObject = $this->createFilterObject();
		$orderObject = $this->createOrderObject();
		$pagerObject = $this->createPagerObject();

		// Query the repository
		$medias = $this->assetRepository->findFiltered($filterObject, $orderObject, $pagerObject->getOffset(), $pagerObject->getItemsPerPage());
		$numberOfMedias = $this->assetRepository->countFiltered($filterObject);
		$pagerObject->setCount($numberOfMedias);

		// Assign values
		$this->view->assign('medias', $medias);
		$this->view->assign('numberOfMedias', $numberOfMedias);
		$this->view->assign('pager', $pagerObject);

		$this->request->setFormat('json');
		# Json header is not automatically respected in the BE... so send one the hard way.
		header('Content-type: application/json');
	}

	/**
	 * action show
	 *
	 * @param int $media
	 * @return void
	 */
	public function showAction($media) {
		$this->view->assign('media', $media);
	}

	/**
	 * Action new: return a form for creating a new media
	 *
	 * @param array $media
	 * @return void
	 * @dontvalidate $media
	 */
	public function newAction(array $media = array()) {

		// Makes sure a media type is set.
		$media['type'] = empty($media['type']) ? 0 : (int) $media['type'];

		/** @var $mediaFactory \TYPO3\CMS\Media\MediaFactory */
		$mediaFactory = \TYPO3\CMS\Media\MediaFactory::getInstance();

		/** @var $mediaObject \TYPO3\CMS\Media\Domain\Model\Media */
		$mediaObject = $mediaFactory->createObject($media);
		$mediaObject->setIndexIfNotIndexed(FALSE); // mandatory, otherwise FAL will try to index a non yet created object.
		$this->view->assign('media', $mediaObject);
	}

	/**
	 * Action create: store a new media in the repository
	 *
	 * @param array $media
	 * @return void
	 * @dontvalidate $media
	 */
	public function createAction(array $media = array()) {
		// @todo check add method when achieving the upload feature
		//$this->assetRepository->add($media);

		// Prepare output
		$result['status'] = FALSE;
		$result['action'] = 'create';
		$result['media'] = array('uid' => '','title' => '',);

		$media['storage'] = \TYPO3\CMS\Media\Utility\Configuration::get('storage');
		$mediaUid = $this->assetRepository->addMedia($media);

		if ($mediaUid > 0) {
			$mediaObject = $this->assetRepository->findByUid($mediaUid);
			$result['status'] = TRUE;
			$result['media'] = array(
				'uid' => $mediaObject->getUid(),
				'title' => $mediaObject->getTitle(),
			);
		}

		# Json header is not automatically respected in the BE... so send one the hard way.
		header('Content-type: application/json');
		return json_encode($result);
	}

	/**
	 * Action edit
	 *
	 * @param int $media
	 * @return void
	 */
	public function editAction($media) {
		$mediaObject = $this->assetRepository->findByUid($media);
		$this->view->assign('media', $mediaObject);
	}

	/**
	 * Action update media.
	 *
	 * @param array $media
	 * @return void
	 * @dontvalidate $media
	 */
	public function updateAction(array $media) {
		$this->assetRepository->updateMedia($media);
		$mediaObject = $this->assetRepository->findByUid($media['uid']);
		$result['status'] = TRUE;
		$result['action'] = 'update';
		$result['media'] = array(
			'uid' => $mediaObject->getUid(),
			'title' => $mediaObject->getTitle(),
		);

		# Json header is not automatically respected in the BE... so send one the hard way.
		header('Content-type: application/json');
		return json_encode($result);
	}

	/**
	 * Delete a row given a media uid.
	 * This action is expected to have a parameter format = json
	 *
	 * @param int $media
	 * @return string
	 */
	public function deleteAction($media) {
		$mediaObject = $this->assetRepository->findByUid($media);
		$result['status'] = $this->assetRepository->remove($mediaObject);
		$result['action'] = 'delete';
		$result['media'] = array(
			'uid' => $mediaObject->getUid(),
			'title' => $mediaObject->getTitle(),
		);

		# Json header is not automatically respected in the BE... so send one the hard way.
		header('Content-type: application/json');
		return json_encode($result);
	}

	/**
	 * Download securely an asset
	 * @todo secure download should be implemented somewhere else (Core?). Put it here for the time being for pragmatic reasons...
	 *
	 * @param int $media
	 * @return void
	 */
	public function downloadAction($media) {

		/** @var $media \TYPO3\CMS\Media\Domain\Model\Media */
		$media = $this->assetRepository->findByUid($media);

		if (is_object($media) && $media->exists() && $media->checkActionPermission('read')) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $media->getMimeType());
			header('Content-Disposition: inline; filename="' . $media->getName() . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . $media->getSize());
			flush();
			readfile(PATH_site .  $media->getPublicUrl());
			return;
		}
		else {
			$result = "Access denied!";
		}
		return $result;
	}

	/**
	 * Handle the file upload action
	 *
	 * @param array $media
	 * @return string
	 */
	public function uploadAction(array $media = array()){

		// @todo transfer directory can be removed if a random name is given to the file.
		$uploadDirectory = PATH_site . 'typo3temp/UploadedFilesTransfer';
		\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir($uploadDirectory);

		/** @var $uploadManager \TYPO3\CMS\Media\FileUpload\UploadManager */
		$uploadManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\UploadManager');
		try {
			/** @var $uploadedFileObject \TYPO3\CMS\Media\FileUpload\UploadedFileInterface */
			$uploadedFileObject = $uploadManager->handleUpload($uploadDirectory, 'replace');
		} catch (\Exception $e) {
			$response = array('error' => $e->getMessage());
		}

		if (is_object($uploadedFileObject)) {

			// Try to instantiate a file object.
			$fileObject = NULL;
			if (!empty($media['uid'])) {
				/** @var $fileObject \TYPO3\CMS\Core\Resource\File */
				$fileObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getFileObject($media['uid']);
			}

			$temporaryFileName = sprintf('%s/%s', $uploadDirectory, $uploadedFileObject->getName());
			$conflictMode = is_object($fileObject) ? 'replace' : 'changeName';
			$fileName = is_object($fileObject) ? $fileObject->getName() : $uploadedFileObject->getName();

			try {
				$targetFolderObject = \TYPO3\CMS\Media\Utility\StorageFolder::get();
				$newFileObject = $targetFolderObject->addFile($temporaryFileName, $fileName , $conflictMode);

				// update tstamp which is not handled by addFile()
				$newFileObject->updateProperties(array('tstamp' => time()));
				/** @var $fileRepository \TYPO3\CMS\Core\Resource\FileRepository */
				$fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\FileRepository');
				$fileRepository->update($newFileObject);

				/** @var $thumbnailService \TYPO3\CMS\Media\Service\Thumbnail */
				$thumbnailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\Thumbnail');

				$response = array(
					'success' => TRUE,
					'uid' => $newFileObject->getUid(),
					'thumbnail' => $thumbnailService->setFile($newFileObject)->create(),
					// @todo hardcoded for now...
					'formAction' => 'mod.php?M=user_MediaM1&tx_media_user_mediam1[format]=json&tx_media_user_mediam1[action]=update&tx_media_user_mediam1[controller]=Media'
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

		// to pass data through iframe you will need to encode all html tags
		header("Content-Type: text/plain");
		return htmlspecialchars(json_encode($response), ENT_NOQUOTES);
	}
}
?>
