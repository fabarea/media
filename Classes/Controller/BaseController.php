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
 * Base Controller which is meant to include all common
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class BaseController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
	 */
	protected $frontendUser;

	/**
	 * Instantiate a filter object and feed the object with conditions
	 *
	 * @param array $filter
	 * @return \TYPO3\CMS\Media\QueryElement\Filter
	 */
	protected function createFilterObject(array $filter = NULL) {
		$filterObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Filter');
		if (!empty($filter)) {
			if ($filter['location'] != '') {
				$filterObject->setLocation($filter['location']);
			}
			if ($filter['country']) {
				$filterObject->setCountry($filter['country']);
			}
			if ($filter['category']) {
				$filterObject->setCategory($filter['category']);
			}
		}
		return $filterObject;
	}

	/**
	 * Instantiate an order object and returns its
	 *
	 * @param array $order
	 * @return \TYPO3\CMS\Media\QueryElement\Order
	 */
	protected function createOrderObject(array $order = NULL) {
		$orderObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Order');
		$orderObject->addOrdering('tstamp', \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING);
		$orderObject->addOrdering('title', \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING);
		return $orderObject;
	}

	/**
	 * Instantiate a pager object and returns its
	 *
	 * @return \TYPO3\CMS\Media\QueryElement\Pager
	 */
	protected function createPagerObject() {

		/** @var $pager \TYPO3\CMS\Media\QueryElement\Pager */
		$pager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Pager');

		if ($this->request->hasArgument('page')) {
			$pager->setPage($this->request->getArgument('page'));
			if ($pager->getPage() < 1) {
				$pager->setPage(1);
			}
		}

		$pager->setItemsPerPage($this->settings['pageBrowser']['itemsPerPage']);
		return $pager;
	}
}
?>