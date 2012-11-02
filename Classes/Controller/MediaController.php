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
	 * mediaRepository
	 *
	 * @var \TYPO3\CMS\Media\Domain\Repository\MediaRepository
	 */
	protected $mediaRepository;

	/**
	 * injectMediaRepository
	 *
	 * @param \TYPO3\CMS\Media\Domain\Repository\MediaRepository $mediaRepository
	 * @return void
	 */
	public function injectMediaRepository(\TYPO3\CMS\Media\Domain\Repository\MediaRepository $mediaRepository) {
		$this->mediaRepository = $mediaRepository;
	}

	/**
	 * List action for this controller. Displays a list of medias
	 *
	 * @return string The rendered view
	 */
	public function listAction() {

		/** @var $grid  \TYPO3\CMS\Media\Service\Grid */
		$grid = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\Grid');

		$this->view->assign('columns', $grid->getListOfColumns());
		$this->view->assign('medias', $this->mediaRepository->findAll());
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
		$medias = $this->mediaRepository->findAllByFilter($filterObject, $orderObject, $pagerObject->getOffset(), $pagerObject->getItemsPerPage());
		$numberOfMedias = $this->mediaRepository->countAllByFilter($filterObject);
		$pagerObject->setCount($numberOfMedias);

		// Assign values
		$this->view->assign('medias', $medias);
		$this->view->assign('numberOfMedias', $numberOfMedias);
		$this->view->assign('pager', $pagerObject);

		$this->request->setFormat('json');
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
	public function newAction($media = NULL) {
		$this->view->assign('media', $media);
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
		//$this->mediaRepository->add($media);

		// Prepare output
		$result['status'] = FALSE;
		$result['action'] = 'create';
		$result['media'] = array('uid' => '','title' => '',);

		$mediaUid = $this->mediaRepository->addMedia($media);

		if ($mediaUid > 0) {
			$mediaObject = $this->mediaRepository->findByUid($mediaUid);
			$result['status'] = TRUE;
			$result['media'] = array(
				'uid' => $mediaObject->getUid(),
				'title' => $mediaObject->getTitle(),
			);
		}
		$this->view->assign('result', $result);
		$this->request->setFormat('json');
	}

	/**
	 * Action edit
	 *
	 * @param int $media
	 * @return void
	 */
	public function editAction($media) {
		$mediaObject = $this->mediaRepository->findByUid($media);
		$this->view->assign('media', $mediaObject);
	}

	/**
	 * action update
	 *
	 * @param array $media
	 * @return void
	 * @dontvalidate $media
	 */
	public function updateAction(array $media) {
		$this->mediaRepository->updateMedia($media);
		$mediaObject = $this->mediaRepository->findByUid($media['uid']);
		$result['status'] = TRUE;
		$result['action'] = 'update';
		$result['media'] = array(
			'uid' => $mediaObject->getUid(),
			'title' => $mediaObject->getTitle(),
		);
		$this->view->assign('result', $result);
		$this->request->setFormat('json');
	}

	/**
	 * Delete a row given a media uid.
	 * This action is expected to have a parameter format = json
	 *
	 * @param int $media
	 * @return string
	 */
	public function deleteAction($media) {
		$mediaObject = $this->mediaRepository->findByUid($media);
		$result['status'] = $this->mediaRepository->remove($mediaObject);
		$result['action'] = 'delete';
		$result['media'] = array(
			'uid' => $mediaObject->getUid(),
			'title' => $mediaObject->getTitle(),
		);
		$this->view->assign('result', $result);

		$this->request->setFormat('json');
	}

}
?>