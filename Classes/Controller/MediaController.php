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
		$this->view->assign('medias', $this->mediaRepository->findAll());
	}

	/**
	 * List Row action for this controller. Output a json list of medias
	 * This action is expected to have a parameter format = json
	 *
	 * @var array $filter The filter
	 * @var array $order The order
	 * @return string The rendered view
	 * @dontvalidate $filter
	 * @dontvalidate $order
	 */
	public function listRowAction(array $filter = NULL, array $order = NULL) {

		// Initialize some objects related to the query
		$filterObject = $this->createFilterObject($filter);
		$orderObject = $this->createOrderObject($order);
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
	 * @param $media
	 * @return void
	 */
	public function showAction(tx_media $media) {
		$this->view->assign('media', $media);
	}

	/**
	 * action new
	 *
	 * @param $newMedia
	 * @dontvalidate $newMedia
	 * @return void
	 */
	public function newAction(tx_media $newMedia = NULL) {
		$this->view->assign('newMedia', $newMedia);
	}

	/**
	 * action create
	 *
	 * @param $newMedia
	 * @return void
	 */
	public function createAction(tx_media $newMedia) {
		$this->mediaRepository->add($newMedia);
		$this->flashMessageContainer->add('Your new Media was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param $media
	 * @return void
	 */
	public function editAction(tx_media $media) {
		$this->view->assign('media', $media);
	}

	/**
	 * action update
	 *
	 * @param $media
	 * @return void
	 */
	public function updateAction(tx_media $media) {
		$this->mediaRepository->update($media);
		$this->flashMessageContainer->add('Your Media was updated.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param int $media
	 * @return void
	 */
	public function deleteAction($media) {
		$mediaObject = $this->mediaRepository->findByUid($media);
		$this->mediaRepository->remove($mediaObject);
		$this->flashMessageContainer->add('Your Media was removed.');
		$this->redirect('list');
	}

	/**
	 * Delete a row given a media uid.
	 * This action is expected to have a parameter format = json
	 *
	 * @param int $media
	 * @return string
	 */
	public function deleteRowAction($media) {
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