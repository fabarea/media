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
 * Controller which handles actions related to Assets.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
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
	 * @var \TYPO3\CMS\Media\Utility\Setting
	 */
	protected $settingsManager;

	/**
	 * @throws \TYPO3\CMS\Media\Exception\StorageNotOnlineException
	 */
	public function initializeAction() {
		$this->settingsManager = \TYPO3\CMS\Media\Utility\Setting::getInstance();
		$storageUid = (int) $this->settingsManager->get('storage');
		$storageObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getStorageObject($storageUid);
		if (!$storageObject->isOnline()) {
			$message = sprintf('The storage "%s" looks currently off-line. Check the storage configuration if you think this is an error', $storageObject->getName());
			throw new \TYPO3\CMS\Media\Exception\StorageNotOnlineException($message, 1361461834);
		}
		$this->pageRenderer->addInlineLanguageLabelFile('EXT:media/Resources/Private/Language/locallang.xlf');
	}

	/**
	 * List action for this controller. Displays a list of assets
	 *
	 * @return void
	 */
	public function listAction() {
		$this->view->assign('columns', \TYPO3\CMS\Media\Tca\ServiceFactory::getGridService('sys_file')->getFields());
	}

	/**
	 * List Row action for this controller. Output a json list of assets
	 * This action is expected to have a parameter format = json
	 *
	 * @param array $matches
	 * @return void
	 */
	public function listRowAction($matches = array()) {

		// Initialize some objects related to the query
		$matchObject = $this->createMatcherObject();
		$matchObject->setDefaultLogicalOperator(\TYPO3\CMS\Media\QueryElement\Query::LOGICAL_OR);
		foreach ($matches as $propertyName => $value) {
			$matchObject->addMatch($propertyName, $value);
		}

		$orderObject = $this->createOrderObject();
		$pagerObject = $this->createPagerObject();

		// Query the repository
		$assets = $this->assetRepository->findBy($matchObject, $orderObject, $pagerObject->getLimit(), $pagerObject->getOffset());
		$numberOfAssets = $this->assetRepository->countBy($matchObject);
		$pagerObject->setCount($numberOfAssets);

		// Assign values
		$this->view->assign('assets', $assets);
		$this->view->assign('numberOfAssets', $numberOfAssets);
		$this->view->assign('pager', $pagerObject);

		$this->request->setFormat('json');
		# Json header is not automatically respected in the BE... so send one the hard way.
		header('Content-type: application/json');
	}

	/**
	 * Action new: return a form for creating a new media
	 *
	 * @param array $asset
	 * @return void
	 * @dontvalidate $asset
	 */
	public function newAction(array $asset = array()) {

		// Makes sure a media type is set.
		$asset['type'] = empty($asset['type']) ? 0 : (int) $asset['type'];

		/** @var $objectFactory \TYPO3\CMS\Media\ObjectFactory */
		$objectFactory = \TYPO3\CMS\Media\ObjectFactory::getInstance();

		/** @var $assetObject \TYPO3\CMS\Media\Domain\Model\Asset */
		$assetObject = $objectFactory->createObject($asset);
		$assetObject->setIndexable(FALSE); // mandatory, otherwise FAL will try to index a non yet created object.
		$this->view->assign('asset', $assetObject);
	}

	/**
	 * Action create: store a new media in the repository
	 *
	 * @param array $asset
	 * @return void
	 * @dontvalidate $asset
	 */
	public function createAction(array $asset = array()) {

		// Prepare output
		$result['status'] = FALSE;
		$result['action'] = 'create';
		$result['asset'] = array('uid' => '','title' => '',);

		$asset['storage'] = $this->settingsManager->get('storage');
		$asset['pid'] = \TYPO3\CMS\Media\Utility\MediaFolder::getDefaultPid();

		$assetUid = $this->assetRepository->addAsset($asset);

		if ($assetUid > 0) {
			$assetObject = $this->assetRepository->findByUid($assetUid);
			$result['status'] = TRUE;
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
	 * Action edit
	 *
	 * @param int $asset
	 * @return void
	 */
	public function editAction($asset) {
		$assetObject = $this->assetRepository->findByUid($asset);
		$this->view->assign('asset', $assetObject);
	}

	/**
	 * Handle GUI for creating a link in the RTE.
	 *
	 * @param int $asset
	 * @return void
	 */
	public function linkMakerAction($asset) {
		$assetObject = $this->assetRepository->findByUid($asset);
		$this->view->assign('asset', $assetObject);
	}

	/**
	 * Handle GUI for inserting an image in the RTE.
	 *
	 * @param int $asset
	 * @param int $variant a possible variant can be given.
	 * @return void
	 */
	public function imageMakerAction($asset, $variant = 0) {
		$assetObject = $this->assetRepository->findByUid($asset);

		$variantObject = NULL;
		if ($variant > 0) {
			$variantObject = $this->variantRepository->findOneByVariant($variant);
		}

		$this->view->assign('asset', $assetObject);
		$this->view->assign('variant', $variantObject);
	}

	/**
	 * Action update media.
	 *
	 * @param array $asset
	 * @return void
	 * @dontvalidate $asset
	 */
	public function updateAction(array $asset) {
		$this->assetRepository->updateAsset($asset);
		$assetObject = $this->assetRepository->findByUid($asset['uid']);
		$result['status'] = TRUE;
		$result['action'] = 'update';
		$result['asset'] = array(
			'uid' => $assetObject->getUid(),
			'title' => $assetObject->getTitle(),
		);

		# Json header is not automatically respected in the BE... so send one the hard way.
		header('Content-type: application/json');
		return json_encode($result);
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
	 * Download securely an asset
	 *
	 * @todo secure download should be implemented somewhere else (Core?). Put it here for the time being for pragmatic reasons...
	 * @param int $asset
	 * @return void
	 */
	public function downloadAction($asset) {

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
			return;
		}
		else {
			$result = "Access denied!";
		}
		return $result;
	}

	/**
	 * Handle the file upload action for a new file and an existing one.
	 *
	 * @param array $asset
	 * @return string
	 */
	public function uploadAction(array $asset = array()) {

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
			if (!empty($asset['uid'])) {
				/** @var $fileObject \TYPO3\CMS\Core\Resource\File */
				$fileObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getFileObject($asset['uid']);
				$fileObject->getType();
				$targetFolderObject = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getContainingFolder($fileObject);
			} else {
				// Get the target folder
				$targetFolderObject = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getContainingFolder($uploadedFileObject);
			}

			try {
				$conflictMode = is_object($fileObject) ? 'replace' : 'changeName';
				$fileName = is_object($fileObject) ? $fileObject->getName() : $uploadedFileObject->getName();
				$newFileObject = $targetFolderObject->addFile($uploadedFileObject->getFileWithAbsolutePath(), $fileName , $conflictMode);

				// Call the indexer service for updating the metadata of the file.
				/** @var $indexerService \TYPO3\CMS\Core\Resource\Service\IndexerService */
				$indexerService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Service\IndexerService');
				$indexerService->indexFile($newFileObject, TRUE);

				/** @var $assetObject \TYPO3\CMS\Media\Domain\Model\Asset */
				$assetObject = $this->assetRepository->findByUid($newFileObject->getUid());

				// Only for a new file
				if (empty($asset['uid'])) {
					$categoryList = $this->settingsManager->get('default_categories');
					$categories = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $categoryList);
					foreach ($categories as $category) {
						$assetObject->addCategory($category);
					}
					$properties['pid'] = \TYPO3\CMS\Media\Utility\MediaFolder::getDefaultPid();
				}

				$properties['tstamp'] = time(); // Force update tstamp - which is not done by addFile()
				$assetObject->updateProperties($properties);

				// Persist the asset
				$this->assetRepository->update($assetObject);

				// Check whether Variant should be automatically created upon upload.
				$variations = \TYPO3\CMS\Media\Utility\SettingVariant::getInstance()->getVariations();
				if (! empty($variations)) {

					/** @var \TYPO3\CMS\Media\Service\VariantService $variantService */
					$variantService = $this->objectManager->get('TYPO3\CMS\Media\Service\VariantService');

					foreach ($variations as $variation) {
						$configuration = array(
							'width' => $variation['width'],
							'height' => $variation['height'],
						);
						$variantService->create($assetObject, $configuration);
					}
				}

				/** @var $thumbnailService \TYPO3\CMS\Media\Service\ThumbnailService */
				$thumbnailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService');
				$thumbnailService->setAppendTimeStamp(TRUE);

				$response = array(
					'success' => TRUE,
					'uid' => $newFileObject->getUid(),
					'name' => $newFileObject->getName(),
					'thumbnail' => $assetObject->getThumbnailWrapped($thumbnailService),
					// @todo hardcoded for now...
					'formAction' => 'mod.php?M=user_MediaM1&tx_media_user_mediam1[format]=json&tx_media_user_mediam1[action]=update&tx_media_user_mediam1[controller]=Asset'
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

	/**
	 * Returns a matcher object.
	 *
	 * @return \TYPO3\CMS\Media\QueryElement\Matcher
	 */
	protected function createMatcherObject() {

		/** @var $matcher \TYPO3\CMS\Media\QueryElement\Matcher */
		$matcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Matcher');

		// Special case for Grid in the BE using jQuery DataTables plugin.
		// Retrieve a possible search term from GP.
		$searchTerm = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('sSearch');
		if (strlen($searchTerm) > 0) {
			$matcher->setSearchTerm($searchTerm);
			$matcher->addCategory($searchTerm);
		}

		return $matcher;
	}

	/**
	 * Returns an order object.
	 *
	 * @return \TYPO3\CMS\Media\QueryElement\Order
	 */
	protected function createOrderObject() {
		// Default sort
		$order['uid'] = \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING;

		// Retrieve a possible id of the column from the request
		$columnPosition = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('iSortCol_0');
		if ($columnPosition > 0) {
			$field = \TYPO3\CMS\Media\Tca\ServiceFactory::getGridService('sys_file')->getFieldNameByPosition($columnPosition);

			$direction = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('sSortDir_0');
			$order = array(
				$field => $direction
			);
		}
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Order', $order);
	}

	/**
	 * Returns a pager object.
	 *
	 * @return \TYPO3\CMS\Media\QueryElement\Pager
	 */
	protected function createPagerObject() {

		/** @var $pager \TYPO3\CMS\Media\QueryElement\Pager */
		$pager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Pager');

		// Set items per page
		// DataTables plugin is not flexible enough - or makes it complicated - to encapsulate parameters like tx_media_pi[page]
		// $this->request->hasArgument('page')
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('iDisplayLength')) {
			$limit = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('iDisplayLength');
			$pager->setLimit($limit);
		}

		// Set offset
		$offset = 0;
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('iDisplayStart')) {
			$offset = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('iDisplayStart');
		}
		$pager->setOffset($offset);

		// set page
		$page = 1;
		if ($pager->getLimit() > 0) {
			$page = round($pager->getOffset() / $pager->getLimit());
		}
		$pager->setPage($page);

		return $pager;
	}
}
?>
