<?php
namespace TYPO3\CMS\Media\Domain\Repository;

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
 * Repository for accessing media
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class MediaRepository extends \TYPO3\CMS\Core\Resource\FileRepository {

	/**
	 * Update a Media Management media with new information
	 *
	 * @param string $uid of the Media media
	 * @return array file information
	 */
	public function updateMedia($uid, $metaData) {

		//TODO finish work
		$data = array();
		$data['tx_media'][$uid] = array(
			'title' => 'New title'
		);

		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->start($data, array());
		$tce->process_datamap();
	}

	/**
	 * Finds all references by the specified filter
	 *
	 * @param \TYPO3\CMS\Media\QueryElement\Filter $filter The filter the references must apply to
	 * @param \TYPO3\CMS\Media\QueryElement\Order $order The order
	 * @param int $offset
	 * @param int $itemsPerPage
	 * @return \TYPO3\CMS\Core\Resource\File[]
	 */
	public function findAllByFilter(\TYPO3\CMS\Media\QueryElement\Filter $filter, \TYPO3\CMS\Media\QueryElement\Order $order = null, $offset = null, $itemsPerPage = null) {

		/** @var $query \TYPO3\CMS\Media\QueryElement\Query */
		$query = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Query');

		$query->setFilter($filter);
		$query->setOrder($order);

		if ($offset) {
			$query->setOffset($offset);
		}

		if ($itemsPerPage) {
			$query->setLimit($itemsPerPage);
		}

		return $query->execute();
	}

	/**
	 * @todo fix me -> this method does not return the right result
	 * Count all references by the specified filter
	 *
	 * @param \TYPO3\CMS\Media\QueryElement\Filter $filter The filter the references must apply to
	 */
	public function countAllByFilter(\TYPO3\CMS\Media\QueryElement\Filter $filter) {

		/** @var $query \TYPO3\CMS\Media\QueryElement\Query */
		$query = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Query');

		$query->setFilter($filter);
		return count($query->execute());
	}
}

?>