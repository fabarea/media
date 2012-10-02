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
	 * @var array $filter The filter
	 * @var array $order The order
	 * @return string The rendered view
	 * @dontvalidate $filter
	 * @dontvalidate $order
	 */
	public function listAction(array $filter = NULL) {

		// Initialize some objects related to the query
		$filterObject = $this->createFilterObject($filter);
		$orderObject = $this->createOrderObject($filter);
		$pagerObject = $this->createPagerObject();

		// Compute sthe offset
		$offset = ($pagerObject->getPage() - 1) * $pagerObject->getItemsPerPage();

		// Query the repository
		$medias = $this->mediaRepository->findAllByFilter($filterObject, $orderObject, $offset, $pagerObject->getItemsPerPage());
		#$count = $this->mediaRepository->countAllByFilter($filterObject);
		#$pagerObject->setCount($count);

		// Assign values
		$this->view->assign('medias', $medias);
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
	 * @param $media
	 * @return void
	 */
	public function deleteAction(tx_media $media) {
		$this->mediaRepository->remove($media);
		$this->flashMessageContainer->add('Your Media was removed.');
		$this->redirect('list');
	}

}
?>